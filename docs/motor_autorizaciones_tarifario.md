# Módulo: Motor de Autorizaciones por Tarifario

El motor evalúa al vuelo cada solicitud médica comparando los parámetros contractuales activos del prestador.

## 1. Reglas y Validaciones
*   **Contrato Activo**: La PSS debe tener un contrato en estado `vigente` para la fecha de solicitud.
*   **Servicio Contratado**: El código Simon del catálogo PDSS debe estar explícitamente pactado en `pss_tariff_items` para esa prestadora.
*   **Nivel de Atención**: Compara si el nivel requerido por el procedimiento está autorizado en la PSS.
*   **Tope y Exceso**: Si el monto solicitado supera el monto contratado, se deriva a Revisión Administrativa.
*   **Auditoría y Documentos**: Valida si el servicio es de Alto Costo, cirugía, o requiere adjuntos obligatorios.

## 2. Lógica de Snapshots
Para evitar que cambios de tarifas alteren autorizaciones del pasado, al aprobar una solicitud se guardan snapshots de:
*   `pss_contract_id` y `pss_contract_version_id`.
*   `contracted_amount_snapshot` (tarifa pactada en ese momento).
*   `affiliate_copay_amount` y `ars_amount` calculados.
Las reclamaciones futuras liquidarán en base a este snapshot.

## 3. Demostración (Demo)
1. Someta una solicitud para una PSS sin contrato. Verifique el rechazo inmediato.
2. Someta una solicitud con un monto superior al tarifado. Verifique el desvío a **Revisión Administrativa**.
3. Cambie la tarifa de un servicio y compruebe que las autorizaciones históricas mantienen sus montos de snapshot intactos.
