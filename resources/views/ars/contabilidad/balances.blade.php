@extends('layouts.core')

@section('title', 'Estados Financieros de la ARS')

@section('content')
<div class="space-y-6" x-data="{ tab: 'situacion' }">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    📊 Información Financiera
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Estados Financieros y Solvencia</h2>
            <p class="text-xs text-slate-500 font-medium">Visualización del Balance General (Situación) y Estado de Resultados (Beneficios).</p>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('ars.contabilidad.balances') }}" method="GET" class="flex items-center space-x-2">
                <select name="period" onchange="this.form.submit()" class="rounded-full border-slate-200 text-xs text-slate-600 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2">
                    @foreach($periodos as $per)
                        <option value="{{ $per->period_code }}" {{ $period == $per->period_code ? 'selected' : '' }}>
                            Período: {{ $per->period_code }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('ars.contabilidad.dashboard') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Pestañas -->
    <div class="flex border-b border-slate-100 space-x-4">
        <button @click="tab = 'situacion'" :class="tab === 'situacion' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500'" class="pb-3 text-xs font-bold border-b-2 transition-all focus:outline-none">
            Balance de Situación (General)
        </button>
        <button @click="tab = 'resultados'" :class="tab === 'resultados' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500'" class="pb-3 text-xs font-bold border-b-2 transition-all focus:outline-none">
            Estado de Resultados (Beneficios)
        </button>
        <button @click="tab = 'comprobacion'" :class="tab === 'comprobacion' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500'" class="pb-3 text-xs font-bold border-b-2 transition-all focus:outline-none">
            Balance de Comprobación
        </button>
    </div>

    <!-- Contenido Pestaña 1: Situación Financiera -->
    <div x-show="tab === 'situacion'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>
        <!-- Activos (Izquierda) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                <span class="material-symbols-outlined text-lg text-emerald-600">trending_up</span>
                <span>1 - Activos</span>
            </h3>

            <div class="divide-y divide-slate-50">
                @forelse($activos as $act)
                    <div class="flex justify-between items-center py-3 text-xs">
                        <div>
                            <p class="font-bold text-slate-700">{{ $act['name'] }}</p>
                            <p class="text-[9px] text-slate-400 font-mono">Cuenta {{ $act['code'] }}</p>
                        </div>
                        <span class="font-mono font-bold text-slate-800 text-sm">DOP {{ number_format($act['saldo'], 2) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 italic">No se registran activos en el período.</p>
                @endforelse
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-slate-800">
                <span class="text-xs uppercase tracking-wider">Total General Activos</span>
                <span class="font-mono text-base text-emerald-700">DOP {{ number_format(collect($activos)->sum('saldo'), 2) }}</span>
            </div>
        </div>

        <!-- Pasivos y Capital (Derecha) -->
        <div class="space-y-6">
            <!-- Pasivos -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-rose-600">trending_down</span>
                    <span>2 - Pasivos (Reservas & Proveedores)</span>
                </h3>

                <div class="divide-y divide-slate-50">
                    @forelse($pasivos as $pas)
                        <div class="flex justify-between items-center py-3 text-xs">
                            <div>
                                <p class="font-bold text-slate-700">{{ $pas['name'] }}</p>
                                <p class="text-[9px] text-slate-400 font-mono">Cuenta {{ $pas['code'] }}</p>
                            </div>
                            <span class="font-mono font-bold text-slate-800 text-sm">DOP {{ number_format($pas['saldo'], 2) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 italic">No se registran pasivos en el período.</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-slate-800">
                    <span class="text-xs uppercase tracking-wider">Total General Pasivos</span>
                    <span class="font-mono text-base text-rose-700">DOP {{ number_format(collect($pasivos)->sum('saldo'), 2) }}</span>
                </div>
            </div>

            <!-- Capital -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-purple-700">account_balance_wallet</span>
                    <span>3 - Capital y Reservas de Capital</span>
                </h3>

                <div class="divide-y divide-slate-50">
                    @forelse($capital as $cap)
                        <div class="flex justify-between items-center py-3 text-xs">
                            <div>
                                <p class="font-bold text-slate-700">{{ $cap['name'] }}</p>
                                <p class="text-[9px] text-slate-400 font-mono">Cuenta {{ $cap['code'] }}</p>
                            </div>
                            <span class="font-mono font-bold text-slate-800 text-sm">DOP {{ number_format($cap['saldo'], 2) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 italic">No se registran cuentas de capital.</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-slate-800">
                    <span class="text-xs uppercase tracking-wider">Total Pasivos + Capital</span>
                    @php
                        $totPasCap = collect($pasivos)->sum('saldo') + collect($capital)->sum('saldo');
                    @endphp
                    <span class="font-mono text-base text-purple-700">DOP {{ number_format($totPasCap, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Pestaña 2: Estado de Resultados -->
    <div x-show="tab === 'resultados'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>
        <!-- Ingresos -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                <span class="material-symbols-outlined text-lg text-blue-700">arrow_circle_up</span>
                <span>4 - Ingresos Operacionales</span>
            </h3>

            <div class="divide-y divide-slate-50">
                @forelse($ingresos as $ing)
                    <div class="flex justify-between items-center py-3 text-xs">
                        <div>
                            <p class="font-bold text-slate-700">{{ $ing['name'] }}</p>
                            <p class="text-[9px] text-slate-400 font-mono">Cuenta {{ $ing['code'] }}</p>
                        </div>
                        <span class="font-mono font-bold text-slate-800 text-sm">DOP {{ number_format($ing['saldo'], 2) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 italic">No se registran ingresos en este período.</p>
                @endforelse
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-slate-800">
                <span class="text-xs uppercase tracking-wider">Total Ingresos</span>
                <span class="font-mono text-base text-blue-700">DOP {{ number_format($totalIngresos, 2) }}</span>
            </div>
        </div>

        <!-- Gastos & Utilidad -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3 flex items-center space-x-2">
                    <span class="material-symbols-outlined text-lg text-rose-600">arrow_circle_down</span>
                    <span>5 - Gastos Operacionales & Financieros</span>
                </h3>

                <div class="divide-y divide-slate-50">
                    @forelse($gastos as $gas)
                        <div class="flex justify-between items-center py-3 text-xs">
                            <div>
                                <p class="font-bold text-slate-700">{{ $gas['name'] }}</p>
                                <p class="text-[9px] text-slate-400 font-mono">Cuenta {{ $gas['code'] }}</p>
                            </div>
                            <span class="font-mono font-bold text-slate-800 text-sm">DOP {{ number_format($gas['saldo'], 2) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 italic">No se registran gastos operacionales.</p>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-slate-800">
                    <span class="text-xs uppercase tracking-wider">Total Gastos</span>
                    <span class="font-mono text-base text-rose-700">DOP {{ number_format($totalGastos, 2) }}</span>
                </div>
            </div>

            <!-- Utilidad Neta del Ejercicio -->
            <div class="p-6 rounded-3xl border shadow-sm {{ $utilidadPeriodo >= 0 ? 'bg-emerald-50 border-emerald-250 text-emerald-950' : 'bg-rose-50 border-rose-250 text-rose-950' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider">Resultado Neto del Ejercicio</h4>
                        <p class="text-[10px] opacity-70 mt-0.5">Utilidades o pérdidas netas acumuladas del período devengado.</p>
                    </div>
                    <span class="font-mono text-lg font-black">
                        DOP {{ number_format($utilidadPeriodo, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Pestaña 3: Balance de Comprobación -->
    <div x-show="tab === 'comprobacion'" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Cuenta</th>
                        <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Movimiento Débito</th>
                        <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Movimiento Crédito</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @php
                        $totDeb = 0;
                        $totCred = 0;
                    @endphp
                    @forelse($comprobacion as $comp)
                        @if($comp->debito > 0 || $comp->credito > 0)
                            @php
                                $totDeb += $comp->debito;
                                $totCred += $comp->credito;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3 font-mono text-slate-600 whitespace-nowrap font-bold">
                                    {{ $comp->code }}
                                </td>
                                <td class="px-6 py-3 text-slate-700 font-semibold">
                                    {{ $comp->name }}
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-slate-800">
                                    {{ $comp->debito > 0 ? ('DOP ' . number_format($comp->debito, 2)) : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-slate-800">
                                    {{ $comp->credito > 0 ? ('DOP ' . number_format($comp->credito, 2)) : '—' }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-slate-400 font-medium">
                                Sin transacciones registradas en el período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 font-mono font-black border-t border-slate-150">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-right text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            Total Comprobado
                        </td>
                        <td class="px-6 py-4 text-right text-slate-900 text-sm">
                            DOP {{ number_format($totDeb, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-slate-900 text-sm">
                            DOP {{ number_format($totCred, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
