@extends('layouts.ars')

@section('title', 'Coberturas de Planes')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Coberturas de Planes de Salud</h2>
            <p class="text-xs text-slate-500 font-medium">Asociación de porcentajes de cobertura, copagos y límites de servicios a planes específicos.</p>
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
        <!-- Registrar Cobertura -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Configurar Cobertura</h3>
            <form action="{{ route('ars.planes_salud.guardar_cobertura') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Plan de Salud <span class="text-rose-500">*</span></label>
                    <select name="health_plan_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($planes as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Servicio Catálogo (PDSS) <span class="text-rose-500">*</span></label>
                    <select name="pdss_service_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id }}">{{ $s->simon_code }} - {{ $s->coverage_description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Cobertura (%) <span class="text-rose-500">*</span></label>
                        <input type="number" name="coverage_percent" value="80" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Copago (%) <span class="text-rose-500">*</span></label>
                        <input type="number" name="copay_percent" value="20" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Copago Fijo (DOP) <span class="text-rose-500">*</span></label>
                        <input type="number" name="fixed_copay" value="0" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Límite Monto (DOP) <span class="text-rose-500">*</span></label>
                        <input type="number" name="limit_amount" value="5000" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período Límite <span class="text-rose-500">*</span></label>
                        <select name="limit_period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                            <option value="Anual">Anual</option>
                            <option value="Mensual">Mensual</option>
                            <option value="Por Evento">Por Evento</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Días de Espera <span class="text-rose-500">*</span></label>
                        <input type="number" name="waiting_period_days" value="0" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">¿Requiere Autorización Previa? <span class="text-rose-500">*</span></label>
                    <select name="requires_authorization" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        <option value="1">Sí, requiere aprobación</option>
                        <option value="0">No, cobertura directa</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Guardar Cobertura</button>
            </form>
        </div>

        <!-- Listado de Coberturas -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Configuración de Coberturas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Plan de Salud</th>
                            <th class="px-4 py-3 text-left">Servicio / Cups</th>
                            <th class="px-4 py-3 text-center">Cobert. / Copago</th>
                            <th class="px-4 py-3 text-mono text-right">Límite</th>
                            <th class="px-4 py-3 text-center">Esperas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($coberturas as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $c->plan?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-650">
                                    <span class="block font-semibold">{{ $c->service?->coverage_description ?? 'N/A' }}</span>
                                    <span class="block font-mono text-[9px] text-slate-400">Cups: {{ $c->service?->cups_code ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="block">{{ $c->coverage_percent }}% / {{ $c->copay_percent }}%</span>
                                    <span class="block text-[9px] text-slate-450">Fijo: DOP {{ number_format($c->fixed_copay, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">DOP {{ number_format($c->limit_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $c->waiting_period_days }} días</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han configurado coberturas de planes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pt-4 border-t border-slate-100">
                {{ $coberturas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection