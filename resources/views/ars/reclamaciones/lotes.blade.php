@extends('layouts.ars')

@section('title', 'Lotes de Reclamación')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lotes de Reclamación</h2>
            <p class="text-xs text-slate-500 font-medium">Generación de lotes de pago para reclamaciones aprobadas.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Lotes de Reclamación Generados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código Lote</th>
                            <th class="px-4 py-3 text-center">Registros</th>
                            <th class="px-4 py-3 text-right">Monto Lote</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($lotes as $l)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $l->batch_number }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $l->total_items }}</td>
                                <td class="px-4 py-3 text-right font-bold">DOP {{ number_format($l->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-bold text-amber-700 border border-amber-200">{{ $l->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="/core/reclamaciones/lotes/ver/{{ $l->id }}" class="bg-slate-100 hover:bg-slate-200 text-[#041e49] rounded-full px-3 py-1 font-bold text-[10px]">Detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400 font-semibold">No hay lotes creados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Generar Nuevo Lote de Pago</h3>
            <p class="text-xs text-slate-450">Agrupar reclamaciones con "Cuenta por pagar generada" en un nuevo lote de tesorería.</p>
            
            <form action="{{ route('ars.reclamaciones.generar_lote') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Reclamaciones Listas</label>
                    <div class="space-y-2 border border-slate-100 rounded-2xl p-3 max-h-[180px] overflow-y-auto bg-slate-50/50">
                        @forelse($claimsAprobadas as $cl)
                            <label class="flex items-start space-x-2.5 p-1 hover:bg-white rounded-lg transition select-none">
                                <input type="checkbox" name="claim_ids[]" value="{{ $cl->id }}" class="mt-0.5 rounded text-blue-600 focus:ring-blue-100">
                                <div>
                                    <span class="font-mono font-bold block text-blue-900">{{ $cl->claim_number }}</span>
                                    <span class="text-[9px] text-slate-400 font-semibold">DOP {{ number_format($cl->claimed_amount, 2) }} - {{ $cl->pss->nombre }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-6 text-slate-400 font-semibold text-[10px]">No hay reclamaciones aprobadas disponibles.</div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Programada de Pago</label>
                    <input type="date" name="scheduled_payment_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white rounded-full py-2.5 font-bold hover:bg-blue-700 transition">Agrupar Lote de Pago</button>
            </form>
        </div>
    </div>


</div>
@endsection
