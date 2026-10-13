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
let currentPage = 1, searchTimer, cachedStudents = [];

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadStudents(1), 400); }

async function loadStudents(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value.trim();
    const status = document.getElementById('statusFilter')?.value || '';
    document.getElementById('studentsBody').innerHTML = '<tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading…</td></tr>';
    try {
        const queryParams = new URLSearchParams({ page });
        if (search) queryParams.append('search', search);
        if (status) queryParams.append('status', status);

        const res = await api('GET', `/admin/students?${queryParams.toString()}`);
        const data = Array.isArray(res.data) ? res.data : (Array.isArray(res.data?.data) ? res.data.data : []);
        const meta = res.meta || res.data?.meta || {};
        cachedStudents = data;

        const total = meta?.total !== undefined ? meta.total : data.length;
        document.getElementById('studentCount').textContent = total + ' students';

        document.getElementById('studentsBody').innerHTML = data.length ? data.map((s, i) => {
            const trialDate  = s.trial_ends_at ? new Date(s.trial_ends_at) : null;
            const trialActive = trialDate && trialDate > new Date();
            const activeSub = (s.user_subscriptions && s.user_subscriptions.length > 0) ? s.user_subscriptions[0] : null;

            return `<tr>
                <td style="color:var(--text-muted)">${(page-1)*20 + i + 1}</td>
                <td>
                    <div style="font-weight:600">${s.name || s.student_profile?.name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.email || s.mobile || '—'}</div>
                </td>
                <td>
                    <div>${s.student_profile?.school_name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.student_profile?.district || '—'}</div>
                </td>
                <td>${s.student_profile?.class ? 'Class ' + s.student_profile.class : '—'}</td>
                <td>
                    ${trialDate ? `<div>${trialDate.toLocaleDateString('en-IN')}</div><div style="font-size:0.7rem"><span class="badge ${trialActive ? 'badge-success' : 'badge-danger'}">${trialActive ? 'Active' : 'Expired'}</span></div>` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td>
                    ${activeSub ? `<span class="badge badge-purple">${activeSub.subscription?.name || 'Active'}</span>` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td><span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span></td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="viewStudent(${s.id})">👁 View</button>
                    <button class="btn btn-sm ${s.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleStudent(${s.id}, ${s.is_active})">
                        ${s.is_active ? 'Suspend' : 'Activate'}
                    </button>
                </td>
            </tr>`;
        }).join('') : '<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No students found.</td></tr>';

        renderPagination(meta?.current_page || page, meta?.last_page || 1, meta?.from, meta?.to, total);
    } catch(e) { toast('Failed to load students: ' + e.message, 'error'); }
}

function renderPagination(current, last, from, to, total) {
    if (!last || last <= 1) { document.getElementById('paginationBar').style.display = 'none'; return; }
    document.getElementById('paginationBar').style.display = 'flex';
    const fromVal = from ?? ((current - 1) * 20 + 1);
    const toVal = to ?? Math.min(current * 20, total);
    document.getElementById('paginationInfo').textContent = `Showing ${fromVal}–${toVal} of ${total}`;
    const btns = [];
    for (let p = Math.max(1, current - 2); p <= Math.min(last, current + 2); p++) {
        btns.push(`<button class="page-btn ${p===current?'active':''}" onclick="loadStudents(${p})">${p}</button>`);
    }
    document.getElementById('paginationBtns').innerHTML =
        `<button class="page-btn" onclick="loadStudents(${current-1})" ${current===1?'disabled':''}>‹</button>` +
        btns.join('') +
        `<button class="page-btn" onclick="loadStudents(${current+1})" ${current===last?'disabled':''}>›</button>`;
}

function viewStudent(id) {
    const s = cachedStudents.find(item => item.id === id);
    if (!s) return;
    openModal('studentModal');
    const trialDate = s.trial_ends_at ? new Date(s.trial_ends_at).toLocaleString('en-IN') : 'None';
    const trialStartDate = s.trial_starts_at ? new Date(s.trial_starts_at).toLocaleString('en-IN') : 'None';
    const activeSub = (s.user_subscriptions && s.user_subscriptions.length > 0) ? s.user_subscriptions[0] : null;

    document.getElementById('studentModalBody').innerHTML = `
        <div style="width:100%;display:flex;flex-direction:column;gap:1.25rem">
            <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border-light)">
                ${s.student_profile?.avatar_url
                    ? `<img src="${s.student_profile.avatar_url}" style="width:52px;height:52px;border-radius:50%;object-fit:cover">`
                    : `<div style="width:52px;height:52px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.35rem;font-weight:700">${((s.name || s.student_profile?.name || 'S')[0]).toUpperCase()}</div>`}
                <div>
                    <h3 style="margin:0;font-size:1.1rem">${s.name || s.student_profile?.name || 'Student'}</h3>
                    <div style="font-size:0.8rem;color:var(--text-muted)">User ID #${s.id} · Registered ${new Date(s.created_at).toLocaleDateString('en-IN')}</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;font-size:0.875rem">
                <div><span style="color:var(--text-muted)">Email:</span> <strong>${s.email || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">Mobile:</span> <strong>${s.mobile || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">Class:</span> <strong>${s.student_profile?.class ? 'Class ' + s.student_profile.class : '—'}</strong></div>
                <div><span style="color:var(--text-muted)">District:</span> <strong>${s.student_profile?.district || '—'}</strong></div>
                <div style="grid-column:span 2"><span style="color:var(--text-muted)">School:</span> <strong>${s.student_profile?.school_name || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">Status:</span> <span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span></div>
                <div><span style="color:var(--text-muted)">Subscription:</span> ${activeSub ? `<span class="badge badge-purple">${activeSub.subscription?.name || 'Active'}</span>` : 'None'}</div>
                <div><span style="color:var(--text-muted)">Trial Started:</span> <div>${trialStartDate}</div></div>
                <div><span style="color:var(--text-muted)">Trial Ends:</span> <div>${trialDate}</div></div>
            </div>
        </div>
    `;
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
