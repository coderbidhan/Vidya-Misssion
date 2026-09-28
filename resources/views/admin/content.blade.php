@extends('layouts.admin')
@section('title','Content Management')
@section('page-title','Content Management')
@section('page-subtitle','Recorded Video Classes, Live Sessions, and Study Notes')

@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="openModal('addRecordedModal')">＋ New Video</button>
    <button class="btn btn-outline btn-sm" onclick="openModal('addLiveModal')">＋ Live Session</button>
    <button class="btn btn-outline btn-sm" onclick="openModal('addNoteModal')">＋ Study Note</button>
@endsection

@section('content')
<div class="tabs tab-group-content" style="margin-bottom:1.25rem">
    <button class="tab-btn active" data-tab="tab-explorer" onclick="switchTab('tab-explorer','tab-group-content')">📂 Chapter Explorer</button>
    <button class="tab-btn" data-tab="tab-recorded" onclick="switchTab('tab-recorded','tab-group-content')">🎬 Recorded Classes</button>
    <button class="tab-btn" data-tab="tab-live" onclick="switchTab('tab-live','tab-group-content')">📡 Live Classes</button>
    <button class="tab-btn" data-tab="tab-notes" onclick="switchTab('tab-notes','tab-group-content')">📋 Study Notes</button>
</div>

<!-- ═══ 1. CHAPTER EXPLORER (DRILL-DOWN) ═══ -->
<div class="tab-panel active" id="tab-explorer">
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header" style="padding:0.9rem 1.25rem">
            <span class="card-title">🔍 Browse Curriculum Content</span>
        </div>
        <div class="card-body" style="padding:1.25rem">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;align-items:end">
                <div class="form-group" style="margin:0">
                    <label class="form-label">1. Select Class</label>
                    <select class="form-control" id="exp-class" onchange="cascadeExplorer('class')">
                        <option value="">Select Class...</option>
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}" {{ $c === 9 ? 'selected' : '' }}>Class {{ $c }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">2. Select Subject</label>
                    <select class="form-control bn-font" id="exp-subject" onchange="cascadeExplorer('subject')">
                        <option value="">Select Subject...</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">3. Select Chapter</label>
                    <select class="form-control bn-font" id="exp-chapter" onchange="loadChapterDetails()">
                        <option value="">Select Chapter...</option>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="loadChapterDetails()" style="height:42px;width:100%">Load Chapter Content</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chapter Content Dashboard (Shown after chapter selected) -->
    <div id="chapterDashboard" style="display:none">
        <div class="card" style="margin-bottom:1.25rem;background:linear-gradient(135deg,#5B45FF 0%,#7F6BFF 100%);color:#fff;border:none">
            <div style="padding:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
                <div>
                    <div style="font-size:0.78rem;opacity:0.85;text-transform:uppercase;letter-spacing:0.06em" id="cd-crumb">Class 9 › Physical Science</div>
                    <div style="font-size:1.3rem;font-weight:800;margin-top:0.25rem" class="bn-font" id="cd-title">Chapter 1</div>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                    <button class="btn btn-sm" style="background:#fff;color:var(--primary);font-weight:700" onclick="openDirectAdd('video')">＋ Video</button>
                    <button class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4)" onclick="openDirectAdd('live')">＋ Live</button>
                    <button class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4)" onclick="openDirectAdd('note')">＋ Note</button>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.25rem">
            <!-- Recorded Classes in this Chapter -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">🎬 Recorded Video Classes (<span id="cd-video-count">0</span>)</span>
                    <button class="btn btn-outline btn-sm" onclick="openDirectAdd('video')">＋ Add</button>
                </div>
                <div style="padding:0.75rem;max-height:380px;overflow-y:auto" id="cd-videos-list">
                    <div class="empty-state" style="padding:2rem"><p>No video classes in this chapter yet.</p></div>
                </div>
            </div>

            <!-- Live Classes in this Chapter -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">📡 Live Sessions (<span id="cd-live-count">0</span>)</span>
                    <button class="btn btn-outline btn-sm" onclick="openDirectAdd('live')">＋ Schedule</button>
                </div>
                <div style="padding:0.75rem;max-height:380px;overflow-y:auto" id="cd-live-list">
                    <div class="empty-state" style="padding:2rem"><p>No live classes in this chapter yet.</p></div>
                </div>
            </div>

            <!-- Study Notes in this Chapter -->
            <div class="card" style="grid-column:1 / -1">
                <div class="card-header">
                    <span class="card-title">📋 Study Notes & PDFs (<span id="cd-note-count">0</span>)</span>
                    <button class="btn btn-outline btn-sm" onclick="openDirectAdd('note')">＋ Add Note</button>
                </div>
                <div style="padding:0.75rem" id="cd-notes-list">
                    <div class="empty-state" style="padding:2rem"><p>No notes in this chapter yet.</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ 2. ALL RECORDED CLASSES ═══ -->
<div class="tab-panel" id="tab-recorded">
    <div class="card">
        <div class="card-header">
            <span class="card-title">🎬 All Recorded Video Classes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addRecordedModal')">＋ Add Class</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Title & Description</th><th>Chapter & Subject</th><th>Teacher</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="recordedBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div> Loading videos…</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ 3. ALL LIVE CLASSES ═══ -->
<div class="tab-panel" id="tab-live">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📡 All Scheduled & Past Live Classes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addLiveModal')">＋ Schedule Live</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Session Title</th><th>Chapter</th><th>Faculty</th><th>Platform</th><th>Scheduled Date & Time</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="liveBody"><tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div> Loading live sessions…</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ 4. ALL NOTES ═══ -->
<div class="tab-panel" id="tab-notes">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 All Study Notes & Documents</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addNoteModal')">＋ Add Note</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Title</th><th>Chapter</th><th>Teacher</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="notesBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div> Loading study notes…</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ════════ MODALS ════════ -->

<!-- Add Recorded Class Modal -->
<div class="modal-backdrop" id="addRecordedModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title">🎬 Add Recorded Video Class</span>
            <button class="modal-close" onclick="closeModal('addRecordedModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class Level</label>
                    <select class="form-control" id="rc-class" onchange="cascadeModal('rc', 'class')">
                        <option value="">Select class…</option>
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}">Class {{ $c }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select class="form-control bn-font" id="rc-subject" onchange="cascadeModal('rc', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control bn-font" id="rc-chapter" disabled>
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
                <label class="form-label">Video Title (Bengali / English) *</label>
                <input type="text" class="form-control bn-font" id="rc-title" placeholder="e.g. গতি ও বল — Part 1 Concept Explanation" required>
            </div>

            <div class="form-group">
                <label class="form-label">YouTube URL / Video ID or Google Drive File ID *</label>
                <input type="text" class="form-control" id="rc-gdrive" placeholder="e.g. https://youtu.be/dQw4w9WgXcQ or 1A2b3C..." required>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem">Accepts YouTube full/short links or Google Drive file IDs.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Duration (Seconds)</label>
                    <input type="number" class="form-control" id="rc-duration" placeholder="e.g. 1800 for 30m" value="1800">
                </div>
                <div class="form-group">
                    <label class="form-label">Custom Thumbnail URL</label>
                    <input type="url" class="form-control" id="rc-thumb" placeholder="https://…">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description / Topics Covered</label>
                <textarea class="form-control bn-font" id="rc-desc" rows="2" placeholder="Key topics explained in this class..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addRecordedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createRecorded()">Save Video Class</button>
        </div>
    </div>
</div>

<!-- Edit Recorded Class Modal -->
<div class="modal-backdrop" id="editRecordedModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Recorded Video Class</span>
            <button class="modal-close" onclick="closeModal('editRecordedModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="erc-id">
            <input type="hidden" id="erc-chapter-id">
            <input type="hidden" id="erc-teacher-id">

            <div class="form-group">
                <label class="form-label">Video Title *</label>
                <input type="text" class="form-control bn-font" id="erc-title" required>
            </div>

            <div class="form-group">
                <label class="form-label">YouTube URL / ID or Google Drive ID *</label>
                <input type="text" class="form-control" id="erc-gdrive" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Duration (Seconds)</label>
                    <input type="number" class="form-control" id="erc-duration">
                </div>
                <div class="form-group">
                    <label class="form-label">Thumbnail URL</label>
                    <input type="url" class="form-control" id="erc-thumb">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" id="erc-active">
                    <option value="1">Active (Visible to Students)</option>
                    <option value="0">Hidden (Draft)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control bn-font" id="erc-desc" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editRecordedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateRecorded()">Update Video</button>
        </div>
    </div>
</div>

<!-- Video Player Preview Modal -->
<div class="modal-backdrop" id="videoPreviewModal">
    <div class="modal" style="max-width:700px;background:#0f172a;color:#fff">
        <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.1)">
            <span class="modal-title bn-font" id="vpm-title" style="color:#fff">Video Preview</span>
            <button class="modal-close" style="color:#fff" onclick="closeVideoPreview()">×</button>
        </div>
        <div class="modal-body" style="padding:0">
            <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;background:#000" id="vpm-container">
                <!-- iframe injected dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Add Live Class Modal -->
<div class="modal-backdrop" id="addLiveModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title">📡 Schedule Live Session</span>
            <button class="modal-close" onclick="closeModal('addLiveModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class</label>
                    <select class="form-control" id="lc-class" onchange="cascadeModal('lc', 'class')">
                        <option value="">Select class…</option>
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}">Class {{ $c }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select class="form-control bn-font" id="lc-subject" onchange="cascadeModal('lc', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control bn-font" id="lc-chapter" disabled>
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
                <label class="form-label">Live Session Title *</label>
                <input type="text" class="form-control bn-font" id="lc-title" placeholder="e.g. Doubt Clearing & Problem Solving" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Platform *</label>
                    <select class="form-control" id="lc-platform">
                        <option value="google_meet">Google Meet</option>
                        <option value="youtube_live">YouTube Live</option>
                        <option value="zoom">Zoom</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (Minutes)</label>
                    <input type="number" class="form-control" id="lc-duration" value="60" min="15">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Meeting / Stream URL *</label>
                <input type="url" class="form-control" id="lc-url" placeholder="https://meet.google.com/..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Scheduled Date & Time *</label>
                <input type="datetime-local" class="form-control" id="lc-scheduled" required>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addLiveModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createLive()">Schedule Live Class</button>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal-backdrop" id="addNoteModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title">📋 Add Study Note / Document</span>
            <button class="modal-close" onclick="closeModal('addNoteModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class</label>
                    <select class="form-control" id="n-class" onchange="cascadeModal('n', 'class')">
                        <option value="">Select class…</option>
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}">Class {{ $c }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select class="form-control bn-font" id="n-subject" onchange="cascadeModal('n', 'subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Chapter *</label>
                    <select class="form-control bn-font" id="n-chapter" disabled>
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

            <div class="form-group">
                <label class="form-label">Document Title *</label>
                <input type="text" class="form-control bn-font" id="n-title" placeholder="e.g. Chapter 1 Hand-written Revision Notes" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">File Type</label>
                    <select class="form-control" id="n-type">
                        <option value="pdf">PDF Document</option>
                        <option value="doc">Word / Google Doc</option>
                        <option value="image">Image / Diagram</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">File URL / Download Link *</label>
                    <input type="url" class="form-control" id="n-url" placeholder="https://drive.google.com/... or link" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description (Optional)</label>
                <textarea class="form-control bn-font" id="n-desc" rows="2" placeholder="Summary of this study material..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addNoteModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createNote()">Save Study Note</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentExplorer = { class: 9, subject: null, chapter: null };

async function loadDropdowns() {
    await cascadeExplorer('class');
}

async function cascadeExplorer(level) {
    const cls = document.getElementById('exp-class').value;
    const sub = document.getElementById('exp-subject');
    const chp = document.getElementById('exp-chapter');

    if (level === 'class') {
        if (!cls) { sub.disabled = true; chp.disabled = true; return; }
        sub.innerHTML = '<option value="">Loading subjects…</option>';
        sub.disabled = true;
        try {
            const res = await api('GET', `/admin/class-subjects?class_level=${cls}`);
            const subjects = res.data?.assigned_subjects || [];
            sub.innerHTML = '<option value="">Select Subject...</option>' + 
                subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            sub.disabled = false;
            chp.innerHTML = '<option value="">Select Chapter...</option>';
            chp.disabled = true;

            // Auto-select first subject if none selected
            if (subjects.length > 0 && !currentExplorer.subject) {
                sub.value = subjects[0].id;
                await cascadeExplorer('subject');
            }
        } catch(e) { toast(e.message, 'error'); }
    } else if (level === 'subject') {
        if (!sub.value) { chp.disabled = true; return; }
        chp.innerHTML = '<option value="">Loading chapters…</option>';
        chp.disabled = true;
        try {
            const res = await api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${sub.value}`);
            const chapters = res.data || [];
            chp.innerHTML = '<option value="">Select Chapter...</option>' + 
                chapters.map(c => `<option value="${c.id}">${c.title}</option>`).join('');
            chp.disabled = false;

            if (chapters.length > 0) {
                chp.value = chapters[0].id;
                await loadChapterDetails();
            }
        } catch(e) { toast(e.message, 'error'); }
    }
}

async function loadChapterDetails() {
    const clsId = document.getElementById('exp-class').value;
    const subId = document.getElementById('exp-subject').value;
    const chpId = document.getElementById('exp-chapter').value;

    if (!chpId) return;

    try {
        const res = await api('GET', `/admin/chapters/${chpId}`);
        const ch = res.data;
        if (!ch) return;

        currentExplorer = { class: clsId, subject: subId, chapter: chpId, data: ch };

        document.getElementById('cd-crumb').textContent = `Class ${clsId} › ${document.getElementById('exp-subject').selectedOptions[0]?.text || 'Subject'}`;
        document.getElementById('cd-title').textContent = ch.title;
        document.getElementById('cd-video-count').textContent = ch.recorded_classes?.length || 0;
        document.getElementById('cd-live-count').textContent = ch.live_classes?.length || 0;
        document.getElementById('cd-note-count').textContent = ch.notes?.length || 0;

        // Render Recorded Classes
        const vList = ch.recorded_classes || [];
        document.getElementById('cd-videos-list').innerHTML = vList.length ? vList.map(v => `
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.65rem 0.75rem;border-radius:10px;background:var(--bg-app);margin-bottom:0.4rem;gap:0.75rem">
                <div style="min-width:0;flex:1">
                    <div style="font-weight:700;font-size:0.85rem" class="bn-font">${v.title}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">👨‍🏫 ${v.teacher?.name || 'Faculty'} • ⏱ ${v.duration_seconds ? Math.round(v.duration_seconds/60)+'m' : '—'}</div>
                </div>
                <div style="display:flex;gap:0.3rem">
                    <button class="btn btn-outline btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem" onclick="previewVideo('${v.embed_url || v.gdrive_file_id}', '${v.title.replace(/'/g,"\\'")}')">▶ Watch</button>
                    <button class="btn btn-danger btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem" onclick="deleteRecorded(${v.id})">🗑</button>
                </div>
            </div>
        `).join('') : '<div class="empty-state" style="padding:1.5rem"><p>No videos in this chapter yet.</p></div>';

        // Render Live Classes
        const lList = ch.live_classes || [];
        document.getElementById('cd-live-list').innerHTML = lList.length ? lList.map(l => `
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.65rem 0.75rem;border-radius:10px;background:var(--bg-app);margin-bottom:0.4rem;gap:0.75rem">
                <div style="min-width:0;flex:1">
                    <div style="font-weight:700;font-size:0.85rem" class="bn-font">${l.title}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">📅 ${l.scheduled_at ? new Date(l.scheduled_at).toLocaleString('en-IN') : '—'} • <span class="badge badge-purple">${l.platform}</span></div>
                </div>
                <div style="display:flex;gap:0.3rem">
                    <a href="${l.meeting_url}" target="_blank" class="btn btn-primary btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem">Join</a>
                    <button class="btn btn-danger btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem" onclick="deleteLive(${l.id})">🗑</button>
                </div>
            </div>
        `).join('') : '<div class="empty-state" style="padding:1.5rem"><p>No live classes in this chapter yet.</p></div>';

        // Render Notes
        const nList = ch.notes || [];
        document.getElementById('cd-notes-list').innerHTML = nList.length ? nList.map(n => `
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.65rem 0.75rem;border-radius:10px;background:var(--bg-app);margin-bottom:0.4rem;gap:0.75rem">
                <div style="min-width:0;flex:1">
                    <div style="font-weight:700;font-size:0.85rem" class="bn-font">${n.title}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)"><span class="badge badge-info">${n.file_type}</span> ${n.teacher?.name ? '• By ' + n.teacher.name : ''}</div>
                </div>
                <div style="display:flex;gap:0.3rem">
                    <a href="${n.file_url}" target="_blank" class="btn btn-outline btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem">Open Link ↗</a>
                    <button class="btn btn-danger btn-sm" style="padding:0.25rem 0.5rem;font-size:0.75rem" onclick="deleteNote(${n.id})">🗑</button>
                </div>
            </div>
        `).join('') : '<div class="empty-state" style="padding:1.5rem"><p>No study notes in this chapter yet.</p></div>';

        document.getElementById('chapterDashboard').style.display = 'block';
    } catch(e) { toast('Error: ' + e.message, 'error'); }
}

function previewVideo(embedOrId, title) {
    document.getElementById('vpm-title').textContent = title || 'Video Preview';
    const container = document.getElementById('vpm-container');
    let src = embedOrId;
    if (src.includes('youtu.be/')) {
        const id = src.split('youtu.be/')[1]?.split('?')[0];
        src = `https://www.youtube.com/embed/${id}`;
    } else if (src.includes('watch?v=')){
        const id = src.split('watch?v=')[1]?.split('&')[0];
        src = `https://www.youtube.com/embed/${id}`;
    } else if (!src.startsWith('http')) {
        src = `https://www.youtube.com/embed/${src}`;
    }
    container.innerHTML = `<iframe src="${src}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>`;
    openModal('videoPreviewModal');
}

function closeVideoPreview() {
    document.getElementById('vpm-container').innerHTML = '';
    closeModal('videoPreviewModal');
}

async function cascadeModal(prefix, level) {
    const cls = document.getElementById(`${prefix}-class`).value;
    const sub = document.getElementById(`${prefix}-subject`);
    const chp = document.getElementById(`${prefix}-chapter`);
    const tch = document.getElementById(`${prefix}-teacher`);

    if (level === 'class') {
        if (!cls) { sub.disabled = true; chp.disabled = true; tch.disabled = true; return; }
        sub.innerHTML = '<option value="">Loading…</option>';
        sub.disabled = true;
        try {
            const res = await api('GET', `/admin/class-subjects?class_level=${cls}`);
            const subjects = res.data?.assigned_subjects || [];
            sub.innerHTML = '<option value="">Select subject…</option>' + 
                subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            sub.disabled = false;
        } catch(e) { toast(e.message, 'error'); }
    } else if (level === 'subject') {
        if (!sub.value) { chp.disabled = true; tch.disabled = true; return; }
        chp.innerHTML = '<option value="">Loading…</option>';
        chp.disabled = true;
        try {
            const [cRes, tRes] = await Promise.all([
                api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${sub.value}`),
                api('GET', `/admin/teachers`)
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

async function openDirectAdd(type) {
    if (!currentExplorer.chapter) { toast('Please select a chapter first', 'warning'); return; }
    
    if (type === 'video') {
        openModal('addRecordedModal');
        document.getElementById('rc-class').value = currentExplorer.class;
        await cascadeModal('rc', 'class');
        document.getElementById('rc-subject').value = currentExplorer.subject;
        await cascadeModal('rc', 'subject');
        document.getElementById('rc-chapter').value = currentExplorer.chapter;
    } else if (type === 'live') {
        openModal('addLiveModal');
        document.getElementById('lc-class').value = currentExplorer.class;
        await cascadeModal('lc', 'class');
        document.getElementById('lc-subject').value = currentExplorer.subject;
        await cascadeModal('lc', 'subject');
        document.getElementById('lc-chapter').value = currentExplorer.chapter;
    } else if (type === 'note') {
        openModal('addNoteModal');
        document.getElementById('n-class').value = currentExplorer.class;
        await cascadeModal('n', 'class');
        document.getElementById('n-subject').value = currentExplorer.subject;
        await cascadeModal('n', 'subject');
        document.getElementById('n-chapter').value = currentExplorer.chapter;
    }
}

async function loadRecorded(page = 1) {
    try {
        const res = await api('GET', `/admin/recorded-classes?page=${page}`);
        const data = res.data?.data || res.data || [];
        document.getElementById('recordedBody').innerHTML = data.length ? data.map(c => `
            <tr>
                <td>
                    <div style="font-weight:700" class="bn-font">${c.title}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${c.description || ''}</div>
                </td>
                <td><strong class="bn-font">${c.chapter?.title || '—'}</strong></td>
                <td>${c.teacher?.name || '—'}</td>
                <td>${c.duration_seconds ? Math.floor(c.duration_seconds/60)+'m' : '—'}</td>
                <td><span class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}">${c.is_active ? 'Active' : 'Hidden'}</span></td>
                <td>
                    <div style="display:flex;gap:0.35rem">
                        <button class="btn btn-outline btn-sm" onclick="previewVideo('${c.embed_url || c.gdrive_file_id}', '${c.title.replace(/'/g,"\\'")}')">▶</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRecorded(${c.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No recorded classes yet.</td></tr>';
    } catch(e) { toast(e.message, 'error'); }
}

async function loadLive(page = 1) {
    try {
        const res = await api('GET', `/admin/live-classes?page=${page}`);
        const data = res.data?.data || res.data || [];
        document.getElementById('liveBody').innerHTML = data.length ? data.map(c => `
            <tr>
                <td><div style="font-weight:700" class="bn-font">${c.title}</div></td>
                <td><span class="bn-font">${c.chapter?.title || '—'}</span></td>
                <td>${c.teacher?.name || '—'}</td>
                <td><span class="badge badge-purple">${c.platform?.replace('_',' ') || '—'}</span></td>
                <td style="font-size:0.82rem;font-weight:600">${c.scheduled_at ? new Date(c.scheduled_at).toLocaleString('en-IN') : '—'}</td>
                <td>
                    <select class="form-control" style="font-size:0.75rem;padding:0.25rem 0.5rem;width:auto;height:auto"
                        onchange="updateLiveStatus(${c.id}, this.value)">
                        ${['scheduled','live','completed','cancelled'].map(s =>
                            `<option value="${s}" ${c.status === s ? 'selected' : ''}>${s}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <div style="display:flex;gap:0.35rem">
                        <a href="${c.meeting_url}" target="_blank" class="btn btn-primary btn-sm">Launch ↗</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteLive(${c.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">No live classes yet.</td></tr>';
    } catch(e) { toast(e.message, 'error'); }
}

async function loadNotes() {
    try {
        const res = await api('GET', '/admin/notes');
        const data = res.data?.data || res.data || [];
        document.getElementById('notesBody').innerHTML = data.length ? data.map(n => `
            <tr>
                <td style="font-weight:700" class="bn-font">${n.title}</td>
                <td><span class="bn-font">${n.chapter?.title || '—'}</span></td>
                <td>${n.teacher?.name || '—'}</td>
                <td><span class="badge badge-info">${n.file_type}</span></td>
                <td><span class="badge ${n.is_active ? 'badge-success' : 'badge-neutral'}">${n.is_active ? 'Active' : 'Hidden'}</span></td>
                <td>
                    <div style="display:flex;gap:0.35rem">
                        <a href="${n.file_url}" target="_blank" class="btn btn-outline btn-sm">Open ↗</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteNote(${n.id})">🗑</button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No notes yet.</td></tr>';
    } catch(e) { toast(e.message, 'error'); }
}

async function createRecorded() {
    const chapter_id = parseInt(document.getElementById('rc-chapter').value);
    const teacher_id = parseInt(document.getElementById('rc-teacher').value);
    const title      = document.getElementById('rc-title').value.trim();
    const gdrive     = document.getElementById('rc-gdrive').value.trim();

    if (!chapter_id || !teacher_id || !title || !gdrive) {
        toast('Please fill all required fields.', 'error');
        return;
    }

    try {
        await api('POST', '/admin/recorded-classes', {
            chapter_id,
            teacher_id,
            title,
            gdrive_file_id: gdrive,
            duration_seconds: parseInt(document.getElementById('rc-duration').value) || null,
            thumbnail_url: document.getElementById('rc-thumb').value || null,
            description: document.getElementById('rc-desc').value || null,
        });
        toast('Recorded class saved!', 'success');
        closeModal('addRecordedModal');
        loadRecorded();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function createLive() {
    const chapter_id = parseInt(document.getElementById('lc-chapter').value);
    const teacher_id = parseInt(document.getElementById('lc-teacher').value);
    const title      = document.getElementById('lc-title').value.trim();
    const url        = document.getElementById('lc-url').value.trim();
    const scheduled  = document.getElementById('lc-scheduled').value;

    if (!chapter_id || !teacher_id || !title || !url || !scheduled) {
        toast('Please fill all required fields.', 'error');
        return;
    }

    try {
        await api('POST', '/admin/live-classes', {
            chapter_id,
            teacher_id,
            title,
            platform: document.getElementById('lc-platform').value,
            meeting_url: url,
            duration_minutes: parseInt(document.getElementById('lc-duration').value) || 60,
            scheduled_at: scheduled,
        });
        toast('Live class scheduled!', 'success');
        closeModal('addLiveModal');
        loadLive();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function createNote() {
    const chapter_id = parseInt(document.getElementById('n-chapter').value);
    const teacher_id = parseInt(document.getElementById('n-teacher').value);
    const title      = document.getElementById('n-title').value.trim();
    const url        = document.getElementById('n-url').value.trim();

    if (!chapter_id || !teacher_id || !title || !url) {
        toast('Please fill all required fields.', 'error');
        return;
    }

    try {
        await api('POST', '/admin/notes', {
            chapter_id,
            teacher_id,
            title,
            file_type: document.getElementById('n-type').value,
            file_url: url,
            description: document.getElementById('n-desc').value || null,
        });
        toast('Study note saved!', 'success');
        closeModal('addNoteModal');
        loadNotes();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteRecorded(id) {
    if (!confirm('Delete this video class?')) return;
    try {
        await api('DELETE', `/admin/recorded-classes/${id}`);
        toast('Video deleted.', 'success');
        loadRecorded();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteLive(id) {
    if (!confirm('Delete this live class?')) return;
    try {
        await api('DELETE', `/admin/live-classes/${id}`);
        toast('Live class deleted.', 'success');
        loadLive();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteNote(id) {
    if (!confirm('Delete this note?')) return;
    try {
        await api('DELETE', `/admin/notes/${id}`);
        toast('Note deleted.', 'success');
        loadNotes();
        loadChapterDetails();
    } catch(e) { toast(e.message, 'error'); }
}

async function updateLiveStatus(id, status) {
    try {
        await api('PATCH', `/admin/live-classes/${id}/status`, { status });
        toast('Status updated!', 'success');
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = async function() {
    // Check URL search params for direct jump
    const params = new URLSearchParams(window.location.search);
    const pClass = params.get('class');
    const pSubj  = params.get('subject');
    const pChap  = params.get('chapter');

    if (pClass) {
        document.getElementById('exp-class').value = pClass;
        currentExplorer.class = pClass;
        currentExplorer.subject = pSubj;
        currentExplorer.chapter = pChap;
    }

    await loadDropdowns();
    loadRecorded(); loadLive(); loadNotes();
};
</script>
@endsection
