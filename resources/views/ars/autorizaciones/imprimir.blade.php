<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización {{ $autorizacion->numero_autorizacion }} — ARS CMD</title>
    
    <!-- Google Fonts: Sora & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#000666',
                            blue: '#0056c5',
                            indigo: '#1a237e',
                            slate: '#475569'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        title: ['Sora', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .format-termica-print {
                width: 80mm !important;
                max-width: 80mm !important;
                padding: 2mm !important;
                margin: 0 auto !important;
                border: none !important;
                box-shadow: none !important;
            }
            .format-carta-print {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased p-4 sm:p-8" x-data="{ format: 'carta' }">

    <!-- Barra de Control Superior (Solo pantalla) -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <a href="{{ route('ars.autorizaciones.show', $autorizacion->id) }}" class="inline-flex items-center px-4 py-2 border border-slate-350 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition shadow-xs">
            <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al Expediente
        </a>
        
        <div class="flex items-center gap-2">
            <button @click="format = 'carta'" 
                :class="format === 'carta' ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-700 border-slate-250'"
                class="px-4 py-2 border rounded-xl font-bold text-xs shadow-xs transition active:scale-95">
                Formato Carta Estándar
            </button>
            <button @click="format = 'termica'" 
                :class="format === 'termica' ? 'bg-brand-blue text-white border-brand-blue' : 'bg-white text-slate-700 border-slate-250'"
                class="px-4 py-2 border rounded-xl font-bold text-xs shadow-xs transition active:scale-95">
                Ticket Térmico (80mm)
            </button>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-5 py-2 border border-transparent rounded-xl shadow-sm text-xs font-bold text-white bg-brand-blue hover:bg-brand-navy transition active:scale-95">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir Volante
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTENEDOR 1: FORMATO CARTA ESTÁNDAR       -->
    <!-- ========================================== -->
    <div x-show="format === 'carta'" class="format-carta-print max-w-4xl mx-auto bg-white p-8 border border-slate-200 shadow-lg rounded-3xl space-y-6 relative overflow-hidden">
        
        <!-- Borde sutil superior con colores corporativos -->
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-brand-navy via-brand-blue to-brand-indigo"></div>

        <!-- Fila de Cabecera -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-slate-200 pb-5 gap-4 pt-2">
            <!-- Logo oficial ARS CMD -->
            <div class="flex items-center space-x-3.5">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-10 w-auto object-contain">
                <div class="border-l border-slate-300 pl-3.5 leading-none py-1">
                    <span class="text-brand-navy font-extrabold text-xl block uppercase tracking-tight font-title">ARS CMD</span>
                    <span class="text-brand-blue font-bold text-[9px] block mt-0.5 tracking-wider uppercase">Seguro Familiar de Salud (SFS)</span>
                </div>
            </div>
            
            <div class="text-center sm:text-right">
                <h1 class="text-xs font-black text-brand-navy tracking-widest uppercase font-title">Constancia de Autorización Médica</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Plan Básico de Salud • CNSS</p>
            </div>

            <!-- Código de barras y número de autorización -->
            <div class="flex flex-col items-center sm:items-end">
                <svg class="h-8 w-40" viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                    @foreach([0,3,6,11,13,17,23,25,29,34,37,40,43,49,53,56,61,63,67,73,75,79,84,87,91,97] as $x)
                        <rect x="{{ $x }}" y="0" width="{{ ($loop->index % 3 === 0) ? 2 : 1 }}" height="30" fill="black" />
                    @endforeach
                </svg>
                <span class="text-[9.5px] font-mono font-bold text-slate-500 mt-1">*{{ $autorizacion->numero_autorizacion }}*</span>
            </div>
        </div>

        <!-- Ficha de Datos Corporativa -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs bg-slate-50/20 text-xs">
            <div class="bg-slate-50/80 px-4 py-2.5 border-b border-slate-200 flex justify-between items-center">
                <span class="font-title font-bold text-[10px] text-brand-navy uppercase tracking-wider">Detalles Generales de la Solicitud</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide border {{ 
                    $autorizacion->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'
                }} uppercase">
                    {{ $autorizacion->estado }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3.5 gap-x-8 p-5">
                <div class="space-y-2.5">
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">No. Autorización:</span>
                        <span class="font-bold text-brand-navy font-mono">{{ $autorizacion->numero_autorizacion }}</span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mr-4">Centro Médico PSS:</span>
                        <span class="font-bold text-slate-800 text-right uppercase">{{ $autorizacion->pss->nombre }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Póliza / Contrato:</span>
                        <span class="font-bold text-brand-blue font-mono">{{ $poliza }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Paciente:</span>
                        <span class="font-bold text-slate-800 uppercase">{{ $afiliado->nombre_completo }}</span>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Documento Identidad:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $afiliado->cedula }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Sexo / Edad:</span>
                        <span class="font-bold text-slate-800">{{ $afiliado->sexo }} / {{ $afiliado->edad }} Años</span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mr-4">Diagnóstico Médico:</span>
                        <span class="font-bold text-slate-800 text-right uppercase">{{ $autorizacion->diagnostico }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-1">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Fecha Autorización:</span>
                        <span class="font-semibold text-slate-600 font-mono">{{ $autorizacion->fecha_solicitud->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Desglose Comercial/Corporativo -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs text-[11px]">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-brand-navy text-white font-title text-[9px] tracking-wider uppercase font-bold">
                    <tr>
                        <th class="px-5 py-3.5 text-left w-24">CÓDIGO</th>
                        <th class="px-5 py-3.5 text-left">SERVICIO / ESTUDIO AUTORIZADO</th>
                        <th class="px-5 py-3.5 text-center w-16">CANT.</th>
                        <th class="px-5 py-3.5 text-center w-16">% COB.</th>
                        <th class="px-5 py-3.5 text-right w-32">TOTAL SOLICITADO</th>
                        <th class="px-5 py-3.5 text-right w-32">COBERTURA ARS</th>
                        <th class="px-5 py-3.5 text-right w-32">DIF. PACIENTE</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white font-medium">
                    @foreach($detallesCalculados as $det)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-slate-500">{{ $det->codigo }}</td>
                        <td class="px-5 py-3.5 text-slate-800 font-bold uppercase">{{ $det->descripcion }}</td>
                        <td class="px-5 py-3.5 text-center text-slate-600 font-bold">{{ $det->cantidad }}</td>
                        <td class="px-5 py-3.5 text-center font-bold text-brand-blue">{{ $det->porcentaje }}%</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-800">${{ number_format($det->monto, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-black text-emerald-700">${{ number_format($det->cobertura, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-850">${{ number_format($det->diferencia, 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-slate-50/80 font-black text-xs text-right border-t-2 border-slate-200">
                        <td colspan="4" class="px-5 py-4 text-left text-brand-navy font-title tracking-wider">TOTALES GENERALES</td>
                        <td class="px-5 py-4 font-mono text-slate-800">${{ number_format($totalSolicitado, 2) }}</td>
                        <td class="px-5 py-4 font-mono text-emerald-700">${{ number_format($totalCobertura, 2) }}</td>
                        <td class="px-5 py-4 font-mono text-slate-900">${{ number_format($totalDiferencia, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Área de Firmas y Sello Oficial -->
        <div class="grid grid-cols-3 gap-8 pt-10 pb-4 items-center">
            <!-- Sello Oficial -->
            <div class="flex justify-start">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl w-36 h-24 flex flex-col items-center justify-center p-2 text-center select-none text-[8.5px] text-slate-350 font-bold leading-tight">
                    <svg class="h-6 w-6 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    SELLO DE APROBACIÓN<br>ARS CMD SFS
                </div>
            </div>
            
            <!-- Firmas -->
            <div class="space-y-2 text-center text-[10px]">
                <div class="border-t border-slate-300 w-40 mx-auto"></div>
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[8.5px]">Entregado Por PSS</p>
            </div>
            <div class="space-y-2 text-center text-[10px]">
                <div class="border-t border-slate-300 w-40 mx-auto"></div>
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[8.5px]">Firma de Conformidad Afiliado</p>
            </div>
        </div>

        <!-- Pie de página Oficial -->
        <div class="border-t border-slate-200 pt-4 text-[9px] text-slate-400 leading-relaxed text-justify">
            <p><strong>DECLARACIÓN SFS:</strong> Este documento constituye una constancia legal de cobertura y liquidación previa de servicios de salud autorizados bajo el Seguro Familiar de Salud de la República Dominicana. La PSS afiliada deberá adjuntar este volante con las firmas y sello reglamentarios al momento de remitir la facturación física o el archivo digital de conciliación. El pago de las coberturas está sujeto a la vigencia de los términos contractuales del afiliado titular con la ARS al momento del despacho clínico.</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTENEDOR 2: FORMATO TICKET TÉRMICO (80mm)-->
    <!-- ========================================== -->
    <div x-show="format === 'termica'" class="format-termica-print max-w-[80mm] mx-auto bg-white p-4 border border-dashed border-slate-300 shadow-md text-[10px] space-y-4 font-mono">
        
        <!-- Cabecera Corta -->
        <div class="text-center border-b border-dashed border-slate-300 pb-2 space-y-1">
            <h2 class="text-xs font-black">*** ARS CMD ***</h2>
            <p class="text-[9px]">SISTEMA DOMINICANO SFS</p>
            <p class="text-[9px] uppercase font-bold">{{ $autorizacion->pss->nombre }}</p>
            <div class="border-y border-slate-300 py-1 my-1 text-center font-bold text-xs bg-slate-50">
                AUTORIZACIÓN
            </div>
            <p class="font-bold text-xs tracking-wider">{{ $autorizacion->numero_autorizacion }}</p>
        </div>

        <!-- Datos Ficha Ticket -->
        <div class="space-y-1 text-[9px] border-b border-dashed border-slate-300 pb-2">
            <div><strong>PACIENTE:</strong> {{ strtoupper($afiliado->nombre_completo) }}</div>
            <div><strong>CÉDULA:</strong> {{ $afiliado->cedula }}</div>
            <div><strong>NSS:</strong> {{ $afiliado->nss ?? 'N/D' }}</div>
            <div><strong>PLAN:</strong> BASIC-SFS</div>
            <div><strong>PÓLIZA:</strong> {{ $poliza }}</div>
            <div><strong>DIAG:</strong> {{ strtoupper($autorizacion->diagnostico) }}</div>
            <div><strong>FECHA:</strong> {{ $autorizacion->fecha_solicitud->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Desglose Ticket -->
        <div class="space-y-2 text-[9px] border-b border-dashed border-slate-300 pb-2">
            <div class="font-bold border-b border-slate-200 pb-1 flex justify-between">
                <span>ESTUDIO / DETALLE</span>
                <span>MONTO</span>
            </div>
            @foreach($detallesCalculados as $det)
            <div class="space-y-0.5">
                <div class="flex justify-between font-bold">
                    <span>{{ $det->codigo }} - {{ substr($det->descripcion, 0, 18) }}..</span>
                    <span>${{ number_format($det->monto, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 pl-2">
                    <span>Cobertura ({{ $det->porcentaje }}%)</span>
                    <span>-${{ number_format($det->cobertura, 2) }}</span>
                </div>
                @if($det->diferencia > 0)
                <div class="flex justify-between text-slate-700 pl-2 font-semibold">
                    <span>Diferencia</span>
                    <span>${{ number_format($det->diferencia, 2) }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totales Ticket -->
        <div class="space-y-1 text-[10px] font-bold border-b border-dashed border-slate-300 pb-2">
            <div class="flex justify-between">
                <span>TOTAL RECLAMO:</span>
                <span>${{ number_format($totalSolicitado, 2) }}</span>
            </div>
            <div class="flex justify-between text-emerald-700">
                <span>COB. ARS:</span>
                <span>-${{ number_format($totalCobertura, 2) }}</span>
            </div>
            <div class="flex justify-between text-red-700 border-t border-slate-200 pt-1">
                <span>DIF. PACIENTE:</span>
                <span>${{ number_format($totalDiferencia, 2) }}</span>
            </div>
        </div>

        <!-- Área de Firma Rápida -->
        <div class="text-center pt-8 space-y-4">
            <div class="border-t border-slate-300 w-32 mx-auto"></div>
            <p class="text-[8px] font-bold uppercase tracking-wider">FIRMA Y SELLO DE CONFORMIDAD</p>
            
            <!-- Código barras simple en ticket -->
            <div class="flex flex-col items-center pt-2">
                <svg class="h-6 w-32" viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                    @foreach([0,4,8,12,16,20,24,28,32,36,40,44,48,52,56,60,64,68,72,76,80,84,88,92,96] as $x)
                        <rect x="{{ $x }}" y="0" width="1" height="20" fill="black" />
                    @endforeach
                </svg>
                <span class="text-[7px] font-bold mt-0.5">{{ $autorizacion->numero_autorizacion }}</span>
            </div>
        </div>

    </div>

</body>
</html>
