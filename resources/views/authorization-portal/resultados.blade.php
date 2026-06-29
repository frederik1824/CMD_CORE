@extends('layouts.authorization-portal')

@section('title', 'Carga de Resultados de Laboratorio')

@section('content')
<div class="space-y-6">
    <div class="border-b border-[#ecf0f3] pb-4">
        <h1 class="text-xl font-black font-rubik text-[#403663] uppercase tracking-wide">Resultados de Exámenes</h1>
        <p class="text-xs text-slate-400 mt-0.5">Sube los informes de resultados de las pruebas realizadas para completar el expediente clínico.</p>
    </div>

    <!-- Grid de Órdenes -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($ordenes as $ord)
            <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6 shadow-xs space-y-4">
                <!-- Encabezado de la Orden -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-[#ecf0f3] pb-3">
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">No. Orden Clínica</span>
                        <span class="text-sm font-black text-[#403663] font-mono" x-text="'{{ $ord->order_number }}'"></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Afiliado</span>
                        <span class="text-xs font-bold text-slate-700">{{ $ord->afiliado->nombre_completo }} <span class="font-mono text-slate-400 font-normal">({{ $ord->afiliado->cedula }})</span></span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Fecha Orden</span>
                        <span class="text-xs font-semibold text-slate-650">{{ \Carbon\Carbon::parse($ord->order_date)->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Detalle de Pruebas de la Orden -->
                <div class="space-y-2">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Pruebas Asociadas</span>
                    
                    <div class="divide-y divide-[#ecf0f3] border border-[#ecf0f3] rounded-2xl overflow-hidden bg-slate-50/20">
                        @foreach($ord->items as $item)
                            <div class="p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-xs font-semibold">
                                <div>
                                    <span class="text-slate-800 font-bold block">{{ $item->test_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono block">SIMON: {{ $item->simon_code_snapshot }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Estado:</span>
                                    @if($item->status === 'Realizada')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-circle-check mr-1"></i> Resultado Cargado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100 animate-pulse">
                                            <i class="fas fa-clock mr-1"></i> Pendiente
                                        </span>
                                    @endif
                                </div>

                                <!-- Formulario de Carga Directa -->
                                @if($item->status !== 'Realizada')
                                    <form action="{{ route('pss.laboratorio.subir_resultado') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-end gap-2 w-full md:w-auto">
                                        @csrf
                                        <input type="hidden" name="lab_order_id" value="{{ $ord->id }}">
                                        <input type="hidden" name="lab_order_item_id" value="{{ $item->id }}">
                                        
                                        <div class="w-full sm:w-48">
                                            <input type="file" name="resultado_archivo" required
                                                   class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-[#49bcf7] hover:file:bg-blue-100 cursor-pointer">
                                        </div>

                                        <div class="w-full sm:w-48">
                                            <input type="text" name="observaciones" placeholder="Notas (opcional)"
                                                   class="block w-full rounded-full border-slate-200 py-1 px-3 text-[10px] placeholder-slate-400 focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                                        </div>

                                        <button type="submit" class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-[10px] px-4 py-1.5 rounded-full transition shadow-xs">
                                            Subir
                                        </button>
                                    </form>
                                @else
                                    <div class="text-[10px] text-slate-500 font-semibold italic">
                                        Subido correctamente.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#ecf0f3] rounded-3xl p-8 text-center text-slate-400 font-bold">
                No hay órdenes de laboratorio pendientes de resultados en este momento.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $ordenes->links() }}
    </div>
</div>
@endsection
