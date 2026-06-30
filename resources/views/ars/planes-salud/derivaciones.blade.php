@extends('layouts.ars')
@section('title', 'Reglas de Derivación')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reglas de Derivación</h2>
            <p class="text-xs text-slate-500 font-medium">Motor de reglas automáticas de derivación de coberturas.</p>
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
            <h3 class="font-bold text-slate-800">Reglas Activas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Tipo Derivación</th>
                            <th class="px-4 py-3 text-left">Condición</th>
                            <th class="px-4 py-3 text-left">Resultado</th>
                            <th class="px-4 py-3 text-center">Prioridad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($derivaciones as $r)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $r->plan->name }}</td>
                                <td class="px-4 py-3 font-semibold text-blue-900">{{ $r->derivation_type }}</td>
                                <td class="px-4 py-3 font-mono text-[10px] text-slate-650 bg-slate-50 p-2 rounded-lg block mt-1">{{ json_encode($r->condition_json) }}</td>
                                <td class="px-4 py-3 font-mono text-[10px] text-slate-600">{{ json_encode($r->result_json) }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold">{{ $r->priority }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Nueva Regla</h3>
            <form action="{{ route('ars.planes_salud.guardar_derivacion') }}" method="POST" class="space-y-4">
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
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Derivación</label>
                    <select name="derivation_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="diagnostico">Por Diagnóstico</option>
                        <option value="rango_edad">Rango de Edad</option>
                        <option value="prestador">Por Prestador</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Condición (JSON)</label>
                    <textarea name="condition" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono" placeholder='{"edad_min": 65}' required></textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Resultado (JSON)</label>
                    <textarea name="result" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono" placeholder='{"cobertura_adicional": 10}' required></textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Prioridad</label>
                    <input type="number" name="priority" value="1" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs">Guardar Regla</button>
            </form>
        </div>
    </div>
</div>
@endsection