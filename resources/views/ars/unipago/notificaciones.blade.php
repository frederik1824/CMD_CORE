@extends('layouts.core')

@section('title', 'Notificaciones Unipago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Notificaciones y Alertas de Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de mensajes transaccionales automatizados recibidos del simulador de cápitas y afiliación.</p>
        </div>
        <a href="{{ route('ars.unipago.dashboard') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la central
        </a>
    </div>

    <!-- Lista de Alertas -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden divide-y divide-slate-100">
        @forelse($notificaciones as $n)
            <div class="p-6 flex items-start space-x-4 hover:bg-slate-50/50 transition-colors">
                <!-- Icono segun tipo -->
                <div class="p-2.5 rounded-2xl bg-slate-50/80 text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                
                <div class="flex-1 text-xs">
                    <div class="flex justify-between items-start">
                        <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 font-bold text-[9px] text-slate-600 uppercase tracking-wider mb-1">
                            {{ $n->notification_type }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold">{{ $n->created_at->format('d/m/Y h:i A') }}</span>
                    </div>
                    
                    <h4 class="font-extrabold text-slate-800 text-sm mt-0.5">{{ $n->title }}</h4>
                    <p class="text-slate-600 font-medium mt-1 leading-relaxed">{{ $n->message }}</p>

                    @if($n->reference_type === 'batch')
                        <a href="{{ route('ars.unipago.lotes.show', $n->reference_id) }}" class="inline-block text-[#041e49] hover:underline font-bold mt-2">
                            Ver Lote Asociado &rarr;
                        </a>
                    @elseif($n->reference_type === 'capitation')
                        <a href="{{ route('ars.unipago.capitas') }}" class="inline-block text-[#041e49] hover:underline font-bold mt-2">
                            Ver Bandeja de Cápitas &rarr;
                        </a>
                    @elseif($n->reference_type === 'dispersion')
                        <a href="{{ route('ars.unipago.cortes') }}" class="inline-block text-[#041e49] hover:underline font-bold mt-2">
                            Ver Cortes de Dispersión &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-slate-400 font-medium text-xs">
                No hay notificaciones ni alertas transaccionales en esta bandeja.
            </div>
        @endforelse
    </div>
    <div class="pt-2">
        {{ $notificaciones->links() }}
    </div>
</div>
@endsection
