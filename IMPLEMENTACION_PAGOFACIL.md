# Implementación de PagoFácil - Guía Completa

## 📋 Resumen de Cambios

Se ha implementado la integración de PagoFácil en el sistema de ventas con los siguientes componentes:

### 1. **Archivos Creados**

#### Backend
- `config/pagofacil.php` - Configuración centralizada
- `app/Http/Controllers/PagoFacilController.php` - Controlador principal
- `database/migrations/2025_11_27_000000_update_pagos_table_for_pagofacil.php` - Migración

#### Frontend
- `resources/js/Pages/PagoFacil/QRModal.vue` - Modal para mostrar QR

### 2. **Archivos Modificados**

#### Backend
- `app/Models/Pagos.php` - Agregados campos para PagoFácil
- `app/Http/Controllers/CatalogoController.php` - Retorna venta_id para QR
- `routes/web.php` - Rutas de PagoFácil

#### Frontend
- `resources/js/Pages/Catalogo/Venta/index.vue` - Integración del QR Modal

## 🔄 Flujo de Compra con QR

```
1. Cliente selecciona productos en el catálogo
   ↓
2. Cliente agrega productos al carrito
   ↓
3. Cliente va a "Proceder al Pago" (catalogo/venta)
   ↓
4. Cliente selecciona "Código QR" como método de pago
   ↓
5. Cliente presiona "Confirmar Pedido"
   ↓
6. Se crea la venta en la BD
   ↓
7. Se muestra el QR Modal con el código QR
   ↓
8. Cliente escanea el QR con su billetera digital
   ↓
9. Cliente completa el pago
   ↓
10. PagoFácil envía callback a /pagofacil/callback
    ↓
11. Se actualiza el estado del pago a "completado"
    ↓
12. Se actualiza el estado del pedido a "COMPLETADO"
    ↓
13. Cliente es redirigido a "Mis Pedidos"
```

## 🔑 Configuración Requerida

Tu `.env` ya tiene los valores correctos:

```env
PAGOFACIL_BASE_URL=https://masterqr.pagofacil.com.bo/api/services/v2
PAGOFACIL_TOKEN_SERVICE=51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba
PAGOFACIL_TOKEN_SECRET=0C351C6679844041AA31AF9C
PAGOFACIL_COMMERCE_ID=d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c
PAGOFACIL_CALLBACK_URL=https://a24192474f5e.ngrok-free.app/pagofacil/callback
PAGOFACIL_RETURN_URL=https://a24192474f5e.ngrok-free.app/pagofacil/return
PAGOFACIL_TIMEOUT=30
PAGOFACIL_ENABLE_LOGS=true
PAGOFACIL_ENVIRONMENT=sandbox
```

## 📡 Métodos del Controlador

### `generarQR(Request $request)`
**Ruta:** `POST /pagofacil/generar-qr`
**Middleware:** `auth:sanctum`

Genera un código QR para el pago de una venta.

**Request:**
```json
{
    "venta_id": 1,
    "metodo_pago": "qr"
}
```

**Response (Éxito):**
```json
{
    "success": true,
    "qr_image": "data:image/png;base64,...",
    "transaction_id": "12345",
    "nro_pago": "venta-1-1234567890",
    "pago_id": 1
}
```

### `consultarEstado(Request $request)`
**Ruta:** `POST /pagofacil/consultar-estado`
**Middleware:** `auth:sanctum`

Consulta el estado actual de un pago.

**Request:**
```json
{
    "transaction_id": "12345"
}
```

**Response (Éxito):**
```json
{
    "success": true,
    "data": {
        "pagofacilTransactionId": "12345",
        "companyTransactionId": "venta-1-1234567890",
        "paymentStatus": 1,
        "paymentDate": "2025-11-27",
        "paymentTime": "14:30:00"
    }
}
```

### `callback(Request $request)`
**Ruta:** `POST /pagofacil/callback`
**Middleware:** Ninguno (público)

Recibe notificaciones de PagoFácil cuando se completa un pago.

**Request (desde PagoFácil):**
```json
{
    "PedidoID": "venta-1-1234567890",
    "Fecha": "2025-11-27",
    "Hora": "14:30:00",
    "MetodoPago": "QR",
    "Estado": "2"
}
```

**Response:**
```json
{
    "error": 0,
    "status": 1,
    "message": "Pago procesado correctamente",
    "values": true
}
```

## 🗄️ Cambios en la Base de Datos

Se agregaron las siguientes columnas a la tabla `pagos`:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `referencia_externa` | string | Número de referencia de PagoFácil (venta-{id}-{timestamp}) |
| `transaction_id` | string | ID de transacción de PagoFácil |
| `estado` | enum | Estado del pago: 'pendiente', 'completado', 'rechazado' |
| `fecha_pago` | timestamp | Fecha y hora del pago |
| `datos_pago` | json | Datos completos de la transacción |

## 🎨 Componentes Vue

### QRModal.vue
Modal que muestra el código QR y gestiona el polling para verificar el estado del pago.

**Props:**
- `ventaId` (Number) - ID de la venta
- `total` (Number) - Monto total a pagar
- `show` (Boolean) - Mostrar/ocultar modal

**Eventos:**
- `@close` - Cuando se cierra el modal
- `@success` - Cuando se completa el pago

**Funcionalidades:**
- ✓ Genera QR automáticamente
- ✓ Muestra el código QR en base64
- ✓ Permite descargar el QR
- ✓ Verifica estado cada 5 segundos
- ✓ Muestra número de referencia copiable
- ✓ Instrucciones claras para el usuario

## 🔐 Seguridad

- ✓ Las rutas de generación de QR y consulta de estado requieren autenticación (`auth:sanctum`)
- ✓ El callback es público (requerido por PagoFácil)
- ✓ Se validan todos los datos de entrada
- ✓ Se registran todos los eventos en logs

## 📝 Logs

Todos los eventos se registran en `storage/logs/laravel.log`:

```
[2025-11-27 14:30:00] local.INFO: Inicio del método generarQR
[2025-11-27 14:30:01] local.INFO: Token obtenido de Pago Fácil
[2025-11-27 14:30:02] local.INFO: QR generado correctamente
[2025-11-27 14:30:15] local.INFO: Callback recibido de Pago Fácil
[2025-11-27 14:30:15] local.INFO: Pago actualizado exitosamente desde callback
```

## 🧪 Pruebas

### 1. Generar QR
```bash
curl -X POST http://localhost:8000/pagofacil/generar-qr \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"venta_id": 1, "metodo_pago": "qr"}'
```

### 2. Consultar Estado
```bash
curl -X POST http://localhost:8000/pagofacil/consultar-estado \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"transaction_id": "12345"}'
```

### 3. Simular Callback
```bash
curl -X POST http://localhost:8000/pagofacil/callback \
  -H "Content-Type: application/json" \
  -d '{
    "PedidoID": "venta-1-1234567890",
    "Fecha": "2025-11-27",
    "Hora": "14:30:00",
    "MetodoPago": "QR",
    "Estado": "2"
  }'
```

## ⚠️ Notas Importantes

1. **Ngrok:** Asegúrate de que tu URL de ngrok esté actualizada en `.env` si cambias el túnel.

2. **Estados de Pago:** Los estados que PagoFácil envía pueden variar. Actualmente se mapean:
   - `1`, `2`, `5` → "completado"
   - `3` → "rechazado"
   - Otros → "pendiente"

3. **Polling:** El QR Modal verifica el estado cada 5 segundos. Puedes ajustar este intervalo en `QRModal.vue` línea 109.

4. **Timeout:** El timeout de conexión es de 30 segundos (configurable en `.env`).

5. **Logs:** Habilita/deshabilita logs con `PAGOFACIL_ENABLE_LOGS` en `.env`.

## 🐛 Troubleshooting

### "No se pudo obtener un token válido"
- Verifica que `PAGOFACIL_TOKEN_SERVICE` y `PAGOFACIL_TOKEN_SECRET` sean correctos
- Verifica que `PAGOFACIL_BASE_URL` sea correcto
- Revisa los logs en `storage/logs/laravel.log`

### "Error al obtener los datos del QR"
- Verifica que la respuesta de PagoFácil incluya `qrBase64` y `transactionId`
- Revisa los logs para ver la respuesta completa

### "Pago no encontrado en el sistema"
- Verifica que la venta exista en la BD
- Verifica que el `PedidoID` del callback coincida con el `referencia_externa` guardado

### El QR no se genera
- Verifica que estés autenticado (token válido)
- Verifica que la venta_id exista
- Revisa la consola del navegador para errores

## 📞 Soporte

Para más información sobre la API de PagoFácil, consulta la documentación en los archivos:
- `pagofacil.txt` - Documentación de la API
- `ejemplo-pago.txt` - Ejemplos de implementación

---

**Implementación completada:** 27 de Noviembre de 2025
