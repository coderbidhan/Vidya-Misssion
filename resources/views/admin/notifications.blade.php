@extends('layouts.admin')
@section('title','Notifications')
@section('page-title','Notifications')
@section('page-subtitle','Send push alerts and in-app broadcasts to students')

@section('content')
<div class="notif-layout">
    <!-- Compose Panel -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📣 Compose Broadcast Alert</span>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Notification Title *</label>
                <input type="text" class="form-control bn-font" id="n-title" placeholder="e.g. 📢 Important Live Session Tomorrow at 6 PM!"
                    maxlength="255" oninput="updatePreview()" required>
            </div>
            <div class="form-group">
                <label class="form-label">Message Body *</label>
                <textarea class="form-control bn-font" id="n-body" rows="4"
                    placeholder="Write message in Bengali or English…" oninput="updatePreview()" style="resize:vertical;min-height:110px" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Target Audience</label>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem">
                    <label class="target-chip active" id="tc-all" onclick="setTarget('all')">
                        <input type="radio" name="target" value="all" checked style="display:none">
                        <span>🌍 All Students</span>
                    </label>
                    <label class="target-chip" id="tc-trial" onclick="setTarget('trial')">
                        <input type="radio" name="target" value="trial" style="display:none">
                        <span>⏳ Trial Users Only</span>
                    </label>
                    <label class="target-chip" id="tc-class" onclick="setTarget('class')">
                        <input type="radio" name="target" value="class" style="display:none">
                        <span>🏫 Specific Class</span>
                    </label>
                </div>

                <!-- Class level selector if specific class chosen -->
                <div id="classTargetWrap" style="display:none;margin-top:0.6rem">
                    <select class="form-control" id="targetClassSelect" onchange="updatePreview()">
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}" {{ $c === 9 ? 'selected' : '' }}>Target Class {{ $c }} Students</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.85rem 1rem;background:var(--bg-app);border-radius:10px;margin-bottom:1.25rem;border:1px solid var(--border-light)">
                <div>
                    <div style="font-size:0.85rem;font-weight:700" id="recipientLabel">All active students</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)" id="recipientSub">Broadcast will reach all registered students</div>
                </div>
                <span id="recipientBadge" class="badge badge-purple">All</span>
            </div>

            <button class="btn btn-primary" style="width:100%;padding:0.8rem;font-size:0.95rem;font-weight:700" onclick="sendNotification()" id="sendBtn">
                🔔 Send Broadcast Notification
            </button>
        </div>
    </div>

    <!-- Preview & Quick Audience Stats Panel -->
    <div style="display:flex;flex-direction:column;gap:1.25rem">
        <div class="card">
            <div class="card-header">
                <span class="card-title">👁 Student In-App Preview</span>
            </div>
            <div class="card-body">
                <div style="background:var(--bg-app);border-radius:14px;padding:1.15rem;border:1px solid var(--border-light)">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#5B45FF,#7F6BFF);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;color:#fff">🔔</div>
                        <div style="min-width:0;flex:1">
                            <div style="font-size:0.88rem;font-weight:800;margin-bottom:0.25rem;color:var(--text)" class="bn-font" id="prev-title">Your notification title…</div>
                            <div style="font-size:0.8rem;color:var(--text-secondary);line-height:1.5" class="bn-font" id="prev-body">Your notification body will appear here…</div>
                            <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.4rem">Just now • VidyaMission App</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Audience Overview</span>
            </div>
            <div class="card-body" id="notifStats">
                <div style="text-align:center"><div class="spinner-sm"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Broadcast History -->
<div class="card" style="margin-top:1.25rem">
    <div class="card-header">
        <span class="card-title">📜 Broadcast History</span>
        <button class="btn btn-outline btn-sm" onclick="loadRecentBroadcasts()">↻ Refresh</button>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Title & Message</th><th>Target Type</th><th>Sent Date & Time</th><th>Delivered To</th></tr></thead>
            <tbody id="recentBroadcastsBody">
                <tr class="loading-row"><td colspan="5"><div class="spinner-sm"></div> Loading history…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Send Result Modal -->
<div class="modal-backdrop" id="sendResultModal">
    <div class="modal" style="max-width:380px">
        <div class="modal-body" style="text-align:center;padding:2rem">
            <div style="font-size:3rem;margin-bottom:0.75rem" id="resultIcon">🎉</div>
            <h3 style="font-size:1.15rem;font-weight:800;margin-bottom:0.4rem" id="resultTitle">Broadcast Dispatched!</h3>
            <p style="color:var(--text-muted);font-size:0.85rem" id="resultBody">Your notification was delivered to students.</p>
            <button class="btn btn-primary" style="margin-top:1.25rem;width:100%" onclick="closeModal('sendResultModal')">Done</button>
        </div>
    </div>
</div>

<style>
.notif-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.25rem;
}

@media (max-width: 860px) {
    .notif-layout { grid-template-columns: 1fr; }
}

.target-chip {
    padding: 0.5rem 0.85rem;
    border-radius: 10px;
    border: 1.5px solid var(--border-light);
    background: #fff;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 700;
    transition: all 0.15s;
    color: var(--text-secondary);
}
.target-chip:hover { border-color: var(--primary); }
.target-chip.active {
    background: rgba(91,69,255,0.1);
    border-color: var(--primary);
    color: var(--primary);
}
</style>
@endsection

@section('scripts')
<script>
let currentTarget = 'all';

function setTarget(val) {
    currentTarget = val;
    document.querySelectorAll('.target-chip').forEach(el => el.classList.remove('active'));
    document.getElementById('tc-' + val)?.classList.add('active');

    const classWrap = document.getElementById('classTargetWrap');
    if (val === 'class') {
        classWrap.style.display = 'block';
        const clsVal = document.getElementById('targetClassSelect').value;
        document.getElementById('recipientLabel').textContent = `Class ${clsVal} Students`;
        document.getElementById('recipientSub').textContent = `Notification will only reach students enrolled in Class ${clsVal}`;
        document.getElementById('recipientBadge').textContent = `Class ${clsVal}`;
    } else {
        classWrap.style.display = 'none';
        const labels = {
            all:   ['All Registered Students', 'Broadcast will reach all active students in the system', 'All'],
            trial: ['Free Trial Users', 'Only students currently within their 7-day trial period', 'Trial']
        };
        const [label, sub, badge] = labels[val];
        document.getElementById('recipientLabel').textContent = label;
        document.getElementById('recipientSub').textContent = sub;
        document.getElementById('recipientBadge').textContent = badge;
    }
}

function updatePreview() {
    const title = document.getElementById('n-title').value || 'Your notification title…';
    const body  = document.getElementById('n-body').value  || 'Your notification body will appear here…';
    document.getElementById('prev-title').textContent = title;
    document.getElementById('prev-body').textContent  = body;

    if (currentTarget === 'class') {
        const clsVal = document.getElementById('targetClassSelect').value;
        document.getElementById('recipientLabel').textContent = `Class ${clsVal} Students`;
        document.getElementById('recipientBadge').textContent = `Class ${clsVal}`;
    }
}

async function sendNotification() {
    const title = document.getElementById('n-title').value.trim();
    const body  = document.getElementById('n-body').value.trim();
    if (!title || !body) { toast('Please enter notification title and message.', 'error'); return; }

    document.getElementById('sendBtn').disabled = true;
    document.getElementById('sendBtn').textContent = 'Sending broadcast…';

    const payload = {
        title,
        body,
        target_type: currentTarget,
        target_class: currentTarget === 'class' ? parseInt(document.getElementById('targetClassSelect').value) : null,
    };

    try {
        const res = await api('POST', '/admin/notifications/broadcast', payload);
        const count = res.data?.recipients_count || 0;
        document.getElementById('resultIcon').textContent = '🎉';
        document.getElementById('resultTitle').textContent = 'Notification Sent!';
        document.getElementById('resultBody').textContent = `Successfully delivered to ${count.toLocaleString()} student${count !== 1 ? 's' : ''}.`;
        openModal('sendResultModal');
        document.getElementById('n-title').value = '';
        document.getElementById('n-body').value = '';
        updatePreview();
        loadRecentBroadcasts();
    } catch(e) {
        toast('Failed to send: ' + e.message, 'error');
    } finally {
        document.getElementById('sendBtn').disabled = false;
        document.getElementById('sendBtn').innerHTML = '🔔 Send Broadcast Notification';
    }
}

async function loadStats() {
    try {
        const res = await api('GET', '/admin/analytics/overview');
        const o = res.data || {};
        document.getElementById('notifStats').innerHTML = `
            <div style="display:flex;flex-direction:column;gap:0.75rem">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--border-light)">
                    <span style="font-size:0.84rem;color:var(--text-muted)">👨‍🎓 Total Students</span>
                    <strong style="color:var(--primary)">${(o.total_students || 0).toLocaleString()}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid var(--border-light)">
                    <span style="font-size:0.84rem;color:var(--text-muted)">⏳ Active Trial</span>
                    <strong style="color:var(--warning)">${(o.trial_users || 0).toLocaleString()}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0">
                    <span style="font-size:0.84rem;color:var(--text-muted)">✅ Active Subscribed</span>
                    <strong style="color:var(--success)">${(o.active_subscriptions || 0).toLocaleString()}</strong>
                </div>
            </div>`;
    } catch(e) { document.getElementById('notifStats').innerHTML = '<p style="color:var(--text-muted);font-size:0.8rem">Could not load stats.</p>'; }
}

async function loadRecentBroadcasts() {
    try {
        const res = await api('GET', '/admin/notifications/recent');
        const items = res.data || [];
        document.getElementById('recentBroadcastsBody').innerHTML = items.length ? items.map((b, i) => `
            <tr>
                <td style="color:var(--text-muted)">#${i+1}</td>
                <td>
                    <div style="font-weight:700;font-size:0.88rem" class="bn-font">${b.title}</div>
                    <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.15rem" class="bn-font">${b.body}</div>
                </td>
                <td><span class="badge badge-purple">${b.type || 'Broadcast'}</span></td>
                <td style="font-size:0.8rem;color:var(--text-muted)">${new Date(b.created_at).toLocaleString('en-IN')}</td>
                <td><span class="badge badge-success">${b.recipients_count || 1} recipients</span></td>
            </tr>
        `).join('') : '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">No past broadcast history.</td></tr>';
    } catch(e) { toast('Error loading broadcasts: ' + e.message, 'error'); }
}

window._pageInit = () => {
    loadStats();
    loadRecentBroadcasts();
};
</script>
@endsection
