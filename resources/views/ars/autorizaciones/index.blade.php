@extends('layouts.ars')

@section('title', 'Bandeja de Autorizaciones')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="pb-2">
        <h2 class="text-xl font-bold tracking-tight text-slate-900">
            Bandeja de Autorizaciones Médicas: <span class="text-[#0b57d0] capitalize">{{ str_replace('_', ' ', $estado ?? 'General') }}</span>
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Auditoría y control de solicitudes de procedimientos y coberturas de salud enviadas por las PSS.
        </p>
    </div>

    <!-- Pestañas de Navegación (Tabs Estilo Workspace) -->
    <div class="border-b border-slate-100">
        <nav class="-mb-px flex space-x-6 text-xs font-bold">
            <a href="{{ route('ars.autorizaciones.index') }}" class="pb-3 px-1 border-b-2 transition {{ Route::is('ars.autorizaciones.index') ? 'border-[#0b57d0] text-[#0b57d0]' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-350' }}">Todos</a>
            <a href="{{ route('ars.autorizaciones.pendientes') }}" class="pb-3 px-1 border-b-2 transition {{ Route::is('ars.autorizaciones.pendientes') ? 'border-[#0b57d0] text-[#0b57d0]' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-350' }}">Pendientes & Docs</a>
            <a href="{{ route('ars.autorizaciones.auditoria') }}" class="pb-3 px-1 border-b-2 transition {{ Route::is('ars.autorizaciones.auditoria') ? 'border-[#0b57d0] text-[#0b57d0]' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-350' }}">Auditoría Médica</a>
            <a href="{{ route('ars.autorizaciones.aprobadas') }}" class="pb-3 px-1 border-b-2 transition {{ Route::is('ars.autorizaciones.aprobadas') ? 'border-[#0b57d0] text-[#0b57d0]' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-350' }}">Aprobadas</a>
            <a href="{{ route('ars.autorizaciones.rechazadas') }}" class="pb-3 px-1 border-b-2 transition {{ Route::is('ars.autorizaciones.rechazadas') ? 'border-[#0b57d0] text-[#0b57d0]' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-350' }}">Rechazadas</a>
        </nav>
    </div>

    <!-- Barra de Filtros (Búsqueda Rápida) -->
    @if(Route::is('ars.autorizaciones.index'))
        <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-100">
            <form action="{{ route('ars.autorizaciones.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
                <div>
                    <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 font-semibold">Buscar Solicitud</label>
                    <input type="text" name="search" id="search" value="{{ $search }}" class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all" placeholder="Número de Autorización (Ej: AUT-2026...)">
                </div>
                <div>
                    <label for="estado_select" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 font-semibold">Estado</label>
                    <select name="estado" id="estado_select" class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 py-2.5 px-4 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all bg-white">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" {{ $estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Auditoría" {{ $estado === 'Auditoría' ? 'selected' : '' }}>Auditoría</option>
                        <option value="Aprobada" {{ $estado === 'Aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="Rechazada" {{ $estado === 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="Pendiente Documento" {{ $estado === 'Pendiente Documento' ? 'selected' : '' }}>Pendiente Documento</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-5 py-2.5 border border-transparent rounded-full shadow-xs text-xs font-bold text-white bg-[#0b57d0] hover:bg-blue-700 hover:shadow-sm transition">
                        Filtrar
                    </button>
                    <a href="{{ route('ars.autorizaciones.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-200 rounded-full text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-350 transition">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    @endif

    <!-- Listado en Formato Tarjetas de Solicitudes -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($autorizaciones as $aut)
            <div class="bg-white shadow-sm rounded-2xl border border-slate-100/80 overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-blue-100 hover:scale-[1.01] transition duration-200">
                <!-- Encabezado de Tarjeta -->
                <div class="px-6 py-4.5 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Autorización</span>
                        <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="text-xs font-bold text-slate-800 hover:text-[#0b57d0] transition font-mono">{{ $aut->numero_autorizacion }}</a>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ 
                        $aut->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : (
                        $aut->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-200' : (
                        $aut->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-amber-50 text-amber-700 border-amber-250'))
                    }}">{{ $aut->estado }}</span>
                </div>
                
                <!-- Cuerpo de Tarjeta -->
                <div class="p-6 space-y-3.5 text-xs flex-1">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Afiliado</span>
                        <span class="font-bold text-slate-700 block">
                            @if($aut->afiliado)
                                {{ $aut->afiliado->nombre_completo }}
                            @else
                                <span class="text-slate-400 font-semibold">Desconocido</span>
                            @endif
                        </span>
                        <span class="text-slate-400 block font-mono">Tipo: <span class="capitalize">{{ $aut->afiliado_type }}</span></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Prestador (PSS)</span>
                        <span class="text-slate-600 font-semibold block">{{ $aut->pss->nombre }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Servicio</span>
                        <span class="text-slate-600 block font-semibold">
                            {{ optional($aut->servicio)->descripcion
                                ?? optional($aut->servicioPdss)->coverage_description
                                ?? $aut->procedimiento
                                ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-50 mt-3">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Monto Solicitado</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">${{ number_format($aut->monto_solicitado, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Prioridad</span>
                            <span class="inline-flex mt-0.5 items-center px-2 py-0.5 rounded text-[9px] font-bold border {{ 
                                $aut->prioridad === 'Alta' ? 'bg-rose-50 text-rose-700 border-rose-200' : ($aut->prioridad === 'Media' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-500 border-slate-200')
                            }}">{{ $aut->prioridad }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer de Tarjeta -->
                <div class="px-6 py-3.5 bg-slate-50/50 border-t border-slate-50 flex justify-between items-center text-[10px] text-slate-400">
                    <span class="font-mono">Solicitud: {{ $aut->fecha_solicitud->format('d/m/Y H:i') }}</span>
                    <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="font-bold text-[#0b57d0] hover:text-blue-800 transition uppercase tracking-wider">Auditar &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center text-slate-400 text-xs shadow-sm rounded-2xl border border-slate-100">
                No hay solicitudes de autorizaciones médicas registradas en esta bandeja.
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($autorizaciones->hasPages())
        <div class="mt-6">
            {{ $autorizaciones->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
