@extends('layouts.ars')

@section('title', 'Carga Individual de Titular')

@section('content')
<div class="space-y-6" x-data="formWizard()">

    {{-- ── HEADER ── --}}
    <div class="sm:flex sm:items-start sm:justify-between border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                <span class="material-symbols-outlined text-[14px]">group</span>
                <span>Afiliaciones</span>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-blue-600">Carga Individual de Titular</span>
            </div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight sm:text-3xl">
                Carga Individual de Titular
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Registro paso a paso de un nuevo afiliado cotizante con validación en tiempo real ante Unipago/JCE.
            </p>
        </div>
        <a href="{{ route('ars.solicitudes.titulares.index') }}"
           class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition shadow-sm">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Volver a Bandeja
        </a>
    </div>

    {{-- ── BODY: STEPPER + FORM ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

        {{-- ── COLUMNA IZQUIERDA: Pasos ── --}}
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pasos del registro</p>
                </div>
                <div class="p-4 space-y-1">
                    <template x-for="(step, i) in steps" :key="i">
                        <div>
                            <button type="button" @click="goToStep(i)"
                                    class="w-full flex items-start gap-3 px-3 py-2.5 rounded-xl transition text-left"
                                    :class="currentStep === i ? 'bg-blue-50' : 'hover:bg-slate-50'">
                                <span class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold border-2 mt-0.5 transition"
                                      :class="currentStep > i ? 'border-emerald-500 bg-emerald-500 text-white' :
                                              currentStep === i ? 'border-blue-600 bg-blue-600 text-white' :
                                              'border-slate-200 bg-white text-slate-300'">
                                    <template x-if="currentStep > i">
                                        <span class="material-symbols-outlined text-[13px]">check</span>
                                    </template>
                                    <template x-if="currentStep <= i">
                                        <span x-text="i + 1"></span>
                                    </template>
                                </span>
                                <div>
                                    <p class="font-bold text-xs transition"
                                       :class="currentStep === i ? 'text-blue-700' : currentStep > i ? 'text-emerald-700' : 'text-slate-400'"
                                       x-text="step.label"></p>
                                    <p class="text-[10px] mt-0.5 transition"
                                       :class="currentStep === i ? 'text-blue-400' : 'text-slate-300'"
                                       x-text="step.desc"></p>
                                </div>
                            </button>
                            <template x-if="i < steps.length - 1">
                                <div class="ml-[18px] w-px h-3 bg-slate-200"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Tip Card --}}
            <div class="bg-slate-800 rounded-2xl p-4 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-400 text-[16px]">tips_and_updates</span>
                    <p class="text-xs font-bold text-white">Tip de Registro</p>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed"
                   x-text="steps[currentStep]?.tip">
                </p>
            </div>
        </div>

        {{-- ── COLUMNA DERECHA: Formulario ── --}}
        <div class="lg:col-span-3">
            <form action="{{ route('ars.solicitudes.titulares.guardar') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                {{-- ═══════════════════════════════════════
                     PASO 1 — Datos Personales
                ════════════════════════════════════════ --}}
                <div x-show="currentStep === 0"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">A</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sección A — Datos Personales</h3>
                            <p class="text-[11px] text-slate-400">Información de identidad del nuevo afiliado titular</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Tipo ID --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">badge</span>
                                    Tipo de Identificación
                                </label>
                                <select name="tipo_identificacion_id"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    @foreach($tiposIdentificacion as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_identificacion_id') == $tipo->id ? 'selected' : '' }} style="color:#1e293b;background:#fff">{{ $tipo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Cédula --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">fingerprint</span>
                                    Cédula de Identidad
                                </label>
                                <input type="text" name="cedula" x-model="cedula" @input="clearPrevalidation()"
                                       placeholder="Ej: 40200000020"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                            </div>

                            {{-- NSS --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">tag</span>
                                    NSS
                                    <span class="ml-1 text-[9px] font-bold bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full">OPCIONAL</span>
                                </label>
                                <input type="text" name="nss"
                                        value="{{ old('nss') }}"
                                       placeholder="Ej: 100000001"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                            </div>

                            {{-- Fecha Nac --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">cake</span>
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" name="fecha_nacimiento"
                                        value="{{ old('fecha_nacimiento') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                            </div>

                            {{-- Nombres --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person</span>
                                    Nombres
                                </label>
                                <input type="text" name="nombres"
                                        value="{{ old('nombres') }}"
                                       placeholder="Nombres del titular"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>

                            {{-- Sexo --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">wc</span>
                                    Sexo
                                </label>
                                <select name="sexo"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }} style="color:#1e293b;background:#fff">Masculino</option>
                                    <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }} style="color:#1e293b;background:#fff">Femenino</option>
                                </select>
                            </div>

                            {{-- Primer Apellido --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person_outline</span>
                                    Primer Apellido
                                </label>
                                <input type="text" name="primer_apellido"
                                        value="{{ old('primer_apellido') }}"
                                       placeholder="Primer apellido"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>

                            {{-- Segundo Apellido --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person_outline</span>
                                    Segundo Apellido
                                    <span class="ml-1 text-[9px] font-bold bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full">OPCIONAL</span>
                                </label>
                                <input type="text" name="segundo_apellido"
                                        value="{{ old('segundo_apellido') }}"
                                       placeholder="Segundo apellido"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">location_city</span>
                                    Provincia
                                </label>
                                <input type="text" name="provincia" placeholder="Provincia"
                                       value="{{ old('provincia') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">apartment</span>
                                    Municipio
                                </label>
                                <input type="text" name="municipio" placeholder="Municipio"
                                       value="{{ old('municipio') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">holiday_village</span>
                                    Sector
                                </label>
                                <input type="text" name="sector" placeholder="Sector"
                                       value="{{ old('sector') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[13px] text-slate-400">home</span>
                                Dirección Completa
                            </label>
                            <input type="text" name="direccion" placeholder="Calle, número, edificio, apartamento..."
                                   value="{{ old('direccion') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">phone</span>
                                    Teléfono
                                </label>
                                <input type="text" name="telefono" placeholder="Ej: 809-555-0199"
                                       value="{{ old('telefono') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">mail</span>
                                    Correo Electrónico
                                    <span class="ml-1 text-[9px] font-bold bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full">OPCIONAL</span>
                                </label>
                                <input type="email" name="correo" placeholder="Ej: usuario@correo.com"
                                       value="{{ old('correo') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════
                     PASO 2 — Datos Laborales
                ════════════════════════════════════════ --}}
                <div x-show="currentStep === 1"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">B</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sección B — Datos Laborales</h3>
                            <p class="text-[11px] text-slate-400">Información de nómina y cotizaciones del afiliado</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">account_tree</span>
                                    Régimen de Cotización
                                </label>
                                <select name="regimen_actual"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    @foreach($regimenes as $reg)
                                        <option value="{{ $reg->descripcion }}" {{ old('regimen_actual') == $reg->descripcion ? 'selected' : '' }} style="color:#1e293b;background:#fff">{{ $reg->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">payments</span>
                                    Salario Reportado (RD$)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">RD$</span>
                                    <input type="number" step="0.01" name="salary_amount"
                                           value="{{ old('salary_amount') }}"
                                           placeholder="0.00"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                                </div>
                            </div>

                            <div class="space-y-1.5 md:col-span-2">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">business</span>
                                    Nombre del Empleador (Razón Social)
                                </label>
                                <input type="text" name="employer_name"
                                       value="{{ old('employer_name') }}"
                                       placeholder="Empresa o institución empleadora"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">pin</span>
                                    RNC del Empleador
                                </label>
                                <input type="text" name="employer_rnc"
                                       value="{{ old('employer_rnc') }}"
                                       placeholder="Ej: 130000000"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════
                     PASO 3 — Contrato / Formulario
                ════════════════════════════════════════ --}}
                <div x-show="currentStep === 2"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
                     x-data="{ contractMode: '{{ old('contract_number') ? 'manual' : 'auto' }}' }">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">C</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sección C — Contrato / Formulario</h3>
                            <p class="text-[11px] text-slate-400">Asignación del número de formulario físico de afiliación</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div @click="contractMode = 'auto'"
                                 :class="contractMode === 'auto' ? 'border-blue-500 bg-blue-50/30 shadow-md shadow-blue-500/10' : 'border-slate-200 hover:bg-slate-50'"
                                 class="p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex items-start gap-4">
                                <span class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 mt-0.5"
                                      :class="contractMode === 'auto' ? 'bg-blue-100' : 'bg-slate-100'">
                                    <span class="material-symbols-outlined text-blue-600 text-[20px]"
                                          :class="contractMode === 'auto' ? 'text-blue-600' : 'text-slate-400'">auto_awesome</span>
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Asignación Automática</h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Toma el siguiente número disponible en la serie oficial asignada al agente.</p>
                                    <span class="inline-flex items-center mt-2 gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                        <span class="material-symbols-outlined text-[11px]">recommend</span>
                                        Recomendado
                                    </span>
                                </div>
                            </div>

                            <div @click="contractMode = 'manual'"
                                 :class="contractMode === 'manual' ? 'border-purple-500 bg-purple-50/30 shadow-md shadow-purple-500/10' : 'border-slate-200 hover:bg-slate-50'"
                                 class="p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex items-start gap-4">
                                <span class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                                      :class="contractMode === 'manual' ? 'bg-purple-100' : 'bg-slate-100'">
                                    <span class="material-symbols-outlined text-[20px]"
                                          :class="contractMode === 'manual' ? 'text-purple-600' : 'text-slate-400'">edit_document</span>
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Ingreso Manual</h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Digita el número de formulario físico que tienes en mano.</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="contractMode === 'manual'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[13px] text-slate-400">confirmation_number</span>
                                Número de Contrato / Formulario
                            </label>
                            <input type="text" name="contract_number"
                                   value="{{ old('contract_number') }}"
                                   placeholder="Ej: 100005"
                                   class="w-full px-4 py-3 rounded-xl border border-purple-300 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════
                     PASO 4 — Documentos Soporte
                ════════════════════════════════════════ --}}
                <div x-show="currentStep === 3"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">D</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sección D — Documentos Soporte</h3>
                            <p class="text-[11px] text-slate-400">Adjunta la documentación física digitalizada del afiliado</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <label class="cursor-pointer block">
                            <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-10 flex flex-col items-center justify-center gap-3 transition duration-200 group bg-slate-50 hover:bg-blue-50/30 relative">
                                <span class="h-14 w-14 rounded-2xl bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-slate-400 group-hover:text-blue-500 text-3xl transition">cloud_upload</span>
                                </span>
                                <div class="text-center">
                                    <p class="font-bold text-slate-600 group-hover:text-blue-600 text-sm transition">Arrastra el archivo aquí o haz clic para buscar</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Formulario o Cédula digitalizada · PDF, PNG, JPG · Máx. 5 MB</p>
                                </div>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full group-hover:bg-blue-100 transition">
                                    Seleccionar archivo
                                </span>
                                <input type="file" name="documento_soporte" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════
                     PASO 5 — Prevalidación JCE/TSS
                ════════════════════════════════════════ --}}
                <div x-show="currentStep === 4"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shrink-0">E</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Sección E — Prevalidación en Tiempo Real</h3>
                            <p class="text-[11px] text-slate-400">Verificación de elegibilidad ante el padrón de Unipago/JCE antes de transmitir</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Panel de prevalidación --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-5">
                            <div class="flex-1 space-y-1.5">
                                <p class="font-bold text-slate-800 text-sm">Consultando ante JCE/TSS/Unipago</p>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    El simulador validará la cédula
                                    <code class="font-mono bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded text-[11px]" x-text="cedula || '—'"></code>
                                    ante el padrón de elegibilidad y te devolverá el resultado en segundos.
                                </p>
                            </div>
                            <button type="button"
                                    @click="prevalidate()"
                                    :disabled="!cedula || prevalidating"
                                    class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition shadow-lg"
                                    :class="!cedula ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none' : 'bg-slate-900 hover:bg-slate-700 text-white shadow-slate-900/20 hover:shadow-slate-900/30'">
                                <template x-if="prevalidating">
                                    <span class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                </template>
                                <template x-if="!prevalidating">
                                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                                </template>
                                <span x-text="prevalidating ? 'Consultando...' : 'Iniciar Prevalidación'"></span>
                            </button>
                        </div>

                        {{-- Sin cédula --}}
                        <div x-show="!cedula"
                             class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-xs font-semibold">
                            <span class="material-symbols-outlined text-[16px]">warning</span>
                            Regresa al Paso 1 e ingresa la cédula del titular para activar la prevalidación.
                        </div>

                        {{-- Resultado --}}
                        <div x-show="prevalResult"
                             x-transition
                             class="border-2 rounded-2xl p-5 flex items-start gap-4 transition"
                             :class="prevalResult?.apto ? 'border-emerald-200 bg-emerald-50/40' : 'border-rose-200 bg-rose-50/40'">
                            <span class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0"
                                  :class="prevalResult?.apto ? 'bg-emerald-100' : 'bg-rose-100'">
                                <span class="material-symbols-outlined text-xl"
                                      :class="prevalResult?.apto ? 'text-emerald-600' : 'text-rose-600'"
                                      x-text="prevalResult?.apto ? 'check_circle' : 'cancel'"></span>
                            </span>
                            <div class="space-y-1.5">
                                <h4 class="font-bold text-sm"
                                    :class="prevalResult?.apto ? 'text-emerald-800' : 'text-rose-800'"
                                    x-text="prevalResult?.apto ? '✓ Ciudadano APTO para Afiliación' : '✗ Ciudadano NO APTO'"></h4>
                                <p class="text-xs font-semibold text-slate-500"
                                   x-text="prevalResult?.motivo_descripcion"></p>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"
                                      :class="prevalResult?.apto ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                      x-text="'Código: ' + (prevalResult?.motivo_codigo ?? '')"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── FOOTER DE NAVEGACIÓN ── --}}
                <div class="flex items-center justify-between pt-2">
                    <button type="button"
                            @click="prev()"
                            :disabled="currentStep === 0"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Anterior
                    </button>

                    <div class="flex items-center gap-3">
                        {{-- Dots de progreso --}}
                        <div class="flex items-center gap-1.5">
                            <template x-for="(s, i) in steps" :key="i">
                                <span class="h-2 rounded-full transition-all duration-300"
                                      :class="currentStep === i ? 'w-5 bg-blue-600' : currentStep > i ? 'w-2 bg-emerald-400' : 'w-2 bg-slate-200'"></span>
                            </template>
                        </div>

                        {{-- Botón Siguiente --}}
                        <button type="button"
                                x-show="currentStep < steps.length - 1"
                                @click="next()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition">
                            Siguiente
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>

                        {{-- Botón Guardar --}}
                        <button type="submit"
                                x-show="currentStep === steps.length - 1"
                                x-transition
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Guardar Borrador
                        </button>
                    </div>
                </div>

            </form>
        </div>{{-- /col-span-3 --}}
    </div>{{-- /grid --}}
</div>

<script>
function formWizard() {
    return {
        currentStep: {{ old('current_step', session('current_step', 0)) }},
        cedula: '{{ old('cedula', '') }}',
        prevalidating: false,
        prevalResult: null,
        steps: [
            { label: 'Datos Personales',  desc: 'Identidad y contacto',       tip: 'Asegúrate de ingresar la cédula exactamente como aparece en el documento oficial del ciudadano.' },
            { label: 'Datos Laborales',   desc: 'Nómina y empleador',         tip: 'El salario cotizable debe corresponder al salario bruto declarado a la TSS por el empleador.' },
            { label: 'Contrato',          desc: 'Número de formulario',        tip: 'La asignación automática garantiza secuencialidad y evita duplicidad en los formularios físicos.' },
            { label: 'Documentos',        desc: 'Adjuntos requeridos',         tip: 'El documento debe ser legible. Se aceptan imágenes tomadas con celular siempre que estén nítidas.' },
            { label: 'Prevalidación',     desc: 'Validación ante JCE/TSS',    tip: 'La prevalidación consulta el padrón oficial de Unipago para determinar si el ciudadano es elegible.' },
        ],

        goToStep(i) {
            if (i <= this.currentStep) this.currentStep = i;
        },
        next() {
            if (this.currentStep < this.steps.length - 1) this.currentStep++;
        },
        prev() {
            if (this.currentStep > 0) this.currentStep--;
        },
        clearPrevalidation() {
            this.prevalResult = null;
        },

        async prevalidate() {
            if (!this.cedula) return;
            this.prevalidating = true;
            this.prevalResult = null;
            try {
                const res = await fetch("/core/unipago-simulador/ejecutar-ws", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        service_code: 'CONSULTA_CIUDADANO',
                        payload: JSON.stringify({ cedula: this.cedula })
                    })
                });
                this.prevalResult = await res.json();
            } catch (err) {
                console.error('Error en prevalidación', err);
            } finally {
                this.prevalidating = false;
            }
        }
    }
}
</script>
@endsection
