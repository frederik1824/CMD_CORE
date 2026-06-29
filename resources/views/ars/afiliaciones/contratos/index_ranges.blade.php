@extends('layouts.ars')

@section('title', 'Rangos de Formularios')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Rangos de Formularios Autorizados
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Administración y monitoreo de los bloques numéricos aprobados para la afiliación ante Unipago.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-4 flex gap-3">
            <a href="{{ route('ars.contract_control.dashboard') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Dashboard
            </a>
            <a href="{{ route('ars.contract_control.ranges.create') }}" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="add">add</span>
                Nuevo Rango
            </a>
        </div>
    </div>

    <!-- Barra de Filtro -->
    <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-200">
        <form action="{{ route('ars.contract_control.ranges.index') }}" method="GET" class="flex gap-4 items-end max-w-md">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Buscar Rango</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none" placeholder="Código o descripción...">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Buscar
            </button>
            <a href="{{ route('ars.contract_control.ranges.index') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Limpiar
            </a>
        </form>
    </div>

    <!-- Tabla de Rangos -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Código Rango</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Límites (Inicio - Fin)</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Disponibles</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Uso Real (OK / RE / PE)</th>
                        <th scope="col" class="px-6 py-4 class-col text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rangos as $r)
                        <tr class="hover:bg-slate-50 transition text-xs">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $r->range_code }}
                                <span class="block text-[9.5px] text-slate-400 font-semibold font-sans mt-0.5">{{ $r->description }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-650">
                                [{{ $r->start_number }} - {{ $r->end_number }}]
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-600">
                                {{ number_format($r->total_numbers) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-emerald-600">
                                {{ number_format($r->available_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-650 leading-relaxed font-mono text-[10.5px]">
                                OK: <span class="text-emerald-600 font-bold">{{ $r->ok_count }}</span> • 
                                RE: <span class="text-rose-600 font-bold">{{ $r->rejected_count }}</span> • 
                                PE: <span class="text-amber-500 font-bold">{{ $r->pending_count }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-bold tracking-wide {{ 
                                    $r->status === 'activo' ? 'bg-emerald-50 text-emerald-700' : (
                                    $r->status === 'agotado' ? 'bg-slate-100 text-slate-500' : 'bg-rose-50 text-rose-700')
                                }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('ars.contract_control.ranges.show', $r->id) }}" class="text-brand-600 hover:text-brand-800 transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle de Rango">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">No hay rangos de contratos autorizados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rangos->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $rangos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
