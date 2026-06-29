@extends('layouts.ars')

@section('title', 'Nuevo Rango de Formularios')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                Crear Nuevo Rango Autorizado
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Registra un rango de números de contrato autorizados y genera de forma automática los números individuales.
            </p>
        </div>
        <a href="{{ route('ars.contract_control.ranges.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-655 bg-white hover:bg-slate-50 transition">
            Volver al Listado
        </a>
    </div>

    <!-- Formulario de Creación (Google Material 3) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('ars.contract_control.ranges.store') }}" method="POST" class="space-y-5 text-xs font-semibold text-slate-700">
            @csrf

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <div class="text-xs font-semibold text-rose-800">
                        <p class="font-bold mb-1">Por favor corrige los siguientes errores de validación:</p>
                        <ul class="list-disc pl-4 space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Código Rango -->
            <div>
                <label for="range_code" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Código Único del Rango</label>
                <input type="text" name="range_code" id="range_code" value="{{ old('range_code') }}" required placeholder="Ej: UNIPAGO-2026-R1"
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-450 bg-white @error('range_code') border-red-550 @enderror">
                @error('range_code')
                    <p class="mt-1 text-[10px] text-red-650 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Descripción del Rango / Observaciones</label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" required placeholder="Ej: Rango autorizado por Sisalril para el segundo semestre"
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-455 bg-white @error('description') border-red-550 @enderror">
                @error('description')
                    <p class="mt-1 text-[10px] text-red-655 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Number y End Number (2 Columnas) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Número de Inicio</label>
                    <input type="number" name="start_number" id="start_number" value="{{ old('start_number') }}" required placeholder="Ej: 450000"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-450 bg-white @error('start_number') border-red-550 @enderror">
                    @error('start_number')
                        <p class="mt-1 text-[10px] text-red-655 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Número de Fin</label>
                    <input type="number" name="end_number" id="end_number" value="{{ old('end_number') }}" required placeholder="Ej: 455000"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-450 bg-white @error('end_number') border-red-550 @enderror">
                    @error('end_number')
                        <p class="mt-1 text-[10px] text-red-655 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Origen e Institución Aprobante -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="source" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Origen / Fuente</label>
                    <select name="source" id="source" required
                            class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                        <option value="unipago">Unipago</option>
                        <option value="sisalril">Sisalril</option>
                        <option value="manual">Manual / Interno</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label for="approval_reference" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Referencia de Aprobación (Resolución/Oficio)</label>
                    <input type="text" name="approval_reference" id="approval_reference" value="{{ old('approval_reference') }}" placeholder="Ej: Oficio No. 058-2026"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-450 bg-white">
                </div>
            </div>

            <!-- Vigencia Rango (2 Columnas) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="valid_from" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Vigente Desde</label>
                    <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from') }}"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white @error('valid_from') border-red-550 @enderror">
                    @error('valid_from')
                        <p class="mt-1 text-[10px] text-red-655 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="valid_until" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Vigente Hasta</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until') }}"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white @error('valid_until') border-red-550 @enderror">
                    @error('valid_until')
                        <p class="mt-1 text-[10px] text-red-655 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex justify-end gap-3.5 border-t border-slate-200">
                <a href="{{ route('ars.contract_control.ranges.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-full text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-full text-white font-bold text-xs bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                    Generar Rango y Números
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
