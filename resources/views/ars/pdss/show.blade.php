@extends('layouts.ars')
@section('title', 'Prestación ' . $servicio->simon_code . ' — Catálogo PDSS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">

    {{-- Breadcrumbs & Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <span>ARS Core</span><span>/</span>
                <a href="{{ route('ars.pdss.catalogo') }}" class="hover:underline">Catálogo PDSS</a><span>/</span>
                <span class="text-gray-600">SIMON {{ $servicio->simon_code }}</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Ficha Técnica de Prestación</h1>
            <p class="text-sm text-gray-500 mt-0.5">Detalle del Catálogo PDSS oficial de la CNSS</p>
        </div>
        <div>
            <a href="{{ route('ars.pdss.catalogo') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-250 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 transition shadow-sm">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="arrow_back">arrow_back</span>
                Regresar al Catálogo
            </a>
        </div>
    </div>

    {{-- Service Information Bento --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Technical Card (Col Span 2) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-6">
            <div class="flex items-start justify-between border-b border-gray-50 pb-4">
                <div class="space-y-1">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-150">
                        {{ $servicio->coverage_type }}
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 pt-1 leading-snug">{{ $servicio->coverage_description }}</h2>
                    <p class="text-[10px] text-gray-400 font-mono">Página fuente del catálogo PDF: {{ $servicio->source_page ?? '1' }}</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-bold text-gray-400 block uppercase tracking-wider">Código SIMON</span>
                    <span class="text-2xl font-mono font-extrabold text-[#0b57d0] block">{{ $servicio->simon_code }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-600">
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Código CUPS:</span>
                        <span class="font-mono text-slate-800">{{ $servicio->cups_code ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Grupo PDSS:</span>
                        <span class="text-slate-850 font-medium text-right">{{ $servicio->group->code }} - {{ $servicio->group->name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Subgrupo:</span>
                        <span class="text-slate-850 font-medium text-right">{{ $servicio->subgroup->code }} - {{ $servicio->subgroup->name }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Cobertura Plan:</span>
                        <span class="text-slate-800 font-mono font-bold">{{ $servicio->amount_coverage ?: 'Ilimitada' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Copago Moderador:</span>
                        <span class="text-slate-800 font-mono font-bold">{{ $servicio->copay_type ?: 'No' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="font-semibold text-slate-450">Nivel de Atención:</span>
                        <div class="flex gap-1">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $servicio->level_1_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300' }}">Nivel 1</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $servicio->level_2_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300' }}">Nivel 2</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $servicio->level_3_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300' }}">Nivel 3</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-50">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-2">Propiedades operativas:</span>
                
                @if($servicio->requires_authorization)
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold">Requiere Autorización</span>
                @else
                    <span class="px-3 py-1 bg-green-50 text-green-750 border border-green-100 rounded-full text-[10px] font-bold">Sin Autorización previa</span>
                @endif

                @if($servicio->requires_medical_audit)
                    <span class="px-3 py-1 bg-purple-50 text-purple-750 border border-purple-100 rounded-full text-[10px] font-bold">Requiere Auditoría Médica</span>
                @endif

                @if($servicio->is_high_cost)
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-[10px] font-bold">Alto Costo</span>
                @endif

                @if($servicio->is_emergency)
                    <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold">Urgencia</span>
                @endif
            </div>
        </div>

        {{-- Stats and quick actions (Col Span 1) --}}
        <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm flex flex-col justify-between min-h-[220px]">
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Estadísticas de Uso</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Aprobadas</span>
                        <span class="text-xl font-bold text-emerald-600 block">{{ $stats['aprobadas'] }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Rechazadas</span>
                        <span class="text-xl font-bold text-rose-600 block">{{ $stats['rechazadas'] }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between text-xs text-slate-400">
                <span>Total Solicitudes:</span>
                <span class="font-bold text-slate-800">{{ $stats['total'] }}</span>
            </div>
        </div>
    </div>

    {{-- Clinics / PSS contracts list & History --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- PSS Contracted --}}
        <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Red de Prestadores Contratados</h3>
            
            <div class="divide-y divide-slate-50 overflow-y-auto max-h-[300px] pr-2">
                @forelse($contratos as $c)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $c->pss->nombre }}</span>
                            <span class="text-[10px] text-gray-400 block font-mono">RNC: {{ $c->pss->rnc }} | Nivel: {{ $c->pss->nivel_atencion }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-slate-800 block">RD$ {{ number_format($c->contracted_amount, 2) }}</span>
                            <span class="text-[9px] font-bold text-emerald-600">Contrato Activo</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-3xl mb-2" data-icon="domain_disabled">domain_disabled</span>
                        <p class="text-xs">No hay clínicas ni prestadoras con este servicio contratado en su nivel de atención.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Authorizations History --}}
        <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Solicitudes Recientes en la ARS</h3>
            
            <div class="divide-y divide-slate-50 overflow-y-auto max-h-[300px] pr-2">
                @forelse($autorizaciones as $aut)
                    @php
                    $estadoClasses = [
                        'Aprobada'            => 'bg-green-50 text-green-700 border-green-100',
                        'Rechazada'           => 'bg-red-50 text-red-700 border-red-100',
                        'Pendiente'           => 'bg-amber-50 text-amber-700 border-amber-100',
                        'Auditoría'           => 'bg-purple-50 text-purple-750 border-purple-100',
                        'Pendiente Documento' => 'bg-slate-50 text-slate-700 border-slate-200',
                    ];
                    @endphp
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" class="font-mono font-bold text-[#0b57d0] hover:underline block">{{ $aut->numero_autorizacion }}</a>
                            <span class="text-[10px] text-gray-400 block">Prestador: {{ $aut->pss->nombre ?? 'N/A' }} | Solicitado: RD$ {{ number_format($aut->monto_solicitado, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $estadoClasses[$aut->estado] ?? 'bg-gray-50 text-gray-400' }}">
                                {{ $aut->estado }}
                            </span>
                            <span class="text-[9px] text-gray-400 block mt-1">{{ $aut->fecha_solicitud->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-3xl mb-2" data-icon="history">history</span>
                        <p class="text-xs">No se registran solicitudes históricas para esta prestación.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
