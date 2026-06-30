@extends('layouts.ars')
@section('title', 'Cálculo de Comisiones')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Cálculo de Comisiones</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de comisiones devengadas y generación automática de CXP.</p>
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
            <h3 class="font-bold text-slate-800">Registro de Comisiones</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 font-mono text-[11px]">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Promotor</th>
                            <th class="px-4 py-3 text-left">Campaña</th>
                            <th class="px-4 py-3 text-left">Afiliado Captado</th>
                            <th class="px-4 py-3 text-right">Comisión (DOP)</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($comisiones as $com)
                            <tr>
                                <td class="px-4 py-3 font-sans text-slate-850 font-semibold">{{ $com->promoter->name }}</td>
                                <td class="px-4 py-3 font-sans text-slate-600">{{ $com->campaign->name }}</td>
                                <td class="px-4 py-3 font-sans text-blue-900 font-bold">{{ $com->affiliate->nombres }} {{ $com->affiliate->primer_apellido }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">DOP {{ number_format($com->amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $com->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Cálculo Mensual / Periodo</h3>
            <form action="{{ route('ars.promotores.calcular_comisiones') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Promotor</label>
                    <select name="promoter_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($promotores as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Campaña</label>
                    <select name="campaign_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        @foreach($campanas as $camp)
                            <option value="{{ $camp->id }}">{{ $camp->name }} (DOP {{ number_format($camp->commission_amount, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período (YYYYMM)</label>
                    <input type="text" name="payout_period" value="202606" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 font-mono" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-full py-2.5 font-bold transition">Calcular y Generar CXP</button>
            </form>
        </div>
    </div>
</div>
@endsection