@extends('layouts.ars')

@section('title', 'Bandeja de Auditoría Médica')

@section('content')
<div class="space-y-6">
    <!-- Encabezado de la Bandeja -->
    <div class="pb-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Bandeja de Auditoría Médica</h2>
            <p class="text-xs text-slate-400 font-medium">Solicitudes que requieren revisión de juntas médicas u homologación por alta complejidad.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-[10px] font-black text-purple-700 bg-purple-50 border border-purple-250 px-3 py-1 rounded-full uppercase tracking-wider font-mono flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-ping"></span>
                <span>Dictamen Médico Requerido</span>
            </span>
        </div>
    </div>

    <!-- Lista de Auditoría -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">Prioridad (Triage)</th>
                        <th scope="col" class="px-6 py-4 text-left">No. Caso</th>
                        <th scope="col" class="px-6 py-4 text-left">Afiliado</th>
                        <th scope="col" class="px-6 py-4 text-left">Prestador PSS</th>
                        <th scope="col" class="px-6 py-4 text-left">Procedimiento</th>
                        <th scope="col" class="px-6 py-4 text-right">Monto</th>
                        <th scope="col" class="px-6 py-4 text-center">SLA Transcurrido</th>
                        <th scope="col" class="px-6 py-4 text-center w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                    @forelse($autorizaciones as $a)
                        @php
                            $diffMins = $a->fecha_solicitud->diffInMinutes(now());
                            $isOverSla = $a->prioridad === 'Alta' ? ($diffMins > 15) : ($diffMins > 120);
                            $priorityStyle = match($a->prioridad) {
                                'Alta' => ['bg-rose-50 text-rose-700 border-rose-250', '🔴 URGENTE'],
                                'Media' => ['bg-amber-50 text-amber-700 border-amber-250', '🟡 MEDIA'],
                                default => ['bg-blue-50 text-blue-700 border-blue-200', '🔵 BAJA']
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <!-- Columna Triage -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[8px] font-black border uppercase tracking-wider {{ $priorityStyle[0] }}">
                                    {{ $priorityStyle[1] }}
                                </span>
                            </td>
                            <!-- No. Caso -->
                            <td class="px-6 py-4 font-mono font-extrabold text-slate-800 text-[11px]">
                                {{ $a->numero_autorizacion }}
                            </td>
                            <!-- Afiliado -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block text-[11px]">{{ $a->afiliado->nombres }} {{ $a->afiliado->primer_apellido }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">Cédula: {{ $a->afiliado->cedula }}</span>
                            </td>
                            <!-- Prestador -->
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $a->pss->nombre }}
                            </td>
                            <!-- Procedimiento -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-750 block leading-tight text-[11px]">{{ $a->procedimiento ?? 'Procedimiento Especial' }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">CIE-10: {{ $a->diagnostico }}</span>
                            </td>
                            <!-- Monto -->
                            <td class="px-6 py-4 text-right font-bold text-slate-900 font-mono text-xs">
                                DOP {{ number_format($a->monto_solicitado, 2) }}
                            </td>
                            <!-- SLA Transcurrido -->
                            <td class="px-6 py-4 text-center font-mono">
                                @if($isOverSla)
                                    <span class="inline-flex items-center space-x-1 text-rose-600 font-extrabold text-[10px] bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">
                                        <span>⚠️ FUERA SLA:</span>
                                        <span>{{ $a->fecha_solicitud->diffForHumans() }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-500 font-semibold text-[10px]">
                                        {{ $a->fecha_solicitud->diffForHumans() }}
                                    </span>
                                @endif
                            </td>
                            <!-- Acción -->
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ars.autorizaciones_medicas.show', $a->id) }}" 
                                   class="text-purple-700 hover:text-purple-900 font-bold border border-purple-150 hover:border-purple-300 px-3.5 py-1.5 rounded-full bg-white transition shadow-2xs text-[10px] inline-block">
                                    Auditar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-semibold italic">
                                No se registran solicitudes pendientes en la bandeja de auditoría médica.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($autorizaciones->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $autorizaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
