@extends('layouts.ars')

@section('title', 'Editar Servicio Médico')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                Editar Servicio Médico / Cobertura
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Modifica los parámetros de cobertura base, estados de alto costo y requerimientos del servicio médico.
            </p>
        </div>
        <a href="{{ route('ars.pss.servicios') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-650 bg-white hover:bg-slate-50 transition">
            Volver al Catálogo
        </a>
    </div>

    <!-- Formulario de Edición (Estilo Google Material 3) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('ars.pss.servicios.update', $servicio->id) }}" method="POST" class="space-y-5 text-xs font-semibold text-slate-700">
            @csrf
            @method('PUT')

            <!-- Código de Servicio -->
            <div>
                <label for="codigo" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Código del Servicio</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $servicio->codigo) }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('codigo') border-red-550 @enderror">
                @error('codigo')
                    <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Descripción del Procedimiento o Servicio</label>
                <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $servicio->descripcion) }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('descripcion') border-red-550 @enderror">
                @error('descripcion')
                    <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cobertura Base -->
            <div>
                <label for="cobertura_base" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Porcentaje de Cobertura Base (%)</label>
                <input type="number" name="cobertura_base" id="cobertura_base" min="0" max="100" step="1" value="{{ old('cobertura_base', round($servicio->cobertura_base)) }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('cobertura_base') border-red-550 @enderror">
                @error('cobertura_base')
                    <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parámetros Clínicos (Alto Costo y Receta) -->
            <div class="space-y-3.5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Parámetros Especiales</span>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="es_alto_costo" id="es_alto_costo" value="1" {{ old('es_alto_costo', $servicio->es_alto_costo) ? 'checked' : '' }}
                           class="rounded text-brand-600 border-slate-300 focus:ring-brand-500 w-4 h-4">
                    <label for="es_alto_costo" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                        Marcar como servicio de **Alto Costo** (Requiere auditoría médica y validación de topes especiales)
                    </label>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-200 pt-3">
                    <input type="checkbox" name="requiere_documento" id="requiere_documento" value="1" {{ old('requiere_documento', $servicio->requiere_documento) ? 'checked' : '' }}
                           class="rounded text-brand-600 border-slate-300 focus:ring-brand-500 w-4 h-4">
                    <label for="requiere_documento" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                        Requiere subir **Receta / Indicación médica** obligatoriamente en el portal PSS
                    </label>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex justify-end gap-3.5 border-t border-slate-200">
                <a href="{{ route('ars.pss.servicios') }}" class="px-5 py-2.5 border border-slate-300 rounded-full text-xs font-semibold text-slate-650 bg-white hover:bg-slate-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-full text-white font-bold text-xs bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
