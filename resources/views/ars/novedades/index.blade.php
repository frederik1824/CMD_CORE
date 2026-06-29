@extends('layouts.ars')

@section('title', 'Bandeja de Novedades')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-[#f1f3f4] pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-semibold tracking-tight text-[#1f1f1f]">
                Novedades de Afiliación
            </h2>
            <p class="mt-1.5 text-sm text-[#5f6368]">
                Historial de novedades patronales y solicitudes de modificación registradas ante la TSS/Unipago.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-4 flex space-x-3 items-center">
            <form action="{{ route('ars.novedades.generar_lote') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-[#0b57d0]/20 rounded-full text-xs font-semibold text-[#0b57d0] bg-[#e8f0fe] hover:bg-[#d2e3fc] transition duration-150 active:scale-98 shadow-sm">
                    <svg class="mr-2 h-4 w-4 text-[#0b57d0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Generar Lote Novedades
                </button>
            </form>
            <a href="{{ route('ars.novedades.create') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-full shadow-sm text-xs font-semibold text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 transition duration-150 active:scale-98">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar Novedad
            </a>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white p-5 shadow-sm rounded-3xl border border-[#e0e0e0]">
        <form action="{{ route('ars.novedades.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
            <!-- Estado -->
            <div>
                <label for="estado" class="block text-xs font-semibold text-[#5f6368] uppercase tracking-wider mb-2">Estado de Procesamiento</label>
                <select name="estado" id="estado"
                        class="block w-full rounded-full border border-[#dcdcdc] py-2.5 px-3.5 text-xs text-[#1f1f1f] bg-white focus:outline-none focus:border-[#0b57d0] focus:ring-1 focus:ring-[#0b57d0] transition-all">
                    <option value="">Todos los estados</option>
                    <option value="PE" {{ $estado === 'PE' ? 'selected' : '' }}>PE - Pendiente de Lote</option>
                    <option value="OK" {{ $estado === 'OK' ? 'selected' : '' }}>OK - Procesado Exitosamente</option>
                    <option value="RE" {{ $estado === 'RE' ? 'selected' : '' }}>RE - Rechazado Unipago</option>
                </select>
            </div>

            <!-- Tipo Novedad -->
            <div>
                <label for="tipo" class="block text-xs font-semibold text-[#5f6368] uppercase tracking-wider mb-2">Tipo de Novedad</label>
                <select name="tipo" id="tipo"
                        class="block w-full rounded-full border border-[#dcdcdc] py-2.5 px-3.5 text-xs text-[#1f1f1f] bg-white focus:outline-none focus:border-[#0b57d0] focus:ring-1 focus:ring-[#0b57d0] transition-all">
                    <option value="">Todos los tipos</option>
                    @foreach($tiposNovedad as $tn)
                        <option value="{{ $tn->id }}" {{ $tipo == $tn->id ? 'selected' : '' }}>{{ $tn->descripcion }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex justify-center items-center px-5 py-2.5 border border-transparent rounded-full shadow-sm text-xs font-semibold text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 active:scale-98 transition duration-150">
                    Filtrar
                </button>
                <a href="{{ route('ars.novedades.index') }}" class="inline-flex items-center px-5 py-2.5 border border-[#dcdcdc] rounded-full text-xs font-semibold text-[#5f6368] bg-white hover:bg-[#f8f9fa] transition duration-150">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Listado en Formato Tabla -->
    <div class="bg-white shadow-sm rounded-3xl border border-[#e0e0e0] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#e0e0e0] text-sm">
                <thead class="bg-[#f8f9fa] font-semibold text-[#5f6368]">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Afiliado</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Tipo Novedad</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Lote Asociado</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Campos Modificados</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Motivo Respuesta</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e0e0] bg-white">
                    @forelse($novedades as $nov)
                        @php $afil = $nov->afiliado; @endphp
                        <tr class="hover:bg-[#f8f9fa] transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($afil)
                                    <div class="text-sm font-semibold text-[#1f1f1f]">{{ $afil->nombre_completo }}</div>
                                    <span class="text-xs text-[#5f6368] font-mono mt-0.5 block">Ced: {{ $afil->cedula }}</span>
                                @else
                                    <span class="text-[#5f6368] text-xs">Desconocido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#f1f3f4] text-[#3c4043] border border-[#e0e0e0]">{{ $nov->tipoNovedad->codigo }}</span>
                                <span class="text-xs text-[#5f6368] ml-2 font-medium">{{ $nov->tipoNovedad->descripcion }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-semibold">
                                @if($nov->lote)
                                    <a href="{{ route('ars.lotes.show', $nov->lote_id) }}" class="text-[#0b57d0] hover:text-[#0b57d0]/80 transition">{{ $nov->lote->numero_lote }}</a>
                                @else
                                    <span class="text-[#5f6368]/60">Sin enviar a lote</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-[#5f6368] leading-relaxed">
                                @if($nov->campos_modificados)
                                    <div class="space-y-1">
                                        @foreach($nov->campos_modificados as $c => $v)
                                            <span class="bg-[#f8f9fa] px-2 py-0.5 rounded border border-[#e0e0e0] block w-max max-w-[200px] truncate">{{ $c }}: {{ $v }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ 
                                    $nov->state_color_classes ?? (
                                    $nov->estado === 'OK' ? 'bg-[#e6f4ea] text-[#137333] border-[#ceead6]' : (
                                    $nov->estado === 'RE' ? 'bg-[#fce8e6] text-[#c5221f] border-[#fad2cf]' : 'bg-[#fef7e0] text-[#b06000] border-[#feebc8]'))
                                }}">
                                    {{ $nov->estado === 'OK' ? 'Aplicada' : ($nov->estado === 'RE' ? 'Rechazada' : 'Pendiente') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-[#5f6368] max-w-xs">
                                @if($nov->motivo_estado)
                                    @if(strlen($nov->motivo_estado) > 40)
                                        <div x-data="{ open: false }">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="truncate block max-w-[160px]">{{ Str::limit($nov->motivo_estado, 30, '') }}</span>
                                                <button type="button" @click="open = !open" class="text-[#0b57d0] font-black focus:outline-none hover:underline text-[10px] whitespace-nowrap">
                                                    <span x-text="open ? '[-] menos' : '[+] más'"></span>
                                                </button>
                                            </div>
                                            <div x-show="open" x-cloak class="mt-1.5 p-2 bg-slate-50 border border-slate-150 rounded-xl leading-normal text-[11px] whitespace-pre-wrap animate-fade-in" x-transition>
                                                {{ $nov->motivo_estado }}
                                            </div>
                                        </div>
                                    @else
                                        {{ $nov->motivo_estado }}
                                    @endif
                                @else
                                    <span class="text-slate-400 font-semibold italic">En espera de ser procesada por Unipago.</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-[#5f6368] font-mono">
                                {{ $nov->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#5f6368] text-sm">No se encontraron novedades de afiliación registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($novedades->hasPages())
            <div class="px-6 py-4 border-t border-[#e0e0e0] bg-[#f8f9fa]">
                {{ $novedades->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
