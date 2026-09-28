@extends('layouts.admin')
@section('title','Students')
@section('page-title','Students')
@section('page-subtitle','Manage registered students, subscriptions, and access')
@section('topbar-actions')
    <span id="studentCount" class="badge badge-purple" style="font-size:0.8rem;padding:0.4rem 0.875rem">Loading…</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">All Students</span>
    </div>
    <div class="card-body" style="padding:1.25rem 1.5rem 0.75rem">
        <div class="search-bar">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Search by name, mobile, or email…" oninput="debounceSearch()">
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr>
                <th>#</th><th>Photo</th><th>Student</th><th>School & District</th><th>Class</th>
                <th>Active Plan & Amount</th><th>Trial / Status</th><th>Actions</th>
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

<!-- Grant Free Subscription Modal -->
<div class="modal-backdrop" id="grantModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">🎁 Grant Free Access / Subscription</span>
            <button class="modal-close" onclick="closeModal('grantModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="grant-student-id">
            <div style="background:var(--bg-surface);padding:0.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem">
                <strong>Student:</strong> <span id="grant-student-name"></span> (<span id="grant-student-mobile"></span>)
            </div>
            <div class="form-group">
                <label class="form-label">Duration (in Days) *</label>
                <input type="number" class="form-control" id="grant-days" value="30" min="1" max="1000" required>
                <small style="color:var(--text-muted);font-size:0.75rem">e.g. 30 days (1 month), 90 days (3 months), 365 days (1 year)</small>
            </div>
            <div class="form-group">
                <label class="form-label">Subscription Plan (Optional)</label>
                <select class="form-control" id="grant-plan">
                    <option value="">Select Plan...</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('grantModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitGrantSubscription()">Grant Access Now</button>
        </div>
    </div>
</div>

<!-- Student Detail Modal -->
<div class="modal-backdrop" id="studentModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">👨‍🎓 Student Details</span>
            <button class="modal-close" onclick="closeModal('studentModal')">×</button>
        </div>
        <div class="modal-body" id="studentModalBody">
            <!-- Filled via JS -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('studentModal')">Close</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1, searchTimer, allPlans = [], currentStudents = [];

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadStudents(1), 400); }

async function loadPlans() {
    try {
        const res = await api('GET', '/admin/subscription-plans');
        allPlans = res.data || [];
        const planSelect = document.getElementById('grant-plan');
        planSelect.innerHTML = '<option value="">Default Standard Plan</option>' +
            allPlans.map(p => `<option value="${p.id}" data-days="${p.duration_days}">${p.name} (₹${p.price} • ${p.duration_days} days)</option>`).join('');
    } catch(e){}
}

document.getElementById('grant-plan')?.addEventListener('change', e => {
    const sel = e.target.options[e.target.selectedIndex];
    const days = sel.getAttribute('data-days');
    if (days) document.getElementById('grant-days').value = days;
});

async function loadStudents(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    document.getElementById('studentsBody').innerHTML = '<tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading…</td></tr>';
    try {
        const res = await api('GET', `/admin/students?page=${page}&search=${encodeURIComponent(search)}`);
        currentStudents = res.data || [];
        const meta = res.meta || null;

        document.getElementById('studentCount').textContent = (meta?.total || currentStudents.length) + ' students';
        document.getElementById('studentsBody').innerHTML = currentStudents.length ? currentStudents.map((s, i) => {
            const profile = s.student_profile || {};
            const avatarHtml = profile.avatar_url
                ? `<img src="${profile.avatar_url}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">`
                : `<div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem">${(s.name || 'S').charAt(0).toUpperCase()}</div>`;
            
            // Subscriptions info
            const subs = s.user_subscriptions || [];
            const activeSub = subs.find(sub => sub.payment_status === 'completed' && new Date(sub.ends_at) >= new Date());
            const subBadge = activeSub
                ? `<span class="badge badge-success" style="margin-bottom:0.2rem;display:inline-block">${activeSub.subscription?.name || 'Active Plan'}</span><div style="font-size:0.75rem;color:var(--success);font-weight:600">₹${activeSub.subscription?.price || 0} • Valid till ${new Date(activeSub.ends_at).toLocaleDateString('en-IN')}</div>`
                : (subs.length ? `<span class="badge badge-neutral">Expired</span>` : `<span class="badge badge-warning">No Subscription</span>`);

            const trialDate  = s.trial_ends_at ? new Date(s.trial_ends_at) : null;
            const trialActive = trialDate && trialDate > new Date();

            return `<tr>
                <td style="color:var(--text-muted)">${(page-1)*20 + i + 1}</td>
                <td>${avatarHtml}</td>
                <td>
                    <div style="font-weight:600">${s.name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.mobile || s.email || '—'}</div>
                </td>
                <td>
                    <div style="font-size:0.82rem;font-weight:500">${profile.school_name || '—'}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">${profile.district || '—'}</div>
                </td>
                <td><span class="badge badge-purple">Class ${profile.class || '—'}</span></td>
                <td>${subBadge}</td>
                <td>
                    <span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span>
                    ${trialActive ? `<div style="font-size:0.7rem;color:var(--primary);margin-top:0.2rem">Trial active (${trialDate.toLocaleDateString('en-IN')})</div>` : ''}
                </td>
                <td style="display:flex;gap:0.35rem;flex-wrap:wrap">
                    <button class="btn btn-outline btn-sm" onclick="openGrantModal(${s.id})" title="Grant Free Subscription" style="color:var(--primary);border-color:rgba(91,69,255,0.3)">🎁 Free Pass</button>
                    <button class="btn btn-outline btn-sm" onclick="viewStudent(${s.id})" title="View Details">👁</button>
                    <button class="btn btn-sm ${s.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleStudent(${s.id}, ${s.is_active})" title="${s.is_active ? 'Suspend' : 'Activate'}">
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

function openGrantModal(studentId) {
    const student = currentStudents.find(s => s.id === studentId);
    if (!student) return;
    document.getElementById('grant-student-id').value = student.id;
    document.getElementById('grant-student-name').textContent = student.name || 'Student';
    document.getElementById('grant-student-mobile').textContent = student.mobile || student.email || '';
    document.getElementById('grant-days').value = '30';
    openModal('grantModal');
}

async function submitGrantSubscription() {
    const studentId = document.getElementById('grant-student-id').value;
    const days = parseInt(document.getElementById('grant-days').value) || 30;
    const planId = document.getElementById('grant-plan').value || null;

    try {
        await api('POST', `/admin/students/${studentId}/grant-subscription`, {
            days: days,
            subscription_id: planId
        });
        toast(`Free access granted for ${days} days!`, 'success');
        closeModal('grantModal');
        loadStudents(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

function viewStudent(id) {
    const student = currentStudents.find(s => s.id === id);
    if (!student) return;
    const profile = student.student_profile || {};
    const subs = student.user_subscriptions || [];

    document.getElementById('studentModalBody').innerHTML = `
        <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1.25rem">
            ${profile.avatar_url ? `<img src="${profile.avatar_url}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid var(--primary)">` : `<div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700">${(student.name||'S').charAt(0)}</div>`}
            <div>
                <div style="font-size:1.1rem;font-weight:700">${student.name}</div>
                <div style="font-size:0.85rem;color:var(--text-muted)">📱 ${student.mobile || '—'} • ✉️ ${student.email || '—'}</div>
                <span class="badge badge-purple" style="margin-top:0.25rem">Class ${profile.class || '—'}</span>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem">
            <div style="background:var(--bg-surface);padding:0.75rem;border-radius:8px">
                <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase">School</div>
                <div style="font-weight:600;font-size:0.85rem">${profile.school_name || '—'}</div>
            </div>
            <div style="background:var(--bg-surface);padding:0.75rem;border-radius:8px">
                <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase">District</div>
                <div style="font-weight:600;font-size:0.85rem">${profile.district || '—'}</div>
            </div>
        </div>

        <div style="font-size:0.8rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase">Subscription History:</div>
        <div style="display:flex;flex-direction:column;gap:0.4rem;max-height:160px;overflow-y:auto">
            ${subs.length ? subs.map(sub => `
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0.8rem;background:var(--bg-surface);border-radius:8px;border:1px solid var(--border-light)">
                    <div>
                        <div style="font-weight:600;font-size:0.82rem">${sub.subscription?.name || 'Custom Plan'} (₹${sub.subscription?.price || 0})</div>
                        <div style="font-size:0.72rem;color:var(--text-muted)">Gateway: ${sub.payment_gateway || 'Razorpay'} • Ref: ${sub.payment_id || '—'}</div>
                    </div>
                    <div style="text-align:right">
                        <span class="badge ${sub.payment_status === 'completed' ? 'badge-success' : 'badge-warning'}">${sub.payment_status}</span>
                        <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.15rem">${sub.ends_at ? 'Ends ' + new Date(sub.ends_at).toLocaleDateString('en-IN') : ''}</div>
                    </div>
                </div>
            `).join('') : '<div style="font-size:0.8rem;color:var(--text-muted);text-align:center;padding:1rem">No subscription payments on record.</div>'}
        </div>
    `;
    openModal('studentModal');
}

async function toggleStudent(id, isActive) {
    if (!confirm(`${isActive ? 'Suspend' : 'Activate'} this student?`)) return;
    try {
        await api('PUT', `/admin/students/${id}`, { is_active: !isActive });
        toast('Student status updated.', 'success');
        loadStudents(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = () => { loadPlans(); loadStudents(1); };
</script>
@endsection
