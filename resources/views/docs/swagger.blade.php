<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidya Mission — API Documentation (OpenAPI / Swagger)</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236366f1'><path d='M12 3L1 9l11 6 9-4.91V17h2V9L12 3z'/></svg>">
    
    <!-- Swagger UI CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --brand-bg: #0f172a;
            --brand-surface: #1e293b;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        /* Top Brand Header */
        .api-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #ffffff;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .api-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .api-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .api-brand-icon svg {
            width: 24px;
            height: 24px;
            fill: #ffffff;
        }

        .api-brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .api-badge {
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(99, 102, 241, 0.25);
            color: #c7d2fe;
            border: 1px solid rgba(165, 180, 252, 0.3);
            padding: 2px 8px;
            border-radius: 9999px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .api-brand-desc {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        .api-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .api-link-btn {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .api-link-btn:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .api-link-btn.primary {
            background: #4f46e5;
            border-color: #6366f1;
            color: #ffffff;
        }

        .api-link-btn.primary:hover {
            background: #4338ca;
        }

        /* Banner subheader */
        .api-curriculum-banner {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 28px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.85rem;
            color: #475569;
        }

        .banner-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pill {
            background: #f1f5f9;
            color: #334155;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pill.active {
            background: #e0e7ff;
            color: #4338ca;
        }

        /* Swagger UI Overrides */
        #swagger-ui {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 24px 60px 24px;
        }

        .swagger-ui .topbar {
            display: none !important;
        }

        .swagger-ui .info {
            margin: 20px 0 24px 0 !important;
        }

        .swagger-ui .info .title {
            font-size: 1.8rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        .swagger-ui .scheme-container {
            background: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 16px 20px !important;
            margin-bottom: 24px !important;
        }

        .swagger-ui .btn.authorize {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 6px 16px !important;
        }

        .swagger-ui .btn.authorize svg {
            fill: #ffffff !important;
        }

        .swagger-ui .opblock {
            border-radius: 10px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }

        .swagger-ui .opblock .opblock-summary {
            padding: 10px 16px !important;
        }

        @media (max-width: 768px) {
            .api-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 14px 18px;
            }

            .api-actions {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .api-curriculum-banner {
                padding: 10px 18px;
            }

            #swagger-ui {
                padding: 12px 14px 40px 14px;
            }
        }
    </style>
</head>
<body>

    <header class="api-header">
        <div class="api-brand">
            <div class="api-brand-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                </svg>
            </div>
            <div>
                <h1 class="api-brand-title">
                    Vidya Mission API
                    <span class="api-badge">OpenAPI 3.0.3</span>
                </h1>
                <div class="api-brand-desc">Interactive REST API documentation & developer testing sandbox</div>
            </div>
        </div>

        <div class="api-actions">
            <a href="/docs/openapi.json" target="_blank" class="api-link-btn" title="Raw OpenAPI Specification">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                openapi.json
            </a>
            <a href="/admin/dashboard" class="api-link-btn primary" title="Open Admin Panel">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Admin Panel
            </a>
        </div>
    </header>

    <div class="api-curriculum-banner">
        <div>
            <strong>Curriculum Coverage:</strong> Classes 1 to 10 (West Bengal Board of Secondary Education — WBBSE)
        </div>
        <div class="banner-tags">
            <span class="pill active">Class 1–4 Primary</span>
            <span class="pill active">Class 5–8 Upper Primary</span>
            <span class="pill active">Class 9–10 Secondary (Madhyamik)</span>
            <span class="pill">88 Chapters</span>
            <span class="pill">101 Videos</span>
            <span class="pill active">101 Live Classes</span>
            <span class="pill">101 Notes</span>
            <span class="pill">101 Tests</span>
            <span class="pill">203 MCQs</span>
        </div>
    </div>

    <!-- Swagger UI Render Target -->
    <div id="swagger-ui"></div>

    <!-- Swagger UI JS Bundle CDN -->
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js" charset="UTF-8"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js" charset="UTF-8"></script>

    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: "{{ route('docs.json') }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "BaseLayout",
                persistAuthorization: true,
                displayRequestDuration: true,
                docExpansion: 'list',
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                filter: true,
                showExtensions: true,
                showCommonExtensions: true,
                syntaxHighlight: {
                    activate: true,
                    theme: "agate"
                }
            });
        };
    </script>
</body>
</html>
