@extends('layouts.core')

@section('title', 'Reembolsos Excepcionales de Afiliados')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0b57d0]/10 text-[#0b57d0] border border-[#0b57d0]/20 uppercase tracking-wider">
                    📄 Procedimiento Excepcional
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reembolsos de Afiliados</h2>
            <p class="text-xs text-slate-500 font-medium">Gestión de expedientes de reembolso por negación de cobertura o cobro indebido (Resolución RA 251_2023).</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.reembolsos.create') }}" class="bg-[#0b57d0] text-white rounded-full px-5 py-2.5 text-xs font-bold hover:bg-[#083d91] transition shadow-sm">
                + Nueva Solicitud
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs">
            <span class="font-bold text-slate-400 uppercase tracking-wider">Filtro por Estado:</span>
            <a href="{{ route('ars.reembolsos.index') }}" class="px-3.5 py-1.5 rounded-full font-bold {{ !$status ? 'bg-[#c2e7ff] text-[#041e49]' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }} transition">
                Todos
            </a>
            @foreach(['Recibido', 'Expediente completo', 'En revisión', 'Aprobado', 'Rechazado'] as $est)
                <a href="{{ route('ars.reembolsos.index', ['status' => $est]) }}" class="px-3.5 py-1.5 rounded-full font-bold {{ $status === $est ? 'bg-[#c2e7ff] text-[#041e49]' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }} transition">
                    {{ $est }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Bandeja de Reembolsos -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">No. Caso</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Afiliado</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tipo Solicitud</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha Servicio</th>
                        <th class="px-6 py-3.5 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Monto Solicitado</th>
                        <th class="px-6 py-3.5 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Monto Aprobado</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Vence En</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3.5 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($cases as $case)
                        @php
                            $badgeColor = match($case->status) {
                                'Recibido' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'Expediente completo' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'Aprobado' => 'bg-teal-50 text-teal-700 border-teal-100',
                                'Rechazado' => 'bg-rose-50 text-rose-700 border-rose-100',
                                default => 'bg-slate-50 text-slate-500 border-slate-100'
                            };

                            $due = \Carbon\Carbon::parse($case->response_due_date);
                            $daysLeft = now()->diffInDays($due, false);
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono text-slate-700 whitespace-nowrap font-bold">
                                {{ $case->case_number }}
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                {{ $case->afiliado->nombre_completo ?? 'N/D' }}
                                <span class="text-[9px] text-slate-400 block font-mono">Ced: {{ $case->afiliado->cedula ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-600">
                                @if($case->request_type === 'cobro_indebido')
                                    💸 Cobro Indebido PSS
                                @elseif($case->request_type === 'negacion_cobertura')
                                    🛡️ Negación de Cobertura
                                @else
                                    🔄 Ambas
                                @endif
                                <span class="text-[9px] text-slate-400 block font-medium">Origen: {{ strtoupper($case->origin) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap font-mono">
                                {{ $case->service_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-700">
                                DOP {{ number_format($case->requested_amount, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-emerald-700">
                                DOP {{ number_format($case->approved_amount, 2) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($case->status === 'Aprobado' || $case->status === 'Rechazado' || $case->status === 'Cerrado')
                                    <span class="text-slate-300 font-medium">Finalizado</span>
                                @else
                                    <span class="font-mono font-bold {{ $daysLeft <= 2 ? 'text-rose-600' : 'text-slate-600' }}">
                                        {{ $daysLeft }} días hábiles
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold border {{ $badgeColor }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <a href="{{ route('ars.reembolsos.show', $case->id) }}" class="text-[#0b57d0] font-bold hover:underline">
                                    Gestionar Caso →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">receipt</span>
                                <p class="text-sm font-semibold">No se encontraron expedientes de reembolsos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cases->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $cases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
