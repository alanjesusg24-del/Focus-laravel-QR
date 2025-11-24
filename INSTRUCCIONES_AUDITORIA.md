# INSTRUCCIONES PARA LA AUDITORÍA CETAM

**Proyecto:** Centro de Servicios (CS) - Order QR System
**Fecha:** 2025-11-24
**Estado:** ✅ LISTO PARA AUDITORÍA
**Auditor:** _[Por definir]_

---

## 📋 DOCUMENTOS A REVISAR

Los siguientes documentos contienen toda la información necesaria para la auditoría:

1. **RESUMEN_AUDITORIA_CETAM_2025.md** - Resumen ejecutivo completo
2. **CABECERAS_CETAM.md** - Plantillas de cabeceras utilizadas
3. **GUIA_ESTANDARES_CETAM.md** - Guía de estándares implementados
4. **Este documento** - Instrucciones para el auditor

---

## 🔍 PUNTOS DE VERIFICACIÓN

### 1. Cabeceras Institucionales

#### Verificar en Controladores
```bash
# Ubicación: app/Http/Controllers/
Archivos a revisar:
- Controller.php (base)
- BusinessController.php
- ChatController.php
- DashboardController.php
- MercadoPagoWebhookController.php
- OrderController.php
- PaymentController.php
- SupportTicketController.php
```

**Qué verificar:**
- Cada archivo debe tener una cabecera con el formato:
  ```php
  /**
   * ============================================
   * CETAM - [Nombre del Controlador]
   * ============================================
   *
   * @project     Centro de Servicios (CS)
   * @file        [NombreArchivo].php
   * @description [Descripción del controlador]
   * @author      CETAM Dev Team
   * @created     [Fecha]
   * @version     1.0.0
   * @copyright   CETAM © 2025
   *
   * ============================================
   */
  ```

#### Verificar en Modelos
```bash
# Ubicación: app/Models/
Archivos a revisar: (14 modelos)
- Business.php
- ChatMessage.php
- MobileDevice.php
- MobileUser.php
- Notification.php
- Order.php
- OrderItem.php
- OrderRealert.php
- OrderStatusHistory.php
- Payment.php
- Plan.php
- SuperAdmin.php
- SupportTicket.php
- User.php
```

**Qué verificar:**
- Cabecera con información de tabla y primaryKey
- Formato consistente con la plantilla

#### Verificar en Form Requests
```bash
# Ubicación: app/Http/Requests/
Archivos a revisar: (6 requests)
- CreateBusinessRequest.php
- CreateOrderRequest.php
- CreatePaymentRequest.php
- CreateSupportTicketRequest.php
- UpdateBusinessRequest.php
- UpdateOrderRequest.php
```

#### Verificar en Rutas
```bash
# Ubicación: routes/
Archivos a revisar:
- web.php
- api.php
- channels.php
- console.php
```

### 2. Configuración CETAM

#### Archivo de Configuración Principal
```bash
# Ubicación: config/cetam.cs.php
```

**Qué verificar:**
```php
✅ Código del proyecto: 'CS'
✅ Slug del proyecto: 'cs'
✅ Nombre completo del proyecto
✅ Configuración de features (chat, qr_scanner, payments, etc.)
✅ Configuración de paginación (per_page: 15)
✅ Configuración de base de datos
✅ Configuración de rutas
✅ Configuración de pagos (MercadoPago)
```

#### Verificar Uso en Controladores
```bash
# Comando para buscar uso de configuración:
grep -r "config('cetam.cs" app/Http/Controllers/
```

**Debe encontrar:**
- OrderController.php: línea 50 (paginación)
- SupportTicketController.php: línea 43 (paginación)

### 3. Sistema de Componentes

#### Componente Icon
```bash
# Archivos a revisar:
- app/View/Components/Icon.php
- resources/views/components/icon.blade.php
- config/icons.php (catálogo de iconos)
```

**Qué verificar:**
- ✅ Componente Icon creado
- ✅ Vista del componente existe
- ✅ Archivo de configuración con 135+ iconos
- ✅ Incluye iconos específicos del proyecto (qrcode, order, etc.)

#### Componente Alert
```bash
# Archivos a revisar:
- app/View/Components/CS/Alert.php
- resources/views/components/cs/alert.blade.php
```

**Qué verificar:**
- ✅ Componente Alert creado
- ✅ Soporte para 4 tipos: success, error, warning, info
- ✅ Soporte para alertas dismissibles
- ✅ Integración con componente Icon

### 4. Layouts y Vistas

#### Layouts Principales
```bash
# Ubicación: resources/views/layouts/
- base.blade.php
- business-app.blade.php
- superadmin-app.blade.php
```

**Qué verificar:**
- ✅ Cada layout tiene cabecera CETAM
- ✅ Uso de componentes del sistema
- ✅ Estructura HTML correcta

---

## 🧪 PRUEBAS DE FUNCIONALIDAD

### 1. Verificar Configuración
```bash
php artisan config:show cetam.cs
```

**Salida esperada:**
- Debe mostrar toda la configuración del proyecto CS
- Features habilitados/deshabilitados
- Configuración de paginación: 15 por página
- Configuración de base de datos
- Configuración de pagos

### 2. Verificar Rutas
```bash
php artisan route:list --path=business
```

**Debe mostrar:**
- Rutas con prefijo "business."
- Controladores correctos asignados
- Middleware apropiado

### 3. Verificar Caché
```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cachear optimizado
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Resultado esperado:**
- Todos los comandos deben ejecutarse sin errores
- Sistema debe funcionar correctamente después del caché

---

## ✅ CHECKLIST PARA EL AUDITOR

### Documentación
- [ ] CABECERAS_CETAM.md existe y contiene plantillas completas
- [ ] GUIA_ESTANDARES_CETAM.md está actualizada
- [ ] RESUMEN_AUDITORIA_CETAM_2025.md es preciso
- [ ] INSTRUCCIONES_AUDITORIA.md (este documento) está completo

### Cabeceras en Archivos PHP
- [ ] 8/8 Controladores con cabeceras correctas
- [ ] 14/14 Modelos con cabeceras correctas
- [ ] 6/6 Form Requests con cabeceras correctas
- [ ] 4/4 Archivos de rutas con cabeceras correctas

### Configuración y Componentes
- [ ] config/cetam.cs.php existe y está configurado
- [ ] config/icons.php existe con 135+ iconos
- [ ] Componente Icon funciona correctamente
- [ ] Componente Alert funciona correctamente

### Uso de Estándares
- [ ] Uso de config('cetam.cs.pagination.per_page') en controladores
- [ ] Nomenclatura correcta (PascalCase, camelCase, kebab-case)
- [ ] PHPDoc en métodos públicos
- [ ] Type hints en métodos y propiedades

### Funcionalidad
- [ ] Sistema se inicia sin errores
- [ ] Configuración CETAM se carga correctamente
- [ ] Comandos de caché funcionan
- [ ] No hay warnings ni errores en logs

---

## 📊 RESULTADOS ESPERADOS

### Cumplimiento: 100%

| Categoría | Estado | Detalles |
|-----------|--------|----------|
| Cabeceras institucionales | ✅ | 32+ archivos actualizados |
| Configuración centralizada | ✅ | config/cetam.cs.php completo |
| Sistema de componentes | ✅ | Icon y Alert implementados |
| Nomenclatura | ✅ | PSR-12 y estándares CETAM |
| Documentación | ✅ | 4 documentos completos |
| Type hints | ✅ | Todos los métodos tipados |
| PHPDoc | ✅ | Métodos públicos documentados |

---

## 📝 NOTAS PARA EL AUDITOR

1. **Todas las cabeceras** siguen el mismo formato estándar
2. **Los componentes** están listos para usar en todo el proyecto
3. **La configuración** está centralizada en config/cetam.cs.php
4. **El código** sigue PSR-12 y estándares institucionales
5. **La documentación** es completa y está actualizada

---

## 🎯 PUNTOS DE VERIFICACIÓN RÁPIDA

### Verificación en 5 minutos:

```bash
# 1. Ver configuración
php artisan config:show cetam.cs

# 2. Verificar cabeceras en un controlador
cat app/Http/Controllers/OrderController.php | head -20

# 3. Verificar cabeceras en un modelo
cat app/Models/Order.php | head -20

# 4. Verificar componente Icon
cat app/View/Components/Icon.php | head -20

# 5. Verificar rutas
php artisan route:list | grep business | head -10
```

---

## ✅ APROBACIÓN

### Criterios de Aprobación:
- ✅ Todas las cabeceras institucionales presentes
- ✅ Configuración CETAM funcionando
- ✅ Componentes del sistema implementados
- ✅ Nomenclatura y convenciones correctas
- ✅ Documentación completa
- ✅ Sistema funciona sin errores

### Firma de Aprobación:

**Auditor:** _____________________________
**Fecha:** _____________________________
**Resultado:** ⬜ APROBADO  ⬜ OBSERVACIONES

**Observaciones:**
```
[Espacio para comentarios del auditor]
```

---

## 📞 CONTACTO

**Equipo de Desarrollo:** CETAM Dev Team
**Proyecto:** Centro de Servicios (CS)
**Email:** [correo de contacto]
**Fecha de Implementación:** 2025-11-24

---

**CETAM - Centro de Desarrollo Tecnológico Aplicado de México**
**© 2025 - Todos los derechos reservados**
