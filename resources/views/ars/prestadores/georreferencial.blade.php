@extends('layouts.ars')

@section('title', 'Consulta Georreferencial PSS')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consulta Georreferencial PSS</h2>
            <p class="text-xs text-slate-500 font-medium">Georreferenciación de clínicas en mapa interactivo.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <!-- Georreferencial PSS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Mapa de Cobertura Física PSS</h3>
            <p class="text-xs text-slate-450">Visualización de georreferenciación de clínicas y médicos auditores mediante Leaflet Maps.</p>
            
            <!-- Leaflet Map Container -->
            <div id="leaflet-map" class="w-full h-[400px] rounded-2xl border border-slate-150 bg-slate-100 z-10"></div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Ubicaciones Registradas</h3>
            <div class="space-y-3 max-h-[420px] overflow-y-auto">
                @foreach($locations as $loc)
                    <div class="p-3 rounded-2xl bg-slate-50/50 border border-slate-100 flex items-start space-x-2.5 cursor-pointer hover:bg-slate-50 transition" onclick="zoomTo('{{ $loc->latitude }}', '{{ $loc->longitude }}', '{{ $loc->pss->nombre }}')">
                        <span class="material-symbols-outlined text-rose-600 mt-0.5">location_on</span>
                        <div>
                            <h4 class="font-bold text-slate-800 text-[11px]">{{ $loc->pss->nombre }}</h4>
                            <p class="text-[9px] text-slate-400 mt-0.5">{{ $loc->province }}, {{ $loc->sector }}</p>
                            <span class="text-[9px] text-slate-500 font-mono block mt-1">Lat: {{ $loc->latitude }}, Lng: {{ $loc->longitude }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha255-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha255-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        let map;
        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar mapa en Santo Domingo
            map = L.map('leaflet-map').setView([18.47186, -69.90712], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Cargar pines dinámicamente
            @foreach($locations as $loc)
                L.marker([{{ $loc->latitude }}, {{ $loc->longitude }}])
                    .addTo(map)
                    .bindPopup("<b>{{ $loc->pss->nombre }}</b><br>{{ $loc->address_details }}");
            @endforeach
        });

        function zoomTo(lat, lng, name) {
            if (map) {
                map.setView([lat, lng], 16);
            }
        }
    </script>


</div>
@endsection
