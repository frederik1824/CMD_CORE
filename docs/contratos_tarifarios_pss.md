# Módulo: Contratos & Tarifarios PSS

Este módulo permite administrar convenios de servicios de salud y tarifarios pactados entre la ARS y cada Prestadora de Servicios de Salud (PSS).

## 1. Tablas y Modelo de Datos
*   **`pss_contracts`**: Cabecera del contrato (vigencia, número de convenio, tipo).
*   **`pss_contract_versions`**: Versionamiento para control de cambios histórico.
*   **`pss_tariff_schedules`**: Catálogo que agrupa un listado de tarifas activas.
*   **`pss_tariff_items`**: Tarifas individuales por código Simon/Cups con reglas clínicas.
*   **`pss_tariff_imports`**: Logs de importación de archivos CSV.
*   **`pss_contract_logs`**: Bitácora de auditoría contractual.

## 2. Flujo de Negocio
1.  **Crear Contrato**: Se define la prestadora, fechas de vigencia y número de convenio. Se autogenera la versión `1.0.0` y el tarifario inicial.
2.  **Configurar Tarifas**: Se añaden tarifas individuales o se importa un tarifario masivo por CSV.
3.  **Versionar**: Al realizar cambios contractuales mayores, se crea una nueva versión (`1.1.0`, `2.0.0`) duplicando el tarifario anterior para edición.

## 3. Demostración (Demo)
1. Ingrese a **Core / PSS / Contratos & Tarifarios V2** en el menú.
2. Seleccione un contrato activo.
3. Vaya a la pestaña **Importación CSV**, descargue la plantilla, agregue tarifas e impórtelas.
4. Modifique una tarifa individual y compruebe el log en **Historial de Cambios**.

## 4. Extensión para Producción
En un ambiente real de producción:
*   Integrar carga de contratos en formato PDF firmados digitalmente.
*   Conectar el importador a un parser Excel compatible con el Catálogo de Prestaciones de la SISALRIL.
