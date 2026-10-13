@extends('layouts.admin')
@section('title', 'Payments & Transactions')
@section('page-title', 'Payments & Transactions')
@section('page-subtitle', 'Monitor Razorpay orders, captured payments, refunds, and webhook events')

@section('topbar-actions')
<button class="btn btn-outline btn-sm" onclick="loadDashboard(); loadPayments();">🔄 Refresh</button>
@endsection

@section('content')
<!-- KPI Metric Cards -->
<div class="stats-grid" id="statsGrid">
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#10B981,#34D399)">
        <div class="stat-icon">💰</div>
        <div class="stat-value" id="statCaptured">₹0</div>
        <div class="stat-label">Total Captured Revenue</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#6D4AFF,#8B6BFF)">
        <div class="stat-icon">📈</div>
        <div class="stat-value" id="statNet">₹0</div>
        <div class="stat-label">Net Collected (After Refunds)</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#0ABCBC,#22D3EE)">
        <div class="stat-icon">✅</div>
        <div class="stat-value" id="statSuccessCount">0</div>
        <div class="stat-label">Successful Payments</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#F59E0B,#FBBF24)">
        <div class="stat-icon">⏳</div>
        <div class="stat-value" id="statPendingCount">0</div>
        <div class="stat-label">Pending Orders</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#EF4444,#F87171)">
        <div class="stat-icon">💸</div>
        <div class="stat-value" id="statRefunded">₹0</div>
        <div class="stat-label">Total Processed Refunds</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg,#6B7280,#9CA3AF)">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value" id="statFailedCount">0</div>
        <div class="stat-label">Failed Transactions</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs tab-group-pay">
    <button class="tab-btn active" data-tab="tab-txns" onclick="switchTab('tab-txns','tab-group-pay')">💳 All Transactions</button>
    <button class="tab-btn" data-tab="tab-webhooks" onclick="switchTab('tab-webhooks','tab-group-pay'); loadWebhooks()">🔔 Webhook Audit Log</button>
</div>

<!-- Tab: All Transactions -->
<div class="tab-panel active" id="tab-txns">
    <div class="card">
        <div class="card-header" style="flex-wrap:wrap;gap:0.75rem;">
            <span class="card-title">Transaction History</span>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search user, order, payment ID..." style="width:230px;" onkeyup="if(event.key==='Enter')loadPayments(1)">
                <select class="filter-select" id="statusFilter" onchange="loadPayments(1)">
                    <option value="">All Statuses</option>
                    <option value="captured">Captured (Success)</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                    <option value="partially_refunded">Partially Refunded</option>
                    <option value="refunded">Refunded</option>
                </select>
                <select class="filter-select" id="purposeFilter" onchange="loadPayments(1)">
                    <option value="">All Purposes</option>
                    <option value="subscription_purchase">Subscription Purchase</option>
                    <option value="subscription_renewal">Subscription Renewal</option>
                    <option value="one_time_purchase">One-Time Purchase</option>
                </select>
                <button class="btn btn-primary btn-sm" onclick="loadPayments(1)">Filter</button>
                <button class="btn btn-outline btn-sm" onclick="resetFilters()">Reset</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID / Receipt</th>
                        <th>Customer</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <th>Gateway IDs</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsBody">
                    <tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading transactions…</td></tr>
                </tbody>
            </table>
        </div>

        <div class="pagination" id="paymentsPag" style="display:none">
            <span id="paymentsPagInfo"></span>
            <div class="pagination-btns" id="paymentsPagBtns"></div>
        </div>
    </div>
</div>

<!-- Tab: Webhook Events Log -->
<div class="tab-panel" id="tab-webhooks">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Razorpay Webhook Events Log</span>
            <div style="display:flex;gap:0.5rem;">
                <select class="filter-select" id="webhookStatusFilter" onchange="loadWebhooks(1)">
                    <option value="">All Event Statuses</option>
                    <option value="processed">Processed</option>
                    <option value="received">Received</option>
                    <option value="failed">Failed</option>
                    <option value="ignored">Ignored / Duplicate</option>
                </select>
                <button class="btn btn-outline btn-sm" onclick="loadWebhooks(1)">Refresh</button>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Event Type</th>
                        <th>Status</th>
                        <th>Error Details</th>
                        <th>Received At</th>
                    </tr>
                </thead>
                <tbody id="webhooksBody">
                    <tr class="loading-row"><td colspan="5"><div class="spinner-sm"></div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="pagination" id="webhooksPag" style="display:none">
            <span id="webhooksPagInfo"></span>
            <div class="pagination-btns" id="webhooksPagBtns"></div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal-backdrop" id="viewPaymentModal">
    <div class="modal" style="max-width:680px">
        <div class="modal-header">
            <span class="modal-title">🔍 Payment Details</span>
            <button class="modal-close" onclick="closeModal('viewPaymentModal')">×</button>
        </div>
        <div class="modal-body" id="viewPaymentContent">
            <div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('viewPaymentModal')">Close</button>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal-backdrop" id="refundModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">💸 Issue Refund</span>
            <button class="modal-close" onclick="closeModal('refundModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="rf-payment-id">
            <div style="background:var(--bg-surface);padding:1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem">
                <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem">
                    <span style="color:var(--text-muted)">Original Amount:</span>
                    <strong id="rf-orig-amount">₹0</strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem">
                    <span style="color:var(--text-muted)">Already Refunded:</span>
                    <span id="rf-already-refunded" style="color:var(--danger)">₹0</span>
                </div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-light);padding-top:0.35rem">
                    <span style="color:var(--text-muted)">Maximum Refundable:</span>
                    <strong id="rf-max-refundable" style="color:var(--success)">₹0</strong>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Refund Amount (₹) *</label>
                <input type="number" class="form-control" id="rf-amount" step="0.01" min="1" placeholder="Enter amount">
                <small style="color:var(--text-muted);font-size:0.75rem">Can be full or partial amount up to refundable balance.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Refund Reason</label>
                <select class="form-control" id="rf-reason-select" onchange="document.getElementById('rf-reason').value = this.value">
                    <option value="">Select a reason...</option>
                    <option value="Customer requested cancellation">Customer requested cancellation</option>
                    <option value="Accidental duplicate payment">Accidental duplicate payment</option>
                    <option value="Technical or course issue">Technical or course issue</option>
                    <option value="Fraudulent transaction">Fraudulent transaction</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" class="form-control" id="rf-reason" placeholder="Or enter custom reason..." style="margin-top:0.5rem">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('refundModal')">Cancel</button>
            <button class="btn btn-danger" id="rf-submit-btn" onclick="submitRefund()">Process Refund</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;

async function loadDashboard() {
    try {
        const res = await api('GET', '/admin/payments/dashboard');
        const data = res.data || {};
        const rev = data.revenue || {};
        const counts = data.counts || {};

        document.getElementById('statCaptured').innerText = '₹' + (rev.captured_rupees || 0).toLocaleString();
        document.getElementById('statNet').innerText = '₹' + (rev.net_rupees || 0).toLocaleString();
        document.getElementById('statRefunded').innerText = '₹' + (rev.refunded_rupees || 0).toLocaleString();

        document.getElementById('statSuccessCount').innerText = counts.captured || 0;
        document.getElementById('statPendingCount').innerText = counts.pending || 0;
        document.getElementById('statFailedCount').innerText = counts.failed || 0;
    } catch (e) {
        console.error('Error loading dashboard:', e);
    }
}

async function loadPayments(page = 1) {
    currentPage = page;
    const search = encodeURIComponent(document.getElementById('searchFilter').value.trim());
    const status = encodeURIComponent(document.getElementById('statusFilter').value);
    const purpose = encodeURIComponent(document.getElementById('purposeFilter').value);

    document.getElementById('paymentsBody').innerHTML = '<tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading…</td></tr>';

    try {
        const url = `/admin/payments?page=${page}&search=${search}&status=${status}&purpose=${purpose}`;
        const res = await api('GET', url);
        const paginated = res.data || {};
        const items = Array.isArray(paginated.data) ? paginated.data : (Array.isArray(paginated) ? paginated : []);

        const statusBadges = {
            captured: '<span class="badge badge-success">Captured</span>',
            pending: '<span class="badge badge-warning">Pending</span>',
            failed: '<span class="badge badge-danger">Failed</span>',
            partially_refunded: '<span class="badge badge-info">Partially Refunded</span>',
            refunded: '<span class="badge badge-purple">Refunded</span>'
        };

        const purposeLabels = {
            subscription_purchase: 'Sub Purchase',
            subscription_renewal: 'Sub Renewal',
            one_time_purchase: 'One-Time'
        };

        if (!items.length) {
            document.getElementById('paymentsBody').innerHTML = '<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No transactions found matching your criteria.</td></tr>';
            document.getElementById('paymentsPag').style.display = 'none';
            return;
        }

        document.getElementById('paymentsBody').innerHTML = items.map(p => {
            const amountRupees = (p.amount / 100).toFixed(2);
            const dateStr = p.created_at ? new Date(p.created_at).toLocaleString('en-IN', { dateStyle:'medium', timeStyle:'short' }) : '—';
            const refundable = Math.max(0, p.amount - (p.amount_refunded || 0));

            return `
                <tr>
                    <td>
                        <div style="font-weight:600;font-family:monospace;font-size:0.8rem">${p.receipt || p.uuid.substring(0,8)}</div>
                        <div style="font-size:0.7rem;color:var(--text-muted);font-family:monospace">${p.uuid}</div>
                    </td>
                    <td>
                        <div style="font-weight:600">${escapeHtml(p.user?.name || '—')}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted)">${escapeHtml(p.user?.email || p.user?.mobile || '—')}</div>
                    </td>
                    <td>
                        <span class="badge badge-neutral" style="font-size:0.68rem">${purposeLabels[p.purpose] || p.purpose}</span>
                    </td>
                    <td>
                        <div style="font-weight:700">₹${parseFloat(amountRupees).toLocaleString()}</div>
                        ${p.amount_refunded > 0 ? `<div style="font-size:0.7rem;color:var(--danger)">Refunded: ₹${(p.amount_refunded/100).toFixed(2)}</div>` : ''}
                    </td>
                    <td>
                        <div style="font-size:0.75rem;font-family:monospace" title="Order ID">📦 ${p.gateway_order_id || '—'}</div>
                        <div style="font-size:0.75rem;font-family:monospace;color:var(--text-muted)" title="Payment ID">💳 ${p.gateway_payment_id || '—'}</div>
                    </td>
                    <td>${statusBadges[p.status] || `<span class="badge badge-neutral">${p.status}</span>`}</td>
                    <td style="font-size:0.78rem;white-space:nowrap">${dateStr}</td>
                    <td style="text-align:right;white-space:nowrap">
                        <button class="btn btn-outline btn-sm" onclick="viewPayment(${p.id})" title="View Details">👁️</button>
                        ${p.status === 'pending' ? `<button class="btn btn-outline btn-sm" onclick="reconcilePayment(${p.id})" title="Sync with Gateway">🔄</button>` : ''}
                        ${(p.is_captured || p.status === 'captured' || p.status === 'partially_refunded') && refundable > 0 ? `
                            <button class="btn btn-danger btn-sm" onclick="openRefundModal(${p.id}, ${p.amount}, ${p.amount_refunded||0})" title="Issue Refund">💸</button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');

        // Pagination
        if (paginated.total > paginated.per_page) {
            document.getElementById('paymentsPag').style.display = 'flex';
            document.getElementById('paymentsPagInfo').innerText = `Showing ${paginated.from || 1}–${paginated.to || items.length} of ${paginated.total}`;
            let btns = '';
            if (paginated.prev_page_url) {
                btns += `<button class="page-btn" onclick="loadPayments(${page - 1})">‹ Prev</button>`;
            }
            btns += `<button class="page-btn active">${paginated.current_page}</button>`;
            if (paginated.next_page_url) {
                btns += `<button class="page-btn" onclick="loadPayments(${page + 1})">Next ›</button>`;
            }
            document.getElementById('paymentsPagBtns').innerHTML = btns;
        } else {
            document.getElementById('paymentsPag').style.display = 'none';
        }
    } catch (e) {
        toast('Failed to load transactions: ' + e.message, 'error');
    }
}

function resetFilters() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('purposeFilter').value = '';
    loadPayments(1);
}

async function viewPayment(id) {
    openModal('viewPaymentModal');
    document.getElementById('viewPaymentContent').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';

    try {
        const res = await api('GET', `/admin/payments/${id}`);
        const p = res.data?.payment || {};
        const events = res.data?.webhook_events || [];

        const amountRupees = (p.amount / 100).toFixed(2);
        const refundedRupees = (p.amount_refunded / 100).toFixed(2);
        const refundableRupees = (Math.max(0, p.amount - (p.amount_refunded || 0)) / 100).toFixed(2);

        document.getElementById('viewPaymentContent').innerHTML = `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem">
                <div style="background:var(--bg-surface);padding:0.875rem;border-radius:10px">
                    <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase">Customer</div>
                    <div style="font-weight:700;margin-top:0.25rem">${escapeHtml(p.user?.name || '—')}</div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">${escapeHtml(p.user?.email || '—')}</div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">${escapeHtml(p.user?.mobile || '—')}</div>
                </div>
                <div style="background:var(--bg-surface);padding:0.875rem;border-radius:10px">
                    <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase">Financial Summary</div>
                    <div style="font-size:1.4rem;font-weight:800;color:var(--text);margin-top:0.25rem">₹${parseFloat(amountRupees).toLocaleString()} <span style="font-size:0.8rem;font-weight:500;color:var(--text-muted)">${p.currency}</span></div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">Refunded: ₹${refundedRupees} | Refundable: ₹${refundableRupees}</div>
                </div>
            </div>

            <div style="background:var(--bg-card);border:1px solid var(--border-light);border-radius:10px;padding:1rem;margin-bottom:1.25rem">
                <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase">Gateway Identifiers</div>
                <div style="display:grid;grid-template-columns:140px 1fr;gap:0.4rem;font-size:0.82rem">
                    <span style="color:var(--text-muted)">Internal UUID:</span><code style="word-break:break-all">${p.uuid}</code>
                    <span style="color:var(--text-muted)">Receipt:</span><code>${p.receipt || '—'}</code>
                    <span style="color:var(--text-muted)">Razorpay Order ID:</span><code>${p.gateway_order_id || '—'}</code>
                    <span style="color:var(--text-muted)">Razorpay Payment ID:</span><code>${p.gateway_payment_id || '—'}</code>
                    <span style="color:var(--text-muted)">Payment Method:</span><span>${p.payment_method || '—'}</span>
                    <span style="color:var(--text-muted)">Internal Status:</span><strong>${p.status}</strong>
                    <span style="color:var(--text-muted)">Gateway Status:</span><span>${p.gateway_status || '—'}</span>
                    <span style="color:var(--text-muted)">Captured At:</span><span>${p.captured_at ? new Date(p.captured_at).toLocaleString('en-IN') : '—'}</span>
                    ${p.failure_reason ? `<span style="color:var(--danger)">Failure Reason:</span><span style="color:var(--danger)">[${p.failure_code||'ERR'}] ${escapeHtml(p.failure_reason)}</span>` : ''}
                </div>
            </div>

            ${p.refunds && p.refunds.length ? `
                <div style="margin-bottom:1.25rem">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase">Refund Records (${p.refunds.length})</div>
                    <table style="width:100%;font-size:0.8rem">
                        <thead><tr><th>Refund ID</th><th>Amount</th><th>Status</th><th>Initiated By</th><th>Date</th></tr></thead>
                        <tbody>
                            ${p.refunds.map(rf => `
                                <tr>
                                    <td><code>${rf.gateway_refund_id || 'Pending'}</code></td>
                                    <td>₹${(rf.amount/100).toFixed(2)}</td>
                                    <td><span class="badge ${rf.status==='processed'?'badge-success':'badge-warning'}">${rf.status}</span></td>
                                    <td>${escapeHtml(rf.initiator?.name || 'System')}</td>
                                    <td>${rf.created_at ? new Date(rf.created_at).toLocaleDateString('en-IN') : '—'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            ` : ''}

            <div>
                <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase">Recent Webhook Audit Events</div>
                ${events.length ? `
                    <div style="max-height:160px;overflow-y:auto;border:1px solid var(--border-light);border-radius:8px">
                        <table style="width:100%;font-size:0.78rem">
                            <thead><tr><th>Event</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                ${events.map(ev => `
                                    <tr>
                                        <td><code>${ev.event_type}</code></td>
                                        <td><span class="badge ${ev.status==='processed'?'badge-success':(ev.status==='failed'?'badge-danger':'badge-neutral')}">${ev.status}</span></td>
                                        <td>${new Date(ev.created_at).toLocaleString('en-IN')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                ` : '<div style="font-size:0.8rem;color:var(--text-muted)">No webhook events logged for this order.</div>'}
            </div>
        `;
    } catch (e) {
        document.getElementById('viewPaymentContent').innerHTML = `<div style="color:var(--danger);padding:1rem">${escapeHtml(e.message)}</div>`;
    }
}

async function reconcilePayment(id) {
    try {
        toast('Syncing payment with Razorpay...', 'info');
        const res = await api('POST', `/admin/payments/${id}/reconcile`);
        toast('Payment reconciled successfully!', 'success');
        loadDashboard();
        loadPayments(currentPage);
    } catch (e) {
        toast('Reconciliation failed: ' + e.message, 'error');
    }
}

function openRefundModal(paymentId, amountPaise, refundedPaise) {
    const refundablePaise = Math.max(0, amountPaise - refundedPaise);
    const origRupees = (amountPaise / 100).toFixed(2);
    const refundedRupees = (refundedPaise / 100).toFixed(2);
    const maxRefundRupees = (refundablePaise / 100).toFixed(2);

    document.getElementById('rf-payment-id').value = paymentId;
    document.getElementById('rf-orig-amount').innerText = '₹' + origRupees;
    document.getElementById('rf-already-refunded').innerText = '₹' + refundedRupees;
    document.getElementById('rf-max-refundable').innerText = '₹' + maxRefundRupees;

    const input = document.getElementById('rf-amount');
    input.max = maxRefundRupees;
    input.value = maxRefundRupees; // Default to full remaining balance
    document.getElementById('rf-reason').value = '';
    document.getElementById('rf-reason-select').value = '';

    openModal('refundModal');
}

async function submitRefund() {
    const id = document.getElementById('rf-payment-id').value;
    const amount = parseFloat(document.getElementById('rf-amount').value);
    const reason = document.getElementById('rf-reason').value.trim() || document.getElementById('rf-reason-select').value;

    if (!amount || amount <= 0) {
        toast('Please enter a valid refund amount greater than 0.', 'error');
        return;
    }

    if (!confirm(`Are you sure you want to process a refund of ₹${amount.toFixed(2)}? This action will immediately contact Razorpay and debit your account.`)) {
        return;
    }

    const btn = document.getElementById('rf-submit-btn');
    btn.disabled = true;
    btn.innerText = 'Processing...';

    try {
        const res = await api('POST', `/admin/payments/${id}/refund`, {
            amount: amount,
            reason: reason || 'Customer refund requested by admin'
        });

        toast('Refund processed successfully!', 'success');
        closeModal('refundModal');
        loadDashboard();
        loadPayments(currentPage);
    } catch (e) {
        toast('Refund failed: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Process Refund';
    }
}

async function loadWebhooks(page = 1) {
    const status = encodeURIComponent(document.getElementById('webhookStatusFilter').value);
    document.getElementById('webhooksBody').innerHTML = '<tr class="loading-row"><td colspan="5"><div class="spinner-sm"></div></td></tr>';

    try {
        const res = await api('GET', `/admin/payments/webhook-events?page=${page}&status=${status}`);
        const paginated = res.data || {};
        const items = Array.isArray(paginated.data) ? paginated.data : [];

        if (!items.length) {
            document.getElementById('webhooksBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No webhook events recorded.</td></tr>';
            document.getElementById('webhooksPag').style.display = 'none';
            return;
        }

        const statusBadges = {
            processed: '<span class="badge badge-success">Processed</span>',
            received: '<span class="badge badge-warning">Received</span>',
            failed: '<span class="badge badge-danger">Failed</span>',
            ignored: '<span class="badge badge-neutral">Ignored</span>'
        };

        document.getElementById('webhooksBody').innerHTML = items.map(ev => `
            <tr>
                <td><code style="font-size:0.75rem">${ev.event_id}</code></td>
                <td><span class="badge badge-purple">${ev.event_type}</span></td>
                <td>${statusBadges[ev.status] || ev.status}</td>
                <td style="font-size:0.75rem;color:var(--danger)">${escapeHtml(ev.error_message || '—')}</td>
                <td style="font-size:0.78rem">${ev.created_at ? new Date(ev.created_at).toLocaleString('en-IN') : '—'}</td>
            </tr>
        `).join('');

        if (paginated.total > paginated.per_page) {
            document.getElementById('webhooksPag').style.display = 'flex';
            document.getElementById('webhooksPagInfo').innerText = `Showing ${paginated.from || 1}–${paginated.to || items.length} of ${paginated.total}`;
            let btns = '';
            if (paginated.prev_page_url) {
                btns += `<button class="page-btn" onclick="loadWebhooks(${page - 1})">‹ Prev</button>`;
            }
            btns += `<button class="page-btn active">${paginated.current_page}</button>`;
            if (paginated.next_page_url) {
                btns += `<button class="page-btn" onclick="loadWebhooks(${page + 1})">Next ›</button>`;
            }
            document.getElementById('webhooksPagBtns').innerHTML = btns;
        } else {
            document.getElementById('webhooksPag').style.display = 'none';
        }
    } catch (e) {
        toast('Failed to load webhook events: ' + e.message, 'error');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

window._pageInit = () => {
    loadDashboard();
    loadPayments();
};
</script>
@endsection
