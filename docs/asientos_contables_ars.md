# Manual de Asientos Contables de la ARS

Este documento describe la estructura y las cuentas contables de débito y crédito utilizadas para registrar las operaciones principales en el libro diario por partida doble.

## 1. Dispersión de Cápitas (Unipago)
Cuando se procesa una dispersión mensual de cápitas recibidas de Unipago, se reconoce el ingreso total devengado, el gasto de comisión de recaudo y el ingreso de fondos neto a bancos:

*   **Débito:** `110204` - Banco de Reservas (Monto Neto Recibido)
*   **Débito:** `510202` - Gasto de Administración de Unipago (Comisión de Recaudo)
*   **Crédito:** `410101` - Ingresos Cápita SFS Régimen Contributivo (Total Devengado)

---

## 2. Provisión / Reserva de Reclamaciones Médicas (Siniestros)
Al recibir y auditar una reclamación de una PSS, se reconoce el gasto médico en base al devengo y se constituye una reserva técnica de pasivo para el desembolso:

*   **Débito:** `510101` - Gasto de Hospitalización y Clínicas (Monto Aprobado)
*   **Crédito:** `210103` - Reserva Técnica de Siniestros en Trámite (Pasivo)

---

## 3. Pago de Lotes a PSS
Cuando la ARS efectúa la transferencia bancaria para liquidar un lote de cuentas por pagar (CXP) de las PSS:

*   **Débito:** `210501` - Cuentas por Pagar PSS (Monto Neto del Lote)
*   **Crédito:** `110204` - Banco de Reservas (Desembolso Real)

---

## 4. Conciliación de Glosas Médicas
Si tras una glosa u objeción médica de una factura la PSS concilia un acuerdo en el que se aprueba un pago adicional (levante de glosa parcial o total), se asienta una cuenta por pagar complementaria:

*   **Débito:** `510101` - Gasto de Hospitalización y Clínicas (Monto Acordado)
*   **Crédito:** `210501` - Cuentas por Pagar PSS (Monto Conciliado a Pagar)

---

## 5. Reembolsos Excepcionales de Afiliados
Cuando la ARS aprueba un reembolso por cobro indebido de una PSS de la red, se paga al afiliado y se registra una cuenta por cobrar a la PSS:

### Paso 1: Registro del Reembolso y Cuenta por Cobrar PSS
*   **Débito:** `110406` - Cuentas por Cobrar a Prestadoras (PSS) por Cobro Indebido
*   **Crédito:** `110204` - Banco de Reservas (Monto pagado al Afiliado)

### Paso 2: Compensación y Débito Automático a PSS
Para recuperar el cobro indebido, la ARS debita de forma automática la cuenta por cobrar de las facturas pendientes de pago de la PSS:
*   **Débito:** `210501` - Cuentas por Pagar PSS (Monto Compensado)
*   **Crédito:** `110406` - Cuentas por Cobrar a Prestadoras (PSS) por Cobro Indebido
