@extends('layouts.core')

@section('title', 'Libro Diario de Asientos Contables')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    📂 Transacciones
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Libro Diario Contable</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de asientos de diario generales y automáticos generados por el devengo operativo.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.contabilidad.dashboard') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.contabilidad.asientos') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Período Contable</label>
                <select name="period" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-200 text-xs text-slate-600 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all">
                    <option value="">Todos los períodos</option>
                    @foreach($periodos as $per)
                        <option value="{{ $per->period_code }}" {{ $period == $per->period_code ? 'selected' : '' }}>
                            {{ $per->period_code }} ({{ $per->is_closed ? 'Cerrado' : 'Abierto' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Módulo Fuente</label>
                <select name="module" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-200 text-xs text-slate-600 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $mod)
                        <option value="{{ $mod }}" {{ $module == $mod ? 'selected' : '' }}>
                            {{ ucfirst($mod) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('ars.contabilidad.asientos') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold py-2.5 rounded-2xl hover:bg-slate-100 transition text-center">
                    Limpiar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de Asientos -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">No. Asiento</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Diario</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Descripción / Glosa</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Débito</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Crédito</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($entries as $ent)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono text-slate-700 whitespace-nowrap font-bold">
                                {{ $ent->entry_number }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap font-mono">
                                {{ $ent->entry_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 font-bold whitespace-nowrap font-mono">
                                {{ $ent->journal->code ?? 'GEN' }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 font-semibold max-w-[280px] truncate" title="{{ $ent->description }}">
                                {{ $ent->description }}
                                <span class="text-[9px] text-slate-400 block font-mono">Origen: {{ ucfirst($ent->source_module) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-800 font-mono font-bold">
                                DOP {{ number_format($ent->total_debit, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-800 font-mono font-bold">
                                DOP {{ number_format($ent->total_credit, 2) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold {{ $ent->status === 'posteado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                    {{ ucfirst($ent->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <a href="{{ route('ars.contabilidad.asiento_show', $ent->id) }}" class="text-blue-600 hover:text-blue-800 transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle Asiento">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">receipt_long</span>
                                <p class="text-sm font-semibold">No se encontraron asientos contables registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
