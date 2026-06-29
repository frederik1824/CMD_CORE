@extends('layouts.authorization-portal')

@section('title', 'Reclamar Autorización')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 pb-4 border-b border-slate-100">
        <div class="bg-gradient-to-tr from-teal-600 to-teal-400 text-white p-2 rounded-xl shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="leading-none flex-1">
            <h2 class="text-base font-bold text-slate-800">Presentar Reclamación / Facturar</h2>
            <p class="text-[11px] text-slate-400 font-medium">Sometimiento de NCF y factura para liquidación de la autorización {{ $autorizacion->numero_autorizacion }}</p>
        </div>
        <a href="{{ route('pss.solicitudes') }}" class="text-slate-500 hover:text-slate-700 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition">
            Volver a solicitudes
        </a>
    </div>

    <!-- Formulario Factura -->
    <form action="{{ route('pss.reclamaciones.reclamar.store', $autorizacion->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @csrf
        <!-- Izquierda: Formulario (2/3) -->
        <div class="md:col-span-2 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                Datos de Facturación del Prestador
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Número de Factura PSS <span class="text-rose-500">*</span></label>
                    <input type="text" name="invoice_number" required
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                           placeholder="Ej: FAC-0098239">
                </div>

                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">NCF (Comprobante Fiscal) <span class="text-rose-500">*</span></label>
                    <input type="text" name="ncf" required
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                           placeholder="Ej: B0100000005">
                </div>

                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Fecha de Prestación del Servicio <span class="text-rose-500">*</span></label>
                    <input type="date" name="service_date" required max="{{ date('Y-m-d') }}"
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Monto Total a Reclamar (DOP) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="claimed_amount" value="{{ $autorizacion->monto_contratado ?: $autorizacion->monto_solicitado }}" required
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs font-bold text-slate-850 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <span class="block text-[10px] text-slate-400 mt-1">Tarifa negociada/autorizada: DOP {{ number_format($autorizacion->monto_contratado ?: $autorizacion->monto_solicitado, 2) }}</span>
                </div>
            </div>

            <!-- Carga Documento -->
            <div class="border border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50 space-y-2 text-xs">
                <div>
                    <p class="font-semibold text-slate-600">Adjuntar Factura Firmada / Soporte Digital</p>
                    <p class="text-[10px] text-slate-400">Requerido para la conciliación y auditoría física de comprobación.</p>
                </div>
                <input type="file" name="documento_factura"
                       class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-50">
                <button type="submit" class="bg-teal-600 text-white rounded-full px-6 py-2.5 font-bold hover:bg-teal-700 transition shadow-xs text-xs">
                    Someter Reclamación ARS
                </button>
            </div>
        </div>

        <!-- Derecha: Resumen Autorización (1/3) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4 self-start text-xs">
            <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider border-b border-slate-50 pb-2">
                Resumen de Autorización
            </h3>

            <div class="space-y-3">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Afiliado Paciente</span>
                    <span class="font-bold text-slate-800">{{ $afiliado->nombres }} {{ $afiliado->primer_apellido ?? $afiliado->apellidos }}</span>
                </div>

                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Procedimiento / Cobertura</span>
                    <span class="font-bold text-slate-800">{{ $autorizacion->procedimiento }}</span>
                    <span class="block text-[10px] font-mono text-slate-500">SIMON: {{ $autorizacion->simon_code_snapshot ?: 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Monto Autorizado</span>
                    <span class="font-extrabold text-[#0f766e] text-sm font-mono">DOP {{ number_format($autorizacion->monto_contratado ?: $autorizacion->monto_solicitado, 2) }}</span>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
