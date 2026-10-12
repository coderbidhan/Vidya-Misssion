@extends('layouts.admin')
@section('title','Content Management')
@section('page-title','Content Management')
@section('page-subtitle','Manage Class 1–10 Recorded Classes, Live Sessions & Study Notes')

@section('content')
<style>
.filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.85rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 12px;
}
.filter-bar .form-control {
    flex: 1;
    min-width: 140px;
}
.filter-bar .search-input {
    min-width: 200px;
    flex: 2;
}
.action-btn-group {
    display: flex;
    gap: 0.35rem;
    align-items: center;
}
.modal-body {
    max-height: calc(85vh - 120px);
    overflow-y: auto;
}
@media (max-width: 768px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-bar .form-control, .filter-bar .search-input {
        min-width: 100%;
    }
    .tabs {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
}
</style>

<div class="tabs tab-group-content">
    <button class="tab-btn active" data-tab="tab-recorded" onclick="switchTab('tab-recorded','tab-group-content')">🎬 Recorded Classes</button>
    <button class="tab-btn" data-tab="tab-live" onclick="switchTab('tab-live','tab-group-content')">📡 Live Classes</button>
    <button class="tab-btn" data-tab="tab-notes" onclick="switchTab('tab-notes','tab-group-content')">📋 Study Notes</button>
</div>

<!-- ═══ RECORDED CLASSES TAB ═══ -->
<div class="tab-panel active" id="tab-recorded">
    <div class="filter-bar">
        <select class="form-control" id="rec-filter-class" onchange="filterRecorded()">
            <option value="">🏫 All Classes (1–10)</option>
            @foreach(range(1, 10) as $c)
            <option value="{{ $c }}">Class {{ $c }}</option>
            @endforeach
        </select>
        <select class="form-control" id="rec-filter-subject" onchange="filterRecorded()">
            <option value="">📚 All Subjects</option>
        </select>
        <select class="form-control" id="rec-filter-status" onchange="filterRecorded()">
            <option value="">Status: All</option>
            <option value="1">Active (Published)</option>
            <option value="0">Hidden (Draft)</option>
        </select>
        <input type="text" class="form-control search-input" id="rec-filter-search" placeholder="🔍 Search recorded classes…" onkeyup="debounce(filterRecorded, 300)()">
        <button class="btn btn-outline btn-sm" onclick="resetRecordedFilters()">Reset</button>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <span class="card-title">Recorded Video Classes</span>
                <span class="badge badge-purple" id="recCountBadge">0</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('addRecordedModal')">＋ Add Class</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title & Description</th>
                        <th>Class & Chapter</th>
                        <th>Teacher</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recordedBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination" id="recPagination" style="display:none">
            <span id="recPagInfo">Showing 0 of 0</span>
            <div class="pagination-btns" id="recPagBtns"></div>
        </div>
    </div>
</div>

<!-- ═══ LIVE CLASSES TAB ═══ -->
<div class="tab-panel" id="tab-live">
    <div class="filter-bar">
        <select class="form-control" id="live-filter-class" onchange="filterLive()">
            <option value="">🏫 All Classes (1–10)</option>
            @foreach(range(1, 10) as $c)
            <option value="{{ $c }}">Class {{ $c }}</option>
            @endforeach
        </select>
        <select class="form-control" id="live-filter-status" onchange="filterLive()">
            <option value="">Status: All</option>
            <option value="scheduled">Scheduled</option>
            <option value="live">Live</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <input type="text" class="form-control search-input" id="live-filter-search" placeholder="🔍 Search live classes…" onkeyup="debounce(filterLive, 300)()">
        <button class="btn btn-outline btn-sm" onclick="resetLiveFilters()">Reset</button>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <span class="card-title">Scheduled Live Classes</span>
                <span class="badge badge-purple" id="liveCountBadge">0</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('addLiveModal')">＋ Schedule Class</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Class & Chapter</th>
                        <th>Teacher</th>
                        <th>Platform</th>
                        <th>Scheduled Date & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="liveBody"><tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination" id="livePagination" style="display:none">
            <span id="livePagInfo">Showing 0 of 0</span>
            <div class="pagination-btns" id="livePagBtns"></div>
        </div>
    </div>
</div>

<!-- ═══ STUDY NOTES TAB ═══ -->
<div class="tab-panel" id="tab-notes">
    <div class="filter-bar">
        <select class="form-control" id="notes-filter-class" onchange="filterNotes()">
            <option value="">🏫 All Classes (1–10)</option>
            @foreach(range(1, 10) as $c)
            <option value="{{ $c }}">Class {{ $c }}</option>
            @endforeach
        </select>
        <select class="form-control" id="notes-filter-type" onchange="filterNotes()">
            <option value="">Type: All</option>
            <option value="pdf">PDF Document</option>
            <option value="image">Image</option>
            <option value="doc">Word Doc</option>
        </select>
        <select class="form-control" id="notes-filter-status" onchange="filterNotes()">
            <option value="">Status: All</option>
            <option value="1">Active (Published)</option>
            <option value="0">Hidden (Draft)</option>
        </select>
        <input type="text" class="form-control search-input" id="notes-filter-search" placeholder="🔍 Search study notes…" onkeyup="debounce(filterNotes, 300)()">
        <button class="btn btn-outline btn-sm" onclick="resetNotesFilters()">Reset</button>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <span class="card-title">Study Notes & PDFs</span>
                <span class="badge badge-purple" id="notesCountBadge">0</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('addNoteModal')">＋ Add Note</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title & Description</th>
                        <th>Class & Chapter</th>
                        <th>Teacher</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="notesBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination" id="notesPagination" style="display:none">
            <span id="notesPagInfo">Showing 0 of 0</span>
            <div class="pagination-btns" id="notesPagBtns"></div>
        </div>
    </div>
</div>

<!-- ════════ MODALS ════════ -->

<!-- Add Recorded Class Modal -->
<div class="modal-backdrop" id="addRecordedModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">🎬 Add Recorded Class</span>
            <button class="modal-close" onclick="closeModal('addRecordedModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select class="form-control" id="rc-class" onchange="cascadeContent('rc', 'class')">
                        <option value="">Select class…</option>
                        @foreach(range(1, 10) as $c)
                        <option value="{{ $c }}">Class {{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject *</label>
                    <select class="form-control" id="rc-subject" onchange="cascadeContent('rc', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control" id="rc-chapter" onchange="cascadeContent('rc', 'chapter')" disabled>
                        <option value="">Select chapter…</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teacher *</label>
                    <select class="form-control" id="rc-teacher" disabled>
                        <option value="">Select teacher…</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" class="form-control" id="rc-title" placeholder="Class title">
            </div>
            <div class="form-group">
                <label class="form-label">Google Drive File ID / Video ID *</label>
                <input type="text" class="form-control" id="rc-gdrive" placeholder="e.g. 1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.35rem">ℹ️ Stored securely and automatically converted to an embed player URL.</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Duration (seconds)</label>
                    <input type="number" class="form-control" id="rc-duration" value="1800">
                </div>
                <div class="form-group">
                    <label class="form-label">Thumbnail URL</label>
                    <input type="url" class="form-control" id="rc-thumb" placeholder="https://…">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="rc-desc" rows="2" placeholder="Optional learning objectives or summary…"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addRecordedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createRecorded()">Add Class</button>
        </div>
    </div>
</div>

<!-- Edit Recorded Class Modal -->
<div class="modal-backdrop" id="editRecordedModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Recorded Class</span>
            <button class="modal-close" onclick="closeModal('editRecordedModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-rc-id">
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" class="form-control" id="edit-rc-title">
            </div>
            <div class="form-group">
                <label class="form-label">Google Drive File ID / Video ID *</label>
                <input type="text" class="form-control" id="edit-rc-gdrive">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Duration (seconds)</label>
                    <input type="number" class="form-control" id="edit-rc-duration">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="edit-rc-active">
                        <option value="1">Active (Published)</option>
                        <option value="0">Hidden (Draft)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Thumbnail URL</label>
                <input type="url" class="form-control" id="edit-rc-thumb">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="edit-rc-desc" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editRecordedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateRecorded()">Save Changes</button>
        </div>
    </div>
</div>

<!-- Add Live Class Modal -->
<div class="modal-backdrop" id="addLiveModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📡 Schedule Live Class</span>
            <button class="modal-close" onclick="closeModal('addLiveModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select class="form-control" id="lc-class" onchange="cascadeContent('lc', 'class')">
                        <option value="">Select class…</option>
                        @foreach(range(1, 10) as $c)
                        <option value="{{ $c }}">Class {{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject *</label>
                    <select class="form-control" id="lc-subject" onchange="cascadeContent('lc', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control" id="lc-chapter" onchange="cascadeContent('lc', 'chapter')" disabled>
                        <option value="">Select chapter…</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teacher *</label>
                    <select class="form-control" id="lc-teacher" disabled>
                        <option value="">Select teacher…</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" class="form-control" id="lc-title" placeholder="Live Q&A Session">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Platform *</label>
                    <select class="form-control" id="lc-platform">
                        <option value="google_meet">Google Meet</option>
                        <option value="zoom">Zoom</option>
                        <option value="teams">MS Teams</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes) *</label>
                    <input type="number" class="form-control" id="lc-duration" value="60" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Meeting URL *</label>
                <input type="url" class="form-control" id="lc-url" placeholder="https://meet.google.com/abc-defg-hij">
            </div>
            <div class="form-group">
                <label class="form-label">Scheduled Date & Time *</label>
                <input type="datetime-local" class="form-control" id="lc-scheduled">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="lc-desc" rows="2" placeholder="Discussion topics, required textbook pages…"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addLiveModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createLive()">Schedule Class</button>
        </div>
    </div>
</div>

<!-- Edit Live Class Modal -->
<div class="modal-backdrop" id="editLiveModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Live Class</span>
            <button class="modal-close" onclick="closeModal('editLiveModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-lc-id">
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" class="form-control" id="edit-lc-title">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Platform *</label>
                    <select class="form-control" id="edit-lc-platform">
                        <option value="google_meet">Google Meet</option>
                        <option value="zoom">Zoom</option>
                        <option value="teams">MS Teams</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select class="form-control" id="edit-lc-status">
                        <option value="scheduled">Scheduled</option>
                        <option value="live">Live</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Meeting URL *</label>
                    <input type="url" class="form-control" id="edit-lc-url">
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" class="form-control" id="edit-lc-duration">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Scheduled At</label>
                <input type="datetime-local" class="form-control" id="edit-lc-scheduled">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="edit-lc-desc" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editLiveModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateLive()">Save Changes</button>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal-backdrop" id="addNoteModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📋 Add Study Note</span>
            <button class="modal-close" onclick="closeModal('addNoteModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select class="form-control" id="n-class" onchange="cascadeContent('n', 'class')">
                        <option value="">Select class…</option>
                        @foreach(range(1, 10) as $c)
                        <option value="{{ $c }}">Class {{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject *</label>
                    <select class="form-control" id="n-subject" onchange="cascadeContent('n', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control" id="n-chapter" onchange="cascadeContent('n', 'chapter')" disabled>
                        <option value="">Select chapter…</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teacher *</label>
                    <select class="form-control" id="n-teacher" disabled>
                        <option value="">Select teacher…</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control" id="n-title" placeholder="Note title">
                </div>
                <div class="form-group">
                    <label class="form-label">File Type *</label>
                    <select class="form-control" id="n-type">
                        <option value="pdf">PDF Document</option>
                        <option value="image">Image</option>
                        <option value="doc">Word Document</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">File URL *</label>
                <input type="url" class="form-control" id="n-url" placeholder="https://…">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="n-desc" rows="2" placeholder="Summary of topics covered in this PDF…"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addNoteModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createNote()">Add Note</button>
        </div>
    </div>
</div>

<!-- Edit Note Modal -->
<div class="modal-backdrop" id="editNoteModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Study Note</span>
            <button class="modal-close" onclick="closeModal('editNoteModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-n-id">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control" id="edit-n-title">
                </div>
                <div class="form-group">
                    <label class="form-label">File Type *</label>
                    <select class="form-control" id="edit-n-type">
                        <option value="pdf">PDF Document</option>
                        <option value="image">Image</option>
                        <option value="doc">Word Document</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">File URL *</label>
                    <input type="url" class="form-control" id="edit-n-url">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="edit-n-active">
                        <option value="1">Active (Published)</option>
                        <option value="0">Hidden (Draft)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="edit-n-desc" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editNoteModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateNote()">Save Changes</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let allClassSubjects = [];
let recPage = 1, livePage = 1, notesPage = 1;

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

async function loadDropdowns() {
    try {
        const [csRes, subRes] = await Promise.all([
            api('GET', '/admin/class-subjects/all'),
            api('GET', '/admin/subjects')
        ]);
        allClassSubjects = csRes.data || [];
        const subjects = subRes.data || [];
        const subOptions = '<option value="">📚 All Subjects</option>' +
            subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        document.getElementById('rec-filter-subject').innerHTML = subOptions;
    } catch(e) { console.error('Error loading dropdowns', e); }
}

async function cascadeContent(prefix, step) {
    const cls = document.getElementById(`${prefix}-class`).value;
    const sub = document.getElementById(`${prefix}-subject`);
    const chp = document.getElementById(`${prefix}-chapter`);
    const tch = document.getElementById(`${prefix}-teacher`);

    if (step === 'class') {
        sub.innerHTML = '<option value="">Select subject…</option>';
        chp.innerHTML = '<option value="">Select chapter…</option>';
        tch.innerHTML = '<option value="">Select teacher…</option>';
        chp.disabled = true; tch.disabled = true;

        if (!cls) { sub.disabled = true; return; }

        const subjects = allClassSubjects.filter(cs => cs.class_level === parseInt(cls));
        sub.innerHTML += subjects.map(s => `<option value="${s.subject_id}">${s.subject_name}</option>`).join('');
        sub.disabled = false;
    } 
    else if (step === 'subject') {
        chp.innerHTML = '<option value="">Select chapter…</option>';
        tch.innerHTML = '<option value="">Select teacher…</option>';
        tch.disabled = true;

        if (!sub.value) { chp.disabled = true; return; }

        chp.innerHTML = '<option value="">Loading…</option>';
        chp.disabled = true;

        try {
            const [cRes, tRes] = await Promise.all([
                api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${sub.value}`),
                api('GET', `/admin/teachers?class_level=${cls}&subject_id=${sub.value}`)
            ]);

            const chapters = cRes.data || [];
            chp.innerHTML = '<option value="">Select chapter…</option>' + 
                chapters.map(c => `<option value="${c.id}">${c.title}</option>`).join('');
            chp.disabled = false;

            const teachers = tRes.data || tRes.data?.data || [];
            tch.innerHTML = '<option value="">Select teacher…</option>' + 
                teachers.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            tch.disabled = false;
        } catch(e) { toast(e.message, 'error'); }
    }
}

// ══════════════ RECORDED CLASSES ══════════════
function filterRecorded() { recPage = 1; loadRecorded(1); }
function resetRecordedFilters() {
    document.getElementById('rec-filter-class').value = '';
    document.getElementById('rec-filter-subject').value = '';
    document.getElementById('rec-filter-status').value = '';
    document.getElementById('rec-filter-search').value = '';
    filterRecorded();
}

async function loadRecorded(page = 1) {
    recPage = page;
    const cls = document.getElementById('rec-filter-class').value;
    const sub = document.getElementById('rec-filter-subject').value;
    const status = document.getElementById('rec-filter-status').value;
    const search = document.getElementById('rec-filter-search').value.trim();

    let query = `?page=${page}`;
    if (cls) query += `&class_level=${cls}`;
    if (sub) query += `&subject_id=${sub}`;
    if (status !== '') query += `&is_active=${status}`;
    if (search) query += `&search=${encodeURIComponent(search)}`;

    document.getElementById('recordedBody').innerHTML = '<tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr>';

    try {
        const res = await api('GET', `/admin/recorded-classes${query}`);
        const data = res.data || [];
        const meta = res.meta || {};
        document.getElementById('recCountBadge').textContent = meta.total || data.length;

        document.getElementById('recordedBody').innerHTML = data.length ? data.map(c => `
            <tr>
                <td>
                    <div style="font-weight:600;font-size:0.875rem">${escapeHtml(c.title)}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${escapeHtml(c.description || 'No description')}</div>
                </td>
                <td>
                    <div>${escapeHtml(c.chapter?.title || '—')}</div>
                    <span class="badge badge-purple" style="font-size:0.65rem">Class ${c.chapter?.class_level || 'All'}</span>
                </td>
                <td>${escapeHtml(c.teacher?.name || '—')}</td>
                <td>${c.duration_seconds ? Math.floor(c.duration_seconds/60) + 'm' : '—'}</td>
                <td>
                    <button class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}" style="cursor:pointer;border:none" onclick="toggleRecordedStatus(${c.id}, ${!c.is_active})">
                        ${c.is_active ? 'Active' : 'Hidden'}
                    </button>
                </td>
                <td>
                    <div class="action-btn-group">
                        <button class="btn btn-outline btn-sm" onclick="openEditRecorded(${c.id})">✏️</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRecorded(${c.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--text-muted)">No recorded classes found matching criteria.</td></tr>';

        renderPagination('recPagination', 'recPagInfo', 'recPagBtns', meta, loadRecorded);
    } catch(e) { toast(e.message, 'error'); }
}

async function openEditRecorded(id) {
    try {
        const res = await api('GET', `/admin/recorded-classes/${id}`);
        const c = res.data;
        document.getElementById('edit-rc-id').value = c.id;
        document.getElementById('edit-rc-title').value = c.title;
        document.getElementById('edit-rc-gdrive').value = c.gdrive_file_id;
        document.getElementById('edit-rc-duration').value = c.duration_seconds || '';
        document.getElementById('edit-rc-thumb').value = c.thumbnail_url || '';
        document.getElementById('edit-rc-desc').value = c.description || '';
        document.getElementById('edit-rc-active').value = c.is_active ? '1' : '0';
        openModal('editRecordedModal');
    } catch(e) { toast(e.message, 'error'); }
}

async function updateRecorded() {
    const id = document.getElementById('edit-rc-id').value;
    try {
        await api('PUT', `/admin/recorded-classes/${id}`, {
            title: document.getElementById('edit-rc-title').value,
            gdrive_file_id: document.getElementById('edit-rc-gdrive').value,
            duration_seconds: parseInt(document.getElementById('edit-rc-duration').value) || null,
            thumbnail_url: document.getElementById('edit-rc-thumb').value || null,
            description: document.getElementById('edit-rc-desc').value || null,
            is_active: document.getElementById('edit-rc-active').value === '1',
        });
        toast('Recorded class updated successfully!', 'success');
        closeModal('editRecordedModal');
        loadRecorded(recPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function toggleRecordedStatus(id, newStatus) {
    try {
        await api('PUT', `/admin/recorded-classes/${id}`, { is_active: newStatus });
        toast(newStatus ? 'Class published!' : 'Class hidden.', 'success');
        loadRecorded(recPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function createRecorded() {
    try {
        await api('POST', '/admin/recorded-classes', {
            chapter_id: parseInt(document.getElementById('rc-chapter').value),
            teacher_id: parseInt(document.getElementById('rc-teacher').value),
            title: document.getElementById('rc-title').value,
            gdrive_file_id: document.getElementById('rc-gdrive').value,
            duration_seconds: parseInt(document.getElementById('rc-duration').value) || null,
            thumbnail_url: document.getElementById('rc-thumb').value || null,
            description: document.getElementById('rc-desc').value || null,
        });
        toast('Recorded class added successfully!', 'success');
        closeModal('addRecordedModal');
        loadRecorded(1);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteRecorded(id) {
    if (confirm('Are you sure you want to delete this recorded class?')) {
        await api('DELETE', `/admin/recorded-classes/${id}`);
        toast('Class deleted.', 'success');
        loadRecorded(recPage);
    }
}

// ══════════════ LIVE CLASSES ══════════════
function filterLive() { livePage = 1; loadLive(1); }
function resetLiveFilters() {
    document.getElementById('live-filter-class').value = '';
    document.getElementById('live-filter-status').value = '';
    document.getElementById('live-filter-search').value = '';
    filterLive();
}

async function loadLive(page = 1) {
    livePage = page;
    const cls = document.getElementById('live-filter-class').value;
    const status = document.getElementById('live-filter-status').value;
    const search = document.getElementById('live-filter-search').value.trim();

    let query = `?page=${page}`;
    if (cls) query += `&class_level=${cls}`;
    if (status) query += `&status=${status}`;
    if (search) query += `&search=${encodeURIComponent(search)}`;

    document.getElementById('liveBody').innerHTML = '<tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div></td></tr>';

    try {
        const res = await api('GET', `/admin/live-classes${query}`);
        const data = res.data || [];
        const meta = res.meta || {};
        document.getElementById('liveCountBadge').textContent = meta.total || data.length;

        document.getElementById('liveBody').innerHTML = data.length ? data.map(c => `
            <tr>
                <td>
                    <div style="font-weight:600;font-size:0.875rem">${escapeHtml(c.title)}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${escapeHtml(c.description || '')}</div>
                </td>
                <td>
                    <div>${escapeHtml(c.chapter?.title || '—')}</div>
                    <span class="badge badge-purple" style="font-size:0.65rem">Class ${c.chapter?.class_level || 'All'}</span>
                </td>
                <td>${escapeHtml(c.teacher?.name || '—')}</td>
                <td><span class="badge badge-neutral">${escapeHtml((c.platform||'').replace('_',' '))}</span></td>
                <td style="font-size:0.8rem">${c.scheduled_at ? new Date(c.scheduled_at).toLocaleString('en-IN') : '—'}</td>
                <td>
                    <select class="filter-select" style="font-size:0.75rem;padding:0.25rem 0.5rem" onchange="updateLiveStatus(${c.id}, this.value)">
                        ${['scheduled','live','completed','cancelled'].map(s =>
                            `<option value="${s}" ${c.status===s?'selected':''}>${s}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <div class="action-btn-group">
                        <button class="btn btn-outline btn-sm" onclick="openEditLive(${c.id})">✏️</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteLive(${c.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--text-muted)">No live classes found.</td></tr>';

        renderPagination('livePagination', 'livePagInfo', 'livePagBtns', meta, loadLive);
    } catch(e) { toast(e.message, 'error'); }
}

async function openEditLive(id) {
    try {
        const res = await api('GET', `/admin/live-classes/${id}`);
        const c = res.data;
        document.getElementById('edit-lc-id').value = c.id;
        document.getElementById('edit-lc-title').value = c.title;
        document.getElementById('edit-lc-platform').value = c.platform || 'google_meet';
        document.getElementById('edit-lc-url').value = c.meeting_url || '';
        document.getElementById('edit-lc-duration').value = c.duration_minutes || 60;
        document.getElementById('edit-lc-status').value = c.status || 'scheduled';
        document.getElementById('edit-lc-desc').value = c.description || '';
        if (c.scheduled_at) {
            document.getElementById('edit-lc-scheduled').value = new Date(c.scheduled_at).toISOString().slice(0, 16);
        }
        openModal('editLiveModal');
    } catch(e) { toast(e.message, 'error'); }
}

async function updateLive() {
    const id = document.getElementById('edit-lc-id').value;
    try {
        await api('PUT', `/admin/live-classes/${id}`, {
            title: document.getElementById('edit-lc-title').value,
            platform: document.getElementById('edit-lc-platform').value,
            meeting_url: document.getElementById('edit-lc-url').value,
            duration_minutes: parseInt(document.getElementById('edit-lc-duration').value) || 60,
            status: document.getElementById('edit-lc-status').value,
            scheduled_at: document.getElementById('edit-lc-scheduled').value,
            description: document.getElementById('edit-lc-desc').value || null,
        });
        toast('Live class updated successfully!', 'success');
        closeModal('editLiveModal');
        loadLive(livePage);
    } catch(e) { toast(e.message, 'error'); }
}

async function createLive() {
    try {
        await api('POST', '/admin/live-classes', {
            chapter_id: parseInt(document.getElementById('lc-chapter').value),
            teacher_id: parseInt(document.getElementById('lc-teacher').value),
            title: document.getElementById('lc-title').value,
            platform: document.getElementById('lc-platform').value,
            meeting_url: document.getElementById('lc-url').value,
            duration_minutes: parseInt(document.getElementById('lc-duration').value),
            scheduled_at: document.getElementById('lc-scheduled').value,
            description: document.getElementById('lc-desc').value || null,
        });
        toast('Live class scheduled successfully!', 'success');
        closeModal('addLiveModal');
        loadLive(1);
    } catch(e) { toast(e.message, 'error'); }
}

async function updateLiveStatus(id, status) {
    try {
        await api('PATCH', `/admin/live-classes/${id}/status`, { status });
        toast('Status updated!', 'success');
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteLive(id) {
    if (confirm('Delete this live class?')) {
        await api('DELETE', `/admin/live-classes/${id}`);
        toast('Live class deleted.', 'success');
        loadLive(livePage);
    }
}

// ══════════════ STUDY NOTES ══════════════
function filterNotes() { notesPage = 1; loadNotes(1); }
function resetNotesFilters() {
    document.getElementById('notes-filter-class').value = '';
    document.getElementById('notes-filter-type').value = '';
    document.getElementById('notes-filter-status').value = '';
    document.getElementById('notes-filter-search').value = '';
    filterNotes();
}

async function loadNotes(page = 1) {
    notesPage = page;
    const cls = document.getElementById('notes-filter-class').value;
    const type = document.getElementById('notes-filter-type').value;
    const status = document.getElementById('notes-filter-status').value;
    const search = document.getElementById('notes-filter-search').value.trim();

    let query = `?page=${page}`;
    if (cls) query += `&class_level=${cls}`;
    if (type) query += `&file_type=${type}`;
    if (status !== '') query += `&is_active=${status}`;
    if (search) query += `&search=${encodeURIComponent(search)}`;

    document.getElementById('notesBody').innerHTML = '<tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr>';

    try {
        const res = await api('GET', `/admin/notes${query}`);
        const data = res.data || [];
        const meta = res.meta || {};
        document.getElementById('notesCountBadge').textContent = meta.total || data.length;

        document.getElementById('notesBody').innerHTML = data.length ? data.map(n => `
            <tr>
                <td>
                    <div style="font-weight:600;font-size:0.875rem">${escapeHtml(n.title)}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${escapeHtml(n.description || '')}</div>
                </td>
                <td>
                    <div>${escapeHtml(n.chapter?.title || '—')}</div>
                    <span class="badge badge-purple" style="font-size:0.65rem">Class ${n.chapter?.class_level || 'All'}</span>
                </td>
                <td>${escapeHtml(n.teacher?.name || '—')}</td>
                <td><span class="badge badge-info">${escapeHtml(n.file_type || 'pdf')}</span></td>
                <td>
                    <button class="badge ${n.is_active ? 'badge-success' : 'badge-neutral'}" style="cursor:pointer;border:none" onclick="toggleNoteStatus(${n.id}, ${!n.is_active})">
                        ${n.is_active ? 'Active' : 'Hidden'}
                    </button>
                </td>
                <td>
                    <div class="action-btn-group">
                        <button class="btn btn-outline btn-sm" onclick="openEditNote(${n.id})">✏️</button>
                        <a href="${escapeHtml(n.file_url)}" target="_blank" class="btn btn-outline btn-sm" title="View File">🔗</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteNote(${n.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--text-muted)">No study notes found.</td></tr>';

        renderPagination('notesPagination', 'notesPagInfo', 'notesPagBtns', meta, loadNotes);
    } catch(e) { toast(e.message, 'error'); }
}

async function openEditNote(id) {
    try {
        const res = await api('GET', `/admin/notes/${id}`);
        const n = res.data;
        document.getElementById('edit-n-id').value = n.id;
        document.getElementById('edit-n-title').value = n.title;
        document.getElementById('edit-n-type').value = n.file_type || 'pdf';
        document.getElementById('edit-n-url').value = n.file_url || '';
        document.getElementById('edit-n-desc').value = n.description || '';
        document.getElementById('edit-n-active').value = n.is_active ? '1' : '0';
        openModal('editNoteModal');
    } catch(e) { toast(e.message, 'error'); }
}

async function updateNote() {
    const id = document.getElementById('edit-n-id').value;
    try {
        await api('PUT', `/admin/notes/${id}`, {
            title: document.getElementById('edit-n-title').value,
            file_type: document.getElementById('edit-n-type').value,
            file_url: document.getElementById('edit-n-url').value,
            description: document.getElementById('edit-n-desc').value || null,
            is_active: document.getElementById('edit-n-active').value === '1',
        });
        toast('Note updated successfully!', 'success');
        closeModal('editNoteModal');
        loadNotes(notesPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function toggleNoteStatus(id, newStatus) {
    try {
        await api('PUT', `/admin/notes/${id}`, { is_active: newStatus });
        toast(newStatus ? 'Note published!' : 'Note hidden.', 'success');
        loadNotes(notesPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function createNote() {
    try {
        await api('POST', '/admin/notes', {
            chapter_id: parseInt(document.getElementById('n-chapter').value),
            teacher_id: parseInt(document.getElementById('n-teacher').value),
            title: document.getElementById('n-title').value,
            file_type: document.getElementById('n-type').value,
            file_url: document.getElementById('n-url').value,
            description: document.getElementById('n-desc').value || null,
        });
        toast('Study note added successfully!', 'success');
        closeModal('addNoteModal');
        loadNotes(1);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteNote(id) {
    if (confirm('Delete this note?')) {
        await api('DELETE', `/admin/notes/${id}`);
        toast('Note deleted.', 'success');
        loadNotes(notesPage);
    }
}

// ══════════════ PAGINATION HELPER ══════════════
function renderPagination(containerId, infoId, btnsId, meta, callback) {
    const container = document.getElementById(containerId);
    if (!meta || meta.total <= meta.per_page) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'flex';
    document.getElementById(infoId).textContent = `Page ${meta.current_page} of ${meta.last_page} (${meta.total} total items)`;

    let btns = '';
    btns += `<button class="page-btn" ${meta.current_page <= 1 ? 'disabled' : ''} onclick="${callback.name}(${meta.current_page - 1})">← Prev</button>`;

    for (let p = Math.max(1, meta.current_page - 2); p <= Math.min(meta.last_page, meta.current_page + 2); p++) {
        btns += `<button class="page-btn ${p === meta.current_page ? 'active' : ''}" onclick="${callback.name}(${p})">${p}</button>`;
    }

    btns += `<button class="page-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} onclick="${callback.name}(${meta.current_page + 1})">Next →</button>`;
    document.getElementById(btnsId).innerHTML = btns;
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

window._pageInit = async function() {
    await loadDropdowns();
    loadRecorded(1);
    loadLive(1);
    loadNotes(1);
};
</script>
@endsection
