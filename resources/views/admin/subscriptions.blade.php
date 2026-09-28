@extends('layouts.admin')
@section('title','Subscriptions')
@section('page-title','Subscriptions')
@section('page-subtitle','Manage plans and view purchase history')

@section('content')
<div class="tabs tab-group-subs">
    <button class="tab-btn active" data-tab="tab-plans" onclick="switchTab('tab-plans','tab-group-subs')">💳 Plans</button>
    <button class="tab-btn" data-tab="tab-purchases" onclick="switchTab('tab-purchases','tab-group-subs');loadPurchases()">📋 Purchases</button>
</div>

<!-- Plans Tab -->
<div class="tab-panel active" id="tab-plans">
    <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
        <button class="btn btn-primary" onclick="openModal('addPlanModal')">＋ Add Plan</button>
    </div>
    <div id="plansGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem">
        <div style="text-align:center;padding:3rem;color:var(--text-muted)"><div class="spinner-sm"></div></div>
    </div>
</div>

<!-- Purchases Tab -->
<div class="tab-panel" id="tab-purchases">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Purchase History</span>
            <select class="filter-select" id="purchaseFilter" onchange="loadPurchases()">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Student</th><th>Plan</th><th>Amount</th><th>Gateway Order</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
                <tbody id="purchasesBody"><tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination" id="purchasePag" style="display:none">
            <span id="purchasePagInfo"></span>
            <div class="pagination-btns" id="purchasePagBtns"></div>
        </div>
    </div>
</div>

<!-- Add Plan Modal -->
<div class="modal-backdrop" id="addPlanModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">💳 Add Subscription Plan</span>
            <button class="modal-close" onclick="closeModal('addPlanModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Plan Name *</label>
                    <input type="text" class="form-control" id="p-name" placeholder="e.g. Monthly">
                </div>
                <div class="form-group">
                    <label class="form-label">Price (₹) *</label>
                    <input type="number" class="form-control" id="p-price" placeholder="199" min="0" step="0.01">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Duration (days) *</label>
                <input type="number" class="form-control" id="p-days" placeholder="30" min="1">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="p-desc" rows="2" placeholder="Brief plan description…"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Features (one per line)</label>
                <textarea class="form-control" id="p-features" rows="4" placeholder="All 7 subjects&#10;Recorded video classes&#10;Live classes"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addPlanModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createPlan()">Create Plan</button>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal-backdrop" id="editPlanModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Plan</span>
            <button class="modal-close" onclick="closeModal('editPlanModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ep-id">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Name</label><input type="text" class="form-control" id="ep-name"></div>
                <div class="form-group"><label class="form-label">Price (₹)</label><input type="number" class="form-control" id="ep-price" min="0" step="0.01"></div>
            </div>
            <div class="form-group"><label class="form-label">Duration (days)</label><input type="number" class="form-control" id="ep-days" min="1"></div>
            <div class="form-group"><label class="form-label">Description</label><textarea class="form-control" id="ep-desc" rows="2"></textarea></div>
            <div class="form-group"><label class="form-label">Features (one per line)</label><textarea class="form-control" id="ep-features" rows="4"></textarea></div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" id="ep-active"><option value="1">Active</option><option value="0">Inactive</option></select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editPlanModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updatePlan()">Save Changes</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadPlans() {
    const res = await api('GET', '/admin/subscription-plans');
    const plans = res.data || [];
    const colors = ['linear-gradient(135deg,#7C5CFC,#9B7DFF)', 'linear-gradient(135deg,#3ECFCF,#22D3EE)', 'linear-gradient(135deg,#F59E0B,#FBBF24)'];
    document.getElementById('plansGrid').innerHTML = plans.length ? plans.map((p, i) => `
        <div style="background:var(--bg-card);border:1px solid var(--border-light);border-radius:20px;overflow:hidden;position:relative">
            <div style="height:4px;background:${colors[i%3]}"></div>
            <div style="padding:1.5rem">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem">
                    <div>
                        <div style="font-size:0.8rem;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem">${p.name}</div>
                        <div style="font-size:2rem;font-weight:800;font-family:'Poppins',sans-serif">₹${parseFloat(p.price).toLocaleString()}</div>
                        <div style="font-size:0.8rem;color:var(--text-muted)">${p.duration_days} days</div>
                    </div>
                    <span class="badge ${p.is_active?'badge-success':'badge-neutral'}">${p.is_active?'Active':'Inactive'}</span>
                </div>
                ${p.description ? `<p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.5">${p.description}</p>` : ''}
                <ul style="list-style:none;display:flex;flex-direction:column;gap:0.35rem;margin-bottom:1.25rem">
                    ${(p.features||[]).map(f => `<li style="font-size:0.8rem;color:var(--text-secondary);display:flex;align-items:center;gap:0.4rem"><span style="color:#4ade80">✓</span>${f}</li>`).join('')}
                </ul>
                <div style="display:flex;gap:0.5rem">
                    <button class="btn btn-outline btn-sm" onclick="editPlan(${JSON.stringify(p).replace(/'/g,'&apos;')})">✏️ Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deletePlan(${p.id})">🗑 Delete</button>
                </div>
            </div>
        </div>
    `).join('') : '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">No plans yet.</div>';
}

async function createPlan() {
    try {
        await api('POST', '/admin/subscription-plans', {
            name: document.getElementById('p-name').value,
            price: parseFloat(document.getElementById('p-price').value),
            duration_days: parseInt(document.getElementById('p-days').value),
            description: document.getElementById('p-desc').value,
            features: document.getElementById('p-features').value.split('\n').filter(f => f.trim()),
        });
        toast('Plan created!', 'success'); closeModal('addPlanModal'); loadPlans();
    } catch(e) { toast(e.message, 'error'); }
}

function editPlan(p) {
    document.getElementById('ep-id').value = p.id;
    document.getElementById('ep-name').value = p.name;
    document.getElementById('ep-price').value = p.price;
    document.getElementById('ep-days').value = p.duration_days;
    document.getElementById('ep-desc').value = p.description || '';
    document.getElementById('ep-features').value = (p.features || []).join('\n');
    document.getElementById('ep-active').value = p.is_active ? '1' : '0';
    openModal('editPlanModal');
}

async function updatePlan() {
    const id = document.getElementById('ep-id').value;
    try {
        await api('PUT', `/admin/subscription-plans/${id}`, {
            name: document.getElementById('ep-name').value,
            price: parseFloat(document.getElementById('ep-price').value),
            duration_days: parseInt(document.getElementById('ep-days').value),
            description: document.getElementById('ep-desc').value,
            features: document.getElementById('ep-features').value.split('\n').filter(f => f.trim()),
            is_active: document.getElementById('ep-active').value === '1',
        });
        toast('Plan updated!', 'success'); closeModal('editPlanModal'); loadPlans();
    } catch(e) { toast(e.message, 'error'); }
}

async function deletePlan(id) {
    if (!confirm('Delete this plan?')) return;
    try { await api('DELETE', `/admin/subscription-plans/${id}`); toast('Plan deleted.', 'success'); loadPlans(); }
    catch(e) { toast(e.message, 'error'); }
}

async function loadPurchases(page = 1) {
    const status = document.getElementById('purchaseFilter').value;
    const res = await api('GET', `/admin/user-subscriptions?page=${page}&status=${status}`);
    const data = res.data || [];
    const statusColors = { completed:'badge-success', pending:'badge-warning', failed:'badge-danger' };
    document.getElementById('purchasesBody').innerHTML = data.length ? data.map(p => `
        <tr>
            <td>
                <div style="font-weight:600">${p.user?.name||'—'}</div>
                <div style="font-size:0.75rem;color:var(--text-muted)">${p.user?.email||'—'}</div>
            </td>
            <td>${p.subscription?.name||'—'}</td>
            <td style="font-weight:700">₹${parseFloat(p.subscription?.price||0).toLocaleString()}</td>
            <td style="font-size:0.75rem;color:var(--text-muted);font-family:monospace">${p.gateway_order_id||'—'}</td>
            <td style="font-size:0.8rem">${p.starts_at ? new Date(p.starts_at).toLocaleDateString('en-IN') : '—'}</td>
            <td style="font-size:0.8rem">${p.ends_at ? new Date(p.ends_at).toLocaleDateString('en-IN') : '—'}</td>
            <td><span class="badge ${statusColors[p.payment_status]||'badge-neutral'}">${p.payment_status}</span></td>
        </tr>
    `).join('') : '<tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">No purchases found.</td></tr>';
}

window._pageInit = () => { loadPlans(); };
</script>
@endsection
