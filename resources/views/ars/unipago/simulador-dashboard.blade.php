@extends('layouts.ars')

@section('title', 'Unipago Simulador - Control Panel')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'ws', openConfigModal: false, selectedService: {} }">
    <!-- Header Hero Section -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 px-8 py-10 shadow-2xl border border-slate-800">
        <!-- Glow effects -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-blue-600/30 blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-emerald-600/20 blur-3xl"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Simulador Activo (Modo Local)
                </span>
                <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
                    Unipago & Unisigma Simulator
                </h1>
                <p class="text-slate-400 max-w-xl text-sm leading-relaxed">
                    Consola de control para orquestación de Web Services, preclasificación de afiliados, individualización de cápitas y simulación de cortes de dispersión TSS.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('ars.unipago_simulador.consola') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition duration-200">
                    <span class="material-symbols-outlined text-lg">terminal</span>
                    Consola de Pruebas
                </a>
            </div>
        </div>
    </div>

    <!-- Metricas Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Metric Item -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peticiones Totales</span>
                <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-2 rounded-xl group-hover:scale-110 transition">api</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 mt-4">{{ number_format($metricas['total_requests']) }}</p>
            <p class="text-xs text-slate-400 mt-2">Logs de auditoría mock</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lotes Transmitidos</span>
                <span class="material-symbols-outlined text-purple-600 bg-purple-50 p-2 rounded-xl group-hover:scale-110 transition">folder_zip</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 mt-4">{{ $metricas['lotes_core'] + $metricas['lotes_simulador'] }}</p>
            <p class="text-xs text-slate-400 mt-2">Core: {{ $metricas['lotes_core'] }} | Sim: {{ $metricas['lotes_simulador'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cápitas Confirmadas</span>
                <span class="material-symbols-outlined text-emerald-600 bg-emerald-50 p-2 rounded-xl group-hover:scale-110 transition">payments</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 mt-4">{{ $metricas['capitas_confirmadas'] }}</p>
            <p class="text-xs text-slate-400 mt-2">Pendientes: {{ $metricas['capitas_notificadas'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cortes Dispersión</span>
                <span class="material-symbols-outlined text-amber-600 bg-amber-50 p-2 rounded-xl group-hover:scale-110 transition">calendar_today</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 mt-4">{{ $metricas['cortes'] }}</p>
            <p class="text-xs text-slate-400 mt-2">Cápitas dispersadas: {{ $metricas['capitas_dispersadas'] }}</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6">
            <button @click="activeTab = 'ws'" :class="activeTab === 'ws' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Catálogo de Web Services
            </button>
            <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition">
                Logs de Solicitudes Recientes
            </button>
        </nav>
    </div>

    <!-- Tab WS Content -->
    <div x-show="activeTab === 'ws'" class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Código de Servicio</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nombre del Servicio</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Endpoint</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Protocolo</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Latencia</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Prob. Error</th>
                            <th scope="col" class="relative px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($servicios as $s)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-800">{{ $s->service_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">{{ $s->service_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-400">{{ $s->endpoint_mock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-500 uppercase">{{ $s->protocol }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-bold">
                                    {{ $s->simulated_latency_ms }} ms
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-600 font-bold">
                                    {{ $s->error_probability }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                    <button @click="selectedService = { id: {{ $s->id }}, name: '{{ $s->service_name }}', code: '{{ $s->service_code }}', latency: {{ $s->simulated_latency_ms }}, error: {{ $s->error_probability }}, default_response: '{{ $s->default_response_type }}' }; openConfigModal = true" class="text-blue-600 hover:text-blue-800 transition p-2 rounded-lg hover:bg-blue-50 inline-flex items-center justify-center" title="Configurar Latencia & Errores">
                                        <span class="material-symbols-outlined text-lg">settings</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Logs Content -->
    <div x-show="activeTab === 'logs'" class="space-y-6" style="display: none;">
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Historial Técnico de Solicitudes</h3>
                <span class="text-xs text-slate-400 font-mono">Consola Auditoría</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                @forelse($logs as $log)
                    <div class="p-4 hover:bg-slate-50/50 transition flex justify-between items-center gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded">{{ $log->service_code }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $log->created_at->format('d/m H:i:s') }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">{{ $log->service_name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $log->status === 'Processed' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $log->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-sm">No se han registrado peticiones al simulador aún.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Config Modal -->
    <div x-show="openConfigModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none" style="display: none;" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openConfigModal = false"></div>
        
        <!-- Content -->
        <div class="relative w-full max-w-md bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 z-10">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="space-y-1">
                    <h3 class="text-lg font-extrabold text-slate-800" x-text="selectedService.name">Configurar Servicio</h3>
                    <span class="font-mono text-xs text-slate-400" x-text="selectedService.code"></span>
                </div>
                <button @click="openConfigModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('ars.unipago_simulador.guardar_config_ws') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="service_id" :value="selectedService.id">

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Latencia Simulada (ms)</label>
                    <input type="number" name="simulated_latency_ms" :value="selectedService.latency" min="0" max="10000" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700">
                    <span class="text-[10px] text-slate-400 mt-1 block">Retardo de respuesta en milisegundos.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Probabilidad de Error (%)</label>
                    <input type="number" name="error_probability" :value="selectedService.error" min="0" max="100" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700">
                    <span class="text-[10px] text-slate-400 mt-1 block">Porcentaje de probabilidad de caída/timeout del servicio.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Respuesta por Defecto</label>
                    <select name="default_response_type" :value="selectedService.default_response" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700">
                        <option value="OK">OK - Procesado Exitosamente</option>
                        <option value="PE64">PE64 - Pendiente de Aporte</option>
                        <option value="PE75">PE75 - Ciudadano no Existe</option>
                        <option value="RE">RE - Rechazado / En otra ARS</option>
                    </select>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" @click="openConfigModal = false" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition text-sm">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 transition text-sm">Guardar Config</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
