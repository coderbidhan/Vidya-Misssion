@extends('layouts.admin')
@section('title','Notifications')
@section('page-title','Notifications')
@section('page-subtitle','Send broadcast notifications to students')

@section('content')
<style>
.notif-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.25rem;
}
@media (max-width: 900px) {
    .notif-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<div class="notif-grid">
    <!-- Compose -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📣 Compose Notification</span>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Notification Title *</label>
                <input type="text" class="form-control" id="n-title" placeholder="e.g. New Live Class Tomorrow!"
                    maxlength="255" oninput="updatePreview()">
            </div>
            <div class="form-group">
                <label class="form-label">Message Body *</label>
                <textarea class="form-control" id="n-body" rows="5"
                    placeholder="Write your message here…" oninput="updatePreview()" style="resize:vertical;min-height:120px"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Send To</label>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;padding:0.6rem 1rem;border-radius:10px;border:1px solid var(--border-light);transition:all 0.2s" id="rb-all-wrap" onclick="selectTarget('all')">
                        <input type="radio" name="target" value="all" id="rb-all" checked style="accent-color:var(--primary)">
                        <span style="font-size:0.875rem;font-weight:600">🌍 All Students</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;padding:0.6rem 1rem;border-radius:10px;border:1px solid var(--border-light);transition:all 0.2s" id="rb-trial-wrap" onclick="selectTarget('trial')">
                        <input type="radio" name="target" value="trial" id="rb-trial" style="accent-color:var(--primary)">
                        <span style="font-size:0.875rem;font-weight:600">⏳ Trial Users</span>
                    </label>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem;background:rgba(255,255,255,0.03);border-radius:10px;margin-bottom:1.25rem">
                <div>
                    <div style="font-size:0.85rem;font-weight:600" id="recipientLabel">All active students</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)" id="recipientSub">Notification will be sent to all active students</div>
                </div>
                <span id="recipientBadge" class="badge badge-purple">All</span>
            </div>
            <button class="btn btn-primary" style="width:100%;padding:0.875rem;font-size:1rem" onclick="sendNotification()" id="sendBtn">
                🔔 Send Notification
            </button>
        </div>
    </div>

    <!-- Preview -->
    <div>
        <div class="card" style="margin-bottom:1.25rem">
            <div class="card-header">
                <span class="card-title">👁 Preview</span>
            </div>
            <div class="card-body">
                <div style="background:var(--bg-surface);border-radius:14px;padding:1.25rem;border:1px solid var(--border-light)">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem">🎓</div>
                        <div>
                            <div style="font-size:0.85rem;font-weight:700;margin-bottom:0.25rem" id="prev-title">Your notification title…</div>
                            <div style="font-size:0.8rem;color:var(--text-secondary);line-height:1.5" id="prev-body">Your notification body will appear here…</div>
                            <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.5rem">Just now · VidyaMission</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Quick Stats</span>
            </div>
            <div class="card-body" id="notifStats">
                <div style="text-align:center"><div class="spinner-sm"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Send result modal -->
<div class="modal-backdrop" id="sendResultModal">
    <div class="modal" style="max-width:380px">
        <div class="modal-body" style="text-align:center;padding:2.5rem">
            <div style="font-size:3rem;margin-bottom:1rem" id="resultIcon">✅</div>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem" id="resultTitle">Notification Sent!</h3>
            <p style="color:var(--text-muted);font-size:0.875rem" id="resultBody">Your notification was delivered successfully.</p>
            <button class="btn btn-primary" style="margin-top:1.5rem;width:100%" onclick="closeModal('sendResultModal')">Done</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentTarget = 'all';

function selectTarget(val) {
    currentTarget = val;
    const labels = { all: ['All active students', 'Notification will be sent to all active students', 'All'],
                     trial: ['Trial Users', 'Only students currently on free trial', 'Trial'] };
    const [label, sub, badge] = labels[val];
    document.getElementById('recipientLabel').textContent = label;
    document.getElementById('recipientSub').textContent = sub;
    document.getElementById('recipientBadge').textContent = badge;
}

function updatePreview() {
    const title = document.getElementById('n-title').value || 'Your notification title…';
    const body  = document.getElementById('n-body').value  || 'Your notification body will appear here…';
    document.getElementById('prev-title').textContent = title;
    document.getElementById('prev-body').textContent  = body;
}

async function sendNotification() {
    const title = document.getElementById('n-title').value.trim();
    const body  = document.getElementById('n-body').value.trim();
    if (!title || !body) { toast('Please fill in both title and message.', 'error'); return; }

    document.getElementById('sendBtn').disabled = true;
    document.getElementById('sendBtn').textContent = 'Sending…';

    try {
        const res = await api('POST', '/admin/notifications/broadcast', {
            title, body,
            user_ids: 'all',
        });
        const count = res.data?.recipients_count || 0;
        document.getElementById('resultIcon').textContent = '🎉';
        document.getElementById('resultTitle').textContent = 'Notification Sent!';
        document.getElementById('resultBody').textContent = `Successfully delivered to ${count.toLocaleString()} student${count!==1?'s':''}.`;
        openModal('sendResultModal');
        document.getElementById('n-title').value = '';
        document.getElementById('n-body').value = '';
        updatePreview();
    } catch(e) {
        toast('Failed to send: ' + e.message, 'error');
    } finally {
        document.getElementById('sendBtn').disabled = false;
        document.getElementById('sendBtn').innerHTML = '🔔 Send Notification';
    }
}

async function loadStats() {
    try {
        const res = await api('GET', '/admin/analytics/overview');
        const o = res.data;
        document.getElementById('notifStats').innerHTML = `
            <div style="display:flex;flex-direction:column;gap:0.75rem">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0;border-bottom:1px solid var(--border-light)">
                    <span style="font-size:0.85rem;color:var(--text-muted)">👨‍🎓 Total Students</span>
                    <strong>${o.total_students||0}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0;border-bottom:1px solid var(--border-light)">
                    <span style="font-size:0.85rem;color:var(--text-muted)">⏳ Trial Active</span>
                    <strong>${o.trial_users||0}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0">
                    <span style="font-size:0.85rem;color:var(--text-muted)">✅ Subscribed</span>
                    <strong>${o.active_subscriptions||0}</strong>
                </div>
            </div>`;
    } catch(e) { document.getElementById('notifStats').innerHTML = '<p style="color:var(--text-muted);font-size:0.8rem">Could not load stats.</p>'; }
}

window._pageInit = () => loadStats();
</script>
@endsection
