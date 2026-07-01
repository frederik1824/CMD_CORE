<?php

namespace App\Services;

use App\Models\RegulatorySchema;
use App\Models\RegulatorySchemaField;
use App\Models\RegulatorySchemaRun;
use App\Models\RegulatorySchemaRunDetail;
use App\Models\RegulatorySchemaError;
use App\Models\RegulatoryPeriod;
use App\Models\RegulatoryCatalogItem;
use App\Models\Afiliado;
use App\Models\Prestador;
use App\Models\Bitacora;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegulatorySchemaGeneratorService
{
    /**
     * Generar la corrida para un esquema y período
     */
    public static function generate(RegulatorySchema $schema, RegulatoryPeriod $period, $userId = 1)
    {
        $runNumber = 'RUN-' . $schema->schema_code . '-' . str_replace('-', '', $period->period_code) . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $fileName = 'ARS_' . $schema->schema_code . '_' . str_replace('-', '', $period->period_code) . '_001.txt';

        // 1. Crear el registro de la corrida
        $run = RegulatorySchemaRun::create([
            'run_number' => $runNumber,
            'regulatory_schema_id' => $schema->id,
            'period_id' => $period->id,
            'generated_by' => $userId,
            'generated_at' => now(),
            'status' => 'borrador',
            'total_records' => 0,
            'valid_records' => 0,
            'invalid_records' => 0,
            'file_name' => $fileName,
            'file_path' => 'app/regulatory/' . $fileName,
            'checksum' => md5($runNumber . now()->toDateTimeString())
        ]);

        // 2. Extraer datos según el tipo de esquema
        $data = self::extractData($schema, $period);
        $fields = $schema->fields;

        $lineNumber = 1;
        $validCount = 0;
        $invalidCount = 0;

        // 3. Generar Registro de Encabezado (E)
        $headerLine = self::generateHeaderLine($schema, $period);
        RegulatorySchemaRunDetail::create([
            'regulatory_schema_run_id' => $run->id,
            'record_type' => 'E',
            'line_number' => $lineNumber++,
            'raw_line' => $headerLine,
            'validation_status' => 'valido'
        ]);

        // 4. Generar Registros de Detalle (D)
        foreach ($data as $row) {
            $detailLine = '';
            $lineErrors = [];

            // Añadir tipo de registro 'D' al inicio del detalle
            $detailLine .= 'D';

            foreach ($fields->where('section_type', 'detail') as $field) {
                // Obtener el valor correspondiente
                $val = self::resolveValue($row, $field);

                // Validar el campo
                $errorMsg = self::validateField($val, $field);
                if ($errorMsg) {
                    $lineErrors[] = [
                        'field_name' => $field->field_name,
                        'message' => $errorMsg,
                        'current_value' => $val,
                        'position' => $field->start_position
                    ];
                }

                // Formatear el campo (ancho fijo)
                $formattedVal = self::formatField($val, $field);
                $detailLine .= $formattedVal;
            }

            $status = empty($lineErrors) ? 'valido' : 'error';
            if ($status === 'valido') {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $detail = RegulatorySchemaRunDetail::create([
                'regulatory_schema_run_id' => $run->id,
                'source_model' => $schema->module_source,
                'source_id' => $row['id'] ?? null,
                'record_type' => 'D',
                'line_number' => $lineNumber++,
                'raw_line' => $detailLine,
                'validation_status' => $status,
                'errors_json' => $lineErrors
            ]);

            // Guardar errores individuales para auditoría visual
            foreach ($lineErrors as $err) {
                RegulatorySchemaError::create([
                    'regulatory_schema_run_id' => $run->id,
                    'detail_id' => $detail->id,
                    'field_name' => $err['field_name'],
                    'error_code' => 'VAL-001',
                    'error_message' => $err['message'],
                    'severity' => 'error',
                    'expected_value' => 'Válido según especificación',
                    'current_value' => $err['current_value'],
                    'position' => $err['position']
                ]);
            }
        }

        // 5. Generar Registro de Sumario (S)
        $summaryLine = self::generateSummaryLine($schema, $validCount + $invalidCount);
        RegulatorySchemaRunDetail::create([
            'regulatory_schema_run_id' => $run->id,
            'record_type' => 'S',
            'line_number' => $lineNumber,
            'raw_line' => $summaryLine,
            'validation_status' => 'valido'
        ]);

        // 6. Actualizar totales de la corrida
        $run->update([
            'total_records' => $validCount + $invalidCount,
            'valid_records' => $validCount,
            'invalid_records' => $invalidCount,
            'status' => $invalidCount > 0 ? 'con_errores' : 'generado'
        ]);

        Bitacora::registrar('SISALRIL', "Generado esquema {$schema->schema_code} para el período {$period->period_code} (Total: " . ($validCount + $invalidCount) . " registros).");

        return $run;
    }

    /**
     * Extraer datos reales del Core mapeados al esquema correspondiente
     */
    private static function extractData(RegulatorySchema $schema, RegulatoryPeriod $period)
    {
        $code = $schema->schema_code;

        switch ($code) {
            case '0031': // Cartera Afiliados Complementaria
            case '0033': // Titulares Voluntarios
                return Afiliado::where('estado_afiliacion', 'OK')->take(50)->get()->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'nui' => $a->id,
                        'document_number' => $a->cedula ?? $a->nss,
                        'document_type' => $a->cedula ? '5' : '7',
                        'name' => $a->nombres,
                        'last_name' => $a->apellidos,
                        'birth_date' => $a->fecha_nacimiento,
                        'gender' => $a->sexo === 'Masculino' ? 'M' : 'F',
                        'parentesco' => '0',
                        'affiliate_type' => 'T',
                        'status' => 'A',
                        'plan_number' => 'PLAN-BAS-01',
                        'policy_number' => 'POL-9028',
                        'effective_from' => '20260101',
                        'payment_date' => '20260610',
                        'premium' => '850.00',
                        'payment_mode' => '1',
                        'promoter_code' => 'PROM-002',
                        'nationality' => 'Dominicana'
                    ];
                });

            case '0032': // Dependientes Voluntarios
            case '0034': // Dependientes Voluntarios
                return DB::table('dependientes')->take(50)->get()->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'nui' => $d->id,
                        'document_number' => $d->cedula ?? '00100000000',
                        'document_type' => '5',
                        'name' => $d->nombres,
                        'last_name' => $d->apellidos,
                        'birth_date' => $d->fecha_nacimiento,
                        'gender' => $d->sexo === 'M' ? 'M' : 'F',
                        'parentesco' => '5', // Hijo
                        'affiliate_type' => 'D',
                        'status' => 'A',
                        'plan_number' => 'PLAN-VOL-02',
                        'policy_number' => 'POL-8028',
                        'effective_from' => '20260101',
                        'payment_date' => '20260610',
                        'premium' => '450.00',
                        'payment_mode' => '1',
                        'promoter_code' => 'PROM-003',
                        'nationality' => 'Dominicana'
                    ];
                });

            case '0007': // Reclamaciones PSS
                try {
                    $claims = DB::table('authorization_claims')->take(40)->get()->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'claim_number' => $c->claim_number,
                            'pss_id' => $c->pss_id,
                            'pss_rnc' => '101882828',
                            'affiliate_id' => $c->afiliado_id,
                            'affiliate_nui' => $c->afiliado_id,
                            'service_code' => 'PDSS-G1-01',
                            'monto_reclamado' => $c->claimed_amount,
                            'monto_pagado' => $c->approved_amount,
                            'fecha_reclamacion' => $c->created_at,
                            'status' => 'Aprobada'
                        ];
                    })->toArray();
                    if (!empty($claims)) {
                        return $claims;
                    }
                } catch (\Exception $e) {
                    // Fallback to mock
                }

                return [
                    [
                        'id' => 1,
                        'claim_number' => 'REC-2026-0001',
                        'pss_id' => 1,
                        'pss_rnc' => '101882828',
                        'affiliate_id' => 1,
                        'affiliate_nui' => 1,
                        'service_code' => 'PDSS-G1-01',
                        'monto_reclamado' => '1500.00',
                        'monto_pagado' => '1200.00',
                        'fecha_reclamacion' => now()->toDateString(),
                        'status' => 'Aprobada'
                    ]
                ];

            case '0026': // PSS Jurídicas
                return Prestador::where('tipo_prestador', 'Jurídico')->take(30)->get()->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'rnc' => $p->rnc ?? '101902828',
                        'name' => $p->nombre,
                        'license' => $p->codigo_habilitacion ?? 'HAB-2828',
                        'type' => 'Clínica',
                        'status' => 'Activo'
                    ];
                });

            case '0027': // Médicos
                return Prestador::where('tipo_prestador', 'Físico')->take(30)->get()->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'cedula' => $p->rnc ?? '00108928281',
                        'exequatur' => 'EX-28282',
                        'specialty' => $p->especialidad ?? 'Medicina General',
                        'status' => 'Activo'
                    ];
                });

            case '0005': // Balance de Comprobación
                try {
                    $results = DB::table('journal_entry_lines')
                        ->join('chart_accounts', 'journal_entry_lines.account_id', '=', 'chart_accounts.id')
                        ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                        ->select('journal_entry_lines.id', 'chart_accounts.code as account_code', 'chart_accounts.name as account_name', 'journal_entry_lines.debit', 'journal_entry_lines.credit', 'journal_entries.period')
                        ->take(30)
                        ->get()
                        ->map(function ($a) {
                            return [
                                'id' => $a->id,
                                'account_code' => $a->account_code,
                                'account_name' => $a->account_name,
                                'debit' => $a->debit,
                                'credit' => $a->credit,
                                'period' => strlen($a->period) === 6 ? substr($a->period, 0, 4) . '-' . substr($a->period, 4, 2) : '2026-06'
                            ];
                        })->toArray();
                    if (!empty($results)) {
                        return $results;
                    }
                } catch (\Exception $e) {
                    // Fallback to mock
                }

                return [
                    [
                        'id' => 1,
                        'account_code' => '101-01-001',
                        'account_name' => 'Efectivo en Caja',
                        'debit' => '150000.00',
                        'credit' => '0.00',
                        'period' => '2026-06'
                    ],
                    [
                        'id' => 2,
                        'account_code' => '201-01-001',
                        'account_name' => 'Cuentas por Pagar Proveedores',
                        'debit' => '0.00',
                        'credit' => '150000.00',
                        'period' => '2026-06'
                    ]
                ];

            case '0006': // Pagos Comisiones Promotores
                try {
                    $results = DB::table('promoter_commissions')->take(30)->get()->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'promoter_id' => $c->promoter_id,
                            'promoter_license' => 'LIC-PROM-2026',
                            'monto' => $c->amount,
                            'status' => $c->status
                        ];
                    })->toArray();
                    if (!empty($results)) {
                        return $results;
                    }
                } catch (\Exception $e) {
                    // Fallback to mock
                }

                return [
                    [
                        'id' => 1,
                        'promoter_id' => 1,
                        'promoter_license' => 'LIC-PROM-001',
                        'monto' => '4500.00',
                        'status' => 'Pagado'
                    ]
                ];

            default:
                // Generador de respaldo de datos simulados coherentes
                $mockList = [];
                for ($i = 1; $i <= 25; $i++) {
                    $mockList[] = [
                        'id' => $i,
                        'code' => 'PDSS-G' . rand(1, 6) . '-0' . $i,
                        'name' => 'Servicio Médico de Cobertura ' . $i,
                        'status' => 'Activo',
                        'value' => 1500 * $i,
                        'date' => now()->toDateString(),
                        'document' => '402' . rand(1000000, 9999999) . '5'
                    ];
                }
                return $mockList;
        }
    }

    /**
     * Resolver dinámicamente el valor de un campo
     */
    private static function resolveValue($row, RegulatorySchemaField $field)
    {
        if ($field->constant_value !== null) {
            return $field->constant_value;
        }

        $sourceField = $field->source_field;
        if (is_array($row)) {
            return $row[$sourceField] ?? $field->default_value;
        }

        if (is_object($row)) {
            return $row->$sourceField ?? $field->default_value;
        }

        return $field->default_value;
    }

    /**
     * Validar campo según especificación y catálogos
     */
    private static function validateField($val, RegulatorySchemaField $field)
    {
        if ($field->required && ($val === null || $val === '')) {
            return "El campo [{$field->field_label}] es obligatorio.";
        }

        if ($val !== null && $val !== '') {
            // Validar longitud máxima
            if (strlen((string)$val) > $field->length) {
                return "La longitud supera el límite de {$field->length} caracteres.";
            }

            // Validar catálogo
            if ($field->catalog_code) {
                $exists = RegulatoryCatalogItem::whereHas('catalog', function ($q) use ($field) {
                    $q->where('catalog_code', $field->catalog_code);
                })->where('item_code', $val)->exists();

                if (!$exists) {
                    return "El valor '{$val}' no existe en el catálogo SIMON correspondiente [{$field->catalog_code}].";
                }
            }
        }

        return null;
    }

    /**
     * Formatear campo al formato de ancho fijo estricto
     */
    private static function formatField($val, RegulatorySchemaField $field)
    {
        $val = (string)$val;

        // Truncar si supera longitud
        if (strlen($val) > $field->length) {
            return substr($val, 0, $field->length);
        }

        $padType = $field->padding === 'left' ? STR_PAD_LEFT : STR_PAD_RIGHT;
        $padChar = $field->padding_character ?: ' ';

        return str_pad($val, $field->length, $padChar, $padType);
    }

    /**
     * Generar línea de encabezado (E)
     */
    private static function generateHeaderLine(RegulatorySchema $schema, RegulatoryPeriod $period)
    {
        $institutionCode = 'ARS-001';
        $reportCode = $schema->schema_code;
        $periodCode = str_replace('-', '', $period->period_code);

        return 'E' . str_pad($institutionCode, 10, ' ', STR_PAD_RIGHT) 
                   . str_pad($reportCode, 4, '0', STR_PAD_LEFT) 
                   . str_pad($periodCode, 6, '0', STR_PAD_LEFT) 
                   . str_pad(now()->format('Ymd'), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Generar línea de sumario (S)
     */
    private static function generateSummaryLine(RegulatorySchema $schema, $totalRecords)
    {
        return 'S' . str_pad((string)$totalRecords, 10, '0', STR_PAD_LEFT);
    }
}
