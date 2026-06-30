@extends('layouts.ars')
@section('title', 'Planes de Salud')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Planes de Salud</h2>
            <p class="text-xs text-slate-500 font-medium">Configuración del catálogo de planes de la ARS.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
            Ecosistema Core
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Planes Configurados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-center">Vigencia</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($planes as $p)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $p->code }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-slate-550 capitalize">{{ $p->plan_type }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $p->effective_from }} {{ $p->effective_to ? ' al ' . $p->effective_to : '' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">{{ $p->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Crear Nuevo Plan</h3>
            <form action="{{ route('ars.planes_salud.guardar_plan') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código Único</label>
                    <input type="text" name="code" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" placeholder="Ej. PLAN-COMP" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre del Plan</label>
                    <input type="text" name="name" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" placeholder="Ej. Plan Especial Complementario" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Plan</label>
                    <select name="plan_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="pdss">PDSS</option>
                        <option value="complementario">Complementario</option>
                        <option value="voluntario">Voluntario</option>
                        <option value="alternativo">Alternativo</option>
                        <option value="pensionado">Pensionado</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Descripción</label>
                    <textarea name="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Inicio</label>
                        <input type="date" name="effective_from" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Fin</label>
                        <input type="date" name="effective_to" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2">
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs">Guardar Plan de Salud</button>
            </form>
        </div>
    </div>
</div>
@endsection