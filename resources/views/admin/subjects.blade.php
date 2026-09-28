@extends('layouts.admin')
@section('title','Classes & Subjects')
@section('page-title','Classes & Subjects')
@section('page-subtitle','Curriculum structure: Class → Subject → Chapter')
@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="openModal('addSubjectModal')">＋ New Subject</button>
@endsection

@section('content')

<!-- Mobile Class Selector Strip -->
<div class="mobile-class-strip" style="margin-bottom:1rem">
    <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.4rem">Select Class:</div>
    <div style="display:flex;gap:0.4rem;overflow-x:auto;padding-bottom:0.4rem;-webkit-overflow-scrolling:touch">
        @foreach(range(1, 12) as $c)
        <button
            id="m-cls-btn-{{ $c }}"
            onclick="selectClass({{ $c }})"
            class="m-cls-chip {{ $c === 9 ? 'active' : '' }}"
            style="padding:0.45rem 0.85rem;border-radius:10px;border:1px solid var(--border-light);background:#fff;font-size:0.82rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:all 0.2s">
            Class {{ $c }}
        </button>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     CURRICULUM PANELS LAYOUT
     Desktop: 3-column split
     Tablet & Mobile: 2-column or stacked
═══════════════════════════════════════════════ --}}
<div class="curriculum-grid">

    {{-- ════ PANEL 1: CLASSES (Desktop sidebar) ════ --}}
    <div class="card desktop-class-card" style="overflow:hidden;display:flex;flex-direction:column">
        <div class="card-header" style="padding:0.85rem 1rem">
            <span class="card-title" style="font-size:0.82rem">🏫 Classes</span>
        </div>
        <div style="flex:1;overflow-y:auto;padding:0.5rem">
            @foreach(range(1, 12) as $c)
            <button
                id="cls-btn-{{ $c }}"
                onclick="selectClass({{ $c }})"
                class="class-select-btn {{ $c === 9 ? 'cls-btn-active' : '' }}"
                style="width:100%;text-align:left;padding:0.6rem 0.75rem;border-radius:10px;border:none;background:transparent;cursor:pointer;font-family:'Inter',sans-serif;font-size:0.85rem;font-weight:600;color:var(--text-secondary);transition:all 0.18s;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.15rem">
                <span class="cls-num" style="width:24px;height:24px;border-radius:7px;background:rgba(91,69,255,0.08);color:var(--primary);font-size:0.75rem;font-weight:800;display:flex;align-items:center;justify-content:center">{{ $c }}</span>
                Class {{ $c }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ════ PANEL 2: SUBJECTS ════ --}}
    <div class="card" style="overflow:hidden;display:flex;flex-direction:column;min-height:300px">
        <div class="card-header" style="padding:0.85rem 1rem;gap:0.5rem">
            <span class="card-title" style="font-size:0.85rem" id="subjPanelTitle">📚 Class 9 Subjects</span>
            <button class="btn btn-primary btn-sm" id="assignSubjBtn" style="font-size:0.75rem;padding:0.25rem 0.6rem" onclick="openAssignModal()">＋ Assign</button>
        </div>
        <div style="flex:1;overflow-y:auto;padding:0.65rem" id="subjectPanel">
            <div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>
        </div>
    </div>

    {{-- ════ PANEL 3: CHAPTERS ════ --}}
    <div class="card" style="overflow:hidden;display:flex;flex-direction:column;min-height:350px">
        <div class="card-header" style="padding:0.85rem 1.15rem">
            <div>
                <div class="card-title" id="chapterPanelTitle">📖 Chapters</div>
                <div id="chapterPanelCrumb" style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem"></div>
            </div>
            <button class="btn btn-primary btn-sm" id="addChapterBtn" onclick="openAddChapter()" style="display:none">＋ Add Chapter</button>
        </div>

        {{-- Class Level filter tabs --}}
        <div id="classLevelTabs" style="display:none;padding:0.35rem 1rem 0;border-bottom:1px solid var(--border-light);overflow-x:auto;white-space:nowrap;-webkit-overflow-scrolling:touch">
            <button class="tab-btn active" data-cl="all" onclick="filterChaptersByClass('all',this)">All</button>
            @foreach(range(1,12) as $c)
            <button class="tab-btn" data-cl="{{ $c }}" onclick="filterChaptersByClass({{ $c }},this)">Cls {{ $c }}</button>
            @endforeach
        </div>

        <div style="flex:1;overflow-y:auto" id="chapterPanel">
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <p>Select a subject to view and manage its chapters</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════ --}}

{{-- Assign Subject Modal --}}
<div class="modal-backdrop" id="assignModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <span class="modal-title">📚 Assign Subject to <span id="assignClassLabel">Class</span></span>
            <button class="modal-close" onclick="closeModal('assignModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Choose Subject</label>
                <select class="form-control bn-font" id="assignSubjectSelect">
                    <option value="">— Select a subject —</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('assignModal')">Cancel</button>
            <button class="btn btn-primary" onclick="assignSubject()">Assign Subject</button>
        </div>
    </div>
</div>

{{-- Add / Edit Chapter Modal --}}
<div class="modal-backdrop" id="chapterModal">
    <div class="modal" style="max-width:540px">
        <div class="modal-header">
            <span class="modal-title" id="chapterModalTitle">Add Chapter</span>
            <button class="modal-close" onclick="closeModal('chapterModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="c-id">
            <input type="hidden" id="c-subject-id">

            {{-- Class Level Chip selector --}}
            <div class="form-group">
                <label class="form-label">Applies to Class</label>
                <div style="display:flex;flex-wrap:wrap;gap:0.35rem" id="classChips">
                    <button type="button" class="chip active" data-val="" onclick="pickClassChip(this)">All Classes</button>
                    @foreach(range(1,12) as $c)
                    <button type="button" class="chip" data-val="{{ $c }}" onclick="pickClassChip(this)">Class {{ $c }}</button>
                    @endforeach
                </div>
                <input type="hidden" id="c-class-level" value="">
            </div>

            <div class="form-group">
                <label class="form-label">Chapter Title (Dual Bengali / English) *</label>
                <input type="text" class="form-control bn-font" id="c-title" placeholder="e.g. Chapter 1 — প্রথম অধ্যায়" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Thumbnail Image URL</label>
                    <input type="url" class="form-control" id="c-image" placeholder="https://… or image url">
                </div>
                <div class="form-group">
                    <label class="form-label">Theme Color</label>
                    <input type="color" class="form-control" id="c-color" value="#5B45FF" style="height:42px;cursor:pointer">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tag / Theme Description</label>
                    <input type="text" class="form-control" id="c-theme" placeholder="e.g. Mechanics, Grammar, Organic">
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order *</label>
                    <input type="number" class="form-control" id="c-sort" value="1" min="1">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" id="c-active">
                    <option value="1">Active (Visible to Students)</option>
                    <option value="0">Hidden (Draft)</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('chapterModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveChapter()">Save Chapter</button>
        </div>
    </div>
</div>

{{-- Add New Subject (Global) Modal --}}
<div class="modal-backdrop" id="addSubjectModal">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <span class="modal-title">➕ Create Master Subject</span>
            <button class="modal-close" onclick="closeModal('addSubjectModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Subject Name (Bangla + English) *</label>
                    <input type="text" class="form-control bn-font" id="s-name" placeholder="e.g. ইতিহাস (History)" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Color Theme</label>
                    <input type="color" class="form-control" id="s-color" value="#5B45FF" style="height:42px;cursor:pointer">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Icon URL / Emoji</label>
                    <input type="text" class="form-control" id="s-icon" placeholder="https://img.icons8.com/…">
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="s-sort" value="1" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Cover Image URL (Optional)</label>
                <input type="url" class="form-control" id="s-image" placeholder="https://…">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addSubjectModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createSubject()">Create Subject</button>
        </div>
    </div>
</div>

{{-- Edit Subject Modal --}}
<div class="modal-backdrop" id="editSubjectModal">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Subject</span>
            <button class="modal-close" onclick="closeModal('editSubjectModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="es-id">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Subject Name (Bangla + English)</label>
                    <input type="text" class="form-control bn-font" id="es-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Color Theme</label>
                    <input type="color" class="form-control" id="es-color" style="height:42px;cursor:pointer">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Icon URL</label>
                    <input type="text" class="form-control" id="es-icon">
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="es-sort">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Cover Image URL</label>
                <input type="url" class="form-control" id="es-image">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editSubjectModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateSubject()">Save Changes</button>
        </div>
    </div>
</div>

<style>
.curriculum-grid {
    display: grid;
    grid-template-columns: 170px 260px 1fr;
    gap: 1rem;
    min-height: calc(100vh - 10rem);
}

.mobile-class-strip { display: none; }

@media (max-width: 1024px) {
    .curriculum-grid { grid-template-columns: 240px 1fr; }
    .desktop-class-card { display: none !important; }
    .mobile-class-strip { display: block; }
}

@media (max-width: 768px) {
    .curriculum-grid { grid-template-columns: 1fr; }
}

.cls-btn-active {
    background: rgba(91,69,255,0.1) !important;
    color: var(--primary) !important;
    font-weight: 700 !important;
}
.cls-btn-active .cls-num {
    background: var(--primary) !important;
    color: #fff !important;
}

.m-cls-chip.active {
    background: var(--primary) !important;
    color: #fff !important;
    border-color: var(--primary) !important;
}

.subj-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.75rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.18s;
    margin-bottom: 0.35rem;
    border: 1.5px solid transparent;
    background: #fff;
}
.subj-item:hover { background: rgba(91,69,255,0.04); border-color: rgba(91,69,255,0.15); }
.subj-item.active { background: rgba(91,69,255,0.08); border-color: rgba(91,69,255,0.3); }
.subj-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.subj-name { font-size: 0.86rem; font-weight: 700; color: var(--text); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.subj-actions { display: flex; gap: 0.25rem; }

.chip {
    padding: 0.32rem 0.65rem;
    border-radius: 8px;
    border: 1.5px solid rgba(0,0,0,0.1);
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.74rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    font-family: 'Inter', sans-serif;
}
.chip:hover { border-color: var(--primary); color: var(--primary); }
.chip.active { background: rgba(91,69,255,0.12); border-color: var(--primary); color: var(--primary); }

.cls-badge {
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 800;
    background: rgba(91,69,255,0.1);
    color: var(--primary);
    border: 1px solid rgba(91,69,255,0.2);
}
</style>
@endsection

@section('scripts')
<script>
let selectedClass   = 9;
let selectedSubject = null;
let allSubjectData  = {};
let allChapters     = [];

async function selectClass(cls) {
    selectedClass   = cls;
    selectedSubject = null;

    // Desktop class buttons
    document.querySelectorAll('.class-select-btn').forEach(b => {
        b.classList.remove('cls-btn-active');
        const num = b.querySelector('.cls-num');
        if (num) { num.style.background = 'rgba(91,69,255,0.08)'; num.style.color = 'var(--primary)'; }
    });
    const activeBtn = document.getElementById('cls-btn-' + cls);
    if (activeBtn) {
        activeBtn.classList.add('cls-btn-active');
        const num = activeBtn.querySelector('.cls-num');
        if (num) { num.style.background = 'var(--primary)'; num.style.color = '#fff'; }
    }

    // Mobile chips
    document.querySelectorAll('.m-cls-chip').forEach(b => b.classList.remove('active'));
    const activeChip = document.getElementById('m-cls-btn-' + cls);
    if (activeChip) activeChip.classList.add('active');

    document.getElementById('subjPanelTitle').textContent = `📚 Class ${cls} Subjects`;
    document.getElementById('assignSubjBtn').style.display = 'inline-flex';
    document.getElementById('chapterPanelTitle').textContent = '📖 Chapters';
    document.getElementById('chapterPanelCrumb').textContent = '';
    document.getElementById('addChapterBtn').style.display = 'none';
    document.getElementById('classLevelTabs').style.display = 'none';
    document.getElementById('chapterPanel').innerHTML = '<div class="empty-state"><div class="empty-icon">📚</div><p>Select a subject to view its chapters</p></div>';

    await loadSubjectsForClass(cls);
}

async function loadSubjectsForClass(cls) {
    document.getElementById('subjectPanel').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';
    try {
        const res = await api('GET', `/admin/class-subjects?class_level=${cls}`);
        allSubjectData[cls] = res.data || {};
        renderSubjectPanel(res.data?.assigned_subjects || [], cls);
    } catch(e) { toast('Error loading subjects: ' + e.message, 'error'); }
}

function renderSubjectPanel(subjects, cls) {
    if (!subjects.length) {
        document.getElementById('subjectPanel').innerHTML =
            `<div class="empty-state" style="padding:2rem 1rem"><div class="empty-icon" style="font-size:2rem">📭</div>
            <p style="font-size:0.8rem">No subjects assigned to Class ${cls} yet.<br>Click <strong>+ Assign</strong> to map subjects.</p></div>`;
        return;
    }
    document.getElementById('subjectPanel').innerHTML = subjects.map(s => `
        <div class="subj-item" id="si-${s.id}" onclick="selectSubject(${s.id},'${s.name.replace(/'/g,"\\'")}','${s.color}')">
            <span class="subj-dot" style="background:${s.color || '#5B45FF'}"></span>
            <span class="subj-name bn-font">${s.name}</span>
            <div class="subj-actions" onclick="event.stopPropagation()">
                <button class="btn btn-outline btn-sm" style="padding:0.25rem 0.45rem;font-size:0.7rem" onclick="editSubject(JSON.parse(this.dataset.subject))" data-subject='${JSON.stringify(s).replace(/'/g, "&#39;")}' title="Edit Subject">✏️</button>
                <button class="btn btn-danger btn-sm" style="padding:0.25rem 0.45rem;font-size:0.7rem" onclick="removeSubjectFromClass(${s.id})" title="Remove from Class">✕</button>
            </div>
        </div>
    `).join('');
}

async function selectSubject(subjectId, subjectName, color) {
    selectedSubject = { id: subjectId, name: subjectName, color };

    document.querySelectorAll('.subj-item').forEach(el => el.classList.remove('active'));
    const el = document.getElementById('si-' + subjectId);
    if (el) el.classList.add('active');

    document.getElementById('chapterPanelTitle').innerHTML = `📖 <span class="bn-font">${subjectName}</span> Chapters`;
    document.getElementById('chapterPanelCrumb').innerHTML = `Class ${selectedClass} › <span class="bn-font">${subjectName}</span>`;
    document.getElementById('addChapterBtn').style.display = 'inline-flex';
    document.getElementById('classLevelTabs').style.display = 'block';

    document.querySelectorAll('[data-cl]').forEach(b => b.classList.remove('active'));
    const allClBtn = document.querySelector('[data-cl="all"]');
    if (allClBtn) allClBtn.classList.add('active');

    await loadChapters(selectedClass, subjectId);
}

async function loadChapters(cls, subjectId) {
    document.getElementById('chapterPanel').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div></div>';
    try {
        const res = await api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${subjectId}`);
        allChapters = res.data || [];
        renderChaptersTable(allChapters);
    } catch(e) { toast('Error loading chapters: ' + e.message, 'error'); }
}

function filterChaptersByClass(clsVal, btn) {
    document.querySelectorAll('[data-cl]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filtered = clsVal === 'all'
        ? allChapters
        : allChapters.filter(c => c.class_level == clsVal || c.class_level === null);
    renderChaptersTable(filtered);
}

function renderChaptersTable(chapters) {
    if (!chapters.length) {
        document.getElementById('chapterPanel').innerHTML =
            '<div class="empty-state"><div class="empty-icon">📖</div><p>No chapters found for this subject. Click <strong>+ Add Chapter</strong> to create one.</p></div>';
        return;
    }
    const rows = chapters.map(c => {
        const imgHtml = c.image 
            ? `<img src="${c.image}" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid var(--border-light)">` 
            : `<div style="width:36px;height:36px;border-radius:8px;background:rgba(91,69,255,0.08);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:0.95rem">📖</div>`;
        
        return `<tr>
            <td style="color:var(--text-muted);width:35px;font-weight:700">#${c.sort_order}</td>
            <td style="width:45px">${imgHtml}</td>
            <td>
                <div style="font-weight:700;font-size:0.88rem" class="bn-font">${c.title}</div>
                ${c.theme ? `<span style="font-size:0.7rem;color:var(--text-muted);background:rgba(0,0,0,0.05);padding:0.1rem 0.4rem;border-radius:4px;display:inline-block;margin-top:0.2rem">${c.theme}</span>` : ''}
            </td>
            <td>${c.class_level ? `<span class="cls-badge">Class ${c.class_level}</span>` : `<span class="badge badge-neutral">All Classes</span>`}</td>
            <td><span class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}">${c.is_active ? 'Active' : 'Hidden'}</span></td>
            <td>
                <div style="display:flex;gap:0.35rem;flex-wrap:wrap">
                    <a href="{{ route('admin.content') }}?class=${selectedClass}&subject=${selectedSubject?.id}&chapter=${c.id}" class="btn btn-outline btn-sm" title="Manage Videos & Notes">🎬 Content</a>
                    <button class="btn btn-outline btn-sm" onclick="editChapter(JSON.parse(this.dataset.chapter))" data-chapter='${JSON.stringify(c).replace(/'/g, "&#39;")}' title="Edit Chapter">✏️</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteChapter(${c.id})" title="Delete Chapter">🗑</button>
                </div>
            </td>
        </tr>`;
    }).join('');

    document.getElementById('chapterPanel').innerHTML = `
        <div class="table-wrapper">
            <table>
                <thead><tr>
                    <th>#</th><th>Thumb</th><th>Chapter Title & Tag</th><th>Class</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

async function openAssignModal() {
    document.getElementById('assignClassLabel').textContent = `Class ${selectedClass}`;
    const data = allSubjectData[selectedClass];
    const all  = data?.all_subjects || [];
    const assignedIds = (data?.assigned_subjects || []).map(s => s.id);
    const unassigned  = all.filter(s => !assignedIds.includes(s.id));

    const select = document.getElementById('assignSubjectSelect');
    select.innerHTML = '<option value="">— Select a subject —</option>' +
        unassigned.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    openModal('assignModal');
}

async function assignSubject() {
    const subjectId = parseInt(document.getElementById('assignSubjectSelect').value);
    if (!subjectId) { toast('Please select a subject to assign.', 'error'); return; }
    try {
        await api('POST', '/admin/class-subjects', { class_level: selectedClass, subject_id: subjectId });
        toast(`Subject assigned to Class ${selectedClass}!`, 'success');
        closeModal('assignModal');
        await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

async function removeSubjectFromClass(subjectId) {
    if (!confirm(`Remove this subject from Class ${selectedClass}? (Associated chapters and content will not be deleted).`)) return;
    try {
        await api('DELETE', `/admin/class-subjects/${selectedClass}/${subjectId}`);
        toast('Subject unassigned from class.', 'success');
        await loadSubjectsForClass(selectedClass);
        if (selectedSubject?.id === subjectId) {
            selectedSubject = null;
            document.getElementById('chapterPanel').innerHTML = '<div class="empty-state"><div class="empty-icon">📚</div><p>Select a subject to view chapters</p></div>';
            document.getElementById('addChapterBtn').style.display = 'none';
            document.getElementById('classLevelTabs').style.display = 'none';
        }
    } catch(e) { toast(e.message, 'error'); }
}

async function createSubject() {
    const name = document.getElementById('s-name').value.trim();
    if (!name) { toast('Please enter a subject name.', 'error'); return; }
    try {
        await api('POST', '/admin/subjects', {
            name,
            color: document.getElementById('s-color').value,
            icon: document.getElementById('s-icon').value || 'https://img.icons8.com/fluency/96/book.png',
            image: document.getElementById('s-image').value || null,
            sort_order: parseInt(document.getElementById('s-sort').value) || 1,
        });
        toast('Subject created! You can now assign it to classes.', 'success');
        closeModal('addSubjectModal');
        document.getElementById('s-name').value = '';
        if (selectedClass) await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

function editSubject(s) {
    document.getElementById('es-id').value    = s.id;
    document.getElementById('es-name').value  = s.name;
    document.getElementById('es-color').value = s.color || '#5B45FF';
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
            sort_order: parseInt(document.getElementById('es-sort').value) || 1,
        });
        toast('Subject updated!', 'success');
        closeModal('editSubjectModal');
        if (selectedClass) await loadSubjectsForClass(selectedClass);
    } catch(e) { toast(e.message, 'error'); }
}

function pickClassChip(btn) {
    document.querySelectorAll('#classChips .chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('c-class-level').value = btn.dataset.val;
}

function openAddChapter() {
    document.getElementById('c-id').value = '';
    document.getElementById('c-title').value = '';
    document.getElementById('c-image').value = '';
    document.getElementById('c-color').value = '#5B45FF';
    document.getElementById('c-theme').value = '';
    document.getElementById('c-sort').value = (allChapters.length + 1).toString();
    document.getElementById('c-active').value = '1';
    document.getElementById('c-subject-id').value = selectedSubject?.id || '';

    document.querySelectorAll('#classChips .chip').forEach(b => b.classList.remove('active'));
    const chipForClass = document.querySelector(`#classChips .chip[data-val="${selectedClass}"]`);
    if (chipForClass) { 
        chipForClass.classList.add('active'); 
        document.getElementById('c-class-level').value = selectedClass; 
    } else { 
        document.querySelector('#classChips .chip[data-val=""]')?.classList.add('active'); 
        document.getElementById('c-class-level').value = ''; 
    }
    document.getElementById('chapterModalTitle').textContent = 'Add Chapter';
    openModal('chapterModal');
}

function editChapter(c) {
    document.getElementById('c-id').value = c.id;
    document.getElementById('c-title').value = c.title;
    document.getElementById('c-image').value = c.image || '';
    document.getElementById('c-color').value = c.color || '#5B45FF';
    document.getElementById('c-theme').value = c.theme || '';
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
    const title      = document.getElementById('c-title').value.trim();

    if (!title) { toast('Please enter chapter title.', 'error'); return; }

    const body = {
        subject_id:  parseInt(document.getElementById('c-subject-id').value),
        class_level: classLevel ? parseInt(classLevel) : null,
        title,
        image:       document.getElementById('c-image').value || null,
        color:       document.getElementById('c-color').value || null,
        theme:       document.getElementById('c-theme').value || null,
        sort_order:  parseInt(document.getElementById('c-sort').value) || 1,
        is_active:   document.getElementById('c-active').value === '1',
    };

    try {
        if (id) await api('PUT',  `/admin/chapters/${id}`, body);
        else    await api('POST', '/admin/chapters', body);
        toast(id ? 'Chapter updated!' : 'Chapter created successfully!', 'success');
        closeModal('chapterModal');
        if (selectedSubject) await loadChapters(selectedClass, selectedSubject.id);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteChapter(id) {
    if (!confirm('Delete this chapter and all its recorded classes, live sessions, notes, and tests?')) return;
    try {
        await api('DELETE', `/admin/chapters/${id}`);
        toast('Chapter deleted.', 'success');
        if (selectedSubject) await loadChapters(selectedClass, selectedSubject.id);
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = () => selectClass(9);
</script>
@endsection
