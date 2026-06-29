# Módulo: Autorizaciones Médicas ARS (Core)

Módulo interno utilizado por los representantes y auditores de la ARS para registrar y evaluar solicitudes directas (vía llamada, correo, presencial o whatsapp).

## 1. Vistas y Funciones
*   **Dashboard**: Monitor de rendimiento operativo, overrides aplicados y alertas del día.
*   **Bandeja General**: Búsqueda avanzada de autorizaciones con filtros de origen y canal.
*   **Formulario Paso a Paso**: Creación de autorizaciones con buscadores predictivos de Afiliados, PSS y Servicios del Catálogo PDSS.
*   **Bandejas de Especialidad**:
    *   *Auditoría Médica*: Casos de alto costo, cirugías o internamiento.
    *   *Revisión Administrativa*: Excesos de tarifas o prestadores sin contrato.

## 2. Override Controlado
Permite a auditores autorizados aprobar de forma excepcional solicitudes rechazadas (e.g. urgencia vital):
*   Se requiere justificación clínica/administrativa obligatoria.
*   Todo override queda grabado en la tabla `authorization_overrides` y visible en el timeline de auditoría para fines de trazabilidad.

## 3. Demostración (Demo)
1. Ingrese a **Dashboard Core ARS**.
2. Vaya a **Registrar Nueva Autorización**. Complete los pasos seleccionando un afiliado activo y un servicio contratado.
3. Someta la solicitud. En el detalle, haga clic en **Aplicar Override Manual** e ingrese una justificación. Confirme la aprobación forzada.
