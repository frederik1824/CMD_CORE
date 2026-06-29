@extends('layouts.authorization-portal')

@section('title', 'Recetas Médicas')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-4">
        <div>
            <h1 class="text-xl font-black font-rubik text-[#403663] uppercase tracking-wide">Recetas & Prescripciones</h1>
            <p class="text-xs text-slate-400 mt-0.5">Listado histórico de recetas médicas registradas por la farmacia.</p>
        </div>
        <a href="{{ route('pss.farmacia.nueva_dispensacion') }}" class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
            <i class="fas fa-plus mr-1.5"></i> Nueva Dispensación
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-bold border-b border-[#ecf0f3]">
                        <th class="py-3 px-4">No. Receta</th>
                        <th class="py-3 px-4">Afiliado</th>
                        <th class="py-3 px-4">Fecha Receta</th>
                        <th class="py-3 px-4">Médico Prescriptor</th>
                        <th class="py-3 px-4">Diagnóstico</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecf0f3] text-slate-700">
                    @forelse($recetas as $rx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $rx->prescription_number }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#403663]">{{ $rx->afiliado->nombre_completo }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $rx->afiliado->cedula }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($rx->prescription_date)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-650">{{ $rx->doctor_name }} <span class="text-[10px] text-slate-400 font-normal">Ex. {{ $rx->doctor_exequatur }}</span></td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">{{ $rx->diagnosis }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($rx->status === 'Validada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Validada</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-bold">No se han registrado recetas en el portal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $recetas->links() }}
        </div>
    </div>
</div>
@endsection
