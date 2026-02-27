# 🚀 Inicio Rápido - PagoFácil QR

## ✅ Verificación Pre-Inicio

```bash
# 1. Verifica que Laravel esté corriendo
php artisan serve
# Deberías ver: "Server running on [http://127.0.0.1:8000]"

# 2. Verifica que Vite esté corriendo (en otra terminal)
npm run dev
# Deberías ver: "Local: http://localhost:5173"

# 3. Verifica que la migración se ejecutó
php artisan migrate --step
# Deberías ver: "2025_11_27_000000_update_pagos_table_for_pagofacil"
```

## 🎯 Flujo Rápido de Prueba

### 1. Abre el navegador
```
http://localhost:5173/catalogo
```

### 2. Agrega productos al carrito
- Haz clic en "Agregar" en 1-2 productos

### 3. Procede al pago
- Haz clic en "Proceder al Pago"

### 4. Selecciona QR como método de pago
- En "Método de Pago", selecciona "Código QR"

### 5. Confirma el pedido
- Haz clic en "Confirmar Pedido"

### 6. Verifica el QR Modal
Deberías ver:
- ✅ Código QR (imagen)
- ✅ Número de referencia
- ✅ Monto a pagar
- ✅ Instrucciones
- ✅ Botones: Descargar QR, Cerrar

## 📊 Verificar en Base de Datos

```bash
# Abre MySQL/MariaDB
mysql -u root -p

# Selecciona la BD
use tw_p2_2_2025;

# Ver todos los pagos
SELECT id, venta_id, referencia_externa, transaction_id, estado, monto FROM pagos ORDER BY id DESC;

# Ver pagos pendientes
SELECT * FROM pagos WHERE estado = 'pendiente';

# Ver pagos completados
SELECT * FROM pagos WHERE estado = 'completado';
```

## 📝 Ver Logs

```bash
# Terminal 1: Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Terminal 2: Ejecutar las acciones en el navegador
# Los logs mostrarán:
# - Generación de QR
# - Consultas de estado
# - Callbacks recibidos
```

## 🧪 Simular Pago Completado (Opcional)

Si no quieres escanear un QR real, puedes simular un callback:

```bash
# Obtén el número de referencia de la BD
# Ejemplo: venta-1-1234567890

curl -X POST http://localhost:8000/pagofacil/callback \
  -H "Content-Type: application/json" \
  -d '{
    "PedidoID": "venta-1-1234567890",
    "Fecha": "2025-11-27",
    "Hora": "14:30:00",
    "MetodoPago": "QR",
    "Estado": "2"
  }'

# Respuesta esperada:
# {"error":0,"status":1,"message":"Pago procesado correctamente","values":true}
```

Luego verifica en la BD que el estado cambió a "completado".

## 🔍 Checklist de Funcionalidad

- [ ] QR se genera correctamente
- [ ] Número de referencia es único
- [ ] Estado inicial es "pendiente"
- [ ] Callback actualiza estado a "completado"
- [ ] Pedido se marca como "COMPLETADO"
- [ ] Modal se cierra después del pago
- [ ] Usuario es redirigido a "Mis Pedidos"
- [ ] Logs registran todos los eventos

## ⚠️ Problemas Comunes

### "Error: No se pudo obtener un token válido"
```bash
# Verifica las credenciales en .env
grep PAGOFACIL .env

# Deberían ser:
PAGOFACIL_TOKEN_SERVICE=51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba
PAGOFACIL_TOKEN_SECRET=0C351C6679844041AA31AF9C
```

### "Error al conectar con el servidor"
```bash
# Verifica que Laravel esté corriendo
ps aux | grep "php artisan serve"

# Si no está, inicia:
php artisan serve
```

### "Venta no encontrada"
```bash
# Verifica que la venta se creó en la BD
SELECT * FROM ventas ORDER BY id DESC LIMIT 1;

# Deberías ver la venta que acabas de crear
```

### El QR no aparece en el modal
```bash
# Revisa los logs
tail -f storage/logs/laravel.log

# Busca errores como:
# "Error en generarQR"
# "No se encontraron qrBase64 o transactionId"
```

## 📱 Prueba Real con Billetera

1. Genera el QR en el modal
2. Abre Tigo Money (o tu billetera digital)
3. Escanea el código QR
4. Completa el pago
5. El modal debería detectarlo automáticamente
6. Serás redirigido a "Mis Pedidos"

## 🎯 Próximos Pasos

1. **Personalizar el QR Modal:**
   - Edita `resources/js/Pages/PagoFacil/QRModal.vue`
   - Cambia colores, textos, instrucciones

2. **Ajustar el polling:**
   - En `QRModal.vue` línea 109
   - Cambia `5000` (5 segundos) a otro valor

3. **Agregar más métodos de pago:**
   - Modifica `MetodoPago.vue` para agregar nuevas opciones
   - Crea nuevos controladores según sea necesario

4. **Integrar con tu sistema de notificaciones:**
   - Envía emails cuando se complete un pago
   - Notifica al admin
   - Actualiza el inventario

## 📚 Documentación Completa

Para más detalles, consulta:
- `IMPLEMENTACION_PAGOFACIL.md` - Guía completa
- `PRUEBA_PAGOFACIL.md` - Guía de prueba detallada
- `RESUMEN_CAMBIOS_PAGOFACIL.txt` - Resumen de cambios

## 🆘 Soporte

Si encuentras problemas:

1. **Revisa los logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verifica la BD:**
   ```bash
   SELECT * FROM pagos ORDER BY id DESC LIMIT 5;
   ```

3. **Consulta la documentación:**
   - `pagofacil.txt` - API de PagoFácil
   - `ejemplo-pago.txt` - Ejemplos de implementación

4. **Contacta al equipo de desarrollo**

---

**¡Listo para empezar!** 🎉

Sigue estos pasos y tendrás PagoFácil QR funcionando en minutos.
