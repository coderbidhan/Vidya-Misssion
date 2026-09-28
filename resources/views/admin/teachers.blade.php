@extends('layouts.admin')
@section('title','Teachers')
@section('page-title','Teachers')
@section('page-subtitle','Manage teacher accounts, photos, and subject assignments')
@section('topbar-actions')
    <button class="btn btn-primary topbar-btn" onclick="openModal('addTeacherModal')">＋ Add Teacher</button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">All Teachers</span>
    </div>
    <div class="card-body" style="padding:1.25rem 1.5rem 0.75rem">
        <div class="search-bar">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Search teachers…" oninput="debounceSearch()">
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Photo</th><th>Teacher</th><th>Qualification</th><th>Assigned Subjects</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="teachersBody">
                <tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div> Loading…</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination" id="paginationBar" style="display:none">
        <span id="paginationInfo"></span>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal-backdrop" id="addTeacherModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Add Teacher</span>
            <button class="modal-close" onclick="closeModal('addTeacherModal')">×</button>
        </div>
        <div class="modal-body">
            <form id="addTeacherForm" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="t-name" placeholder="Teacher name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="t-email" placeholder="teacher@school.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" id="t-mobile" placeholder="+91 98765 43210">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="text" class="form-control" id="t-password" placeholder="Min. 6 chars" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Qualification</label>
                        <input type="text" class="form-control" id="t-qual" placeholder="M.Sc Physics / B.Ed">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teacher Photo</label>
                        <input type="file" class="form-control" id="t-avatar" accept="image/*">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea class="form-control" id="t-bio" placeholder="Brief bio about teacher's experience…" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addTeacherModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createTeacher()">Create Teacher</button>
        </div>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal-backdrop" id="editTeacherModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Teacher</span>
            <button class="modal-close" onclick="closeModal('editTeacherModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="et-id">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="et-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="et-email">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mobile</label>
                    <input type="text" class="form-control" id="et-mobile">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="text" class="form-control" id="et-password" placeholder="Leave blank to keep unchanged">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Qualification</label>
                    <input type="text" class="form-control" id="et-qual">
                </div>
                <div class="form-group">
                    <label class="form-label">Update Photo</label>
                    <input type="file" class="form-control" id="et-avatar" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea class="form-control" id="et-bio" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" id="et-active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editTeacherModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateTeacher()">Save Changes</button>
        </div>
    </div>
</div>

<!-- Assign Subjects Modal -->
<div class="modal-backdrop" id="assignModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📚 Assign Subjects to Teacher</span>
            <button class="modal-close" onclick="closeModal('assignModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="as-teacher-id">
            <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem">Select Class and Subject to map to this teacher:</p>
            
            <div style="display:flex;gap:0.5rem;margin-bottom:1rem;flex-wrap:wrap">
                <select id="as-class" class="form-control" style="flex:1;min-width:130px" onchange="updateAssignSubjects()">
                    <option value="">Select Class...</option>
                    @for($c=1; $c<=12; $c++)
                        <option value="{{ $c }}">Class {{ $c }}</option>
                    @endfor
                </select>
                <select id="as-subject" class="form-control" style="flex:2;min-width:180px" disabled>
                    <option value="">Select Subject...</option>
                </select>
                <button class="btn btn-primary" type="button" onclick="addAssignment()">＋ Add</button>
            </div>
            
            <div style="font-size:0.78rem;font-weight:700;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase">Assigned Subjects:</div>
            <div id="assignmentList" style="display:flex;flex-direction:column;gap:0.4rem;max-height:220px;overflow-y:auto">
                <!-- Filled via JS -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('assignModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveSubjects()">Save All Assignments</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let searchTimer, allClassSubjects = [], currentAssignments = [], currentPage = 1;

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadTeachers(1), 400); }

async function loadTeachers(page = 1) {
    currentPage = page;
    document.getElementById('teachersBody').innerHTML = '<tr class="loading-row"><td colspan="7"><div class="spinner-sm"></div></td></tr>';
    try {
        const res = await api('GET', `/admin/teachers?page=${page}`);
        const data = res.data || [];
        document.getElementById('teachersBody').innerHTML = data.length ? data.map((t, i) => {
            const avatarHtml = t.teacher_profile?.avatar_url
                ? `<img src="${t.teacher_profile.avatar_url}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">`
                : `<div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem">${t.name.charAt(0).toUpperCase()}</div>`;
            return `
            <tr>
                <td style="color:var(--text-muted)">${(page-1)*20+i+1}</td>
                <td>${avatarHtml}</td>
                <td>
                    <div style="font-weight:600">${t.name}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">${t.email} ${t.mobile ? ' • ' + t.mobile : ''}</div>
                </td>
                <td>${t.teacher_profile?.qualification || '—'}</td>
                <td style="max-width:280px;white-space:normal">${(t.subjects||[]).map(s=>`<span class="badge badge-purple" style="margin-bottom:0.25rem;display:inline-block">Class ${s.class_level}: ${s.name}</span>`).join(' ') || '<span style="color:var(--text-muted);font-size:0.8rem">No subjects assigned</span>'}</td>
                <td><span class="badge ${t.is_active ? 'badge-success' : 'badge-danger'}">${t.is_active ? 'Active' : 'Inactive'}</span></td>
                <td style="display:flex;gap:0.4rem;flex-wrap:wrap">
                    <button class="btn btn-outline btn-sm" title="Edit Teacher" onclick="editTeacher(JSON.parse(this.dataset.teacher))" data-teacher='${JSON.stringify(t).replace(/'/g, "&#39;")}'>✏️</button>
                    <button class="btn btn-outline btn-sm" title="Assign Subjects" onclick="openAssign(JSON.parse(this.dataset.teacher))" data-teacher='${JSON.stringify(t).replace(/'/g, "&#39;")}'>📚 Subjects</button>
                    <button class="btn btn-danger btn-sm" title="Deactivate" onclick="deleteTeacher(${t.id})">🗑</button>
                </td>
            </tr>
        `;}).join('') : '<tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">No teachers found.</td></tr>';
        const meta = res.meta;
        if (meta && meta.last_page > 1) {
            document.getElementById('paginationBar').style.display = 'flex';
            document.getElementById('paginationInfo').textContent = `${meta.from}–${meta.to} of ${meta.total}`;
        }
    } catch(e) { toast('Error: ' + e.message, 'error'); }
}

async function createTeacher() {
    try {
        const formData = new FormData();
        formData.append('name', document.getElementById('t-name').value);
        formData.append('email', document.getElementById('t-email').value);
        formData.append('mobile', document.getElementById('t-mobile').value);
        formData.append('password', document.getElementById('t-password').value);
        formData.append('qualification', document.getElementById('t-qual').value);
        formData.append('bio', document.getElementById('t-bio').value);
        const avatarFile = document.getElementById('t-avatar').files[0];
        if (avatarFile) formData.append('avatar', avatarFile);

        const res = await fetch(API + '/admin/teachers', {
            method: 'POST',
            headers: {
                Authorization: 'Bearer ' + TOKEN,
                Accept: 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: formData
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Failed to create teacher');

        toast('Teacher created successfully with photo!', 'success');
        closeModal('addTeacherModal');
        document.getElementById('addTeacherForm').reset();
        loadTeachers(1);
    } catch(e) { toast(e.message, 'error'); }
}

function editTeacher(t) {
    document.getElementById('et-id').value = t.id;
    document.getElementById('et-name').value = t.name;
    document.getElementById('et-email').value = t.email;
    document.getElementById('et-mobile').value = t.mobile || '';
    document.getElementById('et-qual').value = t.teacher_profile?.qualification || '';
    document.getElementById('et-password').value = '';
    document.getElementById('et-bio').value = t.teacher_profile?.bio || '';
    document.getElementById('et-active').value = t.is_active ? '1' : '0';
    document.getElementById('et-avatar').value = '';
    openModal('editTeacherModal');
}

async function updateTeacher() {
    const id = document.getElementById('et-id').value;
    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', document.getElementById('et-name').value);
        formData.append('email', document.getElementById('et-email').value);
        formData.append('mobile', document.getElementById('et-mobile').value);
        if (document.getElementById('et-password').value) {
            formData.append('password', document.getElementById('et-password').value);
        }
        formData.append('qualification', document.getElementById('et-qual').value);
        formData.append('bio', document.getElementById('et-bio').value);
        formData.append('is_active', document.getElementById('et-active').value);
        const avatarFile = document.getElementById('et-avatar').files[0];
        if (avatarFile) formData.append('avatar', avatarFile);

        const res = await fetch(API + `/admin/teachers/${id}`, {
            method: 'POST',
            headers: {
                Authorization: 'Bearer ' + TOKEN,
                Accept: 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: formData
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Failed to update teacher');

        toast('Teacher updated!', 'success');
        closeModal('editTeacherModal');
        loadTeachers(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteTeacher(id) {
    if (!confirm('Deactivate this teacher?')) return;
    try { await api('DELETE', `/admin/teachers/${id}`); toast('Teacher deactivated.', 'success'); loadTeachers(currentPage); }
    catch(e) { toast(e.message, 'error'); }
}

async function openAssign(teacher) {
    document.getElementById('as-teacher-id').value = teacher.id;
    currentAssignments = (teacher.subjects || []).map(s => ({
        class_level: s.class_level,
        subject_id: s.id,
        subject_name: s.name
    }));

    if (!allClassSubjects.length) {
        const res = await api('GET', '/admin/class-subjects/all');
        allClassSubjects = res.data || [];
    }

    document.getElementById('as-class').value = '';
    document.getElementById('as-subject').innerHTML = '<option value="">Select Subject...</option>';
    document.getElementById('as-subject').disabled = true;

    renderAssignments();
    openModal('assignModal');
}

function updateAssignSubjects() {
    const classLevel = parseInt(document.getElementById('as-class').value);
    const subSelect = document.getElementById('as-subject');
    if (!classLevel) {
        subSelect.innerHTML = '<option value="">Select Subject...</option>';
        subSelect.disabled = true;
        return;
    }
    const subjects = allClassSubjects.filter(cs => cs.class_level === classLevel);
    if (!subjects.length) {
        subSelect.innerHTML = '<option value="">No subjects mapped to this class</option>';
        subSelect.disabled = true;
        return;
    }
    subSelect.innerHTML = '<option value="">Select Subject...</option>' + 
        subjects.map(s => `<option value="${s.subject_id}">${s.subject_name}</option>`).join('');
    subSelect.disabled = false;
}

function addAssignment() {
    const classLevel = parseInt(document.getElementById('as-class').value);
    const subjectId = parseInt(document.getElementById('as-subject').value);
    const subjectName = document.getElementById('as-subject').options[document.getElementById('as-subject').selectedIndex]?.text;

    if (!classLevel || !subjectId) return toast('Please select both class and subject.', 'error');
    
    // Check for duplicates
    if (currentAssignments.find(a => a.class_level === classLevel && a.subject_id === subjectId)) {
        return toast('Already assigned to this class and subject.', 'error');
    }

    currentAssignments.push({ class_level: classLevel, subject_id: subjectId, subject_name: subjectName });
    renderAssignments();
    
    // Reset dropdowns
    document.getElementById('as-class').value = '';
    updateAssignSubjects();
}

function removeAssignment(index) {
    currentAssignments.splice(index, 1);
    renderAssignments();
}

function renderAssignments() {
    const list = document.getElementById('assignmentList');
    if (!currentAssignments.length) {
        list.innerHTML = '<div style="font-size:0.8rem;color:var(--text-muted);text-align:center;padding:1rem">No subjects assigned yet.</div>';
        return;
    }
    list.innerHTML = currentAssignments.map((a, i) => `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0.8rem;background:var(--bg-surface);border:1px solid var(--border-light);border-radius:8px">
            <span style="font-size:0.85rem;font-weight:600">Class ${a.class_level} — ${a.subject_name}</span>
            <button class="btn btn-outline btn-sm" onclick="removeAssignment(${i})" style="padding:0.2rem 0.5rem;color:var(--danger)">✕</button>
        </div>
    `).join('');
}

async function saveSubjects() {
    const id = document.getElementById('as-teacher-id').value;
    try {
        await api('POST', `/admin/teachers/${id}/assign-subjects`, { assignments: currentAssignments });
        toast('Subjects assigned successfully!', 'success');
        closeModal('assignModal');
        loadTeachers(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

window._pageInit = () => loadTeachers(1);
</script>
@endsection
