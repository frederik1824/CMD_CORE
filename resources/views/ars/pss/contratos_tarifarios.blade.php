@extends('layouts.ars')

@section('title', 'Contratos & Tarifarios PSS')

@section('content')
<div class="space-y-6" x-data="{ openCrearModal: false }">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Contratos & Tarifarios PSS</h2>
            <p class="text-xs text-slate-400 font-medium">Gestión de acuerdos de servicios y tarifas asignadas a prestadores médicos</p>
        </div>
        <div>
            <button @click="openCrearModal = true" class="bg-[#0056c5] hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-full transition text-xs shadow-xs">
                + Registrar Nuevo Contrato
            </button>
        </div>
    </div>

    <!-- Bento Grid de Alertas / Indicadores -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">Próximos a Vencer (<30 días)</span>
                <span class="text-xl font-black text-slate-800 mt-1 block">{{ $proximosVencer }} contratos</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">Contratos Vencidos</span>
                <span class="text-xl font-black text-rose-700 mt-1 block">{{ $vencidos }} contratos</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-slate-50 text-slate-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">PSS sin Contrato Vigente</span>
                <span class="text-xl font-black text-slate-800 mt-1 block">{{ $pssSinContrato }} prestadores</span>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.pss.contratos_tarifarios') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Prestadora (PSS)</label>
                <select name="pss_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all">
                    <option value="">Todas las prestadoras...</option>
                    @foreach($pssList as $p)
                        <option value="{{ $p->id }}" {{ $pssId == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Estatus</label>
                <select name="status" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all">
                    <option value="">Todos los estados...</option>
                    <option value="vigente" {{ $status === 'vigente' ? 'selected' : '' }}>Vigente</option>
                    <option value="vencido" {{ $status === 'vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="suspendido" {{ $status === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    <option value="borrador" {{ $status === 'borrador' ? 'selected' : '' }}>Borrador</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-[#0056c5] text-white rounded-full px-5 py-2.5 font-bold hover:bg-blue-700 transition shadow-xs text-center">
                    Filtrar Contratos
                </button>
                <a href="{{ route('ars.pss.contratos_tarifarios') }}" class="bg-slate-100 text-slate-600 rounded-full px-5 py-2.5 font-bold hover:bg-slate-200 transition text-center">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Listado -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">No. Contrato / Nombre</th>
                        <th scope="col" class="px-6 py-4 text-left">Prestador PSS</th>
                        <th scope="col" class="px-6 py-4 text-left">Tipo</th>
                        <th scope="col" class="px-6 py-4 text-center">Vigencia</th>
                        <th scope="col" class="px-6 py-4 text-center">Estatus</th>
                        <th scope="col" class="px-6 py-4 text-center w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                    @forelse($contratos as $c)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-800 font-mono block text-xs">{{ $c->contract_number }}</span>
                                <span class="text-slate-400 block mt-0.5">{{ $c->contract_name }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $c->pss->nombre }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ $c->contract_type }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="block text-slate-600 font-bold font-mono">{{ $c->start_date->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">al {{ $c->end_date->format('d/m/Y') }}</span>
                                @if($c->status === 'vigente' && $c->end_date->isFuture() && $c->end_date->diffInDays(now()) <= 30)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[9px] font-black text-amber-700 uppercase mt-1">
                                        ⚠️ Vence en {{ $c->end_date->diffInDays(now()) }} días
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold border uppercase tracking-wider
                                    {{ $c->status === 'vigente' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                       ($c->status === 'vencido' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-50 text-slate-600 border-slate-200') }}">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ars.pss.contratos_tarifarios.show', $c->id) }}" class="text-teal-600 hover:text-teal-800 font-bold border border-teal-200 hover:border-teal-400 px-3 py-1.5 rounded-full transition text-[10px] shadow-2xs bg-white">
                                    Ver Tarifas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-450 font-semibold">
                                No se encontraron contratos en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contratos->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $contratos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Registrar Nuevo Contrato -->
    <div x-show="openCrearModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full border border-slate-100 p-5 space-y-4"
             @click.away="openCrearModal = false">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Registrar Nuevo Contrato</h3>
                <p class="text-[11px] text-slate-400">Convenio de tarifas y servicios con PSS.</p>
            </div>

            <form action="{{ route('ars.pss.contratos_tarifarios.crear') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">PSS Prestador <span class="text-rose-500">*</span></label>
                        <select name="pss_id" required class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                            <option value="">Seleccione PSS...</option>
                            @foreach($pssList as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Número de Contrato <span class="text-rose-500">*</span></label>
                        <input type="text" name="contract_number" required placeholder="Ej: CONV-009238" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Nombre del Convenio <span class="text-rose-500">*</span></label>
                        <input type="text" name="contract_name" required placeholder="Ej: Acuerdo de Tarifas 2026" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Contrato <span class="text-rose-500">*</span></label>
                        <select name="contract_type" required class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                            <option value="general">General</option>
                            <option value="especialidad">Especialidad</option>
                            <option value="capitado">Capitado</option>
                            <option value="evento">Por Evento</option>
                            <option value="mixto">Mixto</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Inicio <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" required class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Fin <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" required class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Observaciones</label>
                    <textarea name="observations" rows="3" class="w-full rounded-xl border border-slate-200 bg-[#eaf1fb]/40 p-3 text-slate-800 focus:bg-white focus:outline-none placeholder:text-slate-400" placeholder="Ingrese detalles o términos especiales del convenio..."></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-50">
                    <button type="button" @click="openCrearModal = false" class="px-4 py-2 border border-slate-200 rounded-full text-slate-500 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#0056c5] hover:bg-blue-700 text-white font-bold rounded-full transition shadow-xs">
                        Registrar Contrato
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
