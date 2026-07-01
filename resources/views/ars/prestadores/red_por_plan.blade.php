@extends('layouts.ars')

@section('title', 'Red por Plan')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Redes de Prestadoras por Plan</h2>
            <p class="text-xs text-slate-500 font-medium">Asociación de redes de clínicas y laboratorios autorizados a planes específicos de salud.</p>
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
        <!-- Registrar Red -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Crear Red de Plan</h3>
            <form action="{{ route('ars.prestadores.guardar_red_por_plan') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre de la Red <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" placeholder="Ej. Red Plan Básico Salud" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Planes de Salud Cobertura <span class="text-rose-500">*</span></label>
                    <div class="space-y-1.5 max-h-32 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        @foreach($planes as $p)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="plan_ids[]" value="{{ $p->id }}" class="rounded text-[#041e49] focus:ring-blue-100">
                                <span>{{ $p->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Crear Red y Asociar Planes</button>
            </form>
        </div>

        <!-- Listado de Redes -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Redes por Plan Configuradas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Nombre de la Red</th>
                            <th class="px-4 py-3 text-left">Planes Asociados</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($redes as $r)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $r->id }}</td>
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $r->name }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($r->plans as $p)
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[8px] font-bold text-blue-700 border border-blue-200">{{ $p->name }}</span>
                                        @empty
                                            <span class="text-slate-400 italic">Ningún plan asociado</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado redes por plan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
