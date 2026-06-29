@extends('layouts.ars')
@section('title', 'Dashboard — Autorizaciones Médicas')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <span>ARS Core</span><span>/</span><span class="text-gray-600">Autorizaciones Médicas</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Autorizaciones Médicas ARS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Módulo interno para representantes ARS</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ars.autorizaciones.reporte') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reporte
            </a>
            <a href="{{ route('ars.autorizaciones.auditoria_medica') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-amber-200 bg-amber-50 text-sm text-amber-700 hover:bg-amber-100 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Auditoría Médica
                @if($kpis['auditoria'] > 0)
                    <span class="ml-2 bg-amber-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $kpis['auditoria'] }}</span>
                @endif
            </a>
            <a href="{{ route('ars.autorizaciones.nueva') }}" class="inline-flex items-center px-5 py-2 rounded-xl bg-[#0b57d0] text-white text-sm font-medium hover:bg-[#0842a0] transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Autorización
            </a>
        </div>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- KPI Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $kpiList = [
            ['label'=>'Autorizaciones Hoy', 'key'=>'total_dia',      'color'=>'bg-blue-50 text-blue-700',    'border'=>'border-blue-100'],
            ['label'=>'Pendientes',          'key'=>'pendientes',     'color'=>'bg-amber-50 text-amber-700',  'border'=>'border-amber-100'],
            ['label'=>'Aprobadas Hoy',       'key'=>'aprobadas',      'color'=>'bg-green-50 text-green-700',  'border'=>'border-green-100'],
            ['label'=>'Rechazadas Hoy',      'key'=>'rechazadas',     'color'=>'bg-red-50 text-red-700',      'border'=>'border-red-100'],
            ['label'=>'En Auditoría',        'key'=>'auditoria',      'color'=>'bg-purple-50 text-purple-700','border'=>'border-purple-100'],
            ['label'=>'Pend. Documento',     'key'=>'pend_documento', 'color'=>'bg-slate-50 text-slate-700',  'border'=>'border-slate-200'],
        ];
        @endphp
        @foreach($kpiList as $k)
        <div class="bg-white rounded-2xl border {{ $k['border'] }} p-4 flex flex-col gap-1 hover:shadow-md transition">
            <span class="text-xs font-medium text-gray-500">{{ $k['label'] }}</span>
            <span class="text-3xl font-bold {{ explode(' ',$k['color'])[1] }}">{{ $kpis[$k['key']] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Tabla recientes --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Solicitudes Recientes</h2>
            <a href="{{ route('ars.autorizaciones.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 transition uppercase tracking-wider">
                Ver todas
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">N° Autorización</th>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left">PSS</th>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Prioridad</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recientes as $aut)
                    @php
                    $estadoClasses = [
                        'Aprobada'            => 'bg-green-100 text-green-700',
                        'Rechazada'           => 'bg-red-100 text-red-700',
                        'Pendiente'           => 'bg-amber-100 text-amber-700',
                        'Auditoría'           => 'bg-purple-100 text-purple-700',
                        'Pendiente Documento' => 'bg-slate-100 text-slate-700',
                        'Anulada'             => 'bg-gray-100 text-gray-500',
                    ];
                    $prioClasses = [
                        'Alta'       => 'text-red-600 font-semibold',
                        'Emergencia' => 'text-red-700 font-bold',
                        'Media'      => 'text-amber-600',
                        'Baja'       => 'text-green-600',
                    ];
                    $afiliado = $aut->afiliado;
                    $nombreAfiliado = $afiliado
                        ? (isset($afiliado->primer_apellido)
                            ? $afiliado->nombres.' '.$afiliado->primer_apellido
                            : $afiliado->nombres.' '.$afiliado->apellidos)
                        : 'N/D';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-[#0b57d0]">{{ $aut->numero_autorizacion }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $nombreAfiliado }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $aut->pss->nombre ?? 'N/D' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ optional($aut->servicio)->descripcion ?? optional($aut->servicioPdss)->coverage_description ?? $aut->procedimiento ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoClasses[$aut->estado] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $aut->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs {{ $prioClasses[$aut->prioridad] ?? '' }}">{{ $aut->prioridad }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $aut->fecha_solicitud->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="text-blue-600 hover:text-blue-800 transition p-1.5 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">No hay autorizaciones registradas aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
        $links = [
            ['label'=>'Bandeja General',    'route'=>'ars.autorizaciones.index',           'color'=>'text-blue-600 bg-blue-50 border-blue-100'],
            ['label'=>'Pendientes',         'route'=>'ars.autorizaciones.pendientes',       'color'=>'text-amber-600 bg-amber-50 border-amber-100'],
            ['label'=>'Auditoría Médica',   'route'=>'ars.autorizaciones.auditoria_medica', 'color'=>'text-purple-600 bg-purple-50 border-purple-100'],
            ['label'=>'Reglas Automáticas', 'route'=>'ars.autorizaciones.reglas',           'color'=>'text-slate-600 bg-slate-50 border-slate-200'],
        ];
        @endphp
        @foreach($links as $lnk)
        <a href="{{ route($lnk['route']) }}"
           class="flex items-center justify-center p-4 rounded-2xl border {{ $lnk['color'] }} hover:shadow transition text-sm font-medium text-center">
            {{ $lnk['label'] }}
        </a>
        @endforeach
    </div>

</div>
@endsection
