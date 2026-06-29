@extends('layouts.ars')
@section('title', 'Configurar Reglas del Catálogo PDSS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">

    {{-- Breadcrumbs & Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <span>ARS Core</span><span>/</span>
                <a href="{{ route('ars.pdss.catalogo') }}" class="hover:underline">Catálogo PDSS</a><span>/</span>
                <span class="text-gray-600">Reglas Operativas</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Reglas Dinámicas del Catálogo PDSS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Controla las auditorías automáticas y flujos de aprobación en lote basados en el Catálogo PDSS real.</p>
        </div>
        <div>
            <a href="{{ route('ars.pdss.catalogo') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-250 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 transition shadow-sm">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="menu_book">menu_book</span>
                Ver Catálogo PDSS
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-500" data-icon="check_circle">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Rules Editor Card --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Settings Form (Col Span 2) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-150 p-6 shadow-sm">
            <form action="{{ route('ars.autorizaciones.guardar_reglas_pdss') }}" method="POST" class="space-y-6">
                @csrf
                
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Reglas por Grupos PDSS</h2>

                <div class="space-y-4">
                    {{-- Alto Costo --}}
                    <div class="flex items-start justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-800 block">Grupo 9: Atenciones de Alto Costo y Máximo Nivel</span>
                            <p class="text-xs text-slate-450 leading-relaxed max-w-lg">
                                Desviar automáticamente a Auditoría Médica especializada todas las solicitudes de prestaciones pertenecientes a este grupo (quimioterapia, diálisis, bypass, etc.).
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="grupo_alto_costo_audit" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0b57d0]"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Emergencia --}}
                    <div class="flex items-start justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-800 block">Grupo 4: Aprobación Directa en Emergencias</span>
                            <p class="text-xs text-slate-450 leading-relaxed max-w-lg">
                                Si está activo, las atenciones de emergencias clínicas en prestadores de la red se aprueban automáticamente sin requerir auditoría, asignando prioridad Alta inmediata.
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="grupo_emergencia_no_audit" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0b57d0]"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Cirugías --}}
                    <div class="flex items-start justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-800 block">Grupo 7: Cirugías requieren Auditoría</span>
                            <p class="text-xs text-slate-450 leading-relaxed max-w-lg">
                                Forzar a auditoría todas las autorizaciones que contengan actos quirúrgicos o anestésicos para validar el protocolo clínico y tarifarios.
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="grupo_cirugia_audit" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0b57d0]"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Hospitalización --}}
                    <div class="flex items-start justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-800 block">Grupo 5: Internamientos/Hospitalizaciones requieren Auditoría</span>
                            <p class="text-xs text-slate-450 leading-relaxed max-w-lg">
                                Forzar a auditoría médica las autorizaciones de habitación clínica general, cunas o internamiento intensivo.
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="grupo_hospitalizacion_audit" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0b57d0]"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Medicamentos --}}
                    <div class="flex items-start justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-800 block">Grupo 12: Regulación de Medicamentos Ambulatorios</span>
                            <p class="text-xs text-slate-450 leading-relaxed max-w-lg">
                                Exigir adjuntar receta de soporte médico y validar el límite anual de RD$ 12,000 acumulado por afiliado antes de aprobar.
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="grupo_medicina_audit" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0b57d0]"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-[#0b57d0] text-white text-xs font-bold hover:bg-[#0842a0] transition shadow-sm">
                        Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        {{-- Explanatory sidebar --}}
        <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Resumen de Reglas PDSS</h3>
            <div class="space-y-4 text-xs text-slate-650 leading-relaxed">
                <p>
                    El motor de reglas evalúa las solicitudes en tiempo real contrastando la PSS y el Servicio seleccionado.
                </p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span>Alto Costo: <strong>{{ $stats['high_cost_audit'] }}</strong> prestaciones.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Emergencia: <strong>{{ $stats['emergency_auto'] }}</strong> prestaciones.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span>Hospitalización: <strong>{{ $stats['hosp_audit'] }}</strong> prestaciones.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Cirugías: <strong>{{ $stats['surgery_audit'] }}</strong> prestaciones.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Medicamentos: <strong>{{ $stats['medicine_doc'] }}</strong> prestaciones.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
