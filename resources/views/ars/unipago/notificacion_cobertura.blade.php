@extends('layouts.ars')
@section('title', 'Notificaciones de Cobertura Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Notificaciones de Cobertura</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de alertas de suspensión y reactivación de coberturas del padrón Unipago.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
            <h3 class="font-bold text-slate-800">Alertas de Cobertura</h3>
            <button onclick="alert('Alerta de suspensión/reactivación simulada con éxito.')" class="bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 rounded-full px-4 py-1.5 font-bold shadow-xs">Simular Alerta de Suspensión</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Mensaje</th>
                        <th class="px-4 py-3 text-center">Fecha Alerta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($notificaciones as $n)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-rose-700 capitalize">{{ $n->notification_type }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $n->title }}</td>
                            <td class="px-4 py-3 text-slate-500 leading-relaxed">{{ $n->message }}</td>
                            <td class="px-4 py-3 text-center font-mono text-slate-450">{{ $n->created_at->format('d/m Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection