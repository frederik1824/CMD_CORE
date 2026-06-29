@extends('layouts.ars')

@section('title', 'Afiliados')

@section('content')
<div class="space-y-8 animate-fade-in font-sans">
    
    <!-- Encabezado con Estilo Moderno -->
    <div class="sm:flex sm:items-center sm:justify-between pb-6 border-b border-slate-200">
        <div class="flex-1 min-w-0 space-y-1">
            <h2 class="text-3xl font-extrabold tracking-tight text-primary font-title">
                Afiliados
            </h2>
            <p class="text-sm text-on-surface-variant">
                Catálogo general de cotizantes titulares de la Administradora de Riesgos de Salud (ARS).
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-4 flex flex-wrap gap-3">
            <a href="{{ route('ars.carga.masiva') }}" class="inline-flex items-center px-5 py-2.5 border border-slate-200 rounded-full text-xs font-bold text-on-surface-variant bg-white hover:bg-slate-50 transition active:scale-95 shadow-sm">
                <span class="material-symbols-outlined mr-2 text-base">upload_file</span>
                Carga Masiva (CSV)
            </a>
            <a href="{{ route('ars.titulares.create') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-full shadow-lg shadow-secondary/10 text-xs font-bold text-white bg-secondary hover:bg-primary transition active:scale-95">
                <span class="material-symbols-outlined mr-2 text-base">add</span>
                Nuevo Afiliado
            </a>
        </div>
    </div>

    <!-- Barra de Filtros en Glass Card -->
    <div class="glass-card p-6 rounded-3xl space-y-4">
        <form action="{{ route('ars.titulares.index') }}" method="GET" class="grid grid-cols-1 gap-5 sm:grid-cols-4 items-end">
            <!-- Búsqueda -->
            <div>
                <label for="search" class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Buscar Afiliado</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input type="text" name="search" id="search" value="{{ $search }}"
                           class="block w-full rounded-full border border-slate-200 bg-surface pl-10 pr-4 py-2.5 text-xs text-on-surface placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all"
                           placeholder="Nombre, Cédula, NSS...">
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Estado de Afiliación</label>
                <select name="estado" id="estado"
                        class="block w-full rounded-full border border-slate-200 py-2.5 px-4 text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-white transition-all">
                    <option value="">Todos los estados</option>
                    <option value="OK" {{ $estado === 'OK' ? 'selected' : '' }}>OK - Activo</option>
                    <option value="RE" {{ $estado === 'RE' ? 'selected' : '' }}>RE - Rechazado</option>
                    <option value="Pendiente" {{ $estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>

            <!-- Régimen -->
            <div>
                <label for="regimen" class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Régimen Social</label>
                <select name="regimen" id="regimen"
                        class="block w-full rounded-full border border-slate-200 py-2.5 px-4 text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-white transition-all">
                    <option value="">Todos los regímenes</option>
                    <option value="Contributivo" {{ $regimen === 'Contributivo' ? 'selected' : '' }}>Régimen Contributivo</option>
                    <option value="Subsidiado" {{ $regimen === 'Subsidiado' ? 'selected' : '' }}>Régimen Subsidiado</option>
                    <option value="Pensionados" {{ $regimen === 'Pensionados' ? 'selected' : '' }}>Régimen Pensionados</option>
                </select>
            </div>

            <!-- Botones de Acción -->
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 inline-flex justify-center items-center px-5 py-2.5 border border-transparent rounded-full shadow-md shadow-secondary/10 text-xs font-bold text-white bg-secondary hover:bg-primary transition active:scale-95">
                    <span class="material-symbols-outlined mr-2 text-base">filter_alt</span>
                    Filtrar
                </button>
                <a href="{{ route('ars.titulares.index') }}" class="inline-flex items-center px-5 py-2.5 border border-slate-250 rounded-full text-xs font-bold text-on-surface-variant bg-white hover:bg-slate-50 transition active:scale-95 shadow-sm">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Listado Premium en Glass Card -->
    <div class="glass-card rounded-[32px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/50 font-bold text-on-surface-variant">
                    <tr>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Nombre Completo</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Cédula / NSS</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Régimen</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Detalles de Validación</th>
                        <th scope="col" class="relative px-6 py-4.5 text-right">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($titulares as $afil)
                        <tr class="hover:bg-slate-50/40 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-secondary/10 text-secondary flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($afil->nombres, 0, 1) }}{{ substr($afil->primer_apellido, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('ars.titulares.show', $afil->id) }}" class="text-sm font-bold text-primary hover:text-secondary transition duration-150 block">
                                            {{ $afil->nombre_completo }}
                                        </a>
                                        <span class="text-xs text-on-surface-variant block mt-0.5">{{ $afil->correo }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-700 block font-mono font-bold">{{ $afil->cedula ?? 'N/A' }}</span>
                                <span class="text-xs text-on-surface-variant block font-mono mt-0.5">NSS: {{ $afil->nss ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-semibold text-slate-700 block">{{ $afil->regimen_actual }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ 
                                    $afil->estado_afiliacion === 'OK' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (
                                    $afil->estado_afiliacion === 'RE' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200')
                                }}">
                                    {{ $afil->estado_afiliacion === 'OK' ? 'OK - Activo' : ($afil->estado_afiliacion === 'RE' ? 'RE - Rechazado' : 'Pendiente') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-on-surface-variant leading-relaxed max-w-xs truncate">
                                {{ $afil->motivo_estado ?? 'Sin incidencias en la validación' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                <a href="{{ route('ars.titulares.show', $afil->id) }}" class="text-secondary hover:text-primary transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Ficha de Afiliado">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant text-sm font-semibold">No se encontraron afiliados que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($titulares->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $titulares->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

