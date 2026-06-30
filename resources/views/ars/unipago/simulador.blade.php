@extends('layouts.ars')
@section('title', 'Consola del Simulador Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consola de Control del Simulador Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Simulación de procesos asíncronos y cargas masivas del SUIR.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Acciones del Simulador</h3>
            <div class="space-y-3">
                <div class="p-3 rounded-2xl bg-slate-50/50 border border-slate-100 flex items-start justify-between">
                    <div>
                        <h4 class="font-bold text-slate-800">Forzar Cierre de Período</h4>
                        <p class="text-[9px] text-slate-400">Genera de forma forzada los registros de cápitas para el mes en curso.</p>
                    </div>
                    <button onclick="alert('Cierre de periodo forzado exitosamente.')" class="bg-blue-600 hover:bg-blue-750 text-white rounded-full px-4 py-1.5 font-bold">Ejecutar</button>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50/50 border border-slate-100 flex items-start justify-between">
                    <div>
                        <h4 class="font-bold text-slate-800">Auditar Cuentas TSS</h4>
                        <p class="text-[9px] text-slate-400">Verifica discrepancias de afiliación con la nómina del empleador.</p>
                    </div>
                    <button onclick="alert('Verificación de aportes TSS finalizada.')" class="bg-[#041e49] hover:bg-slate-850 text-white rounded-full px-4 py-1.5 font-bold">Auditar</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Estatus del Web Service</h3>
                <div class="mt-4 flex items-center space-x-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-bold text-slate-800 text-sm">Servicio Online</span>
                </div>
                <p class="text-xs text-slate-450 mt-2 leading-relaxed">El canal seguro TLS de comunicación con los endpoints de Unipago se encuentra activo y recibiendo peticiones del Core.</p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-[10px]">
                <span class="text-slate-400 font-semibold">Última petición: hace 3 minutos</span>
                <a href="{{ route('ars.unipago.logs') }}" class="text-blue-600 font-bold hover:underline">Ver bitácora API</a>
            </div>
        </div>
    </div>
</div>
@endsection