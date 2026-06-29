<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Aula Virtual - ARS CMD</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col bg-slate-900/5 lg:bg-[#f6f8fc] justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo -->
        <div class="flex justify-center">
            <a href="/" class="flex items-center space-x-3">
                <svg class="h-10 w-10 text-[#0be881]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="6" class="fill-[#49bcf7]" />
                    <path d="M12 7V17M7 12H17" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
        <h2 class="mt-6 text-center text-xl font-bold tracking-tight text-slate-800">
            Ingreso al Aula Virtual de Capacitación
        </h2>
        <p class="mt-1.5 text-center text-xs text-slate-500">
            O vuelve a la <a href="/" class="font-bold text-emerald-600 hover:text-emerald-700">página de inicio</a>.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 border border-slate-100 sm:rounded-3xl sm:px-10 shadow-sm">
            
            @if(session('error'))
                <div class="mb-4 p-3 bg-rose-50 border border-rose-105 rounded-2xl flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-500 text-sm" data-icon="cancel">cancel</span>
                    <span class="text-[11px] font-semibold text-rose-800">{{ session('error') }}</span>
                </div>
            @endif

            <form class="space-y-4" action="{{ route('classroom.login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Correo Electrónico</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required placeholder="estudiante@aula-virtual.com"
                               class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Contraseña</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required placeholder="••••••••"
                               class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-350 text-emerald-600 focus:ring-emerald-500">
                        <label for="remember_me" class="ml-2 block text-xs text-slate-500">Recordarme</label>
                    </div>

                    <div class="text-xs">
                        <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-500">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-2xl bg-emerald-600 py-3 px-4 text-xs font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-md shadow-emerald-650/15">
                        Ingresar al Aula
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-slate-400 font-bold uppercase tracking-wider text-[10px]">Ingreso Rápido Estudiantes</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2">
                    <form action="{{ route('classroom.login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="estudiante1@ars.com">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-xs text-left hover:bg-slate-50 transition shadow-sm font-medium">
                            <div>
                                <span class="font-bold text-slate-800 block">Estudiante Demo 1</span>
                                <span class="text-slate-400 text-[10px] block">Tiene 1 Curso Completado</span>
                            </div>
                            <span class="material-symbols-outlined text-emerald-600" data-icon="school">school</span>
                        </button>
                    </form>
                    
                    <form action="{{ route('classroom.login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="estudiante2@ars.com">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-xs text-left hover:bg-slate-50 transition shadow-sm font-medium">
                            <div>
                                <span class="font-bold text-slate-800 block">Estudiante Demo 2</span>
                                <span class="text-slate-400 text-[10px] block">Tiene Cursos En Progreso</span>
                            </div>
                            <span class="material-symbols-outlined text-emerald-600" data-icon="school">school</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
