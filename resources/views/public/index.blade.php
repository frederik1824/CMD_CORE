@extends('layouts.public')

@section('title', 'ARS CMD | Protegemos tu salud, simplificamos tu experiencia')

@section('content')

    <!-- CUSTOM STYLE FOR FLOATING BUBBLES AND LECTOR HERO FORM -->
    <style>
        .custom-banner {
            background: linear-gradient(135deg, #f4f6fc 0%, #eef1f8 100%);
            padding: 130px 0 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        /* Floating Circles Background */
        .circle-bg {
            position: absolute;
            border: 2px dashed rgba(73, 188, 247, 0.15);
            border-radius: 50%;
            top: 50%;
            left: 70%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 1;
        }
        .circle-bg-1 { width: 350px; height: 350px; }
        .circle-bg-2 { width: 500px; height: 500px; }
        .circle-bg-3 { width: 650px; height: 650px; }

        /* Doctor Image Container */
        .doctor-container {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .doctor-image {
            max-height: 520px;
            width: auto;
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.1));
            display: inline-block;
        }

        /* Floating Bubbles Styling */
        .floating-bubble {
            position: absolute;
            padding: 10px 24px;
            border-radius: 30px;
            font-family: 'Rubik', sans-serif;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            z-index: 3;
            animation: float-animation 6s ease-in-out infinite;
        }
        
        .bubble-pink {
            background: linear-gradient(135deg, #ff5e97 0%, #ff2a70 100%);
            box-shadow: 0 10px 20px rgba(255, 42, 112, 0.35);
            top: 15%;
            right: 0%;
            animation-delay: 0s;
        }
        .bubble-purple {
            background: linear-gradient(135deg, #a176ff 0%, #763cff 100%);
            box-shadow: 0 10px 20px rgba(118, 60, 255, 0.35);
            top: 35%;
            right: -10%;
            animation-delay: 1.5s;
        }
        .bubble-orange {
            background: linear-gradient(135deg, #ff9f59 0%, #ff751a 100%);
            box-shadow: 0 10px 20px rgba(255, 117, 26, 0.35);
            top: 60%;
            right: -5%;
            animation-delay: 3s;
        }
        .bubble-green {
            background: linear-gradient(135deg, #2cf58c 0%, #0be881 100%);
            box-shadow: 0 10px 20px rgba(11, 232, 129, 0.35);
            bottom: 10%;
            right: 5%;
            animation-delay: 4.5s;
        }
        .bubble-blue {
            background: linear-gradient(135deg, #6fd3ff 0%, #49bcf7 100%);
            box-shadow: 0 10px 20px rgba(73, 188, 247, 0.35);
            top: 45%;
            left: 5%;
            animation-delay: 2.2s;
        }

        @keyframes float-animation {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        /* Lector Banner Form styling matching template exactly */
        .lector-banner-form {
            background: #ffffff;
            border-radius: 40px;
            padding: 8px 10px 8px 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
            max-width: 580px;
            margin-top: 35px;
        }
        .lector-banner-form input {
            border: none !important;
            outline: none !important;
            padding: 10px 0;
            width: 40%;
            font-size: 13px;
            color: #777;
        }
        .lector-banner-form .dropdown-container {
            border-left: 1px solid #e2e8f0;
            padding-left: 20px;
            margin-left: 10px;
            width: 45%;
            display: flex;
            align-items: center;
        }
        .lector-banner-form select {
            border: none !important;
            outline: none !important;
            background: transparent;
            font-size: 13px;
            color: #555;
            width: 100%;
            cursor: pointer;
        }
        .lector-banner-form button {
            background: #ff7555;
            color: white;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
            flex-shrink: 0;
            margin-left: auto;
        }
        .lector-banner-form button:hover {
            background: #49bcf7;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(73, 188, 247, 0.3);
        }
    </style>

    <!-- 1. HERO BANNER SECTION (High Fidelity Lector Index.html style) -->
    <section class="custom-banner" id="inicio">
        <!-- Dashed circles behind doctor -->
        <div class="circle-bg circle-bg-1"></div>
        <div class="circle-bg circle-bg-2"></div>
        <div class="circle-bg circle-bg-3"></div>

        <div class="container">
            <div class="row align-items-center">
                <!-- Text Column -->
                <div class="col-lg-6 z-10">
                    <div class="banner-content text-left">
                        <h2 class="banner-text text-[#403663]" style="font-size: 2.8rem; font-weight: 800; line-height: 1.15; font-family: 'Rubik', sans-serif;">
                            Cuidamos tu Bienestar, Simplificamos tu Vida
                        </h2>
                        <p class="banner-desc text-slate-500 mt-3" style="font-size: 14px; line-height: 1.6; max-width: 500px;">
                            En ARS CMD te ofrecemos la red de prestadores de salud más amplia y la tecnología de pre-aprobación más rápida del país para que tú y tu familia siempre estén protegidos.
                        </p>
                        
                        <!-- Lector Banner Form -->
                        <form class="lector-banner-form" action="/portal-autorizaciones" method="GET">
                            <input type="email" placeholder="Tu correo electrónico" required>
                            <div class="dropdown-container">
                                <select>
                                    <option>Plan Salud PDSS 11.0</option>
                                    <option>Plan Complementario Premium</option>
                                    <option>Plan Especial de Pensionados</option>
                                    <option>Planes Corporativos</option>
                                </select>
                            </div>
                            <button type="submit">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Image and Floating Bubbles Column -->
                <div class="col-lg-6 position-relative d-none d-lg-block">
                    <div class="doctor-container">
                        <!-- Animated Bubbles -->
                        <div class="floating-bubble bubble-pink">Cobertura</div>
                        <div class="floating-bubble bubble-purple">Eficacia</div>
                        <div class="floating-bubble bubble-orange">Prevención</div>
                        <div class="floating-bubble bubble-green">Experiencia</div>
                        <div class="floating-bubble bubble-blue">Bienestar</div>

                        <!-- Doctor Image -->
                        <img src="{{ asset('assets/images/hero_doctor.png') }}" alt="Médico ARS CMD" class="doctor-image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner section ending here -->

    <!-- 2. SERVICES SECTION -->
    <section class="services" id="servicios" style="margin-top: -50px; position: relative; z-index: 10;">
        <div class="container">
            <div class="row padding-x justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="post-item bg-white p-4 shadow-sm border border-slate-100 rounded-3xl text-center">
                        <div class="service-icon mb-3" style="font-size: 2.5rem; color: #49bcf7;">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h5 class="font-bold text-slate-800 mb-2">Soluciones de Salud</h5>
                        <p class="text-slate-500 text-xs">Acceso inmediato a consultas, laboratorios, imágenes y procedimientos con la cobertura más sólida del mercado.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="post-item bg-white p-4 shadow-sm border border-slate-100 rounded-3xl text-center">
                        <div class="service-icon mb-3" style="font-size: 2.5rem; color: #49bcf7;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="font-bold text-slate-800 mb-2">Garantía de Aprobación</h5>
                        <p class="text-slate-500 text-xs">Motor automático de reglas para procesar autorizaciones en segundos directamente desde tu prestadora.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="post-item bg-white p-4 shadow-sm border border-slate-100 rounded-3xl text-center">
                        <div class="service-icon mb-3" style="font-size: 2.5rem; color: #49bcf7;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h5 class="font-bold text-slate-800 mb-2">Asesoría Médica 24/7</h5>
                        <p class="text-slate-500 text-xs">Un equipo de auditores y representantes de servicio al cliente listos para apoyarte ante cualquier urgencia.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- services section ending here -->

    <!-- 3. ABOUT SECTION -->
    <section class="about padding-tb" id="nosotros" style="padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="post-thumb pr-lg-4 position-relative">
                        <img src="{{ asset('assets/images/about_consulting.png') }}" alt="Sobre ARS CMD" class="img-fluid rounded-3xl shadow-md">
                        <div class="about-video" style="position: absolute; bottom: 30px; right: 30px; z-index: 5;">
                            <a href="https://www.youtube.com/embed/tnxBWV3DN4k" data-rel="lightcase" title="Video Institucional" class="av-icon flex items-center justify-center pulse3" style="background: #49bcf7; width: 60px; height: 60px; border-radius: 50%; color: white;">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="post-content mt-4 mt-lg-0">
                        <h5 class="text-[#49bcf7] font-bold text-sm uppercase">Cuidamos de ti</h5>
                        <h2 class="font-bold text-slate-800 mb-4" style="font-size: 2.2rem; line-height: 1.2;">Líderes en Gestión de Salud Integral en la República Dominicana</h2>
                        <p class="text-slate-500 text-xs mb-3">En ARS CMD nos enfocamos en ofrecer un servicio de excelencia y calidez humana. Contamos con una infraestructura tecnológica que interconecta a miles de médicos y clínicas a nivel nacional para autorizar tus procedimientos al instante.</p>
                        <p class="text-slate-500 text-xs mb-4">Desde consultas preventivas hasta coberturas especializadas de alto costo, gestionamos tu póliza con total transparencia y eficacia.</p>
                        <a href="#contacto" class="btn" style="background: #49bcf7; color: white; padding: 10px 25px; border-radius: 25px; font-weight: bold;">Contáctanos</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. SERVICES STYLE-2 SECTION -->
    <section class="services style-2 padding-tb" style="background: #f8fafc; padding: 80px 0;">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="font-bold text-slate-800">Servicios y Soluciones a tu Alcance</h2>
                <p class="text-slate-500 text-xs max-w-2xl mx-auto mt-2">Disponemos de herramientas modernas tanto para afiliados individuales como para prestadores y empresas contratantes.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-01.png') }}" alt="Servicio 1" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Auditoría Clínica</h5>
                        <p class="text-slate-500 text-[11px]">Evaluación médica especializada de procedimientos complejos y medicamentos de alto costo para tu tranquilidad.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-02.png') }}" alt="Servicio 2" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Gestión de Riesgos</h5>
                        <p class="text-slate-500 text-[11px]">Planes preventivos y programas especiales de promoción de la salud para afiliados hipertensos o diabéticos.</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-03.png') }}" alt="Servicio 3" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Portal PSS</h5>
                        <p class="text-slate-500 text-[11px]">Plataforma ágil para prestadores (clínicas, laboratorios, médicos) para gestionar autorizaciones en tiempo real.</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-04.png') }}" alt="Servicio 4" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Planes PDSS 11.0</h5>
                        <p class="text-slate-500 text-[11px]">Cobertura médica ajustada a las normativas del Plan Básico de Salud de la Sisalril con beneficios adicionales.</p>
                    </div>
                </div>
                <!-- Card 5 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-05.png') }}" alt="Servicio 5" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Reembolsos Digitales</h5>
                        <p class="text-slate-500 text-[11px]">Solicita el reembolso de tus facturas médicas de forma 100% digital a través del portal de afiliados.</p>
                    </div>
                </div>
                <!-- Card 6 -->
                <div class="post-item bg-white p-5 rounded-3xl shadow-2xs border border-slate-100 hover:translate-y-[-5px] transition duration-300">
                    <div class="post-thumb mb-3">
                        <img src="{{ asset('themes/lector/assets/images/service/icon/Service-Icon-06.png') }}" alt="Servicio 6" class="h-12 w-auto">
                    </div>
                    <div class="post-content">
                        <h5 class="font-bold text-slate-800 mb-2">Red Nacional</h5>
                        <p class="text-slate-500 text-[11px]">Convenios con las principales clínicas, laboratorios clínicos y centros de diagnóstico en todo el país.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. PROGRESS SECTION -->
    <section class="progress style-2 padding-tb" style="padding: 80px 0;">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="font-bold text-slate-800">¿Cómo funciona la Autorización de Servicios?</h2>
                <p class="text-slate-500 text-xs max-w-xl mx-auto mt-2">Nuestra plataforma está integrada con el catálogo de prestaciones para garantizar un proceso automatizado y sin fricciones.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 text-center">
                <div class="post-item bg-white p-4 rounded-3xl border border-slate-100 shadow-2xs">
                    <div class="post-thumb mx-auto mb-3" style="width: 60px;">
                        <img src="{{ asset('themes/lector/assets/images/progress/icon/01.png') }}" alt="Paso 1" class="w-full">
                    </div>
                    <div class="post-content">
                        <span class="font-bold text-slate-800 text-xs">1. Consulta Cobertura</span>
                    </div>
                </div>
                <div class="post-item bg-white p-4 rounded-3xl border border-slate-100 shadow-2xs">
                    <div class="post-thumb mx-auto mb-3" style="width: 60px;">
                        <img src="{{ asset('themes/lector/assets/images/progress/icon/02.png') }}" alt="Paso 2" class="w-full">
                    </div>
                    <div class="post-content">
                        <span class="font-bold text-slate-800 text-xs">2. Registro de Solicitud</span>
                    </div>
                </div>
                <div class="post-item bg-white p-4 rounded-3xl border border-slate-100 shadow-2xs">
                    <div class="post-thumb mx-auto mb-3" style="width: 60px;">
                        <img src="{{ asset('themes/lector/assets/images/progress/icon/03.png') }}" alt="Paso 3" class="w-full">
                    </div>
                    <div class="post-content">
                        <span class="font-bold text-slate-800 text-xs">3. Validación de Reglas</span>
                    </div>
                </div>
                <div class="post-item bg-white p-4 rounded-3xl border border-slate-100 shadow-2xs">
                    <div class="post-thumb mx-auto mb-3" style="width: 60px;">
                        <img src="{{ asset('themes/lector/assets/images/progress/icon/04.png') }}" alt="Paso 4" class="w-full">
                    </div>
                    <div class="post-content">
                        <span class="font-bold text-slate-800 text-xs">4. Entrega de Comprobante</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. RED MÉDICA -->
    <section class="case-study padding-tb" id="buscador-red" style="background: #f8fafc; padding: 80px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="section-header pr-lg-4">
                        <h2 class="font-bold text-slate-800">Contamos con una Amplia Red Contratada</h2>
                        <p class="text-slate-500 text-xs mt-3 mb-4">Trabajamos estrechamente con los centros médicos y laboratorios de mayor prestigio a nivel nacional para ofrecerte un servicio de salud de la más alta calidad.</p>
                        <a href="/portal-autorizaciones" class="btn" style="background: #49bcf7; color: white; padding: 10px 25px; border-radius: 25px; font-weight: bold;">Ver Buscador de Red</a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="bg-white p-4 rounded-3xl border border-slate-200/60 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="font-bold text-slate-700 text-xs uppercase tracking-wider">Principales Clínicas Asociadas</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase">Red Metropolitana</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($prestadoras as $prestadora)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col gap-1">
                                    <span class="font-bold text-slate-800 text-xs">{{ $prestadora->nombre }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">RNC: {{ $prestadora->rnc }}</span>
                                    <span class="text-[10px] text-emerald-600 font-bold block mt-1"><i class="fas fa-check-circle mr-1"></i>Contrato Vigente</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. ACHIEVEMENT SECTION -->
    <section class="achievement padding-tb" style="padding: 80px 0; background: url('{{ asset('assets/images/achievement_bg.png') }}') no-repeat center center; background-size: cover; color: white;">
        <div class="container">
            <div class="row padding-x align-items-center">
                <div class="col-lg-6">
                    <div class="counter-up">
                        <h2 class="font-bold text-white mb-2" style="font-size: 2.2rem;">¿Buscas Planes de Salud Premium?</h2>
                        <p class="text-blue-100 text-xs mb-5">Afíliate hoy mismo a ARS CMD y experimenta la diferencia de contar con una aseguradora de salud centrada en ti.</p>
                        <div class="flex gap-6">
                            <div>
                                <span class="font-black text-2xl" style="font-size: 2.5rem; display: block;">150k+</span>
                                <p class="text-blue-200 text-[10px] uppercase font-bold mt-1">Afiliados Activos</p>
                            </div>
                            <div>
                                <span class="font-black text-2xl" style="font-size: 2.5rem; display: block;">2.5k+</span>
                                <p class="text-blue-200 text-[10px] uppercase font-bold mt-1">Prestadores de Salud</p>
                            </div>
                            <div>
                                <span class="font-black text-2xl" style="font-size: 2.5rem; display: block;">99.8%</span>
                                <p class="text-blue-200 text-[10px] uppercase font-bold mt-1">Tasa de Aprobación</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/10 text-white space-y-4">
                        <h4 class="font-bold text-sm">Garantía de Satisfacción 2026</h4>
                        <p class="text-blue-100 text-xs leading-relaxed">Nuestra red nacional de asistencia médica y el servicio de liquidación rápida de reclamos han sido galardonados con las mejores calificaciones del sector de salud.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. NEWS SECTION -->
    <section class="blog-section padding-tb" id="noticias" style="padding: 80px 0;">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="font-bold text-slate-800">Noticias & Comunicados</h2>
                <p class="text-slate-500 text-xs max-w-xl mx-auto mt-2">Mantente informado sobre las últimas novedades de ARS CMD, campañas de salud preventiva e inclusiones a nuestra red.</p>
            </div>
            <div class="row">
                @foreach($noticias as $noticia)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="post-item bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-xs">
                            <div class="post-thumb" style="height: 180px; overflow: hidden;">
                                <img src="{{ $noticia['image'] }}" alt="{{ $noticia['title'] }}" class="w-full h-full object-cover">
                            </div>
                            <div class="post-content p-4 space-y-2">
                                <span class="text-[9px] text-slate-400 font-bold block">{{ $noticia['date'] }}</span>
                                <h5 class="font-bold text-slate-800 leading-snug"><a href="#" class="hover:text-[#49bcf7] transition">{{ $noticia['title'] }}</a></h5>
                                <p class="text-slate-550 text-[11px] leading-relaxed">{{ $noticia['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 9. CONTACT SECTION -->
    <section class="support padding-tb" id="contacto" style="padding: 80px 0; background: #f8fafc;">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="font-bold text-slate-800">¿Necesitas Ayuda? Contacta a Nuestro Equipo</h2>
                <p class="text-slate-500 text-xs mt-2">Llámanos al <span class="font-bold text-primary">809-555-8888</span> o escríbenos a <span class="font-bold text-primary">info@saludintegral.com.do</span></p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-xs">
                        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-4">Envíanos una Solicitud de Información</h4>
                        <form action="#">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold mb-4">
                                <div>
                                    <label class="block text-slate-400 text-[10px] uppercase mb-1.5 font-bold tracking-wider">Nombre Completo</label>
                                    <input type="text" placeholder="Tu nombre" class="w-full rounded-xl border border-slate-350 p-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] uppercase mb-1.5 font-bold tracking-wider">Correo Electrónico</label>
                                    <input type="email" placeholder="Tu correo" class="w-full rounded-xl border border-slate-350 p-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-slate-400 text-[10px] uppercase mb-1.5 font-bold tracking-wider">Tipo de Consulta</label>
                                    <select class="w-full rounded-xl border border-slate-350 p-2.5 text-xs text-slate-800 focus:outline-none bg-white">
                                        <option>Información de Planes de Salud</option>
                                        <option>Afiliación Individual / Empresa</option>
                                        <option>Soporte de Autorizaciones</option>
                                        <option>Consulta de Reembolso</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button class="btn" type="button" style="background: #49bcf7; color: white; padding: 10px 25px; border-radius: 25px; font-weight: bold; font-size: 11px; text-transform: uppercase;">Enviar Consulta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
