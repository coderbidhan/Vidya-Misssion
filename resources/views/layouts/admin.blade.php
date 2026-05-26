<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard') — VidyaMission Admin</title>
<meta name="api-token" content="{{ $token }}">
<meta name="api-base"  content="{{ $apiBase }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary:#6D4AFF; --primary-light:#8B6BFF; --primary-dark:#5536D4;
    --secondary:#0ABCBC; --accent:#FF5C7A;
    --success:#10B981; --warning:#F59E0B; --danger:#EF4444;

    --sidebar-bg:#FFFFFF; --sidebar-width:240px; --topbar-height:64px;

    /* Page surfaces */
    --bg:#F8FAFC; --bg-card:#FFFFFF; --bg-surface:#F1F5F9;

    /* Borders */
    --border:rgba(109,74,255,0.15); --border-light:rgba(0,0,0,0.07);

    /* Text */
    --text:#111827; --text-muted:#6B7280; --text-secondary:#374151;

    /* Shadows */
    --shadow-sm:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);
    --shadow-md:0 4px 16px rgba(0,0,0,0.08);
    --shadow-purple:0 4px 16px rgba(109,74,255,0.25);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}

/* ══════════════════════ SIDEBAR ══════════════════════ */
.sidebar{
    width:var(--sidebar-width);min-width:var(--sidebar-width);
    background:var(--sidebar-bg);
    border-right:1px solid var(--border-light);
    display:flex;flex-direction:column;
    position:fixed;top:0;left:0;bottom:0;z-index:100;
    transition:transform 0.3s;
    box-shadow:2px 0 8px rgba(0,0,0,0.04);
}
.sidebar-logo{
    padding:1.125rem 1.5rem;border-bottom:1px solid var(--border-light);
    display:flex;align-items:center;gap:0.75rem;text-decoration:none;
}
.sidebar-logo-icon{
    width:34px;height:34px;border-radius:9px;
    background:linear-gradient(135deg,var(--primary),var(--primary-light));
    display:flex;align-items:center;justify-content:center;
    font-family:'Poppins',sans-serif;font-size:0.95rem;font-weight:800;color:#fff;
    box-shadow:var(--shadow-purple);flex-shrink:0;
}
.sidebar-logo-text{font-family:'Poppins',sans-serif;font-size:1rem;font-weight:800;color:var(--text);}
.sidebar-logo-text span{color:var(--primary);}
.sidebar-logo-badge{font-size:0.58rem;font-weight:800;padding:0.15rem 0.45rem;border-radius:5px;background:rgba(109,74,255,0.1);color:var(--primary);border:1px solid rgba(109,74,255,0.2);letter-spacing:0.05em;}

.sidebar-nav{flex:1;padding:0.875rem 0.75rem;overflow-y:auto;display:flex;flex-direction:column;gap:0.2rem;}
.nav-section-label{font-size:0.62rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;color:var(--text-muted);padding:0.75rem 0.75rem 0.2rem;margin-top:0.4rem;opacity:0.7;}
.nav-item{
    display:flex;align-items:center;gap:0.65rem;
    padding:0.6rem 0.875rem;border-radius:10px;
    text-decoration:none;color:var(--text-secondary);
    font-size:0.85rem;font-weight:500;
    transition:all 0.18s;position:relative;
}
.nav-item:hover{background:rgba(109,74,255,0.06);color:var(--primary);}
.nav-item.active{background:rgba(109,74,255,0.1);color:var(--primary);font-weight:600;}
.nav-item.active::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;border-radius:0 3px 3px 0;background:var(--primary);}
.nav-icon{width:18px;text-align:center;font-size:0.95rem;flex-shrink:0;}
.nav-badge{margin-left:auto;font-size:0.62rem;font-weight:700;padding:0.15rem 0.45rem;border-radius:100px;background:rgba(109,74,255,0.12);color:var(--primary);}

.sidebar-footer{padding:0.875rem 0.75rem;border-top:1px solid var(--border-light);}
.sidebar-user{display:flex;align-items:center;gap:0.65rem;padding:0.7rem;border-radius:10px;background:var(--bg-surface);}
.sidebar-avatar{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;color:#fff;flex-shrink:0;}
.sidebar-user-info{flex:1;min-width:0;}
.sidebar-user-name{font-size:0.78rem;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sidebar-user-role{font-size:0.68rem;color:var(--text-muted);}
.logout-btn{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:0.25rem;font-size:1rem;transition:color 0.2s;line-height:1;}
.logout-btn:hover{color:var(--danger);}

/* ══════════════════════ MAIN ══════════════════════ */
.main{margin-left:var(--sidebar-width);flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ══════════════════════ TOPBAR ══════════════════════ */
.topbar{
    height:var(--topbar-height);
    background:#FFFFFF;border-bottom:1px solid var(--border-light);
    display:flex;align-items:center;padding:0 1.75rem;
    gap:1rem;position:sticky;top:0;z-index:50;
    box-shadow:0 1px 4px rgba(0,0,0,0.04);
}
.topbar-title{font-family:'Poppins',sans-serif;font-size:1.05rem;font-weight:800;flex:1;color:var(--text);}
.topbar-subtitle{font-size:0.78rem;color:var(--text-muted);font-weight:400;}
.topbar-actions{display:flex;align-items:center;gap:0.75rem;}
.topbar-btn{padding:0.5rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;border:none;display:flex;align-items:center;gap:0.4rem;font-family:'Inter',sans-serif;}

/* ══════════════════════ CONTENT ══════════════════════ */
.content{flex:1;padding:1.5rem;overflow:hidden;}

/* ══════════════════════ CARDS ══════════════════════ */
.card{background:var(--bg-card);border:1px solid var(--border-light);border-radius:14px;overflow:hidden;box-shadow:var(--shadow-sm);}
.card-header{padding:1.125rem 1.375rem;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.card-title{font-size:0.9rem;font-weight:700;color:var(--text);}
.card-body{padding:1.375rem;}

/* ══════════════════════ STAT CARDS ══════════════════════ */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:1rem;margin-bottom:1.5rem;}
.stat-card{
    background:var(--bg-card);border:1px solid var(--border-light);
    border-radius:14px;padding:1.25rem 1.375rem;position:relative;overflow:hidden;
    transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--shadow-sm);
}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--card-accent,linear-gradient(90deg,var(--primary),var(--primary-light)));}
.stat-icon{font-size:1.6rem;margin-bottom:0.65rem;}
.stat-value{font-family:'Poppins',sans-serif;font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:0.2rem;color:var(--text);}
.stat-label{font-size:0.78rem;color:var(--text-muted);font-weight:500;}

/* ══════════════════════ TABLE ══════════════════════ */
.table-wrapper{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead tr{border-bottom:1px solid var(--border-light);background:rgba(109,74,255,0.03);}
th{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);padding:0.8rem 1rem;text-align:left;white-space:nowrap;}
td{padding:0.8rem 1rem;font-size:0.85rem;border-bottom:1px solid rgba(0,0,0,0.04);vertical-align:middle;color:var(--text);}
tbody tr:hover{background:rgba(109,74,255,0.025);}
tbody tr:last-child td{border-bottom:none;}

/* ══════════════════════ BADGES ══════════════════════ */
.badge{display:inline-flex;align-items:center;gap:0.3rem;padding:0.22rem 0.6rem;border-radius:100px;font-size:0.7rem;font-weight:700;}
.badge-success{background:rgba(16,185,129,0.1); color:#059669; border:1px solid rgba(16,185,129,0.2);}
.badge-warning{background:rgba(245,158,11,0.1); color:#D97706; border:1px solid rgba(245,158,11,0.2);}
.badge-danger {background:rgba(239,68,68,0.1);  color:#DC2626; border:1px solid rgba(239,68,68,0.2);}
.badge-info   {background:rgba(10,188,188,0.1); color:#0891B2; border:1px solid rgba(10,188,188,0.2);}
.badge-purple {background:rgba(109,74,255,0.1); color:var(--primary); border:1px solid rgba(109,74,255,0.2);}
.badge-neutral{background:rgba(0,0,0,0.05);     color:var(--text-muted); border:1px solid rgba(0,0,0,0.08);}

/* ══════════════════════ BUTTONS ══════════════════════ */
.btn{padding:0.5rem 1rem;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;border:none;display:inline-flex;align-items:center;gap:0.35rem;font-family:'Inter',sans-serif;white-space:nowrap;}
.btn-sm{padding:0.32rem 0.7rem;font-size:0.75rem;}
.btn-primary{background:var(--primary);color:#fff;box-shadow:0 2px 8px rgba(109,74,255,0.3);}
.btn-primary:hover{background:var(--primary-dark);box-shadow:0 4px 16px rgba(109,74,255,0.4);transform:translateY(-1px);}
.btn-success{background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.2);}
.btn-success:hover{background:rgba(16,185,129,0.2);}
.btn-danger{background:rgba(239,68,68,0.08);color:#DC2626;border:1px solid rgba(239,68,68,0.2);}
.btn-danger:hover{background:rgba(239,68,68,0.15);}
.btn-outline{background:transparent;border:1px solid var(--border-light);color:var(--text-secondary);}
.btn-outline:hover{border-color:var(--primary);color:var(--primary);background:rgba(109,74,255,0.04);}

/* ══════════════════════ SEARCH / FILTER ══════════════════════ */
.search-bar{display:flex;align-items:center;gap:0.75rem;margin-bottom:1.125rem;flex-wrap:wrap;}
.search-input-wrap{position:relative;flex:1;min-width:220px;}
.search-icon{position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.9rem;}
.search-input{width:100%;padding:0.6rem 1rem 0.6rem 2.4rem;background:#FFFFFF;border:1px solid var(--border-light);border-radius:9px;color:var(--text);font-size:0.85rem;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s;}
.search-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(109,74,255,0.1);}
.search-input::placeholder{color:var(--text-muted);}
.filter-select{padding:0.6rem 1rem;background:#FFFFFF;border:1px solid var(--border-light);border-radius:9px;color:var(--text-secondary);font-size:0.85rem;font-family:'Inter',sans-serif;outline:none;cursor:pointer;}
.filter-select:focus{border-color:var(--primary);}

/* ══════════════════════ MODAL ══════════════════════ */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.35);backdrop-filter:blur(4px);z-index:200;display:none;align-items:center;justify-content:center;padding:1.5rem;}
.modal-backdrop.open{display:flex;}
.modal{background:#FFFFFF;border:1px solid var(--border-light);border-radius:18px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,0.18);animation:modal-in 0.28s cubic-bezier(0.4,0,0.2,1);}
@keyframes modal-in{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:none}}
.modal-header{padding:1.375rem 1.5rem;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-size:0.95rem;font-weight:700;color:var(--text);}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:1.3rem;cursor:pointer;padding:0;line-height:1;transition:color 0.2s;}
.modal-close:hover{color:var(--danger);}
.modal-body{padding:1.375rem;}
.modal-footer{padding:1rem 1.5rem;border-top:1px solid var(--border-light);display:flex;justify-content:flex-end;gap:0.75rem;}

/* ══════════════════════ FORM ══════════════════════ */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.form-group{margin-bottom:1rem;}
.form-label{display:block;font-size:0.72rem;font-weight:700;color:var(--text-muted);letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.4rem;}
.form-control{width:100%;padding:0.65rem 0.875rem;background:#FFFFFF;border:1px solid rgba(0,0,0,0.1);border-radius:9px;color:var(--text);font-size:0.85rem;font-family:'Inter',sans-serif;transition:all 0.2s;outline:none;}
.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(109,74,255,0.1);}
.form-control::placeholder{color:var(--text-muted);}
select.form-control{cursor:pointer;}
textarea.form-control{resize:vertical;min-height:90px;}

/* ══════════════════════ TOAST ══════════════════════ */
.toast-container{position:fixed;bottom:1.5rem;right:1.5rem;z-index:999;display:flex;flex-direction:column;gap:0.65rem;}
.toast{padding:0.8rem 1.125rem;border-radius:12px;font-size:0.85rem;font-weight:500;display:flex;align-items:center;gap:0.55rem;min-width:250px;box-shadow:0 8px 24px rgba(0,0,0,0.12);animation:toast-in 0.3s ease;border:1px solid transparent;}
@keyframes toast-in{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}
.toast-success{background:#F0FDF4;border-color:#A7F3D0;color:#065F46;}
.toast-error  {background:#FEF2F2;border-color:#FECACA;color:#991B1B;}
.toast-info   {background:#F5F3FF;border-color:#DDD6FE;color:#5B21B6;}

/* ══════════════════════ LOADING ══════════════════════ */
.loading-row td{text-align:center;padding:2.5rem;color:var(--text-muted);}
.spinner-sm{width:16px;height:16px;border:2px solid rgba(0,0,0,0.1);border-top-color:var(--primary);border-radius:50%;animation:spin 0.6s linear infinite;display:inline-block;}
@keyframes spin{to{transform:rotate(360deg)}}
.empty-state{text-align:center;padding:3.5rem 2rem;color:var(--text-muted);}
.empty-state .empty-icon{font-size:2.75rem;margin-bottom:0.875rem;opacity:0.4;}
.empty-state p{font-size:0.875rem;}

/* ══════════════════════ PAGINATION ══════════════════════ */
.pagination{display:flex;align-items:center;justify-content:space-between;padding:0.875rem 1.375rem;border-top:1px solid var(--border-light);font-size:0.8rem;color:var(--text-muted);}
.pagination-btns{display:flex;gap:0.4rem;}
.page-btn{padding:0.35rem 0.7rem;border-radius:7px;background:#FFFFFF;border:1px solid var(--border-light);color:var(--text-secondary);cursor:pointer;transition:all 0.2s;font-size:0.78rem;}
.page-btn:hover:not(:disabled){border-color:var(--primary);color:var(--primary);}
.page-btn:disabled{opacity:0.4;cursor:not-allowed;}
.page-btn.active{background:var(--primary);border-color:var(--primary);color:#fff;}

/* ══════════════════════ TABS ══════════════════════ */
.tabs{display:flex;border-bottom:2px solid var(--border-light);margin-bottom:1.375rem;gap:0;}
.tab-btn{padding:0.7rem 1.125rem;background:none;border:none;color:var(--text-muted);font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent;margin-bottom:-2px;display:flex;align-items:center;gap:0.4rem;font-family:'Inter',sans-serif;}
.tab-btn:hover{color:var(--text);}
.tab-btn.active{color:var(--primary);border-bottom-color:var(--primary);}
.tab-panel{display:none;}
.tab-panel.active{display:block;}

/* ══════════════════════ RESPONSIVE ══════════════════════ */
@media(max-width:900px){
    .sidebar{transform:translateX(-100%);}
    .sidebar.open{transform:none;box-shadow:0 0 0 100vw rgba(0,0,0,0.5);}
    .main{margin-left:0;}
    .form-row{grid-template-columns:1fr;}
    #sidebar-toggle{display:flex !important;}
    .stats-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:600px){
    .stats-grid{grid-template-columns:1fr;}
    .topbar{padding:0 1rem;}
    .card-header{flex-direction:column;align-items:flex-start;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <div class="sidebar-logo-icon">V</div>
        <div>
            <div class="sidebar-logo-text">Vidya<span>Mission</span></div>
            <div class="sidebar-logo-badge">ADMIN</div>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span> Dashboard
        </a>

        <div class="nav-section-label">Users</div>
        <a href="{{ route('admin.admins.creation') }}" class="nav-item {{ request()->routeIs('admin.admins.creation') ? 'active' : '' }}">
            <span class="nav-icon">🛡️</span> Admins
        </a>
        <a href="{{ route('admin.students.creation') }}" class="nav-item {{ request()->routeIs('admin.students.creation') ? 'active' : '' }}">
            <span class="nav-icon">👨‍🎓</span> Students
        </a>
        <a href="{{ route('admin.teachers.creation') }}" class="nav-item {{ request()->routeIs('admin.teachers') ? 'active' : '' }}">
            <span class="nav-icon">👨‍🏫</span> Teachers
        </a>

        <div class="nav-section-label">Curriculum</div>
        <a href="{{ route('admin.subjects') }}" class="nav-item {{ request()->routeIs('admin.subjects') ? 'active' : '' }}">
            <span class="nav-icon">📚</span> Classes & Subjects
        </a>
        <a href="{{ route('admin.content') }}" class="nav-item {{ request()->routeIs('admin.content') ? 'active' : '' }}">
            <span class="nav-icon">🎬</span> Content
        </a>
        <a href="{{ route('admin.tests') }}" class="nav-item {{ request()->routeIs('admin.tests') ? 'active' : '' }}">
            <span class="nav-icon">🧪</span> Tests & Questions
        </a>

        <div class="nav-section-label">Business</div>
        <a href="{{ route('admin.subscriptions') }}" class="nav-item {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}">
            <span class="nav-icon">💳</span> Subscriptions
        </a>
        <a href="{{ route('admin.notifications') }}" class="nav-item {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
            <span class="nav-icon">🔔</span> Notifications
        </a>

        <div class="nav-section-label">Platform</div>
        <a href="{{ route('home') }}" class="nav-item" target="_blank">
            <span class="nav-icon">🌐</span> View Website
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr($adminUser['name'] ?? 'A', 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ $adminUser['name'] ?? 'Admin' }}</div>
                <div class="sidebar-user-role">Super Admin</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">⏻</button>
            </form>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
        <button class="btn btn-outline btn-sm" id="sidebar-toggle" onclick="toggleSidebar()" style="display:none">☰</button>
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-subtitle">@yield('page-subtitle', '')</div>
        </div>
        <div class="topbar-actions">
            @yield('topbar-actions')
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="content">
        @yield('content')
    </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<script>
const TOKEN = document.querySelector('meta[name="api-token"]').content;
const API   = document.querySelector('meta[name="api-base"]').content;
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;

// ── API helper ──────────────────────────────────────────────
async function api(method, path, body = null) {
    const opts = {
        method,
        headers: { 
            Authorization: 'Bearer ' + TOKEN, 
            Accept: 'application/json',
            'X-CSRF-TOKEN': CSRF
        }
    };
    if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
    const res  = await fetch(API + path, opts);
    const json = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(json.message || 'API error');
    return json;
}

// ── Toast ───────────────────────────────────────────────────
function toast(msg, type = 'info') {
    const icons = { success: '✓', error: '✕', info: 'ℹ' };
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.innerHTML = `<span>${icons[type]}</span>${msg}`;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => t.remove(), 4500);
}

// ── Modal helpers ────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
window.addEventListener('click', e => {
    if (e.target.classList.contains('modal-backdrop')) e.target.classList.remove('open');
});
window.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
});

// ── Sidebar toggle ───────────────────────────────────────────
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); }

// Close sidebar on click outside
document.addEventListener('click', e => {
    const sb = document.getElementById('sidebar');
    if (sb.classList.contains('open') && !sb.contains(e.target) && !e.target.closest('#sidebar-toggle')) {
        sb.classList.remove('open');
    }
});

// ── Tabs ─────────────────────────────────────────────────────
function switchTab(tabId, groupClass) {
    document.querySelectorAll('.' + groupClass + ' .tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.' + groupClass + ' .tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// ── Shared page init hook ────────────────────────────────────
window._pageInit = function() {};
document.addEventListener('DOMContentLoaded', () => window._pageInit());
</script>

@yield('scripts')
</body>
</html>
