@extends('layouts.ars')
@section('title', 'Reporte de Autorizaciones')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('ars.autorizaciones.dashboard') }}" class="hover:text-blue-600">Autorizaciones</a>
                <span>/</span><span class="text-gray-600">Reporte de Gestión</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Reporte de Autorizaciones Médicas</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-normal">Análisis y listado consolidado de solicitudes de autorizaciones</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 transition print:hidden">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir Reporte
        </button>
    </div>

    {{-- Filtros (Ocultos en impresión) --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm print:hidden">
        <form action="{{ route('ars.autorizaciones.reporte') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Fecha Desde</label>
                <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b57d0] focus:bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b57d0] focus:bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Prestadora (PSS)</label>
                <select name="pss_id" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b57d0] focus:bg-white transition">
                    <option value="">Todas...</option>
                    @foreach($pssList as $pss)
                        <option value="{{ $pss->id }}" {{ $pss_id == $pss->id ? 'selected' : '' }}>{{ $pss->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b57d0] focus:bg-white transition">
                    <option value="">Todos...</option>
                    @foreach(['Pendiente','Aprobada','Rechazada','Auditoría','Pendiente Documento','Anulada'] as $est)
                        <option value="{{ $est }}" {{ $estado == $est ? 'selected' : '' }}>{{ $est }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-[#0b57d0] text-white text-sm font-medium hover:bg-[#0842a0] transition shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('ars.autorizaciones.reporte') }}" class="px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition flex items-center justify-center" title="Limpiar Filtros">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Resumen del Reporte --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
        $resumenKpis = [
            ['label' => 'Total Solicitado', 'val' => $resumen['total'], 'color' => 'text-blue-700 bg-blue-50', 'border' => 'border-blue-100'],
            ['label' => 'Aprobadas', 'val' => $resumen['aprobadas'], 'color' => 'text-green-700 bg-green-50', 'border' => 'border-green-100'],
            ['label' => 'Rechazadas', 'val' => $resumen['rechazadas'], 'color' => 'text-red-700 bg-red-50', 'border' => 'border-red-100'],
            ['label' => 'En Auditoría', 'val' => $resumen['auditoria'], 'color' => 'text-purple-700 bg-purple-50', 'border' => 'border-purple-100'],
        ];
        @endphp
        @foreach($resumenKpis as $rk)
        <div class="bg-white rounded-2xl border {{ $rk['border'] }} p-4 flex flex-col gap-0.5 hover:shadow-sm transition">
            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">{{ $rk['label'] }}</span>
            <span class="text-2xl font-bold {{ explode(' ', $rk['color'])[0] }}">{{ $rk['val'] }}</span>
        </div>
        @endforeach
        <div class="col-span-2 md:col-span-1 bg-white rounded-2xl border border-emerald-100 p-4 flex flex-col gap-0.5 hover:shadow-sm transition">
            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Monto Total Aprobado</span>
            <span class="text-xl font-bold text-emerald-700 truncate">RD$ {{ number_format($resumen['monto_total'], 2) }}</span>
        </div>
    </div>

    {{-- Detalle del Listado --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Listado Consolidado</h2>
            <span class="text-xs text-gray-400">{{ $autorizaciones->count() }} registros encontrados</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">N° Autorización</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">PSS</th>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-left">Canal</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-right">Solicitado</th>
                        <th class="px-4 py-3 text-right">Aprobado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($autorizaciones as $aut)
                    @php
                    $estadoClasses = [
                        'Aprobada'            => 'bg-green-100 text-green-700',
                        'Rechazada'           => 'bg-red-100 text-red-700',
                        'Pendiente'           => 'bg-amber-100 text-amber-700',
                        'Auditoría'           => 'bg-purple-100 text-purple-700',
                        'Pendiente Documento' => 'bg-slate-100 text-slate-700',
                        'Anulada'             => 'bg-gray-100 text-gray-500',
                    ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-[#0b57d0] font-semibold">
                            <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="hover:underline">{{ $aut->numero_autorizacion }}</a>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $aut->fecha_solicitud->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 font-medium">{{ $aut->pss->nombre ?? 'N/D' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 truncate max-w-xs">{{ optional($aut->servicio)->descripcion ?? optional($aut->servicioPdss)->coverage_description ?? $aut->procedimiento ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 capitalize">{{ $aut->canal_recepcion ?? 'N/D' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $estadoClasses[$aut->estado] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $aut->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-right text-gray-700">RD$ {{ number_format($aut->monto_solicitado, 2) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-semibold {{ $aut->estado === 'Aprobada' ? 'text-green-700' : 'text-gray-400' }}">
                            RD$ {{ number_format($aut->monto_contratado, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center text-gray-400">No hay registros para los filtros seleccionados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
