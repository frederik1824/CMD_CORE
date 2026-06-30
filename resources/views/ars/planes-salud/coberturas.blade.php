@extends('layouts.ars')
@section('title', 'Coberturas de Planes')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Coberturas de Planes</h2>
            <p class="text-xs text-slate-500 font-medium">Asignación y límites de servicios por Plan.</p>
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
            <h3 class="font-bold text-slate-800">Coberturas Asignadas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Servicio PDSS</th>
                            <th class="px-4 py-3 text-right">Cobertura / Copago</th>
                            <th class="px-4 py-3 text-right">Límite</th>
                            <th class="px-4 py-3 text-center">Auth. Requerida</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($coberturas as $cob)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $cob->plan->name }}</td>
                                <td class="px-4 py-3 font-semibold text-blue-900">{{ $cob->service->name ?? 'Servicio Mapeado' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">{{ number_format($cob->coverage_percent, 0) }}% / {{ number_format($cob->copay_percent, 0) }}%</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-700">DOP {{ number_format($cob->limit_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold {{ $cob->requires_authorization ? 'bg-amber-50 text-amber-700 border border-amber-250' : 'bg-slate-50 text-slate-500' }}">
                                        {{ $cob->requires_authorization ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="py-2">
                {{ $coberturas->links() }}
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Agregar Cobertura</h3>
            <form action="{{ route('ars.planes_salud.guardar_cobertura') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Plan de Salud</label>
                    <select name="health_plan_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        @foreach($planes as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Servicio del Catálogo</label>
                    <select name="pdss_service_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($servicios as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Cobertura (%)</label>
                        <input type="number" step="0.1" name="coverage_percent" value="80" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Copago (%)</label>
                        <input type="number" step="0.1" name="copay_percent" value="20" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Copago Fijo (DOP)</label>
                        <input type="number" name="fixed_copay" value="0" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Límite DOP</label>
                        <input type="number" name="limit_amount" value="50000" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período Límite</label>
                        <select name="limit_period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                            <option value="anual">Anual</option>
                            <option value="evento">Por Evento</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Días de Espera</label>
                        <input type="number" name="waiting_period_days" value="0" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">¿Requiere Autorización?</label>
                    <select name="requires_authorization" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-full py-2.5 font-bold transition">Asignar Cobertura</button>
            </form>
        </div>
    </div>
</div>
@endsection