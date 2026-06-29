@extends('layouts.authorization-portal')

@section('title', 'Órdenes de Laboratorio')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-4">
        <div>
            <h1 class="text-xl font-black font-rubik text-[#403663] uppercase tracking-wide">Órdenes Clínicas</h1>
            <p class="text-xs text-slate-400 mt-0.5">Listado histórico de órdenes y solicitudes de exámenes diagnósticos.</p>
        </div>
        <a href="{{ route('pss.laboratorio.nueva_orden') }}" class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
            <i class="fas fa-plus mr-1.5"></i> Nueva Orden
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-bold border-b border-[#ecf0f3]">
                        <th class="py-3 px-4">No. Orden</th>
                        <th class="py-3 px-4">Afiliado</th>
                        <th class="py-3 px-4">Fecha Orden</th>
                        <th class="py-3 px-4">Médico Solicitante</th>
                        <th class="py-3 px-4">Diagnóstico</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecf0f3] text-slate-700">
                    @forelse($ordenes as $ord)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $ord->order_number }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#403663]">{{ $ord->afiliado->nombre_completo }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $ord->afiliado->cedula }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($ord->order_date)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-650">{{ $ord->doctor_name }} <span class="text-[10px] text-slate-400 font-normal">Ex. {{ $ord->doctor_exequatur }}</span></td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">{{ $ord->diagnosis }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($ord->status === 'Orden recibida')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Recibida</span>
                                @elseif($ord->status === 'Realizada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Completada</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-bold">No se han registrado órdenes en el portal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $ordenes->links() }}
        </div>
    </div>
</div>
@endsection
