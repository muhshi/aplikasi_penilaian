<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Penilaian Capaian Kinerja Pegawai (CKP) - BPS Kabupaten Demak. Platform terintegrasi untuk manajemen dokumen KIPAPP dan penilaian kinerja secara akurat.">
    
    <title>Portal CKP - BPS Kabupaten Demak</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bps.png') }}">

    <!-- Fonts: Plus Jakarta Sans for Premium Feel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0A2540',
                        accent: '#F8B803',
                        'bps-dark': '#06162a',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'marquee': 'marquee 40s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        marquee: {
                            '0%': { transform: 'translateX(0)' },
                            '100%': { transform: 'translateX(-50%)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .text-gradient {
            background: linear-gradient(135deg, #0A2540 0%, #F8B803 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .btn-premium {
            background: #0A2540;
            color: white;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-premium:hover {
            background: #14375a;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(10, 37, 64, 0.25);
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    </style>
</head>

<body class="text-slate-900 font-sans antialiased overflow-x-hidden selection:bg-primary/10" style="background-color: #FAFAF6;">

    <!-- Premium Background -->
    <div class="fixed inset-0 z-[-1] pointer-events-none overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #FAFAF6 0%, #F5F0EB 40%, #EEF4FF 100%);"></div>
        <div class="absolute top-[-15%] left-[-10%] w-[60%] h-[60%] rounded-full animate-pulse-slow" style="background: radial-gradient(circle, rgba(10,37,64,0.06) 0%, transparent 70%); filter: blur(80px);"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full animate-pulse-slow" style="animation-delay: 2s; background: radial-gradient(circle, rgba(248,184,3,0.05) 0%, transparent 70%); filter: blur(80px);"></div>
        <!-- Subtle Linen texture -->
        <div class="absolute inset-0 opacity-[0.015]" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect width='100' height='100' fill='none'/%3E%3Cpath d='M0 0h1v100H0zM10 0h1v100h-1zM20 0h1v100h-1zM30 0h1v100h-1zM40 0h1v100h-1zM50 0h1v100h-1zM60 0h1v100h-1zM70 0h1v100h-1zM80 0h1v100h-1zM90 0h1v100h-1zM0 0v1h100V0zM0 10v1h100v-1zM0 20v1h100v-1zM0 30v1h100v-1zM0 40v1h100v-1zM0 50v1h100v-1zM0 60v1h100v-1zM0 70v1h100v-1zM0 80v1h100v-1zM0 90v1h100v-1z' fill='%23000' opacity='0.05'/%3E%3C/svg%3E&quot;);"></div>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-6 inset-x-6 z-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between px-8 py-4 rounded-[2.5rem] glass shadow-xl shadow-primary/5 border border-primary/5">
                <div class="flex items-center gap-4 group cursor-pointer">
                    <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS" class="h-10 w-auto">
                    <div class="border-l-2 border-slate-200 pl-4">
                        <span class="block text-base font-black text-primary uppercase tracking-tighter leading-none mb-0.5">PENILAIAN CKP</span>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">BPS Kab. Demak</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    <a href="https://demakkab.bps.go.id" target="_blank" class="text-sm font-bold text-slate-500 hover:text-primary transition-colors relative group">
                        Profil Kantor
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                    </a>
                    @auth
                        <a href="{{ url('/admin') }}" class="btn-premium px-6 py-2.5 rounded-full text-sm font-bold">Dashboard</a>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}" class="btn-premium px-6 py-2.5 rounded-full text-sm font-bold">Masuk Aplikasi</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
            
            <div class="flex-1 text-center lg:text-left relative z-10 animate-reveal">
                <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full border border-primary/10 mb-8 bg-white/50 backdrop-blur">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-accent animate-ping"></span>
                    <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Portal Kinerja Pegawai</span>
                </div>

                <h1 class="text-5xl md:text-6xl font-black tracking-tight mb-6 leading-[1.1] text-primary">
                    Sistem Penilaian <br> <span class="text-gradient">Capaian Kinerja</span>
                </h1>

                <p class="text-lg text-slate-500 max-w-lg mb-10 leading-relaxed font-medium">
                    Transformasi digital manajemen kinerja <span class="font-bold text-primary">BPS Kabupaten Demak</span>. 
                    Kelola dokumen KIPAPP dan pantau rekapitulasi nilai secara akurat, cepat, dan transparan.
                </p>

                <!-- CTA -->
                <div class="flex flex-col sm:flex-row items-center gap-6 justify-center lg:justify-start">
                    @auth
                        <a href="{{ url('/admin') }}" class="btn-premium group px-10 py-4 rounded-3xl text-sm font-black shadow-2xl shadow-primary/20">
                            <span class="flex items-center gap-2">
                                Dashboard Admin
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}" class="btn-premium group px-10 py-4 rounded-3xl text-sm font-black shadow-2xl shadow-primary/20">
                            <span class="flex items-center gap-2">
                                Masuk ke Aplikasi
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                        </a>
                    @endauth
                </div>

                <!-- Stats Bar -->
                <div class="mt-12 transition-all duration-1000">
                    <div class="inline-flex items-center gap-1.5 p-1 rounded-[2rem] glass border border-primary/5 shadow-lg shadow-primary/5">
                        <div class="flex items-center gap-3 pl-3 pr-6 py-2 rounded-full cursor-default group/stat">
                            <div class="w-9 h-9 rounded-xl bg-primary/5 flex items-center justify-center text-primary group-hover/stat:bg-primary group-hover/stat:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="text-left">
                                <span class="block text-lg font-black text-primary leading-none tracking-tighter">38 Pegawai</span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Terdaftar di SDM</span>
                            </div>
                        </div>
                        <div class="w-px h-6 bg-slate-200"></div>
                        <div class="flex items-center gap-3 pl-3 pr-6 py-2 rounded-full cursor-default group/stat">
                            <div class="w-9 h-9 rounded-xl bg-accent/10 flex items-center justify-center text-primary group-hover/stat:bg-accent group-hover/stat:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div class="text-left">
                                <span class="block text-lg font-black text-primary leading-none tracking-tighter">Security</span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Internal Network</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="flex-1 relative hidden lg:block animate-float">
                <div class="relative w-[450px] mx-auto">
                    <div class="absolute -inset-10 bg-primary/5 rounded-[4rem] blur-[60px] opacity-40"></div>
                    <div class="relative bg-white p-3 rounded-[3.5rem] shadow-2xl border border-primary/10 overflow-hidden">
                        <img src="{{ asset('images/ckp-dashboard-actual.png') }}" alt="Dashboard Preview" class="w-full h-auto rounded-[2.8rem] shadow-inner opacity-90 transition-opacity group-hover:opacity-100">
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 glass px-6 py-3 rounded-2xl shadow-xl flex items-center gap-4 border-white/50">
                            <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-primary uppercase tracking-widest leading-none">Dashboard Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marquee -->
        <div class="relative mt-24 overflow-hidden py-4 border-y border-primary/5">
            <div class="flex gap-12 animate-marquee whitespace-nowrap opacity-60">
                @foreach (['Monitoring Lapkin', 'Verifikasi KIPAPP', 'Rekapitulasi Nilai', 'Sistem Informasi CKP', 'Manajemen Pegawai', 'Dokumentasi Digital'] as $label)
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-white rounded-full border border-primary/10 shadow-sm">
                        <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ $label }}</span>
                    </div>
                @endforeach
                <!-- Duplicate for seamless scroll -->
                @foreach (['Monitoring Lapkin', 'Verifikasi KIPAPP', 'Rekapitulasi Nilai', 'Sistem Informasi CKP', 'Manajemen Pegawai', 'Dokumentasi Digital'] as $label)
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-white rounded-full border border-primary/10 shadow-sm">
                        <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section class="py-32 px-6 bg-white/30 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 reveal">
                <h2 class="text-4xl font-black text-primary tracking-tighter mb-6">Fokus <span class="text-gradient">Layanan Utama</span></h2>
                <p class="text-slate-500 font-medium max-w-2xl mx-auto text-lg leading-relaxed italic">Instrumen digital untuk mendukung tertib administrasi kinerja di lingkungan BPS.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Card 1 -->
                <div class="group h-full reveal" style="transition-delay: 100ms">
                    <div class="h-full p-12 rounded-[3.5rem] bg-white border border-primary/5 shadow-lg hover:shadow-2xl hover:shadow-primary/10 transition-all duration-500 hover:-translate-y-3 relative overflow-hidden">
                        <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center mb-8 shadow-xl shadow-primary/20 transition-transform group-hover:rotate-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-primary mb-4 tracking-tighter">Monitoring Lapkin</h3>
                        <p class="text-slate-500 font-medium leading-relaxed text-sm">Pantau pengumpulan laporan kinerja bulanan pegawai secara terpusat dan rapi.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group h-full reveal" style="transition-delay: 200ms">
                    <div class="h-full p-12 rounded-[3.5rem] bg-white border border-accent/20 shadow-lg hover:shadow-2xl hover:shadow-accent/10 transition-all duration-500 hover:-translate-y-3 relative overflow-hidden">
                        <div class="w-16 h-16 rounded-2xl bg-accent text-primary flex items-center justify-center mb-8 shadow-xl shadow-accent/20 transition-transform group-hover:rotate-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-primary mb-4 tracking-tighter">Manajemen KIPAPP</h3>
                        <p class="text-slate-500 font-medium leading-relaxed text-sm">Organisasi dokumen capaian kinerja bulanan yang aman dan tersusun sesuai periode.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group h-full reveal" style="transition-delay: 300ms">
                    <div class="h-full p-12 rounded-[3.5rem] bg-white border border-primary/5 shadow-lg hover:shadow-2xl hover:shadow-primary/10 transition-all duration-500 hover:-translate-y-3 relative overflow-hidden">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800 text-white flex items-center justify-center mb-8 shadow-xl shadow-slate-800/20 transition-transform group-hover:rotate-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-primary mb-4 tracking-tighter">Rekapitulasi Nilai</h3>
                        <p class="text-slate-500 font-medium leading-relaxed text-sm">Otomatisasi penilaian kinerja untuk mempermudah evaluasi akhir bulan pegawai.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-24 pb-12 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 mb-20">
                <div class="lg:col-span-6 flex flex-col items-start gap-8">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white rounded-2xl">
                            <img src="{{ asset('images/logo-bps.png') }}" class="h-10 w-auto" alt="Logo Footer">
                        </div>
                        <div>
                            <h4 class="text-lg font-black tracking-tighter leading-none mb-1 uppercase">BPS KABUPATEN</h4>
                            <h4 class="text-lg font-black tracking-tighter text-accent leading-none uppercase">DEMAK</h4>
                        </div>
                    </div>
                    <p class="text-slate-400 font-medium leading-relaxed max-w-md">
                        Penyedia data statistik berkualitas untuk pembangunan berkelanjutan. Melayani dengan integritas, profesionalisme, dan amanah.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 text-slate-300 text-sm">
                            <svg class="w-5 h-5 text-accent mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>Jl. Sultan Hadiwijaya No. 23, Demak, Jawa Tengah 59515</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-300 text-sm">
                            <svg class="w-5 h-5 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 5z"></path></svg>
                            <span>(0291) 685445</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 flex flex-col gap-8">
                    <h5 class="text-xs font-black uppercase tracking-[0.3em] text-white/50">Layanan Internal</h5>
                    <ul class="space-y-4 font-bold text-slate-400 text-xs">
                        <li><a href="#" class="hover:text-accent transition-colors">Monitoring CKP</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Verifikasi KIPAPP</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Rekapitulasi Nilai</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-3 flex flex-col gap-8">
                    <h5 class="text-xs font-black uppercase tracking-[0.3em] text-white/50">Tautan Resmi</h5>
                    <ul class="space-y-4 font-bold text-slate-400 text-xs">
                        <li><a href="https://bps.go.id" target="_blank" class="hover:text-accent transition-colors">BPS RI</a></li>
                        <li><a href="https://jateng.bps.go.id" target="_blank" class="hover:text-accent transition-colors">BPS Jawa Tengah</a></li>
                        <li><a href="https://demakkab.bps.go.id" target="_blank" class="hover:text-accent transition-colors">BPS Kab. Demak</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-12 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
                <p class="text-slate-500 font-bold text-[10px] tracking-widest uppercase text-center md:text-left">
                    &copy; 2026 Badan Pusat Statistik Kabupaten Demak.
                </p>
                <div class="flex items-center gap-3 px-6 py-2 rounded-full bg-white/5 border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                    <span class="text-[8px] font-black uppercase tracking-[0.3em] text-slate-300 italic">Melayani Dengan Data</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Scroll Reveal Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
    </script>
</body>
</html>