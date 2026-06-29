# Base de Conocimiento de Desarrollo: ARS CMD Core

Este documento contiene la memoria técnica, especificaciones arquitectónicas e inventario de características construidas para la plataforma **ARS CMD Core**. Su objetivo es transferir el conocimiento completo a una nueva instancia de IA o equipo de desarrollo para continuar la evolución del sistema de forma fluida.

---

## 1. Arquitectura y Stack Tecnológico

La plataforma está diseñada sobre un esquema monolítico modular en **Laravel 11.x** utilizando:
*   **Base de Datos:** SQLite.
*   **Frontend:** HTML5, CSS (Vanilla y TailwindCSS), Alpine.js para interactividad responsiva.
*   **Servicios Externos Mockeados:** Simuladores completos para Unipago/TSS (Cápitas y Afiliaciones) y Unisigma.
*   **Tuning de Base de Datos para Concurrencia (SQLite WAL):**
    *   Habilitado el modo de diario `WAL` (Write-Ahead Logging) y un `busy_timeout` de `5000` milisegundos en `config/database.php`.
    *   Migrados los controladores de sesión y caché de `database` a `file` en `.env`. Esto previene de raíz el bloqueo de SQLite (`General error: 5 database is locked`) por peticiones concurrentes de actualización de sesiones en peticiones AJAX.
    *   Envoltura de reintentos (`retry(5, ..., 100)`) en el registro de la `Bitacora` para evitar interrupciones en logs concurrentes.

---

## 2. Inventario de Módulos y Funcionalidades Construidas

### A. Catálogo Oficial PDSS v10/v11 y Motor de Reglas
*   **Importación:** Script de importación desde archivos Excel de Sisalril.
*   **Estructuración:** Clasificación de servicios médicos, asignación de coberturas base, y marcas lógicas para requerimiento de auditoría y documentos soporte (recetas médicas).

### B. Módulo de Contratos y Tarifarios PSS (Prestadores de Salud)
*   **CRUD y Mantenimiento PSS:** Pantalla y flujos para registrar y editar Prestadores de Servicios de Salud (PSS), incluyendo validación de unicidad de RNC.
*   **Edición del Catálogo de Servicios:** Pantalla y flujos para modificar códigos, descripciones, porcentajes de cobertura base, y marcas de alto costo de los servicios médicos en el Core ARS.
*   **Motor de Tarifas:** Gestión de tarifas especiales por contrato con prestadoras.
*   **Snapshot Histórico:** Al autorizar, se copia la tarifa vigente en la transacción, evitando que futuras actualizaciones de tarifas alteren retroactivamente las autorizaciones ya facturadas.

### C. Portal del Afiliado y Carga Masiva (Estilo Google Workspace)
*   **Dashboard e Interfaz:** Limpia, en tonos claros con tipografía Outfit e iconos de Google Material Symbols.
*   **Sidebar Responsivo:** Solucionado error en AlpineJS en pantallas de escritorio donde el menú lateral se ocultaba por defecto. Ahora permanece fijo en computadoras y es interactivo en móviles.
*   **Carga Masiva:** Procesamiento de archivos de afiliados para preclasificación en lote.

### D. Control de Formularios y Contratos de Afiliación (Unipago)
*   **Módulo Nuevo:** `/core/afiliaciones/formularios-contratos`.
*   **Modelos de Datos:**
    *   `AffiliationContractRange`: Bloques de números de formularios autorizados.
    *   `AffiliationContractNumber`: Números individuales (Estados: `disponible`, `reservado`, `usado`, `enviado_unipago`, `ok`, `pe`, `re`, `bloqueado`).
    *   `AffiliationContractMovement`: Bitácora histórica individual de transacciones.
    *   `AffiliationContractReservation`: Control temporal de expiraciones.
*   **Asignación Segura:** `AffiliationContractNumberService` ejecuta bloqueos de fila (`lockForUpdate`) para evitar colisiones de concurrencia al afiliar titulares.
*   **Comando Artisan:** `php artisan contracts:release-expired-reservations` para liberar reservas vencidas.
*   **Simulador Bidireccional:** Integración con `UnipagoMockService` para sincronizar estatus del contrato con la respuesta final de la carga masiva.

### E. Módulo de Finanzas, Contabilidad y Reclamaciones
*   **CxP y Reclamaciones:** Flujo de mesa de entrada, auditoría médica de cuentas, lotes de pago y generación de asientos contables.
*   **Asientos Automáticos:** Registro contable tras cobro de cápitas de Unipago (ej. DOP 1,580.75).

---

## 3. Inventario de Archivos Clave Creados o Modificados

### Base de Datos y Modelos
*   [AffiliationContractRange.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Models/AffiliationContractRange.php)
*   [AffiliationContractNumber.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Models/AffiliationContractNumber.php)
*   [AffiliationContractMovement.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Models/AffiliationContractMovement.php)
*   [AffiliationContractReservation.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Models/AffiliationContractReservation.php)
*   [Afiliado.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Models/Afiliado.php) (Campos inyectados de contrato y relaciones).
*   [Migración Contratos](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/database/migrations/2026_06_28_000001_create_affiliation_contract_control_tables.php)

### Controladores y Lógica de Negocio
*   [AffiliationContractController.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Http/Controllers/AffiliationContractController.php) (Dashboard, creación e importación).
*   [PssController.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Http/Controllers/PssController.php) (Mapeo de edición de PSS y servicios).
*   [AfiliadoController.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Http/Controllers/AfiliadoController.php) (Flujo de consumo de contratos).
*   [AffiliationContractNumberService.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Services/AffiliationContractNumberService.php) (Transacciones de base de datos).
*   [UnipagoMockService.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/app/Services/UnipagoMockService.php) (Procesador del simulador).

### Vistas y Maquetación
*   [dashboard.blade.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/resources/views/ars/afiliaciones/contratos/dashboard.blade.php) (KPIs).
*   [show_range.blade.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/resources/views/ars/afiliaciones/contratos/show_range.blade.php) (Ficha con 4 pestañas).
*   [edit.blade.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/resources/views/ars/pss/edit.blade.php) (Formulario PSS).
*   [servicios_edit.blade.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/resources/views/ars/pss/servicios_edit.blade.php) (Formulario de catálogo de servicios).

### Rutas
*   [web.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/routes/web.php)

---

## 4. Verificación e Integridad

### Suite de Pruebas
Las pruebas automatizadas validan la integridad financiera, del motor de tarifas y del control de contratos:
*   [AffiliationContractControlTest.php](file:///c:/Users/frede.FREDERIKLOPEZ18/Videos/CMD core/CMD_CORE/tests/Feature/AffiliationContractControlTest.php) (Creación de rangos, no solapamientos, reservas y consumos).
*   `ContractAuthorizationRulesTest` y `ClaimLifecycleTest` (Ciclo de vida de reclamos).

Ejecución de la suite completa de verificación:
```bash
php artisan test
```
*(Todas las pruebas están pasando con éxito).*
