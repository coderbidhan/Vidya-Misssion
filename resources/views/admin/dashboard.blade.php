@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle','Platform overview & key metrics')

@section('content')
<div class="stats-grid" id="statsGrid">
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#7C5CFC,#9B7DFF)">
        <div class="stat-icon">👨‍🎓</div>
        <div class="stat-value" id="s-students">—</div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#3ECFCF,#22D3EE)">
        <div class="stat-icon">👨‍🏫</div>
        <div class="stat-value" id="s-teachers">—</div>
        <div class="stat-label">Teachers</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#F59E0B,#FBBF24)">
        <div class="stat-icon">⏳</div>
        <div class="stat-value" id="s-trial">—</div>
        <div class="stat-label">Trial Users (Active)</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#22C55E,#4ADE80)">
        <div class="stat-icon">✅</div>
        <div class="stat-value" id="s-subs">—</div>
        <div class="stat-label">Active Subscriptions</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#EC4899,#F472B6)">
        <div class="stat-icon">💰</div>
        <div class="stat-value" id="s-rev">—</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
    <!-- Popular Subjects -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📚 Popular Subjects</span>
        </div>
        <div class="card-body" id="popularSubjects" style="padding:0">
            <table><tbody id="subjectRows">
                <tr class="loading-row"><td colspan="2"><div class="spinner-sm"></div> Loading…</td></tr>
            </tbody></table>
        </div>
    </div>

    <!-- Test Performance -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">🧪 Test Pass Rates</span>
        </div>
        <div class="card-body" style="padding:0">
            <table>
                <thead><tr><th>Test</th><th>Attempts</th><th>Pass Rate</th></tr></thead>
                <tbody id="testRows">
                    <tr class="loading-row"><td colspan="3"><div class="spinner-sm"></div> Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="card" style="margin-top:1.25rem">
    <div class="card-header">
        <span class="card-title">👨‍🎓 Recent Students</span>
        <a href="{{ route('admin.students') }}" class="btn btn-outline btn-sm">View All →</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>District</th><th>Class</th><th>Trial Status</th></tr></thead>
            <tbody id="recentStudents">
                <tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div> Loading…</td></tr>
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

        const o = overview.data;
        document.getElementById('s-students').textContent = (o.total_students || 0).toLocaleString();
        document.getElementById('s-teachers').textContent = o.total_teachers || 0;
        document.getElementById('s-trial').textContent    = o.trial_users || 0;
        document.getElementById('s-subs').textContent     = o.active_subscriptions || 0;
        document.getElementById('s-rev').textContent      = '₹' + (o.total_revenue || 0).toLocaleString();

        // Subjects
        const subjectColors = ['#EF4444','#3B82F6','#84CC16','#8B5CF6','#F97316','#22C55E','#06B6D4'];
        document.getElementById('subjectRows').innerHTML = (subjects.data || []).map((s, i) =>
            `<tr><td style="padding:0.875rem 1.25rem">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${subjectColors[i]||'#7C5CFC'};margin-right:0.5rem"></span>
                ${s.name}</td>
            <td style="padding:0.875rem 1.25rem;color:var(--text-muted)">${s.completion_count || 0} completions</td></tr>`
        ).join('') || '<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted)">No data yet</td></tr>';

        // Tests
        document.getElementById('testRows').innerHTML = (tests.data || []).slice(0,5).map(t =>
            `<tr><td>${t.title}</td><td><span class="badge badge-neutral">${t.attempts}</span></td>
            <td><span class="badge ${parseFloat(t.pass_rate)>60?'badge-success':'badge-warning'}">${t.pass_rate}</span></td></tr>`
        ).join('') || '<tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--text-muted)">No tests yet</td></tr>';

        // Recent students
        const rows = students.data || [];
        document.getElementById('recentStudents').innerHTML = rows.length ? rows.slice(0,8).map((s, i) =>
            `<tr>
                <td style="color:var(--text-muted)">#${i+1}</td>
                <td><strong>${s.name || '—'}</strong></td>
                <td style="color:var(--text-muted)">${s.email}</td>
                <td>${s.student_profile?.district || '—'}</td>
                <td>${s.student_profile?.class || '—'}</td>
                <td>${s.trial_ends_at ? (new Date(s.trial_ends_at) > new Date()
                    ? '<span class="badge badge-success">✓ Active</span>'
                    : '<span class="badge badge-danger">Expired</span>')
                    : '<span class="badge badge-neutral">Not Started</span>'}</td>
            </tr>`
        ).join('') : '<tr><td colspan="6" class="loading-row">No students yet</td></tr>';

    } catch(e) { toast('Error loading dashboard: ' + e.message, 'error'); }
};
</script>
@endsection
