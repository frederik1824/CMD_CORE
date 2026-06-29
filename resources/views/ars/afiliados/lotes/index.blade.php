@extends('layouts.ars')

@section('title', 'Lotes de Transmisión')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Lotes de Transmisión Unipago
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Monitoreo de archivos XML/TXT simulados de afiliación y novedades enviados para validación ante TSS.
            </p>
        </div>
    </div>

    <!-- Tabla de Lotes -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Número de Lote</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tipo de Lote</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Total Registros</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Procesados OK</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Rechazados</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Creado Por</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Fecha Envío</th>
                        <th scope="col" class="relative px-6 py-4 text-right">
                            <span class="sr-only">Ver</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-slate-800">
                                <a href="{{ route('ars.lotes.show', $lote->id) }}" class="hover:text-brand-600 transition">{{ $lote->numero_lote }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-600 capitalize">
                                {{ str_replace('_', ' ', $lote->tipo_lote) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                                {{ $lote->total_registros }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                                {{ $lote->registros_ok }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-rose-600">
                                {{ $lote->registros_re }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ 
                                    $lote->estado_lote === 'EV' ? 'bg-emerald-50 text-emerald-700' : (
                                    $lote->estado_lote === 'PC' ? 'bg-blue-50 text-blue-700 animate-pulse' : (
                                    $lote->estado_lote === 'PE' ? 'bg-amber-50 text-amber-700' : (
                                    $lote->estado_lote === 'RE' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600')))
                                }}">
                                    {{ $lote->estado_lote === 'EV' ? 'Procesado OK' : ($lote->estado_lote === 'PC' ? 'Procesando' : ($lote->estado_lote === 'PE' ? 'Procesado con Errores' : ($lote->estado_lote === 'RE' ? 'Rechazado' : 'Espera (VE)'))) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $lote->creador->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400 font-mono">
                                {{ $lote->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                <a href="{{ route('ars.lotes.show', $lote->id) }}" class="text-brand-600 hover:text-brand-800 transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle de Lote">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400 text-sm">No hay lotes de transmisión registrados en el sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($lotes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $lotes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
