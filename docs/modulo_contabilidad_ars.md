# Módulo Contable-Financiero de la ARS

Este documento detalla la arquitectura, las directrices regulatorias de la SISALRIL y la lógica de implementación del Core Contable de la ARS bajo el método del devengo por partida doble.

## 1. Fundamentos Contables y Regulaciones
Las Administradoras de Riesgos de Salud (ARS) en la República Dominicana están reguladas contable y financieramente por la Superintendencia de Salud y Riesgos Laborales (SISALRIL) y la Tesorería de la Seguridad Social (TSS).

### Principio del Devengo (Accrual Basis)
Toda transacción se registra en el momento en que se genera la obligación o el derecho, independientemente de cuándo se efectúe el flujo físico de efectivo:
*   **Ingresos por Cápitas:** Se devengan mensualmente cuando la TSS realiza la dispersión, registrándose un ingreso neto tras descontar las comisiones de Unipago/Unisigma.
*   **Gastos por Siniestros (Reclamaciones):** Se reconocen como gasto de salud en el período en que el afiliado recibe la prestación médica, registrando simultáneamente un pasivo de Reserva Técnica por Siniestros en Trámite.
*   **Reservas Técnicas:** Son pasivos regulatorios constituidos para garantizar la solvencia de la ARS. Incluyen las cápitas no devengadas, los siniestros liquidados pendientes de pago y los siniestros en trámite.

## 2. Indicadores de Solvencia y Margen
La solvencia y liquidez de la ARS son monitoreadas mediante los siguientes indicadores regulatorios clave:

### Patrimonio Técnico Real vs. Margen de Solvencia
El Patrimonio Técnico Real (PTR) representa el patrimonio de solvencia constituido por el capital, reservas de capital y utilidades retenidas. Debe ser en todo momento **igual o mayor** al Margen de Solvencia Mínimo Requerido por la SISALRIL:
$$\text{ PTR} \ge \text{Margen de Solvencia}$$
Si el PTR cae por debajo del margen mínimo, la ARS entra en estado de alerta por insolvencia técnica y debe recibir aportes inmediatos de los socios.

### Tasa de Siniestralidad Neta
Mide la relación entre los gastos de prestaciones de salud y los ingresos de cápitas recibidas:
$$\text{Tasa de Siniestralidad} = \left(\frac{\text{Gastos por Prestación de Salud (Siniestros)}}{\text{Ingresos de Cápitas Devengadas}}\right) \times 100$$
*   **Límite Máximo Recomendado:** 85.00%.
*   Si la siniestralidad supera este porcentaje, el margen operativo se reduce drásticamente, poniendo en riesgo la cobertura de gastos administrativos y comisiones.
