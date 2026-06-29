@extends('layouts.ars')

@section('title', 'Mesa de Entrada - Reclamaciones')

@section('content')
<div class="space-y-6" x-data="{ devClaimId: null, devReason: '' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Mesa de Entrada & Radicación</h2>
            <p class="text-xs text-slate-400 font-medium">Recepción oficial de facturas presentadas digitalmente por las PSS</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 px-3 py-1 text-xs font-bold border border-amber-200">
                {{ $reclamaciones->total() }} Pendientes de Entrada
            </span>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Barra de Búsqueda -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.reclamaciones.recepcion') }}" method="GET" class="flex items-center space-x-2 text-xs">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por código de reclamación, NCF, número de factura o nombre de PSS..."
                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 pl-9 pr-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all font-semibold">
            </div>
            <button type="submit" class="bg-[#0056c5] text-white rounded-full px-5 py-2.5 font-bold hover:bg-blue-700 transition shadow-xs">
                Buscar
            </button>
            @if(isset($search) && $search !== '')
                <a href="{{ route('ars.reclamaciones.recepcion') }}" class="bg-slate-100 text-slate-650 rounded-full px-5 py-2.5 font-bold hover:bg-slate-200 transition">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">Código / PSS</th>
                        <th scope="col" class="px-6 py-4 text-left">Factura / NCF</th>
                        <th scope="col" class="px-6 py-4 class-left">F. Prestación / F. Envío</th>
                        <th scope="col" class="px-6 py-4 text-right">Monto Sometido</th>
                        <th scope="col" class="px-6 py-4 text-center">Estado</th>
                        <th scope="col" class="px-6 py-4 text-center w-48">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($reclamaciones as $rec)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-800 font-mono block text-xs">{{ $rec->claim_number }}</span>
                                <span class="text-slate-400 font-medium block mt-0.5">{{ $rec->pss->nombre }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700 font-mono block">{{ $rec->invoice_number }}</span>
                                <span class="text-slate-450 font-medium block mt-0.5">NCF: {{ $rec->ncf }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-600 block">{{ \Carbon\Carbon::parse($rec->service_date)->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Recibido: {{ $rec->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-extrabold text-slate-900 font-mono block">DOP {{ number_format($rec->claimed_amount, 2) }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Aut.: DOP {{ number_format($rec->authorized_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold border
                                    {{ $rec->status === 'Devuelta por documentos' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $rec->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-1">
                                    <form action="{{ route('ars.reclamaciones.dar_entrada', $rec->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-full transition text-[10px] tracking-wide shadow-xs">
                                            Radicar / Dar Entrada
                                        </button>
                                    </form>
                                    <button type="button" 
                                            @click="devClaimId = {{ $rec->id }}; devReason = ''"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-full border border-rose-200 transition text-[10px] tracking-wide">
                                        Devolver
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-450 font-semibold">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                No hay reclamaciones de PSS en mesa de entrada pendientes de radicación.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reclamaciones->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $reclamaciones->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Devolución Documental -->
    <div x-show="devClaimId !== null" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-100 p-5 space-y-4"
             @click.away="devClaimId = null">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Devolución de Expediente</h3>
                <p class="text-[11px] text-slate-450 mt-0.5">Indique el motivo por el cual se rechaza/devuelve la reclamación a la PSS (e.g. factura ilegible, falta firma, error de NCF).</p>
            </div>
            
            <form :action="'/core/reclamaciones/' + devClaimId + '/devolver'" method="POST" class="space-y-4">
                @csrf
                <textarea name="reason" x-model="devReason" rows="4" required minlength="5"
                          class="w-full text-xs border border-slate-200 bg-[#eaf1fb]/20 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-rose-100 placeholder:text-slate-400"
                          placeholder="Indique detalladamente la documentación faltante o el error..."></textarea>

                <div class="flex justify-end space-x-2">
                    <button type="button" @click="devClaimId = null" class="px-4 py-2 border border-slate-200 rounded-full text-xs font-semibold text-slate-500 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-full text-xs transition shadow-xs">
                        Confirmar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
