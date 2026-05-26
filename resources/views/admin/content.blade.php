@extends('layouts.admin')
@section('title','Content Management')
@section('page-title','Content Management')
@section('page-subtitle','Recorded Classes, Live Classes & Notes')

@section('content')
<div class="tabs tab-group-content">
    <button class="tab-btn active" data-tab="tab-recorded" onclick="switchTab('tab-recorded','tab-group-content')">🎬 Recorded Classes</button>
    <button class="tab-btn" data-tab="tab-live" onclick="switchTab('tab-live','tab-group-content')">📡 Live Classes</button>
    <button class="tab-btn" data-tab="tab-notes" onclick="switchTab('tab-notes','tab-group-content')">📋 Notes</button>
</div>

<!-- ═══ RECORDED CLASSES ═══ -->
<div class="tab-panel active" id="tab-recorded">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Recorded Video Classes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addRecordedModal')">＋ Add Class</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Title</th><th>Chapter</th><th>Teacher</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="recordedBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination" id="recPagination" style="display:none">
            <span id="recPagInfo"></span>
            <div class="pagination-btns" id="recPagBtns"></div>
        </div>
    </div>
</div>

<!-- ═══ LIVE CLASSES ═══ -->
<div class="tab-panel" id="tab-live">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Live Classes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addLiveModal')">＋ Schedule Class</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Title</th><th>Chapter</th><th>Teacher</th><th>Platform</th><th>Scheduled</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="liveBody"><tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ NOTES ═══ -->
<div class="tab-panel" id="tab-notes">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Study Notes</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('addNoteModal')">＋ Add Note</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Title</th><th>Chapter</th><th>Teacher</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="notesBody"><tr class="loading-row"><td colspan="6"><div class="spinner-sm"></div></td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ════════ MODALS ════════ -->

<!-- Add Recorded Class -->
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
                <label class="form-label">Google Drive File ID *</label>
                <input type="text" class="form-control" id="rc-gdrive" placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74…">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.35rem">ℹ️ Paste the file ID from the Drive share URL (never exposed to students)</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Duration (seconds)</label>
                    <input type="number" class="form-control" id="rc-duration" placeholder="1800">
                </div>
                <div class="form-group">
                    <label class="form-label">Thumbnail URL</label>
                    <input type="url" class="form-control" id="rc-thumb" placeholder="https://…">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="rc-desc" rows="2" placeholder="Optional description…"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addRecordedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createRecorded()">Add Class</button>
        </div>
    </div>
</div>

<!-- Add Live Class -->
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
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addLiveModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createLive()">Schedule Class</button>
        </div>
    </div>
</div>

<!-- Add Note -->
<div class="modal-backdrop" id="addNoteModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📋 Add Note</span>
            <button class="modal-close" onclick="closeModal('addNoteModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select class="form-control" id="n-class" onchange="cascadeContent('n', 'class')">
                        <option value="">Select class…</option>
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
                        <option value="pdf">PDF</option>
                        <option value="image">Image</option>
                        <option value="doc">Document</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">File URL *</label>
                <input type="url" class="form-control" id="n-url" placeholder="https://drive.google.com/…">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="n-desc" rows="2" placeholder="Optional description…"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addNoteModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createNote()">Add Note</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allClassSubjects = [];

async function loadDropdowns() {
    const res = await api('GET', '/admin/class-subjects/all');
    allClassSubjects = res.data || [];
    const distinctClasses = [...new Set(allClassSubjects.map(cs => cs.class_level))].sort((a,b)=>a-b);
    const classOpts = '<option value="">Select class…</option>' + 
        distinctClasses.map(c => `<option value="${c}">Class ${c}</option>`).join('');

    ['rc-class','lc-class','n-class'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = classOpts;
    });
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

async function loadRecorded(page = 1) {
    const res = await api('GET', `/admin/recorded-classes?page=${page}`);
    const data = res.data?.data || [];
    document.getElementById('recordedBody').innerHTML = data.length ? data.map(c => `
        <tr>
            <td><div style="font-weight:600">${c.title}</div><div style="font-size:0.75rem;color:var(--text-muted)">${c.description||''}</div></td>
            <td>${c.chapter?.title||'—'}</td>
            <td>${c.teacher?.name||'—'}</td>
            <td>${c.duration_seconds ? Math.floor(c.duration_seconds/60)+'m' : '—'}</td>
            <td><span class="badge ${c.is_active?'badge-success':'badge-neutral'}">${c.is_active?'Active':'Hidden'}</span></td>
            <td><button class="btn btn-danger btn-sm" onclick="deleteRecorded(${c.id})">🗑</button></td>
        </tr>
    `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No recorded classes yet.</td></tr>';
}

async function loadLive(page = 1) {
    const res = await api('GET', `/admin/live-classes?page=${page}`);
    const data = res.data?.data || [];
    const statusColors = { scheduled:'badge-info', live:'badge-success', completed:'badge-neutral', cancelled:'badge-danger' };
    document.getElementById('liveBody').innerHTML = data.length ? data.map(c => `
        <tr>
            <td><div style="font-weight:600">${c.title}</div></td>
            <td>${c.chapter?.title||'—'}</td>
            <td>${c.teacher?.name||'—'}</td>
            <td><span class="badge badge-neutral">${c.platform?.replace('_',' ')||'—'}</span></td>
            <td style="font-size:0.8rem">${c.scheduled_at ? new Date(c.scheduled_at).toLocaleString('en-IN') : '—'}</td>
            <td>
                <select class="filter-select" style="font-size:0.75rem;padding:0.3rem 0.5rem"
                    onchange="updateLiveStatus(${c.id}, this.value)">
                    ${['scheduled','live','completed','cancelled'].map(s =>
                        `<option value="${s}" ${c.status===s?'selected':''}>${s}</option>`).join('')}
                </select>
            </td>
            <td><button class="btn btn-danger btn-sm" onclick="deleteLive(${c.id})">🗑</button></td>
        </tr>
    `).join('') : '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">No live classes yet.</td></tr>';
}

async function loadNotes() {
    const res = await api('GET', '/admin/notes');
    const data = res.data?.data || [];
    document.getElementById('notesBody').innerHTML = data.length ? data.map(n => `
        <tr>
            <td style="font-weight:600">${n.title}</td>
            <td>${n.chapter?.title||'—'}</td>
            <td>${n.teacher?.name||'—'}</td>
            <td><span class="badge badge-info">${n.file_type}</span></td>
            <td><span class="badge ${n.is_active?'badge-success':'badge-neutral'}">${n.is_active?'Active':'Hidden'}</span></td>
            <td><button class="btn btn-danger btn-sm" onclick="deleteNote(${n.id})">🗑</button></td>
        </tr>
    `).join('') : '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No notes yet.</td></tr>';
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
        toast('Recorded class added!', 'success'); closeModal('addRecordedModal'); loadRecorded();
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
        });
        toast('Live class scheduled!', 'success'); closeModal('addLiveModal'); loadLive();
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
        toast('Note added!', 'success'); closeModal('addNoteModal'); loadNotes();
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteRecorded(id) { if(confirm('Delete this class?')) { await api('DELETE',`/admin/recorded-classes/${id}`); toast('Deleted.','success'); loadRecorded(); } }
async function deleteLive(id) { if(confirm('Delete this class?')) { await api('DELETE',`/admin/live-classes/${id}`); toast('Deleted.','success'); loadLive(); } }
async function deleteNote(id) { if(confirm('Delete this note?')) { await api('DELETE',`/admin/notes/${id}`); toast('Deleted.','success'); loadNotes(); } }
async function updateLiveStatus(id, status) {
    try { await api('PATCH', `/admin/live-classes/${id}/status`, { status }); toast('Status updated!','success'); }
    catch(e) { toast(e.message,'error'); }
}

window._pageInit = async function() {
    await loadDropdowns();
    loadRecorded(); loadLive(); loadNotes();
};
</script>
@endsection
