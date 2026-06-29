@extends('layouts.affiliate')

@section('title', 'Mi Panel de Afiliado')

@section('content')
<div class="space-y-6 font-sans">
    
    <!-- SALUDO Y BIENVENIDA (GOOGLE STYLE) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <!-- Avatar circular de Perfil Google -->
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg border border-blue-100 shrink-0">
                {{ substr($afiliado->nombres, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">¡Hola, {{ $afiliado->nombres }}!</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium leading-relaxed">Bienvenido al panel virtual de autogestión de salud de ARS CMD.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('affiliate.carnet') }}" class="px-5 py-2.5 rounded-full text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-200 flex items-center gap-2 active:scale-95 shrink-0">
                <span class="material-symbols-outlined text-[16px]">badge</span>
                Ver Carnet Digital
            </a>
        </div>
    </div>

    <!-- SELECTOR DE NÚCLEO FAMILIAR (GOOGLE PROFILE SWITCHER STYLE) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-3.5 shadow-sm">
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
            <span class="block text-[10px] font-bold uppercase text-slate-500 tracking-wider">Núcleo Familiar Asegurado:</span>
        </div>
        <div class="flex flex-wrap gap-3">
            <!-- Titular (Activo por defecto) -->
            <div class="flex items-center gap-2 p-1.5 pr-4 bg-blue-50 border border-blue-200 rounded-full text-xs font-bold text-blue-700 cursor-pointer select-none hover:bg-blue-100/50 transition">
                <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">
                    {{ substr($afiliado->nombres, 0, 1) }}
                </div>
                <span>{{ $afiliado->nombres }} (Titular)</span>
            </div>

            <!-- Dependientes -->
            @foreach($dependientes as $dep)
                <div class="flex items-center gap-2 p-1.5 pr-4 bg-slate-50 border border-slate-200 rounded-full text-xs font-semibold text-slate-655 hover:bg-slate-100 hover:text-slate-800 transition cursor-pointer select-none">
                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-[10px]">
                        {{ substr($dep->nombres, 0, 1) }}
                    </div>
                    <span>{{ $dep->nombres }} ({{ optional($dep->parentesco)->descripcion ?? 'Dependiente' }})</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- BENTO DE KPIS GOOGLE CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- 1. Plan Activo -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200">
            <div>
                <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Régimen y Plan</span>
                <span class="text-base font-bold text-slate-800 block">{{ $afiliado->regimen_actual }}</span>
                <p class="text-xs text-slate-500 mt-2.5 leading-relaxed font-medium">Cobertura nacional activa con acceso garantizado a toda nuestra red médica.</p>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                <span class="text-slate-400 font-medium">Estatus de Cuenta</span>
                <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">Al Día</span>
            </div>
        </div>

        <!-- 2. Límite Medicamentos (Drive Storage Style) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col justify-between relative overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200">
            <div>
                <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Medicamentos</span>
                <span class="text-[10px] text-slate-500 block font-mono">Tope Anual: DOP {{ number_format($limiteAnualMED, 2) }}</span>
                
                <div class="mt-4">
                    <span class="text-[10px] text-slate-400 block font-mono">Disponible</span>
                    <span class="text-base font-extrabold text-emerald-600 block">DOP {{ number_format($disponibleMED, 2) }}</span>
                </div>

                @php
                    $percentageUsed = min(100, ($consumidoMED / $limiteAnualMED) * 100);
                @endphp
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $percentageUsed }}%"></div>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] font-semibold">
                <span class="text-slate-400 font-medium">Consumido:</span>
                <span class="text-slate-700 font-mono">DOP {{ number_format($consumidoMED, 2) }}</span>
            </div>
        </div>

        <!-- 3. Grupo Familiar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
            <div>
                <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Grupo Familiar</span>
                <span class="text-base font-bold text-slate-800 block">{{ $dependientes->count() }} Dependientes</span>
                <p class="text-xs text-slate-500 mt-2.5 leading-relaxed font-medium">Familiares directos registrados en tu núcleo que disfrutan de tus coberturas.</p>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('affiliate.dependientes') }}" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">family_history</span>
                    Ver dependientes
                    <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                </a>
            </div>
        </div>

        <!-- 4. Solicitud de Soporte -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
            <div>
                <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Autogestión rápida</span>
                <span class="text-base font-bold text-slate-800 block">Reembolsos y Solicitudes</span>
                <p class="text-xs text-slate-500 mt-2.5 leading-relaxed font-medium">Envía de forma ágil tus facturas fuera de red para reembolso inmediato.</p>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('affiliate.solicitudes') }}" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">add_circle</span>
                    Nueva Solicitud
                    <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- BANDEJA DE AUTORIZACIONES (GMAIL INBOX STYLE) -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-blue-600">inbox</span>
                Bandeja de Autorizaciones Recientes
            </h3>
            <a href="{{ route('affiliate.autorizaciones') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-wider flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">history</span>
                Ver todo el historial
            </a>
        </div>
        
        <div class="divide-y divide-slate-100 overflow-y-auto max-h-[350px]">
            @forelse($autorizaciones as $sol)
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 hover:bg-slate-50 transition duration-150 text-xs font-semibold text-slate-700">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <!-- Código de Autorización -->
                            <strong class="text-slate-800 font-mono font-bold block">{{ $sol->numero_autorizacion }}</strong>
                            <span class="text-[10px] text-slate-300 font-mono">|</span>
                            <!-- Procedimiento -->
                            <span class="text-slate-600 font-medium">{{ $sol->procedimiento }}</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span>Solicitado: {{ $sol->fecha_solicitud->format('d/m/Y H:i') }}</span>
                            <span>•</span>
                            <span>PSS: {{ $sol->pss->nombre }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0">
                        <span class="font-mono text-slate-800 font-bold">DOP {{ number_format($sol->monto_solicitado, 2) }}</span>
                        
                        <!-- Badges estilo Google -->
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ 
                            $sol->estado === 'Aprobada' ? 'bg-blue-50 text-blue-600 border-blue-100' : (
                            $sol->estado === 'Rechazada' ? 'bg-red-50 text-red-600 border-red-100' : (
                            $sol->estado === 'Auditoría' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-100 text-slate-500 border-slate-200'))
                        }}">{{ $sol->estado }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 font-medium">
                    No tienes historial de autorizaciones médicas recientes.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
