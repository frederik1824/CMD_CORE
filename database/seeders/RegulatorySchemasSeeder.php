<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegulatorySchema;
use App\Models\RegulatorySchemaSection;
use App\Models\RegulatorySchemaField;

class RegulatorySchemasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $esquemas = [
            [
                'schema_code' => '0005',
                'name' => 'Balance de Comprobación',
                'description' => 'Reporte mensual de saldos contables y cuentas del mayor general de la ARS.',
                'module_source' => 'Contabilidad',
                'record_length' => 120,
                'fields' => [
                    ['field_name' => 'account_code', 'label' => 'Código de Cuenta', 'type' => 'AN', 'len' => 20, 'source' => 'account_code'],
                    ['field_name' => 'account_name', 'label' => 'Nombre Cuenta', 'type' => 'AN', 'len' => 40, 'source' => 'account_name'],
                    ['field_name' => 'debit', 'label' => 'Monto Débito', 'type' => 'DECIMAL', 'len' => 15, 'source' => 'debit'],
                    ['field_name' => 'credit', 'label' => 'Monto Crédito', 'type' => 'DECIMAL', 'len' => 15, 'source' => 'credit'],
                ]
            ],
            [
                'schema_code' => '0006',
                'name' => 'Pagos por Comisiones a Promotores',
                'description' => 'Esquema de control y auditoría de comisiones abonadas a los agentes promotores de salud.',
                'module_source' => 'Promotores',
                'record_length' => 90,
                'fields' => [
                    ['field_name' => 'promoter_id', 'label' => 'ID Promotor', 'type' => 'N', 'len' => 10, 'source' => 'promoter_id'],
                    ['field_name' => 'promoter_license', 'label' => 'Licencia Promotor', 'type' => 'AN', 'len' => 20, 'source' => 'promoter_license'],
                    ['field_name' => 'monto', 'label' => 'Monto Comisión', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'monto'],
                    ['field_name' => 'status', 'label' => 'Estado Pago', 'type' => 'AN', 'len' => 10, 'source' => 'status'],
                ]
            ],
            [
                'schema_code' => '0007',
                'name' => 'Reclamaciones de las PSS',
                'description' => 'Reporte mensual de facturas y reclamaciones tramitadas por los prestadores.',
                'module_source' => 'Reclamaciones',
                'record_length' => 150,
                'fields' => [
                    ['field_name' => 'claim_number', 'label' => 'Número Reclamación', 'type' => 'AN', 'len' => 20, 'source' => 'claim_number'],
                    ['field_name' => 'pss_rnc', 'label' => 'RNC Prestador', 'type' => 'AN', 'len' => 15, 'source' => 'pss_rnc'],
                    ['field_name' => 'affiliate_nui', 'label' => 'NUI Afiliado', 'type' => 'N', 'len' => 10, 'source' => 'affiliate_nui'],
                    ['field_name' => 'service_code', 'label' => 'Servicio PDSS', 'type' => 'AN', 'len' => 15, 'source' => 'service_code'],
                    ['field_name' => 'monto_reclamado', 'label' => 'Monto Reclamado', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'monto_reclamado'],
                    ['field_name' => 'monto_pagado', 'label' => 'Monto Liquidado', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'monto_pagado'],
                ]
            ],
            [
                'schema_code' => '0026',
                'name' => 'Prestadoras de Servicios de Salud (PSS)',
                'description' => 'Catálogo oficial de clínicas, laboratorios y farmacias con contratos vigentes.',
                'module_source' => 'Prestadores',
                'record_length' => 110,
                'fields' => [
                    ['field_name' => 'rnc', 'label' => 'RNC/Cédula PSS', 'type' => 'AN', 'len' => 15, 'source' => 'rnc'],
                    ['field_name' => 'name', 'label' => 'Razón Social', 'type' => 'AN', 'len' => 50, 'source' => 'name'],
                    ['field_name' => 'license', 'label' => 'Código Habilitación', 'type' => 'AN', 'len' => 20, 'source' => 'license'],
                    ['field_name' => 'type', 'label' => 'Tipo Prestador', 'type' => 'AN', 'len' => 15, 'source' => 'type'],
                ]
            ],
            [
                'schema_code' => '0027',
                'name' => 'Médicos de las ARS',
                'description' => 'Padrón de médicos autorizados y vinculados para auditoría clínica.',
                'module_source' => 'Prestadores',
                'record_length' => 100,
                'fields' => [
                    ['field_name' => 'cedula', 'label' => 'Cédula Profesional', 'type' => 'AN', 'len' => 15, 'source' => 'cedula'],
                    ['field_name' => 'exequatur', 'label' => 'Número Exequátur', 'type' => 'AN', 'len' => 15, 'source' => 'exequatur'],
                    ['field_name' => 'specialty', 'label' => 'Especialidad Médica', 'type' => 'AN', 'len' => 30, 'source' => 'specialty'],
                    ['field_name' => 'status', 'label' => 'Estatus Contrato', 'type' => 'AN', 'len' => 10, 'source' => 'status'],
                ]
            ],
            [
                'schema_code' => '0028',
                'name' => 'Red de Prestadores de Servicios',
                'description' => 'Asociación y mapeo de la red de prestadores habilitada por tipo de plan de salud.',
                'module_source' => 'Prestadores',
                'record_length' => 90,
                'fields' => [
                    ['field_name' => 'code', 'label' => 'Código Prestador', 'type' => 'AN', 'len' => 15, 'source' => 'code'],
                    ['field_name' => 'name', 'label' => 'Nombre / Razón Social', 'type' => 'AN', 'len' => 45, 'source' => 'name'],
                    ['field_name' => 'status', 'label' => 'Estatus Red', 'type' => 'AN', 'len' => 10, 'source' => 'status'],
                ]
            ],
            [
                'schema_code' => '0031',
                'name' => 'Cartera de Afiliados Titulares y Dependientes',
                'description' => 'Padrón regulatorio de afiliados a planes voluntarios y complementarios.',
                'module_source' => 'Afiliaciones',
                'record_length' => 200,
                'fields' => [
                    ['field_name' => 'nui', 'label' => 'NUI Afiliado', 'type' => 'N', 'len' => 10, 'source' => 'nui'],
                    ['field_name' => 'document_type', 'label' => 'Tipo Documento', 'type' => 'N', 'len' => 1, 'source' => 'document_type', 'catalog' => 'CAT-DOC-TYPE'],
                    ['field_name' => 'document_number', 'label' => 'Cédula/Pasaporte', 'type' => 'AN', 'len' => 15, 'source' => 'document_number'],
                    ['field_name' => 'name', 'label' => 'Nombres', 'type' => 'AN', 'len' => 30, 'source' => 'name'],
                    ['field_name' => 'last_name', 'label' => 'Apellidos', 'type' => 'AN', 'len' => 30, 'source' => 'last_name'],
                    ['field_name' => 'gender', 'label' => 'Género (M/F)', 'type' => 'AN', 'len' => 1, 'source' => 'gender'],
                    ['field_name' => 'parentesco', 'label' => 'Parentesco', 'type' => 'N', 'len' => 2, 'source' => 'parentesco', 'catalog' => 'CAT-PARENTESCO'],
                    ['field_name' => 'affiliate_type', 'label' => 'Tipo (T/D)', 'type' => 'AN', 'len' => 1, 'source' => 'affiliate_type'],
                    ['field_name' => 'plan_number', 'label' => 'Código Plan', 'type' => 'AN', 'len' => 15, 'source' => 'plan_number'],
                    ['field_name' => 'policy_number', 'label' => 'Póliza', 'type' => 'AN', 'len' => 15, 'source' => 'policy_number'],
                    ['field_name' => 'premium', 'label' => 'Prima Comercial', 'type' => 'DECIMAL', 'len' => 10, 'source' => 'premium'],
                    ['field_name' => 'payment_mode', 'label' => 'Frecuencia Pago', 'type' => 'N', 'len' => 1, 'source' => 'payment_mode', 'catalog' => 'CAT-PAY-MODE'],
                    ['field_name' => 'promoter_code', 'label' => 'Promotor Asignado', 'type' => 'AN', 'len' => 10, 'source' => 'promoter_code'],
                ]
            ],
            [
                'schema_code' => '0032',
                'name' => 'Cartera de Dependientes Voluntarios',
                'description' => 'Padrón de núcleo familiar de dependientes bajo pólizas voluntarias.',
                'module_source' => 'Afiliaciones',
                'record_length' => 150,
                'fields' => [
                    ['field_name' => 'nui', 'label' => 'NUI Dependiente', 'type' => 'N', 'len' => 10, 'source' => 'nui'],
                    ['field_name' => 'document_number', 'label' => 'Cédula Dependiente', 'type' => 'AN', 'len' => 15, 'source' => 'document_number'],
                    ['field_name' => 'name', 'label' => 'Nombres', 'type' => 'AN', 'len' => 30, 'source' => 'name'],
                    ['field_name' => 'last_name', 'label' => 'Apellidos', 'type' => 'AN', 'len' => 30, 'source' => 'last_name'],
                    ['field_name' => 'parentesco', 'label' => 'Relación Parentesco', 'type' => 'N', 'len' => 2, 'source' => 'parentesco', 'catalog' => 'CAT-PARENTESCO'],
                ]
            ],
            [
                'schema_code' => '0033',
                'name' => 'Afiliados Titulares a Planes Voluntarios',
                'description' => 'Cartera de titulares adheridos a planes de salud voluntarios.',
                'module_source' => 'Afiliaciones',
                'record_length' => 120,
                'fields' => [
                    ['field_name' => 'nui', 'label' => 'NUI Titular', 'type' => 'N', 'len' => 10, 'source' => 'nui'],
                    ['field_name' => 'document_number', 'label' => 'Cédula Titular', 'type' => 'AN', 'len' => 15, 'source' => 'document_number'],
                    ['field_name' => 'plan_number', 'label' => 'Plan Voluntario', 'type' => 'AN', 'len' => 15, 'source' => 'plan_number'],
                    ['field_name' => 'policy_number', 'label' => 'Póliza', 'type' => 'AN', 'len' => 15, 'source' => 'policy_number'],
                ]
            ],
            [
                'schema_code' => '0034',
                'name' => 'Afiliados Dependientes a Planes Voluntarios',
                'description' => 'Censo de dependientes directos e indirectos vinculados a planes voluntarios.',
                'module_source' => 'Afiliaciones',
                'record_length' => 120,
                'fields' => [
                    ['field_name' => 'nui', 'label' => 'NUI Dependiente', 'type' => 'N', 'len' => 10, 'source' => 'nui'],
                    ['field_name' => 'document_number', 'label' => 'Cédula Dependiente', 'type' => 'AN', 'len' => 15, 'source' => 'document_number'],
                    ['field_name' => 'parentesco', 'label' => 'Parentesco', 'type' => 'N', 'len' => 2, 'source' => 'parentesco', 'catalog' => 'CAT-PARENTESCO'],
                ]
            ],
            [
                'schema_code' => '0035',
                'name' => 'Indexación del Costo del PBS',
                'description' => 'Evaluación de costos, prestaciones y montos para seguimiento del PBS.',
                'module_source' => 'Reclamaciones',
                'record_length' => 110,
                'fields' => [
                    ['field_name' => 'code', 'label' => 'Código Servicio', 'type' => 'AN', 'len' => 15, 'source' => 'code'],
                    ['field_name' => 'value', 'label' => 'Monto Evaluado', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'value'],
                    ['field_name' => 'status', 'label' => 'Estatus Evaluación', 'type' => 'AN', 'len' => 10, 'source' => 'status'],
                ]
            ],
            [
                'schema_code' => '0036',
                'name' => 'Seguimiento de Diagnósticos',
                'description' => 'Monitoreo de diagnósticos clínicos recurrentes reportados por afiliados.',
                'module_source' => 'Reclamaciones',
                'record_length' => 110,
                'fields' => [
                    ['field_name' => 'code', 'label' => 'Código CIE-10', 'type' => 'AN', 'len' => 10, 'source' => 'code'],
                    ['field_name' => 'name', 'label' => 'Descripción Diagnóstico', 'type' => 'AN', 'len' => 50, 'source' => 'name'],
                    ['field_name' => 'status', 'label' => 'Estatus Diagnóstico', 'type' => 'AN', 'len' => 10, 'source' => 'status'],
                ]
            ],
            [
                'schema_code' => '0037',
                'name' => 'Reporte de Accidentes de Tránsito',
                'description' => 'Bandeja de reclamos médicos por colisión terrestre bajo el FONAMAT.',
                'module_source' => 'Reclamaciones',
                'record_length' => 120,
                'fields' => [
                    ['field_name' => 'document', 'label' => 'Cédula Afectado', 'type' => 'AN', 'len' => 15, 'source' => 'document'],
                    ['field_name' => 'code', 'label' => 'Código Evento', 'type' => 'AN', 'len' => 15, 'source' => 'code'],
                    ['field_name' => 'value', 'label' => 'Monto Cubierto', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'value'],
                ]
            ],
            [
                'schema_code' => '0040',
                'name' => 'Evaluación de Programas PyP',
                'description' => 'Seguimiento mensual de metas, talleres y charlas preventivas de la ARS.',
                'module_source' => 'PyP',
                'record_length' => 100,
                'fields' => [
                    ['field_name' => 'code', 'label' => 'Código Actividad', 'type' => 'AN', 'len' => 15, 'source' => 'code'],
                    ['field_name' => 'name', 'label' => 'Nombre Charla', 'type' => 'AN', 'len' => 45, 'source' => 'name'],
                    ['field_name' => 'value', 'label' => 'Participantes', 'type' => 'N', 'len' => 6, 'source' => 'value'],
                ]
            ],
            [
                'schema_code' => '0041',
                'name' => 'Primer Nivel Régimen Subsidiado',
                'description' => 'Servicios asistenciales de atención primaria prestados a afiliados subsidiados.',
                'module_source' => 'Reclamaciones',
                'record_length' => 110,
                'fields' => [
                    ['field_name' => 'code', 'label' => 'Código Prestación', 'type' => 'AN', 'len' => 15, 'source' => 'code'],
                    ['field_name' => 'name', 'label' => 'Nombre Prestación', 'type' => 'AN', 'len' => 45, 'source' => 'name'],
                    ['field_name' => 'value', 'label' => 'Monto Pagado', 'type' => 'DECIMAL', 'len' => 12, 'source' => 'value'],
                ]
            ]
        ];

        foreach ($esquemas as $e) {
            $schema = RegulatorySchema::create([
                'schema_code' => $e['schema_code'],
                'name' => $e['name'],
                'description' => $e['description'],
                'module_source' => $e['module_source'],
                'report_type' => 'TXT',
                'record_length' => $e['record_length'],
                'periodicity' => 'Mensual',
                'simon_enabled' => true,
                'status' => 'Activo'
            ]);

            // Secciones (Header, Detail, Summary)
            RegulatorySchemaSection::create([
                'regulatory_schema_id' => $schema->id,
                'section_type' => 'header',
                'name' => 'Encabezado del Archivo',
                'record_type_constant' => 'E',
                'order' => 1
            ]);

            RegulatorySchemaSection::create([
                'regulatory_schema_id' => $schema->id,
                'section_type' => 'detail',
                'name' => 'Detalle del Reporte',
                'record_type_constant' => 'D',
                'order' => 2
            ]);

            RegulatorySchemaSection::create([
                'regulatory_schema_id' => $schema->id,
                'section_type' => 'summary',
                'name' => 'Sumario de Control',
                'record_type_constant' => 'S',
                'order' => 3
            ]);

            // Campos del detalle
            $pos = 2; // Comenzar en posición 2 (la posición 1 es para la constante 'D')
            $ord = 1;
            foreach ($e['fields'] as $f) {
                $endPos = $pos + $f['len'] - 1;

                RegulatorySchemaField::create([
                    'regulatory_schema_id' => $schema->id,
                    'section_type' => 'detail',
                    'field_name' => $f['field_name'],
                    'field_label' => $f['label'],
                    'data_type' => $f['type'],
                    'length' => $f['len'],
                    'required' => true,
                    'start_position' => $pos,
                    'end_position' => $endPos,
                    'padding' => $f['type'] === 'N' ? 'left' : 'right',
                    'padding_character' => $f['type'] === 'N' ? '0' : ' ',
                    'catalog_code' => $f['catalog'] ?? null,
                    'source_model' => $e['module_source'],
                    'source_field' => $f['source'],
                    'order' => $ord++,
                    'is_active' => true
                ]);

                $pos = $endPos + 1;
            }
        }
    }
}
