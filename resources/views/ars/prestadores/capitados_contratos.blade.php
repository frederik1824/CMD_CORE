@extends('layouts.ars')

@section('title', 'Contratos Capitados')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Contratos Capitados PSS</h2>
            <p class="text-xs text-slate-500 font-medium">Administración de contratos de cápita fija mensual por población afiliada asignada.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Contrato Capitado -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Contrato de Cápita</h3>
            <form action="{{ route('ars.prestadores.guardar_capitado_contrato') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Prestadora (PSS) <span class="text-rose-500">*</span></label>
                    <select name="pss_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($pss as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} (RNC: {{ $p->rnc }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Número de Contrato <span class="text-rose-500">*</span></label>
                    <input type="text" name="contract_number" placeholder="Ej. CAP-HOSP-2026" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Población (Afiliados) <span class="text-rose-500">*</span></label>
                        <input type="number" name="coverage_population_count" placeholder="Ej. 1500" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tasa Mensual (DOP) <span class="text-rose-500">*</span></label>
                        <input type="number" name="monthly_capitation_rate" placeholder="Ej. 450" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Inicio <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Fin <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Guardar Contrato Capitado</button>
            </form>
        </div>

        <!-- Listado de Contratos Capitados -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Contratos Capitados Activos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Prestador (PSS)</th>
                            <th class="px-4 py-3 text-left">Número Contrato</th>
                            <th class="px-4 py-3 text-right">Población / Tasa</th>
                            <th class="px-4 py-3 text-right font-mono">Monto Mensual</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($contratos as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $c->pss?->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $c->contract_number }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="block font-mono text-[10px] text-slate-450">{{ $c->coverage_population_count }} Afiliados</span>
                                    <span class="block">DOP {{ number_format($c->monthly_capitation_rate, 2) }} / cápita</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">DOP {{ number_format($c->total_monthly_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $c->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado contratos capitados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
