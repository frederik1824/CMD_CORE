@extends('layouts.ars')

@section('title', 'Prestadoras Jurídicas')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Prestadoras Jurídicas (Clínicas, Hospitales y Centros)</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de centros médicos, clínicas de especialidades, laboratorios de diagnóstico y farmacias afiliadas.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Prestador Jurídico -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Centro Médico</h3>
            <form action="{{ route('ars.prestadores.guardar') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="pss_nature" value="Jurídica">
                
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Razón Social / Nombre <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" placeholder="Ej. Clínica Metropolitana Dominicana" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">RNC <span class="text-rose-500">*</span></label>
                        <input type="text" name="rnc" placeholder="RNC del Centro" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Centro <span class="text-rose-500">*</span></label>
                        <select name="tipo_entidad" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                            <option value="Clínica">Clínica</option>
                            <option value="Hospital">Hospital</option>
                            <option value="Laboratorio">Laboratorio Clínico</option>
                            <option value="Farmacia">Farmacia</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Teléfono Principal</label>
                        <input type="text" name="telefono" placeholder="809-555-0100" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Correo Corporativo</label>
                        <input type="email" name="correo" placeholder="contacto@clinicamd.com.do" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Dirección Fiscal / Sede</label>
                    <textarea name="direccion" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Dirección completa del centro..."></textarea>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Registrar Centro</button>
            </form>
        </div>

        <!-- Listado de Centros -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Centros Médicos Registrados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Centro Médico</th>
                            <th class="px-4 py-3 text-left">RNC</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">Contacto</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($prestadores as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $p->nombre }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $p->rnc }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $p->tipo_entidad }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="block">{{ $p->telefono ?? 'S/T' }}</span>
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $p->correo ?? 'S/C' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado centros médicos en la red.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
