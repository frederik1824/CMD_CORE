<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Catalogo;
use App\Models\Lote;
use App\Models\LoteDetalle;
use App\Models\AffiliationBatch;
use App\Models\AffiliationBatchDetail;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\DispersionCutDetail;
use App\Models\UnipagoResponseCode;
use App\Models\UnipagoMockService;
use App\Models\UnipagoMockScenario;
use App\Models\HolderAffiliationRequest;
use App\Models\DependentAffiliationRequest;
use App\Models\AffiliationContractRange;
use App\Models\AffiliationContractNumber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CargaDemoCompletaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuarios Demo requeridos
        $this->crearUsuariosDemo();

        // 2. Crear Códigos de Respuesta de Unipago
        $this->crearCodigosRespuesta();

        // 3. Crear Catálogo de Web Services
        $this->crearCatalogoServicios();

        // 4. Crear Rangos de Contratos y Números Disponibles
        $this->crearContratosRangos();

        // 5. Inyectar Ciudadanos y Afiliados Titulares
        $this->crearAfiliadosYCiudadanos();
    }

    private function crearUsuariosDemo(): void
    {
        $roles = [
            ['name' => 'Oficial de Afiliaciones', 'email' => 'afiliaciones@demo.com', 'role' => 'Analista Afiliación'],
            ['name' => 'Supervisor de Afiliaciones', 'email' => 'supervisor.afiliaciones@demo.com', 'role' => 'Supervisor Afiliación'],
            ['name' => 'Unipago Integración Mock', 'email' => 'unipago.mock@demo.com', 'role' => 'Administrador ARS'],
        ];

        foreach ($roles as $r) {
            User::updateOrCreate(
                ['email' => $r['email']],
                [
                    'name' => $r['name'],
                    'password' => Hash::make('password'),
                    'role' => $r['role'],
                ]
            );
        }
    }

    private function crearCodigosRespuesta(): void
    {
        $codes = [
            ['code' => 'OK', 'type' => 'ok', 'title' => 'Solicitud Aceptada', 'description' => 'La validación y procesamiento de afiliación concluyó exitosamente.', 'recommended_action' => 'No requiere acción. El afiliado se encuentra activo.'],
            ['code' => 'PE64', 'type' => 'pe', 'title' => 'Pendiente verificar aporte', 'description' => 'El empleador reporta salario por debajo de la cotización mínima o no registra aporte en tesorería.', 'recommended_action' => 'Verificar con el empleador la regularización de la nómina TSS.'],
            ['code' => 'PE75', 'type' => 'pe', 'title' => 'Ciudadano no existe en Maestro de JCE', 'description' => 'La identificación (Cédula/NSS) no coincide con el Padrón Nacional Electoral.', 'recommended_action' => 'Solicitar copia física de la cédula del afiliado para rectificación.'],
            ['code' => 'PE10036', 'type' => 'pe', 'title' => 'Pendiente de documento obligatorio', 'description' => 'Falta acta de nacimiento o documento que valide el parentesco del dependiente.', 'recommended_action' => 'Subir el documento de soporte faltante en el panel de solicitudes.'],
            ['code' => 'RE001', 'type' => 're', 'title' => 'Afiliado activo en otra ARS', 'description' => 'El ciudadano ya cuenta con cobertura de salud activa en una ARS competidora y no aplica traspaso.', 'recommended_action' => 'Rechazar la solicitud de afiliación o tramitar traspaso correspondiente.'],
            ['code' => 'RE002', 'type' => 're', 'title' => 'Contrato fuera de rango aprobado', 'description' => 'El número de formulario/contrato utilizado no pertenece a la serie asignada.', 'recommended_action' => 'Asignar un número de contrato válido del catálogo de formularios.'],
            ['code' => 'RE003', 'type' => 're', 'title' => 'Contrato ya utilizado', 'description' => 'El número de contrato/formulario ya fue procesado y consumido en una afiliación previa.', 'recommended_action' => 'Utilizar un número de contrato disponible.'],
            ['code' => 'RE004', 'type' => 're', 'title' => 'Documento digital ilegible', 'description' => 'La digitalización del soporte presenta problemas de visualización o validez.', 'recommended_action' => 'Solicitar al promotor escanear nuevamente el documento soporte.'],
            ['code' => 'RE005', 'type' => 're', 'title' => 'Dependiente duplicado', 'description' => 'El dependiente ya se encuentra registrado activamente bajo el mismo núcleo familiar.', 'recommended_action' => 'Verificar el núcleo familiar del titular.'],
            ['code' => 'RE006', 'type' => 're', 'title' => 'Titular no activo', 'description' => 'El titular del grupo familiar se encuentra inactivo o suspendido.', 'recommended_action' => 'Regularizar primero la afiliación del afiliado cotizante titular.'],
            ['code' => 'RE007', 'type' => 're', 'title' => 'Parentesco no permitido', 'description' => 'La relación de parentesco declarada no es elegible para el plan contributivo.', 'recommended_action' => 'Verificar catálogo de parentescos autorizados por SISALRIL.'],
            ['code' => 'EV001', 'type' => 'ev', 'title' => 'Error de estructura del lote', 'description' => 'El archivo XML/CSV enviado no cumple con la estructura requerida.', 'recommended_action' => 'Validar la codificación de caracteres y etiquetas del archivo.'],
            ['code' => 'ER500', 'type' => 'er', 'title' => 'Error técnico del servidor TSS', 'description' => 'Fallo de conexión o timeout con los servidores centrales de Unipago.', 'recommended_action' => 'Reintentar la transmisión del lote en horarios de baja demanda.'],
        ];

        foreach ($codes as $c) {
            UnipagoResponseCode::updateOrCreate(['code' => $c['code']], $c);
        }
    }

    private function crearCatalogoServicios(): void
    {
        $services = [
            ['service_code' => 'CONSULTA_CIUDADANO', 'service_name' => 'Consulta de Ciudadano', 'description' => 'Consulta maestro de ciudadanos JCE para validar datos demográficos.', 'endpoint_mock' => '/api/unipago/ciudadano', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 120, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_AFILIADO', 'service_name' => 'Consulta de Afiliado', 'description' => 'Verifica si el ciudadano ya está afiliado a otra ARS y su estatus.', 'endpoint_mock' => '/api/unipago/afiliado', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 150, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_NUCLEO', 'service_name' => 'Consulta de Núcleo Familiar', 'description' => 'Obtiene los dependientes registrados del titular.', 'endpoint_mock' => '/api/unipago/nucleo', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 180, 'error_probability' => 0],
            ['service_code' => 'VALIDA_TITULAR', 'service_name' => 'Validación de Titular', 'description' => 'Prevalida datos laborales, nómina y cotizaciones del titular.', 'endpoint_mock' => '/api/unipago/validar-titular', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 200, 'error_probability' => 2],
            ['service_code' => 'VALIDA_DEPENDIENTE', 'service_name' => 'Validación de Dependiente', 'description' => 'Prevalida elegibilidad del dependiente según parentesco y edad.', 'endpoint_mock' => '/api/unipago/validar-dependiente', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 200, 'error_probability' => 1],
            ['service_code' => 'ENVIO_TITULAR', 'service_name' => 'Envío de Solicitud de Titular', 'description' => 'Registra una solicitud de afiliación individual de titular.', 'endpoint_mock' => '/api/unipago/solicitud/titular', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 250, 'error_probability' => 3],
            ['service_code' => 'ENVIO_DEPENDIENTE', 'service_name' => 'Envío de Solicitud de Dependiente', 'description' => 'Registra una solicitud de afiliación individual de dependiente.', 'endpoint_mock' => '/api/unipago/solicitud/dependiente', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 250, 'error_probability' => 3],
            ['service_code' => 'ENVIO_LOTE_AFILIACION', 'service_name' => 'Envío de Lote de Afiliación', 'description' => 'Carga y valida estructuralmente un lote masivo de afiliados.', 'endpoint_mock' => '/api/unipago/lote/cargar', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 450, 'error_probability' => 5],
            ['service_code' => 'CONSULTA_LOTE', 'service_name' => 'Consulta de Lote', 'description' => 'Obtiene el resumen y estado de procesamiento de un lote transmitido.', 'endpoint_mock' => '/api/unipago/lote/consultar', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 110, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_SOLICITUD', 'service_name' => 'Consulta de Solicitud', 'description' => 'Verifica el estatus individual de procesamiento de una solicitud.', 'endpoint_mock' => '/api/unipago/solicitud/consultar', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 100, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_CAMBIOS_FECHA', 'service_name' => 'Consulta de Cambios por Fecha', 'description' => 'Obtiene novedades reportadas a la ARS en un rango de fechas.', 'endpoint_mock' => '/api/unipago/cambios', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 300, 'error_probability' => 2],
            ['service_code' => 'ENVIO_NOVEDAD_AFILIACION', 'service_name' => 'Novedades de Afiliación', 'description' => 'Envía bajas, ingresos o actualizaciones de datos de afiliados.', 'endpoint_mock' => '/api/unipago/novedad', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 200, 'error_probability' => 1],
            ['service_code' => 'CONSULTA_NOVEDAD', 'service_name' => 'Consulta de Novedad', 'description' => 'Obtiene el estatus de procesamiento de una novedad transmitida.', 'endpoint_mock' => '/api/unipago/novedad/consultar', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 100, 'error_probability' => 0],
            ['service_code' => 'VALIDA_CONTRATO_FORMULARIO', 'service_name' => 'Validación de Contrato/Formulario', 'description' => 'Valida si el número de contrato pertenece a un rango válido asignado a la ARS.', 'endpoint_mock' => '/api/unipago/contrato/validar', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 90, 'error_probability' => 0],
            ['service_code' => 'NOTIFICACION_CAPITA', 'service_name' => 'Notificación de Individualización de Cápita', 'description' => 'Reporta la asignación de cápita de un afiliado para el período.', 'endpoint_mock' => '/api/unipago/capita/notificar', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 150, 'error_probability' => 0],
            ['service_code' => 'CONFIRMACION_INDIVIDUALIZACION', 'service_name' => 'Confirmación de Individualización', 'description' => 'Confirma o rechaza la asignación de cápita por parte de la ARS.', 'endpoint_mock' => '/api/unipago/capita/confirmar', 'method' => 'POST', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 140, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_CAPITAS_NOTIFICADAS', 'service_name' => 'Consulta de Cápitas Notificadas', 'description' => 'Obtiene el listado de cápitas del período y su estatus.', 'endpoint_mock' => '/api/unipago/capita/consultar', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 200, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_DISPERSION', 'service_name' => 'Consulta de Dispersión', 'description' => 'Consulta los cortes y montos liquidados por Unipago.', 'endpoint_mock' => '/api/unipago/dispersion', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 250, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_CALENDARIO_CORTES', 'service_name' => 'Calendario de Cortes', 'description' => 'Obtiene el cronograma oficial de dispersión para el año fiscal.', 'endpoint_mock' => '/api/unipago/cortes/calendario', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 100, 'error_probability' => 0],
            ['service_code' => 'CONSULTA_ESTADO_SERVICIO', 'service_name' => 'Consulta de Estado de Servicios', 'description' => 'Monitorea la latencia y disponibilidad de la API de Unipago.', 'endpoint_mock' => '/api/unipago/estado', 'method' => 'GET', 'protocol' => 'json', 'default_response_type' => 'OK', 'simulated_latency_ms' => 50, 'error_probability' => 0],
        ];

        foreach ($services as $s) {
            UnipagoMockService::updateOrCreate(['service_code' => $s['service_code']], $s);
        }
    }

    private function crearContratosRangos(): void
    {
        $ranges = [
            ['range_code' => 'RNG-2026-A', 'description' => 'Rango Inicial Contratos 2026', 'start_number' => 100000, 'end_number' => 109999, 'total_numbers' => 10000, 'available_count' => 10000, 'status' => 'activo', 'source' => 'sisalril'],
            ['range_code' => 'RNG-2026-B', 'description' => 'Rango Complementario 2026', 'start_number' => 110000, 'end_number' => 114999, 'total_numbers' => 5000, 'available_count' => 5000, 'status' => 'activo', 'source' => 'manual'],
            ['range_code' => 'RNG-2026-C', 'description' => 'Rango de Emergencia 2026', 'start_number' => 115000, 'end_number' => 115999, 'total_numbers' => 1000, 'available_count' => 1000, 'status' => 'activo', 'source' => 'manual'],
        ];

        foreach ($ranges as $r) {
            $range = AffiliationContractRange::updateOrCreate(['range_code' => $r['range_code']], $r);

            // Generar los primeros 100 números individuales disponibles para cada rango para no saturar SQLite
            $totalGenerados = 100;
            for ($i = 0; $i < $totalGenerados; $i++) {
                $num = $range->start_number + $i;
                AffiliationContractNumber::updateOrCreate(
                    ['contract_number' => (string)$num],
                    [
                        'affiliation_contract_range_id' => $range->id,
                        'status' => 'disponible',
                    ]
                );
            }
            $range->update(['available_count' => $totalGenerados]);
        }
    }

    private function crearAfiliadosYCiudadanos(): void
    {
        $nombresMasculinos = ['Juan', 'José', 'Pedro', 'Manuel', 'Carlos', 'Luis', 'Andrés', 'Francisco', 'Julio', 'Jorge', 'Alejandro', 'Felipe', 'Eduardo', 'Roberto', 'Daniel', 'Marcos', 'Miguel', 'Ricardo', 'Antonio', 'Fernando'];
        $nombresFemeninos = ['María', 'Ana', 'Carmen', 'Josefa', 'Francisca', 'Lucía', 'Isabel', 'Marta', 'Elena', 'Laura', 'Teresa', 'Rosa', 'Antonia', 'Silvia', 'Dolores', 'Beatriz', 'Patricia', 'Cristina', 'Clara', 'Juana'];
        $apellidos = ['Gómez', 'Rodríguez', 'Sánchez', 'Pérez', 'Martínez', 'García', 'López', 'Díaz', 'Fernández', 'Vargas', 'Jiménez', 'Cruz', 'Reyes', 'Hernández', 'Morales', 'Castillo', 'Ramírez', 'Flores', 'Guzmán', 'Ortiz'];
        $provincias = ['Santo Domingo', 'Santiago', 'La Vega', 'San Cristóbal', 'Duarte', 'San Pedro de Macorís', 'La Romana', 'Puerto Plata', 'Espaillat', 'Barahona'];

        $tipoIdCed = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CED')->first()?->id ?? 1;

        // Crear 300 Afiliados Titulares
        for ($i = 1; $i <= 300; $i++) {
            $sexo = ($i % 2 === 0) ? 'M' : 'F';
            $nombre = ($sexo === 'M') ? $nombresMasculinos[$i % 20] : $nombresFemeninos[$i % 20];
            $apellido1 = $apellidos[($i + 3) % 20];
            $apellido2 = $apellidos[($i + 7) % 20];
            
            // Garantizar dígito final coherente
            $cedula = '402' . str_pad($i + 9000, 7, '0', STR_PAD_LEFT) . ($i % 4); // dígitos aptos para Unipago (0, 1, 2, 3), sumamos 9000 para evitar colisiones
            $nss = '1' . str_pad($i + 9000, 8, '0', STR_PAD_LEFT);

            // Evitar duplicidades si se vuelve a correr
            if (Afiliado::where('cedula', $cedula)->exists() || Afiliado::where('nss', $nss)->exists()) {
                continue;
            }

            // Obtener contrato
            $contrato = AffiliationContractNumber::where('status', 'disponible')->first();
            $contratoId = $contrato ? $contrato->id : null;
            $contratoNum = $contrato ? $contrato->contract_number : null;

            $afiliado = Afiliado::create([
                'tipo_identificacion_id' => $tipoIdCed,
                'contract_number_id' => $contratoId,
                'contract_number' => $contratoNum,
                'cedula' => $cedula,
                'nss' => $nss,
                'nombres' => $nombre,
                'primer_apellido' => $apellido1,
                'segundo_apellido' => $apellido2,
                'fecha_nacimiento' => Carbon::now()->subYears(rand(22, 60))->subDays(rand(1, 300))->toDateString(),
                'sexo' => $sexo,
                'provincia' => $provincias[$i % 10],
                'municipio' => 'Municipio ' . ($i % 3 + 1),
                'sector' => 'Sector ' . ($i % 5 + 1),
                'direccion' => 'Calle Principal #' . $i,
                'estado_afiliacion' => 'OK',
                'activo_nomina' => true,
                'tiene_aporte' => true,
                'regimen_actual' => 'Contributivo',
            ]);

            if ($contrato) {
                $contrato->update([
                    'status' => 'ok',
                    'assigned_to_affiliate_id' => $afiliado->id,
                    'used_at' => now(),
                ]);
            }

            // Registrar Grupo Familiar
            $grupo = \App\Models\FamilyGroup::create([
                'holder_affiliate_id' => $afiliado->id,
                'status' => 'activo'
            ]);

            // Generar 1 dependiente para la mitad de los titulares
            if ($i % 2 === 0) {
                $depNombre = ($sexo === 'M') ? $nombresFemeninos[$i % 20] : $nombresMasculinos[$i % 20];
                $depCedula = '402' . str_pad($i + 9500, 7, '0', STR_PAD_LEFT) . '1';
                $depNss = '2' . str_pad($i + 9500, 8, '0', STR_PAD_LEFT);
                $parentescoId = Catalogo::where('grupo', 'parentesco')->where('codigo', ($i % 4 === 0 ? 'CONYUGE' : 'HIJO'))->first()?->id ?? 2;

                if (Dependiente::where('cedula', $depCedula)->exists() || Dependiente::where('nss', $depNss)->exists()) {
                    continue;
                }

                $dep = Dependiente::create([
                    'titular_id' => $afiliado->id,
                    'tipo_identificacion_id' => $tipoIdCed,
                    'parentesco_id' => $parentescoId,
                    'cedula' => $depCedula,
                    'nss' => $depNss,
                    'nombres' => $depNombre,
                    'apellidos' => $apellido1 . ' ' . $apellido2,
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(2, 17))->toDateString(),
                    'sexo' => ($sexo === 'M') ? 'F' : 'M',
                    'tipo_dependiente' => 'Directo',
                    'estado_afiliacion' => 'OK',
                ]);

                \App\Models\FamilyGroupMember::create([
                    'family_group_id' => $grupo->id,
                    'affiliate_id' => $dep->id,
                    'relationship' => ($i % 4 === 0 ? 'Cónyuge' : 'Hijo'),
                    'status' => 'activo',
                    'start_date' => now()->subMonths(6)->toDateString(),
                ]);
            }
        }
    }
}
