@extends('layouts.ars')

@section('title', 'Impresión de Carnets')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consola de Impresión de Carnets</h2>
            <p class="text-xs text-slate-500 font-medium">Buzón de procesamiento por lotes de carnets físicos aprobados para impresión y distribución.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Lote de Impresión -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Cola de Impresión Activa</h3>
        <form action="{{ route('ars.carnetizacion.procesar_impresion') }}" method="POST" class="space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">
                                <input type="checkbox" onclick="toggleSelectAll(this)" class="rounded text-[#041e49] focus:ring-blue-100">
                            </th>
                            <th class="px-4 py-3 text-left">Solicitante</th>
                            <th class="px-4 py-3 text-left">Fecha Solicitud</th>
                            <th class="px-4 py-3 text-left">Motivo Pedido</th>
                            <th class="px-4 py-3 text-left">Centro de Impresión</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($solicitudes as $s)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="request_ids[]" value="{{ $s->id }}" class="request-checkbox rounded text-[#041e49] focus:ring-blue-100">
                                </td>
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $s->affiliate?->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $s->request_date }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $s->request_type }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $s->printingCenter?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-bold text-amber-700 border border-amber-200">
                                        {{ $s->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-semibold">No hay solicitudes en la cola de impresión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($solicitudes->isNotEmpty())
                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-[#041e49] hover:bg-slate-800 text-white rounded-full px-6 py-2.5 font-bold shadow-xs transition text-xs flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">print</span>
                        Procesar Impresión Seleccionada
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
    }
</script>
@endsection