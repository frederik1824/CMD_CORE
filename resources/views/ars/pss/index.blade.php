@extends('layouts.ars')

@section('title', 'Prestadoras (PSS)')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Prestadores de Servicios de Salud (PSS)
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Red nacional de clínicas, centros médicos y hospitales contratados por la ARS.
            </p>
        </div>
    </div>

    <!-- Barra de Filtro -->
    <div class="bg-white p-4 shadow-sm rounded-2xl border border-slate-200">
        <form action="{{ route('ars.pss.index') }}" method="GET" class="flex gap-4 items-end max-w-md">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Buscar Prestadora</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none" placeholder="Nombre o RNC...">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Buscar
            </button>
            <a href="{{ route('ars.pss.index') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Limpiar
            </a>
        </form>
    </div>

    <!-- Listado PSS -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Prestadora</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">RNC</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tipo Entidad</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Contacto</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Dirección</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($pss as $p)
                        <tr class="hover:bg-slate-50 transition text-xs">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $p->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-600">
                                {{ $p->rnc }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-semibold">
                                {{ $p->tipo_entidad }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 leading-normal">
                                Tel: {{ $p->telefono }}<br>Email: {{ $p->correo }}
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-slate-500">
                                {{ $p->direccion }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ 
                                    $p->estado === 'Activa' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'
                                }}">
                                    {{ $p->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('ars.pss.edit', $p->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-brand-600 bg-white hover:bg-slate-50 hover:text-brand-700 transition">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">No se encontraron prestadoras.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($pss->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $pss->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
