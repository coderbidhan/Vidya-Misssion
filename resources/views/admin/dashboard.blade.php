@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle','Platform overview & real-time metrics')

@section('topbar-actions')
    <a href="{{ route('admin.notifications') }}" class="btn btn-primary btn-sm">
        <span>🔔</span> Broadcast Alert
    </a>
@endsection

@section('content')
<!-- Quick Action Shortcuts -->
<div style="display:flex;gap:0.6rem;overflow-x:auto;padding-bottom:0.75rem;margin-bottom:1rem;white-space:nowrap;-webkit-overflow-scrolling:touch">
    <a href="{{ route('admin.students') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>👨‍🎓</span> Manage Students
    </a>
    <a href="{{ route('admin.teachers') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>👨‍🏫</span> Manage Teachers
    </a>
    <a href="{{ route('admin.subjects') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>📚</span> Classes & Subjects
    </a>
    <a href="{{ route('admin.content') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>🎬</span> Content & Classes
    </a>
    <a href="{{ route('admin.tests') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>🧪</span> Tests & MCQs
    </a>
    <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline btn-sm" style="background:#fff;border-radius:12px;padding:0.5rem 0.85rem">
        <span>💳</span> Subscriptions
    </a>
</div>

<!-- Stats Grid -->
<div class="stats-grid" id="statsGrid">
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#5B45FF,#7F6BFF)">
        <div class="stat-card-top">
            <span class="stat-icon">👨‍🎓</span>
            <span class="badge badge-purple">Students</span>
        </div>
        <div>
            <div class="stat-value" id="s-students">—</div>
            <div class="stat-label">Total Registered Students</div>
        </div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#06B6D4,#22D3EE)">
        <div class="stat-card-top">
            <span class="stat-icon">👨‍🏫</span>
            <span class="badge badge-info">Faculty</span>
        </div>
        <div>
            <div class="stat-value" id="s-teachers">—</div>
            <div class="stat-label">Teaching Staff</div>
        </div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#F59E0B,#FBBF24)">
        <div class="stat-card-top">
            <span class="stat-icon">⏳</span>
            <span class="badge badge-warning">Trial</span>
        </div>
        <div>
            <div class="stat-value" id="s-trial">—</div>
            <div class="stat-label">Active Trial Users</div>
        </div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#10B981,#34D399)">
        <div class="stat-card-top">
            <span class="stat-icon">✅</span>
            <span class="badge badge-success">Subscribed</span>
        </div>
        <div>
            <div class="stat-value" id="s-subs">—</div>
            <div class="stat-label">Paid Active Passes</div>
        </div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#EC4899,#F472B6)">
        <div class="stat-card-top">
            <span class="stat-icon">💰</span>
            <span class="badge badge-purple">Revenue</span>
        </div>
        <div>
            <div class="stat-value" id="s-rev">—</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.25rem;margin-bottom:1.25rem">
    <!-- Popular Subjects -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📚 Popular Subjects by Progress</span>
            <a href="{{ route('admin.subjects') }}" class="btn btn-outline btn-sm" style="font-size:0.75rem">Curriculum →</a>
        </div>
        <div class="card-body" id="popularSubjects" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Subject</th><th>Progress Completions</th></tr></thead>
                    <tbody id="subjectRows">
                        <tr class="loading-row"><td colspan="2"><div class="spinner-sm"></div> Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Test Performance -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">🧪 Test Pass Rates</span>
            <a href="{{ route('admin.tests') }}" class="btn btn-outline btn-sm" style="font-size:0.75rem">All Tests →</a>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Test Title</th><th>Attempts</th><th>Pass Rate</th></tr></thead>
                    <tbody id="testRows">
                        <tr class="loading-row"><td colspan="3"><div class="spinner-sm"></div> Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="card">
    <div class="card-header">
        <span class="card-title">👨‍🎓 Recent Registered Students</span>
        <a href="{{ route('admin.students') }}" class="btn btn-primary btn-sm">View All Students →</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Student</th><th>School & District</th><th>Class</th><th>Trial / Pass Status</th><th>Action</th></tr></thead>
            <tbody id="recentStudents">
                <tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div> Loading students…</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
window._pageInit = async function() {
    try {
        const [overview, subjects, tests, students] = await Promise.all([
            api('GET', '/admin/analytics/overview'),
            api('GET', '/admin/analytics/popular-subjects'),
            api('GET', '/admin/analytics/test-performance'),
            api('GET', '/admin/students?page=1'),
        ]);

        const o = overview.data || {};
        document.getElementById('s-students').textContent = (o.total_students || 0).toLocaleString();
        document.getElementById('s-teachers').textContent = o.total_teachers || 0;
        document.getElementById('s-trial').textContent    = o.trial_users || 0;
        document.getElementById('s-subs').textContent     = o.active_subscriptions || 0;
        document.getElementById('s-rev').textContent      = '₹' + (o.total_revenue || 0).toLocaleString();

        // Popular Subjects
        const subjectColors = ['#5B45FF','#06B6D4','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899'];
        const subjList = subjects.data || [];
        document.getElementById('subjectRows').innerHTML = subjList.length ? subjList.map((s, i) => `
            <tr>
                <td style="padding:0.75rem 1rem">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${s.color || subjectColors[i % subjectColors.length]}"></span>
                        <strong class="bn-font">${s.name}</strong>
                    </div>
                </td>
                <td style="padding:0.75rem 1rem;color:var(--text-muted)">
                    <span class="badge badge-purple">${s.completion_count || 0} completions</span>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted)">No curriculum data yet</td></tr>';

        // Tests
        const testList = tests.data?.data || tests.data || [];
        document.getElementById('testRows').innerHTML = testList.length ? testList.slice(0, 6).map(t => {
            const passPct = parseFloat(t.pass_rate) || 0;
            return `<tr>
                <td><strong class="bn-font">${t.title}</strong></td>
                <td><span class="badge badge-neutral">${t.attempts || 0} attempts</span></td>
                <td><span class="badge ${passPct >= 60 ? 'badge-success' : 'badge-warning'}">${t.pass_rate || '0%'}</span></td>
            </tr>`;
        }).join('') : '<tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--text-muted)">No tests yet</td></tr>';

        // Recent students
        const rows = students.data?.data || students.data || [];
        document.getElementById('recentStudents').innerHTML = rows.length ? rows.slice(0, 8).map((s, i) => {
            const p = s.student_profile || {};
            const trialDate = s.trial_ends_at ? new Date(s.trial_ends_at) : null;
            const trialActive = trialDate && trialDate > new Date();

            return `<tr>
                <td style="color:var(--text-muted)">#${i+1}</td>
                <td>
                    <div style="font-weight:700">${s.name || 'Student'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.mobile || s.email || '—'}</div>
                </td>
                <td>
                    <div style="font-size:0.82rem;font-weight:600">${p.school_name || '—'}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">${p.district || '—'}</div>
                </td>
                <td><span class="badge badge-purple">Class ${p.class || '—'}</span></td>
                <td>
                    ${trialActive
                        ? `<span class="badge badge-success">✓ Trial Active (${trialDate.toLocaleDateString('en-IN')})</span>`
                        : (s.is_active ? '<span class="badge badge-neutral">Active Account</span>' : '<span class="badge badge-danger">Suspended</span>')
                    }
                </td>
                <td>
                    <a href="{{ route('admin.students') }}" class="btn btn-outline btn-sm">Manage →</a>
                </td>
            </tr>`;
        }).join('') : '<tr><td colspan="6" class="loading-row">No students registered yet</td></tr>';

    } catch(e) { toast('Error loading dashboard: ' + e.message, 'error'); }
};
</script>
@endsection
