@extends('layouts.authorization-portal')

@section('title', 'Dashboard Farmacia')

@section('content')
<div class="space-y-6">
    <!-- Bienvenida / Resumen -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#49bcf7]/5 border border-[#49bcf7]/15 rounded-3xl p-6 gap-4">
        <div>
            <h1 class="text-xl font-black font-rubik text-[#403663]">¡Hola, {{ Auth::user()->name }}!</h1>
            <p class="text-xs text-slate-500 mt-1">
                Canal Operativo: <span class="font-bold text-[#49bcf7] uppercase">Farmacia</span> · Prestadora activa: <span class="font-bold text-[#403663]">{{ $pss->nombre }}</span>.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pss.buscar') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
                <i class="fas fa-user-check mr-1.5"></i> Validar Afiliado
            </a>
            <a href="{{ route('pss.farmacia.nueva_dispensacion') }}" class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
                <i class="fas fa-prescription-bottle-medical mr-1.5"></i> Nueva Dispensación
            </a>
        </div>
    </div>

    <!-- Métrica Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Recetas Recibidas -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-blue-50 text-[#49bcf7] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-file-prescription"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Recetas Recibidas</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['recetas'] }}</span>
            </div>
        </div>

        <!-- Medicamentos Dispensados -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-emerald-50 text-[#0be881] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-capsules"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Dispensaciones</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['dispensaciones'] }}</span>
            </div>
        </div>

        <!-- Pendientes de Autorizacion -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-amber-50 text-[#ffb03a] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pendientes Autorizar</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['pendientes'] }}</span>
            </div>
        </div>

        <!-- Cobertura Total Aprobada -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-505 flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Monto Aprobado ARS</span>
                <span class="text-xl font-black text-[#403663] font-rubik">DOP {{ number_format($metricas['monto_aprobado'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Dispensaciones Recientes -->
    <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6">
        <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-4 mb-4">
            <div>
                <h3 class="text-xs font-black font-rubik text-[#403663] uppercase tracking-wider">Últimas dispensaciones realizadas</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Historial de registros de medicamentos dispensados en farmacia.</p>
            </div>
            <a href="{{ route('pss.farmacia.recetas') }}" class="text-[#49bcf7] hover:text-[#31a3e6] text-xs font-bold transition">Ver todas &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-bold border-b border-[#ecf0f3]">
                        <th class="py-3 px-4">No. Dispensación</th>
                        <th class="py-3 px-4">Afiliado</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4 text-right">Total Solicitado</th>
                        <th class="py-3 px-4 text-right">Aprobado ARS</th>
                        <th class="py-3 px-4 text-right">Copago</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecf0f3] text-slate-700">
                    @forelse($ultimasDispensaciones as $disp)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $disp->dispensation_number }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#403663]">{{ $disp->afiliado->nombre_completo }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $disp->afiliado->cedula }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($disp->dispensed_at)->format('d/m/Y h:i A') }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold">DOP {{ number_format($disp->total_amount, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600">DOP {{ number_format($disp->ars_amount, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">DOP {{ number_format($disp->affiliate_copay_amount, 2) }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($disp->status === 'Dispensada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Dispensada</span>
                                @elseif($disp->status === 'En auditoría')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">En Auditoría</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600">{{ $disp->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 font-bold">No se han registrado dispensaciones recientemente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
