@extends('layouts.ars')
@section('title', 'Bandeja Auditoría Médica')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('ars.autorizaciones.dashboard') }}" class="hover:text-blue-600">Autorizaciones</a>
                <span>/</span><span class="text-gray-600">Auditoría Médica</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Bandeja de Auditoría Médica</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-normal">Revisión clínica de solicitudes pendientes de aprobación por auditor médico</p>
        </div>
    </div>

    {{-- KPIs Auditoría --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $kpis = [
            ['label' => 'Total Pendiente', 'val' => $kpisAuditoria['total'], 'color' => 'text-purple-700 bg-purple-50', 'border' => 'border-purple-100'],
            ['label' => 'Urgentes/Emergencia', 'val' => $kpisAuditoria['urgentes'], 'color' => 'text-red-700 bg-red-50', 'border' => 'border-red-100'],
            ['label' => 'Alto Costo', 'val' => $kpisAuditoria['alto_costo'], 'color' => 'text-amber-700 bg-amber-50', 'border' => 'border-amber-100'],
            ['label' => 'Recibidas Hoy', 'val' => $kpisAuditoria['hoy'], 'color' => 'text-blue-700 bg-blue-50', 'border' => 'border-blue-100']
        ];
        @endphp
        @foreach($kpis as $kp)
        <div class="bg-white rounded-2xl border {{ $kp['border'] }} p-4 flex flex-col gap-1 hover:shadow-sm transition">
            <span class="text-xs font-medium text-gray-500">{{ $kp['label'] }}</span>
            <span class="text-2xl font-bold {{ explode(' ', $kp['color'])[0] }}">{{ $kp['val'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Filtro y Búsqueda --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('ars.autorizaciones.auditoria_medica') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Nº de autorización..." 
                       class="w-full pl-3 pr-10 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0b57d0] focus:bg-white transition"
                >
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
            @if($search)
                <a href="{{ route('ars.autorizaciones.auditoria_medica') }}" class="text-xs text-gray-400 hover:text-gray-600 hover:underline">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- Tabla de Auditoría --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">N° Autorización</th>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left">PSS</th>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-left">Tipo Servicio</th>
                        <th class="px-4 py-3 text-left">Prioridad</th>
                        <th class="px-4 py-3 text-left">Fecha Solicitud</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($autorizaciones as $aut)
                    @php
                    $prioClasses = [
                        'Alta'       => 'text-red-600 bg-red-50 px-2 py-0.5 rounded-lg font-semibold text-[10px]',
                        'Emergencia' => 'text-red-700 bg-red-100 px-2 py-0.5 rounded-lg font-bold text-[10px]',
                        'Media'      => 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg text-[10px]',
                        'Baja'       => 'text-green-600 bg-green-50 px-2 py-0.5 rounded-lg text-[10px]',
                    ];
                    $afiliado = $aut->afiliado;
                    $nombreAfiliado = $afiliado
                        ? (isset($afiliado->primer_apellido)
                            ? $afiliado->nombres.' '.$afiliado->primer_apellido
                            : $afiliado->nombres.' '.$afiliado->apellidos)
                        : 'N/D';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-[#0b57d0]">
                            {{ $aut->numero_autorizacion }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-700">{{ $nombreAfiliado }}</div>
                            <div class="text-[10px] text-gray-400">{{ $aut->tipo_afiliado_display }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $aut->pss->nombre ?? 'N/D' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            <div class="font-medium text-gray-700">{{ optional($aut->servicio)->codigo ?? 'N/D' }}</div>
                            <div class="text-gray-400 truncate max-w-xs">{{ optional($aut->servicio)->descripcion ?? optional($aut->servicioPdss)->coverage_description ?? $aut->procedimiento ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 capitalize">
                            {{ str_replace('_', ' ', $aut->tipo_servicio) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $prioClasses[$aut->prioridad] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $aut->prioridad }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $aut->fecha_solicitud->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 font-semibold text-xs">
                            RD$ {{ number_format($aut->monto_solicitado, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">
                            <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="inline-flex items-center px-3 py-1 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg text-xs font-semibold transition">
                                <span class="material-symbols-outlined text-sm mr-1">stethoscope</span>
                                Auditar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center text-gray-400">No hay autorizaciones pendientes de auditoría médica.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($autorizaciones->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $autorizaciones->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
