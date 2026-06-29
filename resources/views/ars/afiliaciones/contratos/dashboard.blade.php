@extends('layouts.ars')

@section('title', 'Control de Formularios y Contratos')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Control de Formularios y Contratos de Afiliación
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Módulo dinámico para la asignación y auditoría de rangos de formularios autorizados para Unipago.
            </p>
        </div>
        <div class="mt-4 flex gap-3 sm:mt-0 sm:ml-4">
            <a href="{{ route('ars.contract_control.config') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="settings">settings</span>
                Configuración
            </a>
            <a href="{{ route('ars.contract_control.ranges.create') }}" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="add">add</span>
                Nuevo Rango
            </a>
        </div>
    </div>

    <!-- Alertas críticas -->
    @if($rangosCriticos->count() > 0)
        <div class="bg-amber-55 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 shadow-xs">
            <span class="material-symbols-outlined text-amber-600 mt-0.5" data-icon="warning">warning</span>
            <div class="text-xs text-amber-800 leading-normal font-semibold">
                <span class="font-bold block">¡Rangos Próximos a Agotarse!</span>
                Los siguientes rangos tienen menos del 20% de números de contrato disponibles:
                <ul class="list-disc list-inside mt-1 font-mono">
                    @foreach($rangosCriticos as $rc)
                        <li>{{ $rc->range_code }} (Disponibles: {{ $rc->available_count }} / {{ $rc->total_numbers }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- KPIs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Rangos Activos</span>
            <span class="text-2xl font-bold text-slate-800 block mt-2">{{ $kpis['rangos_activos'] }} Rangos</span>
            <a href="{{ route('ars.contract_control.ranges.index') }}" class="text-[10px] text-brand-600 hover:text-brand-700 font-bold block mt-3">Ver todos &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Contratos Disponibles</span>
            <span class="text-2xl font-bold text-emerald-600 block mt-2">{{ number_format($kpis['disponibles']) }}</span>
            <span class="text-[10px] text-slate-400 block mt-3 font-semibold">Reservados: {{ $kpis['reservados'] }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Aceptados OK (Unipago)</span>
            <span class="text-2xl font-bold text-brand-600 block mt-2">{{ number_format($kpis['ok']) }}</span>
            <span class="text-[10px] text-slate-400 block mt-3 font-semibold">Enviados: {{ $kpis['enviados'] }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Rechazados / Pendientes</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-bold text-rose-600">{{ $kpis['re'] }} RE</span>
                <span class="text-sm font-semibold text-slate-400">/</span>
                <span class="text-lg font-bold text-amber-500">{{ $kpis['pe'] }} PE</span>
            </div>
            <span class="text-[10px] text-slate-400 block mt-3 font-semibold">Bloqueados: {{ $kpis['bloqueados'] }}</span>
        </div>
    </div>

    <!-- Integración Rápida / Estatus Asignación -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Próximo Contrato Disponible -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Próxima Asignación Automatizada</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-semibold">
                    El sistema tomará automáticamente el siguiente número de contrato disponible para las solicitudes individuales que no especifiquen un formulario.
                </p>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-150 flex items-center justify-between">
                    <div>
                        <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Próximo Número</span>
                        <strong class="text-lg font-mono text-slate-800 font-bold block mt-0.5">
                            {{ $proximoDisponible ? $proximoDisponible->contract_number : 'Ninguno (Rango Agotado)' }}
                        </strong>
                    </div>
                    @if($proximoDisponible)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-100 uppercase">Disponible</span>
                    @endif
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-4">
                <span class="text-slate-400">Rango de pertenencia:</span>
                <strong class="font-mono text-slate-700">{{ $proximoDisponible ? $proximoDisponible->range->range_code : 'N/A' }}</strong>
            </div>
        </div>

        <!-- Último Usado -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Último Contrato Transaccionado</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-semibold">
                    Registro de la transacción más reciente de asignación, envío o respuesta realizada ante Unipago.
                </p>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-150 flex items-center justify-between">
                    <div>
                        <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Número</span>
                        <strong class="text-lg font-mono text-slate-800 font-bold block mt-0.5">
                            {{ $ultimoUsado ? $ultimoUsado->contract_number : '—' }}
                        </strong>
                    </div>
                    @if($ultimoUsado)
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border uppercase {{ 
                            $ultimoUsado->status === 'ok' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : (
                            $ultimoUsado->status === 're' ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-purple-50 text-purple-700 border-purple-100')
                        }}">{{ $ultimoUsado->status }}</span>
                    @endif
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-4">
                <span class="text-slate-400">Última actualización:</span>
                <strong class="text-slate-700 font-mono">{{ $ultimoUsado ? $ultimoUsado->updated_at->format('d/m/Y H:i') : 'N/A' }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
