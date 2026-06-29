@extends('layouts.ars')

@section('title', 'Unipago Simulador - Consola de Pruebas')

@section('content')
<div class="space-y-6" x-data="consoleTester()">
    <!-- Breadcrumbs / Top Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight sm:text-3xl">
                Consola Interactiva de Web Services
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Lanza peticiones y analiza las respuestas JSON generadas por el simulador de Unipago/Unisigma.
            </p>
        </div>
        <a href="{{ route('ars.unipago_simulador.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
            <span class="material-symbols-outlined text-lg">dashboard</span>
            Panel de Control
        </a>
    </div>

    <!-- Main Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Form & Selection -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
            <h3 class="font-extrabold text-slate-800 text-base">Parámetros del Request</h3>
            
            <div class="space-y-4">
                <!-- Select WS -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Seleccionar Servicio</label>
                    <select x-model="selectedServiceCode" @change="updatePayloadTemplate()" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700">
                        <option value="">-- Elegir un Web Service --</option>
                        @foreach($servicios as $s)
                            <option value="{{ $s->service_code }}">{{ $s->service_name }} ({{ $s->service_code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Template Selector -->
                <div x-show="selectedServiceCode">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Plantillas de Parámetros</label>
                    <div class="flex flex-wrap gap-2">
                        <button @click="loadTemplate('apto')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Ciudadano Apto (OK)</button>
                        <button @click="loadTemplate('sin_nomina')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Sin Nómina (PE64)</button>
                        <button @click="loadTemplate('otra_ars')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Otra ARS (RE001)</button>
                    </div>
                </div>

                <!-- Textarea Payload -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Request Payload (JSON)</label>
                    <textarea x-model="payload" rows="8" class="w-full font-mono text-xs px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50/50"></textarea>
                </div>
            </div>

            <!-- Action Button -->
            <button @click="enviarPeticion()" :disabled="!selectedServiceCode || loading" class="w-full flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition duration-200">
                <template x-if="loading">
                    <span class="h-5 w-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </template>
                <template x-if="!loading">
                    <span class="material-symbols-outlined text-lg">play_arrow</span>
                </template>
                <span x-text="loading ? 'Procesando llamada...' : 'Enviar Request'"></span>
            </button>
        </div>

        <!-- Right Column: Response Visualizer -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Metadata Response Bar -->
            <div class="bg-slate-900 text-white rounded-2xl p-6 border border-slate-800 shadow-2xl space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <h3 class="font-extrabold text-sm text-slate-400 uppercase tracking-wider">Response Console</h3>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            HTTP: <strong x-text="httpStatus || '--'"></strong>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            Latencia: <strong x-text="latency ? latency + 'ms' : '--'"></strong>
                        </span>
                    </div>
                </div>

                <!-- Code response displayer -->
                <div class="relative font-mono text-xs overflow-x-auto min-h-[300px] max-h-[500px] bg-slate-950 p-4 rounded-xl border border-slate-800 text-slate-300 select-all">
                    <template x-if="loading">
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 gap-3">
                            <span class="h-8 w-8 border-3 border-blue-500 border-t-transparent rounded-full animate-spin"></span>
                            <span class="text-xs text-slate-400 font-bold">Esperando respuesta del nodo simulado...</span>
                        </div>
                    </template>
                    <template x-if="!loading && !responseContent">
                        <div class="absolute inset-0 flex items-center justify-center text-slate-500">
                            <span>Completa los parámetros a la izquierda y pulsa 'Enviar Request'</span>
                        </div>
                    </template>
                    <pre x-text="responseContent" class="whitespace-pre-wrap"></pre>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    function consoleTester() {
        return {
            selectedServiceCode: '',
            payload: '{}',
            loading: false,
            httpStatus: null,
            latency: null,
            responseContent: '',
            
            updatePayloadTemplate() {
                this.loadTemplate('apto');
            },

            loadTemplate(type) {
                let defaultCeds = {
                    apto: '40200000020',
                    sin_nomina: '40200000025',
                    otra_ars: '40200000027'
                };
                
                let cedula = defaultCeds[type] || '40200000020';

                if (this.selectedServiceCode === 'CONSULTA_CIUDADANO') {
                    this.payload = JSON.stringify({ cedula: cedula }, null, 4);
                } else if (this.selectedServiceCode === 'VALIDA_CONTRATO_FORMULARIO') {
                    this.payload = JSON.stringify({ contract_number: '100005' }, null, 4);
                } else if (this.selectedServiceCode === 'CONFIRMACION_INDIVIDUALIZACION') {
                    this.payload = JSON.stringify({ notification_id: 1, decision: 'confirmar' }, null, 4);
                } else {
                    this.payload = JSON.stringify({ cedula: cedula, nss: '100000001' }, null, 4);
                }
            },

            async enviarPeticion() {
                this.loading = true;
                this.httpStatus = null;
                this.latency = null;
                this.responseContent = '';

                let startTime = performance.now();

                try {
                    let response = await fetch("{{ route('ars.unipago_simulador.ejecutar_ws') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            service_code: this.selectedServiceCode,
                            payload: this.payload
                        })
                    });

                    this.httpStatus = response.status;
                    let endTime = performance.now();
                    this.latency = Math.round(endTime - startTime);

                    let data = await response.json();
                    this.responseContent = JSON.stringify(data, null, 4);
                } catch (err) {
                    this.httpStatus = 500;
                    this.responseContent = JSON.stringify({ error: "Fallo técnico en la conexión AJAX con la consola." }, null, 4);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
