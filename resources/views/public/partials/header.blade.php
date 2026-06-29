<!-- mobile-nav section start here -->
<div class="mobile-menu xl:hidden" x-data="{ open: false }">
    <nav class="mobile-header primary-menu bg-white/95 backdrop-blur-md border-b border-slate-100 py-3 px-4 flex justify-between items-center shadow-xs">
        <div class="header-logo">
            <a href="/" class="flex items-center space-x-2 shrink-0">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-10 w-auto object-contain">
            </a>
        </div>
        <button class="header-bar p-2 text-slate-700 focus:outline-none hover:bg-slate-50 rounded-lg transition" @click="open = !open">
            <i class="fas fa-bars text-lg" x-show="!open"></i>
            <i class="fas fa-times text-lg" x-show="open" x-cloak></i>
        </button>
    </nav>
    <nav class="menu" x-show="open" x-transition x-cloak>
        <div class="mobile-menu-area bg-white border-b border-slate-100 shadow-lg py-6 px-4">
            <div class="mobile-menu-area-inner">
                <ul class="m-menu flex flex-col space-y-4 text-sm font-semibold text-slate-800">
                    <li><a href="#inicio" @click="open = false" class="hover:text-[#49bcf7] transition">Inicio</a></li>
                    <li><a href="#nosotros" @click="open = false" class="hover:text-[#49bcf7] transition">Nosotros</a></li>
                    <li><a href="#servicios" @click="open = false" class="hover:text-[#49bcf7] transition">Servicios</a></li>
                    <li><a href="#buscador-red" @click="open = false" class="hover:text-[#49bcf7] transition">Red Médica</a></li>
                    <li><a href="#noticias" @click="open = false" class="hover:text-[#49bcf7] transition">Noticias</a></li>
                    <li><a href="#contacto" @click="open = false" class="hover:text-[#49bcf7] transition">Contacto</a></li>
                </ul>
                <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col gap-3">
                    <a href="/plataforma-virtual" class="w-full text-center py-3 rounded-full text-xs font-bold text-slate-800 border border-slate-200 bg-slate-50 hover:bg-slate-100 transition">
                        Portal Afiliado
                    </a>
                    <a href="/portal-autorizaciones" class="w-full text-center py-3 rounded-full text-xs font-bold text-white bg-[#49bcf7] hover:bg-[#31a3e6] transition shadow-sm shadow-[#49bcf7]/15">
                        Portal Prestadores (PSS)
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>
<!-- mobile-nav section ending here -->

<!-- header section start here -->
<header class="header-section d-none d-xl-block sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-2xs transition-all duration-300">
    <!-- Slim top utility bar -->
    <div class="bg-slate-50 border-b border-slate-100 py-1.5 px-6">
        <div class="container mx-auto flex justify-between items-center text-[11px] text-slate-500 font-medium">
            <div class="flex items-center space-x-6">
                <span><i class="fas fa-phone-alt mr-1.5 text-[#49bcf7]"></i>Asistencia: <strong>809-555-8888</strong></span>
                <span><i class="fas fa-map-marker-alt mr-1.5 text-[#49bcf7]"></i>Av. Winston Churchill No. 1024, Santo Domingo</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#contacto" class="hover:text-[#49bcf7] transition">Preguntas Frecuentes</a>
                <span class="text-slate-200">|</span>
                <a href="#contacto" class="hover:text-[#49bcf7] transition">Oficinas y Sucursales</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <div class="header-bottom py-3.5">
        <div class="container mx-auto">
            <nav class="flex items-center justify-between">
                <!-- Real Logo -->
                <a href="/" class="logo flex items-center shrink-0">
                    <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-12 w-auto object-contain hover:scale-102 transition duration-200">
                </a>
                
                <!-- Nav Links -->
                <ul class="flex items-center space-x-8 text-sm font-semibold text-slate-900 tracking-tight">
                    <li>
                        <a href="#inicio" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Inicio
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#nosotros" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Nosotros
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#servicios" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Servicios
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#buscador-red" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Red Médica
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#noticias" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Noticias
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#contacto" class="relative py-2 hover:text-[#49bcf7] transition group">
                            Contacto
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49bcf7] transition-all duration-200 group-hover:w-full"></span>
                        </a>
                    </li>
                </ul>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-3 shrink-0">
                    <a href="/plataforma-virtual" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-800 border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-350 transition duration-200">
                        Portal Afiliado
                    </a>
                    <a href="/portal-autorizaciones" class="px-6 py-2.5 rounded-full text-xs font-bold text-white bg-[#49bcf7] hover:bg-[#31a3e6] shadow-sm shadow-[#49bcf7]/15 hover:shadow-md hover:translate-y-[-1px] active:translate-y-[0px] transition duration-250">
                        Portal Prestadores (PSS)
                    </a>
                </div>
            </nav>
        </div>
    </div>
</header>
<!-- header section ending here -->
