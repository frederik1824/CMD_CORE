@extends('layouts.ars')

@section('title', 'Editar Dependiente')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 border-b border-slate-200 pb-5">
        <a href="{{ route('ars.titulares.show', $dependiente->titular_id) }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight">Editar Dependiente: {{ $dependiente->nombre_completo }}</h2>
            <p class="mt-1 text-sm text-slate-500">Actualiza los datos del familiar dependiente.</p>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('ars.dependientes.update', $dependiente->id) }}" method="POST" class="space-y-6 text-xs">
        @csrf
        @method('PUT')
        
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombres -->
                <div>
                    <label for="nombres" class="block font-semibold text-slate-500 mb-1.5">Nombres</label>
                    <input type="text" name="nombres" id="nombres" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" value="{{ $dependiente->nombres }}">
                </div>

                <!-- Apellidos -->
                <div>
                    <label for="apellidos" class="block font-semibold text-slate-500 mb-1.5">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" value="{{ $dependiente->apellidos }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Cédula -->
                <div>
                    <label for="cedula" class="block font-semibold text-slate-500 mb-1.5">Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 font-mono" value="{{ $dependiente->cedula }}">
                </div>

                <!-- NSS -->
                <div>
                    <label for="nss" class="block font-semibold text-slate-500 mb-1.5">NSS</label>
                    <input type="text" name="nss" id="nss" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 font-mono" value="{{ $dependiente->nss }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Opciones Especiales -->
                <div class="flex items-center space-x-6 mt-4">
                    <label class="inline-flex items-center text-xs font-medium text-slate-700">
                        <input type="checkbox" name="estudiante" value="1" {{ $dependiente->estudiante ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-brand-600">
                        <span class="ml-2">Es Estudiante</span>
                    </label>
                    <label class="inline-flex items-center text-xs font-medium text-slate-700">
                        <input type="checkbox" name="discapacitado" value="1" {{ $dependiente->discapacitado ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-brand-600">
                        <span class="ml-2">Posee Discapacidad</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('ars.titulares.show', $dependiente->titular_id) }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
