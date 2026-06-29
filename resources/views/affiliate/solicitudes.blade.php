@extends('layouts.affiliate')

@section('title', 'Solicitar Servicio')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto font-sans">
    
    <!-- HEADER CON ICONO -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 shrink-0">
            <span class="material-symbols-outlined text-2xl" data-icon="edit_document">edit_document</span>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800 tracking-tight">Crear Nueva Solicitud de Servicio</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium leading-relaxed">Envía de forma remota tu solicitud de reembolso, inclusión de dependientes o consulta de cobertura especial. El equipo de auditoría revisará tu caso en un plazo menor a 24 horas.</p>
        </div>
    </div>

    <!-- FORMULARIO ESTILO PREMIUM -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm" x-data="{ fileName: '' }">
        <form action="{{ route('affiliate.solicitudes.post') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs font-semibold text-slate-700">
            @csrf
            
            <!-- Campo: Tipo de Solicitud -->
            <div>
                <label for="tipo_solicitud" class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tipo de Solicitud</label>
                <div class="relative">
                    <select id="tipo_solicitud" name="tipo_solicitud" required
                            class="block w-full rounded-xl border border-slate-200 py-3 pl-3 pr-10 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white transition duration-150 appearance-none">
                        <option value="">-- Selecciona el tipo de trámite --</option>
                        <option value="Reembolso de Gastos Médicos">Reembolso de Gastos Médicos (Fuera de Red)</option>
                        <option value="Inclusión de Nuevo Dependiente">Inclusión de Nuevo Dependiente (Matrimonio / Hijo)</option>
                        <option value="Carta de Cobertura Especial">Carta de Cobertura Especial (Estudios Fuera del País)</option>
                        <option value="Corrección de Datos en Padrón">Corrección de Datos en Padrón (Nombres / Cédula)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
                    </div>
                </div>
            </div>

            <!-- Campo: Descripción -->
            <div>
                <label for="descripcion" class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-2">Detalle y Justificación de la Solicitud</label>
                <textarea id="descripcion" name="descripcion" rows="4" required 
                          placeholder="Escribe detalladamente los servicios médicos recibidos, montos a reembolsar y cualquier otra información que consideres de importancia..."
                          class="block w-full rounded-xl border border-slate-200 py-3 px-3.5 text-xs text-slate-855 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white transition duration-150 placeholder:text-slate-400 font-normal leading-relaxed"></textarea>
            </div>

            <!-- Campo: Dropzone Subir Archivos -->
            <div>
                <label class="block text-[10.5px] font-bold text-slate-500 uppercase tracking-wider mb-2">Documento de Soporte (Facturas, Recetas, Actas)</label>
                
                <!-- Input file real pero oculto -->
                <input type="file" name="soporte" id="soporte" x-ref="fileInput" class="hidden" required 
                       @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">

                <!-- Botón de Dropzone visual que dispara el click del input real -->
                <div @click="$refs.fileInput.click()" 
                     class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-slate-200 border-dashed rounded-xl bg-slate-50 hover:bg-slate-100/70 hover:border-blue-400 transition-all duration-200 cursor-pointer group">
                    <div class="space-y-2 text-center">
                        <div class="mx-auto w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 group-hover:scale-110 transition duration-200">
                            <span class="material-symbols-outlined text-lg" data-icon="cloud_upload">cloud_upload</span>
                        </div>
                        <div class="flex text-xs text-slate-600 justify-center">
                            <span class="relative font-bold text-blue-600 hover:text-blue-700 transition">Subir un archivo</span>
                            <p class="pl-1 font-medium">o arrastrar y soltar aquí</p>
                        </div>
                        <p class="text-[9.5px] text-slate-400 font-mono" x-show="!fileName">Archivos permitidos: PDF, PNG, JPG hasta 5MB</p>
                        
                        <!-- Muestra el archivo seleccionado dinámicamente -->
                        <div class="text-xs text-emerald-600 font-bold flex items-center justify-center gap-1.5" x-show="fileName" x-cloak>
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                            <span>Archivo: <span x-text="fileName" class="underline"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold text-xs transition duration-200 shadow-sm hover:shadow-md active:scale-[0.99] tracking-wide">
                    Enviar Solicitud al Core Administrativo
                </button>
            </div>
        </form>
    </div>

    <!-- BANDEJA DE HISTORIAL DE SOLICITUDES -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-blue-600">history</span>
                Historial de Solicitudes de Servicio
            </h3>
            <span class="text-[10px] bg-blue-50 text-blue-700 font-bold px-2.5 py-0.5 rounded-full border border-blue-100">
                {{ $solicitudes->count() }} Solicitudes
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($solicitudes as $sol)
                <div class="p-5 space-y-3.5 hover:bg-slate-50/50 transition duration-150 text-xs text-slate-700 font-medium">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800 text-sm block">{{ $sol->tipo_solicitud }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">Solicitado el: {{ $sol->created_at->format('d/m/Y H:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Badge de estado de la solicitud -->
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ 
                                $sol->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : (
                                $sol->estado === 'Rechazada' ? 'bg-rose-50 text-rose-600 border-rose-100' : (
                                $sol->estado === 'En Revisión' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-amber-50 text-amber-600 border-amber-100'))
                            }}">
                                {{ $sol->estado }}
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 font-normal leading-relaxed bg-slate-50/60 p-3 rounded-xl border border-slate-100">
                        {{ $sol->descripcion }}
                    </p>

                    @if($sol->soporte_path)
                        <div class="flex items-center">
                            <a href="{{ asset($sol->soporte_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                                <span class="material-symbols-outlined text-[16px]">attachment</span>
                                Ver documento soporte adjunto
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 font-normal">
                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-2">find_in_page</span>
                    No has realizado ninguna solicitud de servicio todavía.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
