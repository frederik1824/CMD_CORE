@extends('layouts.ars')

@section('title', 'Vista Previa de Carga Masiva')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in font-sans" x-data="{ activeFilter: 'all' }">
    
    <!-- Stepper Wizard (Lector Style) -->
    <div class="flex items-center justify-between max-w-xl mx-auto mb-8 bg-white p-4 rounded-full border border-[#ecf0f3] shadow-xs">
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-[#e6f4ea] text-[#0be881] border border-[#0be881]/20">
                <i class="fas fa-check"></i>
            </div>
            <span class="text-xs font-bold text-slate-500">Subir CSV</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-[#0be881]/30"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-[#49bcf7] text-white shadow-sm shadow-[#49bcf7]/30">2</div>
            <span class="text-xs font-bold text-slate-800">Prevalidar</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-slate-100"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium bg-[#f9fafb] text-slate-400 border border-[#ecf0f3]">3</div>
            <span class="text-xs font-medium text-slate-400">Resultado</span>
        </div>
    </div>

    <!-- Encabezado -->
    <div class="pb-4 border-b border-[#ecf0f3]">
        <h2 class="text-2xl font-extrabold tracking-tight text-[#403663] font-rubik">Prevalidación del Archivo de Afiliados</h2>
        <p class="mt-1.5 text-xs text-slate-500 font-medium">Resultados del análisis automático de datos y preclasificación ante el simulador TSS/Unipago.</p>
    </div>

    @php
        $total = $resumen['total'] ?: 1;
        $pctAptos = round(($resumen['aptos'] / $total) * 100);
        $pctRech = round(($resumen['rechazados'] / $total) * 100);
    @endphp

    <!-- Panel de Resumen de Prevalidación (Rediseño Interpretativo con Gráfico de Barras y Tarjetas) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Tarjetas de Métricas (Col 7) -->
        <div class="lg:col-span-7 grid grid-cols-3 gap-4">
            <div class="lector-card p-5 flex flex-col justify-between relative overflow-hidden bg-white">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Registros</span>
                    <span class="text-3xl font-black text-[#403663] block mt-2 font-mono">{{ $resumen['total'] }}</span>
                </div>
                <div class="absolute right-4 bottom-4 text-slate-200 text-2xl opacity-40"><i class="fas fa-file-csv"></i></div>
            </div>
            
            <div class="lector-card p-5 flex flex-col justify-between relative overflow-hidden bg-[#e6f4ea]/40 border-emerald-100">
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider block">Aptos (Importar)</span>
                    <span class="text-3xl font-black text-emerald-800 block mt-2 font-mono">{{ $resumen['aptos'] }}</span>
                </div>
                <div class="absolute right-4 bottom-4 text-emerald-400 text-2xl opacity-40"><i class="fas fa-check-circle"></i></div>
            </div>

            <div class="lector-card p-5 flex flex-col justify-between relative overflow-hidden bg-[#fce8e6]/45 border-rose-100">
                <div>
                    <span class="text-[10px] font-extrabold text-rose-700 uppercase tracking-wider block">No Aptos</span>
                    <span class="text-3xl font-black text-rose-800 block mt-2 font-mono">{{ $resumen['rechazados'] }}</span>
                </div>
                <div class="absolute right-4 bottom-4 text-rose-400 text-2xl opacity-40"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>

        <!-- Gráfico de Distribución Visual (Col 5) -->
        <div class="lg:col-span-5 lector-card p-5 bg-white flex flex-col justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-[#403663] uppercase tracking-wider block">Distribución de Registros</span>
                <span class="text-xs text-slate-400 block font-medium">Proporción de aptitud del lote actual</span>
            </div>
            
            <!-- Barra Stacked Visual -->
            <div class="my-4">
                <div class="w-full bg-slate-100 h-4 rounded-full flex overflow-hidden">
                    <div class="bg-[#0be881] h-full" style="width: {{ $pctAptos }}%" title="Aptos: {{ $pctAptos }}%"></div>
                    <div class="bg-[#f53b57] h-full" style="width: {{ $pctRech }}%" title="No Aptos: {{ $pctRech }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-2">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#0be881]"></span>{{ $pctAptos }}% Aptos</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#f53b57]"></span>{{ $pctRech }}% No Aptos</span>
                </div>
            </div>

            <div class="text-[10.5px] font-semibold text-slate-500 leading-none">
                Acción: <span class="text-xs font-bold text-[#49bcf7] ml-1 uppercase">Generar Lote de {{ $resumen['aptos'] }} Afiliados</span>
            </div>
        </div>
    </div>

    <!-- Filtros de Vista Rápida (Alpine.js) -->
    <div class="flex items-center space-x-2 border-b border-[#ecf0f3] pb-3 text-xs font-bold font-rubik">
        <button @click="activeFilter = 'all'" 
                :class="activeFilter === 'all' ? 'bg-[#403663] text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'"
                class="px-4 py-2 rounded-full transition duration-150">
            Todos ({{ $resumen['total'] }})
        </button>
        <button @click="activeFilter = 'apto'" 
                :class="activeFilter === 'apto' ? 'bg-[#0be881] text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'"
                class="px-4 py-2 rounded-full transition duration-150">
            Aptos ({{ $resumen['aptos'] }})
        </button>
        <button @click="activeFilter = 'no_apto'" 
                :class="activeFilter === 'no_apto' ? 'bg-[#f53b57] text-white shadow-xs' : 'text-slate-500 hover:bg-slate-100'"
                class="px-4 py-2 rounded-full transition duration-150">
            No Aptos ({{ $resumen['rechazados'] }})
        </button>
    </div>

    <!-- Tabla Vista Previa Rediseñada -->
    <div class="lector-card overflow-hidden bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#ecf0f3] text-sm">
                <thead class="bg-slate-50/50 font-bold text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Afiliado</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Cédula / NSS</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Fecha Nac.</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Prevalidación</th>
                        <th scope="col" class="px-6 py-4.5 text-left text-xs uppercase tracking-wider">Causa / Diagnóstico TSS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecf0f3] bg-white">
                    @foreach($registros as $row)
                        @php
                            $isApto = (bool) $row['apto'];
                        @endphp
                        <tr x-show="activeFilter === 'all' || (activeFilter === 'apto' && {{ $isApto ? 'true' : 'false' }}) || (activeFilter === 'no_apto' && {{ $isApto ? 'false' : 'true' }})"
                            class="hover:bg-slate-50/40 transition duration-150 border-l-4 {{ $isApto ? 'border-l-emerald-500 hover:bg-emerald-50/5' : 'border-l-rose-500 hover:bg-rose-50/5' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $row['nombres'] }} {{ $row['primer_apellido'] }} {{ $row['segundo_apellido'] ?? '' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-500 leading-relaxed">
                                <span class="text-slate-800 font-bold">Ced:</span> {{ $row['cedula'] }}<br><span class="text-slate-800 font-bold">NSS:</span> {{ $row['nss'] ?? 'N/A' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-500">
                                {{ $row['fecha_nacimiento'] }} <span class="font-sans text-slate-800 font-bold">({{ $row['sexo'] }})</span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ 
                                    $isApto ? 'bg-[#e6f4ea] text-[#137333] border-emerald-200' : 'bg-[#fce8e6] text-[#c5221f] border-rose-200'
                                }}">
                                    <i class="fas {{ $isApto ? 'fa-check-circle mr-1.5' : 'fa-times-circle mr-1.5' }}"></i>
                                    {{ $isApto ? 'APTO' : 'NO APTO' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-xs font-semibold leading-relaxed max-w-sm {{ $isApto ? 'text-slate-500' : 'text-rose-700' }}">
                                {{ $row['motivo'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botones de Confirmación (Lector Style) -->
    <div class="flex justify-end space-x-3 pt-4">
        <a href="{{ route('ars.carga.masiva') }}" class="px-6 py-2.5 border border-[#ecf0f3] rounded-full text-xs font-bold text-slate-500 bg-white hover:bg-slate-50 transition active:scale-98">
            Volver a Cargar
        </a>
        <form action="{{ route('ars.carga.procesar') }}" method="POST">
            @csrf
            <button type="submit" class="px-6 py-2.5 border border-transparent rounded-full shadow-lg shadow-[#49bcf7]/15 text-xs font-bold text-white bg-[#49bcf7] hover:bg-[#31a3e6] active:scale-98 transition duration-200">
                <i class="fas fa-file-import mr-1.5"></i> Confirmar e Importar Registros Aptos
            </button>
        </form>
    </div>
</div>
@endsection
