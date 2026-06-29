@extends('layouts.authorization-portal')

@section('title', 'Dashboard Laboratorio')

@section('content')
<div class="space-y-6">
    <!-- Bienvenida / Resumen -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#49bcf7]/5 border border-[#49bcf7]/15 rounded-3xl p-6 gap-4">
        <div>
            <h1 class="text-xl font-black font-rubik text-[#403663]">¡Hola, {{ Auth::user()->name }}!</h1>
            <p class="text-xs text-slate-505 mt-1">
                Canal Operativo: <span class="font-bold text-[#49bcf7] uppercase">Laboratorio</span> · Prestadora activa: <span class="font-bold text-[#403663]">{{ $pss->nombre }}</span>.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pss.buscar') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
                <i class="fas fa-user-check mr-1.5"></i> Validar Afiliado
            </a>
            <a href="{{ route('pss.laboratorio.nueva_orden') }}" class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-xs px-4 py-2.5 rounded-full transition shadow-sm">
                <i class="fas fa-microscope mr-1.5"></i> Nueva Orden / Prueba
            </a>
        </div>
    </div>

    <!-- Métrica Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Ordenes Recibidas -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-blue-50 text-[#49bcf7] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-vial"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Órdenes Clínicas</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['ordenes'] }}</span>
            </div>
        </div>

        <!-- Pruebas Realizadas -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-emerald-50 text-[#0be881] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pruebas Realizadas</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['pruebas_realizadas'] }}</span>
            </div>
        </div>

        <!-- Pruebas Pendientes -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-amber-50 text-[#ffb03a] flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pendientes Autorizar</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['pendientes'] }}</span>
            </div>
        </div>

        <!-- Resultados Subidos -->
        <div class="bg-white border border-[#ecf0f3] rounded-2xl p-5 shadow-xs flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-505 flex items-center justify-center text-sm shrink-0">
                <i class="fas fa-file-medical"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Resultados Cargados</span>
                <span class="text-xl font-black text-[#403663] font-rubik">{{ $metricas['resultados'] }}</span>
            </div>
        </div>
    </div>

    <!-- Ordenes Recientes -->
    <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6">
        <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-4 mb-4">
            <div>
                <h3 class="text-xs font-black font-rubik text-[#403663] uppercase tracking-wider">Últimas órdenes clínicas recibidas</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Historial de órdenes de pruebas de laboratorio diagnósticas.</p>
            </div>
            <a href="{{ route('pss.laboratorio.ordenes') }}" class="text-[#49bcf7] hover:text-[#31a3e6] text-xs font-bold transition">Ver todas &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-bold border-b border-[#ecf0f3]">
                        <th class="py-3 px-4">No. Orden</th>
                        <th class="py-3 px-4">Afiliado</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Médico</th>
                        <th class="py-3 px-4">Diagnóstico</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecf0f3] text-slate-700">
                    @forelse($ultimasOrdenes as $ord)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $ord->order_number }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#403663]">{{ $ord->afiliado->nombre_completo }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $ord->afiliado->cedula }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($ord->order_date)->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-650">{{ $ord->doctor_name }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">{{ $ord->diagnosis }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($ord->status === 'Orden recibida')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Recibida</span>
                                @elseif($ord->status === 'Realizada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Completada</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600">{{ $ord->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-bold">No se han registrado órdenes clínicas recientemente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
