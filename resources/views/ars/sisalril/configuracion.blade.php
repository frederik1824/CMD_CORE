@extends('layouts.ars')

@section('title', 'Configuración Entidad')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Configuración de Entidad Regulatoria</h2>
            <p class="text-xs text-slate-500 font-medium">Parámetros corporativos y firmas electrónicas que alimentan los encabezados oficiales de los archivos regulatorios.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs max-w-2xl">
        <form action="{{ route('sisalril.guardar_configuracion') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Institución <span class="text-rose-500">*</span></label>
                    <input type="text" name="tipo_institucion" value="{{ $config['tipo_institucion'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código de Institución <span class="text-rose-500">*</span></label>
                    <input type="text" name="codigo_institucion" value="{{ $config['codigo_institucion'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Siglas de la ARS <span class="text-rose-500">*</span></label>
                    <input type="text" name="sigla" value="{{ $config['sigla'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre Oficial de la ARS <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" value="{{ $config['nombre'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">RNC Corporativo <span class="text-rose-500">*</span></label>
                    <input type="text" name="rnc" value="{{ $config['rnc'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código SISALRIL <span class="text-rose-500">*</span></label>
                    <input type="text" name="codigo_sisalril" value="{{ $config['codigo_sisalril'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código SIMON <span class="text-rose-500">*</span></label>
                    <input type="text" name="codigo_simon" value="{{ $config['codigo_simon'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Responsable Técnico <span class="text-rose-500">*</span></label>
                    <input type="text" name="responsable_tecnico" value="{{ $config['responsable_tecnico'] }}" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 focus:bg-white focus:outline-none text-xs" required>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5 flex justify-end">
                <button type="submit" class="bg-[#041e49] hover:bg-slate-800 text-white rounded-full px-6 py-2.5 font-bold shadow-xs transition text-xs">
                    Guardar Configuración Entidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
