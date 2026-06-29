@extends('layouts.ars')

@section('title', 'Bitácora de Auditoría')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-[#f1f3f4] pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-semibold tracking-tight text-[#1f1f1f]">
                Bitácora de Eventos & Auditoría
            </h2>
            <p class="mt-1.5 text-sm text-[#5f6368]">
                Registro cronológico detallado de transacciones de negocio y seguridad ejecutadas en el sistema.
            </p>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white p-5 shadow-sm rounded-3xl border border-[#e0e0e0]">
        <form action="{{ route('ars.admin.bitacora') }}" method="GET" class="flex gap-3 items-end max-w-md">
            <div class="flex-1">
                <label for="modulo" class="block text-xs font-semibold text-[#5f6368] uppercase tracking-wider mb-2">Filtrar por Módulo</label>
                <select name="modulo" id="modulo"
                        class="block w-full rounded-full border border-[#dcdcdc] py-2.5 px-3.5 text-xs text-[#1f1f1f] bg-white focus:outline-none focus:border-[#0b57d0] focus:ring-1 focus:ring-[#0b57d0] transition">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $mod)
                        <option value="{{ $mod }}" {{ $modulo === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 border border-transparent rounded-full shadow-sm text-xs font-semibold text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 active:scale-98 transition duration-150">
                Filtrar
            </button>
            <a href="{{ route('ars.admin.bitacora') }}" class="inline-flex items-center px-5 py-2.5 border border-[#dcdcdc] rounded-full text-xs font-semibold text-[#5f6368] bg-white hover:bg-[#f8f9fa] transition duration-150">
                Limpiar
            </a>
        </form>
    </div>

    <!-- Tabla de Trazabilidad -->
    <div class="bg-white shadow-sm rounded-3xl border border-[#e0e0e0] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#e0e0e0] text-sm">
                <thead class="bg-[#f8f9fa] font-semibold text-[#5f6368]">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha / Hora</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Usuario</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Módulo</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Acción Realizada</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Detalles adicionales</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e0e0] bg-white text-xs">
                    @forelse($bitacoras as $bit)
                        <tr class="hover:bg-[#f8f9fa] transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[#5f6368] text-xs">
                                {{ $bit->fecha_registro->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-semibold text-[#1f1f1f] block">{{ $bit->usuario ? $bit->usuario->name : 'Motor de Reglas' }}</span>
                                <span class="text-xs text-[#5f6368] block mt-0.5">{{ $bit->usuario ? $bit->usuario->email : 'System' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-[#f1f3f4] text-[#3c4043] border border-[#e0e0e0]">{{ $bit->modulo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#1f1f1f]">
                                {{ $bit->accion }}
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-xs text-[#5f6368] leading-normal font-mono">
                                @if($bit->detalles)
                                    {{ json_encode($bit->detalles) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[#5f6368] text-xs">
                                {{ $bit->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#5f6368] text-sm">No hay registros de bitácora coincidentes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($bitacoras->hasPages())
            <div class="px-6 py-4 border-t border-[#e0e0e0] bg-[#f8f9fa]">
                {{ $bitacoras->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

