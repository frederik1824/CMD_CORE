@extends('layouts.ars')

@section('title', 'Servicios Médicos')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Catálogo de Servicios Médicos
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Catálogo general de consultas, laboratorios, imágenes, cirugías y coberturas autorizadas.
            </p>
        </div>
    </div>

    <!-- Barra de Filtro -->
    <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-200">
        <form action="{{ route('ars.pss.servicios') }}" method="GET" class="flex gap-4 items-end max-w-md">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Buscar Servicio</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none" placeholder="Código o descripción...">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Buscar
            </button>
            <a href="{{ route('ars.pss.servicios') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Limpiar
            </a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Cobertura Base</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Alto Costo</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Requiere Receta/Indicación</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($servicios as $s)
                        <tr class="hover:bg-slate-50 transition text-xs">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-slate-800">
                                {{ $s->codigo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-700">
                                {{ $s->descripcion }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $s->cobertura_base }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ 
                                    $s->es_alto_costo ? 'bg-purple-50 text-purple-700' : 'bg-slate-100 text-slate-500'
                                }}">
                                    {{ $s->es_alto_costo ? 'Alto Costo' : 'Normal' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ 
                                    $s->requiere_documento ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500'
                                }}">
                                    {{ $s->requiere_documento ? 'Sí, requerido' : 'No requerido' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('ars.pss.servicios.edit', $s->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-brand-600 bg-white hover:bg-slate-50 hover:text-brand-700 transition">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">No hay servicios médicos cargados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($servicios->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $servicios->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
