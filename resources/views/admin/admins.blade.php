@extends('layouts.admin')
@section('title','Admins')
@section('page-title','Admins')
@section('page-subtitle','Manage admin accounts')
@section('topbar-actions')
    <button class="btn btn-primary topbar-btn" onclick="openModal('addAdminModal')">＋ Add Admin</button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">All Admins</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Admin Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="adminsBody">
                <tr class="loading-row"><td colspan="5"><div class="spinner-sm"></div> Loading…</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination" id="paginationBar" style="display:none">
        <span id="paginationInfo"></span>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal-backdrop" id="addAdminModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">➕ Add Admin</span>
            <button class="modal-close" onclick="closeModal('addAdminModal')">×</button>
        </div>
        <div class="modal-body">
            <form id="addAdminForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="a-name" placeholder="Admin name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="a-email" placeholder="admin@vidyamission.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="text" class="form-control" id="a-password" placeholder="Min. 6 chars" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addAdminModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createAdmin()">Create Admin</button>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal-backdrop" id="editAdminModal">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">✏️ Edit Admin</span>
            <button class="modal-close" onclick="closeModal('editAdminModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ea-id">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="ea-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="ea-email">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="text" class="form-control" id="ea-password" placeholder="Leave blank to keep unchanged">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="ea-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('editAdminModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateAdmin()">Save Changes</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;

async function loadAdmins(page = 1) {
    currentPage = page;
    document.getElementById('adminsBody').innerHTML = '<tr class="loading-row"><td colspan="5"><div class="spinner-sm"></div></td></tr>';
    try {
        const res = await api('GET', `/admin/admins?page=${page}`);
        const data = res.data || [];
        document.getElementById('adminsBody').innerHTML = data.length ? data.map((a, i) => `
            <tr>
                <td style="color:var(--text-muted)">${(page-1)*20+i+1}</td>
                <td><div style="font-weight:600">${a.name}</div></td>
                <td><div style="font-size:0.85rem;color:var(--text-muted)">${a.email}</div></td>
                <td><span class="badge ${a.is_active ? 'badge-success' : 'badge-danger'}">${a.is_active ? 'Active' : 'Inactive'}</span></td>
                <td style="display:flex;gap:0.4rem;flex-wrap:wrap">
                    <button class="btn btn-outline btn-sm" onclick='editAdmin(${JSON.stringify(a).replace(/'/g, "&apos;")})'>✏️</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteAdmin(${a.id})">🗑</button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No admins found.</td></tr>';
        
        const meta = res.meta;
        if (meta && meta.last_page > 1) {
            document.getElementById('paginationBar').style.display = 'flex';
            document.getElementById('paginationInfo').textContent = `${meta.from || '?'}–${meta.to || '?'} of ${meta.total}`;
            const btns = [];
            for (let p = Math.max(1, meta.current_page - 2); p <= Math.min(meta.last_page, meta.current_page + 2); p++) {
                btns.push(`<button class="page-btn ${p===meta.current_page?'active':''}" onclick="loadAdmins(${p})">${p}</button>`);
            }
            document.getElementById('paginationBtns').innerHTML =
                `<button class="page-btn" onclick="loadAdmins(${meta.current_page-1})" ${meta.current_page===1?'disabled':''}>‹</button>` +
                btns.join('') +
                `<button class="page-btn" onclick="loadAdmins(${meta.current_page+1})" ${meta.current_page===meta.last_page?'disabled':''}>›</button>`;
        } else {
            document.getElementById('paginationBar').style.display = 'none';
        }
    } catch(e) { toast('Error: ' + e.message, 'error'); }
}

async function createAdmin() {
    try {
        await api('POST', '/admin/admins', {
            name: document.getElementById('a-name').value,
            email: document.getElementById('a-email').value,
            password: document.getElementById('a-password').value,
        });
        toast('Admin created successfully!', 'success');
        closeModal('addAdminModal');
        document.getElementById('addAdminForm').reset();
        loadAdmins(1);
    } catch(e) { toast(e.message, 'error'); }
}

function editAdmin(a) {
    document.getElementById('ea-id').value = a.id;
    document.getElementById('ea-name').value = a.name;
    document.getElementById('ea-email').value = a.email;
    document.getElementById('ea-password').value = '';
    document.getElementById('ea-active').value = a.is_active ? '1' : '0';
    openModal('editAdminModal');
}

async function updateAdmin() {
    const id = document.getElementById('ea-id').value;
    try {
        await api('PUT', `/admin/admins/${id}`, {
            name: document.getElementById('ea-name').value,
            email: document.getElementById('ea-email').value,
            password: document.getElementById('ea-password').value,
            is_active: document.getElementById('ea-active').value === '1',
        });
        toast('Admin updated!', 'success');
        closeModal('editAdminModal');
        loadAdmins(currentPage);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteAdmin(id) {
    if (!confirm('Deactivate this admin?')) return;
    try { 
        await api('DELETE', `/admin/admins/${id}`); 
        toast('Admin deactivated.', 'success'); 
        loadAdmins(currentPage); 
    } catch(e) { 
        toast(e.message, 'error'); 
    }
}

window._pageInit = () => loadAdmins(1);
</script>
@endsection
