@extends('layouts.core')

@section('title', 'Nueva Solicitud de Reembolso')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0b57d0]/10 text-[#0b57d0] border border-[#0b57d0]/20 uppercase tracking-wider">
                    📄 Apertura de Expediente
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Nueva Solicitud de Reembolso</h2>
            <p class="text-xs text-slate-500 font-medium">Ingrese los datos generales de la prestación y la factura para iniciar la validación de reembolsos.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.reembolsos.index') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                Cancelar
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="max-w-3xl bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.reembolsos.store') }}" method="POST" class="space-y-6 text-xs font-medium text-slate-600">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Afiliado Solicitante</label>
                    <select name="afiliado_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        <option value="">-- Seleccionar afiliado --</option>
                        @foreach($afiliados as $af)
                            <option value="{{ $af->id }}" {{ old('afiliado_id') == $af->id ? 'selected' : '' }}>
                                {{ $af->nombre_completo }} (Ced: {{ $af->cedula }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">PSS Prestadora</label>
                    <select name="pss_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        <option value="">-- Seleccionar PSS --</option>
                        @foreach($pssList as $pss)
                            <option value="{{ $pss->id }}" {{ old('pss_id') == $pss->id ? 'selected' : '' }}>
                                {{ $pss->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Solicitud</label>
                    <select name="request_type" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        @foreach($tipos as $tp)
                            <option value="{{ $tp }}" {{ old('request_type') == $tp ? 'selected' : '' }}>
                                @if($tp === 'cobro_indebido')
                                    Cobro Indebido PSS (Genera débito)
                                @elseif($tp === 'negacion_cobertura')
                                    Negación de Cobertura ARS
                                @else
                                    Ambos
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Monto Solicitado</label>
                    <input type="number" step="0.01" name="requested_amount" required value="{{ old('requested_amount') }}"
                           class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5"
                           placeholder="DOP 0.00">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha del Servicio</label>
                    <input type="date" name="service_date" required value="{{ old('service_date', date('Y-m-d')) }}"
                           class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha del Pago / Factura</label>
                    <input type="date" name="payment_date" required value="{{ old('payment_date', date('Y-m-d')) }}"
                           class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                    <span class="text-[9px] text-slate-400 mt-1 block">El plazo máximo es de 120 días calendario a partir de esta fecha.</span>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Canal de Recepción</label>
                    <select name="request_channel" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        @foreach($canales as $cn)
                            <option value="{{ $cn }}" {{ old('request_channel') == $cn ? 'selected' : '' }}>
                                {{ ucfirst($cn) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Origen / Ente Remitente</label>
                    <select name="origin" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        @foreach($origenes as $or)
                            <option value="{{ $or }}" {{ old('origin') == $or ? 'selected' : '' }}>
                                {{ strtoupper($or) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="w-full bg-[#0b57d0] text-white rounded-full py-3 font-bold hover:bg-[#083d91] transition shadow-xs text-center block">
                    Registrar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
