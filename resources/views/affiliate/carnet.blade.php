@extends('layouts.affiliate')

@section('title', 'Mi Carnet Digital')

@section('content')
<div class="space-y-6 flex flex-col items-center font-sans" 
     x-data="{
        copiadoId: '',
        copiarAlPortapapeles(texto, campo) {
            navigator.clipboard.writeText(texto);
            this.copiadoId = campo;
            setTimeout(() => this.copiadoId = '', 2000);
        }
     }">
     
    <!-- HEADER GENERAL -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 w-full max-w-lg text-center shadow-sm">
        <h2 class="text-base font-bold text-slate-800 tracking-tight">Mi Carnet Digital de Salud</h2>
        <p class="text-xs text-slate-500 mt-1.5 font-medium leading-relaxed font-sans">Presenta este carnet digital en las clínicas y farmacias afiliadas para tu validación.</p>
    </div>

    <!-- CARNET DIGITAL (GOOGLE MATERIAL 3) -->
    <div class="relative w-full max-w-md bg-white border border-slate-200 rounded-2xl p-6 text-slate-700 shadow-md flex flex-col justify-between hover:shadow-lg transition duration-200">
        <!-- Borde sutil superior en azul Google -->
        <div class="absolute inset-x-0 top-0 h-1.5 bg-blue-600 rounded-t-2xl"></div>

        <!-- Header del Carnet -->
        <div class="flex justify-between items-start pt-2">
            <div class="flex items-center space-x-2.5">
                <svg class="h-7 w-7 text-emerald-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" class="fill-blue-500" />
                    <path d="M12 7V17M7 12H17" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase">
                AFILIADO TITULAR
            </span>
        </div>

        <!-- Nombre Completo -->
        <div class="my-8">
            <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider block">Afiliado Titular</span>
            <span class="text-base font-bold tracking-tight block uppercase mt-1 text-slate-800">{{ $afiliado->nombre_completo }}</span>
            <span class="text-[9.5px] text-emerald-600 font-bold block mt-1.5 font-mono">Plan Contributivo Activo</span>
        </div>

        <!-- Datos del Afiliado con Copiado Rápido -->
        <div class="grid grid-cols-3 gap-4 border-t border-slate-100 pt-4 text-xs font-semibold">
            <!-- Cédula -->
            <div class="space-y-1">
                <span class="text-[8px] text-slate-400 block uppercase tracking-wider">Cédula</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold font-mono text-slate-800">{{ $afiliado->cedula }}</span>
                    <button type="button" @click="copiarAlPortapapeles('{{ $afiliado->cedula }}', 'cedula')"
                            class="p-1 hover:bg-slate-100 text-slate-400 hover:text-blue-600 rounded-lg transition relative"
                            title="Copiar Cédula">
                        <span class="material-symbols-outlined text-[12px] block" x-show="copiadoId !== 'cedula'" data-icon="content_copy">content_copy</span>
                        <span class="material-symbols-outlined text-[12px] block text-emerald-600" x-show="copiadoId === 'cedula'" x-cloak data-icon="done">done</span>
                        <div x-show="copiadoId === 'cedula'" x-cloak class="absolute bottom-full right-1/2 translate-x-1/2 mb-1.5 px-2 py-0.5 bg-slate-850 text-white rounded text-[8px] whitespace-nowrap shadow">Copiado</div>
                    </button>
                </div>
            </div>

            <!-- NSS -->
            <div class="space-y-1">
                <span class="text-[8px] text-slate-400 block uppercase tracking-wider">NSS</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold font-mono text-slate-800">{{ $afiliado->nss }}</span>
                    <button type="button" @click="copiarAlPortapapeles('{{ $afiliado->nss }}', 'nss')"
                            class="p-1 hover:bg-slate-100 text-slate-400 hover:text-blue-600 rounded-lg transition relative"
                            title="Copiar NSS">
                        <span class="material-symbols-outlined text-[12px] block" x-show="copiadoId !== 'nss'" data-icon="content_copy">content_copy</span>
                        <span class="material-symbols-outlined text-[12px] block text-emerald-600" x-show="copiadoId === 'nss'" x-cloak data-icon="done">done</span>
                        <div x-show="copiadoId === 'nss'" x-cloak class="absolute bottom-full right-1/2 translate-x-1/2 mb-1.5 px-2 py-0.5 bg-slate-850 text-white rounded text-[8px] whitespace-nowrap shadow">Copiado</div>
                    </button>
                </div>
            </div>

            <!-- Contrato -->
            <div class="space-y-1 text-right">
                <span class="text-[8px] text-slate-400 block uppercase tracking-wider">Contrato</span>
                <span class="text-[10px] font-bold font-mono text-blue-600 block mt-1">{{ $afiliado->numero_contrato }}</span>
            </div>
        </div>
    </div>

    <!-- WIDGET EXPLICATIVO -->
    <div class="w-full max-w-lg bg-blue-50/50 border border-blue-100 p-4 rounded-2xl flex items-start gap-3 shadow-xs">
        <span class="material-symbols-outlined text-blue-600 text-sm mt-0.5" data-icon="info">info</span>
        <div class="text-xs text-slate-600 leading-relaxed font-semibold">
            <span class="font-bold text-blue-700 block mb-0.5">¿Cómo utilizar tu Carnet Digital?</span>
            Muestra el carnet desde tu teléfono móvil al momento de facturar tu copago en la clínica o farmacia. El prestador validará tu número de NSS en el sistema para procesar tu cobertura inmediatamente sin necesidad de plástico físico. Puedes usar los botones de copiado rápido al lado de cada número para mayor comodidad.
        </div>
    </div>

</div>
@endsection
