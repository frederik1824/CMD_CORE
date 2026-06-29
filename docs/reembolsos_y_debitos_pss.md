# Reembolsos y Débitos Compensados a PSS

Este documento especifica el marco operativo para la tramitación de reembolsos excepcionales a afiliados y su posterior recuperación mediante débito automático a prestadoras (PSS) bajo la Resolución RA 251_2023.

## 1. Plazos Operativos Legales
*   **Solicitud del Afiliado:** El afiliado tiene un plazo de hasta **120 días calendario** contados a partir de la fecha de la factura o pago para solicitar el reembolso ante la ARS. Solicitudes presentadas después de 120 días son rechazadas automáticamente por extemporaneidad.
*   **Respuesta de la ARS:** La ARS tiene un plazo máximo de **10 días hábiles** para emitir una resolución (Aprobación, Rechazo o Glosa) contados a partir del momento en que el expediente esté completo de documentación soporte.

---

## 2. Requisitos de Documentación Soporte
Para que un expediente sea catalogado como "Completo" y se inicie el plazo de los 10 días hábiles de respuesta, el afiliado debe adjuntar obligatoriamente:
1.  **Factura Original:** Emitida por la PSS con su NCF de Ley.
2.  **Recibo de Pago:** Documento que demuestre el desembolso efectuado.

---

## 3. Lógica de Cobro Indebido y Compensación Contable
Si la resolución determina que la PSS de la red incurrió en un **Cobro Indebido** (por ejemplo, cobrando por encima de las tarifas pactadas en el contrato, cobrando copagos en el régimen subsidiado o exigiendo depósitos de garantía):

1.  **Aprobación del Reembolso:** La ARS le paga el monto aprobado al afiliado emitiendo una transferencia de banco (crédito a cuenta 1102) y asienta una cuenta por cobrar a la PSS (débito a cuenta 110406).
2.  **Compensación de Cuenta por Cobrar:** Contablemente, para recuperar estos fondos, la ARS aplica una compensación automática contra las cuentas por pagar (CXP) de reclamaciones futuras o concurrentes presentadas por la misma PSS:
    $$\text{CXP PSS (Debitar 210501)} \iff \text{CXC PSS Cobro Indebido (Acreditar 110406)}$$
3.  **Resultado Operativo:** El pago final a la PSS en el siguiente lote de desembolso se reduce por el monto compensado, deduciendo automáticamente el cobro indebido.
