@extends('layouts.ars')
@section('title', 'Consulta de Procesos Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consulta de Procesos Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Log general de peticiones API y Web Services consumidos desde el core.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Llamadas de Servicios Mock</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 font-mono text-[11px]">
                <thead class="bg-slate-50 font-bold text-slate-400 font-sans">
                    <tr>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-left">Endpoint</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Fecha/Hora Consumo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($logs as $log)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-blue-900 block font-sans">{{ $log->service_name }}</span>
                                <span class="text-[9px] text-slate-400 font-normal">Código: {{ $log->service_code }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-650">{{ $log->endpoint_mock }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200 font-sans">{{ $log->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-450">{{ $log->created_at->format('d/m Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="py-2">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection