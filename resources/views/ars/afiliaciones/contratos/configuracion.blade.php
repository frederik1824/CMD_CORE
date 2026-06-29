@extends('layouts.ars')

@section('title', 'Configuración de Formularios')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                Configuración General de Contratos y Formularios
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Ajusta las políticas de reserva temporal, obligatoriedad del contrato e integraciones.
            </p>
        </div>
        <a href="{{ route('ars.contract_control.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-655 bg-white hover:bg-slate-50 transition">
            Dashboard
        </a>
    </div>

    <!-- Formulario de Configuración (Google Material 3) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('ars.contract_control.config.save') }}" method="POST" class="space-y-5 text-xs font-semibold text-slate-700">
            @csrf

            <!-- Obligatoriedad -->
            <div class="space-y-3.5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Políticas de Obligatoriedad</span>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="require_contract_for_holder" id="require_contract_for_holder" value="1" {{ $config['require_contract_for_holder'] ? 'checked' : '' }}
                           class="rounded text-brand-600 border-slate-300 focus:ring-brand-500 w-4 h-4">
                    <label for="require_contract_for_holder" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                        Requerir obligatoriamente número de formulario para **Titulares** (Carga individual y masiva)
                    </label>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-200 pt-3">
                    <input type="checkbox" name="require_contract_for_dependent" id="require_contract_for_dependent" value="1" {{ $config['require_contract_for_dependent'] ? 'checked' : '' }}
                           class="rounded text-brand-600 border-slate-300 focus:ring-brand-500 w-4 h-4">
                    <label for="require_contract_for_dependent" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                        Requerir obligatoriamente número de formulario independiente para **Dependientes**
                    </label>
                </div>
            </div>

            <!-- Parámetros de Reserva -->
            <div>
                <label for="default_reservation_minutes" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tiempo de Expiración de Reserva (Minutos)</label>
                <input type="number" name="default_reservation_minutes" id="default_reservation_minutes" min="1" max="1440" value="{{ $config['default_reservation_minutes'] }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none placeholder:text-slate-450 bg-white">
                <p class="mt-1 text-[9.5px] text-slate-450 font-normal">Plazo en que un número reservado queda bloqueado antes de liberarse automáticamente si la transacción no concluye.</p>
            </div>

            <!-- Reutilización RE -->
            <div class="space-y-3.5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Políticas de Rechazo Unipago</span>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="allow_reuse_rejected" id="allow_reuse_rejected" value="1" {{ $config['allow_reuse_rejected'] ? 'checked' : '' }}
                           class="rounded text-brand-600 border-slate-300 focus:ring-brand-500 w-4 h-4">
                    <label for="allow_reuse_rejected" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                        Permitir la reutilización manual de contratos en estado **Rechazado (RE)** (No recomendado por auditoría)
                    </label>
                </div>
            </div>

            <!-- Alerta de Agotamiento -->
            <div>
                <label for="alert_threshold_available" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Umbral de Alerta por Cantidad Disponible (Contratos)</label>
                <input type="number" name="alert_threshold_available" id="alert_threshold_available" min="10" value="{{ $config['alert_threshold_available'] }}" required
                       class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none bg-white">
                <p class="mt-1 text-[9.5px] text-slate-450 font-normal">Dispara una alerta en el dashboard general cuando los contratos disponibles caigan por debajo de este límite.</p>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex justify-end gap-3.5 border-t border-slate-200">
                <a href="{{ route('ars.contract_control.dashboard') }}" class="px-5 py-2.5 border border-slate-300 rounded-full text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-full text-white font-bold text-xs bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
