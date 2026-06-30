# Planes de Salud y Coberturas

## Descripción
Permite configurar el catálogo de planes de salud (complementarios, voluntarios, alternativos) y sus correspondientes niveles de cobertura, porcentajes de copagos, límites financieros, carencias y reglas automáticas de derivación.

## Entidades Principales
- `HealthPlan`: Plan de salud core (Ej. Plan Complementario, Especial).
- `HealthPlanCoverage`: Coberturas asignadas a códigos de servicio/procedimiento PDSS.
- `CoverageDerivationRule`: Reglas lógicas de cobertura (edad, origen, tipo de afiliado).
- `CoverageLimit`: Límites anuales o por evento aplicados a los planes.

## Instrucciones Demo
1. Vaya a **Planes & Coberturas -> Planes de Salud** para registrar nuevos planes complementarios.
2. Ingrese a **Coberturas por Plan** para configurar los porcentajes de copago de cada servicio.