@extends('layouts.ars')

@section('title', 'Planes de Salud')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Planes de Salud</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de planes de cobertura complementaria, básico PDSS y planes alternativos de la ARS.</p>
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
        <!-- Registrar Plan -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Crear Nuevo Plan de Salud</h3>
            <form action="{{ route('ars.planes_salud.guardar_plan') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código Plan <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" placeholder="Ej. PLAN-BASICO" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre del Plan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" placeholder="Ej. Plan Básico PDSS" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Plan <span class="text-rose-500">*</span></label>
                    <select name="plan_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        <option value="Básico">Básico de Ley (PDSS)</option>
                        <option value="Complementario">Complementario Voluntario</option>
                        <option value="Especial">Especial Auto-Financiado</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha de Vigencia <span class="text-rose-500">*</span></label>
                    <input type="date" name="effective_from" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Descripción</label>
                    <textarea name="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Coberturas especiales..."></textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Crear Plan de Salud</button>
            </form>
        </div>

        <!-- Listado de Planes -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Planes de Salud Activos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código</th>
                            <th class="px-4 py-3 text-left">Nombre del Plan</th>
                            <th class="px-4 py-3 text-left">Tipo de Plan</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($planes as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-[#041e49] font-bold">{{ $p->code }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $p->plan_type }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado planes de salud.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection