@extends('layouts.ars')

@section('title', 'Detalle de Rango de Contratos')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Rango: {{ $rango->range_code }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $rango->description }} • Límites: <span class="font-mono text-slate-700 font-bold">[{{ $rango->start_number }} - {{ $rango->end_number }}]</span>
            </p>
        </div>
        <div class="mt-4 flex gap-3 sm:mt-0 sm:ml-4">
            <form action="{{ route('ars.contract_control.ranges.status', $rango->id) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="status" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700 bg-white">
                    <option value="activo" {{ $rango->status === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="suspendido" {{ $rango->status === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    <option value="cerrado" {{ $rango->status === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    <option value="anulado" {{ $rango->status === 'anulado' ? 'selected' : '' }}>Anulado</option>
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                    Cambiar Estado
                </button>
            </form>
            <a href="{{ route('ars.contract_control.ranges.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Volver
            </a>
        </div>
    </div>

    <!-- Sistema de Pestañas Google Workspace Style -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="border-b border-slate-200 bg-slate-50 px-6 flex items-center space-x-1.5 overflow-x-auto text-xs font-bold text-slate-500 uppercase tracking-wider select-none h-14 shrink-0">
            <a href="{{ route('ars.contract_control.ranges.show', ['id' => $rango->id, 'tab' => 'resumen']) }}" 
               class="px-4 py-2.5 rounded-full transition {{ $tab === 'resumen' ? 'bg-brand-50 text-brand-700 font-extrabold' : 'hover:bg-slate-100 hover:text-slate-800' }}">
                Resumen
            </a>
            <a href="{{ route('ars.contract_control.ranges.show', ['id' => $rango->id, 'tab' => 'numeros']) }}" 
               class="px-4 py-2.5 rounded-full transition {{ $tab === 'numeros' ? 'bg-brand-50 text-brand-700 font-extrabold' : 'hover:bg-slate-100 hover:text-slate-800' }}">
                Números
            </a>
            <a href="{{ route('ars.contract_control.ranges.show', ['id' => $rango->id, 'tab' => 'consumo']) }}" 
               class="px-4 py-2.5 rounded-full transition {{ $tab === 'consumo' ? 'bg-brand-50 text-brand-700 font-extrabold' : 'hover:bg-slate-100 hover:text-slate-800' }}">
                Consumo
            </a>
            <a href="{{ route('ars.contract_control.ranges.show', ['id' => $rango->id, 'tab' => 'auditoria']) }}" 
               class="px-4 py-2.5 rounded-full transition {{ $tab === 'auditoria' ? 'bg-brand-50 text-brand-700 font-extrabold' : 'hover:bg-slate-100 hover:text-slate-800' }}">
                Auditoría
            </a>
        </div>

        <div class="p-6">
            <!-- 1. TAB RESUMEN -->
            @if($tab === 'resumen')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Detalle Administrativo -->
                    <div class="space-y-4 md:col-span-2 text-xs font-semibold text-slate-700">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detalles Administrativos</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Referencia Aprobación</span>
                                <span class="block text-slate-800 mt-0.5">{{ $rango->approval_reference ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Aprobado Por</span>
                                <span class="block text-slate-800 mt-0.5">{{ $rango->approved_by }} ({{ $rango->approved_at ? $rango->approved_at->format('d/m/Y') : '—' }})</span>
                            </div>
                            <div>
                                <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Vigencia</span>
                                <span class="block text-slate-800 mt-0.5">
                                    Desde: {{ $rango->valid_from ? $rango->valid_from->format('d/m/Y') : '—' }}<br>
                                    Hasta: {{ $rango->valid_until ? $rango->valid_until->format('d/m/Y') : '—' }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Origen de Datos</span>
                                <span class="block text-slate-800 mt-0.5 uppercase">{{ $rango->source }}</span>
                            </div>
                        </div>
                        @if($rango->observations)
                            <div class="pt-2">
                                <span class="block text-[8.5px] font-bold text-slate-400 uppercase">Observaciones</span>
                                <p class="text-slate-500 font-medium mt-1 leading-relaxed">{{ $rango->observations }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Gráfico / Progresos circulares -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-4 text-xs font-semibold text-slate-700">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Estadísticas del Rango</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                    <span>Disponible ({{ $rango->available_count }} / {{ $rango->total_numbers }})</span>
                                    <span>{{ number_format(($rango->available_count / $rango->total_numbers) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ ($rango->available_count / $rango->total_numbers) * 100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                    <span>Consumido (OK / RE / PE)</span>
                                    <span>{{ number_format((($rango->ok_count + $rango->rejected_count + $rango->pending_count) / $rango->total_numbers) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-brand-600 h-1.5 rounded-full" style="width: {{ (($rango->ok_count + $rango->rejected_count + $rango->pending_count) / $rango->total_numbers) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. TAB NÚMEROS INDIVIDUALES -->
            @if($tab === 'numeros')
                <div class="space-y-4">
                    <!-- Filtros locales -->
                    <form action="{{ route('ars.contract_control.ranges.show', $rango->id) }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs font-semibold text-slate-700 items-end bg-slate-50 p-4 rounded-xl border border-slate-150">
                        <input type="hidden" name="tab" value="numeros">
                        <div class="sm:col-span-2">
                            <label class="block text-[8.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Buscar Número</label>
                            <input type="text" name="search_number" value="{{ $searchNumber }}" placeholder="Ej: 45001..." class="block w-full rounded-lg border border-slate-300 py-1.5 px-3 text-xs text-slate-800 bg-white">
                        </div>
                        <div>
                            <label class="block text-[8.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estado</label>
                            <select name="status_filter" class="block w-full rounded-lg border border-slate-300 py-1.5 px-3 text-xs text-slate-850 bg-white">
                                <option value="">Todos</option>
                                <option value="disponible" {{ $statusFilter === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="reservado" {{ $statusFilter === 'reservado' ? 'selected' : '' }}>Reservado</option>
                                <option value="usado" {{ $statusFilter === 'usado' ? 'selected' : '' }}>Usado</option>
                                <option value="enviado_unipago" {{ $statusFilter === 'enviado_unipago' ? 'selected' : '' }}>Enviado a Unipago</option>
                                <option value="ok" {{ $statusFilter === 'ok' ? 'selected' : '' }}>Aceptado OK</option>
                                <option value="re" {{ $statusFilter === 're' ? 'selected' : '' }}>Rechazado RE</option>
                                <option value="bloqueado" {{ $statusFilter === 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-600 text-white font-bold hover:bg-brand-700 transition">Filtrar</button>
                            <a href="{{ route('ars.contract_control.ranges.show', ['id' => $rango->id, 'tab' => 'numeros']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 bg-white transition">Limpiar</a>
                        </div>
                    </form>

                    <!-- Listado de números -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Número</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Asignado a</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Última Modificación</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-400 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-xs">
                                @forelse($numeros as $num)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-3 font-mono font-bold text-slate-800">
                                            {{ $num->contract_number }}
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase {{ 
                                                $num->status === 'disponible' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : (
                                                $num->status === 'reservado' ? 'bg-blue-50 text-blue-700 border-blue-100' : (
                                                $num->status === 'ok' ? 'bg-emerald-150 text-emerald-800 border-emerald-250' : (
                                                $num->status === 're' ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-slate-100 text-slate-500 border-slate-200')))
                                            }}">
                                                {{ $num->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-slate-500 font-semibold">
                                            @if($num->assigned_to_affiliate_id)
                                                <a href="{{ route('ars.titulares.show', $num->assigned_to_affiliate_id) }}" class="text-brand-600 hover:underline">
                                                    {{ optional($num->affiliate)->nombre_completo ?? 'Afiliado ID: '.$num->assigned_to_affiliate_id }}
                                                </a>
                                            @elseif($num->assigned_to_user_id)
                                                Reservado (Usuario ID: {{ $num->assigned_to_user_id }})
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 font-mono text-slate-400">
                                            {{ $num->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if($num->status === 'disponible' || $num->status === 'reservado')
                                                <!-- Formulario Bloqueo -->
                                                <form action="{{ route('ars.contract_control.numbers.block', $num->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de que desea bloquear este número de formulario? No se podrá utilizar.')">
                                                    @csrf
                                                    <input type="hidden" name="block_reason" value="Bloqueo administrativo manual">
                                                    <button type="submit" class="text-rose-600 hover:text-rose-750 font-bold hover:underline">Bloquear</button>
                                                </form>
                                            @endif
                                            @if($num->status === 'reservado')
                                                <!-- Formulario Liberación -->
                                                <form action="{{ route('ars.contract_control.numbers.release', $num->id) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('¿Desea liberar esta reserva de formulario para que vuelva a estar disponible?')">
                                                    @csrf
                                                    <button type="submit" class="text-brand-600 hover:text-brand-700 font-bold hover:underline">Liberar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">No se encontraron números con los criterios seleccionados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($numeros->hasPages())
                        <div class="pt-2">
                            {{ $numeros->appends(['tab' => 'numeros', 'search_number' => $searchNumber, 'status_filter' => $statusFilter])->links() }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- 3. TAB CONSUMO -->
            @if($tab === 'consumo')
                <div class="space-y-5 text-xs font-semibold text-slate-700">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Desglose de Uso del Rango</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <div class="border border-slate-200 rounded-xl p-5 space-y-4">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b pb-2">Contadores Generales</span>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Total Bloque Completo:</span>
                                    <strong class="font-mono text-slate-800">{{ $rango->total_numbers }}</strong>
                                </div>
                                <div class="flex justify-between items-center border-t pt-2.5">
                                    <span class="text-slate-500">Disponible:</span>
                                    <strong class="font-mono text-emerald-600 font-bold">{{ $rango->available_count }}</strong>
                                </div>
                                <div class="flex justify-between items-center border-t pt-2.5">
                                    <span class="text-slate-500">Aceptados (OK):</span>
                                    <strong class="font-mono text-brand-600 font-bold">{{ $rango->ok_count }}</strong>
                                </div>
                                <div class="flex justify-between items-center border-t pt-2.5">
                                    <span class="text-slate-500">Rechazados (RE):</span>
                                    <strong class="font-mono text-rose-600 font-bold">{{ $rango->rejected_count }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 4. TAB AUDITORÍA / MOVIMIENTOS -->
            @if($tab === 'auditoria')
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Trazabilidad del Rango</h3>
                    
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Contrato</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Tipo Movimiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Transición</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Fecha / Auditor</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-xs">
                                @forelse($movimientos as $mv)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-3 font-mono font-bold text-slate-800">
                                            {{ $mv->number->contract_number }}
                                        </td>
                                        <td class="px-6 py-3 uppercase text-[9.5px] font-bold text-slate-500">
                                            {{ $mv->movement_type }}
                                        </td>
                                        <td class="px-6 py-3 font-mono text-[9px] text-slate-550">
                                            {{ $mv->old_status ?: 'null' }} &rarr; <span class="font-bold text-slate-800">{{ $mv->new_status }}</span>
                                        </td>
                                        <td class="px-6 py-3 leading-normal">
                                            <span class="font-mono text-slate-400 block">{{ $mv->created_at->format('d/m/Y H:i') }}</span>
                                            <span class="text-slate-500 font-bold block text-[10px]">{{ optional($mv->user)->name ?: 'Sistema' }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-slate-500 font-medium">
                                            {{ $mv->description }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">No se registran movimientos ni transacciones históricas en este rango.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($movimientos->hasPages())
                        <div class="pt-2">
                            {{ $movimientos->appends(['tab' => 'auditoria'])->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
