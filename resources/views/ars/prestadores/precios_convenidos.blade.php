@extends('layouts.ars')

@section('title', 'Precios Convenidos')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Precios Convenidos</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja maestra de tarifas acordadas con las prestadoras (PSS) por servicio médico y plan de salud.</p>
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
        <!-- Registrar Tarifa -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Configurar Precio Convenido</h3>
            <form action="{{ route('ars.prestadores.guardar_precio_convenido') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Prestadora (PSS) <span class="text-rose-500">*</span></label>
                    <select name="pss_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($pss as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} (RNC: {{ $p->rnc }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Plan de Salud <span class="text-rose-500">*</span></label>
                    <select name="health_plan_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($planes as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Servicio Médico <span class="text-rose-500">*</span></label>
                    <select name="servicio_medico_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tarifa Acordada (DOP) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" placeholder="Ej. 1200" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Guardar Convenio</button>
            </form>
        </div>

        <!-- Listado de Precios Convenidos -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Precios Convenidos Activos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Prestadora (PSS)</th>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Servicio Médico</th>
                            <th class="px-4 py-3 text-mono text-right">Tarifa (DOP)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($convenios as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $c->pss?->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->plan?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="block font-mono text-[10px] text-slate-450">{{ $c->service?->codigo ?? 'N/A' }}</span>
                                    <span class="block">{{ $c->service?->descripcion ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">DOP {{ number_format($c->price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado convenios de precios de PSS.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pt-4 border-t border-slate-100">
                {{ $convenios->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
