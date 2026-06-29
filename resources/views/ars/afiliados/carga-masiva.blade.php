@extends('layouts.ars')

@section('title', 'Carga Masiva')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in" x-data="{
    pasteDemo() {
        const demoCsv = 'cedula,nss,nui,nombres,primer_apellido,segundo_apellido,fecha_nacimiento,sexo\n00109283740,10293840,30192840,Manuel,Guerrero,Pena,1985-05-15,M\n00183746252,10928372,30928372,Laura,Castillo,Guzman,1992-10-22,F\n00109283743,10384723,30827363,Pedro,Ramirez,Santos,1978-01-30,M\n00109283746,10827366,30293846,Carlos,Perez,Diaz,1990-12-05,M\n00192837468,10293848,30293848,Sofia,Lozano,Ortiz,1983-08-14,F';
        document.getElementById('csv_content').value = demoCsv;
    }
}">
    <!-- Stepper Wizard -->
    <div class="flex items-center justify-between max-w-xl mx-auto mb-8 bg-white/60 p-4 rounded-full border border-[#e0e0e0] shadow-sm backdrop-blur-sm">
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold bg-[#0b57d0] text-white">1</div>
            <span class="text-sm font-semibold text-[#1f1f1f]">Subir CSV</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-[#e0e0e0]"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium bg-[#f1f3f4] text-[#5f6368] border border-[#e0e0e0]">2</div>
            <span class="text-sm font-medium text-[#5f6368]">Prevalidar</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-[#e0e0e0]"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-medium bg-[#f1f3f4] text-[#5f6368] border border-[#e0e0e0]">3</div>
            <span class="text-sm font-medium text-[#5f6368]">Resultado</span>
        </div>
    </div>

    <!-- Encabezado -->
    <div class="pb-2">
        <h2 class="text-2xl font-semibold tracking-tight text-[#1f1f1f]">Carga Masiva de Afiliados</h2>
        <p class="mt-1.5 text-sm text-[#5f6368]">Importa múltiples afiliados titulares a través de archivos o pegado de texto en formato CSV.</p>
    </div>

    <!-- Instrucciones -->
    <div class="bg-[#e8f0fe] border-l-4 border-[#0b57d0] rounded-r-2xl p-5 shadow-sm space-y-2 text-sm text-[#041e49]">
        <h4 class="font-semibold uppercase tracking-wider text-xs text-[#0b57d0] flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Estructura del Archivo CSV:
        </h4>
        <p class="leading-relaxed text-xs">
            El archivo debe contener una fila de encabezados con los siguientes nombres exactos:
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">cedula</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">nss</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">nui</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">nombres</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">primer_apellido</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">segundo_apellido</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">fecha_nacimiento</code>, 
            <code class="bg-white/80 px-2 py-0.5 rounded border border-[#b4cbfb] font-mono font-bold text-[#0b57d0]">sexo</code>.
        </p>
    </div>

    <!-- Panel de Entrada de Datos -->
    <div class="bg-white shadow-sm rounded-3xl border border-[#e0e0e0] overflow-hidden">
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-[#f1f3f4]">
                <h3 class="text-sm font-semibold text-[#1f1f1f] uppercase tracking-wider">Pegar Contenido CSV</h3>
                <button type="button" @click="pasteDemo()" class="inline-flex items-center px-4 py-2 border border-[#c2e7ff] rounded-full text-xs font-semibold text-[#041e49] bg-[#c2e7ff]/60 hover:bg-[#c2e7ff] transition duration-150 shadow-sm active:scale-98">
                    <svg class="mr-1.5 h-4 w-4 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Cargar CSV Demo de Pruebas
                </button>
            </div>
            
            <form action="{{ route('ars.carga.prevalidar') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <textarea name="csv_content" id="csv_content" rows="10" required class="block w-full rounded-2xl border border-[#dcdcdc] p-4 text-xs font-mono text-[#1f1f1f] placeholder-[#5f6368]/60 focus:border-[#0b57d0] focus:ring-1 focus:ring-[#0b57d0] focus:outline-none transition" placeholder="Pega el contenido CSV aquí..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ route('ars.titulares.index') }}" class="px-6 py-2.5 border border-[#dcdcdc] rounded-full text-xs font-semibold text-[#5f6368] bg-white hover:bg-[#f8f9fa] transition duration-150">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 border border-transparent rounded-full shadow-sm text-xs font-semibold text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 hover:shadow-md transition duration-150 active:scale-98">
                        Prevalidar Registros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

