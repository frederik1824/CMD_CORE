@extends('layouts.ars')

@section('title', 'Editar Prestadora PSS')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                Editar Prestadora de Servicios de Salud (PSS)
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Modifica los datos generales de contacto y operación de la prestadora en el padrón de la ARS.
            </p>
        </div>
        <a href="{{ route('ars.pss.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-650 bg-white hover:bg-slate-50 transition">
            Volver al Listado
        </a>
    </div>

    <!-- Formulario de Edición (Estilo Google Material 3) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('ars.pss.update', $pss->id) }}" method="POST" class="space-y-5 text-xs font-semibold text-slate-700">
            @csrf
            @method('PUT')

            <!-- Nombre de la Prestadora -->
            <div>
                <label for="nombre" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nombre / Razón Social</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $pss->nombre) }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('nombre') border-red-550 @enderror">
                @error('nombre')
                    <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- RNC y Tipo Entidad (2 Columnas) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rnc" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">RNC (Registro Contribuyente)</label>
                    <input type="text" name="rnc" id="rnc" value="{{ old('rnc', $pss->rnc) }}" required
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('rnc') border-red-550 @enderror">
                    @error('rnc')
                        <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tipo_entidad" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Entidad</label>
                    <select name="tipo_entidad" id="tipo_entidad" required
                            class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                        <option value="Clínica" {{ old('tipo_entidad', $pss->tipo_entidad) === 'Clínica' ? 'selected' : '' }}>Clínica</option>
                        <option value="Centro Médico" {{ old('tipo_entidad', $pss->tipo_entidad) === 'Centro Médico' ? 'selected' : '' }}>Centro Médico</option>
                        <option value="Consultorio" {{ old('tipo_entidad', $pss->tipo_entidad) === 'Consultorio' ? 'selected' : '' }}>Consultorio Médico</option>
                        <option value="Laboratorio" {{ old('tipo_entidad', $pss->tipo_entidad) === 'Laboratorio' ? 'selected' : '' }}>Laboratorio Clínico</option>
                        <option value="Farmacia" {{ old('tipo_entidad', $pss->tipo_entidad) === 'Farmacia' ? 'selected' : '' }}>Farmacia</option>
                    </select>
                </div>
            </div>

            <!-- Teléfono y Correo (2 Columnas) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="telefono" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Teléfono de Contacto</label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $pss->telefono) }}" required
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('telefono') border-red-550 @enderror">
                    @error('telefono')
                        <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="correo" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" value="{{ old('correo', $pss->correo) }}" required
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('correo') border-red-550 @enderror">
                    @error('correo')
                        <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label for="direccion" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dirección Física</label>
                <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $pss->direccion) }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('direccion') border-red-550 @enderror">
                @error('direccion')
                    <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Configuración de Red y Atención (3 Columnas) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label for="nivel_atencion" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nivel de Atención</label>
                    <select name="nivel_atencion" id="nivel_atencion"
                            class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                        <option value="1" {{ old('nivel_atencion', $pss->nivel_atencion) == '1' ? 'selected' : '' }}>Primer Nivel (N1)</option>
                        <option value="2" {{ old('nivel_atencion', $pss->nivel_atencion) == '2' ? 'selected' : '' }}>Segundo Nivel (N2)</option>
                        <option value="3" {{ old('nivel_atencion', $pss->nivel_atencion) == '3' ? 'selected' : '' }}>Tercer Nivel (N3)</option>
                    </select>
                </div>
                <div>
                    <label for="tipo_pss" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Especialidad / Tipo PSS</label>
                    <input type="text" name="tipo_pss" id="tipo_pss" value="{{ old('tipo_pss', $pss->tipo_pss) }}"
                           placeholder="Ej. Clínica, Farmacia, Laboratorio"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white">
                </div>
                <div>
                    <label for="red_contratada" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Red Contratada</label>
                    <select name="red_contratada" id="red_contratada"
                            class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                        <option value="1" {{ old('red_contratada', $pss->red_contratada) == '1' ? 'selected' : '' }}>Red Nacional (1)</option>
                        <option value="2" {{ old('red_contratada', $pss->red_contratada) == '2' ? 'selected' : '' }}>Red Metropolitana (2)</option>
                        <option value="3" {{ old('red_contratada', $pss->red_contratada) == '3' ? 'selected' : '' }}>Red Regional (3)</option>
                    </select>
                </div>
            </div>

            <!-- Detalles del Contrato (3 Columnas) -->
            @php
                $contrato = $pss->contrato_activo ?? $pss->contratos()->latest()->first();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label for="contrato_vigente" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Número de Contrato</label>
                    <input type="text" name="contrato_vigente" id="contrato_vigente" 
                           value="{{ old('contrato_vigente', $contrato ? $contrato->numero_contrato : $pss->contrato_vigente) }}"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-400 bg-white @error('contrato_vigente') border-red-550 @enderror">
                    @error('contrato_vigente')
                        <p class="mt-1 text-[10px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="fecha_inicio" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha Inicio Contrato</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" 
                           value="{{ old('fecha_inicio', $contrato ? ($contrato->fecha_inicio instanceof \Carbon\Carbon ? $contrato->fecha_inicio->format('Y-m-d') : substr($contrato->fecha_inicio, 0, 10)) : '') }}"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha Vencimiento Contrato</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" 
                           value="{{ old('fecha_fin', $contrato ? ($contrato->fecha_fin instanceof \Carbon\Carbon ? $contrato->fecha_fin->format('Y-m-d') : substr($contrato->fecha_fin, 0, 10)) : '') }}"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                </div>
            </div>

            <!-- Estado de Operación -->
            <div class="border-t border-slate-100 pt-4">
                <label for="estado" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Estado de Operación</label>
                <select name="estado" id="estado" required
                        class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                    <option value="Activa" {{ old('estado', $pss->estado) === 'Activa' ? 'selected' : '' }}>Activa (Habilitada para autorizar)</option>
                    <option value="Inactiva" {{ old('estado', $pss->estado) === 'Inactiva' ? 'selected' : '' }}>Inactiva (Acceso suspendido)</option>
                </select>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex justify-end gap-3.5 border-t border-slate-200">
                <a href="{{ route('ars.pss.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-full text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
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
