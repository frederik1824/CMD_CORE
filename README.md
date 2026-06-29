# ARS Core Demo - Administradora de Riesgos de Salud

Este proyecto es una aplicación web demo interactiva desarrollada para simular los procesos centrales de una Administradora de Riesgos de Salud (ARS). Está construida con **Laravel 11**, **Tailwind CSS**, y **Alpine.js**, y diseñada bajo principios de arquitectura limpia, alto impacto estético institucional y portabilidad inmediata.

---

## Características Principales

1. **Gestión de Afiliación:**
   - Registro individual y edición de afiliados Titulares y Dependientes.
   - Carga Masiva de Afiliados vía archivo CSV/Excel simulado con prevalidación en tiempo real.
   - Integración y Simulación del procesador de lotes **Unipago / Tesorería de la Seguridad Social (TSS)**.

2. **Gestión de Novedades:**
   - Registro y procesamiento de novedades de afiliación (Cambio de datos, fallecimiento, cambio de plan, etc.).
   - Agrupación por lotes de novedades y simulación de su procesamiento externo.

3. **Motor de Reglas de Autorización Médica:**
   - Evaluación en tiempo real de solicitudes de procedimientos y consultas médicas.
   - Reglas automáticas configuradas:
     - Afiliado activo en la ARS.
     - PSS (Prestadora) con contrato y estado activo.
     - Servicios dentro de la cobertura contratada.
     - Control de tarifas máximas excedidas (desvío a Auditoría Médica).
     - Validación de documentos de soporte clínico requeridos (Recetas / Indicaciones).
     - Control de frecuencia y duplicidad (limite de 30 días).
     - Detección de Alto Costo (desvío directo a Auditoría Médica de prioridad alta).

4. **Portal Simulador PSS (Prestadoras - Modelo ARS CMD):**
   - Panel independiente para clínicas (ej. Clínica Abreu).
   - Interfaz unificada de autorizaciones en una sola página: validación de afiliación, diagnóstico y teléfono, selección de servicios agrupada por especialidad, y tabla interactiva.
   - Buscador reactivo por **Póliza** o **Cédula** (AJAX) con auto-formateado y carga dinámica de la ficha del afiliado.
   - Tabla interactiva de servicios (Alpine.js) con cálculo en tiempo real de Valor Reclamado, Cobertura ARS (tasa base aplicada sobre tarifa contratada) y Diferencia a pagar por el paciente.
   - Envío de solicitudes en lote con procesamiento individual e independiente en el backend.

5. **Auditoría y Bitácora General:**
   - Registro automático e inmutable de eventos de seguridad y negocio.
   - Visor estructurado JSON de logs para control interno de procesos.

6. **Selector de Roles Rápido (Role Switcher):**
   - Panel dinámico flotante en la esquina inferior derecha para alternar entre perfiles de usuario sin cerrar sesión.

---

## Requisitos del Sistema

- **PHP:** v8.2 o superior (v8.3 recomendado) con las extensiones `pdo_sqlite` y `sqlite3` habilitadas.
- **Composer:** v2.x
- **NPM & Node.js:** v18.x o superior
- **Base de Datos:** SQLite (por defecto y configurado para portabilidad inmediata) o MySQL/MariaDB.

---

## Instalación y Configuración Rápida (SQLite)

Para levantar el proyecto en un entorno local de desarrollo de forma inmediata, siga estos pasos:

1. **Clonar o abrir el repositorio** en su directorio local.
2. **Instalar dependencias de Composer:**
   ```bash
   composer install
   ```
3. **Copiar configuración de entorno:**
   ```bash
   copy .env.example .env
   ```
4. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```
5. **Configurar SQLite en el `.env`:**
   Asegúrese de que su archivo `.env` tenga las siguientes líneas (Laravel 11 crea automáticamente el archivo de base de datos sqlite en `database/database.sqlite` si no existe):
   ```env
   DB_CONNECTION=sqlite
   # DB_DATABASE=... (dejar vacío o apuntar al path absoluto)
   ```
6. **Ejecutar Migraciones y Poblar con Datos Demo:**
   Este comando limpia e inicializa la base de datos sqlite, cargando catálogos oficiales, 7 usuarios demo con roles específicos, 10 PSS, 30 servicios médicos con contratos y tarifas asociadas, y más de 100 registros históricos para el dashboard:
   ```bash
   php artisan migrate:fresh --seed
   ```
7. **Instalar y Ejecutar Dependencias de Node.js (Tailwind & Assets):**
   ```bash
   npm install
   npm run dev
   ```
8. **Iniciar el Servidor de Desarrollo:**
   ```bash
   php artisan serve
   ```
   La aplicación estará disponible en [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Credenciales de Acceso Demo

Puede iniciar sesión utilizando el selector de roles en la pantalla principal o el menú flotante en la parte inferior derecha. Si desea acceder de forma manual, la contraseña para todos los usuarios es `password`:

| Rol / Perfil | Correo Electrónico | Descripción de Pruebas |
| :--- | :--- | :--- |
| **Administrador ARS** | `admin@ars.com` | Acceso a configuraciones, bitácora y dashboard completo. |
| **Supervisor Afiliación** | `supervisor@ars.com` | Gestión de lotes, novedades y visualización general. |
| **Analista Afiliación** | `analista@ars.com` | Carga masiva de afiliados, registro manual de dependientes. |
| **Auditor Médico** | `auditor@ars.com` | Evaluación y toma de decisión en autorizaciones derivadas. |
| **Autorizaciones Médicas** | `autorizaciones@ars.com` | Monitoreo y respuesta manual a solicitudes de PSS. |
| **Usuario PSS (Clínica Abreu)** | `pss@ars.com` | Consultas de cobertura y solicitudes de autorización. |
| **Consulta (Solo Lectura)** | `consulta@ars.com` | Acceso pasivo a listados y reportes gráficos. |

---

## Tecnologías Utilizadas

- **Backend:** Laravel 11.x (PHP 8.3)
- **Frontend CSS:** Tailwind CSS (CDN para máxima adaptabilidad)
- **Frontend JS:** Alpine.js (Efectos interactivos, modales y navegación)
- **Gráficos:** Chart.js (Visualización de siniestralidad, estados y prioridades)
- **Base de Datos:** SQLite 3
