@extends('layouts.ars')

@section('title', 'Auditores Médicos')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Auditores Médicos</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de personal médico responsable de autorizaciones concurrentes, glosas y auditoría clínica.</p>
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
        <!-- Registrar Auditor -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Auditor Clínico</h3>
            <form action="{{ route('ars.prestadores.guardar_auditor') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Código Auditor <span class="text-rose-500">*</span></label>
                    <input type="text" name="auditor_code" placeholder="Ej. AUD-MED-05" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Exequátur Clínico <span class="text-rose-500">*</span></label>
                    <input type="text" name="exequatur" placeholder="Ej. EX-9838" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Profesional / Especialidad <span class="text-rose-500">*</span></label>
                    <select name="professional_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        <option value="Auditor General">Auditor General</option>
                        <option value="Auditor de Alto Costo">Auditor de Alto Costo</option>
                        <option value="Auditor Farmacéutico">Auditor Farmacéutico</option>
                        <option value="Inspector Clínico">Inspector Clínico</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Registrar Auditor</button>
            </form>
        </div>

        <!-- Listado de Auditores -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Auditores Médicos Registrados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código Auditor</th>
                            <th class="px-4 py-3 text-left">Exequátur</th>
                            <th class="px-4 py-3 text-left">Tipo Especialidad</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($auditores as $aud)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-[#041e49] font-bold">{{ $aud->auditor_code }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800 font-mono">{{ $aud->exequatur }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $aud->professional_type }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $aud->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado auditores clínicos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
