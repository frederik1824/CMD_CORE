<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegulatoryCatalog;
use App\Models\RegulatoryCatalogItem;

class SimonCatalogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Catálogo Tipo Documento Probatorio
        $docCatalog = RegulatoryCatalog::create([
            'catalog_code' => 'CAT-DOC-TYPE',
            'name' => 'Tipo Documento Probatorio SIMON',
            'description' => 'Códigos oficiales de identificación probatoria.'
        ]);
        RegulatoryCatalogItem::create(['regulatory_catalog_id' => $docCatalog->id, 'item_code' => '5', 'item_description' => 'Cédula']);
        RegulatoryCatalogItem::create(['regulatory_catalog_id' => $docCatalog->id, 'item_code' => '7', 'item_description' => 'NUI']);
        RegulatoryCatalogItem::create(['regulatory_catalog_id' => $docCatalog->id, 'item_code' => '9', 'item_description' => 'Número Afiliado ARS']);

        // 2. Catálogo Modalidades de Pago
        $payCatalog = RegulatoryCatalog::create([
            'catalog_code' => 'CAT-PAY-MODE',
            'name' => 'Modalidades de Pago SIMON',
            'description' => 'Frecuencia y modalidades de pago de primas.'
        ]);
        $modes = [
            '1' => 'Mensual',
            '2' => 'Bimestral',
            '3' => 'Trimestral',
            '4' => 'Cuatrimestral',
            '5' => 'Semestral',
            '6' => 'Anual',
            '7' => 'No Pagado'
        ];
        foreach ($modes as $code => $desc) {
            RegulatoryCatalogItem::create(['regulatory_catalog_id' => $payCatalog->id, 'item_code' => $code, 'item_description' => $desc]);
        }

        // 3. Catálogo Parentesco
        $parentCatalog = RegulatoryCatalog::create([
            'catalog_code' => 'CAT-PARENTESCO',
            'name' => 'Parentesco Familiar SIMON',
            'description' => 'Grados de parentesco y relación familiar en el padrón.'
        ]);
        $relationships = [
            '0' => 'Titular',
            '1' => 'Padre',
            '2' => 'Madre',
            '3' => 'Esposo',
            '4' => 'Esposa',
            '5' => 'Hijo',
            '6' => 'Hija',
            '7' => 'Hermano',
            '8' => 'Hermana',
            '9' => 'Abuelo',
            '10' => 'Abuela',
            '19' => 'Compañero de vida'
        ];
        foreach ($relationships as $code => $desc) {
            RegulatoryCatalogItem::create(['regulatory_catalog_id' => $parentCatalog->id, 'item_code' => $code, 'item_description' => $desc]);
        }

        // 4. Catálogo Nacionalidades
        $natCatalog = RegulatoryCatalog::create([
            'catalog_code' => 'CAT-NACIONALIDAD',
            'name' => 'Nacionalidades SIMON',
            'description' => 'Lista de países de origen.'
        ]);
        $nationalities = [
            'DOM' => 'Dominicana',
            'USA' => 'Norteamericana',
            'CAN' => 'Canadiense',
            'MEX' => 'Mexicana',
            'HAI' => 'Haitiana',
            'VEN' => 'Venezolana',
            'COL' => 'Colombiana',
            'ESP' => 'Española'
        ];
        foreach ($nationalities as $code => $desc) {
            RegulatoryCatalogItem::create(['regulatory_catalog_id' => $natCatalog->id, 'item_code' => $code, 'item_description' => $desc]);
        }
    }
}
