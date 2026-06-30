# Análisis de Brecha Funcional (Gap Analysis) - Sistema ARS Core vs. Sistema Legado

Este documento presenta una comparación exhaustiva entre el ecosistema ARS actual y las funcionalidades requeridas del sistema legado de Administración de Riesgos de Salud, con el fin de identificar las funciones faltantes, sugerir las rutas de implementación en el Core y definir su prioridad.

---

## 1. MÓDULO DE RECLAMACIONES
**Ruta base:** `/core/reclamaciones`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Recepción de radicaciones | Parcialmente existente | Ampliar para controlar fecha de entrada y asignación formal de número de radicación. | `/core/reclamaciones/radicaciones` | Alta |
| Corrección de datos en radicaciones | No existe | Crear interfaz para auditar y corregir datos con histórico de cambios. | `/core/reclamaciones/radicaciones/correcciones` | Alta |
| Reportes de radicaciones | No existe | Crear reportes con filtros por PSS, fechas y estados. | `/core/reclamaciones/reportes` | Alta |
| Transferencia de radicaciones | No existe | Crear funcionalidad para reasignar expedientes entre auditores. | `/core/reclamaciones/radicaciones/transferencias` | Media |
| Auditoría retrospectiva | No existe | Crear bandeja de reclamaciones pagadas para auditoría de control concurrente. | `/core/reclamaciones/auditoria-retrospectiva` | Alta |
| Auditoría de facturación | Parcialmente existente | Ampliar para validar facturas físicas y convenios de precios asociados a la PSS. | `/core/reclamaciones/auditoria-facturacion` | Alta |
| Validaciones requeridas | Parcialmente existente | Ampliar el motor de validaciones (períodos de carencia, pre-existencias, etc.). | `/core/reclamaciones/validaciones` | Alta |
| Corrección de NCF | No existe | Crear formulario de modificación de NCF que requiera permiso especial y motivo. | `/core/reclamaciones/ncf` | Alta |
| Reportes de reclamaciones | Parcialmente existente | Ampliar para incluir estado del ciclo de vida y métricas financieras. | `/core/reclamaciones/reportes` | Alta |
| Reportes de devoluciones | No existe | Crear reporte de reclamaciones rechazadas en recepción con motivo de devolución. | `/core/reclamaciones/reportes` | Media |
| Generación de lotes | Parcialmente existente | Crear agrupador de reclamaciones aprobadas listas para generar CXP en bloque. | `/core/reclamaciones/lotes` | Alta |
| Impresión/exportación de lotes | No existe | Crear exportadores de lotes de reclamaciones en formato Excel/PDF por bloques. | `/core/reclamaciones/lotes/exportar` | Media |
| Corrección de NCF de lotes | No existe | Permitir correcciones masivas de NCF sobre lotes de reclamaciones específicas. | `/core/reclamaciones/lotes/ncf` | Media |
| Reportes de lotes | No existe | Reporte consolidado de lotes generados y en proceso de pago. | `/core/reclamaciones/reportes` | Media |
| Conciliación de glosas | Parcialmente existente | Ampliar con estado del acta de conciliación, fecha de firma y resultado formal. | `/core/reclamaciones/glosas` | Alta |
| Notificación de glosas | No existe | Crear canal de alertas y notificaciones electrónicas a PSS. | `/core/reclamaciones/notificaciones` | Alta |
| Solicitud de notas de crédito | No existe | Registrar solicitud automática de nota de crédito por glosas aceptadas por la PSS. | `/core/reclamaciones/notificaciones` | Alta |
| Plantillas de glosas/devoluciones | No existe | Centralizar plantillas de correos de glosas y devoluciones en base a catálogos. | `/core/reclamaciones/plantillas` | Media |
| Configuración de formatos | No existe | Interfaz para seleccionar columnas y cabeceras en reportes exportables. | `/core/reclamaciones/configuracion` | Media |
| Tipificación de glosas | No existe | Crear catálogo parametrizado de motivos y conceptos de glosas (médica, tarifa, etc.). | `/core/reclamaciones/glosas/tipificacion` | Alta |
| Reportes de cuentas por pagar | Parcialmente existente | Ampliar reporte de CXP por PSS agrupando por reclamaciones y estados. | `/core/reclamaciones/cuentas-por-pagar` | Alta |

---

## 2. MÓDULO PyP / PROGRAMAS DE PROMOCIÓN Y PREVENCIÓN
**Ruta base:** `/core/pyp`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Actividades no asistenciales | No existe | Crear catálogo de charlas, talleres y campañas preventivas. | `/core/pyp/actividades-no-asistenciales` | Media |
| Grupos de riesgo | No existe | Mantenimiento de grupos (Materno-Infantil, Crónicos, Cardiovascular, etc.). | `/core/pyp/grupos-riesgo` | Media |
| Factores de riesgo | No existe | Catálogo de factores (Hipertensión, Obesidad, Sedentarismo, Tabaquismo, etc.). | `/core/pyp/factores-riesgo` | Media |
| Tipos de programas | No existe | Clasificación de programas (Promoción, Prevención Primaria, Secundaria). | `/core/pyp/tipos-programas` | Media |
| Programas de salud | No existe | Definición de programas específicos con población objetivo, fechas y estados. | `/core/pyp/programas` | Media |
| Calendario de servicios | No existe | Programación de citas y servicios preventivos por programa de salud. | `/core/pyp/calendario` | Media |
| Gestión de candidatos | No existe | Identificación y filtrado de afiliados candidatos por edad, sexo, provincia, etc. | `/core/pyp/candidatos` | Media |
| Inscripción en programas | No existe | Formulario de inscripción manual y seguimiento del historial clínico del afiliado. | `/core/pyp/inscripciones` | Media |
| Cancelación de inscripción | No existe | Formulario de desvinculación con selección obligatoria de motivo parametrizado. | `/core/pyp/cancelaciones` | Media |
| Reportes del módulo PyP | No existe | Reporte consolidado de participantes, efectividad del programa y distribución. | `/core/pyp/reportes` | Media |

---

## 3. MÓDULO PLANES DE SALUD Y COBERTURAS
**Ruta base:** `/core/planes-salud`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Planes de salud | No existe | Crear catálogo de planes (PDSS, Complementarios, Alternativos, Pensionados). | `/core/planes-salud/planes` | Alta |
| Catálogo de cobertura PDSS | Parcialmente existente | Integrar catálogo de prestaciones oficial del PDSS vinculado a los planes. | `/core/planes-salud/catalogo-pdss` | Alta |
| Catálogo de planes alternativos | No existe | Catálogo de coberturas para planes alternativos o complementarios. | `/core/planes-salud/catalogo-planes-alternativos`| Alta |
| Coberturas por plan | No existe | Definir porcentaje de cobertura, copago, topes y si requiere autoriz. o auditoría. | `/core/planes-salud/coberturas` | Alta |
| Detalle de cobertura | No existe | Detalle por servicio médico y plan asignado. | `/core/planes-salud/detalle-servicio` | Alta |
| Derivar coberturas | No existe | Reglas dinámicas (JSON) para ajustar coberturas según diagnóstico, prestador, etc. | `/core/planes-salud/derivaciones` | Alta |
| Períodos de espera | No existe | Configurar tiempos mínimos de afiliación para activar coberturas específicas. | `/core/planes-salud/periodos-espera` | Alta |
| Topes por grupos y origen | No existe | Configurar topes monetarios anuales, por evento o afiliado en base a servicios. | `/core/planes-salud/topes` | Alta |
| Reportes de coberturas | No existe | Auditoría y trazabilidad de cambios en tarifas y coberturas por plan. | `/core/planes-salud/reportes` | Alta |

---

## 4. MÓDULO DE PRESTADORES
**Ruta base:** `/core/prestadores`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Prestadores Persona Física | Parcialmente existente | Ampliar tabla PSS para diferenciar personas físicas (Médicos) de jurídicas. | `/core/prestadores/personas-fisicas` | Alta |
| Prestadores Persona Jurídica | Parcialmente existente | Ampliar para clasificar Clínicas, Hospitales, Laboratorios y Farmacias. | `/core/prestadores/personas-juridicas` | Alta |
| Auditores médicos | Parcialmente existente | Ampliar mantenimiento con exequatur, especialidad y estado de habilitación. | `/core/prestadores/auditores-medicos` | Alta |
| Mantenimiento serv. contratados| Parcialmente existente | Interfaz para habilitar o deshabilitar servicios específicos contratados por PSS. | `/core/prestadores/servicios-contratados` | Alta |
| Convenios de precios | Parcialmente existente | Ampliar gestión de tarifas vigentes con contratos, versiones y anexos. | `/core/prestadores/convenios-precios` | Alta |
| Consulta precios convenidos | Parcialmente existente | Buscador rápido de precios acordados para autorizaciones y auditorías. | `/core/prestadores/precios-convenidos` | Alta |
| Grupos de prestadores | No existe | Agrupar PSS por corporaciones, redes provinciales o niveles de atención. | `/core/prestadores/grupos` | Alta |
| Red de prestadores por plan | No existe | Asociar grupos o prestadores individuales a las redes de cobertura de cada plan. | `/core/prestadores/red-por-plan` | Alta |
| Habilitar servicios por origen | No existe | Reglas de exclusión/inclusión de PSS por tipo de atención (ambulatorio/emergencia).| `/core/prestadores/habilitacion-servicios`| Alta |
| Consulta georreferencial | No existe | Simular georreferenciación de prestadores por provincia, municipio y sector. | `/core/prestadores/georreferencial` | Alta |
| Contratos servicios capitados | No existe | Registro de contratos capitados: tarifa cápita, población asignada y vigencia. | `/core/prestadores/servicios-capitados/contratos`| Alta |
| Pagos servicios capitados | No existe | Liquidación y desembolso mensual de cápita basado en afiliados adscritos. | `/core/prestadores/servicios-capitados/pagos` | Alta |

---

## 5. MÓDULO DE AFILIACIONES
**Ruta base:** `/core/afiliaciones`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Mantenimiento de afiliados | Parcialmente existente | Ampliar datos demográficos, datos de contacto e histórico de estados. | `/core/afiliaciones/mantenimiento` | Alta |
| Tipos de contratos | No existe | Definición de contratos comerciales (Colectivos, Individuales, Corporativos). | `/core/afiliaciones/tipos-contratos` | Alta |
| Solicitudes de titulares | Parcialmente existente | Ampliar flujo de solicitudes vinculándolo con promotores y prevalidaciones. | `/core/afiliaciones/titulares` | Alta |
| Solicitudes de dependientes | Parcialmente existente | Ampliar flujo con parentescos válidos y control de actas de nacimiento. | `/core/afiliaciones/dependientes` | Alta |
| Gestión de traspasos | No existe | Registrar y simular entrada/salida de afiliados por traspaso entre ARS. | `/core/afiliaciones/traspasos` | Alta |
| Consultas SDSS / Unipago | No existe | Integración con el simulador Unipago para verificar estatus del afiliado en SFS. | `/core/afiliaciones/consultas` | Alta |
| Transferencia de consumos | No existe | Simular traspaso del historial de consumos y acumuladores al cambiar de plan. | `/core/afiliaciones/mantenimiento` | Alta |
| Grupos de afiliados | No existe | Crear agrupaciones familiares y corporativas de afiliados. | `/core/afiliaciones/grupos` | Alta |
| Unidades de negocio | No existe | Configurar unidades de negocio (sucursales, canales) para segmentar el Core. | `/core/afiliaciones/unidades-negocio` | Alta |
| Transacciones de afiliados | No existe | Log de auditoría detallado (antes/después) para cada modificación del afiliado. | `/core/afiliaciones/transacciones` | Alta |
| Generación de archivos | No existe | Exportador masivo de archivos TXT para reporte de novedades a Unipago. | `/core/afiliaciones/archivos` | Alta |
| Reportes de afiliaciones | Parcialmente existente | Reportes de crecimiento, distribución por plan, edad, sexo y región. | `/core/afiliaciones/reportes` | Alta |

---

## 6. MÓDULO DE CARNETIZACIÓN
**Ruta base:** `/core/carnetizacion`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Solicitudes de impresión | No existe | Registro de afiliados pendientes de carnet o reposiciones solicitadas. | `/core/carnetizacion/solicitudes` | Alta |
| Control de impresión | No existe | Flujo para enviar a imprimir en lotes de carnets, marcando estado "impreso". | `/core/carnetizacion/impresion` | Alta |
| Mantenimiento de tipos de carnet| No existe | Configurar plantillas y tipos (titular, dependiente, plan complementario, etc.). | `/core/carnetizacion/tipos-carnets` | Alta |
| Conceptos de impresión | No existe | Parametrizar conceptos de cobro o reposición de carnet (pérdida, deterioro). | `/core/carnetizacion/conceptos` | Alta |
| Entrega de carnet | No existe | Registrar receptor, fecha, firma digital simulada y centro de entrega. | `/core/carnetizacion/entregas` | Alta |
| Transferencias de carnets | No existe | Envío de bloques de carnets desde la oficina central a centros regionales. | `/core/carnetizacion/transferencias` | Alta |
| Localizaciones y centros | No existe | Configurar almacenes y oficinas con impresoras de carnets habilitadas. | `/core/carnetizacion/localizaciones` | Alta |
| Insumos e inventario | No existe | Controlar stock de plásticos de carnets, cintas de color, etc. | `/core/carnetizacion/insumos` | Alta |
| Ajustes e inventarios | No existe | Ajustes de stock por pérdidas, daños o pruebas de impresión. | `/core/carnetizacion/ajustes` | Alta |
| Despacho y devoluciones | No existe | Documentar traslados de insumos y devoluciones de carnets no entregados. | `/core/carnetizacion/despachos` | Alta |
| Reportes del módulo | No existe | Reporte de carnets impresos, entregados, pendientes y stock de insumos. | `/core/carnetizacion/reportes` | Alta |

---

## 7. MÓDULO UNIPAGO
**Ruta base:** `/core/unipago`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Procesos de afiliación | Parcialmente existente | Reestructurar bandeja para procesar lotes de solicitudes Unipago. | `/core/unipago/procesos-afiliacion`| Alta |
| Procesos de novedades | Parcialmente existente | Bandeja para enviar y procesar lotes de novedades contractuales y de datos. | `/core/unipago/procesos-novedades` | Alta |
| Consultas de ciudadanos | Parcialmente existente | Buscar ciudadanos y verificar elegibilidad directamente en base simulada. | `/core/unipago/consultas-ciudadanos` | Alta |
| Notificación de cartera | No existe | Registrar y conciliar la cartera de empleadores de la TSS. | `/core/unipago/notificacion-cartera` | Alta |
| Notificación de cobertura | No existe | Registrar historial de asignaciones de cápitas mensuales. | `/core/unipago/notificacion-cobertura` | Alta |
| Procesos de recaudo | Parcialmente existente | Simular ingreso por dispersión de fondos Unipago-TSS y distribución de cápitas.| `/core/unipago/recaudo` | Alta |
| Gestión de traspasos | No existe | Histórico de aprobaciones de entrada y salida registradas por Unipago. | `/core/unipago/traspasos` | Alta |
| Consulta de procesos | Parcialmente existente | Monitor general de logs de peticiones y respuestas con códigos oficiales. | `/core/unipago/consulta-procesos` | Alta |
| Simulador | Parcialmente existente | Mantener consola de WS para pruebas interactivas de respuestas configuradas. | `/core/unipago/simulador` | Alta |

---

## 8. MÓDULO PROMOTORES
**Ruta base:** `/core/promotores`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Promotores Persona Física | No existe | Registrar promotores independientes (Cédula, Exequatur, Zona). | `/core/promotores/personas-fisicas` | Media |
| Promotores Empresas | No existe | Registrar agencias promotoras externas (RNC, Razón social, Representante). | `/core/promotores/empresas` | Media |
| Contratos de promotores | No existe | Gestionar vigencia de contratos, comisiones pactadas y metas de afiliación. | `/core/promotores/tipos-contratos` | Media |
| Campañas | No existe | Configurar campañas comerciales de afiliación con fechas y comisiones especiales.| `/core/promotores/campanas` | Media |
| Comisiones por campaña | No existe | Configurar y calcular comisiones por afiliados aprobados en una campaña. | `/core/promotores/esquemas-comisiones-campana`| Media |
| Comisiones por gestión | No existe | Configurar comisiones según el tipo de gestión (traspaso, venta nueva). | `/core/promotores/esquemas-comisiones-gestion`| Media |
| Cálculo de comisiones | No existe | Proceso de cierre mensual para calcular la comisión de cada promotor. | `/core/promotores/calculo-comisiones-campana`| Media |
| Reportes del módulo | No existe | Detalle de producción de promotores, comisiones liquidadas y CXP generadas. | `/core/promotores/reportes` | Media |

---

## 9. MÓDULO SISALRIL / REPORTES REGULATORIOS
**Ruta base:** `/core/sisalril`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Esquema 31 (Complementarios) | No existe | Estructurar reporte de afiliados con planes complementarios del período. | `/core/sisalril/esquema-31` | Media |
| Esquema 33 (Titular Voluntario) | No existe | Estructurar reporte de afiliados titulares independientes voluntarios. | `/core/sisalril/esquema-33` | Media |
| Esquema 34 (Dependiente Volunt.)| No existe | Estructurar reporte de dependientes de afiliados voluntarios. | `/core/sisalril/esquema-34` | Media |
| Histórico de exportaciones | No existe | Guardar log de archivos generados, usuario que los descargó y estado. | `/core/sisalril/exportaciones` | Media |
| Reportes de validación | No existe | Validar consistencia de los datos del esquema antes de exportar (mensajes error).| `/core/sisalril/reportes` | Media |

---

## 10. MÓDULO FACTURACIÓN Y SERVICIO AL CLIENTE
**Ruta base:** `/core/facturacion` y `/core/servicio-cliente`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Facturación planes alternativos | No existe | Generación y control de facturas por planes no incluidos en el PDSS. | `/core/facturacion/planes-alternativos`| Base |
| Facturación a grupos afiliados | No existe | Emisión de facturación corporativa consolidada para grupos de afiliados. | `/core/facturacion/grupos-afiliados` | Base |
| Facturas emitidas y NCF | No existe | Control contable de facturas de ingresos, notas de crédito/débito con NCF. | `/core/facturacion/comprobantes` | Base |
| Registro de Casos | No existe | Bandeja para registro de requerimientos de afiliados (solicitudes, quejas). | `/core/servicio-cliente/casos` | Base |
| Seguimiento y SLAs | No existe | Control de tiempos de respuesta según SLA y escalados automáticos. | `/core/servicio-cliente/seguimiento` | Base |
| Reportes de gestión de servicio | No existe | Métricas de satisfacción, casos atendidos y motivos más recurrentes. | `/core/servicio-cliente/reportes` | Base |

---

## 11. CATÁLOGOS GENERALES
**Ruta base:** `/core/catalogos`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Centralización de Catálogos | Parcialmente existente | Centralizar prov/mun/sec, parentescos, tipos de afiliación, glosas, etc. | `/core/catalogos` | Alta |
| Auditoría de cambios | No existe | Registrar qué usuario cambió el estado de un catálogo o su descripción. | `/core/catalogos/auditoria` | Alta |

---

## 12. REPORTES GENERALES
**Ruta base:** `/core/reportes`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Reportes transversales | Parcialmente existente | Unificar en un panel interactivo la descarga de Excel/PDF de todos los módulos. | `/core/reportes` | Media |
| Filtros avanzados | Parcialmente existente | Permitir filtros cruzados de fechas, regiones, planes y prestadoras. | `/core/reportes/filtros` | Media |
| Reportes favoritos y programados| No existe | Simular la programación periódica de reportes y marcación de favoritos. | `/core/reportes/favoritos` | Media |

---

## 13. ROLES, PERMISOS Y AUDITORÍA
**Ruta base:** `/core/administracion`

| Función Legada | Estado Actual | Acción Requerida | Ruta Sugerida | Prioridad |
| :--- | :--- | :--- | :--- | :--- |
| Roles ampliados | Parcialmente existente | Registrar roles (Reclamaciones, PyP, Carnetización, SISALRIL, Facturación, etc.). | `/core/administracion/usuarios` | Alta |
| Permisos granulares | No existe | Configurar matriz de permisos (ver, crear, editar, anular, aprobar, exportar). | `/core/administracion/permisos` | Alta |
| Auditoría de cambios | Parcialmente existente | Extender Bitacora para auditoría visual interactiva de cambios en cualquier entidad.| `/core/administracion/bitacora` | Alta |
