<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización {{ $autorizacion->numero_autorizacion }} — ARS CMD</title>
    
    <!-- Google Fonts: Inter and Sora -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        title: ['Sora', 'sans-serif'],
                    },
                    colors: {
                        primary: "#000666",
                        secondary: "#0056c5",
                        cyan: {
                            600: "#00a7bb",
                            700: "#008fa0"
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
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
            .print-container {
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
    <div class="max-w-4xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2">
            <button @click="format = 'carta'" 
                :class="format === 'carta' ? 'bg-secondary text-white border-secondary' : 'bg-white text-slate-700 border-slate-200'"
                class="px-4 py-2 border rounded-xl font-bold text-xs shadow-sm transition active:scale-95">
                Formato Carta Estándar
            </button>
            <button @click="format = 'termica'" 
                :class="format === 'termica' ? 'bg-secondary text-white border-secondary' : 'bg-white text-slate-700 border-slate-200'"
                class="px-4 py-2 border rounded-xl font-bold text-xs shadow-sm transition active:scale-95">
                Ticket Térmico (80mm)
            </button>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-5 py-2 border border-transparent rounded-xl shadow-md text-xs font-bold text-white bg-secondary hover:bg-primary transition active:scale-95">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir Volante
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTENEDOR 1: FORMATO CARTA ESTÁNDAR       -->
    <!-- ========================================== -->
    <div x-show="format === 'carta'" class="print-container max-w-4xl mx-auto bg-white p-10 border border-slate-200 shadow-xl rounded-[24px] space-y-6">
        
        <!-- Fila de Cabecera (Logo + Título + Barcode) -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-slate-100 pb-6 gap-4">
            
            <!-- Logo Oficial ARS CMD -->
            <div class="flex items-center space-x-3.5">
                <svg class="w-12 h-12 flex-shrink-0" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Left Triangle (Dark Blue/Teal) -->
                    <path d="M15 25 L85 25 L35 75 Z" fill="#000666"/>
                    <!-- Right Triangle overlay (Cyan) -->
                    <path d="M45 25 L85 25 L65 55 Z" fill="#00a7bb"/>
                </svg>
                <div class="leading-none">
                    <span class="text-primary font-extrabold text-2xl tracking-tighter block font-title">ARS</span>
                    <span class="text-cyan-600 font-bold text-xl block -mt-1 font-title">CMD</span>
                </div>
            </div>
            
            <div class="text-center sm:text-left flex-1 sm:pl-8">
                <h1 class="text-xl font-bold text-primary tracking-wide uppercase font-title">Autorización de Servicios Médicos</h1>
                <p class="text-xs text-slate-400 font-medium">Plan Básico de Salud - SISALRIL</p>
            </div>

            <!-- Código de barras y número de autorización -->
            <div class="flex flex-col items-center">
                <svg class="h-8 w-44" viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                    @foreach([0,3,6,11,13,17,23,25,29,34,37,40,43,49,53,56,61,63,67,73,75,79,84,87,91,97] as $x)
                        <rect x="{{ $x }}" y="0" width="{{ ($loop->index % 3 === 0) ? 2 : 1 }}" height="30" fill="black" />
                    @endforeach
                </svg>
                <span class="text-[9px] font-mono font-bold text-slate-500 mt-1">*{{ $autorizacion->numero_autorizacion }}*</span>
            </div>
        </div>

        <!-- Ficha de Datos -->
        <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/30 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3.5 gap-x-12">
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">No. Autorización:</span>
                        <span class="font-bold text-slate-900 font-mono text-sm">{{ $autorizacion->numero_autorizacion }}</span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mr-4">Centro Médico PSS:</span>
                        <span class="font-bold text-slate-800 text-right">{{ strtoupper($autorizacion->pss->nombre) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Póliza / Contrato:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $poliza }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Paciente:</span>
                        <span class="font-extrabold text-slate-900">{{ strtoupper($afiliado->nombre_completo) }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Cédula:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $afiliado->cedula }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Sexo / Edad:</span>
                        <span class="font-bold text-slate-800">{{ $afiliado->sexo }} / {{ $afiliado->edad }} años</span>
                    </div>
                    <div class="flex justify-between items-start border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider mr-4">Diagnóstico:</span>
                        <span class="font-bold text-slate-800 text-right">{{ strtoupper($autorizacion->diagnostico) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100/70 pb-1.5">
                        <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Fecha Autorización:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $autorizacion->fecha_solicitud->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Desglose -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <table class="min-w-full divide-y divide-slate-150 text-xs">
                <thead class="bg-slate-50/70 font-bold text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 text-left w-28 text-[9px] tracking-wider uppercase">CÓDIGO</th>
                        <th class="px-5 py-3.5 text-left text-[9px] tracking-wider uppercase">SERVICIO / ESTUDIO</th>
                        <th class="px-5 py-3.5 text-center w-16 text-[9px] tracking-wider uppercase">CANT.</th>
                        <th class="px-5 py-3.5 text-center w-16 text-[9px] tracking-wider uppercase">% COB.</th>
                        <th class="px-5 py-3.5 text-right w-36 text-[9px] tracking-wider uppercase">TOTAL</th>
                        <th class="px-5 py-3.5 text-right w-36 text-[9px] tracking-wider uppercase">COBERTURA</th>
                        <th class="px-5 py-3.5 text-right w-36 text-[9px] tracking-wider uppercase">DIFERENCIA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($detallesCalculados as $det)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-5 py-3 font-mono font-bold text-slate-500" x-text="'{{ $det->codigo }}'"></td>
                        <td class="px-5 py-3 text-slate-800 font-semibold">{{ $det->descripcion }}</td>
                        <td class="px-5 py-3 text-center text-slate-700 font-medium">{{ $det->cantidad }}</td>
                        <td class="px-5 py-3 text-center text-slate-700 font-semibold">{{ $det->porcentaje }}%</td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800">${{ number_format($det->monto, 2) }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-emerald-600">${{ number_format($det->cobertura, 2) }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-slate-800">${{ number_format($det->diferencia, 2) }}</td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-slate-50/50 font-bold text-xs border-t-2 border-slate-200">
                        <td colspan="4" class="px-5 py-3.5 text-left text-slate-450 uppercase tracking-wider text-[10px]">TOTALES</td>
                        <td class="px-5 py-3.5 text-right text-slate-900 font-extrabold">${{ number_format($totalSolicitado, 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-650 font-extrabold">${{ number_format($totalCobertura, 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-slate-900 font-extrabold">${{ number_format($totalDiferencia, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Área de Firmas -->
        <div class="grid grid-cols-2 gap-12 pt-16 pb-4 text-center text-xs">
            <div class="space-y-2.5">
                <div class="border-t border-slate-300 w-48 mx-auto"></div>
                <p class="font-bold text-slate-400 uppercase text-[8px] tracking-wider">ENTREGADO POR PSS</p>
            </div>
            <div class="space-y-2.5">
                <div class="border-t border-slate-300 w-48 mx-auto"></div>
                <p class="font-bold text-slate-400 uppercase text-[8px] tracking-wider">FIRMA AFILIADO</p>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="border-t border-slate-100 pt-5 text-[9px] text-slate-400 leading-relaxed text-center sm:text-justify font-medium">
            <p><strong>ARS CMD SFS:</strong> Este documento es una constancia oficial de aprobación de cobertura de servicios de salud. La validez de los servicios aprobados está sujeta a la confirmación de la afiliación activa al momento de la prestación del servicio médico. Vía de reclamación: Facturar con anexo de esta volante debidamente sellada y firmada.</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTENEDOR 2: FORMATO TICKET TÉRMICO (80mm)-->
    <!-- ========================================== -->
    <div x-show="format === 'termica'" class="max-w-[80mm] mx-auto bg-white p-5 border border-dashed border-slate-300 shadow-md text-[10px] space-y-4 font-mono">
        
        <!-- Cabecera Corta con Logo -->
        <div class="text-center border-b border-dashed border-slate-300 pb-3 space-y-2">
            <div class="flex items-center justify-center space-x-1.5">
                <svg class="w-8 h-8 flex-shrink-0" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 25 L85 25 L35 75 Z" fill="#000666"/>
                    <path d="M45 25 L85 25 L65 55 Z" fill="#00a7bb"/>
                </svg>
                <div class="text-left leading-none">
                    <span class="font-bold text-sm block">ARS CMD</span>
                    <span class="text-[8px] block text-slate-500">SISTEMA SFS</span>
                </div>
            </div>
            <p class="text-[9px] uppercase font-bold">{{ $autorizacion->pss->nombre }}</p>
            <div class="border-y border-slate-200 py-1 my-1 text-center font-bold text-xs bg-slate-50">
                AUTORIZACIÓN MÉDICA
            </div>
            <p class="font-bold text-xs tracking-wider">{{ $autorizacion->numero_autorizacion }}</p>
        </div>

        <!-- Datos Ficha Ticket -->
        <div class="space-y-1 text-[9px] border-b border-dashed border-slate-300 pb-2">
            <div><strong>PACIENTE:</strong> {{ strtoupper($afiliado->nombre_completo) }}</div>
            <div><strong>CÉDULA:</strong> {{ $afiliado->cedula }}</div>
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
