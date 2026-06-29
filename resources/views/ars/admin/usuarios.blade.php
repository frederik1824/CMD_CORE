@extends('layouts.ars')

@section('title', 'Usuarios & Roles')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Usuarios & Roles del Sistema
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Lista de cuentas de usuario autorizadas para acceder a los portales ARS y PSS.
            </p>
        </div>
    </div>

    <!-- Listado Usuarios -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nombre Completo</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Correo Electrónico</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Rol de Sistema</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Asociación PSS</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($usuarios as $usr)
                        <tr class="hover:bg-slate-50 transition text-xs">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $usr->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-600">
                                {{ $usr->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full font-semibold border border-brand-100">
                                    {{ $usr->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-semibold">
                                {{ $usr->role === 'Usuario PSS' ? 'Clínica Abreu (ID: ' . ($usr->pss_id ?? '1') . ')' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-emerald-50 text-emerald-700">
                                    Activo
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
