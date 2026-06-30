@extends('layouts.ars')
@section('title', 'Traspasos Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Traspasos Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de traspasos de afiliados cotizantes aprobados por Unipago.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Historial de Solicitudes de Traspaso</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-center">NSS</th>
                        <th class="px-4 py-3 text-center">Cédula</th>
                        <th class="px-4 py-3 text-left">Tipo Contrato</th>
                        <th class="px-4 py-3 text-center">Estado Traspaso</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($traspasos as $t)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-850">{{ $t->nombres }} {{ $t->primer_apellido }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $t->nss }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $t->cedula }}</td>
                            <td class="px-4 py-3 font-semibold text-blue-900">Traspaso Entrada</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">Confirmado</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="alert('Traspaso verificado de forma exitosa.')" class="bg-slate-100 hover:bg-slate-200 text-[#041e49] rounded-full px-3 py-1 font-bold text-[9px]">Verificar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection