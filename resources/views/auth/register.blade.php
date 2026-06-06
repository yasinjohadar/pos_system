<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - إنشاء حساب</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@600;700;800&family=Cairo:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-body: "Cairo", sans-serif;
            --font-heading: "Alexandria", sans-serif;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-hover: #4338ca;
            --surface: #ffffff;
            --bg: #f8fafc;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --error: #dc2626;
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
        }

        h1, h2, .brand-headline, .brand-logo-text strong, .form-header h1 {
            font-family: var(--font-heading);
        }

        .auth-page {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ── Brand Panel ── */
        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 50%, #312e81 100%);
            color: #ffffff;
            overflow: hidden;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cpath d='M0 60V0h60v60H0z' fill='none'/%3E%3Cpath d='M0 0h60M0 15h60M0 30h60M0 45h60M15 0v60M30 0v60M45 0v60' stroke='%23ffffff' stroke-width='0.5' opacity='0.07'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            max-width: 28rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo svg {
            width: 3.5rem;
            height: 3.5rem;
            flex-shrink: 0;
        }

        .brand-logo-text {
            display: flex;
            flex-direction: column;
        }

        .brand-logo-text strong {
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .brand-logo-text span {
            font-size: 0.8125rem;
            opacity: 0.8;
            margin-top: 0.125rem;
        }

        .brand-headline {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 0.75rem;
        }

        .brand-subheadline {
            font-size: 1rem;
            opacity: 0.85;
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }

        .brand-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.9375rem;
        }

        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }

        .feature-icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* ── Form Panel ── */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: var(--bg);
        }

        .form-card {
            width: 100%;
            max-width: 26rem;
            background: var(--surface);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.04),
                0 4px 16px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.03);
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.375rem;
        }

        .form-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .field-icon {
            position: absolute;
            top: 50%;
            right: 0.875rem;
            transform: translateY(-50%);
            width: 1.125rem;
            height: 1.125rem;
            color: var(--text-muted);
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-wrapper input {
            display: block;
            width: 100%;
            padding: 0.75rem 2.75rem 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9375rem;
            font-family: inherit;
            color: var(--text);
            background: var(--surface);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .input-wrapper input:focus + .field-icon,
        .input-wrapper:focus-within .field-icon {
            color: var(--primary);
        }

        .input-wrapper input.input-error {
            border-color: var(--error);
        }

        .input-wrapper input.input-error:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .error-messages {
            font-size: 0.8125rem;
            color: var(--error);
            margin-top: 0.375rem;
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
        }

        .error-messages li {
            margin-top: 0.25rem;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 0.875rem 1.5rem;
            margin-top: 0.5rem;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .form-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .form-footer a:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-radius: 2px;
        }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .auth-page {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .brand-panel {
                padding: 2rem;
            }

            .brand-headline {
                font-size: 1.375rem;
            }

            .brand-subheadline {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .brand-panel {
                padding: 1.5rem;
            }

            .brand-logo {
                margin-bottom: 1rem;
            }

            .brand-headline {
                font-size: 1.125rem;
                margin-bottom: 0;
            }

            .brand-subheadline,
            .brand-features {
                display: none;
            }

            .form-panel {
                padding: 1.25rem;
            }

            .form-card {
                padding: 1.75rem;
                border-radius: 12px;
                box-shadow:
                    0 1px 3px rgba(0, 0, 0, 0.04),
                    0 2px 8px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
</head>
<body>
    <div class="auth-page">

        {{-- Brand Panel (right side in RTL) --}}
        <aside class="brand-panel">
            <div class="brand-content">
                <a href="/" class="brand-logo">
                    <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect width="56" height="56" rx="14" fill="rgba(255,255,255,0.15)"/>
                        <rect x="14" y="12" width="28" height="32" rx="3" stroke="white" stroke-width="2" fill="none"/>
                        <line x1="20" y1="20" x2="36" y2="20" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <line x1="20" y1="26" x2="32" y2="26" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                        <line x1="20" y1="32" x2="36" y2="32" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                        <rect x="30" y="36" width="10" height="8" rx="1" fill="white" opacity="0.9"/>
                        <text x="35" y="42" text-anchor="middle" fill="#4f46e5" font-size="6" font-weight="bold" font-family="Arial">$</text>
                    </svg>
                    <div class="brand-logo-text">
                        <strong>{{ config('app.name', 'Laravel') }}</strong>
                        <span>نظام نقاط البيع والمحاسبة</span>
                    </div>
                </a>

                <h2 class="brand-headline">ابدأ إدارة أعمالك بذكاء</h2>
                <p class="brand-subheadline">انضم إلى آلاف التجار الذين يديرون مبيعاتهم ومخزونهم وحساباتهم من مكان واحد.</p>

                <ul class="brand-features">
                    <li>
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </span>
                        فواتير ومبيعات دقيقة
                    </li>
                    <li>
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </span>
                        تقارير مالية فورية
                    </li>
                    <li>
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                        </span>
                        مخزون وحسابات متكاملة
                    </li>
                </ul>
            </div>
        </aside>

        {{-- Form Panel (left side in RTL) --}}
        <main class="form-panel">
            <div class="form-card">
                <div class="form-header">
                    <h1>إنشاء حساب جديد</h1>
                    <p>أدخل بياناتك للوصول إلى لوحة التحكم</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">الاسم الكامل</label>
                        <div class="input-wrapper">
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="أدخل اسمك الكامل"
                                required
                                autofocus
                                autocomplete="name"
                                @class(['input-error' => $errors->has('name')])
                            />
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        @if ($errors->has('name'))
                            <div class="error-messages">
                                <ul>
                                    @foreach ((array) $errors->get('name') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@email.com"
                                required
                                autocomplete="username"
                                @class(['input-error' => $errors->has('email')])
                            />
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        @if ($errors->has('email'))
                            <div class="error-messages">
                                <ul>
                                    @foreach ((array) $errors->get('email') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="input-wrapper">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="أدخل كلمة مرور قوية"
                                required
                                autocomplete="new-password"
                                @class(['input-error' => $errors->has('password')])
                            />
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        @if ($errors->has('password'))
                            <div class="error-messages">
                                <ul>
                                    @foreach ((array) $errors->get('password') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                        <div class="input-wrapper">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="أعد إدخال كلمة المرور"
                                required
                                autocomplete="new-password"
                                @class(['input-error' => $errors->has('password_confirmation')])
                            />
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <div class="error-messages">
                                <ul>
                                    @foreach ((array) $errors->get('password_confirmation') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit">إنشاء الحساب</button>
                </form>

                <p class="form-footer">
                    لديك حساب؟ <a href="{{ route('login') }}">سجّل الدخول</a>
                </p>
            </div>
        </main>

    </div>
</body>
</html>
