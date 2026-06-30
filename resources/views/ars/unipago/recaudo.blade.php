@extends('layouts.ars')
@section('title', 'Procesamiento de Recaudo Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Procesamiento de Recaudo Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Carga de archivos de aportes y recaudación nacional de cápitas del SUIR.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Cortes de Dispersión Recaudados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código Corte</th>
                            <th class="px-4 py-3 text-center font-mono">Periodo</th>
                            <th class="px-4 py-3 text-right">Afiliados</th>
                            <th class="px-4 py-3 text-right">Monto Total</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($dispersiones as $disp)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $disp->cut_number }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $disp->period }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ $disp->total_affiliates }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">DOP {{ number_format($disp->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $disp->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Procesar Archivo Recaudo</h3>
            <form action="{{ route('ars.unipago.procesar_recaudo') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Monto del Depósito de Recaudo (DOP)</label>
                    <input type="number" step="0.01" name="amount" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. 250000.00" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Archivo Planillas Recaudadas</label>
                    <div class="border border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50/50 text-slate-400">
                        <span class="material-symbols-outlined text-3xl">cloud_upload</span>
                        <p class="text-[9px] font-semibold mt-1">Arrastre o seleccione el CSV de planilla Unipago</p>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Conciliar Recaudo Unipago</button>
            </form>
        </div>
    </div>
</div>
@endsection