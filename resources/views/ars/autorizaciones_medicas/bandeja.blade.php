@extends('layouts.ars')

@section('title', 'Bandeja de Autorizaciones Médicas')

@section('content')
<div class="space-y-6">
    <!-- Encabezado Corporativo Premium -->
    <div class="bg-gradient-to-r from-[#000666] via-[#00368c] to-[#0056c5] rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="flex items-center space-x-4 z-10">
            <div class="p-2.5 bg-white rounded-2xl shadow-sm flex-shrink-0">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
            </div>
            <div class="space-y-1">
                <span class="text-[9px] bg-white/20 text-white border border-white/20 px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">Gestión Central</span>
                <h2 class="text-xl font-bold tracking-tight text-white">Bandeja General de Autorizaciones</h2>
                <p class="text-xs text-blue-100 font-medium">Consulte, filtre y gestione solicitudes generadas por el Core y por el Portal PSS.</p>
            </div>
        </div>
        <div class="z-10">
            <a href="{{ route('ars.autorizaciones_medicas.create') }}" class="bg-white text-[#000666] hover:bg-slate-50 font-bold px-5 py-2.5 rounded-full transition text-xs shadow-md inline-block">
                + Crear Autorización
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white/90 backdrop-blur-md p-6 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.autorizaciones_medicas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-[9px]">
            <div>
                <label class="block mb-1.5">No. Autorización</label>
                <input type="text" name="numero_autorizacion" value="{{ $numero }}" placeholder="Ej: AUT-2026..."
                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
            </div>

            <div>
                <label class="block mb-1.5">Estatus</label>
                <select name="estado" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todos...</option>
                    <option value="Aprobada" {{ $estado === 'Aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="Auditoría" {{ $estado === 'Auditoría' ? 'selected' : '' }}>En Auditoría</option>
                    <option value="Pendiente Documento" {{ $estado === 'Pendiente Documento' ? 'selected' : '' }}>Pendiente Documento</option>
                    <option value="Rechazada" {{ $estado === 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>

            <div>
                <label class="block mb-1.5">Canal de Entrada</label>
                <select name="channel" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todos...</option>
                    <option value="llamada" {{ $canal === 'llamada' ? 'selected' : '' }}>Llamada Telefónica</option>
                    <option value="correo" {{ $canal === 'correo' ? 'selected' : '' }}>Correo Electrónico</option>
                    <option value="whatsapp" {{ $canal === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="presencial" {{ $canal === 'presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="portal" {{ $canal === 'portal' ? 'selected' : '' }}>Portal PSS</option>
                </select>
            </div>

            <div>
                <label class="block mb-1.5">Origen</label>
                <select name="origin" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todos...</option>
                    <option value="core_ars" {{ $origen === 'core_ars' ? 'selected' : '' }}>Interno (Core ARS)</option>
                    <option value="portal_pss" {{ $origen === 'portal_pss' ? 'selected' : '' }}>Externo (Portal PSS)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-[#0056c5] text-white rounded-full px-5 py-2.5 font-bold hover:bg-blue-700 transition shadow-sm text-center normal-case text-xs">
                    Filtrar
                </button>
                <a href="{{ route('ars.autorizaciones_medicas.index') }}" class="bg-slate-100 text-slate-600 rounded-full px-4 py-2.5 font-bold hover:bg-slate-200 transition text-center normal-case text-xs">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Listado General -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/60 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-3.5 py-3 text-left w-[15%]">No. Autorización</th>
                        <th scope="col" class="px-3.5 py-3 text-left w-[20%]">Afiliado</th>
                        <th scope="col" class="px-3.5 py-3 text-left w-[20%]">Prestador PSS</th>
                        <th scope="col" class="px-3.5 py-3 text-left w-[25%]">Procedimiento / Detalle</th>
                        <th scope="col" class="px-3.5 py-3 text-right w-[10%]">Monto</th>
                        <th scope="col" class="px-3.5 py-3 text-center w-[10%]">Estatus</th>
                        <th scope="col" class="px-3.5 py-3 text-center w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                    @forelse($autorizaciones as $a)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-3.5 py-3">
                                <span class="font-extrabold text-slate-800 font-mono block text-xs">{{ $a->numero_autorizacion }}</span>
                                <div class="flex items-center space-x-1.5 mt-1">
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider
                                        {{ $a->origin === 'core_ars' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                        {{ $a->origin === 'core_ars' ? 'Core' : 'Portal' }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 capitalize font-semibold">{{ $a->channel ?? 'portal' }}</span>
                                </div>
                            </td>
                            <td class="px-3.5 py-3">
                                <span class="font-bold text-slate-800 block text-[11px]">{{ $a->afiliado->nombres }} {{ $a->afiliado->primer_apellido }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">Ced: {{ $a->afiliado->cedula }}</span>
                            </td>
                            <td class="px-3.5 py-3 font-bold text-slate-700 max-w-[150px] truncate animate-fade-in" title="{{ $a->pss->nombre }}">
                                {{ $a->pss->nombre }}
                            </td>
                            <td class="px-3.5 py-3 max-w-[200px] truncate animate-fade-in" title="{{ $a->procedimiento ?? 'Consulta o Análisis' }}">
                                <span class="font-bold text-slate-750 block leading-tight text-[11px] truncate">{{ $a->procedimiento ?? 'Consulta o Análisis' }}</span>
                                <span class="text-[9px] text-slate-400 block mt-0.5 font-bold uppercase">Prioridad: {{ $a->prioridad }}</span>
                            </td>
                            <td class="px-3.5 py-3 text-right font-bold text-slate-900 font-mono text-xs whitespace-nowrap">
                                DOP {{ number_format($a->monto_solicitado, 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-black border uppercase tracking-wider
                                    {{ $a->estado === 'Aprobada' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                       ($a->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-250' : 
                                       ($a->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-250' : 'bg-slate-50 text-slate-655 border-slate-200')) }}">
                                    {{ $a->estado }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <a href="{{ route('ars.autorizaciones_medicas.show', $a->id) }}" 
                                   class="text-[#0056c5] hover:text-blue-800 transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle de Autorización">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-semibold italic">
                                No se encontraron autorizaciones que coincidan con los criterios de búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($autorizaciones->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $autorizaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
