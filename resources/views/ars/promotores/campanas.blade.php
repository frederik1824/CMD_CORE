@extends('layouts.ars')
@section('title', 'Campañas Comerciales')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Campañas de Promoción</h2>
            <p class="text-xs text-slate-500 font-medium">Configuración de campañas temporales de ventas.</p>
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
            <h3 class="font-bold text-slate-800">Campañas Vigentes</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Campaña</th>
                            <th class="px-4 py-3 text-center">Vigencia</th>
                            <th class="px-4 py-3 text-right">Comisión Fija por Afiliado</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($campanas as $camp)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-850 block">{{ $camp->name }}</span>
                                    <span class="text-[9px] text-slate-400">{{ $camp->description }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono">{{ $camp->start_date }} al {{ $camp->end_date }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">DOP {{ number_format($camp->commission_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $camp->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Configurar Campaña</h3>
            <form action="{{ route('ars.promotores.guardar_campana') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre Campaña</label>
                    <input type="text" name="name" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Campaña Navidad Oro" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Descripción</label>
                    <textarea name="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Válido para planes complementarios..."></textarea>
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
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Monto Comisión Fija (DOP)</label>
                    <input type="number" name="commission_amount" value="500" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Configurar Campaña</button>
            </form>
        </div>
    </div>
</div>
@endsection