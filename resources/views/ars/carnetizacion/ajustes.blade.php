@extends('layouts.ars')

@section('title', 'Ajustes de Impresión')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Ajustes y Desperdicios de Impresión</h2>
            <p class="text-xs text-slate-500 font-medium">Buzón de control de mermas, plásticos dañados por mal funcionamiento técnico y ajustes de stock.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Ajuste -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Ajuste / Merma</h3>
            <form action="{{ route('ars.carnetizacion.registrar_ajuste') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">ID Suministro (Insumo) <span class="text-rose-500">*</span></label>
                    <input type="number" name="supply_id" placeholder="Ej. 1" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">ID Centro Impresión <span class="text-rose-500">*</span></label>
                    <input type="number" name="printing_center_id" placeholder="Ej. 1" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Ajuste <span class="text-rose-500">*</span></label>
                    <select name="adjustment_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        <option value="Merma">Merma Técnica (Plástico Dañado)</option>
                        <option value="Inventario">Ajuste de Inventario Físico</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Cantidad Plásticos / Cinta <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" placeholder="Ej. -5 o 10" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Ajuste <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Ej. Atasco en rodillo de impresora..." required></textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Registrar Ajuste</button>
            </form>
        </div>

        <!-- Listado de Ajustes -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Ajustes de Suministros</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Insumo</th>
                            <th class="px-4 py-3 text-left">Centro Impresión</th>
                            <th class="px-4 py-3 text-left">Ajuste</th>
                            <th class="px-4 py-3 text-mono text-center">Cant.</th>
                            <th class="px-4 py-3 text-left">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($ajustes as $aj)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $aj->supply?->name ?? 'Insumo' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $aj->printingCenter?->name ?? 'Centro' }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $aj->adjustment_type }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-rose-700">{{ $aj->quantity }}</td>
                                <td class="px-4 py-3 text-slate-500 leading-relaxed">{{ $aj->reason }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se registran ajustes técnicos de impresión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection