@extends('layouts.ars')

@section('title', 'Archivos de Novedades')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Archivos de Novedades (TXT Masivos)</h2>
            <p class="text-xs text-slate-500 font-medium">Generación y exportación de archivos planos de novedades para reporte periódico a Unipago.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Generar Archivo -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Generar Nuevo Archivo</h3>
            <form action="{{ route('ars.afiliaciones.guardar_archivo_novedad') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período de Novedades <span class="text-rose-500">*</span></label>
                    <select name="period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        <option value="{{ now()->format('Y-m') }}">{{ now()->format('F Y') }} (Mes en curso)</option>
                        <option value="{{ now()->subMonth()->format('Y-m') }}">{{ now()->subMonth()->format('F Y') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Archivos</label>
                    <select name="file_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                        <option value="TXT">Estructura Plana Unipago (.TXT)</option>
                        <option value="CSV">Valores Separados por Comas (.CSV)</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Generar TXT e Iniciar Descarga</button>
            </form>
        </div>

        <!-- Información Técnica -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Especificaciones de Estructura de Datos</h3>
            <div class="space-y-3 leading-relaxed text-slate-600">
                <p>La ARS procesa las novedades operativas cumpliendo con el formato oficial establecido por la Superintendencia de Salud y Riesgos Laborales (SISALRIL):</p>
                <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl font-mono text-[10px] space-y-1">
                    <p>Header: H|ARS001|2026-07-01|000001</p>
                    <p>Detalle: D|NUI-39828|07900175907|JUAN|PEREZ|ALCANTARA|N|2026-07-01</p>
                    <p>Trailer: T|000001|0000001000.00</p>
                </div>
                <div class="p-4 bg-blue-50 border border-blue-150 text-blue-900 rounded-2xl">
                    <h4 class="font-bold text-xs mb-1">Nota sobre Procesamiento Masivo:</h4>
                    <p class="text-xs">Los archivos generados son almacenados en el storage seguro y pueden ser re-descargados desde las bitácoras de auditoría de novedades.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
