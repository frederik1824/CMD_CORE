@extends('layouts.core')

@section('title', 'Catálogo de Cuentas Contables')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    🏛️ Estructura Contable
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Catálogo de Cuentas Decimal</h2>
            <p class="text-xs text-slate-500 font-medium">Catálogo unificado oficial de la SISALRIL para ARS, ARL y SNS.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.contabilidad.dashboard') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Catálogo de Cuentas en Lista Jerárquica -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lista General de Cuentas</span>
            <span class="text-[10px] text-slate-400 font-medium">Las cuentas de tipo <b>Posteable</b> permiten asentar registros directos.</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Código Decimal</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nombre de la Cuenta</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Naturaleza</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Posteable</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sistema</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($cuentas as $cta)
                        @php
                            $padding = $cta->level === 1 ? 'pl-6 font-bold text-[#041e49]' : ($cta->level === 2 ? 'pl-12 text-slate-700 font-semibold' : 'pl-16 text-slate-500 font-medium');
                            $natureBadge = $cta->nature === 'debito' 
                                ? 'bg-blue-50 text-blue-700 border-blue-100' 
                                : 'bg-amber-50 text-amber-700 border-amber-100';
                            
                            $classColor = match($cta->account_class) {
                                1 => 'text-emerald-700 bg-emerald-50', // Activos
                                2 => 'text-rose-700 bg-rose-50', // Pasivos
                                3 => 'text-purple-700 bg-purple-50', // Capital
                                4 => 'text-blue-700 bg-blue-50', // Ingresos
                                5 => 'text-amber-700 bg-amber-50', // Gastos
                                default => 'text-slate-600 bg-slate-50'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono text-slate-600 whitespace-nowrap font-bold">
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $classColor }} font-bold">
                                    {{ $cta->code }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 {{ $padding }}">
                                {{ $cta->name }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $natureBadge }}">
                                    {{ ucfirst($cta->nature) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 font-mono">
                                Nivel {{ $cta->level }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($cta->is_postable)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        ✔ Posteable
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold bg-slate-50 text-slate-400 border border-slate-100">
                                        ➖ Control
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($cta->is_system)
                                    <span class="text-[10px] font-bold text-blue-600">● Sistema</span>
                                @else
                                    <span class="text-[10px] text-slate-300">Usuario</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
