@extends('layouts.ars')

@section('title', 'Capas de Catálogos')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Capas de Catálogos del Sistema
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Catálogos y codificaciones homologadas para traducción de estados, parentescos, e identificaciones.
            </p>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-200">
        <form action="{{ route('ars.admin.catalogos') }}" method="GET" class="flex gap-4 items-end max-w-md">
            <div class="flex-1">
                <label for="grupo" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Filtrar por Grupo</label>
                <select name="grupo" id="grupo" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 bg-white focus:outline-none">
                    <option value="">Todos los grupos</option>
                    @foreach($grupos as $gr)
                        <option value="{{ $gr }}" {{ $grupo === $gr ? 'selected' : '' }}>{{ str_replace('_', ' ', $gr) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Filtrar
            </button>
            <a href="{{ route('ars.admin.catalogos') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
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
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Grupo</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @forelse($catalogos as $cat)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-600 capitalize">
                                {{ str_replace('_', ' ', $cat->grupo) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-slate-800">
                                {{ $cat->codigo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                {{ $cat->descripcion }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ 
                                    $cat->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'
                                }}">
                                    {{ $cat->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">No se encontraron catálogos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($catalogos->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $catalogos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
