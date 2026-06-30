@extends('layouts.ars')
@section('title', 'Topes y Límites')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Topes y Límites</h2>
            <p class="text-xs text-slate-500 font-medium">Establecimiento de límites financieros anuales o familiares.</p>
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
            <h3 class="font-bold text-slate-800">Límites Configurados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Tipo Límite</th>
                            <th class="px-4 py-3 text-left">Origen / Grupo</th>
                            <th class="px-4 py-3 text-right">Monto Tope</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($topes as $t)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $t->plan->name }}</td>
                                <td class="px-4 py-3 text-blue-900 font-semibold capitalize">{{ $t->limit_type }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $t->service_group ?? 'Todo el plan' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">DOP {{ number_format($t->amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $t->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Nuevo Tope de Cobertura</h3>
            <form action="{{ route('ars.planes_salud.guardar_tope') }}" method="POST" class="space-y-4">
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
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Límite</label>
                    <select name="limit_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="individual">Individual</option>
                        <option value="familiar">Familiar</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Grupo de Servicios (Opcional)</label>
                    <input type="text" name="service_group" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Odontología, Alto Costo">
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Monto Límite (DOP)</label>
                    <input type="number" name="amount" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período</label>
                    <select name="period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                        <option value="anual">Anual</option>
                        <option value="evento">Por Evento</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Configurar Tope</button>
            </form>
        </div>
    </div>
</div>
@endsection