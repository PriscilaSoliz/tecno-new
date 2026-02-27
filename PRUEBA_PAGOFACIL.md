# 🧪 Guía de Prueba - PagoFácil QR

## ✅ Checklist Pre-Prueba

- [ ] Servidor Laravel corriendo (`php artisan serve`)
- [ ] Servidor Vite corriendo (`npm run dev`)
- [ ] Base de datos migrada (`php artisan migrate`)
- [ ] `.env` configurado con credenciales de PagoFácil
- [ ] Ngrok activo y URL actualizada en `.env`

## 🔍 Prueba 1: Flujo Completo de Compra con QR

### Paso 1: Acceder al Catálogo
1. Abre `http://localhost:5173/catalogo`
2. Deberías ver los productos disponibles

### Paso 2: Agregar Productos al Carrito
1. Haz clic en "Agregar" en uno o más productos
2. Verifica que aparezcan en el carrito

### Paso 3: Ir a Proceder al Pago
1. Haz clic en "Proceder al Pago"
2. Deberías ir a `http://localhost:5173/catalogo/venta`

### Paso 4: Seleccionar Método de Pago QR
1. En la sección "Método de Pago", selecciona "Código QR"
2. Verifica que se seleccione correctamente

### Paso 5: Confirmar Pedido
1. Haz clic en "Confirmar Pedido"
2. Deberías ver un modal con el código QR

### Paso 6: Verificar QR Modal
El modal debe mostrar:
- ✓ Monto a pagar
- ✓ Código QR (imagen)
- ✓ Número de referencia (copiable)
- ✓ Instrucciones
- ✓ Botones: Descargar QR, Cerrar

## 🔍 Prueba 2: Verificar Base de Datos

### Verificar que se creó el pago
```sql
SELECT * FROM pagos WHERE venta_id = 1;
```

Deberías ver:
- `referencia_externa`: `venta-1-1234567890`
- `transaction_id`: ID de PagoFácil
- `estado`: `pendiente`
- `datos_pago`: JSON con datos de la transacción

## 🔍 Prueba 3: Verificar Logs

```bash
tail -f storage/logs/laravel.log
```

Deberías ver logs como:
```
[2025-11-27 14:30:00] local.INFO: Inicio del método generarQR
[2025-11-27 14:30:01] local.INFO: Token obtenido de Pago Fácil
[2025-11-27 14:30:02] local.INFO: QR generado correctamente
```

## 🔍 Prueba 4: Simular Callback (Opcional)

Si quieres simular un pago completado sin escanear el QR real:

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

Respuesta esperada:
```json
{
    "error": 0,
    "status": 1,
    "message": "Pago procesado correctamente",
    "values": true
}
```

Luego verifica en la BD:
```sql
SELECT * FROM pagos WHERE referencia_externa = 'venta-1-1234567890';
```

El estado debe ser `completado`.

## 🔍 Prueba 5: Verificar Consulta de Estado

```bash
curl -X POST http://localhost:8000/pagofacil/consultar-estado \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"transaction_id": "12345"}'
```

## ⚠️ Errores Comunes

### Error: "No se pudo obtener un token válido"
**Causa:** Credenciales de PagoFácil incorrectas
**Solución:** Verifica `PAGOFACIL_TOKEN_SERVICE` y `PAGOFACIL_TOKEN_SECRET` en `.env`

### Error: "Venta no encontrada"
**Causa:** La venta_id no existe
**Solución:** Verifica que la venta se haya creado correctamente en la BD

### Error: "Error al conectar con el servidor"
**Causa:** Servidor Laravel no está corriendo o hay error en la ruta
**Solución:** Verifica que `php artisan serve` esté corriendo y revisa los logs

### El QR no aparece
**Causa:** La respuesta de PagoFácil no incluye `qrBase64`
**Solución:** Revisa los logs en `storage/logs/laravel.log`

### El modal no cierra después del pago
**Causa:** El callback no se recibió correctamente
**Solución:** Verifica que `PAGOFACIL_CALLBACK_URL` sea correcto y accesible desde PagoFácil

## 📱 Prueba Real con Billetera Digital

1. Genera el QR en el modal
2. Abre tu billetera digital (Tigo Money, etc.)
3. Escanea el código QR
4. Completa el pago
5. El modal debería detectar el pago automáticamente
6. Serás redirigido a "Mis Pedidos"

## 📊 Monitoreo

### Ver todos los pagos
```sql
SELECT id, venta_id, referencia_externa, transaction_id, estado, monto, fecha FROM pagos ORDER BY id DESC;
```

### Ver pagos pendientes
```sql
SELECT * FROM pagos WHERE estado = 'pendiente';
```

### Ver pagos completados
```sql
SELECT * FROM pagos WHERE estado = 'completado';
```

### Ver pagos rechazados
```sql
SELECT * FROM pagos WHERE estado = 'rechazado';
```

## 🎯 Puntos Clave a Verificar

- [ ] El QR se genera correctamente
- [ ] El número de referencia es único
- [ ] El estado inicial es "pendiente"
- [ ] El callback actualiza el estado a "completado"
- [ ] El pedido se marca como "COMPLETADO"
- [ ] Los logs registran todos los eventos
- [ ] La respuesta JSON es correcta
- [ ] El modal se cierra después del pago
- [ ] El usuario es redirigido a "Mis Pedidos"

---

**Última actualización:** 27 de Noviembre de 2025
