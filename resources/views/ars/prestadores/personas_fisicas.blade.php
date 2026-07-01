@extends('layouts.ars')

@section('title', 'Prestadoras Físicas')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Prestadoras Físicas (Médicos Especialistas)</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja maestra y registro de médicos, especialistas e inspectores independientes de la red.</p>
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
        <!-- Registrar Prestador Físico -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Médico / Profesional</h3>
            <form action="{{ route('ars.prestadores.guardar') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="pss_nature" value="Física">
                
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre Completo <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" placeholder="Ej. Dr. Carlos Martínez" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Cédula / RNC <span class="text-rose-500">*</span></label>
                        <input type="text" name="rnc" placeholder="RNC o Cédula" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Especialidad <span class="text-rose-500">*</span></label>
                        <select name="tipo_entidad" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                            <option value="Médico General">Médico General</option>
                            <option value="Cardiólogo">Cardiólogo</option>
                            <option value="Ortopeda">Ortopeda</option>
                            <option value="Pediatra">Pediatra</option>
                            <option value="Ginecólogo">Ginecólogo</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Teléfono</label>
                        <input type="text" name="telefono" placeholder="809-555-0199" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Correo</label>
                        <input type="email" name="correo" placeholder="correo@ejemplo.com" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Dirección Consultorio</label>
                    <textarea name="direccion" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Ubicación física..."></textarea>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Registrar Profesional</button>
            </form>
        </div>

        <!-- Listado de Médicos -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Médicos Especialistas Registrados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Médico</th>
                            <th class="px-4 py-3 text-left">Cédula / RNC</th>
                            <th class="px-4 py-3 text-left">Especialidad</th>
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
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado médicos en la red.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
