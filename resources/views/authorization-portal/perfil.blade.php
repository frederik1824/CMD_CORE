@extends('layouts.authorization-portal')

@section('title', 'Mi Perfil PSS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-2xl font-bold leading-7 text-slate-800 tracking-tight">Mi Perfil & Contrato Comercial</h2>
        <p class="mt-1 text-sm text-slate-500 font-medium">Información institucional del prestador y listado de tarifas médicas convenidas.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ficha Prestadora -->
        <div class="md:col-span-1 bg-white p-6 shadow-sm rounded-3xl border border-slate-200 space-y-4 text-xs">
            <div class="text-center pb-4 border-b border-slate-100">
                <div class="inline-flex h-14 w-14 rounded-2xl bg-teal-50 text-teal-600 items-center justify-center font-bold text-xl mb-3 shadow-md">
                    {{ substr($pss->nombre, 0, 1) }}
                </div>
                <h3 class="text-sm font-bold text-slate-800 block">{{ $pss->nombre }}</h3>
                <span class="text-slate-400 block mt-0.5">RNC: <span class="font-mono font-semibold text-slate-600">{{ $pss->rnc }}</span></span>
            </div>
            
            <div class="space-y-3">
                <div>
                    <span class="font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tipo de PSS</span>
                    <span class="text-slate-700 font-semibold">{{ $pss->tipo_entidad }}</span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Contacto</span>
                    <span class="text-slate-600 block">Tel: {{ $pss->telefono }}</span>
                    <span class="text-slate-600 block mt-0.5">Email: {{ $pss->correo }}</span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Dirección</span>
                    <span class="text-slate-600 block leading-normal">{{ $pss->direccion }}</span>
                </div>
            </div>
        </div>

        <!-- Ficha Tarifas Contratadas -->
        <div class="md:col-span-2 bg-white shadow-sm rounded-3xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tarifas Médicas Acordadas</h3>
                <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded border border-teal-100 font-mono">
                    {{ $contrato ? $contrato->numero_contrato : 'SIN CONTRATO' }}
                </span>
            </div>
            
            @if($contrato)
                <div class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto pr-2">
                    @foreach($contrato->tarifas as $tarifa)
                        <div class="p-5 flex items-center justify-between text-xs hover:bg-slate-50 transition">
                            <div>
                                <span class="font-mono font-bold text-slate-400 block">{{ $tarifa->servicio->codigo }}</span>
                                <span class="font-bold text-slate-700 block mt-1">{{ $tarifa->servicio->descripcion }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Cobertura Base: {{ $tarifa->servicio->cobertura_base }}%</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Monto Tarifa</span>
                                <span class="text-sm font-bold text-slate-800">${{ number_format($tarifa->monto_tarifa, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-[10px] text-slate-400 text-center font-mono">
                    Vigencia: {{ $contrato->fecha_inicio->format('d/m/Y') }} al {{ $contrato->fecha_fin->format('d/m/Y') }}
                </div>
            @else
                <div class="p-12 text-center text-slate-400">
                    No se encuentra ningún contrato activo vigente asociado a esta prestadora.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
