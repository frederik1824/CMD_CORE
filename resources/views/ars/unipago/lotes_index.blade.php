@extends('layouts.core')

@section('title', 'Lotes de Afiliación Unipago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lotes de Afiliación ante Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de transmisión de estructuras de titulares y dependientes para validación nacional de cápitas.</p>
        </div>
        <a href="{{ route('ars.unipago.dashboard') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la central
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transmitir Nuevo Lote (Izquierda 1/3) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                <svg class="w-4.5 h-4.5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Transmitir Lote (Simulador)</span>
            </h3>

            <form action="{{ route('ars.unipago.lotes.subir') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Lote</label>
                    <select name="batch_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none transition-all">
                        <option value="titular">Titulares (Carga Masiva)</option>
                        <option value="dependientes">Dependientes (Carga Familiar)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Registros del Lote (CSV: Cédula, Nombre, Apellido)</label>
                    <textarea name="raw_records" rows="8" required
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                              placeholder="22500756150,Juan,Perez&#10;22500756159,Juan,Gomez&#10;22500756158,Pedro,Ramirez"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-center block">
                    Transmitir Lote (Validación VE)
                </button>
            </form>
        </div>

        <!-- Historial de Lotes Transmitidos (Derecha 2/3) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                Historial de Lotes Transmitidos
            </h3>

            <div class="border border-slate-100 rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">No. Lote</th>
                            <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-right text-[9px] uppercase tracking-wider">Registros</th>
                            <th class="px-4 py-3 text-right text-[9px] uppercase tracking-wider">OK</th>
                            <th class="px-4 py-3 text-right text-[9px] uppercase tracking-wider">Rechazos</th>
                            <th class="px-4 py-3 text-center text-[9px] uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-center text-[9px] uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                        @forelse($lotes as $b)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-[#041e49]">
                                    {{ $b->batch_number }}
                                    <span class="block text-[10px] text-slate-400 font-normal">Envío: {{ $b->submitted_at->format('d/m h:i A') }}</span>
                                </td>
                                <td class="px-4 py-3 uppercase text-slate-600 font-semibold">{{ $b->batch_type }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ $b->total_records }}</td>
                                <td class="px-4 py-3 text-right font-mono text-emerald-600 font-bold">{{ $b->total_ok }}</td>
                                <td class="px-4 py-3 text-right font-mono text-rose-500 font-bold">{{ $b->total_rejected }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold tracking-wider
                                        {{ $b->status === 'PC' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 
                                           ($b->status === 'PE' ? 'bg-amber-50 text-amber-700 border border-amber-150' : 
                                           ($b->status === 'VE' ? 'bg-blue-50 text-blue-700 border border-blue-150' : 'bg-rose-50 text-rose-700 border border-rose-150')) }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <a href="{{ route('ars.unipago.lotes.show', $b->id) }}" class="text-[#041e49] hover:text-blue-900 font-bold border border-slate-200 rounded-full px-3 py-1.5 hover:bg-slate-50 transition">
                                        Ver Lote
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                    No se registran lotes de afiliación transmitidos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pt-2">
                {{ $lotes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
