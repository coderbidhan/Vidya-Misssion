@extends('layouts.admin')
@section('title','Classes & Subjects')
@section('page-title','Classes & Subjects')
@section('page-subtitle','Manage curriculum: Class → Subject → Chapter')
@section('topbar-actions')
    <button class="btn btn-outline btn-sm" onclick="openModal('addSubjectModal')">＋ New Subject</button>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════
     THREE-PANEL LAYOUT
     Panel 1 (fixed): Class list 1–12
     Panel 2 (fixed): Subjects for selected class
     Panel 3 (flex):  Chapters for selected class + subject
═══════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:160px 240px 1fr;gap:1rem;height:calc(100vh - 7rem)">

    {{-- ════ PANEL 1: CLASSES ════ --}}
    <div class="card" style="overflow:hidden;display:flex;flex-direction:column">
        <div class="card-header" style="padding:1rem">
            <span class="card-title" style="font-size:0.8rem">🏫 Classes</span>
        </div>
        <div style="flex:1;overflow-y:auto;padding:0.5rem">
            @foreach(range(1, 12) as $c)
            <button
                id="cls-btn-{{ $c }}"
                onclick="selectClass({{ $c }})"
                style="width:100%;text-align:left;padding:0.65rem 0.875rem;border-radius:10px;border:none;background:transparent;cursor:pointer;font-family:'Inter',sans-serif;font-size:0.85rem;font-weight:600;color:#374151;transition:all 0.18s;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.15rem">
                <span style="width:20px;height:20px;border-radius:5px;background:rgba(109,74,255,0.08);color:#6D4AFF;font-size:0.65rem;font-weight:800;display:flex;align-items:center;justify-content:center">{{ $c }}</span>
                Class {{ $c }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ════ PANEL 2: SUBJECTS ════ --}}
    <div class="card" style="overflow:hidden;display:flex;flex-direction:column">
        <div class="card-header" style="padding:1rem;gap:0.5rem">
            <span class="card-title" style="font-size:0.8rem" id="subjPanelTitle">📚 Subjects</span>
            <button class="btn btn-primary btn-sm" id="assignSubjBtn" style="display:none;font-size:0.72rem;padding:0.28rem 0.6rem" onclick="openAssignModal()">＋ Assign</button>
        </div>
        <div style="flex:1;overflow-y:auto;padding:0.5rem" id="subjectPanel">
            <div class="empty-state" style="padding:2rem 1rem">
                <div class="empty-icon" style="font-size:2rem">🏫</div>
                <p style="font-size:0.78rem">Select a class</p>
            </div>
        </div>
    </div>

    {{-- ════ PANEL 3: CHAPTERS ════ --}}
    <div class="card" style="overflow:hidden;display:flex;flex-direction:column">
        <div class="card-header" style="padding:1rem 1.25rem">
            <div>
                <div class="card-title" id="chapterPanelTitle">📖 Chapters</div>
                <div id="chapterPanelCrumb" style="font-size:0.72rem;color:#6B7280;margin-top:0.15rem"></div>
            </div>
            <button class="btn btn-primary btn-sm" id="addChapterBtn" onclick="openAddChapter()" style="display:none">＋ Add Chapter</button>
        </div>

        {{-- Class Level filter inside chapters panel --}}
        <div id="classLevelTabs" style="display:none;padding:0 1.25rem;border-bottom:2px solid rgba(0,0,0,0.07);overflow-x:auto;white-space:nowrap">
            <button class="tab-btn active" data-cl="all" onclick="filterChaptersByClass('all',this)">All</button>
            @foreach(range(1,12) as $c)
            <button class="tab-btn" data-cl="{{ $c }}" onclick="filterChaptersByClass({{ $c }},this)">Cls {{ $c }}</button>
            @endforeach
        </div>

        <div style="flex:1;overflow-y:auto" id="chapterPanel">
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <p>Select a class, then a subject to see chapters</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════ --}}

{{-- Assign subject to class --}}
<div class="modal-backdrop" id="assignModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <span class="modal-title">📚 Assign Subject to <span id="assignClassLabel">Class ?</span></span>
            <button class="modal-close" onclick="closeModal('assignModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Select Subject</label>
                <select class="form-control" id="assignSubjectSelect">
                    <option value="">— choose a subject —</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('assignModal')">Cancel</button>
            <button class="btn btn-primary" onclick="assignSubject()">Assign Subject</button>
        </div>
    </div>
</div>

{{-- Add / Edit Chapter --}}
<div class="modal-backdrop" id="chapterModal">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <span class="modal-title" id="chapterModalTitle">Add Chapter</span>
            <button class="modal-close" onclick="closeModal('chapterModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="c-id">
            <input type="hidden" id="c-subject-id">

            {{-- Class Level chip picker --}}
            <div class="form-group">
                <label class="form-label">Applies to Class</label>
                <div style="display:flex;flex-wrap:wrap;gap:0.4rem" id="classChips">
                    <button type="button" class="chip active" data-val="" onclick="pickClassChip(this)">All Classes</button>
                    @foreach(range(1,12) as $c)
                    <button type="button" class="chip" data-val="{{ $c }}" onclick="pickClassChip(this)">Class {{ $c }}</button>
                    @endforeach
                </div>
                <input type="hidden" id="c-class-level" value="">
            </div>

            <div class="form-group">
                <label class="form-label">Chapter Title *</label>
                <input type="text" class="form-control" id="c-title" placeholder="e.g. Chapter 1 — Introduction">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="c-sort" value="1" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="c-active">
                        <option value="1">Active</option>
                        <option value="0">Hidden</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('chapterModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveChapter()">Save Chapter</button>
        </div>
    </div>
</div>

{{-- Add new Subject (global) --}}
<div class="modal-backdrop" id="addSubjectModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Create Subject</span>
            <button class="modal-close" onclick="closeModal('addSubjectModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Subject Name *</label>
                    <input type="text" class="form-control" id="s-name" placeholder="e.g. Mathematics">
                </div>
                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="color" class="form-control" id="s-color" value="#6D4AFF" style="height:42px;cursor:pointer">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Icon</label>
                    <input type="text" class="form-control" id="s-icon" placeholder="book-math">
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="s-sort" value="1" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Image URL</label>
                <input type="text" class="form-control" id="s-image" placeholder="e.g. https://images.unsplash.com/photo-...">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addSubjectModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createSubject()">Create Subject</button>
        </div>
    </div>
</div>

{{-- Edit Subject --}}
<div class="modal-backdrop" id="editSubjectModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Subject</span>
            <button class="modal-close" onclick="closeModal('editSubjectModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="es-id">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Name</label><input type="text" class="form-control" id="es-name"></div>
                <div class="form-group"><label class="form-label">Color</label><input type="color" class="form-control" id="es-color" style="height:42px;cursor:pointer"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Icon</label><input type="text" class="form-control" id="es-icon"></div>
                <div class="form-group"><label class="form-label">Sort Order</label><input type="number" class="form-control" id="es-sort"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Image URL</label>
                <input type="text" class="form-control" id="es-image" placeholder="e.g. https://images.unsplash.com/photo-...">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editSubjectModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateSubject()">Save Changes</button>
        </div>
    </div>
</div>

<style>
/* Class selector button - active state */
.cls-btn-active {
    background: rgba(109,74,255,0.1) !important;
    color: #6D4AFF !important;
}
.cls-btn-active .cls-num {
    background: #6D4AFF !important;
    color: #fff !important;
}

/* Subject items in panel 2 */
.subj-item {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.6rem 0.75rem; border-radius: 10px;
    cursor: pointer; transition: all 0.18s; margin-bottom: 0.15rem;
    border: 1.5px solid transparent;
}
.subj-item:hover { background: rgba(109,74,255,0.05); }
.subj-item.active { background: rgba(109,74,255,0.1); border-color: rgba(109,74,255,0.2); }
.subj-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.subj-name { font-size: 0.82rem; font-weight: 600; color: #111827; flex: 1; }
.subj-actions { display: flex; gap: 0.2rem; opacity: 0; transition: opacity 0.15s; }
.subj-item:hover .subj-actions { opacity: 1; }

/* Class level chip picker */
.chip {
    padding: 0.28rem 0.65rem; border-radius: 8px;
    border: 1.5px solid rgba(0,0,0,0.1);
    background: transparent; color: #374151;
    font-size: 0.72rem; font-weight: 700;
    cursor: pointer; transition: all 0.15s;
    font-family: 'Inter', sans-serif;
}
.chip:hover { border-color: #6D4AFF; color: #6D4AFF; }
.chip.active { background: rgba(109,74,255,0.12); border-color: #6D4AFF; color: #6D4AFF; }

/* Class level badge in table */
.cls-badge {
    padding: 0.18rem 0.5rem; border-radius: 6px;
    font-size: 0.68rem; font-weight: 800;
    background: rgba(109,74,255,0.1); color: #6D4AFF;
    border: 1px solid rgba(109,74,255,0.2);
}
</style>
@endsection

@section('scripts')
<script>
let selectedClass   = null;
let selectedSubject = null;
let allSubjectData  = {}; // cache: class → { assigned, all }
let allChapters     = []; // raw chapters for current selection

// ─── PANEL 1: Select Class ─────────────────────────────────────
async function selectClass(cls) {
    selectedClass   = cls;
    selectedSubject = null;

    // Update button styles
    document.querySelectorAll('[id^="cls-btn-"]').forEach(b => {
        b.style.background  = 'transparent';
        b.style.color       = '#374151';
        const num = b.querySelector('span');
        if (num) { num.style.background = 'rgba(109,74,255,0.08)'; num.style.color = '#6D4AFF'; }
    });
    const active = document.getElementById('cls-btn-' + cls);
    active.style.background = 'rgba(109,74,255,0.1)';
    active.style.color      = '#6D4AFF';
    const num = active.querySelector('span');
    if (num) { num.style.background = '#6D4AFF'; num.style.color = '#fff'; }

    document.getElementById('subjPanelTitle').textContent = `📚 Class ${cls} Subjects`;
    document.getElementById('assignSubjBtn').style.display = 'flex';
    document.getElementById('chapterPanelTitle').textContent = '📖 Chapters';
    document.getElementById('chapterPanelCrumb').textContent = '';
    document.getElementById('addChapterBtn').style.display = 'none';
    document.getElementById('classLevelTabs').style.display = 'none';
    document.getElementById('chapterPanel').innerHTML = '<div class="empty-state"><div class="empty-icon">📚</div><p>Select a subject to see chapters</p></div>';

    await loadSubjectsForClass(cls);
}

// ─── Load subjects for a class ─────────────────────────────────
async function loadSubjectsForClass(cls) {
    document.getElementById('subjectPanel').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';
    try {
        const res = await api('GET', `/admin/class-subjects?class_level=${cls}`);
        allSubjectData[cls] = res.data;
        renderSubjectPanel(res.data.assigned_subjects, cls);
    } catch(e) { toast('Error loading subjects: ' + e.message, 'error'); }
}

// ─── Render subject list in panel 2 ───────────────────────────
function renderSubjectPanel(subjects, cls) {
    if (!subjects.length) {
        document.getElementById('subjectPanel').innerHTML =
            `<div class="empty-state" style="padding:1.5rem 1rem"><div class="empty-icon" style="font-size:1.75rem">📭</div>
            <p style="font-size:0.78rem">No subjects assigned to Class ${cls} yet.<br>Click + Assign to add.</p></div>`;
        return;
    }
    document.getElementById('subjectPanel').innerHTML = subjects.map(s => `
        <div class="subj-item" id="si-${s.id}" onclick="selectSubject(${s.id},'${s.name.replace(/'/g,"\\'")}','${s.color}')">
            <span class="subj-dot" style="background:${s.color}"></span>
            <span class="subj-name">${s.name}</span>
            <div class="subj-actions">
                <button class="btn btn-outline btn-sm" style="padding:0.2rem 0.4rem;font-size:0.65rem" onclick="event.stopPropagation();editSubject(JSON.parse(this.dataset.subject))" data-subject='${JSON.stringify(s).replace(/'/g, "&#39;")}'>✏️</button>
                <button class="btn btn-danger btn-sm" style="padding:0.2rem 0.4rem;font-size:0.65rem" onclick="event.stopPropagation();removeSubjectFromClass(${s.id})">✕</button>
            </div>
        </div>
    `).join('');
}

// ─── PANEL 2: Select Subject ───────────────────────────────────
async function selectSubject(subjectId, subjectName, color) {
    selectedSubject = { id: subjectId, name: subjectName, color };

    document.querySelectorAll('.subj-item').forEach(el => el.classList.remove('active'));
    const el = document.getElementById('si-' + subjectId);
    if (el) el.classList.add('active');

    document.getElementById('chapterPanelTitle').textContent = `📖 ${subjectName} — Chapters`;
    document.getElementById('chapterPanelCrumb').textContent = `Class ${selectedClass} › ${subjectName}`;
    document.getElementById('addChapterBtn').style.display = 'flex';
    document.getElementById('classLevelTabs').style.display = 'block';

    // Reset to "All" tab
    document.querySelectorAll('[data-cl]').forEach(b => b.classList.remove('active'));
    document.querySelector('[data-cl="all"]').classList.add('active');

    await loadChapters(selectedClass, subjectId);
}

// ─── Load chapters ─────────────────────────────────────────────
async function loadChapters(cls, subjectId) {
    document.getElementById('chapterPanel').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';
    try {
        const res = await api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${subjectId}`);
        allChapters = res.data || [];
        renderChaptersTable(allChapters);
    } catch(e) { toast('Error: ' + e.message, 'error'); }
}

// ─── Filter chapters by class level tab ────────────────────────
function filterChaptersByClass(clsVal, btn) {
    document.querySelectorAll('[data-cl]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filtered = clsVal === 'all'
        ? allChapters
        : allChapters.filter(c => c.class_level === clsVal || c.class_level === null);
    renderChaptersTable(filtered);
}

// ─── Render chapter table ──────────────────────────────────────
function renderChaptersTable(chapters) {
    if (!chapters.length) {
        document.getElementById('chapterPanel').innerHTML =
            '<div class="empty-state"><div class="empty-icon">📖</div><p>No chapters yet. Click + Add Chapter.</p></div>';
        return;
    }
    const rows = chapters.map(c => `<tr>
        <td style="color:#9CA3AF;width:40px">#${c.sort_order}</td>
        <td>
            <div style="font-weight:600;font-size:0.875rem">${c.title}</div>
        </td>
        <td>${c.class_level
            ? `<span class="cls-badge">Class ${c.class_level}</span>`
            : `<span style="font-size:0.7rem;color:#9CA3AF">All</span>`}</td>
        <td><span class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}">${c.is_active ? 'Active' : 'Hidden'}</span></td>
        <td>
            <button class="btn btn-outline btn-sm" onclick="editChapter(JSON.parse(this.dataset.chapter))" data-chapter='${JSON.stringify(c).replace(/'/g, "&#39;")}'> ✏️</button>
            <button class="btn btn-danger btn-sm" onclick="deleteChapter(${c.id})">🗑</button>
        </td>
    </tr>`).join('');

    document.getElementById('chapterPanel').innerHTML = `
        <div class="table-wrapper">
            <table>
                <thead><tr>
                    <th>#</th><th>Title</th><th>Class</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

// ─── Assign Subject to Class ───────────────────────────────────
async function openAssignModal() {
    document.getElementById('assignClassLabel').textContent = `Class ${selectedClass}`;
    // Populate dropdown with unassigned subjects
    const data = allSubjectData[selectedClass];
    const all  = data?.all_subjects || [];
    const assignedIds = (data?.assigned_subjects || []).map(s => s.id);
    const unassigned  = all.filter(s => !assignedIds.includes(s.id));

    const select = document.getElementById('assignSubjectSelect');
    select.innerHTML = '<option value="">— choose a subject —</option>' +
        unassigned.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    openModal('assignModal');
}

async function assignSubject() {
    const subjectId = parseInt(document.getElementById('assignSubjectSelect').value);
    if (!subjectId) { toast('Please select a subject', 'error'); return; }
    try {
        await api('POST', '/admin/class-subjects', { class_level: selectedClass, subject_id: subjectId });
        toast('Subject assigned to Class ' + selectedClass, 'success');
        closeModal('assignModal');
        await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

async function removeSubjectFromClass(subjectId) {
    if (!confirm(`Remove this subject from Class ${selectedClass}? (Chapters are NOT deleted.)`)) return;
    try {
        await api('DELETE', `/admin/class-subjects/${selectedClass}/${subjectId}`);
        toast('Subject removed from class.', 'success');
        await loadSubjectsForClass(selectedClass);
        // Reset chapter panel
        if (selectedSubject?.id === subjectId) {
            selectedSubject = null;
            document.getElementById('chapterPanel').innerHTML = '<div class="empty-state"><div class="empty-icon">📚</div><p>Select a subject</p></div>';
            document.getElementById('addChapterBtn').style.display = 'none';
        }
    } catch(e) { toast(e.message, 'error'); }
}

// ─── Subject CRUD ──────────────────────────────────────────────
async function createSubject() {
    try {
        await api('POST', '/admin/subjects', {
            name: document.getElementById('s-name').value,
            color: document.getElementById('s-color').value,
            icon: document.getElementById('s-icon').value || 'book',
            image: document.getElementById('s-image').value,
            sort_order: parseInt(document.getElementById('s-sort').value),
        });
        toast('Subject created! Now assign it to classes via + Assign.', 'success');
        closeModal('addSubjectModal');
        if (selectedClass) await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

function editSubject(s) {
    document.getElementById('es-id').value    = s.id;
    document.getElementById('es-name').value  = s.name;
    document.getElementById('es-color').value = s.color;
    document.getElementById('es-icon').value  = s.icon || '';
    document.getElementById('es-image').value = s.image || '';
    document.getElementById('es-sort').value  = s.sort_order || 1;
    openModal('editSubjectModal');
}

async function updateSubject() {
    const id = document.getElementById('es-id').value;
    try {
        await api('PUT', `/admin/subjects/${id}`, {
            name: document.getElementById('es-name').value,
            color: document.getElementById('es-color').value,
            icon: document.getElementById('es-icon').value,
            image: document.getElementById('es-image').value,
            sort_order: parseInt(document.getElementById('es-sort').value),
        });
        toast('Subject updated!', 'success');
        closeModal('editSubjectModal');
        if (selectedClass) await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

// ─── Class chip picker ─────────────────────────────────────────
function pickClassChip(btn) {
    document.querySelectorAll('#classChips .chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('c-class-level').value = btn.dataset.val;
}

// ─── Chapter CRUD ──────────────────────────────────────────────
function openAddChapter() {
    document.getElementById('c-id').value = '';
    document.getElementById('c-title').value = '';
    document.getElementById('c-sort').value = (allChapters.length + 1).toString();
    document.getElementById('c-active').value = '1';
    document.getElementById('c-subject-id').value = selectedSubject?.id || '';
    // Pre-select current class
    document.querySelectorAll('#classChips .chip').forEach(b => b.classList.remove('active'));
    const chipForClass = document.querySelector(`#classChips .chip[data-val="${selectedClass}"]`);
    if (chipForClass) { chipForClass.classList.add('active'); document.getElementById('c-class-level').value = selectedClass; }
    else { document.querySelector('#classChips .chip[data-val=""]').classList.add('active'); document.getElementById('c-class-level').value = ''; }
    document.getElementById('chapterModalTitle').textContent = 'Add Chapter';
    openModal('chapterModal');
}

function editChapter(c) {
    document.getElementById('c-id').value = c.id;
    document.getElementById('c-title').value = c.title;
    document.getElementById('c-sort').value = c.sort_order || 1;
    document.getElementById('c-active').value = c.is_active ? '1' : '0';
    document.getElementById('c-subject-id').value = c.subject_id;
    const clVal = c.class_level ? c.class_level.toString() : '';
    document.querySelectorAll('#classChips .chip').forEach(b => b.classList.remove('active'));
    const chip = document.querySelector(`#classChips .chip[data-val="${clVal}"]`);
    if (chip) chip.classList.add('active');
    document.getElementById('c-class-level').value = clVal;
    document.getElementById('chapterModalTitle').textContent = 'Edit Chapter';
    openModal('chapterModal');
}

async function saveChapter() {
    const id         = document.getElementById('c-id').value;
    const classLevel = document.getElementById('c-class-level').value;
    const body = {
        subject_id:  parseInt(document.getElementById('c-subject-id').value),
        class_level: classLevel ? parseInt(classLevel) : null,
        title:       document.getElementById('c-title').value,
        sort_order:  parseInt(document.getElementById('c-sort').value) || 1,
        is_active:   document.getElementById('c-active').value === '1',
    };
    try {
        if (id) await api('PUT',  `/admin/chapters/${id}`, body);
        else    await api('POST', '/admin/chapters', body);
        toast(id ? 'Chapter updated!' : 'Chapter added!', 'success');
        closeModal('chapterModal');
        await loadChapters(selectedClass, selectedSubject.id);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteChapter(id) {
    if (!confirm('Delete this chapter and all its content?')) return;
    try {
        await api('DELETE', `/admin/chapters/${id}`);
        toast('Chapter deleted.', 'success');
        await loadChapters(selectedClass, selectedSubject.id);
    } catch(e) { toast(e.message, 'error'); }
}

// ─── Auto-select Class 8 on load ──────────────────────────────
window._pageInit = () => selectClass(9); // default to class 9
</script>
@endsection
