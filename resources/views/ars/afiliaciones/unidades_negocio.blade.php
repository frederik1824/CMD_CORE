@extends('layouts.ars')

@section('title', 'Unidades de Negocio')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Unidades de Negocio</h2>
            <p class="text-xs text-slate-500 font-medium">Administración de sucursales o divisiones corporativas vinculadas a los contratos.</p>
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
        <!-- Registrar Unidad -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Nueva Unidad</h3>
            <form action="{{ route('ars.afiliaciones.guardar_unidad_negocio') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre de la Unidad <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" placeholder="Ej. Sucursal Metropolitana" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Ubicación</label>
                    <input type="text" name="location" placeholder="Ciudad o dirección..." class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Crear Unidad</button>
            </form>
        </div>

        <!-- Listado de Unidades -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Unidades Registradas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Ubicación</th>
                            <th class="px-4 py-3 text-mono text-center">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($unidades as $u)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $u->id }}</td>
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $u->location ?? 'N/D' }}</td>
                                <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $u->created_at ? $u->created_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado unidades de negocio.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
