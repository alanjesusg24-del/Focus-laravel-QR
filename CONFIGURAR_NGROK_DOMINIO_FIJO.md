# 🌐 Configurar ngrok con Dominio Fijo (Gratis)

## 🎯 Objetivo

Obtener una URL fija de ngrok que NO cambie cada vez que reinicias el servidor.

**Ejemplo:**
- ❌ Antes: `https://abc123.ngrok.io` (cambia cada vez)
- ✅ Después: `https://tu-proyecto.ngrok-free.app` (siempre igual)

---

## 📋 Pasos Completos

### Paso 1: Crear Cuenta en ngrok (Gratis)

1. Ve a: **https://dashboard.ngrok.com/signup**
2. Regístrate con:
   - Email
   - GitHub
   - Google
3. Verifica tu email

⏱️ **Tiempo:** 2 minutos

---

### Paso 2: Obtener tu Authtoken

1. Una vez dentro del dashboard, ve a:
   **https://dashboard.ngrok.com/get-started/your-authtoken**

2. Verás algo como:
   ```
   Your Authtoken
   2h3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d
   ```

3. Copia ese token (da clic en el botón "Copy")

⏱️ **Tiempo:** 30 segundos

---

### Paso 3: Configurar ngrok en tu computadora

Abre una terminal (CMD o PowerShell) en la carpeta de tu proyecto Laravel y ejecuta:

```bash
ngrok config add-authtoken TU_TOKEN_AQUI
```

**Ejemplo:**
```bash
ngrok config add-authtoken 2h3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d
```

**Resultado esperado:**
```
Authtoken saved to configuration file: C:\Users\TU_USUARIO\.ngrok2\ngrok.yml
```

⏱️ **Tiempo:** 1 minuto

---

### Paso 4: Obtener un Dominio Gratuito

1. Ve a: **https://dashboard.ngrok.com/cloud-edge/domains**

2. Da clic en **"+ New Domain"** o **"Create Domain"**

3. ngrok te dará un dominio aleatorio GRATIS como:
   ```
   charming-clearly-parrot.ngrok-free.app
   ```

4. **O puedes elegir uno personalizado** (si está disponible):
   - Escribe el nombre que quieras (ej: `order-qr-cetam`)
   - ngrok verificará si está disponible
   - Si está disponible, lo reserva para ti
   - Resultado: `order-qr-cetam.ngrok-free.app`

5. **Copia ese dominio completo** (lo vas a necesitar)

⏱️ **Tiempo:** 2 minutos

---

### Paso 5: Iniciar ngrok con tu Dominio Fijo

En lugar de usar `start-with-ngrok.bat`, ahora usa este comando:

```bash
ngrok http 8000 --domain=TU-DOMINIO.ngrok-free.app
```

**Ejemplo:**
```bash
ngrok http 8000 --domain=order-qr-cetam.ngrok-free.app
```

**Resultado:**
```
Session Status                online
Account                       tu-email@gmail.com (Plan: Free)
Version                       3.33.0
Region                        United States (us)
Web Interface                 http://127.0.0.1:4040
Forwarding                    https://order-qr-cetam.ngrok-free.app -> http://localhost:8000

Connections                   ttl     opn     rt1     rt5     p50     p90
                              0       0       0.00    0.00    0.00    0.00
```

✅ **Ahora tu URL es:** `https://order-qr-cetam.ngrok-free.app`

⏱️ **Tiempo:** 30 segundos

---

### Paso 6: Actualizar Laravel

El `.env` se actualizará automáticamente si usas el script, pero puedes verificar:

```env
APP_URL=https://order-qr-cetam.ngrok-free.app
```

Luego:
```bash
php artisan config:clear
php artisan cache:clear
```

⏱️ **Tiempo:** 30 segundos

---

### Paso 7: Actualizar App Flutter

En tu proyecto Flutter, cambia la URL del API:

**Archivo:** `lib/services/api_service.dart` (o donde esté tu configuración)

```dart
// ANTES
static const String baseUrl = "http://192.168.1.100:8000/api/v1";

// DESPUÉS
static const String baseUrl = "https://order-qr-cetam.ngrok-free.app/api/v1";
```

Luego:
```bash
flutter clean
flutter pub get
flutter run
```

⏱️ **Tiempo:** 2 minutos

---

## 🚀 Usar en el Futuro

### Cada vez que vayas a presentar:

1. Abre terminal en la carpeta del proyecto Laravel

2. Inicia el servidor Laravel:
   ```bash
   php artisan serve
   ```

3. **En otra terminal**, inicia ngrok con TU dominio:
   ```bash
   ngrok http 8000 --domain=order-qr-cetam.ngrok-free.app
   ```

4. ¡Listo! Tu app móvil ya está configurada con esa URL, no necesitas cambiar nada más ✅

---

## 🎓 Ventajas de esta Configuración

✅ **URL siempre igual** - No cambia nunca
✅ **Gratis para siempre** - No caduca
✅ **No modificas la app** - La URL está hardcodeada
✅ **Funciona en cualquier red** - WiFi, 4G, 5G, lo que sea
✅ **Sin problemas de firewall** - ngrok maneja todo
✅ **HTTPS incluido** - Más seguro

---

## ⚡ Script Automatizado (Opcional)

Crea un archivo `start-with-my-domain.bat` en tu proyecto Laravel:

```batch
@echo off
echo ========================================
echo 🚀 Iniciando Order QR System
echo ========================================
echo.

echo [1/2] Iniciando servidor Laravel...
start /B php artisan serve
timeout /t 3 /nobreak >nul
echo ✅ Laravel corriendo en http://localhost:8000
echo.

echo [2/2] Iniciando ngrok con dominio fijo...
echo.
echo 🌐 URL Pública: https://order-qr-cetam.ngrok-free.app
echo 📱 API URL: https://order-qr-cetam.ngrok-free.app/api/v1
echo.

ngrok http 8000 --domain=order-qr-cetam.ngrok-free.app
```

Luego solo ejecuta:
```bash
start-with-my-domain.bat
```

---

## 📊 Comparación: Gratis vs Pro

| Característica | Plan Gratis | Plan Pro ($8/mes) |
|---|---|---|
| Dominio fijo | ✅ .ngrok-free.app | ✅ .ngrok.app o custom |
| HTTPS | ✅ Sí | ✅ Sí |
| Túneles simultáneos | 1 | 3+ |
| Sin advertencia al abrir | ❌ Muestra aviso | ✅ Sin aviso |
| Requests por minuto | 60 | Ilimitado |

**Para desarrollo/presentaciones:** El plan gratis es suficiente ✅

---

## 🚨 Solución de Problemas

### Error: "Domain not found"
**Causa:** El dominio no está registrado o mal escrito
**Solución:**
1. Verifica en https://dashboard.ngrok.com/cloud-edge/domains
2. Copia exactamente el dominio que aparece ahí

### Error: "Authtoken not configured"
**Causa:** No se configuró el authtoken
**Solución:**
```bash
ngrok config add-authtoken TU_TOKEN
```

### Error: "Account limit reached"
**Causa:** Solo puedes tener 1 túnel gratis activo a la vez
**Solución:** Cierra cualquier otra instancia de ngrok

### La app móvil muestra "Advertencia de seguridad"
**Causa:** Los dominios `.ngrok-free.app` muestran un aviso la primera vez
**Solución:**
- Es normal con plan gratis
- El usuario debe dar clic en "Visit Site"
- Luego ya no vuelve a aparecer
- Para eliminarlo: Upgrade a plan Pro

---

## ✅ Checklist Final

- [ ] Cuenta de ngrok creada
- [ ] Authtoken configurado (`ngrok config add-authtoken ...`)
- [ ] Dominio reservado (ej: `order-qr-cetam.ngrok-free.app`)
- [ ] Probado ngrok con el dominio
- [ ] URL actualizada en app Flutter
- [ ] App Flutter probada y funcionando

---

## 🎯 Resumen Ultra Rápido

```bash
# 1. Crear cuenta en:
https://dashboard.ngrok.com/signup

# 2. Configurar authtoken:
ngrok config add-authtoken TU_TOKEN_AQUI

# 3. Reservar dominio en:
https://dashboard.ngrok.com/cloud-edge/domains

# 4. Iniciar ngrok:
ngrok http 8000 --domain=TU-DOMINIO.ngrok-free.app

# 5. En Flutter, cambiar a:
https://TU-DOMINIO.ngrok-free.app/api/v1
```

**Total:** 5-10 minutos ⚡

---

¡Listo! Ahora tienes una URL permanente que funciona en cualquier red 🚀
