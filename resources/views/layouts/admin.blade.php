<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>@yield('title', 'Dashboard') — VidyaMission Admin</title>
<meta name="api-token" content="{{ $token ?? session('admin_token') }}">
<meta name="api-base"  content="{{ $apiBase ?? '/api' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #5B45FF; 
    --primary-light: #7F6BFF; 
    --primary-dark: #432ED8;
    --secondary: #06B6D4; 
    --accent: #F43F5E;
    --success: #10B981; 
    --warning: #F59E0B; 
    --danger: #EF4444;

    --sidebar-bg: #FFFFFF; 
    --sidebar-width: 250px; 
    --topbar-height: 64px;
    --bottom-nav-height: 60px;

    /* Page surfaces */
    --bg: #F4F6FB; 
    --bg-card: #FFFFFF; 
    --bg-surface: #F8FAFC;

    /* Borders */
    --border: rgba(91,69,255,0.15); 
    --border-light: rgba(0,0,0,0.08);

    /* Text */
    --text: #0F172A; 
    --text-muted: #64748B; 
    --text-secondary: #334155;

    /* Shadows */
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
    --shadow-md: 0 4px 20px rgba(0,0,0,0.07);
    --shadow-lg: 0 12px 36px rgba(0,0,0,0.12);
    --shadow-purple: 0 4px 20px rgba(91,69,255,0.28);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; -webkit-tap-highlight-color: transparent; }
body {
    font-family: 'Inter', 'Noto Sans Bengali', 'Hind Siliguri', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
    overflow-x: hidden;
}
.bengali-text, .bn-font { font-family: 'Kalpurush', 'Noto Sans Bengali', 'Hind Siliguri', sans-serif; }

/* ══════════════════════ SIDEBAR ══════════════════════ */
.sidebar {
    width: var(--sidebar-width);
    min-width: var(--sidebar-width);
    background: var(--sidebar-bg);
    border-right: 1px solid var(--border-light);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1000;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 2px 0 12px rgba(0,0,0,0.04);
}
.sidebar-logo {
    padding: 1.125rem 1.25rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
}
.sidebar-logo-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
}
.sidebar-logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: #fff;
    box-shadow: var(--shadow-purple);
    flex-shrink: 0;
}
.sidebar-logo-text { font-family: 'Poppins', sans-serif; font-size: 1.05rem; font-weight: 800; color: var(--text); }
.sidebar-logo-text span { color: var(--primary); }
.sidebar-logo-badge { font-size: 0.6rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 6px; background: rgba(91,69,255,0.1); color: var(--primary); border: 1px solid rgba(91,69,255,0.2); letter-spacing: 0.05em; }

.sidebar-close-mobile {
    display: none;
    background: none;
    border: none;
    font-size: 1.4rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
}

.sidebar-nav {
    flex: 1;
    padding: 0.75rem 0.65rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    -webkit-overflow-scrolling: touch;
}
.nav-section-label {
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0.65rem 0.75rem 0.2rem;
    margin-top: 0.25rem;
    opacity: 0.8;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.65rem 0.85rem;
    border-radius: 10px;
    text-decoration: none;
    color: var(--text-secondary);
    font-size: 0.86rem;
    font-weight: 600;
    transition: all 0.18s ease;
    position: relative;
}
.nav-item:hover { background: rgba(91,69,255,0.06); color: var(--primary); }
.nav-item.active { background: rgba(91,69,255,0.1); color: var(--primary); font-weight: 700; }
.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    bottom: 20%;
    width: 3.5px;
    border-radius: 0 4px 4px 0;
    background: var(--primary);
}
.nav-icon { width: 20px; text-align: center; font-size: 1rem; flex-shrink: 0; }

.sidebar-footer { padding: 0.875rem 0.75rem; border-top: 1px solid var(--border-light); }
.sidebar-user { display: flex; align-items: center; gap: 0.65rem; padding: 0.7rem; border-radius: 12px; background: var(--bg-surface); border: 1px solid var(--border-light); }
.sidebar-avatar { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; flex-shrink: 0; }
.sidebar-user-info { flex: 1; min-width: 0; }
.sidebar-user-name { font-size: 0.82rem; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sidebar-user-role { font-size: 0.68rem; color: var(--text-muted); }

/* Backdrop for mobile drawer */
.sidebar-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 999;
    display: none;
    opacity: 0;
    transition: opacity 0.28s ease;
}
.sidebar-backdrop.show { display: block; opacity: 1; }

/* ══════════════════════ MAIN ══════════════════════ */
.main {
    margin-left: var(--sidebar-width);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-width: 0;
}

/* ══════════════════════ TOPBAR ══════════════════════ */
.topbar {
    height: var(--topbar-height);
    background: #FFFFFF;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    gap: 0.85rem;
    position: sticky;
    top: 0;
    z-index: 500;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}
.topbar-toggle {
    display: none;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--bg-surface);
    border: 1px solid var(--border-light);
    color: var(--text);
    font-size: 1.2rem;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
}
.topbar-toggle:hover { background: rgba(91,69,255,0.08); color: var(--primary); border-color: var(--primary); }

.topbar-heading { flex: 1; min-width: 0; }
.topbar-title { font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 800; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2; }
.topbar-subtitle { font-size: 0.76rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.topbar-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }

/* ══════════════════════ CONTENT ══════════════════════ */
.content {
    flex: 1;
    padding: 1.25rem 1.5rem;
    min-width: 0;
}

/* ══════════════════════ CARDS ══════════════════════ */
.card {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.card-title { font-size: 0.92rem; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 0.4rem; }
.card-body { padding: 1.25rem; }

/* ══════════════════════ STAT CARDS ══════════════════════ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    padding: 1.125rem 1.25rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--card-accent, linear-gradient(90deg, var(--primary), var(--primary-light)));
}
.stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem; }
.stat-icon { font-size: 1.6rem; line-height: 1; }
.stat-value { font-family: 'Poppins', sans-serif; font-size: 1.65rem; font-weight: 800; line-height: 1.1; color: var(--text); }
.stat-label { font-size: 0.78rem; color: var(--text-muted); font-weight: 600; margin-top: 0.25rem; }

/* ══════════════════════ TABLE ══════════════════════ */
.table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}
table { width: 100%; border-collapse: collapse; text-align: left; }
thead tr { border-bottom: 1px solid var(--border-light); background: rgba(91,69,255,0.02); }
th {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    padding: 0.75rem 1rem;
    white-space: nowrap;
}
td {
    padding: 0.8rem 1rem;
    font-size: 0.85rem;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    vertical-align: middle;
    color: var(--text);
}
tbody tr:hover { background: rgba(91,69,255,0.025); }
tbody tr:last-child td { border-bottom: none; }

/* ══════════════════════ BADGES ══════════════════════ */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 100px;
    font-size: 0.72rem;
    font-weight: 700;
    white-space: nowrap;
}
.badge-success{ background: rgba(16,185,129,0.12); color: #059669; border: 1px solid rgba(16,185,129,0.25); }
.badge-warning{ background: rgba(245,158,11,0.12); color: #D97706; border: 1px solid rgba(245,158,11,0.25); }
.badge-danger { background: rgba(239,68,68,0.12);  color: #DC2626; border: 1px solid rgba(239,68,68,0.25); }
.badge-info   { background: rgba(6,182,212,0.12); color: #0891B2; border: 1px solid rgba(6,182,212,0.25); }
.badge-purple { background: rgba(91,69,255,0.12); color: var(--primary); border: 1px solid rgba(91,69,255,0.25); }
.badge-neutral{ background: rgba(0,0,0,0.06);     color: var(--text-muted); border: 1px solid rgba(0,0,0,0.08); }

/* ══════════════════════ BUTTONS ══════════════════════ */
.btn {
    min-height: 38px;
    padding: 0.5rem 1rem;
    border-radius: 9px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-family: 'Inter', sans-serif;
    white-space: nowrap;
    text-decoration: none;
    user-select: none;
}
.btn:active { transform: scale(0.98); }
.btn-sm { min-height: 32px; padding: 0.32rem 0.65rem; font-size: 0.76rem; border-radius: 7px; }
.btn-lg { min-height: 44px; padding: 0.75rem 1.35rem; font-size: 0.92rem; }

.btn-primary { background: var(--primary); color: #fff; box-shadow: 0 2px 8px rgba(91,69,255,0.28); }
.btn-primary:hover { background: var(--primary-dark); box-shadow: 0 4px 16px rgba(91,69,255,0.38); }
.btn-success { background: rgba(16,185,129,0.12); color: #059669; border: 1px solid rgba(16,185,129,0.25); }
.btn-success:hover { background: rgba(16,185,129,0.22); }
.btn-danger { background: rgba(239,68,68,0.1); color: #DC2626; border: 1px solid rgba(239,68,68,0.25); }
.btn-danger:hover { background: rgba(239,68,68,0.18); }
.btn-outline { background: transparent; border: 1px solid var(--border-light); color: var(--text-secondary); }
.btn-outline:hover { border-color: var(--primary); color: var(--primary); background: rgba(91,69,255,0.04); }

/* ══════════════════════ SEARCH / FILTER ══════════════════════ */
.search-bar { display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1rem; flex-wrap: wrap; }
.search-input-wrap { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.95rem; }
.search-input {
    width: 100%;
    min-height: 42px;
    padding: 0.55rem 1rem 0.55rem 2.4rem;
    background: #FFFFFF;
    border: 1px solid var(--border-light);
    border-radius: 10px;
    color: var(--text);
    font-size: 0.88rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: all 0.2s;
}
.search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(91,69,255,0.12); }
.filter-select {
    min-height: 42px;
    padding: 0.55rem 1rem;
    background: #FFFFFF;
    border: 1px solid var(--border-light);
    border-radius: 10px;
    color: var(--text-secondary);
    font-size: 0.86rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    cursor: pointer;
}
.filter-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(91,69,255,0.12); }

/* ══════════════════════ MODAL ══════════════════════ */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(5px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    overflow-y: auto;
}
.modal-backdrop.open { display: flex; }
.modal {
    background: #FFFFFF;
    border: 1px solid var(--border-light);
    border-radius: 20px;
    width: 100%;
    max-width: 540px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-lg);
    animation: modal-in 0.24s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
@keyframes modal-in { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: none; } }
.modal-header {
    padding: 1.125rem 1.35rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.modal-title { font-size: 1rem; font-weight: 700; color: var(--text); }
.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--bg-surface);
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    line-height: 1;
}
.modal-close:hover { color: var(--danger); background: rgba(239,68,68,0.1); }
.modal-body {
    padding: 1.25rem 1.35rem;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    flex: 1;
}
.modal-footer {
    padding: 0.9rem 1.35rem;
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: flex-end;
    gap: 0.65rem;
    flex-shrink: 0;
    background: var(--bg-surface);
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
}

/* ══════════════════════ FORM ══════════════════════ */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; }
.form-group { margin-bottom: 0.95rem; }
.form-label {
    display: block;
    font-size: 0.73rem;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 0.35rem;
}
.form-control {
    width: 100%;
    min-height: 42px;
    padding: 0.55rem 0.85rem;
    background: #FFFFFF;
    border: 1px solid rgba(0,0,0,0.12);
    border-radius: 10px;
    color: var(--text);
    font-size: 0.88rem;
    font-family: 'Inter', 'Noto Sans Bengali', sans-serif;
    transition: all 0.2s;
    outline: none;
}
.form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(91,69,255,0.12); }
.form-control::placeholder { color: var(--text-muted); }
select.form-control { cursor: pointer; }
textarea.form-control { resize: vertical; min-height: 85px; }

/* ══════════════════════ TOAST ══════════════════════ */
.toast-container {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 90vw;
}
.toast {
    padding: 0.8rem 1.15rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    min-width: 260px;
    box-shadow: var(--shadow-lg);
    animation: toast-in 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
}
@keyframes toast-in { from { opacity: 0; transform: translateY(12px) scale(0.96); } to { opacity: 1; transform: none; } }
.toast-success { background: #F0FDF4; border-color: #A7F3D0; color: #065F46; }
.toast-error   { background: #FEF2F2; border-color: #FECACA; color: #991B1B; }
.toast-info    { background: #F5F3FF; border-color: #DDD6FE; color: #5B21B6; }

/* ══════════════════════ LOADING & EMPTY ══════════════════════ */
.loading-row td { text-align: center; padding: 2.5rem; color: var(--text-muted); }
.spinner-sm {
    width: 18px;
    height: 18px;
    border: 2.5px solid rgba(91,69,255,0.15);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    display: inline-block;
    vertical-align: middle;
}
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); }
.empty-state .empty-icon { font-size: 2.6rem; margin-bottom: 0.75rem; opacity: 0.6; }
.empty-state p { font-size: 0.86rem; }

/* ══════════════════════ PAGINATION ══════════════════════ */
.pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.25rem;
    border-top: 1px solid var(--border-light);
    font-size: 0.8rem;
    color: var(--text-muted);
    flex-wrap: wrap;
    gap: 0.5rem;
}
.pagination-btns { display: flex; gap: 0.35rem; }
.page-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 0.55rem;
    border-radius: 8px;
    background: #FFFFFF;
    border: 1px solid var(--border-light);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.page-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); }
.page-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.page-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }

/* ══════════════════════ TABS ══════════════════════ */
.tabs {
    display: flex;
    border-bottom: 2px solid var(--border-light);
    margin-bottom: 1.25rem;
    gap: 0.25rem;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
}
.tab-btn {
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 0.86rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s;
    border-bottom: 2.5px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-family: 'Inter', sans-serif;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 700; }
.tab-panel { display: none; }
.tab-panel.active { display: block; animation: fade-in 0.2s ease; }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }

/* ══════════════════════ MOBILE BOTTOM NAV ══════════════════════ */
.mobile-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--bottom-nav-height);
    background: #FFFFFF;
    border-top: 1px solid var(--border-light);
    z-index: 900;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    padding: 0 0.5rem;
    justify-content: space-around;
    align-items: center;
}
.bnav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.35rem 0.6rem;
    border-radius: 8px;
    gap: 0.18rem;
    flex: 1;
    transition: all 0.18s;
}
.bnav-item .bnav-icon { font-size: 1.15rem; line-height: 1; }
.bnav-item.active { color: var(--primary); font-weight: 700; }
.bnav-item:active { transform: scale(0.92); }

/* ══════════════════════ RESPONSIVE BREAKPOINTS ══════════════════════ */
@media (max-width: 992px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: none; box-shadow: var(--shadow-lg); }
    .sidebar-close-mobile { display: block; }
    .main { margin-left: 0; }
    .topbar-toggle { display: flex; }
    .form-row { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
}

@media (max-width: 768px) {
    body { padding-bottom: var(--bottom-nav-height); }
    .mobile-bottom-nav { display: flex; }
    .content { padding: 1rem 0.85rem; }
    .topbar { padding: 0 1rem; }
    .card-header { padding: 0.85rem 1rem; }
    .card-body { padding: 1rem; }
    .toast-container { bottom: calc(var(--bottom-nav-height) + 1rem); right: 1rem; left: 1rem; max-width: none; }
    
    /* Modal full/bottom sheet on mobile */
    .modal-backdrop { padding: 0; align-items: flex-end; }
    .modal {
        max-width: 100vw;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        max-height: 92vh;
        animation: modal-sheet-in 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes modal-sheet-in { from { transform: translateY(100%); } to { transform: none; } }
}
</style>
</head>
<body>

<!-- BACKDROP FOR MOBILE SIDEBAR -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo-brand">
            <div class="sidebar-logo-icon">V</div>
            <div>
                <div class="sidebar-logo-text">Vidya<span>Mission</span></div>
                <div class="sidebar-logo-badge">ADMIN CONTROL</div>
            </div>
        </a>
        <button class="sidebar-close-mobile" onclick="closeSidebar()" aria-label="Close sidebar">✕</button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span> Dashboard
        </a>

        <div class="nav-section-label">Users & Staff</div>
        <a href="{{ route('admin.students') }}" class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
            <span class="nav-icon">👨‍🎓</span> Students
        </a>
        <a href="{{ route('admin.teachers') }}" class="nav-item {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
            <span class="nav-icon">👨‍🏫</span> Teachers
        </a>
        <a href="{{ route('admin.admins') }}" class="nav-item {{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
            <span class="nav-icon">🛡️</span> Admins
        </a>

        <div class="nav-section-label">Curriculum</div>
        <a href="{{ route('admin.subjects') }}" class="nav-item {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
            <span class="nav-icon">📚</span> Classes & Subjects
        </a>
        <a href="{{ route('admin.content') }}" class="nav-item {{ request()->routeIs('admin.content*') ? 'active' : '' }}">
            <span class="nav-icon">🎬</span> Content & Classes
        </a>
        <a href="{{ route('admin.tests') }}" class="nav-item {{ request()->routeIs('admin.tests*') ? 'active' : '' }}">
            <span class="nav-icon">🧪</span> Tests & Questions
        </a>

        <div class="nav-section-label">Business & Reach</div>
        <a href="{{ route('admin.subscriptions') }}" class="nav-item {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}">
            <span class="nav-icon">💳</span> Subscriptions
        </a>
        <a href="{{ route('admin.notifications') }}" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
            <span class="nav-icon">🔔</span> Broadcast Alerts
        </a>

        <div class="nav-section-label">Quick Links</div>
        <a href="{{ route('docs') }}" class="nav-item" target="_blank" style="color:var(--primary)">
            <span class="nav-icon">⚡</span> Swagger API Docs ↗
        </a>
        <a href="{{ route('home') }}" class="nav-item" target="_blank">
            <span class="nav-icon">🌐</span> Website Home ↗
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr($adminUser['name'] ?? 'A', 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ $adminUser['name'] ?? 'Super Admin' }}</div>
                <div class="sidebar-user-role">{{ $adminUser['email'] ?? 'admin' }}</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="padding:0.3rem 0.55rem;color:var(--danger);border-color:rgba(239,68,68,0.25);background:rgba(239,68,68,0.05)" title="Logout">
                    ⏻
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Open Navigation Menu">☰</button>
        <div class="topbar-heading">
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-subtitle">@yield('page-subtitle', 'VidyaMission Control Center')</div>
        </div>
        <div class="topbar-actions">
            @yield('topbar-actions')
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="border-color:rgba(239,68,68,0.2);color:var(--danger);background:rgba(239,68,68,0.04)">
                    <span>⏻</span> <span class="hide-mobile">Logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="content">
        @yield('content')
    </main>
</div>

<!-- MOBILE BOTTOM NAVIGATION -->
<nav class="mobile-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="bnav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="bnav-icon">📊</span>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.subjects') }}" class="bnav-item {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
        <span class="bnav-icon">📚</span>
        <span>Subjects</span>
    </a>
    <a href="{{ route('admin.content') }}" class="bnav-item {{ request()->routeIs('admin.content*') ? 'active' : '' }}">
        <span class="bnav-icon">🎬</span>
        <span>Content</span>
    </a>
    <a href="{{ route('admin.students') }}" class="bnav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
        <span class="bnav-icon">👨‍🎓</span>
        <span>Students</span>
    </a>
    <button class="bnav-item" onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer">
        <span class="bnav-icon">☰</span>
        <span>Menu</span>
    </button>
</nav>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<script>
const TOKEN = document.querySelector('meta[name="api-token"]')?.content || '';
const API   = document.querySelector('meta[name="api-base"]')?.content || '/api';
const CSRF  = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── API helper (JSON & FormData supported) ──────────────────
async function api(method, path, body = null) {
    const isFormData = body instanceof FormData;
    const headers = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': CSRF
    };
    if (TOKEN) headers['Authorization'] = 'Bearer ' + TOKEN;
    if (body && !isFormData) headers['Content-Type'] = 'application/json';

    const opts = {
        method,
        headers,
        body: isFormData ? body : (body ? JSON.stringify(body) : null)
    };

    const res  = await fetch(API + path, opts);
    const json = await res.json().catch(() => ({}));
    if (!res.ok) {
        let msg = json.message || 'API request failed';
        if (json.errors) {
            const firstError = Object.values(json.errors)[0];
            if (Array.isArray(firstError)) msg = firstError[0];
        }
        throw new Error(msg);
    }
    return json;
}

// ── Toast ───────────────────────────────────────────────────
function toast(msg, type = 'info', duration = 4000) {
    const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠️' };
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.innerHTML = `<span>${icons[type] || 'ℹ'}</span><span>${msg}</span>`;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateY(10px)';
        t.style.transition = 'all 0.25s ease';
        setTimeout(() => t.remove(), 250);
    }, duration);
}

// ── Modal helpers ────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('open');
        if (!document.querySelector('.modal-backdrop.open')) {
            document.body.style.overflow = '';
        }
    }
}
window.addEventListener('click', e => {
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.remove('open');
        if (!document.querySelector('.modal-backdrop.open')) document.body.style.overflow = '';
    }
});
window.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
        document.body.style.overflow = '';
    }
});

// ── Sidebar toggle ───────────────────────────────────────────
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const bd = document.getElementById('sidebarBackdrop');
    const isOpen = sb.classList.toggle('open');
    if (isOpen) {
        bd.classList.add('show');
        document.body.style.overflow = 'hidden';
    } else {
        bd.classList.remove('show');
        document.body.style.overflow = '';
    }
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('show');
    document.body.style.overflow = '';
}

// ── Tabs ─────────────────────────────────────────────────────
function switchTab(tabId, groupClass) {
    document.querySelectorAll('.' + groupClass + ' .tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.' + groupClass + ' .tab-panel').forEach(p => p.classList.remove('active'));
    const btn = document.querySelector(`[data-tab="${tabId}"]`);
    const panel = document.getElementById(tabId);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
}

// ── Shared page init hook ────────────────────────────────────
window._pageInit = function() {};
document.addEventListener('DOMContentLoaded', () => window._pageInit());
</script>

@yield('scripts')
</body>
</html>
