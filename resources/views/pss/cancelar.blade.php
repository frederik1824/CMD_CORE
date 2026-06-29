@extends('layouts.pss')

@section('title', 'Cancelar Autorización')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
    <!-- Encabezado -->
    <div class="pb-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#1f1f1f]">Cancelar Autorización Médica</h2>
        <p class="mt-1.5 text-sm text-[#5f6368]">Módulo de anulación de autorizaciones. Ingrese el número de autorización para procesar su cancelación.</p>
    </div>

    <!-- Mensajes Flash de Error / Éxito Locales -->
    @if(isset($error) || session('error'))
        <div class="p-4 bg-[#fce8e6] border border-[#fad2cf] rounded-2xl flex items-start space-x-3 shadow-sm text-sm text-[#c5221f]">
            <svg class="w-5 h-5 text-[#c5221f] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold">Error de Operación</p>
                <p class="mt-0.5 text-xs text-[#5f6368]">{{ $error ?? session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Card de Búsqueda -->
    <div class="bg-white p-6 rounded-3xl border border-[#e0e0e0] shadow-sm space-y-4">
        <h3 class="text-sm font-semibold text-[#1f1f1f] flex items-center space-x-2 pb-2 border-b border-[#f1f3f4]">
            <svg class="w-5 h-5 text-[#0b57d0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Buscar por Número de Autorización</span>
        </h3>
        
        <form action="{{ route('pss.autorizaciones.cancelar_buscar') }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
            @csrf
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#5f6368]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <input type="text" name="numero_autorizacion" value="{{ $numero ?? '' }}" required
                       class="block w-full rounded-full border border-[#dcdcdc] bg-[#f1f3f4]/50 pl-11 pr-5 py-3 text-sm text-[#1f1f1f] focus:bg-white focus:outline-none focus:border-[#0b57d0] focus:ring-1 focus:ring-[#0b57d0] transition-all placeholder-[#5f6368]/60"
                       placeholder="Ej: AUT-20260626-00001">
            </div>
            <button type="submit"
                    class="px-6 py-3 rounded-full text-sm font-medium text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 hover:shadow-md active:scale-98 transition duration-150 flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Buscar</span>
            </button>
        </form>
    </div>

    <!-- Si se ha encontrado una autorización -->
    @if(isset($autorizacion))
        <div class="bg-white border border-[#e0e0e0] rounded-3xl shadow-sm overflow-hidden animate-fade-in-up">
            <!-- Cabecera de Ficha -->
            <div class="px-6 py-5 border-b border-[#f1f3f4] bg-[#f8f9fa] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <span class="text-xs font-semibold text-[#5f6368] uppercase tracking-wider block mb-1">Código de Autorización</span>
                    <h3 class="text-lg font-mono font-bold text-[#1f1f1f]">{{ $autorizacion->numero_autorizacion }}</h3>
                </div>
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium border {{ 
                    $autorizacion->estado === 'Aprobada' ? 'bg-[#e6f4ea] text-[#137333] border-[#ceead6]' : (
                    $autorizacion->estado === 'Rechazada' ? 'bg-[#fce8e6] text-[#c5221f] border-[#fad2cf]' : (
                    $autorizacion->estado === 'Cancelada' ? 'bg-[#f1f3f4] text-[#3c4043] border-[#e0e0e0]' : (
                    $autorizacion->estado === 'Auditoría' ? 'bg-[#f3e8ff] text-[#6b21a8] border-[#e9d5ff]' : 'bg-[#fef7e0] text-[#b06000] border-[#feebc8]')))
                }}">{{ $autorizacion->estado }}</span>
            </div>

            <!-- Ficha de Datos Grid -->
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm border-b border-[#f1f3f4]">
                <div class="bg-[#f8f9fa] p-4 rounded-2xl border border-[#f1f3f4]">
                    <span class="text-xs font-semibold text-[#5f6368] uppercase tracking-wider block mb-1">Nombre Afiliado</span>
                    <h4 class="text-base font-semibold text-[#1f1f1f]">{{ $afiliado ? $afiliado->nombre_completo : 'Desconocido' }}</h4>
                    <span class="text-[#5f6368] block font-mono mt-1 text-xs">Cédula: {{ $afiliado ? $afiliado->cedula : 'N/A' }}</span>
                </div>
                <div class="bg-[#f8f9fa] p-4 rounded-2xl border border-[#f1f3f4]">
                    <span class="text-xs font-semibold text-[#5f6368] uppercase tracking-wider block mb-1">Información Médica</span>
                    <h4 class="text-base font-semibold text-[#1f1f1f]">{{ $autorizacion->diagnostico }}</h4>
                    <span class="text-[#5f6368] block mt-1 text-xs">Fecha de Solicitud: {{ $autorizacion->fecha_solicitud->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>

            <!-- Tabla de Servicios Incluidos -->
            <div class="p-6 space-y-4">
                <span class="text-xs font-semibold text-[#1f1f1f] uppercase tracking-wider block">Servicios Médicos Incluidos</span>
                <div class="border border-[#e0e0e0] rounded-2xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-[#e0e0e0] text-sm">
                        <thead class="bg-[#f8f9fa] font-semibold text-[#5f6368]">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs uppercase tracking-wider">Código</th>
                                <th class="px-5 py-3 text-left text-xs uppercase tracking-wider">Servicio</th>
                                <th class="px-5 py-3 text-right w-36 text-xs uppercase tracking-wider">Monto</th>
                                <th class="px-5 py-3 text-center w-32 text-xs uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e0e0e0] bg-white">
                            @foreach($autorizacion->detalles as $det)
                                <tr class="hover:bg-[#f8f9fa] transition duration-150">
                                    <td class="px-5 py-4 font-mono font-semibold text-[#5f6368]">{{ $det->codigo }}</td>
                                    <td class="px-5 py-4 text-[#1f1f1f] font-medium">{{ $det->descripcion }}</td>
                                    <td class="px-5 py-4 text-right font-semibold text-[#1f1f1f] font-mono">${{ number_format($det->monto, 2) }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium border {{ 
                                            $det->estado === 'Aprobado' ? 'bg-[#e6f4ea] text-[#137333] border-[#ceead6]' : (
                                            $det->estado === 'Rechazado' ? 'bg-[#fce8e6] text-[#c5221f] border-[#fad2cf]' : (
                                            $det->estado === 'Cancelado' ? 'bg-[#f1f3f4] text-[#3c4043] border-[#e0e0e0]' : 'bg-[#fef7e0] text-[#b06000] border-[#feebc8]'))
                                        }}">{{ $det->estado }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-[#f8f9fa] font-bold text-right">
                                <td colspan="2" class="px-5 py-4 uppercase text-xs text-[#5f6368] font-semibold text-left">Monto Solicitado Total</td>
                                <td class="px-5 py-4 text-[#1f1f1f] font-mono text-base">${{ number_format($autorizacion->monto_solicitado, 2) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de Cancelación (Si aplica) -->
            @if(!in_array($autorizacion->estado, ['Cancelada', 'Rechazada']))
                <div class="p-6 bg-[#f8f9fa] border-t border-[#f1f3f4] space-y-4">
                    <form action="{{ route('pss.autorizaciones.cancelar_procesar', $autorizacion->id) }}" method="POST" class="space-y-4 text-sm">
                        @csrf
                        <div>
                            <label for="motivo_cancelacion" class="block font-semibold text-[#5f6368] mb-2 uppercase tracking-wider text-xs">Motivo de Cancelación <span class="text-rose-500">*</span></label>
                            <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3" required minlength="5"
                                      class="block w-full rounded-2xl border border-[#dcdcdc] p-4 text-sm text-[#1f1f1f] bg-white focus:ring-1 focus:ring-[#c5221f] focus:border-[#c5221f] focus:outline-none placeholder-[#5f6368]/60 transition"
                                      placeholder="Describa brevemente el motivo por el cual cancela esta solicitud de servicios médicos..."></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" onclick="return confirm('¿Está seguro de que desea cancelar definitivamente esta autorización?')"
                                    class="px-6 py-3 rounded-full shadow-sm text-sm font-semibold text-white bg-[#c5221f] hover:bg-[#b31b1b] hover:shadow-md transition active:scale-98 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Anular Autorización</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-6 bg-[#f1f3f4]/50 border-t border-[#e0e0e0] text-center text-sm text-[#5f6368] font-medium leading-relaxed">
                    Esta autorización no puede ser modificada ni cancelada debido a que su estado actual es <span class="font-bold text-[#1f1f1f]">{{ $autorizacion->estado }}</span>.
                    @if($autorizacion->motivo_estado)
                        <p class="mt-2 font-normal text-[#5f6368] italic bg-white p-3 rounded-xl border border-[#e0e0e0] max-w-lg mx-auto">"{{ $autorizacion->motivo_estado }}"</p>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
