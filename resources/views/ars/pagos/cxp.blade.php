@extends('layouts.core')

@section('title', 'Cuentas por Pagar PSS')

@section('content')
<div class="space-y-6" x-data="{ selectedPayables: [], openBatchModal: false }">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Cuentas por Pagar (CXP) a Prestadores</h2>
            <p class="text-xs text-slate-500 font-medium">Historial y liquidación de obligaciones monetarias con clínicas y centros médicos tras aprobación de auditoría.</p>
        </div>
        <div class="flex space-x-2">
            <button type="button" @click="openBatchModal = true" :disabled="selectedPayables.length === 0"
                    class="bg-[#041e49] text-white rounded-full px-5 py-2 text-xs font-bold hover:bg-slate-800 disabled:bg-slate-200 disabled:text-slate-400 transition shadow-xs flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Crear Lote de Pago (<span x-text="selectedPayables.length"></span>)</span>
            </button>
            <a href="{{ route('ars.lotes.index') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
                Ver Lotes de Pago
            </a>
        </div>
    </div>

    <!-- Filtros Bento -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
        <form action="{{ route('ars.pagos.cxp') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
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
                <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Estado de Obligación</label>
                <select name="status" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todos los estados...</option>
                    @foreach($estados as $e)
                        <option value="{{ $e }}" {{ $status === $e ? 'selected' : '' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-[#041e49] text-white rounded-full px-4 py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-center">
                    Filtrar CXP
                </button>
                <a href="{{ route('ars.pagos.cxp') }}" class="bg-slate-100 text-slate-600 rounded-full px-4 py-2.5 font-bold hover:bg-slate-200 transition text-center">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla CXP -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-center w-12">
                            <!-- Checkbox Select All (opcional) -->
                        </th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-32">No. CXP</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-40">Prestadora PSS</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider w-36">Documentos Reclamación</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider w-32">Monto Bruto (DOP)</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider w-32">Objeciones (DOP)</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider w-32">Monto Neto Aprobado</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider w-32">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($payables as $ap)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                @if(in_array($ap->status, ['Generada', 'Validada']))
                                    <input type="checkbox" :value="{{ $ap->id }}" x-model="selectedPayables"
                                           class="rounded border-slate-300 text-blue-900 focus:ring-blue-800">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-[#041e49]">
                                {{ $ap->payable_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Recl: {{ $ap->claim->claim_number }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $ap->pss->nombre }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">
                                Factura: {{ $ap->claim->invoice_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">NCF: {{ $ap->claim->ncf }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-400 font-mono">
                                DOP {{ number_format($ap->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-rose-500 font-mono">
                                DOP {{ number_format($ap->retained_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-[#041e49] font-mono bg-blue-50/20">
                                DOP {{ number_format($ap->net_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wider
                                    {{ $ap->status === 'Generada' || $ap->status === 'Validada' ? 'bg-amber-50 text-amber-700 border border-amber-250' : 
                                       ($ap->status === 'En lote de pago' ? 'bg-blue-50 text-blue-700 border border-blue-250' : 
                                       ($ap->status === 'Pagada' || $ap->status === 'Conciliada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 'bg-slate-50 text-slate-600 border border-slate-200')) }}">
                                    {{ $ap->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se encontraron cuentas por pagar registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $payables->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Modal Crear Lote de Pago (Drawer/Modal) -->
    <div x-show="openBatchModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50" x-cloak>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full border border-slate-100 shadow-xl space-y-4" @click.away="openBatchModal = false">
            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                <h3 class="text-sm font-bold text-[#041e49]">Consolidar Lote de Pago</h3>
                <button type="button" @click="openBatchModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('ars.lotes.crear') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <!-- Inputs ocultos para enviar IDs de CXP seleccionadas -->
                <template x-for="id in selectedPayables" :key="id">
                    <input type="hidden" name="cxp_ids[]" :value="id">
                </template>

                <div>
                    <p class="font-semibold text-slate-600">Obligaciones seleccionadas:</p>
                    <p class="text-lg font-bold text-[#041e49] mt-1"><span x-text="selectedPayables.length"></span> Cuentas por Pagar</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Programada de Pago</label>
                    <input type="date" name="scheduled_payment_date" required min="{{ date('Y-m-d') }}"
                           class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-50">
                    <button type="button" @click="openBatchModal = false" class="bg-slate-100 text-slate-600 rounded-full px-4 py-2.5 font-bold hover:bg-slate-200 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-[#041e49] text-white rounded-full px-5 py-2.5 font-bold hover:bg-slate-800 transition shadow-xs">
                        Crear Lote (Borrador)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
