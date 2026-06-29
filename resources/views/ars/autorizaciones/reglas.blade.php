@extends('layouts.ars')

@section('title', 'Motor de Reglas')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Reglas del Motor de Autorización Automática
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Configura los parámetros del motor de toma de decisiones automáticas sobre las solicitudes PSS.
            </p>
        </div>
    </div>

    <!-- Listado de Reglas -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden divide-y divide-slate-100">
        @foreach($reglas as $regla)
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-slate-50 transition gap-4">
                <div class="space-y-1 max-w-2xl">
                    <div class="flex items-center space-x-2">
                        <span class="font-mono font-bold text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded">{{ $regla->codigo }}</span>
                        <h4 class="text-sm font-bold text-slate-800">{{ $regla->tipo_regla }}</h4>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $regla->descripcion }}</p>
                </div>
                
                <div class="flex items-center space-x-4 justify-end">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ 
                        $regla->estado === 'Activa' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'
                    }}">
                        {{ $regla->estado }}
                    </span>
                    
                    @if(Auth::user()->role === 'Administrador ARS')
                        <form action="{{ route('ars.autorizaciones.reglas.toggle', $regla->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                                {{ $regla->estado === 'Activa' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
