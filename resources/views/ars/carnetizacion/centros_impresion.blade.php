@extends('layouts.ars')

@section('title', 'Centros de Impresión')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Centros de Impresión de Carnets</h2>
            <p class="text-xs text-slate-500 font-medium">Configuración de terminales de impresión, centros de despacho y logística de suministros de la ARS.</p>
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
        <!-- Registrar Centro -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Oficina de Impresión</h3>
            <form action="{{ route('ars.carnetizacion.guardar_centro_impresion') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre del Centro <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" placeholder="Ej. Centro de Impresión Metropolitana" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Ubicación Sucursal</label>
                    <input type="text" name="location" placeholder="Ciudad o sucursal..." class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs">
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Registrar Terminal</button>
            </form>
        </div>

        <!-- Listado de Centros -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Terminales e Impresoras Activas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Nombre de Oficina</th>
                            <th class="px-4 py-3 text-left">Sucursal</th>
                            <th class="px-4 py-3 text-mono text-center">Fecha Alta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($centros as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $c->id }}</td>
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $c->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $c->location ?? 'N/D' }}</td>
                                <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $c->created_at ? $c->created_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han configurado centros de impresión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
