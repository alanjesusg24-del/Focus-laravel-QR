# 🚀 Inicio Rápido - Order QR System

## 📱 Para Presentar en Clase (RECOMENDADO)

### Paso 1: Instalar ngrok (solo una vez)

1. Ve a: https://ngrok.com/download
2. Descarga para Windows
3. Extrae `ngrok.exe` en esta carpeta del proyecto

### Paso 2: Iniciar el servidor

```bash
start-with-ngrok.bat
```

### Paso 3: Configurar app móvil

1. El script te dará una URL como: `https://abc123.ngrok.io/mobile-config`
2. Ábrela en tu navegador
3. Proyecta el QR en pantalla
4. Tus compañeros escanean
5. ¡Listo! ✅

---

## 🏠 Para Desarrollo Local (en casa)

### Iniciar servidor local

```bash
start-server.bat
```

### Configurar app móvil

1. Abre `http://TU_IP:8000/mobile-config` en el navegador
2. Escanea el QR con tu celular
3. **Importante:** Tu celular y laptop deben estar en la misma WiFi

---

## 🤔 ¿Cuál usar?

| Situación | Script | ¿Por qué? |
|---|---|---|
| Presentar en clase | `start-with-ngrok.bat` | Funciona con cualquier red y firewall |
| Desarrollo en casa | `start-server.bat` | Más rápido, no requiere internet |
| Testing con amigos | `start-with-ngrok.bat` | Ellos pueden conectarse desde sus datos |
| Demo a profesor | `start-with-ngrok.bat` | Más profesional (HTTPS) |

---

## 📚 Más Información

- **Guía completa de ngrok:** Ver `NGROK_SETUP.md`
- **Configuración de red:** Ver `NETWORK_SETUP.md`
- **Credenciales de prueba:** Ver abajo ⬇️

---

## 🔐 Credenciales de Prueba

### Login: `/business/login`

**Negocio:**
- Email: `test@example.com`
- Contraseña: `password123`

**SuperAdmin:**
- Email: `admin@example.com`
- Contraseña: `password`

---

## ⚡ Comandos Útiles

```bash
# Iniciar con ngrok (presentaciones)
start-with-ngrok.bat

# Iniciar local (desarrollo)
start-server.bat

# Detener ngrok
kill-ngrok.bat

# Ver logs de Laravel
tail -f storage/logs/laravel.log
```

---

## 🆘 Problemas?

1. Ver `NGROK_SETUP.md` para troubleshooting
2. Revisar `storage/logs/laravel.log`
3. Verificar que ngrok esté instalado: `ngrok version`

---

¡Buena suerte en tu presentación! 🎓✨
