# Diferencias: Portal PSS vs Core ARS

El sistema separa estrictamente los canales de entrada para mantener un control riguroso de la seguridad y el flujo de autorizaciones.

## 1. Comparación de Módulos

| Característica | Portal PSS (Externo) | Core ARS (Interno) |
| :--- | :--- | :--- |
| **Usuario Destino** | Prestadoras de Servicios de Salud (clínicas, laboratorios) | Representantes de atención, auditores médicos, supervisores |
| **Propósito** | Solicitar coberturas directas y registrar reclamaciones | Gestión administrativa, control, auditoría, override y tesorería |
| **Visibilidad de Tarifas** | Limitada únicamente a sus tarifas contratadas | Catálogo completo de todas las prestadoras y comparativos |
| **Aprobación Manual** | No permitida (sometido a reglas automáticas) | Permitida mediante override justificado con perfil de auditor |
| **Notas y Bitácoras** | Solo comentarios de cara al prestador | Timeline completo, justificaciones contables y notas privadas |
| **Impresión y Firma** | Prioridad alta (volante termal/vertical para firma) | Prioridad baja (enfoque en trazabilidad y auditoría de datos) |

## 2. Relación con el Proceso de Reclamaciones
*   Las prestadoras someten solicitudes desde el Portal PSS basadas en tarifas contratadas.
*   Una vez aprobadas (automática o mediante override en el Core), se congela el snapshot de la tarifa.
*   Al someter la reclamación facturada, el módulo contable liquida basándose exclusivamente en el snapshot congelado, previniendo alteraciones por actualizaciones de contratos posteriores.
