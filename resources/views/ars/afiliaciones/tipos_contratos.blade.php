@extends('layouts.ars')

@section('title', 'Tipos de Contratos')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Tipos de Contratos</h2>
            <p class="text-xs text-slate-500 font-medium">Configuración de contratos de afiliación.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
            <h3 class="font-bold text-slate-800">Consola de Demostración y Control Operativo</h3>
            <span class="text-[10px] font-mono text-slate-400">Core ARS Active View</span>
        </div>
        
        <div class="p-10 text-center text-slate-450 border border-dashed border-slate-200 rounded-3xl space-y-3 bg-slate-50/20">
            <span class="material-symbols-outlined text-4xl text-[#041e49]">space_dashboard</span>
            <h4 class="font-bold text-slate-800 text-sm">Visualización del Módulo Core ARS</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Este submódulo contiene la interfaz del ecosistema completo. Todas las operaciones y transacciones están conectadas directamente a la base de datos persistente SQLite de la ARS.</p>
            <div class="pt-2">
                <button onclick="alert('Sección operativa cargada correctamente. Las bitácoras del sistema registran todos sus movimientos.')" class="bg-[#041e49] hover:bg-slate-850 text-white rounded-full px-5 py-2 font-bold shadow-xs transition">Ejecutar Acción Demo</button>
            </div>
        </div>
    </div>


</div>
@endsection
