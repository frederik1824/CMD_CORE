@extends('layouts.core')

@section('title', 'Logs API Mock Unipago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Logs de Peticiones API Unipago (Mock)</h2>
            <p class="text-xs text-slate-500 font-medium">Historial de llamadas transmitidas y procesadas por los adaptadores y simuladores internos de Unipago/Unisigma.</p>
        </div>
        <a href="{{ route('ars.unipago.dashboard') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la central
        </a>
    </div>

    <!-- Tabla Logs -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left w-20 text-[9px] uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider w-36">Servicio</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider w-44">Endpoint Mock</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Payload Solicitud (JSON)</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Respuesta Servidor</th>
                        <th class="px-4 py-3 text-center text-[9px] uppercase tracking-wider w-24">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-mono text-slate-650">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 text-slate-400">{{ $log->id }}</td>
                            <td class="px-4 py-3 font-sans font-bold text-slate-700">
                                {{ $log->service_name }}
                                <span class="block font-mono text-[9px] text-slate-400 font-normal">{{ $log->service_code }}</span>
                            </td>
                            <td class="px-4 py-3 text-blue-800 font-bold">{{ $log->endpoint_mock }}</td>
                            <td class="px-4 py-3 max-w-xs overflow-hidden text-ellipsis whitespace-nowrap text-[10px] text-slate-500">
                                {{ json_encode($log->request_payload) }}
                            </td>
                            <td class="px-4 py-3 max-w-xs overflow-hidden text-ellipsis whitespace-nowrap text-[10px] text-teal-600 font-bold">
                                {{ json_encode($log->response_payload) }}
                            </td>
                            <td class="px-4 py-3 text-center font-sans text-slate-500 text-[10px]">
                                {{ $log->processed_at->format('d/m H:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center font-sans text-slate-400 font-medium">
                                No se registran llamadas API en los logs del simulador.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
