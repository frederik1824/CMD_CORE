@extends('layouts.pss')

@section('title', 'Detalle de Reclamación')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center space-x-4">
            <div class="bg-gradient-to-tr from-teal-600 to-teal-400 text-white p-2 rounded-xl shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="leading-none">
                <h2 class="text-base font-bold text-slate-800">Reclamación: {{ $reclamacion->claim_number }}</h2>
                <p class="text-[11px] text-slate-400 font-medium">Asociada a autorización {{ $reclamacion->authorization->numero_autorizacion }}</p>
            </div>
        </div>
        <a href="{{ route('pss.reclamaciones.index') }}" class="text-slate-500 hover:text-slate-700 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition">
            Volver a la lista
        </a>
    </div>

    <!-- Grid de Datos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <!-- Detalle Factura -->
        <div class="md:col-span-2 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider border-b border-slate-50 pb-2">
                Resumen de Liquidación de Factura
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">No. Factura</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $reclamacion->invoice_number }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">NCF</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $reclamacion->ncf }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fecha Servicio</span>
                    <span class="font-semibold text-slate-700">{{ $reclamacion->service_date->format('d/m/Y') }}</span>
                </div>
            </div>

            <!-- Panel de Montos en Bento/Grid -->
            <div class="grid grid-cols-3 gap-4 font-mono font-bold text-slate-700 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-[8px] font-bold text-slate-400 uppercase block mb-1">Monto Sometido</span>
                    <span class="text-sm">DOP {{ number_format($reclamacion->claimed_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-[8px] font-bold text-slate-400 uppercase block mb-1 text-teal-600">Aprobado / Liquidado</span>
                    <span class="text-sm text-teal-600">DOP {{ number_format($reclamacion->approved_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-[8px] font-bold text-slate-400 uppercase block mb-1 text-rose-600">Objetado / Retenido</span>
                    <span class="text-sm text-rose-600">DOP {{ number_format($reclamacion->objected_amount, 2) }}</span>
                </div>
            </div>

            <!-- Observaciones del Auditor -->
            @if($reclamacion->audits->isNotEmpty())
                <div class="border-t border-slate-100 pt-4 space-y-2">
                    <p class="font-bold text-slate-850 text-xs">Historial de Objeciones y Observaciones ARS:</p>
                    @foreach($reclamacion->audits as $audit)
                        @if($audit->pss_observation || $audit->objection_reason)
                            <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100 space-y-1">
                                <p class="font-bold text-amber-900">Auditoría {{ $audit->audit_type }}</p>
                                @if($audit->objection_reason)
                                    <p class="font-semibold text-rose-900">Motivo: {{ $audit->objection_reason }}</p>
                                @endif
                                <p class="text-slate-650 mt-1">{{ $audit->pss_observation }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Columna Derecha: Estado de CXP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4 self-start">
            <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider border-b border-slate-50 pb-2">
                Estado del Pago
            </h3>

            <div class="space-y-3">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Estado Reclamación</span>
                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 font-bold text-[#0f766e] mt-1 border border-slate-200">
                        {{ $reclamacion->status }}
                    </span>
                </div>

                @if($reclamacion->payables->isNotEmpty())
                    @php
                        $ap = $reclamacion->payables->first();
                    @endphp
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Cuenta por Pagar (CXP)</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $ap->payable_number }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Monto Neto a Desembolsar</span>
                        <span class="font-extrabold text-slate-800 font-mono">DOP {{ number_format($ap->net_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Estado de Cuenta</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 font-bold mt-1 border border-emerald-200">
                            {{ $ap->status }}
                        </span>
                    </div>
                @else
                    <p class="text-slate-400 italic">En espera de liberación por Auditoría Médica para generar Cuenta por Pagar.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
