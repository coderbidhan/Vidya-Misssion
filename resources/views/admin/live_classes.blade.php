@extends('layouts.admin')
@section('title', 'Live Classes Management')
@section('page-title', 'Live Classes Management')
@section('page-subtitle', 'Schedule, monitor, and manage interactive live classes for Classes 1 through 10')

@section('content')
<style>
/* ─── LIVE DASHBOARD SPECIFIC STYLES ─── */
.live-pulse {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10B981;
    margin-right: 6px;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse-green 1.6s infinite;
}
@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.attention-badge {
    background: rgba(239, 68, 68, 0.12);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.25);
    font-weight: 700;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 0.6rem;
    align-items: center;
    margin-bottom: 1.25rem;
    padding: 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 12px;
}
.filter-search-wrap {
    grid-column: span 2;
}

.platform-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    font-size: 0.73rem;
    font-weight: 600;
}
.platform-meet { background: rgba(16, 185, 129, 0.1); color: #047857; border: 1px solid rgba(16, 185, 129, 0.2); }
.platform-zoom { background: rgba(59, 130, 246, 0.1); color: #1D4ED8; border: 1px solid rgba(59, 130, 246, 0.2); }
.platform-teams { background: rgba(109, 74, 255, 0.1); color: var(--primary); border: 1px solid rgba(109, 74, 255, 0.2); }
.platform-youtube { background: rgba(239, 68, 68, 0.1); color: #B91C1C; border: 1px solid rgba(239, 68, 68, 0.2); }
.platform-other { background: rgba(107, 114, 128, 0.1); color: #374151; border: 1px solid rgba(107, 114, 128, 0.2); }

.action-menu-btn {
    padding: 0.35rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    background: var(--bg-surface);
    border: 1px solid var(--border-light);
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.2s;
}
.action-menu-btn:hover {
    background: rgba(109,74,255,0.08);
    color: var(--primary);
    border-color: var(--primary);
}

.modal-body {
    max-height: calc(85vh - 120px);
    overflow-y: auto;
}

.url-feedback-box {
    margin-top: 0.4rem;
    font-size: 0.75rem;
    border-radius: 6px;
    padding: 0.35rem 0.6rem;
    display: none;
}
.url-feedback-box.valid {
    background: #ECFDF5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}
.url-feedback-box.invalid {
    background: #FEF2F2;
    color: #991B1B;
    border: 1px solid #FECACA;
}

@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    .filter-search-wrap {
        grid-column: span 1;
    }
}
</style>

<!-- ═════════ OPERATIONAL METRICS CARDS ═════════ -->
<div class="stats-grid" id="statsGrid">
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #6D4AFF, #8B6BFF)">
        <div class="stat-icon">📡</div>
        <div class="stat-value" id="statTotal">—</div>
        <div class="stat-label">Total Live Classes</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #3B82F6, #60A5FA)">
        <div class="stat-icon">📅</div>
        <div class="stat-value" id="statScheduled">—</div>
        <div class="stat-label">Scheduled Sessions</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #10B981, #34D399)">
        <div class="stat-icon"><span class="live-pulse"></span>🎥</div>
        <div class="stat-value" id="statLive" style="color:#059669">—</div>
        <div class="stat-label">Currently Live Now</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #0ABCBC, #2DD4BF)">
        <div class="stat-icon">✅</div>
        <div class="stat-value" id="statCompleted">—</div>
        <div class="stat-label">Completed Classes</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #F59E0B, #FBBF24)">
        <div class="stat-icon">⏳</div>
        <div class="stat-value" id="statUpcoming">—</div>
        <div class="stat-label">Upcoming (Future)</div>
    </div>
    <div class="stat-card" style="--card-accent:linear-gradient(90deg, #EF4444, #F87171);cursor:pointer" onclick="filterByAttention()">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value" id="statAttention" style="color:#DC2626">—</div>
        <div class="stat-label">Requires Attention</div>
    </div>
</div>

<!-- ═════════ SEARCH & FILTER BAR ═════════ -->
<div class="filter-grid">
    <select class="form-control" id="f-class" onchange="onClassFilterChange()">
        <option value="">🏫 All Classes (1–10)</option>
        @foreach(range(1, 10) as $c)
        <option value="{{ $c }}">Class {{ $c }}</option>
        @endforeach
    </select>

    <select class="form-control" id="f-subject" onchange="fetchLiveClasses(1)">
        <option value="">📚 All Subjects</option>
    </select>

    <select class="form-control" id="f-teacher" onchange="fetchLiveClasses(1)">
        <option value="">👨‍🏫 All Teachers</option>
    </select>

    <select class="form-control" id="f-status" onchange="fetchLiveClasses(1)">
        <option value="">Status: All</option>
        <option value="scheduled">Scheduled</option>
        <option value="live">Live Now</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
        <option value="attention">⚠️ Needs Attention</option>
    </select>

    <select class="form-control" id="f-published" onchange="fetchLiveClasses(1)">
        <option value="">Visibility: All</option>
        <option value="1">Published Only</option>
        <option value="0">Draft Only</option>
    </select>

    <select class="form-control" id="f-platform" onchange="fetchLiveClasses(1)">
        <option value="">Platform: All</option>
        <option value="google_meet">Google Meet</option>
        <option value="zoom">Zoom</option>
        <option value="teams">MS Teams</option>
        <option value="youtube_live">YouTube Live</option>
        <option value="other">Other / Custom</option>
    </select>

    <div class="filter-search-wrap">
        <input type="text" class="form-control" id="f-search" placeholder="🔍 Search class title, teacher, meeting link…" onkeyup="debounce(function(){ fetchLiveClasses(1); }, 300)()">
    </div>

    <button class="btn btn-outline btn-sm" onclick="resetFilters()">Reset Filters</button>
</div>

<!-- ═════════ LIVE CLASSES DATA TABLE ═════════ -->
<div class="card">
    <div class="card-header">
        <div>
            <span class="card-title">Live Class Sessions</span>
            <span class="badge badge-purple" id="classesCountBadge">0</span>
        </div>
        <div style="display:flex;gap:0.5rem;align-items:center">
            <button class="btn btn-outline btn-sm" onclick="fetchLiveClasses(currentPage)" title="Refresh data">🔄 Refresh</button>
            <button class="btn btn-primary btn-sm" onclick="openCreateModal()">＋ Schedule Live Class</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Class & Subject</th>
                    <th>Live Session Title</th>
                    <th>Teacher / Instructor</th>
                    <th>Platform & Meeting Link</th>
                    <th>Scheduled Date & Time</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody id="liveClassesTableBody">
                <tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Loading live classes…</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination" id="livePagination" style="display:none">
        <span id="livePagInfo">Showing 0 of 0</span>
        <div class="pagination-btns" id="livePagBtns"></div>
    </div>
</div>

<!-- ═════════ MODAL: SCHEDULE LIVE CLASS ═════════ -->
<div class="modal-backdrop" id="scheduleModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="scheduleModalTitle">📡 Schedule New Live Class</span>
            <button class="modal-close" onclick="closeModal('scheduleModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="modal-lc-id">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Academic Class (1–10) *</label>
                    <select class="form-control" id="m-class" onchange="cascadeModalClass()">
                        <option value="">Select Class…</option>
                        @foreach(range(1, 10) as $c)
                        <option value="{{ $c }}">Class {{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject *</label>
                    <select class="form-control" id="m-subject" onchange="cascadeModalSubject()" disabled>
                        <option value="">Select Class first…</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Curriculum Chapter (Optional)</label>
                    <select class="form-control" id="m-chapter" disabled>
                        <option value="">Whole Subject / General</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned Teacher *</label>
                    <select class="form-control" id="m-teacher">
                        <option value="">Select teacher…</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Live Class Title *</label>
                <input type="text" class="form-control" id="m-title" placeholder="e.g. Class 10 Mathematics — Quadratic Equations Doubt Clearing">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Meeting Provider *</label>
                    <select class="form-control" id="m-platform" onchange="onPlatformChange()">
                        <option value="google_meet">Google Meet</option>
                        <option value="zoom">Zoom</option>
                        <option value="teams">Microsoft Teams</option>
                        <option value="youtube_live">YouTube Live</option>
                        <option value="other">Other / Custom</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes) *</label>
                    <input type="number" class="form-control" id="m-duration" value="60" min="5" max="480">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Meeting URL (HTTPS) *</label>
                <input type="url" class="form-control" id="m-url" placeholder="https://meet.google.com/abc-defg-hij" oninput="validateModalMeetingUrl()">
                <div id="m-url-feedback" class="url-feedback-box"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Scheduled Date & Start Time *</label>
                    <input type="datetime-local" class="form-control" id="m-scheduled">
                </div>
                <div class="form-group">
                    <label class="form-label">Timezone</label>
                    <input type="text" class="form-control" id="m-timezone" value="Asia/Kolkata">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description & Overview</label>
                <textarea class="form-control" id="m-desc" rows="2" placeholder="Topics covered, previous exam questions, formula derivations…"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Learning Objectives (What students will master)</label>
                <textarea class="form-control" id="m-objectives" rows="2" placeholder="e.g. 1. Solve equations by factorization. 2. Understand discriminant rules."></textarea>
            </div>

            <div class="form-row" style="margin-top:0.5rem">
                <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
                    <input type="checkbox" id="m-published" checked style="width:16px;height:16px;cursor:pointer">
                    <label for="m-published" style="cursor:pointer;font-size:0.85rem;font-weight:600">Publish immediately (visible to students)</label>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
                    <input type="checkbox" id="m-notify" checked style="width:16px;height:16px;cursor:pointer">
                    <label for="m-notify" style="cursor:pointer;font-size:0.85rem;font-weight:600">Send in-app notification to students</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('scheduleModal')">Cancel</button>
            <button class="btn btn-primary" id="btnSaveSchedule" onclick="saveLiveClass()">Schedule Class</button>
        </div>
    </div>
</div>

<!-- ═════════ MODAL: RESCHEDULE CLASS ═════════ -->
<div class="modal-backdrop" id="rescheduleModal">
    <div class="modal" style="max-width:460px">
        <div class="modal-header">
            <span class="modal-title">⏰ Reschedule Live Class</span>
            <button class="modal-close" onclick="closeModal('rescheduleModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="reschedule-id">
            <p style="font-size:0.85rem;margin-bottom:1rem;color:var(--text-secondary)">
                Rescheduling <strong id="reschedule-title">—</strong>
            </p>
            <div class="form-group">
                <label class="form-label">New Scheduled Date & Time *</label>
                <input type="datetime-local" class="form-control" id="reschedule-datetime">
            </div>
            <div class="form-group">
                <label class="form-label">Duration (minutes)</label>
                <input type="number" class="form-control" id="reschedule-duration" min="5" max="480">
            </div>
            <div class="form-group">
                <label class="form-label">Reason for Rescheduling (Optional)</label>
                <input type="text" class="form-control" id="reschedule-reason" placeholder="e.g. Teacher request, exam schedule conflict…">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
                <input type="checkbox" id="reschedule-notify" checked style="width:16px;height:16px;cursor:pointer">
                <label for="reschedule-notify" style="cursor:pointer;font-size:0.85rem;font-weight:600">Notify enrolled students</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('rescheduleModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitReschedule()">Confirm Reschedule</button>
        </div>
    </div>
</div>

<!-- ═════════ MODAL: CANCEL CLASS ═════════ -->
<div class="modal-backdrop" id="cancelModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <span class="modal-title" style="color:var(--danger)">🚫 Cancel Live Class</span>
            <button class="modal-close" onclick="closeModal('cancelModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="cancel-id">
            <p style="font-size:0.85rem;margin-bottom:1rem;color:var(--text-secondary)">
                Are you sure you want to cancel <strong id="cancel-title">—</strong>?
            </p>
            <div class="form-group">
                <label class="form-label">Cancellation Reason *</label>
                <textarea class="form-control" id="cancel-reason" rows="3" placeholder="Provide reason shown to students…"></textarea>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
                <input type="checkbox" id="cancel-notify" checked style="width:16px;height:16px;cursor:pointer">
                <label for="cancel-notify" style="cursor:pointer;font-size:0.85rem;font-weight:600">Notify enrolled students</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('cancelModal')">Keep Class</button>
            <button class="btn btn-danger" onclick="submitCancel()">Cancel Class</button>
        </div>
    </div>
</div>

<!-- ═════════ MODAL: MARK COMPLETED & RECORDING ═════════ -->
<div class="modal-backdrop" id="completeModal">
    <div class="modal" style="max-width:460px">
        <div class="modal-header">
            <span class="modal-title">✅ Mark Class Completed</span>
            <button class="modal-close" onclick="closeModal('completeModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="complete-id">
            <p style="font-size:0.85rem;margin-bottom:1rem;color:var(--text-secondary)">
                Mark session <strong id="complete-title">—</strong> as completed.
            </p>
            <div class="form-group">
                <label class="form-label">Optional Recording URL (YouTube / Video)</label>
                <input type="url" class="form-control" id="complete-recording-url" placeholder="https://www.youtube.com/watch?v=…">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.35rem">
                    Students will be able to watch this recording in their study portal after the class has ended.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('completeModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitComplete()">Mark Completed</button>
        </div>
    </div>
</div>

<!-- ═════════ MODAL: STUDENT PREVIEW / DETAILS ═════════ -->
<div class="modal-backdrop" id="previewModal">
    <div class="modal" style="max-width:540px">
        <div class="modal-header">
            <span class="modal-title">👁️ Student Portal Preview</span>
            <button class="modal-close" onclick="closeModal('previewModal')">×</button>
        </div>
        <div class="modal-body" id="previewModalBody">
            <!-- Dynamically populated -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal('previewModal')">Close Preview</button>
        </div>
    </div>
</div>

<!-- ═════════ MODAL: MEETING LINK VALIDATOR & TESTER ═════════ -->
<div class="modal-backdrop" id="testLinkModal">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <span class="modal-title">🔗 Meeting Link Inspection</span>
            <button class="modal-close" onclick="closeModal('testLinkModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="test-id">
            <div class="form-group">
                <label class="form-label">Meeting Provider</label>
                <input type="text" class="form-control" id="test-platform" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Current Meeting URL</label>
                <input type="url" class="form-control" id="test-url" oninput="inspectTestUrl()">
                <div id="test-url-feedback" class="url-feedback-box" style="display:block"></div>
            </div>
            <div style="margin-top:1rem;display:flex;gap:0.75rem">
                <a href="#" id="test-launch-btn" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                    🚀 Test Open in Browser ↗
                </a>
                <button class="btn btn-primary btn-sm" onclick="saveUpdatedMeetingLink()">
                    💾 Save Updated Link
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('testLinkModal')">Close</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════════════════
// STATE & CONFIGURATION
// ══════════════════════════════════════════════════════════════════════════
const API_TOKEN = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
const API_BASE  = document.querySelector('meta[name="api-base"]')?.getAttribute('content') || '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

let currentPage = 1;
let totalPages = 1;
let currentEditingId = null;

let allTeachers = [];
let classSubjectMappings = [];

// API Fetch Helper
async function apiCall(endpoint, method = 'GET', body = null) {
    const headers = {
        'Accept': 'application/json',
        'Authorization': `Bearer ${API_TOKEN}`,
        'X-CSRF-TOKEN': CSRF_TOKEN,
    };
    if (body && !(body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const options = {
        method,
        headers,
    };
    if (body) {
        options.body = (body instanceof FormData) ? body : JSON.stringify(body);
    }

    try {
        const res = await fetch(`${API_BASE}${endpoint}`, options);
        const json = await res.json();
        if (!res.ok) {
            throw new Error(json.message || `Request failed with status ${res.status}`);
        }
        return json;
    } catch (err) {
        console.error('API Error:', err);
        throw err;
    }
}

// ══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ══════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([
        loadDashboardStats(),
        loadTeachers(),
        loadClassSubjectMappings(),
    ]);
    await fetchLiveClasses(1);
});

// Load real-time operational dashboard stats
async function loadDashboardStats() {
    try {
        const res = await apiCall('/admin/live-classes/dashboard-stats');
        const data = res.data;
        document.getElementById('statTotal').textContent     = data.total_classes ?? 0;
        document.getElementById('statScheduled').textContent = data.scheduled_classes ?? 0;
        document.getElementById('statLive').textContent      = data.live_classes ?? 0;
        document.getElementById('statCompleted').textContent = data.completed_classes ?? 0;
        document.getElementById('statUpcoming').textContent  = data.upcoming_classes ?? 0;
        document.getElementById('statAttention').textContent = data.requiring_attention ?? 0;
    } catch (err) {
        console.warn('Could not load dashboard stats:', err);
    }
}

// Load Teachers list for dropdowns
async function loadTeachers() {
    try {
        const res = await apiCall('/admin/teachers');
        allTeachers = res.data?.data || res.data || [];
        populateTeacherSelects();
    } catch (err) {
        console.warn('Could not load teachers:', err);
    }
}

function populateTeacherSelects() {
    const fTeacher = document.getElementById('f-teacher');
    const mTeacher = document.getElementById('m-teacher');

    fTeacher.innerHTML = '<option value="">👨‍🏫 All Teachers</option>';
    mTeacher.innerHTML = '<option value="">Select teacher…</option>';

    allTeachers.forEach(t => {
        const opt1 = document.createElement('option');
        opt1.value = t.id;
        opt1.textContent = t.name;
        fTeacher.appendChild(opt1);

        const opt2 = document.createElement('option');
        opt2.value = t.id;
        opt2.textContent = `${t.name} (${t.email || ''})`;
        mTeacher.appendChild(opt2);
    });
}

// Load all class-subject mappings
async function loadClassSubjectMappings() {
    try {
        const res = await apiCall('/admin/class-subjects/all');
        classSubjectMappings = res.data || [];
    } catch (err) {
        console.warn('Could not load class-subject mappings:', err);
    }
}

// ══════════════════════════════════════════════════════════════════════════
// FETCH LIVE CLASSES LISTING
// ══════════════════════════════════════════════════════════════════════════
async function fetchLiveClasses(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('liveClassesTableBody');
    tbody.innerHTML = '<tr class="loading-row"><td colspan="8"><div class="spinner-sm"></div> Fetching live classes…</td></tr>';

    const params = new URLSearchParams();
    params.set('page', page);
    params.set('per_page', 15);

    const fClass     = document.getElementById('f-class').value;
    const fSubject   = document.getElementById('f-subject').value;
    const fTeacher   = document.getElementById('f-teacher').value;
    const fStatus    = document.getElementById('f-status').value;
    const fPublished = document.getElementById('f-published').value;
    const fPlatform  = document.getElementById('f-platform').value;
    const fSearch    = document.getElementById('f-search').value.trim();

    if (fClass)     params.set('class_level', fClass);
    if (fSubject)   params.set('subject_id', fSubject);
    if (fTeacher)   params.set('teacher_id', fTeacher);
    if (fStatus)    params.set('status', fStatus);
    if (fPublished) params.set('is_published', fPublished);
    if (fPlatform)  params.set('platform', fPlatform);
    if (fSearch)    params.set('search', fSearch);

    try {
        const res = await apiCall(`/admin/live-classes?${params.toString()}`);
        renderTable(res.data, res.meta);
        loadDashboardStats(); // keep operational metrics refreshed
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--danger);padding:2rem">Failed to load classes: ${escapeHtml(err.message)}</td></tr>`;
    }
}

function renderTable(classes, meta) {
    const tbody = document.getElementById('liveClassesTableBody');
    document.getElementById('classesCountBadge').textContent = meta?.total ?? (classes?.length ?? 0);

    if (!classes || classes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <div class="empty-icon">📡</div>
                        <p style="font-weight:600;margin-bottom:0.25rem">No live classes found</p>
                        <p style="font-size:0.8rem;color:var(--text-muted)">Try adjusting your search criteria or schedule a new live class session.</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('livePagination').style.display = 'none';
        return;
    }

    let rowsHtml = '';
    classes.forEach(c => {
        const classLevel = c.class_level || c.chapter?.class_level || '—';
        const subjectName = c.subject?.name || c.chapter?.subject?.name || 'Subject';
        const subjectColor = c.subject?.color || '#6D4AFF';
        const teacherName = c.teacher?.name || 'Unassigned';
        const teacherEmail = c.teacher?.email || '';

        // Platform badge
        let platformClass = 'platform-other';
        let platformLabel = 'Custom';
        if (c.platform === 'google_meet') { platformClass = 'platform-meet'; platformLabel = 'Google Meet'; }
        else if (c.platform === 'zoom') { platformClass = 'platform-zoom'; platformLabel = 'Zoom'; }
        else if (c.platform === 'teams') { platformClass = 'platform-teams'; platformLabel = 'MS Teams'; }
        else if (c.platform === 'youtube_live') { platformClass = 'platform-youtube'; platformLabel = 'YouTube Live'; }

        // Scheduled Date & Time
        const schedDate = new Date(c.scheduled_at);
        const dateStr = schedDate.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
        const timeStr = schedDate.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit', hour12:true });
        const durationStr = `${c.duration_minutes ?? 60} mins`;

        // Status Badge
        let statusBadge = '<span class="badge badge-neutral">Scheduled</span>';
        if (c.status === 'live') {
            statusBadge = '<span class="badge badge-success"><span class="live-pulse"></span>LIVE NOW</span>';
        } else if (c.status === 'completed') {
            statusBadge = '<span class="badge badge-info">Completed</span>';
        } else if (c.status === 'cancelled') {
            statusBadge = '<span class="badge badge-danger">Cancelled</span>';
        } else if (c.status === 'scheduled') {
            // Check if past scheduled time
            if (new Date() > new Date(schedDate.getTime() + 15 * 60000)) {
                statusBadge = '<span class="badge attention-badge" title="Past scheduled start time">⚠️ Past Due</span>';
            } else {
                statusBadge = '<span class="badge badge-purple">Scheduled</span>';
            }
        }

        // Visibility
        const pubBadge = c.is_published 
            ? '<span class="badge badge-success" style="font-size:0.65rem">Published</span>'
            : '<span class="badge badge-warning" style="font-size:0.65rem">Draft</span>';

        // Meeting URL preview
        const meetingUrl = c.meeting_url || '';
        const urlDisplay = meetingUrl.length > 28 ? meetingUrl.substring(0, 26) + '…' : meetingUrl;

        rowsHtml += `
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:0.45rem">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:${subjectColor}"></span>
                        <div>
                            <strong style="font-size:0.85rem">Class ${classLevel}</strong>
                            <div style="font-size:0.75rem;color:var(--text-muted)">${escapeHtml(subjectName)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight:600;color:var(--text)">${escapeHtml(c.title)}</div>
                    ${c.chapter ? `<div style="font-size:0.75rem;color:var(--text-muted)">📖 ${escapeHtml(c.chapter.title)}</div>` : ''}
                </td>
                <td>
                    <div style="font-size:0.82rem;font-weight:500">${escapeHtml(teacherName)}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">${escapeHtml(teacherEmail)}</div>
                </td>
                <td>
                    <div style="display:flex;flex-direction:column;gap:0.25rem">
                        <span class="platform-pill ${platformClass}">${platformLabel}</span>
                        ${meetingUrl ? `
                            <a href="javascript:void(0)" onclick="openTestLinkModal(${c.id}, '${escapeHtml(c.platform)}', '${escapeHtml(meetingUrl)}')" style="font-size:0.75rem;color:var(--primary);text-decoration:none" title="Test / Inspect Link">
                                🔗 ${escapeHtml(urlDisplay)}
                            </a>
                        ` : `<span style="font-size:0.75rem;color:var(--danger)">⚠️ No meeting link</span>`}
                    </div>
                </td>
                <td>
                    <div style="font-weight:600;font-size:0.82rem">${dateStr}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${timeStr} (${durationStr})</div>
                </td>
                <td>${statusBadge}</td>
                <td>${pubBadge}</td>
                <td style="text-align:right">
                    <div style="display:flex;gap:0.3rem;justify-content:flex-end;flex-wrap:wrap">
                        <button class="action-menu-btn" onclick="openPreviewModal(${c.id})" title="Preview as student">👁️</button>
                        <button class="action-menu-btn" onclick="openEditModal(${c.id})" title="Edit class details">✏️</button>
                        
                        ${c.status === 'scheduled' ? `
                            <button class="action-menu-btn" style="color:#059669" onclick="quickGoLive(${c.id})" title="Start Session (Go Live)">▶ Live</button>
                            <button class="action-menu-btn" onclick="openRescheduleModal(${c.id}, '${escapeHtml(c.title)}', '${c.scheduled_at}', ${c.duration_minutes ?? 60})" title="Reschedule">⏰</button>
                            <button class="action-menu-btn" style="color:var(--danger)" onclick="openCancelModal(${c.id}, '${escapeHtml(c.title)}')" title="Cancel">🚫</button>
                        ` : ''}

                        ${c.status === 'live' ? `
                            <button class="action-menu-btn" style="color:var(--primary)" onclick="openCompleteModal(${c.id}, '${escapeHtml(c.title)}')" title="Finish & Add Recording">🏁 Done</button>
                        ` : ''}

                        <button class="action-menu-btn" style="color:var(--danger)" onclick="deleteLiveClass(${c.id})" title="Delete">🗑️</button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = rowsHtml;
    renderPagination(meta);
}

function renderPagination(meta) {
    const pagWrap = document.getElementById('livePagination');
    if (!meta || meta.total <= meta.per_page) {
        pagWrap.style.display = 'none';
        return;
    }

    pagWrap.style.display = 'flex';
    document.getElementById('livePagInfo').textContent = `Showing ${meta.from ?? 0}–${meta.to ?? 0} of ${meta.total} live classes`;

    totalPages = meta.last_page;
    const btnsWrap = document.getElementById('livePagBtns');
    btnsWrap.innerHTML = '';

    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn';
    prevBtn.textContent = '« Prev';
    prevBtn.disabled = meta.current_page <= 1;
    prevBtn.onclick = () => fetchLiveClasses(meta.current_page - 1);
    btnsWrap.appendChild(prevBtn);

    for (let p = Math.max(1, meta.current_page - 2); p <= Math.min(meta.last_page, meta.current_page + 2); p++) {
        const btn = document.createElement('button');
        btn.className = `page-btn ${p === meta.current_page ? 'active' : ''}`;
        btn.textContent = p;
        btn.onclick = () => fetchLiveClasses(p);
        btnsWrap.appendChild(btn);
    }

    const nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn';
    nextBtn.textContent = 'Next »';
    nextBtn.disabled = meta.current_page >= meta.last_page;
    nextBtn.onclick = () => fetchLiveClasses(meta.current_page + 1);
    btnsWrap.appendChild(nextBtn);
}

// ══════════════════════════════════════════════════════════════════════════
// FILTER ACTIONS
// ══════════════════════════════════════════════════════════════════════════
function onClassFilterChange() {
    const classVal = document.getElementById('f-class').value;
    const fSubject = document.getElementById('f-subject');

    fSubject.innerHTML = '<option value="">📚 All Subjects</option>';
    if (classVal) {
        const filteredMappings = classSubjectMappings.filter(m => m.class_level == classVal);
        filteredMappings.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.subject_id;
            opt.textContent = m.subject_name;
            fSubject.appendChild(opt);
        });
    }
    fetchLiveClasses(1);
}

function filterByAttention() {
    document.getElementById('f-status').value = 'attention';
    fetchLiveClasses(1);
}

function resetFilters() {
    document.getElementById('f-class').value = '';
    document.getElementById('f-subject').value = '';
    document.getElementById('f-teacher').value = '';
    document.getElementById('f-status').value = '';
    document.getElementById('f-published').value = '';
    document.getElementById('f-platform').value = '';
    document.getElementById('f-search').value = '';
    fetchLiveClasses(1);
}

// ══════════════════════════════════════════════════════════════════════════
// MODAL CASCADING & CREATION / EDITING
// ══════════════════════════════════════════════════════════════════════════
function openCreateModal() {
    currentEditingId = null;
    document.getElementById('scheduleModalTitle').textContent = '📡 Schedule New Live Class';
    document.getElementById('modal-lc-id').value = '';
    document.getElementById('btnSaveSchedule').textContent = 'Schedule Class';

    document.getElementById('m-class').value = '';
    document.getElementById('m-subject').innerHTML = '<option value="">Select Class first…</option>';
    document.getElementById('m-subject').disabled = true;
    document.getElementById('m-chapter').innerHTML = '<option value="">Whole Subject / General</option>';
    document.getElementById('m-chapter').disabled = true;

    document.getElementById('m-teacher').value = '';
    document.getElementById('m-title').value = '';
    document.getElementById('m-platform').value = 'google_meet';
    document.getElementById('m-duration').value = '60';
    document.getElementById('m-url').value = '';
    document.getElementById('m-desc').value = '';
    document.getElementById('m-objectives').value = '';
    document.getElementById('m-published').checked = true;
    document.getElementById('m-notify').checked = true;

    // Set default scheduled datetime to tomorrow at 10:00 AM
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(10, 0, 0, 0);
    document.getElementById('m-scheduled').value = toLocalISOString(tomorrow);

    validateModalMeetingUrl();
    openModal('scheduleModal');
}

async function openEditModal(id) {
    currentEditingId = id;
    document.getElementById('scheduleModalTitle').textContent = '✏️ Edit Live Class Session';
    document.getElementById('btnSaveSchedule').textContent = 'Save Changes';
    document.getElementById('modal-lc-id').value = id;

    try {
        const res = await apiCall(`/admin/live-classes/${id}`);
        const c = res.data;

        document.getElementById('m-class').value = c.class_level || c.chapter?.class_level || '';
        await cascadeModalClass();

        document.getElementById('m-subject').value = c.subject_id || c.chapter?.subject_id || '';
        await cascadeModalSubject();

        if (c.chapter_id) {
            document.getElementById('m-chapter').value = c.chapter_id;
        }

        document.getElementById('m-teacher').value = c.teacher_id;
        document.getElementById('m-title').value = c.title;
        document.getElementById('m-platform').value = c.platform || 'other';
        document.getElementById('m-duration').value = c.duration_minutes ?? 60;
        document.getElementById('m-url').value = c.meeting_url || '';
        document.getElementById('m-timezone').value = c.timezone || 'Asia/Kolkata';
        document.getElementById('m-desc').value = c.description || '';
        document.getElementById('m-objectives').value = c.learning_objectives || '';
        document.getElementById('m-published').checked = !!c.is_published;
        document.getElementById('m-notify').checked = false; // default false on edit

        if (c.scheduled_at) {
            document.getElementById('m-scheduled').value = toLocalISOString(new Date(c.scheduled_at));
        }

        validateModalMeetingUrl();
        openModal('scheduleModal');
    } catch (err) {
        showToast(`Could not load class details: ${err.message}`, 'error');
    }
}

async function cascadeModalClass() {
    const classVal = document.getElementById('m-class').value;
    const mSub = document.getElementById('m-subject');
    const mChap = document.getElementById('m-chapter');

    mSub.innerHTML = '<option value="">Select subject…</option>';
    mChap.innerHTML = '<option value="">Whole Subject / General</option>';
    mChap.disabled = true;

    if (!classVal) {
        mSub.disabled = true;
        return;
    }

    const filtered = classSubjectMappings.filter(m => m.class_level == classVal);
    filtered.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.subject_id;
        opt.textContent = m.subject_name;
        mSub.appendChild(opt);
    });
    mSub.disabled = false;
}

async function cascadeModalSubject() {
    const classVal = document.getElementById('m-class').value;
    const subVal = document.getElementById('m-subject').value;
    const mChap = document.getElementById('m-chapter');

    mChap.innerHTML = '<option value="">Whole Subject / General (No chapter)</option>';
    if (!classVal || !subVal) {
        mChap.disabled = true;
        return;
    }

    try {
        const res = await apiCall(`/admin/class-subjects/${classVal}/chapters?subject_id=${subVal}`);
        const chapters = res.data || [];
        chapters.forEach(ch => {
            const opt = document.createElement('option');
            opt.value = ch.id;
            opt.textContent = `Ch ${ch.sort_order ?? ''}: ${ch.title}`;
            mChap.appendChild(opt);
        });
        mChap.disabled = false;
    } catch (err) {
        console.warn('Could not load chapters:', err);
    }
}

function onPlatformChange() {
    validateModalMeetingUrl();
}

function validateModalMeetingUrl() {
    const url = document.getElementById('m-url').value.trim();
    const platform = document.getElementById('m-platform').value;
    const feedback = document.getElementById('m-url-feedback');

    if (!url) {
        feedback.style.display = 'none';
        return;
    }

    let isValid = true;
    let msg = 'Valid meeting URL format.';

    if (!url.startsWith('https://')) {
        isValid = false;
        msg = '⚠️ URL must use secure HTTPS protocol.';
    } else if (platform === 'google_meet' && !url.includes('meet.google.com')) {
        isValid = false;
        msg = '⚠️ Google Meet link should contain meet.google.com (e.g. https://meet.google.com/abc-defg-hij)';
    } else if (platform === 'zoom' && !url.includes('zoom.us')) {
        isValid = false;
        msg = '⚠️ Zoom link should contain zoom.us (e.g. https://zoom.us/j/1234567890)';
    } else if (platform === 'teams' && !url.includes('teams.microsoft.com') && !url.includes('teams.live.com')) {
        isValid = false;
        msg = '⚠️ MS Teams link should be on teams.microsoft.com';
    } else if (platform === 'youtube_live' && !url.includes('youtube.com') && !url.includes('youtu.be')) {
        isValid = false;
        msg = '⚠️ YouTube Live link should be a valid YouTube URL';
    }

    feedback.style.display = 'block';
    feedback.className = `url-feedback-box ${isValid ? 'valid' : 'invalid'}`;
    feedback.textContent = msg;
}

async function saveLiveClass() {
    const classLevel = document.getElementById('m-class').value;
    const subjectId  = document.getElementById('m-subject').value;
    const chapterId  = document.getElementById('m-chapter').value;
    const teacherId  = document.getElementById('m-teacher').value;
    const title      = document.getElementById('m-title').value.trim();
    const platform   = document.getElementById('m-platform').value;
    const duration   = document.getElementById('m-duration').value;
    const url        = document.getElementById('m-url').value.trim();
    const scheduled  = document.getElementById('m-scheduled').value;
    const timezone   = document.getElementById('m-timezone').value.trim();
    const desc       = document.getElementById('m-desc').value.trim();
    const objectives = document.getElementById('m-objectives').value.trim();
    const published  = document.getElementById('m-published').checked;
    const notify     = document.getElementById('m-notify').checked;

    if (!classLevel || !subjectId) {
        showToast('Please select academic Class and Subject.', 'error');
        return;
    }
    if (!teacherId) {
        showToast('Please assign a Teacher.', 'error');
        return;
    }
    if (!title) {
        showToast('Please enter a Live Class Title.', 'error');
        return;
    }
    if (!url || !url.startsWith('https://')) {
        showToast('Please provide a valid HTTPS Meeting URL.', 'error');
        return;
    }
    if (!scheduled) {
        showToast('Please choose scheduled date and time.', 'error');
        return;
    }

    const payload = {
        class_level: parseInt(classLevel),
        subject_id: parseInt(subjectId),
        chapter_id: chapterId ? parseInt(chapterId) : null,
        teacher_id: parseInt(teacherId),
        title,
        platform,
        duration_minutes: parseInt(duration),
        meeting_url: url,
        scheduled_at: scheduled,
        timezone: timezone || 'Asia/Kolkata',
        description: desc,
        learning_objectives: objectives,
        is_published: published,
        notify_students: notify,
    };

    const btn = document.getElementById('btnSaveSchedule');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    try {
        if (currentEditingId) {
            await apiCall(`/admin/live-classes/${currentEditingId}`, 'PUT', payload);
            showToast('Live class updated successfully!', 'success');
        } else {
            await apiCall('/admin/live-classes', 'POST', payload);
            showToast('Live class scheduled successfully!', 'success');
        }
        closeModal('scheduleModal');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Save failed: ${err.message}`, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = currentEditingId ? 'Save Changes' : 'Schedule Class';
    }
}

// ══════════════════════════════════════════════════════════════════════════
// QUICK ACTIONS: GO LIVE, RESCHEDULE, COMPLETE, CANCEL, DELETE
// ══════════════════════════════════════════════════════════════════════════
async function quickGoLive(id) {
    if (!confirm('Start this session now and mark status as LIVE? Enrolled students will be alerted.')) return;
    try {
        await apiCall(`/admin/live-classes/${id}/status`, 'PATCH', { status: 'live' });
        showToast('Live class is now LIVE!', 'success');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Failed: ${err.message}`, 'error');
    }
}

function openRescheduleModal(id, title, scheduledAt, duration) {
    document.getElementById('reschedule-id').value = id;
    document.getElementById('reschedule-title').textContent = title;
    document.getElementById('reschedule-duration').value = duration || 60;
    document.getElementById('reschedule-reason').value = '';
    if (scheduledAt) {
        document.getElementById('reschedule-datetime').value = toLocalISOString(new Date(scheduledAt));
    }
    openModal('rescheduleModal');
}

async function submitReschedule() {
    const id = document.getElementById('reschedule-id').value;
    const dt = document.getElementById('reschedule-datetime').value;
    const dur = document.getElementById('reschedule-duration').value;
    const reason = document.getElementById('reschedule-reason').value;
    const notify = document.getElementById('reschedule-notify').checked;

    if (!dt) {
        showToast('Please pick the new date and time.', 'error');
        return;
    }

    try {
        await apiCall(`/admin/live-classes/${id}/reschedule`, 'PATCH', {
            scheduled_at: dt,
            duration_minutes: dur ? parseInt(dur) : undefined,
            reschedule_reason: reason,
            notify_students: notify,
        });
        showToast('Live class rescheduled successfully.', 'success');
        closeModal('rescheduleModal');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Reschedule failed: ${err.message}`, 'error');
    }
}

function openCancelModal(id, title) {
    document.getElementById('cancel-id').value = id;
    document.getElementById('cancel-title').textContent = title;
    document.getElementById('cancel-reason').value = 'Administrative rescheduling.';
    openModal('cancelModal');
}

async function submitCancel() {
    const id = document.getElementById('cancel-id').value;
    const reason = document.getElementById('cancel-reason').value.trim();
    const notify = document.getElementById('cancel-notify').checked;

    if (!reason) {
        showToast('Please enter a cancellation reason.', 'error');
        return;
    }

    try {
        await apiCall(`/admin/live-classes/${id}/cancel`, 'PATCH', {
            reason,
            notify_students: notify,
        });
        showToast('Class marked as cancelled.', 'success');
        closeModal('cancelModal');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Cancellation failed: ${err.message}`, 'error');
    }
}

function openCompleteModal(id, title) {
    document.getElementById('complete-id').value = id;
    document.getElementById('complete-title').textContent = title;
    document.getElementById('complete-recording-url').value = '';
    openModal('completeModal');
}

async function submitComplete() {
    const id = document.getElementById('complete-id').value;
    const recUrl = document.getElementById('complete-recording-url').value.trim();

    try {
        await apiCall(`/admin/live-classes/${id}/status`, 'PATCH', {
            status: 'completed',
            recording_url: recUrl || null,
        });
        showToast('Class marked as completed.', 'success');
        closeModal('completeModal');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Action failed: ${err.message}`, 'error');
    }
}

async function deleteLiveClass(id) {
    if (!confirm('Are you sure you want to delete this live class permanently? This cannot be undone.')) return;
    try {
        await apiCall(`/admin/live-classes/${id}`, 'DELETE');
        showToast('Live class deleted.', 'success');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Delete failed: ${err.message}`, 'error');
    }
}

// ══════════════════════════════════════════════════════════════════════════
// STUDENT PORTAL PREVIEW
// ══════════════════════════════════════════════════════════════════════════
async function openPreviewModal(id) {
    const container = document.getElementById('previewModalBody');
    container.innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div> Loading preview…</div>';
    openModal('previewModal');

    try {
        const res = await apiCall(`/admin/live-classes/${id}`);
        const c = res.data;

        const schedDate = new Date(c.scheduled_at);
        const formattedDate = schedDate.toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'long', year:'numeric' });
        const formattedTime = schedDate.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit', hour12:true });

        container.innerHTML = `
            <div style="background:var(--bg-surface);border-radius:12px;padding:1.25rem;border:1px solid var(--border-light)">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem">
                    <span class="badge badge-purple">Class ${c.class_level || c.chapter?.class_level} • ${escapeHtml(c.subject?.name || 'Subject')}</span>
                    <span class="badge ${c.status === 'live' ? 'badge-success' : 'badge-neutral'}">${c.status.toUpperCase()}</span>
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem">${escapeHtml(c.title)}</h3>
                ${c.chapter ? `<p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.75rem">Chapter: ${escapeHtml(c.chapter.title)}</p>` : ''}

                <div style="background:#fff;border-radius:8px;padding:0.85rem;margin:0.75rem 0;border:1px solid rgba(0,0,0,0.06);font-size:0.83rem">
                    <div style="margin-bottom:0.35rem">📅 <strong>Scheduled:</strong> ${formattedDate} at ${formattedTime}</div>
                    <div style="margin-bottom:0.35rem">⏱ <strong>Duration:</strong> ${c.duration_minutes ?? 60} Minutes</div>
                    <div style="margin-bottom:0.35rem">👨‍🏫 <strong>Instructor:</strong> ${escapeHtml(c.teacher?.name || 'Faculty')}</div>
                    <div>📡 <strong>Platform:</strong> ${escapeHtml(c.platform)}</div>
                </div>

                ${c.description ? `
                    <div style="margin-top:0.75rem">
                        <strong style="font-size:0.8rem;text-transform:uppercase;color:var(--text-muted)">About This Class:</strong>
                        <p style="font-size:0.85rem;margin-top:0.25rem">${escapeHtml(c.description)}</p>
                    </div>
                ` : ''}

                ${c.learning_objectives ? `
                    <div style="margin-top:0.75rem">
                        <strong style="font-size:0.8rem;text-transform:uppercase;color:var(--text-muted)">Learning Goals:</strong>
                        <p style="font-size:0.85rem;margin-top:0.25rem;white-space:pre-line">${escapeHtml(c.learning_objectives)}</p>
                    </div>
                ` : ''}

                <div style="margin-top:1.25rem;text-align:center">
                    ${c.meeting_url ? `
                        <a href="${escapeHtml(c.meeting_url)}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width:100%;justify-content:center">
                            🚀 Join Live Class (${escapeHtml(c.platform)})
                        </a>
                    ` : `
                        <button class="btn btn-outline" disabled style="width:100%">Meeting link not yet assigned</button>
                    `}
                </div>
            </div>
        `;
    } catch (err) {
        container.innerHTML = `<div style="color:var(--danger);padding:1rem">Failed to load preview: ${err.message}</div>`;
    }
}

// ══════════════════════════════════════════════════════════════════════════
// MEETING LINK TESTER & QUICK REPLACE
// ══════════════════════════════════════════════════════════════════════════
function openTestLinkModal(id, platform, url) {
    document.getElementById('test-id').value = id;
    document.getElementById('test-platform').value = platform;
    document.getElementById('test-url').value = url;
    inspectTestUrl();
    openModal('testLinkModal');
}

function inspectTestUrl() {
    const url = document.getElementById('test-url').value.trim();
    const platform = document.getElementById('test-platform').value;
    const feedback = document.getElementById('test-url-feedback');
    const launchBtn = document.getElementById('test-launch-btn');

    let isValid = true;
    let msg = 'Valid HTTPS URL.';

    if (!url || !url.startsWith('https://')) {
        isValid = false;
        msg = '⚠️ Must be a valid HTTPS URL.';
    }

    feedback.className = `url-feedback-box ${isValid ? 'valid' : 'invalid'}`;
    feedback.textContent = msg;

    launchBtn.href = isValid ? url : '#';
    launchBtn.style.pointerEvents = isValid ? 'auto' : 'none';
    launchBtn.style.opacity = isValid ? '1' : '0.5';
}

async function saveUpdatedMeetingLink() {
    const id = document.getElementById('test-id').value;
    const url = document.getElementById('test-url').value.trim();

    if (!url || !url.startsWith('https://')) {
        showToast('Please provide a valid HTTPS link.', 'error');
        return;
    }

    try {
        await apiCall(`/admin/live-classes/${id}`, 'PUT', { meeting_url: url });
        showToast('Meeting URL updated successfully!', 'success');
        closeModal('testLinkModal');
        await fetchLiveClasses(currentPage);
    } catch (err) {
        showToast(`Update failed: ${err.message}`, 'error');
    }
}

// ══════════════════════════════════════════════════════════════════════════
// UTILITIES
// ══════════════════════════════════════════════════════════════════════════
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function toLocalISOString(date) {
    const pad = n => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}
</script>
@endpush
