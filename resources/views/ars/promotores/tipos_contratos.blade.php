@extends('layouts.ars')
@section('title', 'Contratos de Promotores')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Contratos de Promotores</h2>
            <p class="text-xs text-slate-500 font-medium">Configuración de comisiones y contratos con promotores.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Contratos Comerciales</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Promotor</th>
                            <th class="px-4 py-3 text-left">No. Contrato</th>
                            <th class="px-4 py-3 text-center">Vigencia</th>
                            <th class="px-4 py-3 text-right">Comisión (%)</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($contratos as $c)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $c->promoter->name }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $c->contract_number }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $c->start_date }} al {{ $c->end_date }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold">{{ number_format($c->commission_percent, 2) }}%</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $c->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Configurar Contrato</h3>
            <form action="{{ route('ars.promotores.guardar_contrato') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Promotor</label>
                    <select name="promoter_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($promotores as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->name }} ({{ $pr->promoter_type === 'persona_fisica' ? 'Física' : 'Empresa' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código de Contrato</label>
                    <input type="text" name="contract_number" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 font-mono" placeholder="PROM-CONTR-123" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Inicio</label>
                        <input type="date" name="start_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Fin</label>
                        <input type="date" name="end_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Porcentaje Comisión (%)</label>
                    <input type="number" step="0.01" name="commission_percent" value="5.00" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Guardar Contrato</button>
            </form>
        </div>
    </div>
</div>
@endsection