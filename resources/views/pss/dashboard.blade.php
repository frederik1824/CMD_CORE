@extends('layouts.pss')

@section('title', 'Inicio - Portal Prestadores')

@section('content')
<div class="space-y-6">
    <!-- Encabezado PSS -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">¡Bienvenido, {{ $pss->nombre }}!</h2>
            <p class="text-xs text-slate-400 mt-0.5">Conectado como Portal de Transmisión Clínica de la Red ARS CMD.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('pss.autorizaciones.nueva') }}" class="inline-flex items-center px-5 py-2.5 rounded-full shadow-sm text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md transition">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Nueva Autorización
            </a>
        </div>
    </div>

    <!-- Contadores Métricas PSS -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <!-- Total Solicitudes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] hover:shadow-md hover:border-teal-100 transition duration-200">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mis Solicitudes</span>
                <span class="text-2xl font-extrabold text-slate-800 block mt-1">{{ $metricas['total'] }}</span>
            </div>
            <div class="p-2.5 bg-teal-50 text-teal-600 rounded-full">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </div>

        <!-- Solicitudes Aprobadas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] hover:shadow-md hover:border-teal-100 transition duration-200">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aprobadas</span>
                <span class="text-2xl font-extrabold text-emerald-600 block mt-1">{{ $metricas['aprobadas'] }}</span>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-full">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Solicitudes Rechazadas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] hover:shadow-md hover:border-teal-100 transition duration-200">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rechazadas</span>
                <span class="text-2xl font-extrabold text-rose-600 block mt-1">{{ $metricas['rechazadas'] }}</span>
            </div>
            <div class="p-2.5 bg-rose-50 text-rose-600 rounded-full">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Solicitudes Pendientes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] hover:shadow-md hover:border-teal-100 transition duration-200">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendientes Auditoría</span>
                <span class="text-2xl font-extrabold text-amber-600 block mt-1">{{ $metricas['pendientes'] }}</span>
            </div>
            <div class="p-2.5 bg-amber-50 text-amber-600 rounded-full">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Últimas Solicitudes -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Últimas Solicitudes Tramitadas</h3>
                <a href="{{ route('pss.solicitudes') }}" class="inline-flex items-center gap-1.5 text-[10px] font-bold text-teal-600 hover:text-teal-700 transition uppercase tracking-wider">
                    Ver todas
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto">
                @forelse($ultimasSolicitudes as $sol)
                    <div class="p-4 flex items-center justify-between text-xs hover:bg-slate-50/50 transition">
                        <div>
                            <span class="font-bold text-slate-800 block font-mono">{{ $sol->numero_autorizacion }}</span>
                            <span class="text-slate-500 mt-1 block">{{ optional($sol->servicio)->descripcion ?? optional($sol->servicioPdss)->coverage_description ?? $sol->procedimiento ?? '—' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">Fecha: {{ $sol->fecha_solicitud->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-800 block">${{ number_format($sol->monto_solicitado, 2) }}</span>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ 
                                $sol->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : (
                                $sol->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-200' : (
                                $sol->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-amber-50 text-amber-700 border-amber-250'))
                            }}">{{ $sol->estado }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">No has tramitado solicitudes hoy.</div>
                @endforelse
            </div>
        </div>

        <!-- Consultas Recientes -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Historial de Búsquedas de Cobertura</h3>
            </div>
            <div class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto">
                @forelse($consultasRecientes as $log)
                    <div class="p-4 text-xs hover:bg-slate-50/50 transition">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="font-bold text-slate-700">{{ $log->accion }}</h4>
                            <span class="text-[9px] text-slate-400 font-mono">{{ $log->fecha_registro->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-[9px] text-slate-400 font-mono leading-normal">Detalles: {{ json_encode($log->detalles) }}</p>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">No has realizado consultas de afiliados recientemente.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
