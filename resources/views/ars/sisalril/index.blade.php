@extends('layouts.ars')

@section('title', 'Esquemas SISALRIL')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Catálogo de Esquemas SISALRIL / SIMON</h2>
            <p class="text-xs text-slate-500 font-medium">Listado detallado de los 15 informes regulatorios exigidos por la Superintendencia de Salud y Riesgos Laborales.</p>
        </div>
    </div>

    <!-- Tabla de Esquemas -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-left">Nombre de Esquema</th>
                        <th class="px-4 py-3 text-left">Módulo Origen</th>
                        <th class="px-4 py-3 text-center">Longitud Registro</th>
                        <th class="px-4 py-3 text-center">Frecuencia</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($esquemas as $e)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-mono font-bold text-[#041e49] text-xs">Esquema {{ $e->schema_code }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-850 max-w-xs truncate">{{ $e->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->module_source }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $e->record_length }} bytes</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">
                                    {{ $e->periodicity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('sisalril.show', $e->schema_code) }}" class="text-[#041e49] font-bold hover:underline">Gestionar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
