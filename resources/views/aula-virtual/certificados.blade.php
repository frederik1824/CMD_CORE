@extends('layouts.virtual-classroom')

@section('title', 'Mis Certificados')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 tracking-tight">Mis Certificados Académicos</h2>
        <p class="text-xs text-slate-400 mt-0.5">Aquí se muestran las certificaciones oficiales obtenidas al completar satisfactoriamente los cursos de capacitación.</p>
    </div>

    <!-- Certificates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($certificados as $cert)
            <!-- Certificate Glassmorphic preview -->
            <div class="bg-white border border-slate-150 p-6 rounded-3xl shadow-sm flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition relative overflow-hidden">
                <!-- Watermark seal background -->
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-emerald-500/5 rounded-full border-4 border-emerald-500/10 pointer-events-none flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-500/10 text-6xl" data-icon="workspace_premium">workspace_premium</span>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                            CERTIFICADO OFICIAL
                        </span>
                        <span class="text-[9px] text-slate-400 font-bold font-mono">
                            Código: {{ $cert->certificate_code }}
                        </span>
                    </div>

                    <h3 class="font-bold text-slate-800 text-sm leading-snug">{{ $cert->course->title }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Otorgado a: <span class="font-bold text-slate-700">{{ Auth::user()->name }}</span></p>
                    <p class="text-[10px] text-slate-450 mt-0.5 font-mono">Fecha Emisión: {{ $cert->issued_at->format('d/m/Y') }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between z-10">
                    <span class="text-[9px] text-emerald-600 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs" data-icon="verified">verified</span>
                        Verificado
                    </span>
                    <button onclick="alert('Abriendo visor de impresión de certificado demo...')" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition flex items-center gap-1.5 shadow-sm bg-white">
                        <span class="material-symbols-outlined text-sm text-slate-500" data-icon="print">print</span>
                        <span>Imprimir Certificado</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white p-8 text-center text-slate-400 border border-slate-100 rounded-3xl">
                Aún no has completado ningún curso. Completa los temas y aprueba el examen final para obtener tu certificación.
            </div>
        @endforelse
    </div>
</div>
@endsection
