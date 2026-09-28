<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidya Mission API Documentation — Swagger UI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        :root {
            --primary: #5B45FF;
            --primary-dark: #432ED8;
            --secondary: #06B6D4;
            --dark-bg: #0B0F19;
            --dark-card: #131B2E;
            --border-color: rgba(255,255,255,0.08);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            min-height: 100vh;
        }

        /* Top Brand Navigation */
        .docs-navbar {
            background: var(--dark-bg);
            color: #FFFFFF;
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .docs-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: #FFFFFF;
        }
        .docs-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 1.2rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(91,69,255,0.4);
        }
        .docs-brand-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .docs-brand-text span { color: #8B7AFF; }
        .docs-badge {
            background: rgba(91,69,255,0.25);
            color: #C4B5FD;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            border: 1px solid rgba(91,69,255,0.4);
            margin-left: 0.5rem;
        }

        .docs-nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .docs-nav-btn {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-color);
            color: #E2E8F0;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        .docs-nav-btn:hover {
            background: rgba(255,255,255,0.12);
            color: #FFFFFF;
            border-color: rgba(255,255,255,0.2);
        }
        .docs-nav-btn.btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .docs-nav-btn.btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Token Helper Banner */
        .token-helper-bar {
            background: #0F172A;
            color: #CBD5E1;
            padding: 0.65rem 2rem;
            font-size: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .token-input-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .token-input {
            background: #1E293B;
            border: 1px solid rgba(255,255,255,0.15);
            color: #F8FAFC;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            width: 280px;
            outline: none;
        }
        .token-input:focus {
            border-color: var(--primary);
        }
        .token-btn {
            background: var(--primary);
            border: none;
            color: #fff;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .token-btn:hover { background: var(--primary-dark); }

        /* Swagger Container Customization */
        #swagger-ui {
            max-width: 1240px;
            margin: 0 auto;
            padding: 1.5rem 1rem 4rem;
        }
        .swagger-ui .topbar { display: none !important; }
        .swagger-ui .info { margin: 1.5rem 0 2rem !important; }
        .swagger-ui .info .title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 800 !important;
            color: #0F172A !important;
        }
        .swagger-ui .opblock-tag {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #E2E8F0 !important;
            padding: 12px 0 6px !important;
        }
        .swagger-ui .opblock {
            border-radius: 10px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03) !important;
            margin-bottom: 0.75rem !important;
        }
        .swagger-ui .btn.authorize {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
        }
        .swagger-ui .btn.authorize svg { fill: #fff !important; }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="docs-navbar">
        <a href="{{ route('home') }}" class="docs-brand">
            <div class="docs-brand-icon">V</div>
            <div class="docs-brand-text">Vidya<span>Mission</span> API</div>
            <span class="docs-badge">OpenAPI 3.0</span>
        </a>
        <div class="docs-nav-actions">
            <a href="{{ url('/docs/openapi.json') }}" target="_blank" class="docs-nav-btn">
                <span>📄</span> openapi.json
            </a>
            <a href="{{ route('admin.dashboard') }}" class="docs-nav-btn btn-primary">
                <span>⚡</span> Admin Portal
            </a>
        </div>
    </header>

    <!-- Quick Token Injection Bar -->
    <div class="token-helper-bar">
        <div>
            🔐 <strong>Authorize Sanctum Token:</strong> Paste your Bearer token below to authenticate API test requests across all endpoints.
        </div>
        <div class="token-input-group">
            <input type="text" id="quickTokenInput" class="token-input" placeholder="Bearer 1|xyz123...">
            <button type="button" class="token-btn" onclick="applyQuickToken()">Apply Token</button>
        </div>
    </div>

    <!-- Swagger UI Container -->
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: "{{ $apiSpecUrl }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                filter: true,
                displayRequestDuration: true,
                docExpansion: 'list',
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                showExtensions: true,
                showCommonExtensions: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "BaseLayout"
            });
        };

        function applyQuickToken() {
            let token = document.getElementById('quickTokenInput').value.trim();
            if (!token) {
                alert('Please enter a token first.');
                return;
            }
            if (token.toLowerCase().startsWith('bearer ')) {
                token = token.substring(7).trim();
            }
            if (window.ui) {
                window.ui.authActions.authorize({
                    bearerAuth: {
                        name: "bearerAuth",
                        schema: {
                            type: "http",
                            scheme: "bearer",
                            bearerFormat: "Sanctum Token"
                        },
                        value: token
                    }
                });
                alert('✓ Bearer token applied successfully! All authorized requests will include this header.');
            }
        }
    </script>
</body>
</html>
