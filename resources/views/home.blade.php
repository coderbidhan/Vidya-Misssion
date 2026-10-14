<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VidyaMission — West Bengal's #1 Student Learning Platform</title>
<meta name="description" content="VidyaMission helps West Bengal students excel with recorded video classes, live sessions, PDF notes, and chapter tests. Start your free 3-day trial today.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #6D4AFF;
    --primary-light: #8B6BFF;
    --primary-dark: #5536D4;
    --secondary: #0ABCBC;
    --accent: #FF5C7A;
    --gold: #F59E0B;
    --green: #10B981;

    /* Light theme surfaces */
    --bg: #F7F8FF;
    --bg-card: #FFFFFF;
    --bg-surface: #EEF0FF;
    --bg-hero: #F0EDFF;

    /* Text */
    --text-heading: #0D0F28;
    --text-body: #374151;
    --text-muted: #6B7280;
    --text-light: #9CA3AF;

    /* Borders */
    --border: rgba(109, 74, 255, 0.12);
    --border-light: rgba(0, 0, 0, 0.07);

    /* Shadows */
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
    --shadow-lg: 0 20px 40px rgba(0,0,0,0.1);
    --shadow-purple: 0 8px 30px rgba(109,74,255,0.25);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-body); overflow-x: hidden; }

/* ════════════════════════
   NAVBAR
════════════════════════ */
.nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    padding: 0.875rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border-light);
    box-shadow: 0 1px 20px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s;
}
.nav.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.1); }
.nav-logo { display: flex; align-items: center; gap: 0.625rem; text-decoration: none; }
.nav-logo-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 800; color: #fff;
    box-shadow: 0 4px 12px rgba(109, 74, 255, 0.35);
}
.nav-logo-text { font-family: 'Poppins', sans-serif; font-size: 1.15rem; font-weight: 800; color: var(--text-heading); }
.nav-logo-text span { color: var(--primary); }
.nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; }
.nav-links a { color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.nav-links a:hover { color: var(--text-heading); }
.nav-cta { display: flex; align-items: center; gap: 0.75rem; }
.btn-ghost {
    padding: 0.5rem 1rem; border-radius: 8px; background: transparent;
    border: 1.5px solid var(--border); color: var(--text-muted);
    font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none;
}
.btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
.btn-primary-sm {
    padding: 0.55rem 1.25rem; border-radius: 8px;
    background: var(--primary); color: #fff;
    font-size: 0.8rem; font-weight: 700; border: none;
    cursor: pointer; transition: all 0.25s; text-decoration: none; display: inline-block;
    box-shadow: 0 4px 14px rgba(109,74,255,0.3);
}
.btn-primary-sm:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(109,74,255,0.4); }

/* ════════════════════════
   HERO
════════════════════════ */
.hero {
    min-height: 100vh;
    background: linear-gradient(160deg, #FDFCFF 0%, #F0EDFF 40%, #E8F4FF 80%, #F5FFF8 100%);
    display: flex; align-items: center;
    padding: 6rem 2rem 4rem;
    position: relative; overflow: hidden;
}
/* Soft decorative blobs */
.blob {
    position: absolute; border-radius: 50%; pointer-events: none;
    filter: blur(60px); opacity: 0.55;
}
.blob-1 { width: 500px; height: 500px; top: -150px; right: -100px; background: radial-gradient(circle, #C4B5FD, transparent 70%); }
.blob-2 { width: 350px; height: 350px; bottom: -100px; left: 5%; background: radial-gradient(circle, #BAE6FD, transparent 70%); }
.blob-3 { width: 250px; height: 250px; top: 30%; left: 40%; background: radial-gradient(circle, #D1FAE5, transparent 70%); }

/* Grid pattern overlay */
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(109,74,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(109,74,255,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

.hero-container { max-width: 1200px; margin: 0 auto; width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; position: relative; z-index: 1; }

.hero-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.35rem 0.875rem; border-radius: 100px;
    background: rgba(109,74,255,0.09); border: 1.5px solid rgba(109,74,255,0.2);
    font-size: 0.78rem; font-weight: 700; color: var(--primary);
    margin-bottom: 1.25rem;
}
.badge-pulse { width: 8px; height: 8px; border-radius: 50%; background: var(--green); animation: pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.5);opacity:0.6} }

.hero-title {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(2rem, 4.5vw, 3.25rem);
    font-weight: 900; line-height: 1.15;
    color: var(--text-heading);
    margin-bottom: 1.25rem; letter-spacing: -0.02em;
}
.hero-title .underline-wrap { position: relative; display: inline-block; }
.hero-title .underline-wrap::after {
    content: '';
    position: absolute; bottom: 2px; left: 0; right: 0; height: 8px;
    background: linear-gradient(90deg, rgba(109,74,255,0.25), rgba(109,74,255,0.15));
    border-radius: 4px; z-index: -1;
}
.hero-title .gradient-text {
    background: linear-gradient(135deg, var(--primary), #E040FB);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.05rem; line-height: 1.75; color: var(--text-muted);
    margin-bottom: 2rem; max-width: 480px;
}
.hero-actions { display: flex; gap: 0.875rem; align-items: center; flex-wrap: wrap; margin-bottom: 2.5rem; }

.btn-hero {
    padding: 0.875rem 2rem; border-radius: 12px;
    background: var(--primary); color: #fff;
    font-size: 0.95rem; font-weight: 700; border: none;
    cursor: pointer; transition: all 0.3s; text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.5rem;
    box-shadow: var(--shadow-purple);
}
.btn-hero:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 12px 36px rgba(109,74,255,0.4); }

.btn-hero-outline {
    padding: 0.875rem 1.75rem; border-radius: 12px;
    background: #fff; border: 2px solid var(--border-light);
    color: var(--text-heading); font-size: 0.95rem; font-weight: 600;
    cursor: pointer; transition: all 0.3s; text-decoration: none;
    display: inline-flex; align-items: center; gap: 0.5rem;
    box-shadow: var(--shadow-sm);
}
.btn-hero-outline:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }

.hero-trust { display: flex; align-items: center; gap: 1rem; }
.hero-avatars { display: flex; }
.hero-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    border: 2px solid #fff; margin-left: -8px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 700; color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.hero-trust-text { font-size: 0.825rem; color: var(--text-muted); }
.hero-trust-text strong { color: var(--text-heading); }

/* ─── Hero Visual ─── */
.hero-visual { position: relative; }
.app-card {
    background: #fff; border-radius: 24px; padding: 1.5rem;
    box-shadow: 0 24px 60px rgba(0,0,0,0.12), 0 4px 16px rgba(109,74,255,0.1);
    border: 1px solid rgba(0,0,0,0.06);
    animation: float-card 6s ease-in-out infinite;
    max-width: 380px; margin: 0 auto;
}
@keyframes float-card { 0%,100%{transform:translateY(0) rotate(1deg)} 50%{transform:translateY(-14px) rotate(-0.5deg)} }
.app-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
.app-title { font-weight: 700; font-size: 0.9rem; color: var(--text-heading); }
.app-badge-green { font-size: 0.68rem; padding: 0.2rem 0.55rem; border-radius: 100px; background: #D1FAE5; color: #065F46; font-weight: 700; }
.app-subject-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 0.625rem; margin-bottom: 1.125rem; }
.app-subject {
    border-radius: 12px; padding: 0.7rem 0.4rem;
    text-align: center; font-size: 0.68rem; font-weight: 700;
    cursor: pointer; transition: transform 0.2s;
}
.app-subject:hover { transform: scale(1.04); }
.app-subject .s-emoji { font-size: 1.2rem; display: block; margin-bottom: 0.2rem; }
.progress-section { background: var(--bg-surface); border-radius: 12px; padding: 0.875rem; }
.progress-label { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.4rem; display: flex; justify-content: space-between; font-weight: 600; }
.progress-bar { height: 7px; border-radius: 100px; background: rgba(0,0,0,0.06); overflow: hidden; }
.progress-fill { height: 100%; border-radius: 100px; background: linear-gradient(90deg, var(--primary), #A78BFA); }
.progress-info { font-size: 0.68rem; color: var(--text-muted); margin-top: 0.45rem; }

/* Floating mini-cards */
.mini-card {
    position: absolute; background: #fff; border-radius: 12px;
    padding: 0.625rem 0.875rem; box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.06); white-space: nowrap;
    display: flex; align-items: center; gap: 0.5rem;
}
.mc-1 { top: -15px; right: -20px; animation: fc-float 7s ease-in-out infinite; }
.mc-2 { bottom: 10px; left: -30px; animation: fc-float 5s ease-in-out infinite 1.5s; }
@keyframes fc-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
.mc-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
.mc-label { font-size: 0.68rem; color: var(--text-muted); font-weight: 600; }
.mc-value { font-size: 0.825rem; font-weight: 800; color: var(--text-heading); }

/* ════════════════════════
   STATS BAR
════════════════════════ */
.stats-bar {
    background: var(--primary); padding: 2rem;
}
.stats-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 1.5rem; }
.stat-item { text-align: center; }
.stat-number { font-family: 'Poppins', sans-serif; font-size: 2rem; font-weight: 800; color: #fff; }
.stat-label { font-size: 0.8rem; color: rgba(255,255,255,0.75); font-weight: 500; margin-top: 0.15rem; }

/* ════════════════════════
   SECTIONS
════════════════════════ */
.section { padding: 5rem 2rem; }
.section-alt { background: var(--bg-surface); }
.section-container { max-width: 1200px; margin: 0 auto; }
.section-tag {
    display: inline-flex; align-items: center; gap: 0.35rem;
    font-size: 0.75rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--primary); margin-bottom: 0.75rem;
}
.section-tag::before { content: ''; width: 16px; height: 2px; background: var(--primary); border-radius: 2px; }
.section-title {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(1.7rem, 3vw, 2.4rem); font-weight: 800;
    color: var(--text-heading); margin-bottom: 1rem; letter-spacing: -0.02em; line-height: 1.2;
}
.section-subtitle { font-size: 1rem; color: var(--text-muted); max-width: 520px; line-height: 1.75; }
.section-header { margin-bottom: 3rem; }

/* ════════════════════════
   SUBJECTS
════════════════════════ */
.subjects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: 1rem; }
.subject-card {
    background: #fff; border-radius: 18px; padding: 1.5rem 1rem;
    text-align: center; cursor: pointer;
    box-shadow: var(--shadow-sm);
    border: 1.5px solid var(--border-light);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    position: relative; overflow: hidden;
}
.subject-card::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 4px;
    background: var(--card-color, var(--primary));
    transform: scaleX(0); transition: transform 0.3s; transform-origin: left;
}
.subject-card:hover { transform: translateY(-6px); box-shadow: 0 16px 32px rgba(0,0,0,0.1); border-color: transparent; }
.subject-card:hover::after { transform: scaleX(1); }
.subject-emoji { font-size: 2.2rem; margin-bottom: 0.75rem; display: block; }
.subject-name { font-size: 0.875rem; font-weight: 700; color: var(--text-heading); }
.subject-sub  { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem; }

/* ════════════════════════
   FEATURES
════════════════════════ */
.features-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; }
.feature-card {
    background: #fff; border-radius: 18px; padding: 1.75rem;
    border: 1.5px solid var(--border-light); box-shadow: var(--shadow-sm);
    transition: all 0.3s;
}
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 16px 36px rgba(0,0,0,0.09); border-color: var(--border); }
.feature-icon-wrap {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 1.125rem;
}
.feature-title { font-size: 1rem; font-weight: 700; color: var(--text-heading); margin-bottom: 0.5rem; }
.feature-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.65; }

/* ════════════════════════
   HOW IT WORKS
════════════════════════ */
.steps-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; }
.step { display: flex; gap: 1.25rem; align-items: flex-start; }
.step-num {
    width: 46px; height: 46px; min-width: 46px; border-radius: 12px;
    background: var(--primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 800;
    box-shadow: 0 6px 18px rgba(109,74,255,0.3);
}
.step h3 { font-size: 1rem; font-weight: 700; color: var(--text-heading); margin-bottom: 0.4rem; }
.step p { font-size: 0.85rem; color: var(--text-muted); line-height: 1.65; }

/* ════════════════════════
   CLASS LEVELS SECTION
════════════════════════ */
.classes-grid { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.class-pill {
    padding: 0.625rem 1.25rem; border-radius: 100px;
    background: #fff; border: 1.5px solid var(--border-light);
    font-size: 0.875rem; font-weight: 700; color: var(--text-heading);
    box-shadow: var(--shadow-sm); cursor: pointer;
    transition: all 0.2s;
}
.class-pill:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow-purple); }

/* ════════════════════════
   PRICING
════════════════════════ */
.pricing-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; }
.pricing-card {
    background: #fff; border-radius: 20px; padding: 2rem;
    border: 1.5px solid var(--border-light); box-shadow: var(--shadow-sm);
    transition: all 0.3s; position: relative;
}
.pricing-card.featured {
    border-color: var(--primary); background: var(--primary);
    box-shadow: 0 20px 50px rgba(109,74,255,0.3);
}
.pricing-badge {
    position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, var(--gold), #FBBF24);
    color: #fff; font-size: 0.72rem; font-weight: 800;
    padding: 0.3rem 0.9rem; border-radius: 100px;
    box-shadow: 0 4px 12px rgba(245,158,11,0.4);
}
.pricing-name { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem; }
.pricing-name-light { color: rgba(255,255,255,0.75); }
.pricing-name-dark { color: var(--text-muted); }
.pricing-price { font-family: 'Poppins', sans-serif; font-size: 2.75rem; font-weight: 900; line-height: 1; }
.pricing-price-light { color: #fff; }
.pricing-price-dark { color: var(--text-heading); }
.pricing-price sub { font-size: 1rem; font-weight: 600; opacity: 0.8; }
.pricing-period { font-size: 0.8rem; margin-top: 0.25rem; margin-bottom: 1.5rem; }
.pricing-period-light { color: rgba(255,255,255,0.65); }
.pricing-period-dark { color: var(--text-muted); }
.pricing-features { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.75rem; }
.pricing-features li { display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; }
.li-check-light { color: rgba(255,255,255,0.85); }
.li-check-dark { color: var(--text-muted); }
.li-tick { font-weight: 900; }
.li-tick-light { color: #A5F3FC; }
.li-tick-dark { color: var(--green); }
.btn-pricing {
    width: 100%; padding: 0.875rem; border-radius: 12px;
    font-size: 0.9rem; font-weight: 700; cursor: pointer;
    transition: all 0.25s; border: none; display: block; text-align: center; text-decoration: none;
}
.btn-pricing-light { background: rgba(255,255,255,0.2); color: #fff; border: 2px solid rgba(255,255,255,0.4); }
.btn-pricing-light:hover { background: rgba(255,255,255,0.35); }
.btn-pricing-dark { background: var(--primary); color: #fff; box-shadow: 0 4px 18px rgba(109,74,255,0.3); }
.btn-pricing-dark:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 8px 28px rgba(109,74,255,0.4); }
.pricing-card:hover:not(.featured) { transform: translateY(-4px); border-color: var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.09); }

/* ════════════════════════
   CTA
════════════════════════ */
.cta-section { padding: 5rem 2rem; background: #fff; }
.cta-box {
    max-width: 860px; margin: 0 auto; text-align: center;
    background: linear-gradient(135deg, #F0EDFF, #E8F4FF);
    border: 1.5px solid var(--border); border-radius: 28px; padding: 4rem 3rem;
    position: relative; overflow: hidden;
}
.cta-box::before {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(109,74,255,0.15), transparent 70%);
}
.cta-title {
    font-family: 'Poppins', sans-serif; font-size: clamp(1.7rem, 3vw, 2.4rem);
    font-weight: 900; color: var(--text-heading); margin-bottom: 1rem; position: relative;
}
.cta-sub { font-size: 1rem; color: var(--text-muted); margin-bottom: 2rem; position: relative; }
.cta-actions { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; position: relative; }
.cta-note { margin-top: 1.5rem; font-size: 0.78rem; color: var(--text-light); }

/* ════════════════════════
   FOOTER
════════════════════════ */
footer { background: var(--text-heading); color: rgba(255,255,255,0.75); padding: 3.5rem 2rem 1.75rem; }
.footer-container { max-width: 1200px; margin: 0 auto; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem; margin-bottom: 3rem; }
.footer-logo { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1rem; }
.footer-logo-icon {
    width: 36px; height: 36px; border-radius: 9px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 800; color: #fff;
}
.footer-logo-text { font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 800; color: #fff; }
.footer-logo-text span { color: #A78BFA; }
.footer-desc { font-size: 0.85rem; line-height: 1.7; max-width: 280px; }
.footer-col h4 { font-size: 0.8rem; font-weight: 700; color: #fff; margin-bottom: 1rem; letter-spacing: 0.06em; text-transform: uppercase; }
.footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 0.55rem; }
.footer-col ul li a { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.85rem; transition: color 0.2s; }
.footer-col ul li a:hover { color: #A78BFA; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; }
.footer-bottom p { font-size: 0.8rem; color: rgba(255,255,255,0.4); }

/* ════════════════════════
   SCROLL ANIMATIONS
════════════════════════ */
.reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.65s ease, transform 0.65s ease; }
.reveal.in { opacity: 1; transform: none; }
.reveal-d1 { transition-delay: 0.1s; }
.reveal-d2 { transition-delay: 0.2s; }
.reveal-d3 { transition-delay: 0.3s; }
.reveal-d4 { transition-delay: 0.4s; }

/* ════════════════════════
   APP DOWNLOAD SECTION
════════════════════════ */
.app-download-section {
    padding: 3.5rem 2rem 3rem;
    position: relative;
    background: #FDFCFF;
}
.app-download-container {
    max-width: 1200px;
    margin: 0 auto;
    background: linear-gradient(145deg, #120B2F 0%, #1A1147 45%, #281768 100%);
    border-radius: 32px;
    padding: 3.75rem 3.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 28px 70px rgba(18, 11, 47, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 3.5rem;
    align-items: center;
}
.app-ambient-glow {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(80px);
}
.app-glow-top {
    width: 380px; height: 380px;
    top: -120px; right: 5%;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.45), transparent 70%);
}
.app-glow-bottom {
    width: 320px; height: 320px;
    bottom: -100px; left: -50px;
    background: radial-gradient(circle, rgba(14, 165, 233, 0.35), transparent 70%);
}
.app-glow-center {
    width: 260px; height: 260px;
    top: 35%; right: 28%;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.25), transparent 70%);
}
.app-grid-pattern {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
    background-size: 28px 28px;
    pointer-events: none;
}
.app-content-col {
    position: relative;
    z-index: 2;
}
.app-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.4rem 1rem;
    border-radius: 100px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    font-size: 0.8rem;
    font-weight: 700;
    color: #E0E7FF;
    margin-bottom: 1.25rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
.app-tag-pill .app-live-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10B981;
    box-shadow: 0 0 10px #10B981;
    animation: pulse-dot 1.8s infinite;
}
.app-headline {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(2rem, 3.5vw, 2.85rem);
    font-weight: 900;
    color: #FFFFFF;
    line-height: 1.2;
    margin-bottom: 1.15rem;
    letter-spacing: -0.02em;
}
.app-headline-gradient {
    background: linear-gradient(135deg, #C4B5FD 0%, #38BDF8 50%, #34D399 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.app-description {
    font-size: 0.98rem;
    line-height: 1.68;
    color: rgba(255, 255, 255, 0.82);
    margin-bottom: 1.75rem;
    max-width: 530px;
}
.app-perks-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.85rem 1.25rem;
    margin-bottom: 2rem;
}
.app-perk-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #F1F5F9;
}
.app-perk-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.app-actions-row {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
    margin-bottom: 1.75rem;
}
.playstore-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.95rem;
    padding: 0.85rem 1.75rem;
    background: #000000;
    color: #FFFFFF;
    border-radius: 14px;
    text-decoration: none;
    border: 1.5px solid rgba(255, 255, 255, 0.22);
    transition: all 0.25s ease;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(109, 74, 255, 0.35);
    position: relative;
    overflow: hidden;
}
.playstore-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent);
    opacity: 0;
    transition: opacity 0.25s;
}
.playstore-btn:hover {
    transform: translateY(-3px);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5), 0 0 30px rgba(139, 92, 246, 0.55);
}
.playstore-btn:hover::before {
    opacity: 1;
}
.playstore-btn-svg {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
}
.playstore-btn-texts {
    display: flex;
    flex-direction: column;
    text-align: left;
}
.playstore-btn-sub {
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    font-weight: 600;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.75);
    line-height: 1.1;
}
.playstore-btn-main {
    font-family: 'Poppins', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: -0.01em;
    line-height: 1.25;
}
.app-qr-card {
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.55rem 0.95rem;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 14px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    text-decoration: none;
    transition: all 0.2s;
}
.app-qr-card:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.3);
}
.app-qr-image {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    background: #FFFFFF;
    padding: 3px;
    display: block;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    flex-shrink: 0;
}
.app-qr-info {
    text-align: left;
}
.app-qr-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.app-qr-sub {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.65);
    margin-top: 0.15rem;
    line-height: 1.2;
}
.app-trust-row {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.75);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1.25rem;
}
.app-trust-item {
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.app-stars {
    color: #FBBF24;
    letter-spacing: 1px;
    font-size: 0.85rem;
}

/* Phone Mockup Column */
.app-visual-col {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
}
.app-phone-wrapper {
    position: relative;
    max-width: 300px;
    width: 100%;
}
.app-phone-frame {
    background: #090616;
    border-radius: 44px;
    padding: 12px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 0 3px #372A70, 0 0 35px rgba(139, 92, 246, 0.3);
    position: relative;
}
.app-phone-island {
    position: absolute;
    top: 17px;
    left: 50%;
    transform: translateX(-50%);
    width: 86px;
    height: 18px;
    background: #000000;
    border-radius: 20px;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 12px;
}
.app-phone-camera {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #111E38;
    box-shadow: inset 0 0 2px #3B82F6;
}
.app-phone-screen {
    background: #F8FAFC;
    border-radius: 34px;
    overflow: hidden;
    padding: 1.65rem 0.95rem 1.15rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    border: 1px solid rgba(255,255,255,0.15);
}
.mockup-status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.62rem;
    font-weight: 700;
    color: #1E293B;
    padding: 0 0.35rem;
}
.mockup-app-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.2rem 0.2rem 0.3rem;
}
.mockup-user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.mockup-avatar {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
}
.mockup-user-name {
    font-size: 0.72rem;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.15;
}
.mockup-user-class {
    font-size: 0.6rem;
    color: var(--primary);
    font-weight: 600;
}
.mockup-video-card {
    background: linear-gradient(135deg, #1E1B4B, #4338CA);
    border-radius: 14px;
    padding: 0.8rem;
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 18px rgba(67, 56, 202, 0.25);
}
.mockup-video-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.58rem;
    font-weight: 700;
    background: #EF4444;
    color: #FFFFFF;
    padding: 0.12rem 0.45rem;
    border-radius: 100px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.45rem;
}
.mockup-video-title {
    font-size: 0.74rem;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}
.mockup-video-sub {
    font-size: 0.6rem;
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 0.6rem;
}
.mockup-play-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mockup-play-btn {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #FFFFFF;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.mockup-time-pill {
    font-size: 0.58rem;
    color: #E0E7FF;
    font-weight: 600;
}
.mockup-subjects-row {
    display: flex;
    gap: 0.35rem;
    overflow-x: hidden;
}
.mockup-subject-pill {
    padding: 0.3rem 0.5rem;
    border-radius: 8px;
    font-size: 0.6rem;
    font-weight: 700;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.mockup-quiz-card {
    background: #FFFFFF;
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 12px;
    padding: 0.7rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.mockup-quiz-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.35rem;
}
.mockup-quiz-title {
    font-size: 0.65rem;
    font-weight: 700;
    color: #0F172A;
}
.mockup-quiz-score {
    font-size: 0.58rem;
    font-weight: 800;
    color: #10B981;
    background: #ECFDF5;
    padding: 0.12rem 0.4rem;
    border-radius: 100px;
}
.mockup-quiz-bar {
    height: 5px;
    border-radius: 100px;
    background: #F1F5F9;
    overflow: hidden;
}
.mockup-quiz-fill {
    height: 100%;
    background: linear-gradient(90deg, #10B981, #34D399);
    border-radius: 100px;
}

/* Floating Badges around phone */
.app-float-badge {
    position: absolute;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 14px;
    padding: 0.55rem 0.8rem;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 12;
    border: 1px solid rgba(255, 255, 255, 0.8);
    white-space: nowrap;
}
.afb-1 {
    top: 35px;
    left: -40px;
    animation: afb-float-1 6s ease-in-out infinite;
}
.afb-2 {
    bottom: 45px;
    right: -35px;
    animation: afb-float-2 7s ease-in-out infinite 1s;
}
@keyframes afb-float-1 {
    0%, 100% { transform: translateY(0) rotate(-1deg); }
    50% { transform: translateY(-10px) rotate(1deg); }
}
@keyframes afb-float-2 {
    0%, 100% { transform: translateY(0) rotate(1deg); }
    50% { transform: translateY(-8px) rotate(-1deg); }
}
.afb-icon {
    width: 26px;
    height: 26px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.afb-text-main {
    font-size: 0.7rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1.15;
}
.afb-text-sub {
    font-size: 0.6rem;
    color: #64748B;
    font-weight: 500;
}

/* ════════════════════════
   RESPONSIVE
════════════════════════ */
@media (max-width: 992px) {
    .app-download-container {
        grid-template-columns: 1fr;
        padding: 3rem 2rem;
        gap: 2.75rem;
        text-align: center;
    }
    .app-content-col {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .app-headline {
        font-size: 2.2rem;
    }
    .app-description {
        margin-left: auto;
        margin-right: auto;
    }
    .app-perks-grid {
        max-width: 460px;
        text-align: left;
    }
    .app-actions-row {
        justify-content: center;
    }
    .app-trust-row {
        justify-content: center;
    }
    .afb-1 { left: 5px; top: 15px; }
    .afb-2 { right: 5px; bottom: 25px; }
}
@media (max-width: 900px) {
    .hero-container { grid-template-columns: 1fr; text-align: center; }
    .hero-subtitle { margin: 0 auto 2rem; }
    .hero-actions { justify-content: center; }
    .hero-trust { justify-content: center; }
    .hero-visual { display: none; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
    .nav-links { display: none; }
}
@media (max-width: 600px) {
    .section { padding: 3.5rem 1.25rem; }
    .app-download-section { padding: 2rem 1rem 2.5rem; }
    .app-download-container { border-radius: 24px; padding: 2.25rem 1.25rem; }
    .app-headline { font-size: 1.75rem; }
    .app-perks-grid { grid-template-columns: 1fr; max-width: 100%; }
    .app-actions-row { flex-direction: column; width: 100%; gap: 1rem; }
    .playstore-btn { width: 100%; justify-content: center; }
    .app-qr-card { width: 100%; justify-content: center; }
    .afb-1, .afb-2 { display: none; }
    .footer-grid { grid-template-columns: 1fr; }
    .pricing-card.featured { transform: none; }
    .stats-bar { padding: 1.5rem; }
    .stat-number { font-size: 1.6rem; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="mainNav">
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon">V</div>
        <span class="nav-logo-text">Vidya<span>Mission</span></span>
    </a>
    <ul class="nav-links">
        <li><a href="#download-app" style="color:var(--primary);font-weight:700">📱 App</a></li>
        <li><a href="#subjects">Subjects</a></li>
        <li><a href="#classes">Classes</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#pricing">Pricing</a></li>
    </ul>
    <div class="nav-cta">
        <a href="{{ route('admin.login') }}" class="btn-ghost">Admin</a>
        <a href="#pricing" class="btn-primary-sm">Free Trial →</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="hero-container">
        <!-- Content -->
        <div>
            <div class="hero-badge">
                <span class="badge-pulse"></span>
                West Bengal's #1 Learning Platform
            </div>
            <h1 class="hero-title">
                Learn Smarter,<br>
                <span class="underline-wrap">
                    <span class="gradient-text">Score Higher</span>
                </span><br>
                Every Class
            </h1>
            <p class="hero-subtitle">
                Expert-taught video classes, live doubt sessions, PDF notes, and chapter tests — curated for each WB Board class from 1 to 12. Your complete study partner.
            </p>
            <div class="hero-actions">
                <a href="#pricing" class="btn-hero">🚀 Start Free 3-Day Trial</a>
                <a href="#download-app" class="btn-hero-outline">📱 Download App</a>
            </div>
            <div class="hero-trust">
                <div class="hero-avatars">
                    <div class="hero-avatar">A</div>
                    <div class="hero-avatar">P</div>
                    <div class="hero-avatar">R</div>
                    <div class="hero-avatar">S</div>
                    <div class="hero-avatar">M</div>
                </div>
                <p class="hero-trust-text"><strong>5,000+</strong> students learning right now</p>
            </div>
        </div>

        <!-- Visual -->
        <div class="hero-visual">
            <div class="mini-card mc-1">
                <div class="mc-icon" style="background:#EDE9FE">🏆</div>
                <div>
                    <div class="mc-label">Top Score</div>
                    <div class="mc-value">98 / 100</div>
                </div>
            </div>
            <div class="mini-card mc-2">
                <div class="mc-icon" style="background:#D1FAE5">📚</div>
                <div>
                    <div class="mc-label">Chapters Done</div>
                    <div class="mc-value">24 this week</div>
                </div>
            </div>

            <div class="app-card">
                <div class="app-header">
                    <span class="app-title">📱 My Dashboard</span>
                    <span class="app-badge-green">✓ Trial Active</span>
                </div>
                <div class="app-subject-grid">
                    <div class="app-subject" style="background:#FEF2F2;color:#DC2626"><span class="s-emoji">📝</span>Bengali</div>
                    <div class="app-subject" style="background:#EFF6FF;color:#2563EB"><span class="s-emoji">🇬🇧</span>English</div>
                    <div class="app-subject" style="background:#F5F3FF;color:#7C3AED"><span class="s-emoji">📐</span>Maths</div>
                    <div class="app-subject" style="background:#F0FDF4;color:#16A34A"><span class="s-emoji">⚡</span>Physics</div>
                    <div class="app-subject" style="background:#FFF7ED;color:#D97706"><span class="s-emoji">🗺️</span>Geo</div>
                    <div class="app-subject" style="background:#ECFEFF;color:#0E7490"><span class="s-emoji">🔬</span>Life Sci</div>
                </div>
                <div class="progress-section">
                    <div class="progress-label">
                        <span>Class 9 — Weekly Progress</span>
                        <span style="color:var(--primary);font-weight:800">72%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:72%"></div>
                    </div>
                    <div class="progress-info">🎯 3 more chapters to unlock your weekly badge!</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- APP DOWNLOAD SECTION -->
<section class="app-download-section" id="download-app">
    <div class="app-download-container reveal">
        <!-- Ambient decorative glows -->
        <div class="app-ambient-glow app-glow-top"></div>
        <div class="app-ambient-glow app-glow-bottom"></div>
        <div class="app-ambient-glow app-glow-center"></div>
        <div class="app-grid-pattern"></div>

        <!-- Left Column: Content & Download Options -->
        <div class="app-content-col">
            <div class="app-tag-pill">
                <span class="app-live-dot"></span>
                Official Android App • Live on Google Play
            </div>
            <h2 class="app-headline">
                Learn Anywhere, Anytime with the <span class="app-headline-gradient">VidyaMission App</span>
            </h2>
            <p class="app-description">
                Carry West Bengal's complete Classes 1–12 curriculum right in your pocket. Watch HD video classes, take interactive chapter tests, access teacher notes, and attend live doubt sessions — all on your mobile phone, even offline.
            </p>

            <div class="app-perks-grid">
                <div class="app-perk-item">
                    <div class="app-perk-icon">📥</div>
                    <span>Offline Video Downloads</span>
                </div>
                <div class="app-perk-item">
                    <div class="app-perk-icon">⚡</div>
                    <span>Fast HD Video Streaming</span>
                </div>
                <div class="app-perk-item">
                    <div class="app-perk-icon">📝</div>
                    <span>Chapter Quizzes & Solutions</span>
                </div>
                <div class="app-perk-item">
                    <div class="app-perk-icon">🔔</div>
                    <span>Live Class Alerts & Reminders</span>
                </div>
            </div>

            <div class="app-actions-row">
                <!-- Google Play Button -->
                <a href="https://play.google.com/store/apps/details?id=com.vidyaMission.vidyaMission&pcampaignid=web_share"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="playstore-btn"
                   title="Download VidyaMission on Google Play Store">
                    <svg class="playstore-btn-svg" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M47.3 12.8C42.8 17.6 40 25.1 40 34.6V477.4C40 486.9 42.8 494.4 47.3 499.2L49.5 501.2L282.8 268V256V244L49.5 10.8L47.3 12.8Z" fill="url(#gp_grad1)"/>
                        <path d="M360.5 345.7L282.8 268V256V244L360.5 166.3L362.2 167.3L454.2 219.6C480.4 234.5 480.4 258.8 454.2 273.7L362.2 326.1L360.5 345.7Z" fill="url(#gp_grad2)"/>
                        <path d="M282.8 256L47.3 499.2C54.7 507.1 66.8 508.2 80.6 500.4L360.5 345.7L282.8 256Z" fill="url(#gp_grad3)"/>
                        <path d="M282.8 256L360.5 166.3L80.6 7.6C66.8 -0.2 54.7 0.9 47.3 8.8L282.8 256Z" fill="url(#gp_grad4)"/>
                        <defs>
                            <linearGradient id="gp_grad1" x1="262.5" y1="31.2" x2="-56.2" y2="350" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#00A0FF"/><stop offset="0.007" stop-color="#00A1FF"/><stop offset="0.26" stop-color="#00BEFF"/><stop offset="0.512" stop-color="#00D2FF"/><stop offset="0.76" stop-color="#00DFFF"/><stop offset="1" stop-color="#00E3FF"/>
                            </linearGradient>
                            <linearGradient id="gp_grad2" x1="489.8" y1="256" x2="23.4" y2="256" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FFE000"/><stop offset="0.409" stop-color="#FFBD00"/><stop offset="0.775" stop-color="#FFA500"/><stop offset="1" stop-color="#FF9C00"/>
                            </linearGradient>
                            <linearGradient id="gp_grad3" x1="316.5" y1="289.7" x2="89.1" y2="517.1" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FF3A44"/><stop offset="1" stop-color="#C31162"/>
                            </linearGradient>
                            <linearGradient id="gp_grad4" x1="97" y1="-8.4" x2="230.1" y2="124.7" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#32A071"/><stop offset="0.069" stop-color="#2DA771"/><stop offset="0.476" stop-color="#15CF74"/><stop offset="0.801" stop-color="#06E775"/><stop offset="1" stop-color="#00F076"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="playstore-btn-texts">
                        <span class="playstore-btn-sub">GET IT ON</span>
                        <span class="playstore-btn-main">Google Play</span>
                    </div>
                </a>

                <!-- QR Code Box -->
                <a href="https://play.google.com/store/apps/details?id=com.vidyaMission.vidyaMission&pcampaignid=web_share"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="app-qr-card"
                   title="Scan QR Code to open Google Play Store">
                    <img src="{{ asset('images/app-download-qr.svg') }}" alt="VidyaMission Play Store QR Code" class="app-qr-image">
                    <div class="app-qr-info">
                        <div class="app-qr-label">📲 Scan to Install</div>
                        <div class="app-qr-sub">Point mobile camera to download</div>
                    </div>
                </a>
            </div>

            <!-- Trust proof line -->
            <div class="app-trust-row">
                <div class="app-trust-item">
                    <span class="app-stars">★★★★★</span>
                    <span><strong>4.8</strong> Play Store</span>
                </div>
                <div class="app-trust-item">
                    <span>👥 <strong>10,000+</strong> Downloads</span>
                </div>
                <div class="app-trust-item">
                    <span>🛡️ Play Protect Verified</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Phone Mockup & Floating Badges -->
        <div class="app-visual-col">
            <div class="app-phone-wrapper">
                <!-- Floating Orbit Badges -->
                <div class="app-float-badge afb-1">
                    <div class="afb-icon" style="background:#EDE9FE;color:#6D4AFF">📥</div>
                    <div>
                        <div class="afb-text-main">Offline Mode</div>
                        <div class="afb-text-sub">Study without internet</div>
                    </div>
                </div>

                <div class="app-float-badge afb-2">
                    <div class="afb-icon" style="background:#FEF3C7;color:#D97706">⭐</div>
                    <div>
                        <div class="afb-text-main">4.8 Rating</div>
                        <div class="afb-text-sub">1,200+ Student reviews</div>
                    </div>
                </div>

                <!-- Realistic Phone Frame -->
                <div class="app-phone-frame">
                    <div class="app-phone-island">
                        <div class="app-phone-camera"></div>
                    </div>

                    <div class="app-phone-screen">
                        <!-- Phone Status Bar -->
                        <div class="mockup-status-bar">
                            <span>9:41</span>
                            <span>📶 5G 🔋</span>
                        </div>

                        <!-- Top In-App Bar -->
                        <div class="mockup-app-bar">
                            <div class="mockup-user-info">
                                <div class="mockup-avatar">V</div>
                                <div>
                                    <div class="mockup-user-name">Rahul Mondal 👋</div>
                                    <div class="mockup-user-class">Class 10 • WB Board</div>
                                </div>
                            </div>
                            <span style="font-size:0.9rem">🔔</span>
                        </div>

                        <!-- Video Card Mockup -->
                        <div class="mockup-video-card">
                            <div class="mockup-video-badge">🔴 LIVE CLASS TODAY</div>
                            <div class="mockup-video-title">Physical Science: Current Electricity</div>
                            <div class="mockup-video-sub">Chapter 4 • Dr. S. Mukherjee</div>
                            <div class="mockup-play-row">
                                <div class="mockup-play-btn">▶</div>
                                <span class="mockup-time-pill">42 mins • 1080p HD</span>
                            </div>
                        </div>

                        <!-- Subject Pills -->
                        <div class="mockup-subjects-row">
                            <div class="mockup-subject-pill" style="background:#F5F3FF;color:#6D4AFF">📐 Maths</div>
                            <div class="mockup-subject-pill" style="background:#F0FDF4;color:#16A34A">🔬 Life Sci</div>
                            <div class="mockup-subject-pill" style="background:#EFF6FF;color:#2563EB">🇬🇧 English</div>
                            <div class="mockup-subject-pill" style="background:#FEF2F2;color:#DC2626">📝 Bengali</div>
                        </div>

                        <!-- Daily Practice Quiz Widget -->
                        <div class="mockup-quiz-card">
                            <div class="mockup-quiz-header">
                                <span class="mockup-quiz-title">Daily Chapter Quiz</span>
                                <span class="mockup-quiz-score">10/10 🏆</span>
                            </div>
                            <div class="mockup-quiz-bar">
                                <div class="mockup-quiz-fill" style="width:100%"></div>
                            </div>
                            <div style="font-size:0.56rem;color:#64748B;margin-top:0.3rem">
                                ✅ All 10 practice questions completed today!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stats-container">
        <div class="stat-item reveal"><div class="stat-number" data-count="5000">0</div><div class="stat-label">Students Enrolled</div></div>
        <div class="stat-item reveal reveal-d1"><div class="stat-number">12</div><div class="stat-label">Class Levels (1–12)</div></div>
        <div class="stat-item reveal reveal-d2"><div class="stat-number">7</div><div class="stat-label">Core Subjects</div></div>
        <div class="stat-item reveal reveal-d3"><div class="stat-number">3</div><div class="stat-label">Days Free Trial</div></div>
    </div>
</div>

<!-- CLASS LEVELS -->
<section class="section" id="classes">
    <div class="section-container">
        <div class="section-header reveal">
            <div class="section-tag">Curriculum</div>
            <h2 class="section-title">Classes 1 to 12 —<br>Every Grade Covered</h2>
            <p class="section-subtitle">Each class has its own dedicated chapters, mapped to the WB Board syllabus. Choose your class and pick exactly what you need.</p>
        </div>
        <div class="classes-grid reveal">
            @foreach(range(1,12) as $c)
            <div class="class-pill">Class {{ $c }}</div>
            @endforeach
        </div>
        <div style="margin-top:1.5rem;padding:1.25rem 1.5rem;background:rgba(109,74,255,0.06);border:1.5px solid rgba(109,74,255,0.15);border-radius:14px;display:flex;align-items:flex-start;gap:0.75rem" class="reveal">
            <span style="font-size:1.25rem">💡</span>
            <div>
                <div style="font-weight:700;color:var(--text-heading);font-size:0.9rem;margin-bottom:0.25rem">Smart Class Filtering</div>
                <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6">After you set up your profile with your class, VidyaMission automatically shows you only the chapters relevant to your grade. No clutter, just your content.</div>
            </div>
        </div>
    </div>
</section>

<!-- SUBJECTS -->
<section class="section section-alt" id="subjects">
    <div class="section-container">
        <div class="section-header reveal">
            <div class="section-tag">Subjects</div>
            <h2 class="section-title">All WB Board Subjects,<br>Under One Roof</h2>
            <p class="section-subtitle">7 subjects taught by experienced teachers — tailored for each class level.</p>
        </div>
        <div class="subjects-grid">
            <div class="subject-card reveal" style="--card-color:#EF4444">
                <span class="subject-emoji">📝</span>
                <div class="subject-name">Bengali</div>
                <div class="subject-sub">বাংলা ভাষা ও সাহিত্য</div>
            </div>
            <div class="subject-card reveal reveal-d1" style="--card-color:#3B82F6">
                <span class="subject-emoji">🇬🇧</span>
                <div class="subject-name">English</div>
                <div class="subject-sub">Language & Literature</div>
            </div>
            <div class="subject-card reveal reveal-d2" style="--card-color:#84CC16">
                <span class="subject-emoji">🏛️</span>
                <div class="subject-name">History</div>
                <div class="subject-sub">ইতিহাস</div>
            </div>
            <div class="subject-card reveal reveal-d1" style="--card-color:#8B5CF6">
                <span class="subject-emoji">📐</span>
                <div class="subject-name">Mathematics</div>
                <div class="subject-sub">গণিত</div>
            </div>
            <div class="subject-card reveal reveal-d2" style="--card-color:#F97316">
                <span class="subject-emoji">🗺️</span>
                <div class="subject-name">Geography</div>
                <div class="subject-sub">ভূগোল</div>
            </div>
            <div class="subject-card reveal reveal-d3" style="--card-color:#22C55E">
                <span class="subject-emoji">⚡</span>
                <div class="subject-name">Physics</div>
                <div class="subject-sub">পদার্থবিজ্ঞান</div>
            </div>
            <div class="subject-card reveal reveal-d1" style="--card-color:#06B6D4">
                <span class="subject-emoji">🔬</span>
                <div class="subject-name">Life Science</div>
                <div class="subject-sub">জীববিজ্ঞান</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="section-container">
        <div class="section-header reveal">
            <div class="section-tag">Features</div>
            <h2 class="section-title">Everything You Need<br>to <span style="color:var(--primary)">Ace Your Exams</span></h2>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon-wrap" style="background:#EDE9FE">🎬</div>
                <h3 class="feature-title">Recorded Video Classes</h3>
                <p class="feature-desc">Watch expert lessons anytime. Rewind, pause, and learn at your pace via Google Drive streaming.</p>
            </div>
            <div class="feature-card reveal reveal-d1">
                <div class="feature-icon-wrap" style="background:#ECFEFF">📡</div>
                <h3 class="feature-title">Live Interactive Classes</h3>
                <p class="feature-desc">Join real-time sessions on Meet or Zoom. Ask questions and get instant clarification from teachers.</p>
            </div>
            <div class="feature-card reveal reveal-d2">
                <div class="feature-icon-wrap" style="background:#FEF3C7">📋</div>
                <h3 class="feature-title">PDF Notes & Study Material</h3>
                <p class="feature-desc">Download teacher-curated notes, important questions, and chapter summaries. Study offline anytime.</p>
            </div>
            <div class="feature-card reveal reveal-d3">
                <div class="feature-icon-wrap" style="background:#FCE7F3">🧪</div>
                <h3 class="feature-title">Chapter Tests & Instant Results</h3>
                <p class="feature-desc">MCQ quizzes after each chapter. Instant scored results — correct answers shown only after submission.</p>
            </div>
            <div class="feature-card reveal reveal-d1">
                <div class="feature-icon-wrap" style="background:#D1FAE5">📊</div>
                <h3 class="feature-title">Class-wise Progress Tracking</h3>
                <p class="feature-desc">See chapter completion by class, test scores, and weekly streaks. Stay motivated with badges.</p>
            </div>
            <div class="feature-card reveal reveal-d2">
                <div class="feature-icon-wrap" style="background:#E0E7FF">🏆</div>
                <h3 class="feature-title">Rewards & Achievements</h3>
                <p class="feature-desc">Earn rewards for completing chapters and passing tests. Compete with your district's top learners.</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section section-alt" id="how-it-works">
    <div class="section-container">
        <div class="section-header reveal" style="text-align:center;max-width:560px;margin:0 auto 3rem">
            <div class="section-tag" style="justify-content:center">How It Works</div>
            <h2 class="section-title">Start Learning in<br>3 Simple Steps</h2>
        </div>
        <div class="steps-grid">
            <div class="step reveal">
                <div class="step-num">1</div>
                <div>
                    <h3>Enter Your Email</h3>
                    <p>No passwords needed. Enter your email, verify with a 6-digit OTP, and you're in. Takes under 30 seconds.</p>
                </div>
            </div>
            <div class="step reveal reveal-d1">
                <div class="step-num">2</div>
                <div>
                    <h3>Set Up Your Profile</h3>
                    <p>Tell us your class (1–12), school, and district. Your 3-day free trial starts instantly — no credit card needed.</p>
                </div>
            </div>
            <div class="step reveal reveal-d2">
                <div class="step-num">3</div>
                <div>
                    <h3>Start Learning!</h3>
                    <p>Your dashboard shows chapters specific to your class. Watch videos, join live classes, download notes, take tests.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section" id="pricing">
    <div class="section-container">
        <div class="section-header reveal" style="text-align:center;max-width:560px;margin:0 auto 3rem">
            <div class="section-tag" style="justify-content:center">Pricing</div>
            <h2 class="section-title">Simple, Affordable Plans</h2>
            <p class="section-subtitle">Start with a free 3-day trial. No credit card required. Cancel anytime.</p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card reveal">
                <div class="pricing-name pricing-name-dark">Monthly</div>
                <div class="pricing-price pricing-price-dark">₹199 <sub>/mo</sub></div>
                <div class="pricing-period pricing-period-dark">Billed monthly</div>
                <ul class="pricing-features">
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> All 7 subjects</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Recorded video classes</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Live classes access</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> PDF notes</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Chapter tests</li>
                </ul>
                <a href="#" class="btn-pricing btn-pricing-dark">Get Started</a>
            </div>

            <div class="pricing-card featured reveal reveal-d1">
                <div class="pricing-badge">⭐ Most Popular</div>
                <div class="pricing-name pricing-name-light">Quarterly</div>
                <div class="pricing-price pricing-price-light">₹499 <sub>/3mo</sub></div>
                <div class="pricing-period pricing-period-light">Save ₹98 vs monthly</div>
                <ul class="pricing-features">
                    <li class="li-check-light"><span class="li-tick li-tick-light">✓</span> Everything in Monthly</li>
                    <li class="li-check-light"><span class="li-tick li-tick-light">✓</span> Save 16%</li>
                    <li class="li-check-light"><span class="li-tick li-tick-light">✓</span> Priority support</li>
                    <li class="li-check-light"><span class="li-tick li-tick-light">✓</span> Exam tips newsletter</li>
                </ul>
                <a href="#" class="btn-pricing btn-pricing-light">Get Started</a>
            </div>

            <div class="pricing-card reveal reveal-d2">
                <div class="pricing-name pricing-name-dark">Annual</div>
                <div class="pricing-price pricing-price-dark">₹1499 <sub>/yr</sub></div>
                <div class="pricing-period pricing-period-dark">Best value — save ₹889!</div>
                <ul class="pricing-features">
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Everything in Quarterly</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Save 37%</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Exam preparation guides</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Teacher Q&A access</li>
                    <li class="li-check-dark"><span class="li-tick li-tick-dark">✓</span> Completion certificate</li>
                </ul>
                <a href="#" class="btn-pricing btn-pricing-dark">Get Started</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-box reveal">
        <h2 class="cta-title">Ready to Transform<br>Your Studies? 🚀</h2>
        <p class="cta-sub">Join thousands of West Bengal students learning smarter, by class, every day.</p>
        <div class="cta-actions">
            <a href="#pricing" class="btn-hero">Start Free 3-Day Trial</a>
            <a href="{{ route('admin.login') }}" class="btn-hero-outline">Admin Panel →</a>
        </div>
        <p class="cta-note">No credit card · Cancel anytime · Class 1 to 12 supported</p>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-container">
        <div class="footer-grid">
            <div>
                <div class="footer-logo">
                    <div class="footer-logo-icon">V</div>
                    <span class="footer-logo-text">Vidya<span>Mission</span></span>
                </div>
                <p class="footer-desc">West Bengal's leading student learning platform. Expert teachers, quality content, and class-level specific chapters — all in one place.</p>
            </div>
            <div class="footer-col">
                <h4>Subjects</h4>
                <ul>
                    <li><a href="#">Bengali</a></li>
                    <li><a href="#">English</a></li>
                    <li><a href="#">Mathematics</a></li>
                    <li><a href="#">Physics</a></li>
                    <li><a href="#">Geography</a></li>
                    <li><a href="#">Life Science</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="https://play.google.com/store/apps/details?id=com.vidyaMission.vidyaMission&pcampaignid=web_share" target="_blank" rel="noopener noreferrer" style="color:#A78BFA;font-weight:700">📱 Android App (Play Store) ↗</a></li>
                    <li><a href="#">Video Classes</a></li>
                    <li><a href="#">Live Classes</a></li>
                    <li><a href="#">PDF Notes</a></li>
                    <li><a href="#">Chapter Tests</a></li>
                    <li><a href="#">Rewards</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Use</a></li>
                    <li><a href="{{ route('admin.login') }}">Admin Login</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} VidyaMission. Made with ❤️ for West Bengal students.</p>
            <p>🎓 Classes 1–12 · All 23 Districts</p>
        </div>
    </div>
</footer>

<script>
// Scroll reveal
const observer = new IntersectionObserver(
    entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); }),
    { threshold: 0.1, rootMargin: '0px 0px -30px 0px' }
);
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Count-up animation
function countUp(el, target) {
    let n = 0;
    const step = target / (2000 / 16);
    const t = setInterval(() => {
        n = Math.min(n + step, target);
        el.textContent = Math.floor(n).toLocaleString() + '+';
        if (n >= target) clearInterval(t);
    }, 16);
}
new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting && e.target.dataset.count) {
            countUp(e.target, parseInt(e.target.dataset.count));
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.5 }).observe(document.querySelector('[data-count]'));

// Navbar shadow on scroll
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', scrollY > 20);
});
</script>
</body>
</html>
