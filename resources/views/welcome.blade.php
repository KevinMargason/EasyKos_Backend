<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — {{ config('app.name', 'EasyKos') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream: #F8F4EE;
            --ink: #1A1612;
            --ink-light: #6B6560;
            --terracotta: #C9614A;
            --terracotta-light: #F0E0DA;
            --gold: #B8924A;
            --white: #FFFFFF;
            --border: #E4DEDA;
        }

        html { font-size: 16px; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 3rem;
            border-bottom: 1px solid var(--border);
            background: var(--white);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .logo span { color: var(--terracotta); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--ink-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }

        .nav-link:hover { color: var(--ink); background: var(--cream); }

        .nav-link-primary {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--white);
            background: var(--ink);
            text-decoration: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: background 0.2s, transform 0.15s;
        }

        .nav-link-primary:hover { background: var(--terracotta); transform: translateY(-1px); }

        /* ── Main ── */
        .page-main {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 5rem 3rem;
            gap: 5rem;
            align-items: center;
        }

        /* ── Left: Content ── */
        .content-col { display: flex; flex-direction: column; }

        .error-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--terracotta);
            margin-bottom: 2rem;
        }

        .error-tag::before {
            content: '';
            display: block;
            width: 24px;
            height: 1px;
            background: var(--terracotta);
        }

        .headline {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--ink);
            margin-bottom: 1.5rem;
        }

        .headline em {
            font-style: italic;
            color: var(--terracotta);
        }

        .description {
            font-size: 1.0625rem;
            line-height: 1.75;
            color: var(--ink-light);
            font-weight: 300;
            max-width: 420px;
            margin-bottom: 2.5rem;
        }

        .cta-group {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.875rem 2rem;
            background: var(--ink);
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9375rem;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(26,22,18,0.12);
        }

        .btn-primary:hover {
            background: var(--terracotta);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201,97,74,0.25);
        }

        .btn-primary svg { transition: transform 0.2s; }
        .btn-primary:hover svg { transform: translateX(3px); }

        .btn-ghost {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--ink-light);
            text-decoration: none;
            padding: 0.875rem 0;
            border-bottom: 1px solid transparent;
            transition: color 0.2s, border-color 0.2s;
        }

        .btn-ghost:hover { color: var(--ink); border-color: var(--ink); }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 1px solid var(--border);
        }

        .divider-label {
            font-size: 0.8125rem;
            color: var(--ink-light);
            white-space: nowrap;
        }

        .quick-links {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .quick-link {
            font-size: 0.8125rem;
            color: var(--ink-light);
            text-decoration: none;
            padding: 0.3rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 100px;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
        }

        .quick-link:hover {
            border-color: var(--terracotta);
            color: var(--terracotta);
            background: var(--terracotta-light);
        }

        /* ── Right: Visual ── */
        .visual-col {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .visual-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 60% 40%, var(--terracotta-light) 0%, var(--cream) 70%);
            border-radius: 24px;
            z-index: 0;
        }

        .visual-card {
            position: relative;
            z-index: 1;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 40px rgba(26,22,18,0.08), 0 2px 8px rgba(26,22,18,0.04);
            width: 100%;
            max-width: 360px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-dot-group { display: flex; gap: 0.375rem; }

        .card-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .dot-red { background: #FF6B6B; }
        .dot-yellow { background: #FFD93D; }
        .dot-green { background: #6BCB77; }

        .card-badge {
            font-size: 0.6875rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gold);
            background: #FBF5EA;
            border: 1px solid #E8D8B0;
            padding: 0.25rem 0.625rem;
            border-radius: 100px;
        }

        .error-code-display {
            text-align: center;
            padding: 1.5rem 0;
        }

        .error-num {
            font-family: 'Playfair Display', serif;
            font-size: 5.5rem;
            font-weight: 700;
            letter-spacing: -0.04em;
            line-height: 1;
            background: linear-gradient(135deg, var(--terracotta) 0%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-sub {
            font-size: 0.8125rem;
            color: var(--ink-light);
            margin-top: 0.5rem;
            font-weight: 300;
        }

        .card-footer {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .card-footer-label {
            font-size: 0.75rem;
            color: var(--ink-light);
            margin-bottom: 0.875rem;
            font-weight: 300;
        }

        .card-bar {
            height: 6px;
            background: var(--border);
            border-radius: 100px;
            overflow: hidden;
            margin-bottom: 0.625rem;
        }

        .card-bar-fill {
            height: 100%;
            border-radius: 100px;
        }

        .card-gif-wrap {
            border-radius: 10px;
            overflow: hidden;
            margin: 0.25rem 0 0.5rem;
            border: 1px solid var(--border);
        }

        .card-gif {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            filter: grayscale(15%);
            transition: filter 0.4s ease;
        }

        .card-gif:hover { filter: grayscale(0%); }

        .bar-1 { width: 72%; background: linear-gradient(90deg, var(--terracotta), var(--gold)); }
        .bar-2 { width: 45%; background: var(--terracotta-light); border: 1px solid var(--border); }
        .bar-3 { width: 88%; background: linear-gradient(90deg, var(--terracotta), var(--gold)); opacity: 0.4; }

        /* Decorative blobs */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.25;
            z-index: 0;
        }

        .deco-1 {
            width: 200px;
            height: 200px;
            background: var(--terracotta);
            top: -60px;
            right: -40px;
            filter: blur(60px);
        }

        .deco-2 {
            width: 140px;
            height: 140px;
            background: var(--gold);
            bottom: -30px;
            left: -30px;
            filter: blur(50px);
        }

        /* ── Footer ── */
        .site-footer {
            text-align: center;
            padding: 1.5rem 3rem;
            border-top: 1px solid var(--border);
            font-size: 0.8125rem;
            color: var(--ink-light);
            font-weight: 300;
        }

        /* ── Page-load animation ── */
        .content-col > * {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.6s ease forwards;
        }

        .content-col > *:nth-child(1) { animation-delay: 0.05s; }
        .content-col > *:nth-child(2) { animation-delay: 0.15s; }
        .content-col > *:nth-child(3) { animation-delay: 0.25s; }
        .content-col > *:nth-child(4) { animation-delay: 0.35s; }
        .content-col > *:nth-child(5) { animation-delay: 0.45s; }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .visual-col {
            opacity: 0;
            animation: fadeIn 0.8s ease 0.3s forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .site-header { padding: 1.25rem 1.5rem; }

            .page-main {
                grid-template-columns: 1fr;
                padding: 3rem 1.5rem;
                gap: 3rem;
            }

            .visual-col { order: -1; }

            .visual-card { max-width: 100%; }

            .divider { flex-direction: column; align-items: flex-start; gap: 0.75rem; }

            .site-footer { padding: 1.25rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .headline { font-size: 2.25rem; }
            .nav-link { display: none; }
        }
    </style>
</head>

<body>

    {{-- ── Header ── --}}
    @if (Route::has('login'))
    <header class="site-header">
        <a href="/" class="logo">Easy<span>Kos</span></a>

        <nav class="nav-links">
            @auth
                <a href="{{ url('/dashboard') }}" class="nav-link-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="nav-link">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="nav-link-primary">Daftar</a>
                @endif
            @endauth
        </nav>
    </header>
    @endif

    {{-- ── Main ── --}}
    <main class="page-main">

        {{-- Left: Text Content --}}
        <div class="content-col">

            <span class="error-tag">404 — Halaman tidak ditemukan</span>

            <h1 class="headline">
                Ups, kamu<br>
                <em>nyasar</em> nih!
            </h1>

            <p class="description">
                Halaman yang kamu cari sepertinya sudah pindah, dihapus, atau memang tidak pernah ada. Tenang saja, kos impianmu masih menunggumu di tempat yang benar.
            </p>

            <div class="cta-group">
                <a href="https://easykos.vercel.app" class="btn-primary">
                    <span>Kembali ke Beranda</span>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="javascript:history.back()" class="btn-ghost">← Halaman sebelumnya</a>
            </div>

            <div class="divider">
                <span class="divider-label">Atau cari di sini:</span>
                <div class="quick-links">
                    <a href="#" class="quick-link">Cari Kos</a>
                    <a href="#" class="quick-link">Tentang Kami</a>
                    <a href="#" class="quick-link">Hubungi Kami</a>
                </div>
            </div>

        </div>

        {{-- Right: Visual --}}
        <div class="visual-col">
            <div class="deco-circle deco-1"></div>
            <div class="deco-circle deco-2"></div>
            <div class="visual-bg"></div>

            <div class="visual-card">
                <div class="card-header">
                    <div class="card-dot-group">
                        <div class="card-dot dot-red"></div>
                        <div class="card-dot dot-yellow"></div>
                        <div class="card-dot dot-green"></div>
                    </div>
                    <span class="card-badge">EasyKos</span>
                </div>

                <div class="error-code-display">
                    <div class="error-num">404</div>
                    <p class="error-sub">Halaman tidak dapat ditemukan</p>
                </div>

                <div class="card-gif-wrap">
                    <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExM2dpdXl6MDZhMHhzandjamwyMzV0Z2h3aWJ1Y2lpbnZ0djZ6NTlsNSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/eVuh33eNQFzfAEM9gS/giphy.gif"
                         alt="Lost illustration"
                         class="card-gif">
                </div>

                <div class="card-footer">
                    <p class="card-footer-label">Halaman yang tersedia</p>
                    <div class="card-bar"><div class="card-bar-fill bar-1"></div></div>
                    <div class="card-bar"><div class="card-bar-fill bar-2"></div></div>
                    <div class="card-bar"><div class="card-bar-fill bar-3"></div></div>
                </div>
            </div>
        </div>

    </main>

    {{-- ── Footer ── --}}
    <footer class="site-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'EasyKos') }}. Semua hak dilindungi.
    </footer>

</body>
</html>