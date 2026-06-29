@extends('layouts.ars')

@section('title', 'Bandeja de Solicitudes de Afiliación')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight sm:text-3xl">
                Bandeja de Solicitudes Individuales (Titulares)
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Monitoreo de solicitudes en borrador, transmitidas y auditadas ante Unipago.
            </p>
        </div>
        <a href="{{ route('ars.solicitudes.titulares.nueva') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition duration-200">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Nuevo Titular
        </a>
    </div>

    <!-- Pestañas de Estados -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6">
            @foreach(['borrador' => 'Borradores', 'enviado_unipago' => 'Enviados a Unipago', 'procesado_ok' => 'Aprobados (OK)', 'rechazado_re' => 'Rechazados (RE)'] as $k => $label)
                <a href="{{ route('ars.solicitudes.titulares.index', ['status' => $k]) }}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition {{ $status === $k ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Tabla de Solicitudes -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Número Solicitud</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Cédula</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nombre Completo</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Contrato/Formulario</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Fecha Creación</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Creado Por</th>
                        <th scope="col" class="relative px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($solicitudes as $sol)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-slate-800">
                                <a href="{{ route('ars.solicitudes.titulares.show', $sol->id) }}" class="hover:text-brand-600 transition">{{ $sol->request_number }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-mono">
                                {{ $sol->affiliate?->cedula ?? 'N/D' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                                {{ $sol->affiliate?->nombre_completo ?? 'N/D' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-mono">
                                {{ $sol->contract_number ?? 'Ninguno' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400 font-mono">
                                {{ $sol->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $sol->creator?->name ?? 'Sistema' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                <a href="{{ route('ars.solicitudes.titulares.show', $sol->id) }}" class="text-brand-600 hover:text-brand-800 transition p-2 rounded-full hover:bg-slate-100 inline-flex items-center justify-center" title="Ver Detalle de Solicitud">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">No hay solicitudes registradas con este estado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($solicitudes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
