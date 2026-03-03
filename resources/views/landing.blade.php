<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Penilaian CKP — BPS Kabupaten Demak</title>
    <meta name="description" content="Sistem Informasi Penilaian Capaian Kinerja Pegawai (CKP) BPS Kabupaten Demak">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a1628 0%, #0f2744 30%, #132f52 50%, #0d2240 70%, #091a30 100%);
            color: #ffffff;
            overflow-x: hidden;
            position: relative;
        }

        /* Subtle animated background pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(30, 90, 170, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(20, 70, 140, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(25, 80, 155, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Floating decorative elements */
        .bg-decoration {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.07;
            pointer-events: none;
            z-index: 0;
        }

        .bg-decoration-1 {
            width: 500px;
            height: 500px;
            background: #3b82f6;
            top: -150px;
            right: -100px;
            animation: float1 20s ease-in-out infinite;
        }

        .bg-decoration-2 {
            width: 400px;
            height: 400px;
            background: #1d4ed8;
            bottom: -100px;
            left: -100px;
            animation: float2 25s ease-in-out infinite;
        }

        @keyframes float1 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-30px, 30px);
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(20px, -20px);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem 1.5rem;
            max-width: 580px;
            width: 100%;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo */
        .logo-wrapper {
            margin-bottom: 2rem;
        }

        .logo-wrapper img {
            width: 90px;
            height: auto;
            filter: drop-shadow(0 4px 20px rgba(59, 130, 246, 0.3));
            transition: transform 0.3s ease;
        }

        .logo-wrapper img:hover {
            transform: scale(1.05);
        }

        /* Titles */
        .app-title {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-subtitle {
            font-size: 1.1rem;
            font-weight: 500;
            color: #93c5fd;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        /* Divider */
        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            border-radius: 2px;
            margin: 0 auto 1.5rem;
        }

        /* Description */
        .description {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #94a3b8;
            margin-bottom: 2.5rem;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
        }

        /* CTA Button */
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.9rem 2.2rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            border: 1px solid rgba(59, 130, 246, 0.3);
            box-shadow:
                0 4px 15px rgba(37, 99, 235, 0.3),
                0 1px 3px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            letter-spacing: 0.01em;
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow:
                0 8px 25px rgba(37, 99, 235, 0.4),
                0 2px 6px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .cta-button:active {
            transform: translateY(0);
        }

        .cta-button svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s ease;
        }

        .cta-button:hover svg {
            transform: translateX(3px);
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1;
            text-align: center;
            padding: 1.2rem;
            color: #475569;
            font-size: 0.8rem;
            letter-spacing: 0.02em;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .app-title {
                font-size: 1.5rem;
            }

            .app-subtitle {
                font-size: 0.95rem;
            }

            .description {
                font-size: 0.88rem;
            }

            .logo-wrapper img {
                width: 72px;
            }
        }
    </style>
</head>

<body>
    <!-- Background decorations -->
    <div class="bg-decoration bg-decoration-1"></div>
    <div class="bg-decoration bg-decoration-2"></div>

    <main class="container">
        <!-- Logo BPS -->
        <div class="logo-wrapper">
            <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS Kabupaten Demak">
        </div>

        <!-- Title -->
        <h1 class="app-title">Aplikasi Penilaian CKP</h1>
        <p class="app-subtitle">BPS Kabupaten Demak</p>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Description -->
        <p class="description">
            Sistem informasi untuk mengelola pengiriman, monitoring, dan penilaian
            Capaian Kinerja Pegawai (CKP) di lingkungan BPS Kabupaten Demak.
        </p>

        <!-- CTA Button -->
        <a href="{{ url('/admin') }}" class="cta-button" id="login-button">
            Masuk ke Aplikasi
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </main>

    <!-- Footer -->
    <footer class="footer">
        &copy; {{ date('Y') }} BPS Kabupaten Demak. Seluruh hak cipta dilindungi.
    </footer>
</body>

</html>