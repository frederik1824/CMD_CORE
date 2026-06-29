@extends('layouts.authorization-portal')

@section('title', 'Mis Reclamaciones')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center space-x-4">
            <div class="bg-gradient-to-tr from-teal-600 to-teal-400 text-white p-2 rounded-xl shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="leading-none">
                <h2 class="text-base font-bold text-slate-800">Mis Reclamaciones Facturadas</h2>
                <p class="text-[11px] text-slate-400 font-medium">Historial y estado de auditoría de reclamaciones enviadas a la ARS.</p>
            </div>
        </div>
        <a href="{{ route('pss.solicitudes') }}" class="bg-teal-600 text-white rounded-full px-4 py-2 text-xs font-bold hover:bg-teal-700 transition flex items-center space-x-1.5 shadow-sm">
            <span>Nueva Reclamación / Facturar</span>
        </a>
    </div>

    <!-- Tabla Reclamaciones PSS -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">No. Reclamación</th>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">No. Factura / NCF</th>
                        <th class="px-6 py-3.5 text-right text-[9px] uppercase tracking-wider">Monto Reclamado</th>
                        <th class="px-6 py-3.5 text-right text-[9px] uppercase tracking-wider">Monto Aprobado</th>
                        <th class="px-6 py-3.5 text-center text-[9px] uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3.5 text-center text-[9px] uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                    @forelse($reclamaciones as $rec)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3 font-mono font-bold text-[#0f766e]">
                                {{ $rec->claim_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Aut: {{ $rec->authorization->numero_autorizacion }}</span>
                            </td>
                            <td class="px-6 py-3">
                                {{ $rec->afiliado ? $rec->afiliado->nombres . ' ' . ($rec->afiliado->primer_apellido ?? $rec->afiliado->apellidos) : 'N/A' }}
                                <span class="block text-[10px] text-slate-400 font-normal">Póliza: {{ $rec->authorization->numero_contrato ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-3 font-mono text-slate-650">
                                {{ $rec->invoice_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">NCF: {{ $rec->ncf }}</span>
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-slate-900 font-mono">
                                DOP {{ number_format($rec->claimed_amount, 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-teal-600 font-mono">
                                DOP {{ number_format($rec->approved_amount, 2) }}
                            </td>
                            <td class="px-6 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold tracking-wider
                                    {{ $rec->status === 'Reclamación aprobada' || $rec->status === 'Cerrada' || $rec->status === 'Pagada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 
                                       ($rec->status === 'En auditoría de reclamación' || $rec->status === 'Reclamación recibida' ? 'bg-amber-50 text-amber-700 border border-amber-150' : 'bg-slate-50 text-slate-600 border border-slate-200') }}">
                                    {{ $rec->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('pss.reclamaciones.show', $rec->id) }}" class="text-teal-600 hover:text-teal-800 hover:bg-teal-50 transition p-2 rounded-full inline-flex items-center justify-center animate-fade-in" title="Ver Detalle">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se registran reclamaciones de facturación enviadas por esta prestadora.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $reclamaciones->links() }}
        </div>
    </div>
</div>
@endsection
