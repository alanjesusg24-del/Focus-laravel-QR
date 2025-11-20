# 🌐 Guía de Configuración con ngrok

## 🎯 ¿Por qué ngrok?

### ✅ Ventajas
- **Funciona en CUALQUIER red** - No importa el WiFi, firewall o router
- **URL pública con HTTPS** - Más seguro y profesional
- **Sin configuración de red** - No necesitas abrir puertos ni configurar firewall
- **Perfecto para presentaciones** - Tus compañeros pueden conectarse desde sus datos móviles

### ❌ Desventajas (cuenta gratuita)
- La URL cambia cada vez que reinicias (ej: `https://abc123.ngrok.io`)
- Límite de conexiones simultáneas
- Puedes tener una URL fija con cuenta de pago

---

## 📥 Instalación de ngrok

### Paso 1: Descargar ngrok

1. Ve a: **https://ngrok.com/download**
2. Descarga la versión para **Windows**
3. Extrae el archivo `ngrok.exe`

### Paso 2: Agregar ngrok al sistema

**Opción A: Colocar en la carpeta del proyecto (más fácil)**
```
C:\Users\alanG\Documentos\VSC\Laravel\volt-laravel-dashboard-1.0.1-main\
   ├── ngrok.exe  ← Coloca el archivo aquí
   ├── start-with-ngrok.bat
   └── ...
```

**Opción B: Agregar al PATH de Windows**
1. Crea una carpeta: `C:\ngrok\`
2. Mueve `ngrok.exe` a esa carpeta
3. Agregar al PATH:
   - Busca "Variables de entorno" en Windows
   - En "Variables del sistema" → "Path" → "Editar"
   - "Nuevo" → `C:\ngrok\`
   - "Aceptar" en todo

### Paso 3: Verificar instalación

Abre una terminal (CMD) y escribe:
```bash
ngrok version
```

Si ves la versión, ¡está instalado correctamente! ✅

---

## 🚀 Uso Rápido

### Iniciar el servidor con ngrok

Simplemente ejecuta:
```bash
start-with-ngrok.bat
```

### ¿Qué hace el script?

1. ✅ Verifica que ngrok esté instalado
2. 🧹 Limpia caché de Laravel
3. 🔧 Inicia servidor Laravel en puerto 8000
4. 🌐 Inicia ngrok y crea un tunnel público
5. 📡 Obtiene la URL pública automáticamente
6. 💾 Actualiza `.env` con la URL de ngrok
7. 📱 Te muestra la URL para configurar la app móvil

### Salida del script

```
========================================
✅ SERVIDOR PÚBLICO ACTIVO
========================================

🌐 URL Pública: https://abc123.ngrok.io
🔗 API URL: https://abc123.ngrok.io/api

========================================
📱 CONFIGURAR APP MÓVIL
========================================

🎯 Opción 1: Escanear QR
   Abre esta URL en tu navegador:
   https://abc123.ngrok.io/mobile-config

📋 Opción 2: Copiar manualmente
   Configura esta URL en la app móvil:
   https://abc123.ngrok.io/api
```

---

## 📱 Configurar la App Móvil

### Método 1: QR (Recomendado)

1. Ejecuta `start-with-ngrok.bat`
2. Copia la URL que dice `/mobile-config`
3. Ábrela en tu navegador
4. Se mostrará un **QR gigante**
5. Escanéalo con la app móvil
6. ¡Listo! ✅

### Método 2: Manual

1. Abre la app móvil
2. Ve a **Configuración** → **URL del Servidor**
3. Pega la URL del API (ej: `https://abc123.ngrok.io/api`)
4. Guarda

---

## 🎓 Escenario en Clase

### Preparación (5 minutos antes)

1. Abre tu laptop
2. Conéctate a cualquier WiFi (o usa tus datos móviles)
3. Ejecuta `start-with-ngrok.bat`
4. Copia la URL `/mobile-config`
5. Ábrela en tu navegador

### Durante la Presentación

1. **Proyecta el QR** en la pantalla grande
2. Tus compañeros escanean con sus celulares
3. ¡Funciona instantáneamente! ✅

**No importa si:**
- Están en WiFi diferente
- Usan datos móviles
- El WiFi tiene firewall
- Están en otra ciudad

---

## 🔧 Opciones Avanzadas

### URL Permanente (Cuenta Pro)

Si vas a presentar seguido, considera una cuenta de pago:

1. Crea cuenta en: https://dashboard.ngrok.com/
2. Reserva un dominio: `tu-proyecto.ngrok.io`
3. Usa el comando:
   ```bash
   ngrok http 8000 --domain=tu-proyecto.ngrok.io
   ```

**Ventaja:** La URL NUNCA cambia, así que no necesitas reconfigurar la app móvil.

### Ver Estadísticas y Requests

Mientras ngrok está activo, abre:
```
http://localhost:4040
```

Verás:
- 📊 Todas las peticiones HTTP en tiempo real
- 🔍 Headers, body, response de cada petición
- ⏱️ Tiempo de respuesta
- 🔄 Opción de replay requests

**¡Súper útil para debugging!**

---

## 🛑 Detener el Servidor

### Opción 1: Cerrar ventanas
- Cierra la ventana del script
- Cierra la ventana de ngrok

### Opción 2: Script de limpieza
Ejecuta el archivo que se crea automáticamente:
```bash
kill-ngrok.bat
```

---

## ⚠️ Problemas Comunes

### Error: "ngrok no está instalado"

**Solución:**
1. Verifica que `ngrok.exe` esté en la carpeta del proyecto
2. O verifica que esté en el PATH
3. Abre nueva terminal después de agregar al PATH

### Error: "No se pudo obtener la URL automáticamente"

**Solución:**
1. El script te pedirá la URL manualmente
2. Abre http://localhost:4040 en tu navegador
3. Copia la URL que aparece (ej: `https://abc123.ngrok.io`)
4. Pégala en la terminal cuando te lo pida

### Error: Puerto 8000 ya está en uso

**Solución:**
```bash
# Ver qué proceso usa el puerto
netstat -ano | findstr :8000

# Matar ese proceso (reemplaza PID con el número que viste)
taskkill /F /PID <PID>
```

### ngrok muestra "ERR_NGROK_108"

**Solución:**
- Esto significa que llegaste al límite de conexiones gratuitas
- Espera un momento y reinicia
- O crea una cuenta en ngrok.com (gratis) para más límite

### La app móvil no se conecta

**Verificar:**
1. ✅ ¿La URL de ngrok está correcta en la app?
2. ✅ ¿El servidor Laravel sigue corriendo?
3. ✅ ¿El celular tiene internet?
4. ✅ ¿ngrok sigue activo? (verifica la ventana)

---

## 🔐 Seguridad

### ⚠️ Importante

Con ngrok, **tu servidor es público en internet**. Cualquiera con la URL puede acceder.

**Recomendaciones:**

1. ✅ Solo úsalo para desarrollo y presentaciones
2. ✅ Cierra ngrok cuando termines
3. ✅ No expongas información sensible
4. ❌ NO lo uses en producción sin autenticación
5. ✅ En la cuenta de ngrok puedes configurar:
   - Autenticación básica
   - Restricción de IPs
   - Verificación OAuth

### Agregar autenticación básica

En el dashboard de ngrok, puedes configurar:
```bash
ngrok http 8000 --basic-auth="usuario:contraseña"
```

---

## 📊 Comparación: Local vs ngrok

| Característica | start-server.bat | start-with-ngrok.bat |
|---|---|---|
| Velocidad | ⚡⚡⚡ Muy rápida | ⚡⚡ Rápida |
| Funciona con firewall | ❌ No | ✅ Sí |
| Requiere misma WiFi | ✅ Sí | ❌ No |
| URL fija | ✅ Sí (tu IP) | ❌ No (gratis) |
| HTTPS | ❌ No | ✅ Sí |
| Acceso desde internet | ❌ No | ✅ Sí |
| Debugging tools | ❌ No | ✅ Sí (dashboard) |
| Límite de conexiones | ♾️ Ilimitado | 40/min (gratis) |
| Mejor para | Desarrollo local | Presentaciones |

---

## 💡 Tips Pro

### 1. Guarda la configuración de ngrok

Crea un archivo `ngrok.yml` en `C:\Users\TU_USUARIO\.ngrok2\ngrok.yml`:

```yaml
authtoken: TU_TOKEN_AQUI
tunnels:
  orderqr:
    proto: http
    addr: 8000
    inspect: true
```

Luego inicia con:
```bash
ngrok start orderqr
```

### 2. Inspeccionar requests en tiempo real

Abre `http://localhost:4040` mientras ngrok está activo para ver todas las peticiones HTTP.

### 3. Compartir temporalmente

Si solo necesitas mostrar algo rápido:
```bash
ngrok http 8000 --region=us
```

### 4. Testing con celular sin app

Puedes acceder al panel web desde el celular:
```
https://abc123.ngrok.io/business/login
```

---

## 🎬 Demo Rápida

1. Ejecuta `start-with-ngrok.bat`
2. Espera 10 segundos
3. Verás algo como:
   ```
   ✅ SERVIDOR PÚBLICO ACTIVO
   🌐 URL Pública: https://abc123.ngrok.io
   ```
4. Abre `https://abc123.ngrok.io/mobile-config` en tu celular
5. ¡Listo! Estás conectado desde internet 🌍

---

## 📞 Recursos

- 📚 Documentación oficial: https://ngrok.com/docs
- 🎓 Dashboard: https://dashboard.ngrok.com/
- 💬 Soporte: https://ngrok.com/docs/support
- 📖 Blog con tutoriales: https://ngrok.com/blog

---

## ✅ Checklist para Presentación

**Un día antes:**
- [ ] Verificar que ngrok está instalado
- [ ] Probar `start-with-ngrok.bat` al menos una vez
- [ ] Verificar que la app móvil funciona
- [ ] Tener el script listo en el escritorio

**Antes de presentar (15 minutos):**
- [ ] Conectarte a internet (WiFi o datos móviles)
- [ ] Ejecutar `start-with-ngrok.bat`
- [ ] Abrir `/mobile-config` en el navegador
- [ ] Verificar que el QR se muestra correctamente
- [ ] Hacer una prueba con tu celular

**Durante la presentación:**
- [ ] Proyectar el QR en pantalla
- [ ] Explicar que funciona desde cualquier red
- [ ] Demostrar en vivo

**Después de presentar:**
- [ ] Cerrar ngrok
- [ ] Cerrar el servidor Laravel

---

Made with ❤️ for CETAM - Order QR System

🌐 **ngrok** - Making localhost accessible to anyone, anywhere
