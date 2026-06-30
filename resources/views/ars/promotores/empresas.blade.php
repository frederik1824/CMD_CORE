@extends('layouts.ars')
@section('title', 'Promotores Corporativos')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Agencias Comerciales</h2>
            <p class="text-xs text-slate-500 font-medium">Mantenimiento de agencias de corretaje y promotores jurídicos.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Agencias Registradas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Razón Social</th>
                            <th class="px-4 py-3 text-left">RNC</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($promotores as $pr)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $pr->name }}</td>
                                <td class="px-4 py-3 font-mono font-semibold">{{ $pr->identification_number }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $pr->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-slate-400">No hay agencias comerciales jurídicas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Registrar Agencia</h3>
            <form action="{{ route('ars.promotores.guardar') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="promoter_type" value="empresa">
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Razón Social</label>
                    <input type="text" name="name" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Corredores Asociados SRL" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">RNC</label>
                    <input type="text" name="identification_number" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 font-mono" placeholder="101-00000-0" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Guardar Agencia</button>
            </form>
        </div>
    </div>
</div>
@endsection