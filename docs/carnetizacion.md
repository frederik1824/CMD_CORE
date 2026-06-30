# Carnetización e Insumos

## Descripción
Flujo completo de solicitudes, impresión física de carnets con descuento automático de stocks de plástico y cinta, y tracking de transferencias y entregas formales a los afiliados.

## Entidades Principales
- `PrintingCenter`: Sucursales equipadas con impresoras térmicas de carnets.
- `PrintingSupply`: Inventario de plásticos PVC y cintas.
- `PrintingSupplyMovement` y `CarnetAdjustment`: Logs de entradas, mermas y salidas de stock.
- `CarnetRequest`: Solicitudes de carnets individuales y lotes de impresión.
- `CarnetDelivery`: Recibo de entrega formal firmado por el afiliado.
- `CarnetTransfer`: Despachos de carnets impresos entre oficinas.

## Instrucciones Demo
1. Vaya a **Carnetización -> Solicitudes Carnets** para registrar un nuevo pedido de plástico.
2. Acceda a **Bandeja Impresión** para procesar los carnets y descontar insumos de inventario.