@extends('layouts.admin')
@section('title','Students')
@section('page-title','Students')
@section('page-subtitle','Manage registered students and user credentials')
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
                <input type="text" class="search-input" id="searchInput" placeholder="Search by name, email, or mobile…"
                    oninput="debounceSearch()">
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr>
                <th>#</th><th>Student</th><th>School / District</th><th>Class</th>
                <th>Account Password</th>
                <th>Trial Ends</th><th>Subscription</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody id="studentsBody">
                <tr class="loading-row"><td colspan="9"><div class="spinner-sm"></div> Loading students…</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination" id="paginationBar" style="display:none">
        <span id="paginationInfo"></span>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

<!-- ════════════════════════════════════════════════
     MODALS
════════════════════════════════════════════════ -->

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

<!-- Grant Free Access Modal -->
<div class="modal-backdrop" id="grantAccessModal">
    <div class="modal" style="max-width:480px">
        <div class="modal-header">
            <span class="modal-title">🎁 Grant Free Student Access</span>
            <button class="modal-close" onclick="closeModal('grantAccessModal')">×</button>
        </div>
        <form id="grantAccessForm" onsubmit="submitGrantAccess(event)">
            <div class="modal-body" style="display:flex;flex-direction:column;gap:1.1rem">
                <input type="hidden" id="grantStudentId">
                
                <div style="background:rgba(109,74,255,0.06);border:1px solid rgba(109,74,255,0.15);padding:0.85rem 1rem;border-radius:10px">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;font-weight:700;letter-spacing:0.05em">Selected Student</div>
                    <div style="font-weight:700;font-size:1rem;color:var(--primary);margin-top:0.2rem" id="grantStudentName">—</div>
                    <div style="font-size:0.8rem;color:var(--text-muted)" id="grantStudentEmail">—</div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Access Type</label>
                    <select class="search-input" id="grantAccessType" onchange="toggleAccessTypeFields()" required>
                        <option value="subscription">Complimentary Subscription Plan (Recommended)</option>
                        <option value="trial">Extend / Reactivate Free Trial</option>
                    </select>
                </div>

                <div class="form-group" id="planSelectGroup" style="margin-bottom:0">
                    <label class="form-label">Select Subscription Plan</label>
                    <select class="search-input" id="grantSubscriptionId" onchange="onPlanChange()">
                        <option value="">Loading plans...</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Access Duration (Days)</label>
                    <div style="display:flex;gap:0.5rem;align-items:center">
                        <input type="number" class="search-input" id="grantDurationDays" min="1" max="3650" value="30" required style="flex:1">
                        <div style="display:flex;gap:0.25rem">
                            <button type="button" class="btn btn-outline btn-sm" onclick="setGrantDays(7)">7d</button>
                            <button type="button" class="btn btn-outline btn-sm" onclick="setGrantDays(30)">30d</button>
                            <button type="button" class="btn btn-outline btn-sm" onclick="setGrantDays(90)">90d</button>
                            <button type="button" class="btn btn-outline btn-sm" onclick="setGrantDays(365)">1yr</button>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Administrative Reason (Optional)</label>
                    <input type="text" class="search-input" id="grantReason" placeholder="e.g. Merit scholarship, Promotion, Support assistance">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('grantAccessModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitGrant">Confirm Free Access</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1, searchTimer, cachedStudents = [];
const visiblePasswordIds = new Set();

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadStudents(1), 400); }

async function loadStudents(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value.trim();
    const status = document.getElementById('statusFilter')?.value || '';
    document.getElementById('studentsBody').innerHTML = '<tr class="loading-row"><td colspan="9"><div class="spinner-sm"></div> Loading…</td></tr>';
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
            const hasPassword = !!s.account_password;
            const isVisible = visiblePasswordIds.has(s.id);

            return `<tr>
                <td style="color:var(--text-muted)">${(page-1)*20 + i + 1}</td>
                <td>
                    <div style="font-weight:600">${s.name || s.student_profile?.name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.mobile || s.email || '—'}</div>
                </td>
                <td>
                    <div>${s.student_profile?.school_name || '—'}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${s.student_profile?.district || '—'}</div>
                </td>
                <td>${s.student_profile?.class ? 'Class ' + s.student_profile.class : '—'}</td>
                
                <!-- PASSWORD VIEW & COPY CELL (VIEW & COPY ONLY) -->
                <td>
                    ${hasPassword ? `
                        <div style="display:inline-flex;align-items:center;gap:0.38rem;background:var(--bg-surface);padding:0.25rem 0.55rem;border-radius:8px;border:1px solid var(--border-light)">
                            <code id="pwd-val-${s.id}" style="font-family:monospace;font-size:0.82rem;letter-spacing:${isVisible ? '0.04em' : '0.14em'};color:var(--text);font-weight:600;min-width:68px;display:inline-block">${isVisible ? escapeHtml(s.account_password) : '••••••••'}</code>
                            <button type="button" class="btn btn-outline btn-sm" style="padding:0.2rem 0.4rem;font-size:0.75rem;border:none;line-height:1;cursor:pointer" onclick="togglePasswordVisibility(${s.id})" title="${isVisible ? 'Hide password' : 'View password'}">
                                <span id="pwd-eye-${s.id}">${isVisible ? '🙈' : '👁️'}</span>
                            </button>
                            <button type="button" class="btn btn-outline btn-sm" style="padding:0.2rem 0.4rem;font-size:0.75rem;border:none;line-height:1;cursor:pointer" onclick="copyStudentPassword(${s.id})" title="Copy password to clipboard">
                                📋
                            </button>
                        </div>
                    ` : `
                        <span style="color:var(--text-muted);font-size:0.8rem">—</span>
                    `}
                </td>

                <td>
                    ${trialDate ? `<div>${trialDate.toLocaleDateString('en-IN')}</div><div style="font-size:0.7rem"><span class="badge ${trialActive ? 'badge-success' : 'badge-danger'}">${trialActive ? 'Active' : 'Expired'}</span></div>` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td>
                    ${activeSub ? `<span class="badge badge-purple">${activeSub.subscription?.name || 'Active'}</span>` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td><span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span></td>
                <td style="white-space:nowrap">
                    <button class="btn btn-outline btn-sm" onclick="viewStudent(${s.id})">👁 View</button>
                    <button class="btn btn-outline btn-sm" onclick="openGrantAccessModal(${s.id})" style="border-color:var(--primary);color:var(--primary)" title="Grant complimentary subscription or trial">🎁 Free Access</button>
                    <button class="btn btn-sm ${s.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleStudent(${s.id}, ${s.is_active})">
                        ${s.is_active ? 'Suspend' : 'Activate'}
                    </button>
                </td>
            </tr>`;
        }).join('') : '<tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--text-muted)">No students found.</td></tr>';

        renderPagination(meta?.current_page || page, meta?.last_page || 1, meta?.from, meta?.to, total);
    } catch(e) { toast('Failed to load students: ' + e.message, 'error'); }
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── PASSWORD VIEW & COPY HELPERS ────────────────────────────
function togglePasswordVisibility(id) {
    const s = cachedStudents.find(item => item.id === id);
    if (!s || !s.account_password) return;

    const valEl = document.getElementById(`pwd-val-${id}`);
    const eyeEl = document.getElementById(`pwd-eye-${id}`);
    if (!valEl) return;

    if (visiblePasswordIds.has(id)) {
        visiblePasswordIds.delete(id);
        valEl.textContent = '••••••••';
        valEl.style.letterSpacing = '0.12em';
        if (eyeEl) eyeEl.textContent = '👁️';
    } else {
        visiblePasswordIds.add(id);
        valEl.textContent = s.account_password;
        valEl.style.letterSpacing = '0.04em';
        if (eyeEl) eyeEl.textContent = '🙈';
    }
}

async function copyStudentPassword(id) {
    const s = cachedStudents.find(item => item.id === id);
    if (!s || !s.account_password) {
        toast('No viewable password for this student. Use "Set Password" to assign one.', 'info');
        return;
    }

    try {
        await navigator.clipboard.writeText(s.account_password);
        toast(`Password for ${s.name || 'student'} copied to clipboard!`, 'success');
    } catch(e) {
        // Fallback copy using temporary textarea
        const ta = document.createElement('textarea');
        ta.value = s.account_password;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }
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
        <div style="width:100%;display:flex;flex-direction:column;gap:1.15rem">
            <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border-light)">
                ${s.student_profile?.avatar_url
                    ? `<img src="${s.student_profile.avatar_url}" style="width:52px;height:52px;border-radius:50%;object-fit:cover">`
                    : `<div style="width:52px;height:52px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.35rem;font-weight:700">${((s.name || s.student_profile?.name || 'S')[0]).toUpperCase()}</div>`}
                <div style="flex:1">
                    <h3 style="margin:0;font-size:1.1rem">${s.name || s.student_profile?.name || 'Student'}</h3>
                    <div style="font-size:0.8rem;color:var(--text-muted)">User ID #${s.id} · Registered ${new Date(s.created_at).toLocaleDateString('en-IN')}</div>
                </div>
            </div>

            <!-- CREDENTIALS BOX -->
            <div style="background:rgba(109,74,255,0.06);border:1px solid rgba(109,74,255,0.18);padding:0.85rem 1rem;border-radius:12px">
                <div style="font-size:0.72rem;font-weight:800;color:var(--primary);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.6rem">
                    🔐 Student Login & Password
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;font-size:0.85rem">
                    <div>
                        <div style="color:var(--text-muted);font-size:0.72rem;font-weight:600">Mobile (Login ID):</div>
                        <div style="font-weight:700;display:flex;align-items:center;gap:0.4rem;margin-top:0.2rem">
                            <span>${s.mobile || '—'}</span>
                            ${s.mobile ? `<button type="button" class="btn btn-outline btn-sm" style="padding:0.12rem 0.4rem;font-size:0.68rem" onclick="navigator.clipboard.writeText('${s.mobile}');toast('Mobile copied!','success')">📋 Copy</button>` : ''}
                        </div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.72rem;font-weight:600">User Account Password:</div>
                        <div style="margin-top:0.2rem">
                            ${s.account_password ? `
                                <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap">
                                    <code id="modal-pwd-${s.id}" style="font-size:0.85rem;font-weight:700;letter-spacing:0.06em;background:#fff;padding:0.25rem 0.5rem;border-radius:6px;border:1px solid var(--border-light)">••••••••</code>
                                    <button type="button" class="btn btn-outline btn-sm" style="padding:0.18rem 0.45rem;font-size:0.7rem;cursor:pointer" onclick="toggleModalPassword(${s.id})">👁 View</button>
                                    <button type="button" class="btn btn-primary btn-sm" style="padding:0.18rem 0.5rem;font-size:0.7rem;cursor:pointer" onclick="copyStudentPassword(${s.id})">📋 Copy</button>
                                </div>
                            ` : `
                                <span style="color:var(--text-muted);font-size:0.82rem">—</span>
                            `}
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;font-size:0.875rem">
                <div><span style="color:var(--text-muted)">Email:</span> <strong>${s.email || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">Class:</span> <strong>${s.student_profile?.class ? 'Class ' + s.student_profile.class : '—'}</strong></div>
                <div><span style="color:var(--text-muted)">District:</span> <strong>${s.student_profile?.district || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">School:</span> <strong>${s.student_profile?.school_name || '—'}</strong></div>
                <div><span style="color:var(--text-muted)">Status:</span> <span class="badge ${s.is_active ? 'badge-success' : 'badge-danger'}">${s.is_active ? 'Active' : 'Suspended'}</span></div>
                <div><span style="color:var(--text-muted)">Subscription:</span> ${activeSub ? `<span class="badge badge-purple">${activeSub.subscription?.name || 'Active'}</span>` : 'None'}</div>
                <div><span style="color:var(--text-muted)">Trial Started:</span> <div>${trialStartDate}</div></div>
                <div><span style="color:var(--text-muted)">Trial Ends:</span> <div>${trialDate}</div></div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-light)">
                <button class="btn btn-outline btn-sm" onclick="closeModal('studentModal')">Close</button>
                <button class="btn btn-primary btn-sm" onclick="closeModal('studentModal');openGrantAccessModal(${s.id})">🎁 Grant Free Access</button>
            </div>
        </div>
    `;
}

function toggleModalPassword(id) {
    const s = cachedStudents.find(item => item.id === id);
    if (!s || !s.account_password) return;
    const el = document.getElementById(`modal-pwd-${id}`);
    if (!el) return;
    if (el.textContent === '••••••••') {
        el.textContent = s.account_password;
    } else {
        el.textContent = '••••••••';
    }
}

let availablePlans = [];

async function loadPlans() {
    if (availablePlans.length > 0) return;
    try {
        const res = await api('GET', '/admin/subscription-plans');
        availablePlans = Array.isArray(res.data) ? res.data : [];
        const select = document.getElementById('grantSubscriptionId');
        if (!select) return;
        if (availablePlans.length === 0) {
            select.innerHTML = '<option value="">No plans found</option>';
            return;
        }
        select.innerHTML = availablePlans.map(p => 
            `<option value="${p.id}" data-days="${p.duration_days}">${p.name} (${p.duration_days} days - ₹${p.price})</option>`
        ).join('');
        onPlanChange();
    } catch(e) {
        console.error('Failed to load plans:', e);
    }
}

function onPlanChange() {
    const select = document.getElementById('grantSubscriptionId');
    if (!select || !select.options.length) return;
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.dataset.days) {
        document.getElementById('grantDurationDays').value = selectedOption.dataset.days;
    }
}

function toggleAccessTypeFields() {
    const type = document.getElementById('grantAccessType').value;
    const planGroup = document.getElementById('planSelectGroup');
    if (type === 'trial') {
        planGroup.style.display = 'none';
        if (!document.getElementById('grantDurationDays').value) {
            document.getElementById('grantDurationDays').value = '14';
        }
    } else {
        planGroup.style.display = 'block';
        onPlanChange();
    }
}

function setGrantDays(days) {
    document.getElementById('grantDurationDays').value = days;
}

async function openGrantAccessModal(id) {
    const s = cachedStudents.find(item => item.id === id);
    if (!s) return;
    document.getElementById('grantStudentId').value = s.id;
    document.getElementById('grantStudentName').textContent = s.name || s.student_profile?.name || ('Student #' + s.id);
    document.getElementById('grantStudentEmail').textContent = s.email || s.mobile || 'No email/phone';
    document.getElementById('grantReason').value = '';
    document.getElementById('grantAccessType').value = 'subscription';
    toggleAccessTypeFields();
    await loadPlans();
    openModal('grantAccessModal');
}

async function submitGrantAccess(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitGrant');
    const studentId = document.getElementById('grantStudentId').value;
    const accessType = document.getElementById('grantAccessType').value;
    const durationDays = parseInt(document.getElementById('grantDurationDays').value, 10);
    const subscriptionId = document.getElementById('grantSubscriptionId').value;
    const reason = document.getElementById('grantReason').value.trim();

    if (!studentId || !durationDays || durationDays < 1) {
        toast('Please enter a valid duration in days.', 'error');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Granting...';

    try {
        const payload = {
            access_type: accessType,
            duration_days: durationDays,
            reason: reason || undefined
        };
        if (accessType === 'subscription' && subscriptionId) {
            payload.subscription_id = parseInt(subscriptionId, 10);
        }

        const res = await api('POST', `/admin/students/${studentId}/grant-access`, payload);
        toast(res.message || 'Free access granted successfully!', 'success');
        closeModal('grantAccessModal');
        loadStudents(currentPage);
    } catch(err) {
        toast('Error granting access: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Confirm Free Access';
    }
}

async function toggleStudent(id, isActive) {
    if (!confirm(`${isActive ? 'Suspend' : 'Activate'} this student?`)) return;
    try {
        await api('PUT', `/admin/students/${id}`, { is_active: !isActive });
        toast('Student status updated.', 'success');
        loadStudents(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = () => {
    loadStudents(1);
    loadPlans();
};
</script>
@endsection
