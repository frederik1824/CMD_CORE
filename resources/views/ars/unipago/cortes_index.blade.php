@extends('layouts.core')

@section('title', 'Cortes de Dispersión')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Cortes de Dispersión Certificados</h2>
            <p class="text-xs text-slate-500 font-medium">Historial y conciliación de cortes financieros de asignación de fondos por cápitas per cápita.</p>
        </div>
        <a href="{{ route('ars.unipago.dashboard') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la central
        </a>
    </div>

    <!-- Tabla Cortes -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">No. Corte</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Período</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">Tipo de Corte</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Titulares</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Dependientes</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Monto Total</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                    @forelse($cortes as $c)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-[#041e49]">
                                {{ $c->cut_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Dispersado: {{ $c->dispersed_at ? $c->dispersed_at->format('d/m/Y h:i A') : 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-500 font-mono">
                                {{ $c->period }}
                            </td>
                            <td class="px-6 py-4 uppercase text-slate-600 font-semibold">
                                {{ $c->cut_type }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono">{{ $c->total_holders }}</td>
                            <td class="px-6 py-4 text-right font-mono">{{ $c->total_dependents }}</td>
                            <td class="px-6 py-4 text-right font-extrabold text-[#041e49] font-mono">
                                DOP {{ number_format($c->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-bold tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-150">
                                    {{ $c->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se registran cortes de dispersión ejecutados en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $cortes->links() }}
        </div>
    </div>
</div>
@endsection
