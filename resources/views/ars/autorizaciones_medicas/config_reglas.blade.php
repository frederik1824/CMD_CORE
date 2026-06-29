@extends('layouts.ars')

@section('title', 'Configuración de Reglas del Motor')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Parámetros del Motor de Reglas</h2>
            <p class="text-xs text-slate-400 font-medium">Configure topes y flujos automáticos de auditoría para toda la red ARS</p>
        </div>
        <span class="text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 px-3 py-1 rounded-full uppercase tracking-wider font-mono">Reglas Operativas</span>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('autorizaciones_medicas.guardar_config_reglas') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6 text-xs font-semibold text-slate-650">
        @csrf
        
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Reglas de Auditoría Automática</h3>
            
            <div class="space-y-3">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-slate-200 text-teal-600 focus:ring-teal-200 mt-0.5">
                    <div>
                        <span class="text-slate-700 block font-bold">Desviar cirugías y hospitalización a Auditoría Médica</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Todo servicio catalogado como cirugía o internamiento requerirá dictamen manual de un auditor.</span>
                    </div>
                </label>

                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-slate-200 text-teal-600 focus:ring-teal-200 mt-0.5">
                    <div>
                        <span class="text-slate-700 block font-bold">Desviar solicitudes que excedan montos tarifados</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Si el prestador solicita un monto mayor a la tarifa contratada, pasa a Revisión Administrativa.</span>
                    </div>
                </label>

                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-slate-200 text-teal-600 focus:ring-teal-200 mt-0.5">
                    <div>
                        <span class="text-slate-700 block font-bold">Rechazo directo si PSS no tiene contrato</span>
                        <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Denegar automáticamente si el prestador no posee un convenio o tarifario vigente activo.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="space-y-4 border-t border-slate-50 pt-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Parámetros de Frecuencia y Acumuladores</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Límite de Consultas Ambulatorias</label>
                    <select class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                        <option value="1">Máximo 1 por día por especialidad</option>
                        <option value="2">Máximo 2 por mes</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Periodo de Cómputo de Límites</label>
                    <select class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                        <option value="30">Últimos 30 días móviles</option>
                        <option value="365">Año calendario (1 de Ene - 31 Dic)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-50">
            <button type="submit" class="bg-[#0056c5] hover:bg-blue-700 text-white font-bold rounded-full px-6 py-2.5 transition shadow-xs text-xs">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
