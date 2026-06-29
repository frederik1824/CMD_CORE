@extends('layouts.core')

@section('title', 'Notificaciones de Cápitas')

@section('content')
<div class="space-y-6" x-data="{ openRejectModal: false, rejectUrl: '', rejectReason: '' }">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Cápitas Notificadas e Individualización</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de auditoría financiera para confirmar o rechazar las cápitas de salud emitidas por Unipago.</p>
        </div>
        <a href="{{ route('ars.unipago.dashboard') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la central
        </a>
    </div>

    <!-- Tabla Cápitas -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">No. Notificación</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">Afiliado Paciente</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Período</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Monto Cápita</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                    @forelse($capitas as $c)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-[#041e49]">
                                {{ $c->notification_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Recibido: {{ $c->notified_at->format('d/m Y') }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $c->afiliado->nombres ?? 'N/A' }} {{ $c->afiliado->primer_apellido ?? ($c->afiliado->apellidos ?? '') }}
                                <span class="block text-[10px] text-slate-400 font-normal">Cédula: {{ $c->afiliado->cedula ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-500 font-mono">
                                {{ $c->period }}
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-slate-900 font-mono">
                                DOP {{ number_format($c->capitation_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-500">
                                {{ $c->individualization_type }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-bold tracking-wider
                                    {{ $c->status === 'IC' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 
                                       ($c->status === 'NT' || $c->status === 'PE' ? 'bg-amber-50 text-amber-700 border border-amber-150' : 
                                       ($c->status === 'DI' ? 'bg-blue-50 text-blue-700 border border-blue-150' : 'bg-rose-50 text-rose-700 border border-rose-150')) }}">
                                    {{ $c->status === 'IC' ? 'Confirmada (IC)' : ($c->status === 'NT' || $c->status === 'PE' ? 'Pendiente' : ($c->status === 'DI' ? 'Dispersada' : 'Rechazada')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                @if(in_array($c->status, ['NT', 'PE']))
                                    <!-- Confirmar -->
                                    <form action="{{ route('ars.unipago.capitas.accion', [$c->id, 'confirmar']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-full px-3 py-1 font-bold text-[10px] transition shadow-2xs">
                                            Confirmar
                                        </button>
                                    </form>
                                    <!-- Rechazar -->
                                    <button type="button" @click="rejectUrl = '{{ route('ars.unipago.capitas.accion', [$c->id, 'rechazar']) }}'; openRejectModal = true"
                                            class="bg-rose-500 hover:bg-rose-600 text-white rounded-full px-3 py-1 font-bold text-[10px] transition shadow-2xs">
                                        Rechazar
                                    </button>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">No requiere acción</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se registran cápitas pendientes de conciliación en el período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $capitas->links() }}
        </div>
    </div>

    <!-- Modal Rechazar Cápita -->
    <div x-show="openRejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50" x-cloak>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full border border-slate-100 shadow-xl space-y-4" @click.away="openRejectModal = false">
            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                <h3 class="text-sm font-bold text-[#041e49]">Rechazar Individualización de Cápita</h3>
                <button type="button" @click="openRejectModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="rejectUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Rechazo</label>
                    <textarea name="rejection_reason" x-model="rejectReason" required rows="3"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none transition-all"
                              placeholder="Ej: Registro civil inactivo o duplicación de aportes en otra ARS..."></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-50">
                    <button type="button" @click="openRejectModal = false" class="bg-slate-100 text-slate-600 rounded-full px-4 py-2.5 font-bold hover:bg-slate-200 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-rose-600 text-white rounded-full px-5 py-2.5 font-bold hover:bg-rose-700 transition shadow-xs">
                        Rechazar Cápita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
