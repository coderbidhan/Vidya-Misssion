@extends('layouts.admin')
@section('title','Students')
@section('page-title','Students')
@section('page-subtitle','Manage all registered students')
@section('topbar-actions')
    <span id="studentCount" class="badge badge-purple" style="font-size:0.8rem;padding:0.4rem 0.875rem">Loading…</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">All Students</span>
        <div style="display:flex;gap:0.75rem;align-items:center">
            <select class="filter-select" id="statusFilter" onchange="loadStudents(1)">
                <option value="">All Status</option>
                <option value="active">Trial Active</option>
                <option value="expired">Trial Expired</option>
                <option value="subscribed">Subscribed</option>
            </select>
        </div>
    </div>
    <div class="card-body" style="padding:1.25rem 1.5rem 0.75rem">
        <div class="search-bar">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Search by name or email…"
                    oninput="debounceSearch()">
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr>
                <th>#</th><th>Student</th><th>School / District</th><th>Class</th>
                <th>Trial Ends</th><th>Subscription</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody id="studentsBody">
                <tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading students…</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination" id="paginationBar" style="display:none">
        <span id="paginationInfo"></span>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

<!-- Student Detail Modal -->
<div class="modal-backdrop" id="studentModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">👨‍🎓 Student Profile</span>
            <button class="modal-close" onclick="closeModal('studentModal')">×</button>
        </div>
        <div class="modal-body" id="studentModalBody" style="min-height:200px;display:flex;align-items:center;justify-content:center">
            <div class="spinner-sm"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1, searchTimer;

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadStudents(1), 400); }

async function loadStudents(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    document.getElementById('studentsBody').innerHTML = '<tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading…</td></tr>';
    try {
        const res = await api('GET', `/admin/students?page=${page}&search=${encodeURIComponent(search)}`);
        const data = res.data?.data || [];
        const meta = res.data?.meta || res.data;

        document.getElementById('studentCount').textContent = (meta?.total || '?') + ' students';
        document.getElementById('studentsBody').innerHTML = data.length ? data.map((s, i) => {
            const trialDate  = s.trial_ends_at ? new Date(s.trial_ends_at) : null;
            const trialActive = trialDate && trialDate > new Date();
            return `<tr>
                <td style="color:var(--text-muted)">${(page-1)*20 + i + 1}</td>
                <td>
                    <div style="font-weight:600">${s.name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.email}</div>
                </td>
                <td>
                    <div>${s.student_profile?.school_name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.student_profile?.district || '—'}</div>
                </td>
                <td>${s.student_profile?.class || '—'}</td>
                <td>${trialDate ? trialDate.toLocaleDateString('en-IN') : '—'}</td>
                <td>—</td>
                <td><span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span></td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="viewStudent(${s.id})">👁 View</button>
                    <button class="btn btn-sm ${s.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleStudent(${s.id}, ${s.is_active})">
                        ${s.is_active ? 'Suspend' : 'Activate'}
                    </button>
                </td>
            </tr>`;
        }).join('') : '<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No students found.</td></tr>';

        renderPagination(meta?.current_page, meta?.last_page, meta?.from, meta?.to, meta?.total);
    } catch(e) { toast('Failed to load students: ' + e.message, 'error'); }
}

function renderPagination(current, last, from, to, total) {
    if (!last || last <= 1) { document.getElementById('paginationBar').style.display = 'none'; return; }
    document.getElementById('paginationBar').style.display = 'flex';
    document.getElementById('paginationInfo').textContent = `Showing ${from}–${to} of ${total}`;
    const btns = [];
    for (let p = Math.max(1, current - 2); p <= Math.min(last, current + 2); p++) {
        btns.push(`<button class="page-btn ${p===current?'active':''}" onclick="loadStudents(${p})">${p}</button>`);
    }
    document.getElementById('paginationBtns').innerHTML =
        `<button class="page-btn" onclick="loadStudents(${current-1})" ${current===1?'disabled':''}>‹</button>` +
        btns.join('') +
        `<button class="page-btn" onclick="loadStudents(${current+1})" ${current===last?'disabled':''}>›</button>`;
}

async function viewStudent(id) {
    openModal('studentModal');
    document.getElementById('studentModalBody').innerHTML = '<div style="text-align:center"><div class="spinner-sm"></div></div>';
    try {
        const res = await api('GET', `/admin/students?search=`);
        // Fallback: find from current data
        document.getElementById('studentModalBody').innerHTML = '<p style="color:var(--text-muted);text-align:center">Profile details loaded via students list.</p>';
    } catch(e) { toast(e.message, 'error'); }
}

async function toggleStudent(id, isActive) {
    if (!confirm(`${isActive ? 'Suspend' : 'Activate'} this student?`)) return;
    try {
        await api('PUT', `/admin/students/${id}`, { is_active: !isActive });
        toast('Student status updated.', 'success');
        loadStudents(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = () => loadStudents(1);
</script>
@endsection
