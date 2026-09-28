<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms and Conditions — VidyaMission</title>
<meta name="description" content="Terms and Conditions of Use for VidyaMission student learning platform. Review user guidelines, billing, and account rules.">
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
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-body); overflow-x: hidden; }

/* NAVBAR */
.nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    padding: 0.875rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border-light);
    box-shadow: 0 1px 20px rgba(0,0,0,0.05);
}
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
.nav-cta { display: flex; align-items: center; gap: 0.75rem; }
.btn-ghost {
    padding: 0.5rem 1rem; border-radius: 8px; background: transparent;
    border: 1.5px solid var(--border); color: var(--text-muted);
    font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none;
}
.btn-ghost:hover { border-color: var(--primary); color: var(--primary); }

/* HERO BANNER */
.hero-banner {
    padding: 8rem 2rem 4rem;
    background: linear-gradient(160deg, #FDFCFF 0%, #F0EDFF 70%, #E8F4FF 100%);
    text-align: center;
    border-bottom: 1px solid var(--border-light);
}
.hero-banner h1 {
    font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 900; color: var(--text-heading); margin-bottom: 0.5rem;
}
.hero-banner p { font-size: 0.95rem; color: var(--text-muted); }

/* LEGAL CONTAINER */
.legal-container {
    max-width: 1000px; margin: 3rem auto; padding: 0 1.5rem;
    display: grid; grid-template-columns: 280px 1fr; gap: 3rem;
}

/* SIDEBAR NAVIGATION */
.legal-sidebar {
    position: sticky; top: 6.5rem; height: fit-content;
}
.legal-nav-card {
    background: var(--bg-card); border-radius: 16px; padding: 1.5rem;
    border: 1.5px solid var(--border-light); box-shadow: var(--shadow-sm);
}
.legal-nav-card h3 {
    font-size: 0.85rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: var(--text-light); margin-bottom: 1rem;
}
.legal-nav-list { list-style: none; display: flex; flex-direction: column; gap: 0.55rem; }
.legal-nav-link {
    display: block; padding: 0.6rem 0.875rem; border-radius: 8px;
    font-size: 0.85rem; font-weight: 600; color: var(--text-muted);
    text-decoration: none; transition: all 0.2s;
}
.legal-nav-link:hover { color: var(--primary); background: var(--bg-surface); }
.legal-nav-link.active {
    color: #fff; background: var(--primary);
    box-shadow: 0 4px 12px rgba(109, 74, 255, 0.25);
}

/* CONTENT CARD */
.legal-content-card {
    background: var(--bg-card); border-radius: 20px; padding: 3rem;
    border: 1.5px solid var(--border-light); box-shadow: var(--shadow-sm);
}
.legal-section { margin-bottom: 2.5rem; scroll-margin-top: 6.5rem; }
.legal-section:last-child { margin-bottom: 0; }
.legal-section h2 {
    font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700;
    color: var(--text-heading); margin-bottom: 1rem;
    border-bottom: 2px solid var(--bg-surface); padding-bottom: 0.5rem;
}
.legal-section p { font-size: 0.925rem; line-height: 1.7; color: var(--text-body); margin-bottom: 1rem; }
.legal-section ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
.legal-section ul li { font-size: 0.925rem; line-height: 1.7; color: var(--text-body); margin-bottom: 0.5rem; }

/* FOOTER */
footer { background: var(--text-heading); color: rgba(255,255,255,0.75); padding: 3.5rem 2rem 1.75rem; margin-top: 6rem; }
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

@media (max-width: 900px) {
    .legal-container { grid-template-columns: 1fr; gap: 2rem; }
    .legal-sidebar { position: relative; top: 0; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>

<nav class="nav" id="mainNav">
    <a href="{{ route('home') }}" class="nav-logo">
        <div class="nav-logo-icon">VM</div>
        <div class="nav-logo-text">Vidya<span>Mission</span></div>
    </a>
    <div class="nav-cta">
        <a href="{{ route('home') }}" class="btn-ghost">Back to Home</a>
    </div>
</nav>

<div class="hero-banner">
    <h1>Terms and Conditions</h1>
    <p>Last updated: July 7, 2026</p>
</div>

<div class="legal-container">
    <div class="legal-sidebar">
        <div class="legal-nav-card">
            <h3>Sections</h3>
            <ul class="legal-nav-list">
                <li><a href="#acceptance" class="legal-nav-link active">1. Acceptance of Terms</a></li>
                <li><a href="#services" class="legal-nav-link">2. Our Services</a></li>
                <li><a href="#security" class="legal-nav-link">3. Account Security</a></li>
                <li><a href="#billing" class="legal-nav-link">4. Billing & Refund</a></li>
                <li><a href="#property" class="legal-nav-link">5. Intellectual Property</a></li>
                <li><a href="#termination" class="legal-nav-link">6. Account Termination</a></li>
            </ul>
        </div>
    </div>

    <div class="legal-content-card">
        <section id="acceptance" class="legal-section">
            <h2>1. Acceptance of Terms</h2>
            <p>Welcome to VidyaMission. By registering an account, purchasing a subscription, or browsing our platform, you agree to be bound by these Terms and Conditions and all applicable laws and regulations. If you do not agree to these terms, please stop using the site immediately.</p>
        </section>

        <section id="services" class="legal-section">
            <h2>2. Our Services</h2>
            <p>VidyaMission provides access to specialized online education resources for West Bengal syllabus (Classes 1–12), consisting of recorded video classes, scheduled live tutoring sessions, downloadable PDF textbooks and notes, chapter-wise test quizzes, and reward points tracking systems.</p>
        </section>

        <section id="security" class="legal-section">
            <h2>3. Account Security</h2>
            <p>To use our services, you must sign up using a mobile number and verify it via OTP. You are entirely responsible for all actions taken under your account. You agree:</p>
            <ul>
                <li>To keep your password private and secure.</li>
                <li>Not to share your account credentials with other students or external users.</li>
                <li>To notify us immediately of any unauthorized access or breach of security.</li>
            </ul>
        </section>

        <section id="billing" class="legal-section">
            <h2>4. Billing, Subscriptions, & Refund</h2>
            <p>We provide a 3-day free trial on account activation. After the trial, continued access requires a valid subscription plan purchase.</p>
            <ul>
                <li>All payments are integrated with automated secure merchant checkouts.</li>
                <li>Billing dates are tracked under your profile dashboard.</li>
                <li>Refund policies are subject to our consumer guidelines. Please contact customer support for specific billing discrepancies.</li>
            </ul>
        </section>

        <section id="property" class="legal-section">
            <h2>5. Intellectual Property Rights</h2>
            <p>All assets published on VidyaMission, including video lectures, question banks, study PDFs, code, design graphics, icons, logos, and layouts are the exclusive property of VidyaMission or our instructors.</p>
            <p>You are granted a personal, non-exclusive, non-transferable license to view and study the educational materials. You must not capture, record, redistribute, publish, or modify our educational videos or documents for commercial use.</p>
        </section>

        <section id="termination" class="legal-section">
            <h2>6. Account Termination</h2>
            <p>We reserve the right to suspend or block your account at any time without notice for violation of these terms, including multi-device account sharing, abuse of instructors, or piracy.</p>
            <p>You are free to close and delete your account voluntarily through our application profile settings. Account deletion is final and wipes all subscription plans, progress tracking history, and reward points, with no refunds for unused portions of subscriptions.</p>
        </section>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="{{ route('home') }}" class="footer-logo">
                    <div class="footer-logo-icon">VM</div>
                    <div class="footer-logo-text">Vidya<span>Mission</span></div>
                </a>
                <p class="footer-desc">West Bengal's premier digital learning platform offering quality classes and study notes for students.</p>
            </div>
            <div class="footer-col">
                <br/>
            </div>
            <div class="footer-col">
                <br/>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms-and-conditions') }}">Terms of Use</a></li>
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
// Sidebar active link switching on scroll
const sections = document.querySelectorAll('.legal-section');
const navLinks = document.querySelectorAll('.legal-nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (scrollY >= sectionTop - 120) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').slice(1) === current) {
            link.classList.add('active');
        }
    });
});
</script>

</body>
</html>
