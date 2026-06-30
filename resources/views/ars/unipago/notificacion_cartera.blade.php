@extends('layouts.ars')
@section('title', 'Notificaciones de Cartera Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Notificaciones de Cartera Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Alertas de recepción de aportes patronales enviados por Unipago.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
            <h3 class="font-bold text-slate-800">Historial de Notificaciones de Cartera</h3>
            <button onclick="alert('Notificación simulada enviada exitosamente.')" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-4 py-1.5 font-bold shadow-xs">Simular Recibo Cartera</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Mensaje</th>
                        <th class="px-4 py-3 text-center">Fecha Notificado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($notificaciones as $n)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-blue-900 capitalize">{{ $n->notification_type }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $n->title }}</td>
                            <td class="px-4 py-3 text-slate-500 leading-relaxed">{{ $n->message }}</td>
                            <td class="px-4 py-3 text-center font-mono text-slate-450">{{ $n->created_at->format('d/m Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="py-2">
            {{ $notificaciones->links() }}
        </div>
    </div>
</div>
@endsection