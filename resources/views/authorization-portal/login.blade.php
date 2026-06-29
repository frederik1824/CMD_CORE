<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Portal Prestadores (PSS) - ARS CMD</title>
    
    <!-- Google Fonts: Rubik & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#403663',
                            blue: '#49bcf7',
                            accent: '#49bcf7'
                        }
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        rubik: ['Rubik', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f6f8fc;
            font-family: 'Roboto', sans-serif;
        }
        .selector-card {
            border: 2px solid #ecf0f3;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .selector-card.active {
            border-color: #49bcf7;
            background-color: rgba(73, 188, 247, 0.05);
            box-shadow: 0 10px 20px -5px rgba(73, 188, 247, 0.15);
            transform: translateY(-2px);
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-xl">
        <!-- Logo -->
        <div class="flex justify-center">
            <a href="/" class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-14 w-auto object-contain">
            </a>
        </div>
        <h2 class="mt-6 text-center text-2xl font-black font-rubik tracking-tight text-[#403663]">
            PORTAL MULTI-PRESTADOR PSS
        </h2>
        <p class="mt-1.5 text-center text-xs text-slate-505">
            Selecciona tu tipo de prestadora para ingresar al canal operativo correspondiente.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl" x-data="{ accessType: 'medical_center' }">
        <div class="bg-white py-8 px-6 border border-slate-100 sm:rounded-3xl sm:px-10 shadow-xl">
            
            @if(session('error'))
                <div class="mb-5 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 animate-headshake">
                    <span class="material-symbols-outlined text-rose-500 text-sm mt-0.5" data-icon="cancel">cancel</span>
                    <span class="text-xs font-semibold text-rose-900">{{ session('error') }}</span>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('pss.login.post') }}" method="POST">
                @csrf
                
                <!-- Tipo de Acceso Selector Cards -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 font-rubik">Tipo de Acceso PSS</label>
                    <input type="hidden" name="access_type" :value="accessType">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Centro Medico -->
                        <button type="button" @click="accessType = 'medical_center'"
                                :class="accessType === 'medical_center' ? 'active' : ''"
                                class="selector-card w-full text-left p-4 rounded-2xl bg-white flex flex-col justify-between hover:border-[#49bcf7]/50 hover:bg-slate-50/50 transition">
                            <div class="h-8 w-8 rounded-full bg-[#49bcf7]/10 flex items-center justify-center text-[#49bcf7] mb-3">
                                <i class="fas fa-hospital text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black font-rubik text-[#403663]">Centro Médico</h4>
                                <p class="text-[10px] text-slate-400 mt-1 font-semibold">Clínicas, Hospitales y Consultorios.</p>
                            </div>
                        </button>

                        <!-- Farmacia -->
                        <button type="button" @click="accessType = 'pharmacy'"
                                :class="accessType === 'pharmacy' ? 'active' : ''"
                                class="selector-card w-full text-left p-4 rounded-2xl bg-white flex flex-col justify-between hover:border-[#49bcf7]/50 hover:bg-slate-50/50 transition">
                            <div class="h-8 w-8 rounded-full bg-[#49bcf7]/10 flex items-center justify-center text-[#49bcf7] mb-3">
                                <i class="fas fa-prescription-bottle-medical text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black font-rubik text-[#403663]">Farmacia</h4>
                                <p class="text-[10px] text-slate-400 mt-1 font-semibold">Dispensación y recetas ambulatorias.</p>
                            </div>
                        </button>

                        <!-- Laboratorio -->
                        <button type="button" @click="accessType = 'laboratory'"
                                :class="accessType === 'laboratory' ? 'active' : ''"
                                class="selector-card w-full text-left p-4 rounded-2xl bg-white flex flex-col justify-between hover:border-[#49bcf7]/50 hover:bg-slate-50/50 transition">
                            <div class="h-8 w-8 rounded-full bg-[#49bcf7]/10 flex items-center justify-center text-[#49bcf7] mb-3">
                                <i class="fas fa-vials text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black font-rubik text-[#403663]">Laboratorio</h4>
                                <p class="text-[10px] text-slate-400 mt-1 font-semibold">Pruebas diagnósticas y resultados.</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider font-rubik">Usuario (Correo)</label>
                        <div class="mt-1.5">
                            <input id="email" name="email" type="email" autocomplete="email" required placeholder="correo@prestador.com"
                                   class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-[#49bcf7] focus:ring-[#49bcf7] shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider font-rubik">Contraseña</label>
                        <div class="mt-1.5">
                            <input id="password" name="password" type="password" required placeholder="••••••••"
                                   class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-[#49bcf7] focus:ring-[#49bcf7] shadow-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="pss_code" class="block text-xs font-bold text-slate-500 uppercase tracking-wider font-rubik">Código PSS / RNC <span class="text-slate-400 font-normal">(Opcional)</span></label>
                    <div class="mt-1.5">
                        <input id="pss_code" name="pss_code" type="text" placeholder="Ej. 101002034"
                               class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-[#49bcf7] focus:ring-[#49bcf7] shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#49bcf7] focus:ring-[#49bcf7]">
                        <label for="remember_me" class="ml-2 block text-xs text-slate-500 font-medium">Recordar sesión</label>
                    </div>

                    <div class="text-xs">
                        <a href="#" class="font-bold text-[#49bcf7] hover:text-[#31a3e6]">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-2xl bg-[#49bcf7] py-3.5 px-4 text-xs font-black uppercase tracking-wider text-white hover:bg-[#31a3e6] focus:outline-none focus:ring-2 focus:ring-[#49bcf7] focus:ring-offset-2 transition shadow-md shadow-blue-500/10">
                        Ingresar al Canal Operativo
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-4 text-slate-400 font-bold uppercase tracking-wider text-[9px] font-rubik">Accesos Rápidos Demo</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-2">
                    <!-- Demo Clinic -->
                    <form action="{{ route('pss.login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="pss@ars.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="access_type" value="medical_center">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-2xl border border-[#49bcf7]/20 bg-blue-50/30 hover:bg-blue-50 text-left text-[11px] font-bold text-[#403663] transition flex items-center gap-2">
                            <i class="fas fa-hospital text-[#49bcf7] text-xs"></i>
                            <span>Clínica Central</span>
                        </button>
                    </form>

                    <!-- Demo Pharmacy -->
                    <form action="{{ route('pss.login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="farmacia@demo.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="access_type" value="pharmacy">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-2xl border border-[#49bcf7]/20 bg-blue-50/30 hover:bg-blue-50 text-left text-[11px] font-bold text-[#403663] transition flex items-center gap-2">
                            <i class="fas fa-prescription-bottle-medical text-[#49bcf7] text-xs"></i>
                            <span>Farmacias GBC</span>
                        </button>
                    </form>

                    <!-- Demo Lab -->
                    <form action="{{ route('pss.login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="laboratorio@demo.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="access_type" value="laboratory">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-2xl border border-[#49bcf7]/20 bg-blue-50/30 hover:bg-blue-50 text-left text-[11px] font-bold text-[#403663] transition flex items-center gap-2">
                            <i class="fas fa-vials text-[#49bcf7] text-xs"></i>
                            <span>Amadita Lab</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
