@extends('layouts.core')

@section('title', 'Auditoría de Reclamaciones')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reclamaciones y Facturas PSS</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja general de recepción y auditoría clínica/administrativa de facturas recibidas de prestadores.</p>
        </div>
    </div>

    <!-- Filtros Bento -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
        <form action="{{ route('ars.reclamaciones.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Buscar</label>
                <input type="text" name="search" value="{{ $search }}" 
                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                       placeholder="No. Reclamación, Factura, NCF...">
            </div>

            <div>
                <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Prestadora (PSS)</label>
                <select name="pss_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todas las prestadoras...</option>
                    @foreach($pssList as $p)
                        <option value="{{ $p->id }}" {{ $pssId == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Estado</label>
                <select name="status" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todos los estados...</option>
                    @foreach($estados as $e)
                        <option value="{{ $e }}" {{ $status === $e ? 'selected' : '' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-[#041e49] text-white rounded-full px-4 py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-center">
                    Filtrar Resultados
                </button>
                <a href="{{ route('ars.reclamaciones.index') }}" class="bg-slate-100 text-slate-600 rounded-full px-4 py-2.5 font-bold hover:bg-slate-200 transition text-center">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Reclamaciones -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-32">Reclamación</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-40">Prestadora PSS</th>
                        <th class="px-6 py-4 class text-left text-[9px] uppercase tracking-wider w-36">Afiliado</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-32">No. Factura</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider w-32">Reclamado (DOP)</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider w-32">Aprobado (DOP)</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider w-32">Estado</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($reclamaciones as $rec)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-[#041e49]">
                                {{ $rec->claim_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Aut: {{ $rec->authorization->numero_autorizacion }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $rec->pss->nombre }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">
                                {{ $rec->afiliado ? $rec->afiliado->nombres . ' ' . ($rec->afiliado->primer_apellido ?? $rec->afiliado->apellidos) : 'N/A' }}
                                <span class="block text-[10px] text-slate-400 font-normal">NSS: {{ $rec->afiliado->nss ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-semibold text-slate-500">
                                {{ $rec->invoice_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">NCF: {{ $rec->ncf }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900 font-mono">
                                DOP {{ number_format($rec->claimed_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-teal-600 font-mono">
                                DOP {{ number_format($rec->approved_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wider
                                    {{ $rec->status === 'Reclamación aprobada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 
                                       ($rec->status === 'En auditoría de reclamación' ? 'bg-amber-50 text-amber-700 border border-amber-250' : 
                                       ($rec->status === 'Pagada' || $rec->status === 'Cerrada' ? 'bg-blue-50 text-blue-700 border border-blue-250' : 'bg-slate-50 text-slate-600 border border-slate-200')) }}">
                                    {{ $rec->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('ars.reclamaciones.show', $rec->id) }}" 
                                   class="text-[#041e49] hover:text-blue-900 font-bold border border-slate-250 rounded-full px-3 py-1.5 hover:bg-slate-50 transition">
                                    Auditar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se encontraron reclamaciones pendientes de auditoría en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $reclamaciones->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
