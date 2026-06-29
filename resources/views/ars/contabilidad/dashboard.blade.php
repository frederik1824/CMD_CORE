@extends('layouts.core')

@section('title', 'Dashboard Financiero y Contable')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    🏛️ Contabilidad General ARS
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Financiero</h2>
            <p class="text-xs text-slate-500 font-medium">Control de reservas técnicas, devengamiento de cápitas, estados financieros y solvencia de la ARS.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 rounded-full px-4.5 py-2">
                Período Contable Activo: <b>{{ $periodCode }}</b>
            </span>
            <a href="{{ route('ars.contabilidad.balances') }}" class="bg-[#0b57d0] text-white rounded-full px-5 py-2.5 text-xs font-bold hover:bg-[#083d91] transition shadow-sm">
                Ver Estados Financieros
            </a>
        </div>
    </div>

    <!-- KPIs Principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <!-- Efectivo en Bancos -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4 hover:shadow-md transition">
            <div class="p-3 bg-emerald-50 text-emerald-700 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">account_balance</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Efectivo en Bancos</span>
                <span class="text-lg font-black text-slate-800 mt-0.5">DOP {{ number_format($efectivoBancos, 2) }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">Cuenta 1102 (Bancos)</span>
            </div>
        </div>

        <!-- Ingresos por Cápita -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4 hover:shadow-md transition">
            <div class="p-3 bg-blue-50 text-blue-700 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cápitas Devengadas</span>
                <span class="text-lg font-black text-slate-800 mt-0.5">DOP {{ number_format($ingresosCapita, 2) }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">Régimen Contributivo</span>
            </div>
        </div>

        <!-- Cuentas por Cobrar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4 hover:shadow-md transition">
            <div class="p-3 bg-amber-50 text-amber-700 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cuentas por Cobrar</span>
                <span class="text-lg font-black text-slate-800 mt-0.5">DOP {{ number_format($cuentasPorCobrar, 2) }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">Cuentas 1103 / 1104</span>
            </div>
        </div>

        <!-- Cuentas por Pagar PSS -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4 hover:shadow-md transition">
            <div class="p-3 bg-rose-50 text-rose-700 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">request_quote</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cuentas por Pagar</span>
                <span class="text-lg font-black text-slate-800 mt-0.5">DOP {{ number_format($cuentasPorPagar, 2) }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">Cuenta 2105 (PSS / Proveedores)</span>
            </div>
        </div>
    </div>

    <!-- Bento Grid central -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Reservas Técnicas & Siniestralidad -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detalle Reservas Técnicas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                        <span class="material-symbols-outlined text-lg text-blue-700">shield</span>
                        <span>Reservas Técnicas Constituidas (Pasivos)</span>
                    </h3>
                    <span class="text-xs font-bold text-[#0b57d0] bg-blue-50 px-3 py-1 rounded-full border border-blue-100 font-mono">
                        DOP {{ number_format($totalReservas, 2) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aportes No Devengados</span>
                        <p class="text-sm font-black text-slate-700 font-mono">DOP {{ number_format($reservasNoDevengadas, 2) }}</p>
                        <span class="block text-[9px] text-slate-400">Cuenta 210101</span>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siniestros Liquidados</span>
                        <p class="text-sm font-black text-slate-700 font-mono">DOP {{ number_format($reservasPendientePago, 2) }}</p>
                        <span class="block text-[9px] text-slate-400">Cuenta 210102 (Pend. Pago)</span>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siniestros en Trámite</span>
                        <p class="text-sm font-black text-slate-700 font-mono">DOP {{ number_format($reservasPendienteLiquidacion, 2) }}</p>
                        <span class="block text-[9px] text-slate-400">Cuenta 210103 (Pend. Liquidación)</span>
                    </div>
                </div>
            </div>

            <!-- Gráficos e indicadores de Siniestralidad y Límites -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-rose-600">query_stats</span>
                    <span>Tasa de Siniestralidad y Margen Operativo</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-xs font-bold text-slate-600">
                            <span>Siniestralidad Neta del Mes</span>
                            <span class="font-mono text-rose-600">{{ number_format($siniestralidad, 2) }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="bg-rose-500 h-3 rounded-full transition-all" style="width: {{ min(100, $siniestralidad) }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-normal font-medium">Siniestralidad óptima requerida por la Superintendencia (SISALRIL): menor al 85.00%. Si excede este valor, la ARS debe fondear con reservas especiales.</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between text-xs font-bold text-slate-600">
                            <span>Margen Operativo de Salud</span>
                            <span class="font-mono text-emerald-600">{{ number_format(max(0, 100 - $siniestralidad), 2) }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="bg-emerald-500 h-3 rounded-full transition-all" style="width: {{ min(100, max(0, 100 - $siniestralidad)) }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-normal font-medium">Margen financiero disponible para cubrir gastos generales, administrativos, comisiones de promotores de salud y Unipago.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Indicadores de Solvencia & Cierre Contable -->
        <div class="space-y-6">
            <!-- Solvencia y Patrimonio Técnico -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-blue-700">balance</span>
                    <span>Margen de Solvencia SISALRIL</span>
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <p class="font-bold text-slate-700">Patrimonio Técnico Real</p>
                            <p class="text-[9px] text-slate-400">Cuenta Clase 3</p>
                        </div>
                        <span class="font-mono font-bold text-slate-800">DOP {{ number_format($patrimonioTecnico, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <p class="font-bold text-slate-700">Margen Mínimo Requerido</p>
                            <p class="text-[9px] text-slate-400">Límite de solvencia legal</p>
                        </div>
                        <span class="font-mono font-bold text-rose-600">DOP {{ number_format($margenSolvencia, 2) }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-500">Estado de Solvencia</span>
                        @if($patrimonioTecnico >= $margenSolvencia)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                ✔ CUMPLE SOLVENCIA
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                ✖ BAJA SOLVENCIA (ALERTA)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones Contables Rápidas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-slate-700">settings</span>
                    <span>Acciones Financieras Rápidas</span>
                </h3>

                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('ars.contabilidad.catalogo') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition text-xs font-semibold text-slate-700">
                        <span>Consultar Catálogo de Cuentas</span>
                        <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                    </a>
                    <a href="{{ route('ars.contabilidad.asientos') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition text-xs font-semibold text-slate-700">
                        <span>Ver Libro Diario y Asientos</span>
                        <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                    </a>
                    <a href="{{ route('ars.contabilidad.mayor') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition text-xs font-semibold text-slate-700">
                        <span>Consultar Mayor Auxiliar</span>
                        <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                    </a>
                    <a href="{{ route('ars.contabilidad.cierre') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition text-xs font-semibold text-slate-700">
                        <span>Cierre de Período Mensual</span>
                        <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
