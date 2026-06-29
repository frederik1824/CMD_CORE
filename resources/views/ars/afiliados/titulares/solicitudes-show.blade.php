@extends('layouts.ars')

@section('title', 'Detalle de Solicitud de Afiliación')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight sm:text-3xl">
                Solicitud {{ $solicitud->request_number }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Creado por {{ $solicitud->creator?->name ?? 'Sistema' }} el {{ $solicitud->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ 
                $solicitud->status === 'procesado_ok' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (
                $solicitud->status === 'enviado_unipago' ? 'bg-blue-50 text-blue-700 border border-blue-200' : (
                $solicitud->status === 'rechazado_re' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200'))
            }}">
                {{ str_replace('_', ' ', $solicitud->status) }}
            </span>
        </div>
    </div>

    <!-- Main Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left Side: Solicitud Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos Personales -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Sección A: Datos Personales</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Nombre Completo</span>
                        <span class="font-bold text-slate-700 text-sm">{{ $solicitud->affiliate?->nombre_completo }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Cédula</span>
                        <span class="font-semibold text-slate-700 font-mono">{{ $solicitud->affiliate?->cedula }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">NSS</span>
                        <span class="font-semibold text-slate-700 font-mono">{{ $solicitud->affiliate?->nss ?: 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Fecha Nacimiento</span>
                        <span class="font-semibold text-slate-700">{{ $solicitud->affiliate?->fecha_nacimiento?->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Sexo</span>
                        <span class="font-semibold text-slate-700">{{ $solicitud->affiliate?->sexo === 'M' ? 'Masculino' : 'Femenino' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Provincia / Municipio</span>
                        <span class="font-semibold text-slate-700">{{ $solicitud->affiliate?->provincia }} / {{ $solicitud->affiliate?->municipio }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos Laborales -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Sección B: Datos Laborales</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Nombre Empleador</span>
                        <span class="font-semibold text-slate-700">{{ $solicitud->employer_name ?: 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">RNC Empleador</span>
                        <span class="font-semibold text-slate-700 font-mono">{{ $solicitud->employer_rnc ?: 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Salario Cotizable</span>
                        <span class="font-semibold text-slate-700 font-mono">DOP {{ number_format($solicitud->salary_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Tipo Régimen</span>
                        <span class="font-semibold text-slate-700">{{ $solicitud->regime_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos de Contrato -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Sección C: Contrato / Formulario</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Número de Formulario</span>
                        <span class="font-bold text-slate-700 font-mono">{{ $solicitud->contract_number ?: 'Sin Asignar' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase mb-1">Estado Contrato</span>
                        <span class="font-semibold text-slate-700 capitalize">{{ $solicitud->contractNumber?->status ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Actions & Status Timeline -->
        <div class="space-y-6">
            <!-- Acciones -->
            @if($solicitud->status === 'borrador')
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Acciones Disponibles</h4>
                    <form action="{{ route('ars.solicitudes.titulares.enviar', $solicitud->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 transition">
                            <span class="material-symbols-outlined text-lg">send</span>
                            Transmitir a Unipago
                        </button>
                    </form>
                </div>
            @endif

            <!-- Respuesta del Simulador -->
            @if($solicitud->unipago_response_code)
                <div class="bg-slate-900 border border-slate-800 text-white rounded-3xl p-6 shadow-2xl space-y-4">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400">Respuesta de Unipago</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold font-mono text-xs px-2 py-0.5 rounded {{ $solicitud->unipago_response_code === 'OK' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                                {{ $solicitud->unipago_response_code }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $solicitud->processed_at?->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-xs font-semibold leading-relaxed text-slate-300">{{ $solicitud->unipago_response_message }}</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
