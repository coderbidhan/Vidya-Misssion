@extends('layouts.admin')
@section('title','Class Management')
@section('page-title','Class Management')
@section('page-subtitle','Create and organize academic classes, curriculum boards, and grades')
@section('topbar-actions')
    <span id="classCountBadge" class="badge badge-purple" style="font-size:0.8rem;padding:0.4rem 0.875rem">Loading…</span>
    <button class="btn btn-primary btn-sm" onclick="openAddClassModal()">＋ Add New Class</button>
@endsection

@section('content')
<!-- STATS CARDS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏫</div>
        <div class="stat-value" id="statTotalClasses">—</div>
        <div class="stat-label">Total Academic Classes</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value" id="statActiveClasses">—</div>
        <div class="stat-label">Active Classes</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value" id="statTotalSubjects">—</div>
        <div class="stat-label">Curriculum Subjects Mapped</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📖</div>
        <div class="stat-value" id="statTotalChapters">—</div>
        <div class="stat-label">Syllabus Chapters</div>
    </div>
</div>

<!-- MAIN CARD -->
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">All Academic Classes</div>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem">Manage standards, boards, stages, and their curriculum assignments</div>
        </div>
        <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <div style="display:inline-flex;border:1px solid var(--border-light);border-radius:8px;overflow:hidden;background:var(--bg-surface)">
                <button id="viewGridBtn" class="btn btn-sm" style="border-radius:0;background:var(--primary);color:#fff" onclick="switchView('grid')">▦ Cards</button>
                <button id="viewTableBtn" class="btn btn-sm btn-outline" style="border-radius:0;border:none" onclick="switchView('table')">☰ Table</button>
            </div>
            <a href="{{ route('admin.subjects') }}" class="btn btn-outline btn-sm">📚 Subjects & Curriculum →</a>
        </div>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="card-body" style="padding:1.15rem 1.35rem 0.75rem">
        <div class="search-bar" style="margin-bottom:0">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="classSearchInput" placeholder="Search by class name, grade, or board…" oninput="filterClasses()">
            </div>
            <select class="filter-select" id="boardFilter" onchange="filterClasses()">
                <option value="">All Boards</option>
                <option value="WBBSE">WBBSE (West Bengal Secondary)</option>
                <option value="WBCHSE">WBCHSE (Higher Secondary)</option>
                <option value="CBSE">CBSE</option>
                <option value="ICSE">ICSE</option>
            </select>
            <select class="filter-select" id="stageFilter" onchange="filterClasses()">
                <option value="">All Stages</option>
                <option value="Primary">Primary (1–4)</option>
                <option value="Upper Primary">Upper Primary (5–8)</option>
                <option value="Secondary">Secondary (9–10)</option>
                <option value="Higher Secondary">Higher Secondary (11–12)</option>
            </select>
            <select class="filter-select" id="statusFilter" onchange="filterClasses()">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- CARDS VIEW CONTAINER -->
    <div id="classesGridContainer" style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.15rem">
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">
            <div class="spinner-sm"></div> Loading classes…
        </div>
    </div>

    <!-- TABLE VIEW CONTAINER (Hidden by default) -->
    <div id="classesTableContainer" class="table-wrapper" style="display:none">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">Level</th>
                    <th>Class / Grade</th>
                    <th>Board / Curriculum</th>
                    <th>Stage</th>
                    <th>Curriculum Metrics</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody id="classesTableBody">
                <tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div> Loading classes…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ════════════════════════════════════════════════
     MODALS
════════════════════════════════════════════════ -->

<!-- ADD CLASS MODAL -->
<div class="modal-backdrop" id="addClassModal">
    <div class="modal" style="max-width:520px">
        <div class="modal-header">
            <span class="modal-title">🏫 Add New Academic Class</span>
            <button class="modal-close" onclick="closeModal('addClassModal')">×</button>
        </div>
        <form id="addClassForm" onsubmit="submitAddClass(event)">
            <div class="modal-body" style="display:flex;flex-direction:column;gap:1rem">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Class Level / Number *</label>
                        <input type="number" class="search-input" id="newClassLevel" min="1" max="50" placeholder="e.g. 11" required>
                        <span style="font-size:0.7rem;color:var(--text-muted)">Unique numeric grade (e.g. 1, 2, 11)</span>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Class Name *</label>
                        <input type="text" class="search-input" id="newClassName" placeholder="e.g. Class 11" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Display Name / Title</label>
                    <input type="text" class="search-input" id="newClassDisplayName" placeholder="e.g. Class 11 (Higher Secondary)">
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Education Board</label>
                        <input type="text" class="search-input" id="newClassBoard" list="boardSuggestions" value="WBBSE" placeholder="e.g. WBBSE, WBCHSE, CBSE">
                        <datalist id="boardSuggestions">
                            <option value="WBBSE">WBBSE (West Bengal Board)</option>
                            <option value="WBCHSE">WBCHSE (Higher Secondary)</option>
                            <option value="CBSE">CBSE</option>
                            <option value="ICSE">ICSE</option>
                        </datalist>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Academic Stage</label>
                        <select class="search-input" id="newClassStage">
                            <option value="Primary">Primary (1–4)</option>
                            <option value="Upper Primary">Upper Primary (5–8)</option>
                            <option value="Secondary">Secondary (9–10)</option>
                            <option value="Higher Secondary">Higher Secondary (11–12)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Stream (Optional)</label>
                        <input type="text" class="search-input" id="newClassStream" placeholder="e.g. Science, Arts, General" value="General">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="search-input" id="newClassSortOrder" min="0" placeholder="Auto (matches level)">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Description / Remarks</label>
                    <textarea class="search-input" id="newClassDescription" rows="2" style="resize:vertical" placeholder="Optional notes about curriculum syllabus or syllabus track"></textarea>
                </div>

                <div style="display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0">
                    <input type="checkbox" id="newClassIsActive" checked style="width:16px;height:16px;accent-color:var(--primary);cursor:pointer">
                    <label for="newClassIsActive" style="font-size:0.85rem;font-weight:600;cursor:pointer">Class is Active & Available for Enrollment</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addClassModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitAddClass">Create Class</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT CLASS MODAL -->
<div class="modal-backdrop" id="editClassModal">
    <div class="modal" style="max-width:520px">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Academic Class</span>
            <button class="modal-close" onclick="closeModal('editClassModal')">×</button>
        </div>
        <form id="editClassForm" onsubmit="submitEditClass(event)">
            <input type="hidden" id="editClassId">
            <div class="modal-body" style="display:flex;flex-direction:column;gap:1rem">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Class Level / Number *</label>
                        <input type="number" class="search-input" id="editClassLevel" min="1" max="50" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Class Name *</label>
                        <input type="text" class="search-input" id="editClassName" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Display Name / Title</label>
                    <input type="text" class="search-input" id="editClassDisplayName">
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Education Board</label>
                        <input type="text" class="search-input" id="editClassBoard" list="boardSuggestions">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Academic Stage</label>
                        <select class="search-input" id="editClassStage">
                            <option value="Primary">Primary (1–4)</option>
                            <option value="Upper Primary">Upper Primary (5–8)</option>
                            <option value="Secondary">Secondary (9–10)</option>
                            <option value="Secondary (Madhyamik)">Secondary (Madhyamik)</option>
                            <option value="Higher Secondary">Higher Secondary (11–12)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Stream</label>
                        <input type="text" class="search-input" id="editClassStream">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="search-input" id="editClassSortOrder">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Description</label>
                    <textarea class="search-input" id="editClassDescription" rows="2" style="resize:vertical"></textarea>
                </div>

                <div style="display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0">
                    <input type="checkbox" id="editClassIsActive" style="width:16px;height:16px;accent-color:var(--primary);cursor:pointer">
                    <label for="editClassIsActive" style="font-size:0.85rem;font-weight:600;cursor:pointer">Class is Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editClassModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitEditClass">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allClasses = [];
let currentView = 'grid';

document.addEventListener('DOMContentLoaded', () => {
    loadClasses();
});

async function loadClasses() {
    try {
        const res = await api('GET', '/admin/classes');
        allClasses = Array.isArray(res.data) ? res.data : [];
        updateStats();
        renderView();
    } catch (e) {
        toast('Failed to load academic classes: ' + e.message, 'error');
    }
}

function updateStats() {
    const total = allClasses.length;
    const active = allClasses.filter(c => c.is_active).length;
    const subjects = allClasses.reduce((sum, c) => sum + (c.subjects_count || 0), 0);
    const chapters = allClasses.reduce((sum, c) => sum + (c.chapters_count || 0), 0);

    document.getElementById('statTotalClasses').textContent = total;
    document.getElementById('statActiveClasses').textContent = active;
    document.getElementById('statTotalSubjects').textContent = subjects;
    document.getElementById('statTotalChapters').textContent = chapters;
    document.getElementById('classCountBadge').textContent = `${total} Classes (${active} Active)`;
}

function switchView(view) {
    currentView = view;
    if (view === 'grid') {
        document.getElementById('classesGridContainer').style.display = 'grid';
        document.getElementById('classesTableContainer').style.display = 'none';
        document.getElementById('viewGridBtn').style.background = 'var(--primary)';
        document.getElementById('viewGridBtn').style.color = '#fff';
        document.getElementById('viewTableBtn').style.background = 'transparent';
        document.getElementById('viewTableBtn').style.color = 'var(--text-secondary)';
    } else {
        document.getElementById('classesGridContainer').style.display = 'none';
        document.getElementById('classesTableContainer').style.display = 'block';
        document.getElementById('viewTableBtn').style.background = 'var(--primary)';
        document.getElementById('viewTableBtn').style.color = '#fff';
        document.getElementById('viewGridBtn').style.background = 'transparent';
        document.getElementById('viewGridBtn').style.color = 'var(--text-secondary)';
    }
    renderView();
}

function filterClasses() {
    renderView();
}

function getFilteredClasses() {
    const search = document.getElementById('classSearchInput').value.trim().toLowerCase();
    const board = document.getElementById('boardFilter').value;
    const stage = document.getElementById('stageFilter').value;
    const status = document.getElementById('statusFilter').value;

    return allClasses.filter(c => {
        if (search) {
            const matchName = (c.name || '').toLowerCase().includes(search);
            const matchDisplay = (c.display_name || '').toLowerCase().includes(search);
            const matchBoard = (c.board || '').toLowerCase().includes(search);
            const matchLevel = String(c.class_level) === search;
            if (!matchName && !matchDisplay && !matchBoard && !matchLevel) return false;
        }
        if (board && c.board !== board) return false;
        if (stage && c.stage !== stage) return false;
        if (status === 'active' && !c.is_active) return false;
        if (status === 'inactive' && c.is_active) return false;
        return true;
    });
}

function renderView() {
    const list = getFilteredClasses();

    if (currentView === 'grid') {
        renderGrid(list);
    } else {
        renderTable(list);
    }
}

function renderGrid(list) {
    const grid = document.getElementById('classesGridContainer');
    if (!list.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">
            <div style="font-size:2.5rem;margin-bottom:0.5rem">🏫</div>
            <div style="font-weight:600">No matching classes found.</div>
            <div style="font-size:0.8rem;margin-top:0.25rem">Try adjusting filters or click "Add New Class" to create one.</div>
        </div>`;
        return;
    }

    grid.innerHTML = list.map(c => `
        <div style="background:var(--bg-card);border:1px solid ${c.is_active ? 'var(--border-light)' : 'rgba(239,68,68,0.2)'};border-radius:14px;padding:1.15rem;display:flex;flex-direction:column;gap:0.85rem;box-shadow:var(--shadow-sm);transition:all 0.2s;position:relative" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.75rem">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(109,74,255,0.09);color:var(--primary);font-weight:800;font-size:1.1rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        ${c.class_level}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:1rem;color:var(--text);line-height:1.2">${c.name}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem">${c.display_name || c.name}</div>
                    </div>
                </div>
                <span class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}">${c.is_active ? 'Active' : 'Inactive'}</span>
            </div>

            <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                <span class="badge badge-purple" style="font-size:0.68rem">${c.board || 'WBBSE'}</span>
                <span class="badge badge-info" style="font-size:0.68rem">${c.stage || 'General'}</span>
                ${c.stream && c.stream !== 'General' ? `<span class="badge badge-neutral" style="font-size:0.68rem">${c.stream}</span>` : ''}
            </div>

            <!-- CURRICULUM STATS -->
            <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:0.4rem;background:var(--bg-surface);padding:0.65rem 0.75rem;border-radius:8px;text-align:center;font-size:0.75rem">
                <div>
                    <div style="font-weight:800;font-size:0.95rem;color:var(--primary)">${c.subjects_count || 0}</div>
                    <div style="color:var(--text-muted);font-size:0.68rem">Subjects</div>
                </div>
                <div>
                    <div style="font-weight:800;font-size:0.95rem;color:var(--text)">${c.chapters_count || 0}</div>
                    <div style="color:var(--text-muted);font-size:0.68rem">Chapters</div>
                </div>
                <div>
                    <div style="font-weight:800;font-size:0.95rem;color:var(--text)">${c.students_count || 0}</div>
                    <div style="color:var(--text-muted);font-size:0.68rem">Students</div>
                </div>
            </div>

            <!-- ACTIONS -->
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.4rem;padding-top:0.4rem;border-top:1px solid var(--border-light);margin-top:auto">
                <a href="/admin/subjects?class_level=${c.class_level}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;font-size:0.75rem;padding:0.3rem 0.5rem">
                    📚 Subjects
                </a>
                <button class="btn btn-outline btn-sm" onclick="openEditClassModal(${c.id})" title="Edit class details" style="padding:0.3rem 0.6rem">
                    ✏️ Edit
                </button>
                <button class="btn btn-sm ${c.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleActiveClass(${c.id})" style="padding:0.3rem 0.55rem" title="${c.is_active ? 'Deactivate class' : 'Activate class'}">
                    ${c.is_active ? 'Disable' : 'Enable'}
                </button>
                <button class="btn btn-outline btn-sm" onclick="confirmDeleteClass(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.subjects_count}, ${c.chapters_count})" title="Delete class" style="color:var(--danger);border-color:rgba(239,68,68,0.2);padding:0.3rem 0.5rem">
                    🗑️
                </button>
            </div>
        </div>
    `).join('');
}

function renderTable(list) {
    const tbody = document.getElementById('classesTableBody');
    if (!list.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">No matching classes found.</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(c => `
        <tr>
            <td style="font-weight:800;color:var(--primary);font-size:0.95rem">
                ${c.class_level}
            </td>
            <td>
                <div style="font-weight:700;color:var(--text)">${c.name}</div>
                <div style="font-size:0.72rem;color:var(--text-muted)">${c.display_name || '—'}</div>
            </td>
            <td>
                <span class="badge badge-purple">${c.board || 'WBBSE'}</span>
                <div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.2rem">${c.curriculum || ''}</div>
            </td>
            <td>
                <span class="badge badge-info">${c.stage || 'General'}</span>
            </td>
            <td>
                <div style="display:flex;gap:0.75rem;font-size:0.8rem">
                    <span>📚 <strong>${c.subjects_count || 0}</strong> subj</span>
                    <span>📖 <strong>${c.chapters_count || 0}</strong> chap</span>
                    <span>👨‍🎓 <strong>${c.students_count || 0}</strong> stud</span>
                </div>
            </td>
            <td>
                <span class="badge ${c.is_active ? 'badge-success' : 'badge-neutral'}">${c.is_active ? 'Active' : 'Inactive'}</span>
            </td>
            <td style="text-align:right;white-space:nowrap">
                <a href="/admin/subjects?class_level=${c.class_level}" class="btn btn-outline btn-sm" style="font-size:0.75rem;padding:0.25rem 0.55rem">
                    📚 Subjects
                </a>
                <button class="btn btn-outline btn-sm" onclick="openEditClassModal(${c.id})" style="font-size:0.75rem;padding:0.25rem 0.55rem">
                    ✏️ Edit
                </button>
                <button class="btn btn-sm ${c.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleActiveClass(${c.id})" style="font-size:0.75rem;padding:0.25rem 0.55rem">
                    ${c.is_active ? 'Disable' : 'Enable'}
                </button>
                <button class="btn btn-outline btn-sm" onclick="confirmDeleteClass(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.subjects_count}, ${c.chapters_count})" style="color:var(--danger);border-color:rgba(239,68,68,0.2);padding:0.25rem 0.5rem">
                    🗑️
                </button>
            </td>
        </tr>
    `).join('');
}

// ── ADD CLASS ────────────────────────────────────────────────
function openAddClassModal() {
    document.getElementById('addClassForm').reset();
    // Guess next class level
    const maxLevel = allClasses.reduce((max, c) => Math.max(max, c.class_level || 0), 0);
    const nextLevel = maxLevel > 0 ? maxLevel + 1 : 1;
    document.getElementById('newClassLevel').value = nextLevel;
    document.getElementById('newClassName').value = `Class ${nextLevel}`;
    document.getElementById('newClassDisplayName').value = `Class ${nextLevel}`;
    document.getElementById('newClassSortOrder').value = nextLevel;
    document.getElementById('newClassIsActive').checked = true;

    // Default stage based on level
    if (nextLevel <= 4) document.getElementById('newClassStage').value = 'Primary';
    else if (nextLevel <= 8) document.getElementById('newClassStage').value = 'Upper Primary';
    else if (nextLevel <= 10) document.getElementById('newClassStage').value = 'Secondary';
    else document.getElementById('newClassStage').value = 'Higher Secondary';

    openModal('addClassModal');
}

// Auto sync name when level changes in Add Modal
document.getElementById('newClassLevel')?.addEventListener('input', (e) => {
    const val = parseInt(e.target.value);
    if (val && !document.getElementById('newClassName').dataset.customized) {
        document.getElementById('newClassName').value = `Class ${val}`;
        document.getElementById('newClassDisplayName').value = `Class ${val}`;
        document.getElementById('newClassSortOrder').value = val;
    }
});

async function submitAddClass(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitAddClass');
    btn.disabled = true;
    btn.textContent = 'Creating…';

    const payload = {
        class_level: parseInt(document.getElementById('newClassLevel').value),
        name: document.getElementById('newClassName').value.trim(),
        display_name: document.getElementById('newClassDisplayName').value.trim() || document.getElementById('newClassName').value.trim(),
        board: document.getElementById('newClassBoard').value.trim() || 'WBBSE',
        stage: document.getElementById('newClassStage').value,
        stream: document.getElementById('newClassStream').value.trim() || 'General',
        sort_order: parseInt(document.getElementById('newClassSortOrder').value) || parseInt(document.getElementById('newClassLevel').value),
        description: document.getElementById('newClassDescription').value.trim() || null,
        is_active: document.getElementById('newClassIsActive').checked
    };

    try {
        await api('POST', '/admin/classes', payload);
        toast(`Class ${payload.class_level} created successfully!`, 'success');
        closeModal('addClassModal');
        await loadClasses();
    } catch (err) {
        toast('Failed to create class: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Create Class';
    }
}

// ── EDIT CLASS ───────────────────────────────────────────────
function openEditClassModal(id) {
    const c = allClasses.find(item => item.id === id);
    if (!c) return;

    document.getElementById('editClassId').value = c.id;
    document.getElementById('editClassLevel').value = c.class_level;
    document.getElementById('editClassName').value = c.name;
    document.getElementById('editClassDisplayName').value = c.display_name || '';
    document.getElementById('editClassBoard').value = c.board || 'WBBSE';
    document.getElementById('editClassStage').value = c.stage || 'Primary';
    document.getElementById('editClassStream').value = c.stream || 'General';
    document.getElementById('editClassSortOrder').value = c.sort_order ?? c.class_level;
    document.getElementById('editClassDescription').value = c.description || '';
    document.getElementById('editClassIsActive').checked = !!c.is_active;

    openModal('editClassModal');
}

async function submitEditClass(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitEditClass');
    const id = document.getElementById('editClassId').value;
    btn.disabled = true;
    btn.textContent = 'Saving…';

    const payload = {
        class_level: parseInt(document.getElementById('editClassLevel').value),
        name: document.getElementById('editClassName').value.trim(),
        display_name: document.getElementById('editClassDisplayName').value.trim(),
        board: document.getElementById('editClassBoard').value.trim() || 'WBBSE',
        stage: document.getElementById('editClassStage').value,
        stream: document.getElementById('editClassStream').value.trim() || 'General',
        sort_order: parseInt(document.getElementById('editClassSortOrder').value),
        description: document.getElementById('editClassDescription').value.trim() || null,
        is_active: document.getElementById('editClassIsActive').checked
    };

    try {
        await api('PUT', `/admin/classes/${id}`, payload);
        toast('Class updated successfully!', 'success');
        closeModal('editClassModal');
        await loadClasses();
    } catch (err) {
        toast('Failed to update class: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
}

// ── TOGGLE ACTIVE ────────────────────────────────────────────
async function toggleActiveClass(id) {
    try {
        const res = await api('POST', `/admin/classes/${id}/toggle-status`);
        toast(res.message || 'Status updated.', 'success');
        await loadClasses();
    } catch (err) {
        toast('Failed to change status: ' + err.message, 'error');
    }
}

// ── DELETE CLASS ─────────────────────────────────────────────
async function confirmDeleteClass(id, name, subjectsCount, chaptersCount) {
    if (subjectsCount > 0 || chaptersCount > 0) {
        const msg = `${name} has ${subjectsCount} subject(s) and ${chaptersCount} chapter(s) linked to it.\n\nDeleting this class will permanently remove all subject mappings for this grade.\n\nTip: You can deactivate this class instead of deleting it.\n\nAre you absolutely sure you want to FORCE delete this class?`;
        if (!confirm(msg)) return;
        try {
            await api('DELETE', `/admin/classes/${id}?force=1`);
            toast(`${name} deleted successfully!`, 'success');
            await loadClasses();
        } catch (err) {
            toast('Failed to delete class: ' + err.message, 'error');
        }
    } else {
        if (!confirm(`Are you sure you want to delete ${name}?`)) return;
        try {
            await api('DELETE', `/admin/classes/${id}`);
            toast(`${name} deleted successfully!`, 'success');
            await loadClasses();
        } catch (err) {
            toast('Failed to delete class: ' + err.message, 'error');
        }
    }
}
</script>
@endsection
