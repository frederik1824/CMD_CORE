<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Plataforma Virtual - ARS CMD</title>
    
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
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-12 w-auto object-contain">
            </a>
        </div>
        <h2 class="mt-6 text-center text-xl font-bold tracking-tight text-slate-800">
            Ingreso a tu Cuenta de Afiliado
        </h2>
        <p class="mt-1.5 text-center text-xs text-slate-500">
            O vuelve a la <a href="/" class="font-bold text-blue-600 hover:text-blue-700">página de inicio</a>.
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

            <form class="space-y-4" action="{{ route('affiliate.login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="cedula" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Cédula de Identidad</label>
                    <div class="mt-1">
                        <input id="cedula" name="cedula" type="text" required placeholder="000-0000000-0"
                               class="block w-full rounded-2xl border-slate-200 py-3 text-xs placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-2xl bg-blue-600 py-3 px-4 text-xs font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-md shadow-blue-650/15">
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-slate-400 font-bold uppercase tracking-wider text-[10px]">Cuentas de Prueba Demo</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2">
                    @foreach($demoAffiliates as $demo)
                        <form action="{{ route('affiliate.login.post') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $demo->id }}">
                            <button type="submit" class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 py-3 px-4 text-xs text-left hover:bg-slate-50 transition shadow-sm font-medium">
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $demo->nombre_completo }}</span>
                                    <span class="text-slate-400 text-[10px] block">Cédula: {{ $demo->cedula }}</span>
                                </div>
                                <span class="text-[9px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full font-bold">
                                    OK
                                </span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</body>
</html>
