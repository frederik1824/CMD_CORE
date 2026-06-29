@extends('layouts.pss')

@section('title', 'Mis Solicitudes')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="pb-2">
        <h2 class="text-xl font-bold tracking-tight text-slate-800">Mis Solicitudes de Autorización</h2>
        <p class="mt-1 text-xs text-slate-500">Historial completo de autorizaciones médicas tramitadas ante la ARS por esta prestadora.</p>
    </div>

    <!-- Panel de Autorizaciones Recién Creadas para Impresión Directa -->
    @if(session('created_aut_ids'))
        @php
            $nuevasAuts = \App\Models\Autorizacion::whereIn('id', session('created_aut_ids'))->with(['servicio', 'servicioPdss'])->get();
        @endphp
        @if($nuevasAuts->count() > 0)
            <div class="bg-gradient-to-r from-teal-50/50 to-emerald-50/50 border border-emerald-150 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 text-emerald-800">
                    <div class="bg-emerald-600 text-white p-2 rounded-full shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">¡Autorizaciones Generadas Exitosamente!</h3>
                        <p class="text-xs text-emerald-600 font-medium">Se han procesado los servicios médicos solicitados. Puede imprimir los volantes de autorización con su desglose a continuación:</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($nuevasAuts as $newAut)
                        <div class="bg-white border border-slate-100 rounded-xl p-4 flex items-center justify-between shadow-xs hover:shadow-sm transition">
                            <div class="space-y-1">
                                <span class="font-mono font-bold text-xs text-slate-800 block">{{ $newAut->numero_autorizacion }}</span>
                                <span class="text-xs text-slate-600 font-medium block">
                                    {{ optional($newAut->servicio)->descripcion ?? optional($newAut->servicioPdss)->coverage_description ?? $newAut->procedimiento ?? '—' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold tracking-wide border {{ 
                                    $newAut->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (
                                    $newAut->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-200' : (
                                    $newAut->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-amber-50 text-amber-700 border-amber-200'))
                                }}">{{ $newAut->estado }}</span>
                            </div>
                            <div>
                                <a href="{{ route('pss.autorizaciones.imprimir', $newAut->id) }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 rounded-full shadow-sm text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md transition active:scale-95">
                                    <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Imprimir Volante
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Barra de Filtros -->
    <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-100">
        <form action="{{ route('pss.solicitudes') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end max-w-lg">
            <div class="flex-1 w-full">
                <label for="estado" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 font-semibold">Filtrar por Estado</label>
                <select name="estado" id="estado" class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 py-2.5 px-4 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all duration-150">
                    <option value="">Todos los estados</option>
                    <option value="Aprobada" {{ $estado === 'Aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="Rechazada" {{ $estado === 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="Auditoría" {{ $estado === 'Auditoría' ? 'selected' : '' }}>Auditoría Médica</option>
                    <option value="Pendiente Documento" {{ $estado === 'Pendiente Documento' ? 'selected' : '' }}>Pendiente Documento</option>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-5 py-2.5 rounded-full shadow-xs text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md transition">
                    Filtrar
                </button>
                <a href="{{ route('pss.solicitudes') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-5 py-2.5 border border-slate-200 rounded-full text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla Solicitudes -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Autorización</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Afiliado</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Servicio Solicitado</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Monto Sol. / Aprob.</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Motivo Respuesta</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fecha Envío</th>
                        <th scope="col" class="px-6 py-4.5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @forelse($solicitudes as $sol)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4.5 whitespace-nowrap font-mono font-bold text-slate-800">
                                {{ $sol->numero_autorizacion }}
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap font-bold text-slate-800">
                                {{ $sol->afiliado ? $sol->afiliado->nombre_completo : 'Desconocido' }}
                                <span class="text-[10px] text-slate-400 block font-mono">Ced: {{ $sol->afiliado ? $sol->afiliado->cedula : 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap text-slate-600 font-semibold">
                                {{ optional($sol->servicio)->descripcion ?? optional($sol->servicioPdss)->coverage_description ?? $sol->procedimiento ?? '—' }}
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap font-mono">
                                Sol: <span class="font-bold text-slate-700">${{ number_format($sol->monto_solicitado, 2) }}</span><br>
                                Apr: <span class="font-bold text-emerald-600">${{ number_format($sol->monto_contratado, 2) }}</span>
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ 
                                    $sol->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : (
                                    $sol->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-200' : (
                                    $sol->estado === 'Cancelada' ? 'bg-slate-50 text-slate-600 border-slate-200' : (
                                    $sol->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-amber-50 text-amber-700 border-amber-250')))
                                }}">{{ $sol->estado }}</span>
                            </td>
                            <td class="px-6 py-4.5 text-slate-400 max-w-xs leading-normal">
                                {{ $sol->motivo_estado ?? 'Solicitud en revisión en bandeja de autorizaciones.' }}
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap text-slate-400 font-mono">
                                {{ $sol->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap text-center">
                                <a href="{{ route('pss.autorizaciones.imprimir', $sol->id) }}" target="_blank" 
                                   class="inline-flex items-center px-3.5 py-1.5 border border-slate-200 rounded-full text-[10px] font-bold text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-350 hover:text-slate-800 transition shadow-xs">
                                    <svg class="mr-1 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Imprimir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">No se han registrado solicitudes bajo este filtro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($solicitudes->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $solicitudes->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
