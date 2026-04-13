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
                        accent: '#38BDF8',
                        'bps-dark': '#06162a',
                        'bps-light': '#E8EDF2',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 8s ease-in-out infinite',
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
            background: linear-gradient(135deg, #0A2540 0%, #38BDF8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.4);
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

    <!-- Premium Background (Synced with Dashboard) -->
    <div class="fixed inset-0 z-[-1] pointer-events-none overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #E8EDF2 0%, #F5F7FA 40%, #FFFFFF 100%);"></div>
        <div class="absolute top-[-15%] left-[-10%] w-[60%] h-[60%] rounded-full animate-pulse-slow" style="background: radial-gradient(circle, rgba(10,37,64,0.06) 0%, transparent 70%); filter: blur(80px);"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full animate-pulse-slow" style="animation-delay: 2s; background: radial-gradient(circle, rgba(56,189,248,0.05) 0%, transparent 70%); filter: blur(80px);"></div>
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

            <!-- Refined Dashboard Mockup -->
            <div class="flex-1 relative hidden lg:block reveal" style="transition-delay: 400ms">
                <div class="relative w-[500px] mx-auto transform hover:scale-[1.02] transition-transform duration-700">
                    <!-- Layered Glows for Depth -->
                    <div class="absolute -inset-20 bg-primary/10 rounded-[6rem] blur-[100px] opacity-30 animate-pulse-slow"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[140%] h-[140%] bg-white/20 rounded-full blur-[120px] opacity-20 pointer-events-none"></div>
                    
                    <!-- The Mockup Container -->
                    <div class="relative bg-white/40 p-1.5 rounded-[4rem] shadow-[0_40px_100px_-20px_rgba(10,37,64,0.15)] backdrop-blur-md border border-white/60 group overflow-hidden">
                        <div class="bg-white rounded-[3.8rem] p-3 shadow-inner">
                            <div class="relative rounded-[3rem] overflow-hidden border border-primary/5 shadow-2xl">
                                <img src="{{ asset('images/ckp-dashboard-actual.png') }}" alt="Dashboard Preview" class="w-full h-auto opacity-95 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 via-transparent to-transparent pointer-events-none"></div>
                            </div>
                        </div>

                        <!-- Floating Indicators -->
                        <div class="absolute -top-6 -right-6 glass px-6 py-4 rounded-3xl shadow-xl border-white group-hover:-translate-y-2 transition-transform duration-500">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Sistem</span>
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-primary uppercase tracking-tighter">Aktif Terhubung</span>
                            </div>
                        </div>

                        <div class="absolute -bottom-8 -left-8 glass px-8 py-5 rounded-[2rem] shadow-xl border-white group-hover:translate-x-2 transition-transform duration-500">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-primary leading-none">Terverifikasi</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">Data BPS RI Valid</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Refined Features Section (Premium Bentukan) -->
    <section class="py-40 px-6 relative">
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-[#FAFAF6] to-transparent"></div>
        
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl mx-auto text-center mb-24 reveal">
                <div class="inline-block px-4 py-1 rounded-full bg-primary/5 border border-primary/5 mb-6">
                    <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">Fitur Keunggulan</span>
                </div>
                <h2 class="text-5xl font-black text-primary tracking-tighter mb-8 leading-[1.1]">
                    Fokus <span class="text-gradient">Integritas Penilaian</span>
                </h2>
                <div class="w-24 h-1.5 bg-accent mx-auto rounded-full mb-8"></div>
                <p class="text-slate-500 font-medium text-lg leading-relaxed">
                    Platform khusus yang dirancang untuk menjaga objektivitas dan efisiensi birokrasi di lingkungan Badan Pusat Statistik.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 xl:gap-12">
                <!-- Feature Card 1 -->
                <div class="group reveal" style="transition-delay: 100ms">
                    <div class="relative h-full p-1 w-full rounded-[3.5rem] bg-gradient-to-br from-primary/5 to-transparent hover:from-primary/10 transition-all duration-700">
                        <div class="h-full bg-white rounded-[3.4rem] p-12 shadow-sm border border-primary/[0.03] flex flex-col items-start gap-8 group-hover:shadow-[0_40px_80px_-20px_rgba(10,37,64,0.08)] transition-all duration-700 group-hover:-translate-y-4">
                            <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-xl shadow-transparent group-hover:shadow-primary/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-primary mb-4 tracking-tight">Monitoring CKP</h3>
                                <p class="text-slate-500 font-medium leading-relaxed text-sm">Pemantauan *real-time* pengumpulan capaian kinerja bulanan untuk memastikan tertib waktu seluruh pegawai.</p>
                            </div>
                            <div class="mt-auto pt-4 flex items-center gap-2 text-primary/30 group-hover:text-primary transition-colors text-[10px] font-black uppercase tracking-widest">
                                <span>Learn More</span>
                                <svg class="w-4 h-4 translate-x-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Card 2 (Highlighted) -->
                <div class="group reveal" style="transition-delay: 200ms">
                    <div class="relative h-full p-1 w-full rounded-[3.5rem] bg-gradient-to-br from-accent/20 to-transparent">
                        <div class="h-full bg-white rounded-[3.4rem] p-12 shadow-xl border border-accent/20 flex flex-col items-start gap-8 group-hover:shadow-[0_40px_80px_-20px_rgba(56,189,248,0.15)] transition-all duration-700 group-hover:-translate-y-4">
                            <div class="w-16 h-16 rounded-2xl bg-accent text-white flex items-center justify-center shadow-xl shadow-accent/20 transition-all duration-500 group-hover:scale-110">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-primary mb-4 tracking-tight">Verifikasi KIPAPP</h3>
                                <p class="text-slate-500 font-medium leading-relaxed text-sm">Sistem pengarsipan digital bukti fisik kinerja (KIPAPP) yang terintegrasi dengan tautan dokumen *cloud* yang aman.</p>
                            </div>
                            <div class="mt-auto pt-4">
                                <span class="bg-accent/10 px-4 py-1.5 rounded-full text-[10px] font-black text-primary uppercase tracking-widest">Fitur Unggulan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Card 3 -->
                <div class="group reveal" style="transition-delay: 300ms">
                    <div class="relative h-full p-1 w-full rounded-[3.5rem] bg-gradient-to-br from-slate-200 to-transparent hover:from-slate-300 transition-all duration-700">
                        <div class="h-full bg-white rounded-[3.4rem] p-12 shadow-sm border border-primary/[0.03] flex flex-col items-start gap-8 group-hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.06)] transition-all duration-700 group-hover:-translate-y-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white flex items-center justify-center group-hover:bg-primary group-hover:rotate-12 transition-all duration-500 shadow-xl group-hover:shadow-primary/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-primary mb-4 tracking-tight">Rekap Nilai Otomatis</h3>
                                <p class="text-slate-500 font-medium leading-relaxed text-sm">Dapatkan rekapitulasi nilai akhir secara instan dari berbagai penilai (Ketua Tim) dengan perhitungan rata-rata yang akurat.</p>
                            </div>
                            <div class="mt-auto pt-4 flex items-center gap-2 text-slate-100 group-hover:text-primary transition-colors text-[10px] font-black uppercase tracking-widest">
                                <span>Automation Ready</span>
                                <svg class="w-4 h-4 translate-x-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Redesigned Footer -->
    <footer class="relative bg-primary pt-32 pb-16 overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent/5 rounded-full blur-[100px]"></div>

        <div class="max-w-7xl mx-auto px-8 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-20 mb-24">
                <!-- Branding Section -->
                <div class="max-w-md">
                    <div class="flex items-center gap-5 mb-10">
                        <div class="p-3 bg-white rounded-[1.25rem] shadow-xl">
                            <img src="{{ asset('images/logo-bps.png') }}" class="h-12 w-auto" alt="Logo Footer">
                        </div>
                        <div class="h-10 w-px bg-white/20"></div>
                        <div>
                            <h4 class="text-xl font-black tracking-tight leading-none text-white uppercase italic">Portal <span class="text-accent">CKP</span></h4>
                            <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em] mt-1">BPS Kabupaten Demak</p>
                        </div>
                    </div>
                    <p class="text-white/60 font-medium leading-relaxed text-lg mb-10 italic">
                        "Menyediakan data statistik berkualitas untuk Indonesia Maju. Melayani dengan integritas dan profesionalisme tinggi."
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-accent hover:text-primary transition-all duration-500 group">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-accent hover:text-primary transition-all duration-500 group">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.916 4.916 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.213c9.057 0 14.01-7.496 14.01-13.986 0-.21 0-.423-.015-.634a10.025 10.025 0 002.457-2.548l-.047-.02z"/></svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-accent hover:text-primary transition-all duration-500 group">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.264-.069-1.644-.069-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Contact & Location Section -->
                <div class="lg:w-1/3 flex flex-col gap-10">
                    <h5 class="text-sm font-black uppercase tracking-[0.3em] text-accent">Kantor Pusat Kami</h5>
                    <div class="space-y-8">
                        <div class="flex items-start gap-6 group">
                            <div class="w-12 h-12 rounded-[1rem] bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-primary transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            <div>
                                <span class="block text-white/40 text-[10px] uppercase font-black tracking-widest mb-1">Alamat</span>
                                <p class="text-white/80 font-bold text-sm leading-relaxed">Jl. Sultan Hadiwijaya No. 23, Demak, <br> Jawa Tengah 59515</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6 group">
                            <div class="w-12 h-12 rounded-[1rem] bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-primary transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 5z"></path></svg>
                            </div>
                            <div>
                                <span class="block text-white/40 text-[10px] uppercase font-black tracking-widest mb-1">Telepon & Email</span>
                                <p class="text-white/80 font-bold text-sm">(0291) 685445</p>
                                <p class="text-white/80 font-bold text-sm mt-1">bps3321@bps.go.id</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horizontal Links (Replaces Image 2 columns) -->
            <div class="py-12 border-t border-white/10 flex flex-wrap justify-center gap-10">
                <a href="https://bps.go.id" target="_blank" class="text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-accent transition-colors">BPS RI</a>
                <a href="https://jateng.bps.go.id" target="_blank" class="text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-accent transition-colors">BPS Jawa Tengah</a>
                <a href="https://demakkab.bps.go.id" target="_blank" class="text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-accent transition-colors">BPS Kab. Demak</a>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
                <p class="text-white/20 font-black text-[9px] tracking-[0.3em] uppercase">
                    &copy; 2026 Badan Pusat Statistik Kabupaten Demak.
                </p>
                <div class="flex items-center gap-4">
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-white/20 italic">v2.0.4 - Premium Edition</span>
                    <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                        <span class="text-[8px] font-black text-white/40 tracking-widest leading-none">SYSTEM ONLINE</span>
                    </div>
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