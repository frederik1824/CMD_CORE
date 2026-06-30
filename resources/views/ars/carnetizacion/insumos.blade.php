@extends('layouts.ars')
@section('title', 'Insumos de Impresión')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Insumos de Impresión</h2>
            <p class="text-xs text-slate-500 font-medium">Control de stock e inventario de tarjetas plásticas y ribbons.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Nivel de Stocks</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($insumos as $ins)
                    <div class="p-4 rounded-2xl bg-slate-50/50 border border-slate-100 flex flex-col justify-between">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">{{ $ins->name }}</span>
                        <div class="mt-2 flex items-baseline">
                            <span class="text-2xl font-bold text-slate-950 font-mono">{{ $ins->current_stock }}</span>
                            <span class="text-[9px] text-slate-400 ml-1">/ {{ $ins->initial_stock }} {{ $ins->unit }}</span>
                        </div>
                        @if($ins->current_stock < ($ins->initial_stock * 0.15))
                            <span class="text-[9px] text-rose-600 font-bold block mt-1">Stock Crítico!</span>
                        @else
                            <span class="text-[9px] text-emerald-600 font-bold block mt-1">Stock Saludable</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <h3 class="font-bold text-slate-800 pt-4">Movimientos de Inventario</h3>
            <div class="overflow-x-auto max-h-[300px]">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Insumo</th>
                            <th class="px-4 py-3 text-left">Centro</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-left">Concepto / Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($movimientos as $mov)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $mov->supply->name }}</td>
                                <td class="px-4 py-3">{{ $mov->printingCenter->name ?? 'Sede Central' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold
                                        {{ $mov->movement_type === 'entrada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 'bg-rose-50 text-rose-700 border border-rose-250' }}">
                                        {{ $mov->movement_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold">{{ $mov->quantity }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $mov->reason }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Registrar Carga / Entrada</h3>
            <form action="{{ route('ars.carnetizacion.registrar_movimiento') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Insumo</label>
                    <select name="supply_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        @foreach($insumos as $in)
                            <option value="{{ $in->id }}">{{ $in->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Centro de Destino</label>
                    <select name="printing_center_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        @foreach($centros as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Movimiento</label>
                    <select name="movement_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="entrada">Entrada (Abastecimiento)</option>
                        <option value="salida">Salida (Ajuste / Descarte)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Cantidad</label>
                    <input type="number" name="quantity" value="100" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo</label>
                    <textarea name="reason" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Compra de insumos lote A..." required></textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Cargar Stock</button>
            </form>
        </div>
    </div>
</div>
@endsection