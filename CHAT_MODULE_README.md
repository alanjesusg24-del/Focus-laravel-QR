# Módulo de Chat - Order QR System

## Descripción General

El módulo de chat es una funcionalidad **premium opcional** que permite a los negocios interactuar en tiempo real con sus clientes a través de un widget de chat integrado en el dashboard.

## Estado Actual: UI Implementada ✅

### Funcionalidades Implementadas

1. **Widget de Chat Flotante**
   - Botón circular flotante en la esquina inferior derecha
   - Indicador de mensajes no leídos
   - Ventana de chat responsive (oculta/mostrada con click)
   - Diseño moderno con colores institucionales CETAM

2. **Interfaz de Chat**
   - Área de mensajes con scroll
   - Formulario de envío de mensajes
   - Indicador de "escribiendo..." (typing indicator)
   - Mensajes del usuario (azul, alineados a la derecha)
   - Mensajes del bot/soporte (blanco, alineados a la izquierda)
   - Timestamps en cada mensaje

3. **Lógica Frontend**
   - Envío de mensajes con Enter
   - Simulación de respuestas del bot
   - Animaciones suaves de entrada/salida
   - Auto-scroll al último mensaje

4. **Visibilidad Condicional**
   - Solo se muestra si `$business->has_chat_module === true`
   - Banner informativo en el dashboard cuando está activado
   - Indicador de precio en el perfil del negocio

## Configuración de Precios

- **Precio base del sistema:** $299 MXN/mes
- **Módulo de chat:** +$150 MXN/mes
- Total con chat: **$449 MXN/mes** (más costo de retención de datos)

## Archivos Clave

```
resources/views/components/chat-widget.blade.php  # Componente principal del chat
resources/views/layouts/order-qr.blade.php         # Incluye el widget en el layout
resources/views/dashboard/index.blade.php          # Banner informativo
resources/views/business/register.blade.php        # Checkbox de activación
app/Http/Controllers/BusinessController.php        # Lógica de registro con módulos
```

## Base de Datos

### Tabla: `businesses`
```sql
has_chat_module TINYINT(1) DEFAULT 0  # 1 = activado, 0 = desactivado
monthly_price DECIMAL(10,2)            # Precio calculado automáticamente
```

## Activación del Módulo

### Durante el Registro
El negocio puede activar el módulo de chat marcando el checkbox en el formulario de registro:
- Checkbox: "Módulo de Chat"
- Descripción: "Incluye chat en tiempo real con clientes."
- Precio: "+$150.00 MXN/mes"

### Cálculo Automático de Precio
```php
$monthlyPrice = $plan->base_price; // $299

if ($request->has('has_chat_module')) {
    $monthlyPrice += $plan->chat_module_price; // +$150
}
```

## Integración de API (Pendiente) 🚧

El widget está **preparado para integración** con las siguientes opciones:

### Opción 1: Pusher (Recomendado)
```javascript
// Configuración de Pusher
import Pusher from 'pusher-js';

const pusher = new Pusher('YOUR_PUSHER_KEY', {
    cluster: 'YOUR_CLUSTER',
    encrypted: true
});

const channel = pusher.subscribe(`business-${businessId}`);

channel.bind('new-message', function(data) {
    addMessageToChat(data.message, 'bot');
});
```

### Opción 2: Laravel WebSockets
```bash
composer require beyondcode/laravel-websockets
php artisan websockets:serve
```

### Opción 3: Socket.io con Node.js
```javascript
const socket = io('http://localhost:3000');

socket.on('message', (data) => {
    addMessageToChat(data.message, 'bot');
});

socket.emit('send-message', { message: userMessage });
```

### Opción 4: API REST con Polling
```javascript
// Polling cada 3 segundos
setInterval(async () => {
    const response = await fetch('/api/chat/messages');
    const messages = await response.json();
    updateChatMessages(messages);
}, 3000);
```

## Puntos de Integración

### Ubicación del código de integración
Archivo: `resources/views/components/chat-widget.blade.php`

Buscar el comentario:
```javascript
// TODO: WebSocket/API Integration placeholder
```

### Funciones a conectar

1. **sendMessage(event)**
   - Actualmente: Simula envío
   - Necesita: Enviar mensaje a API/WebSocket

2. **addMessageToChat(message, sender)**
   - Ya implementada
   - Recibe mensajes del backend

3. **Eventos a escuchar**
   - `new-message`: Nuevo mensaje entrante
   - `typing`: Usuario está escribiendo
   - `read`: Mensaje leído
   - `online/offline`: Estado de conexión

## Próximos Pasos para Integración

1. **Elegir proveedor de chat** (Pusher, Laravel WebSockets, custom)
2. **Crear tabla `chat_messages`**
   ```sql
   CREATE TABLE chat_messages (
       message_id BIGINT PRIMARY KEY AUTO_INCREMENT,
       business_id BIGINT,
       sender_type ENUM('business', 'customer', 'support'),
       message TEXT,
       is_read BOOLEAN DEFAULT 0,
       created_at TIMESTAMP,
       FOREIGN KEY (business_id) REFERENCES businesses(business_id)
   );
   ```
3. **Crear API endpoints**
   - `POST /api/chat/messages` - Enviar mensaje
   - `GET /api/chat/messages` - Obtener mensajes
   - `POST /api/chat/mark-read` - Marcar como leído
4. **Implementar lógica de backend**
   - Validación de permisos
   - Almacenamiento de mensajes
   - Broadcasting de eventos
5. **Conectar frontend con backend**
6. **Testing de tiempo real**

## Ejemplo de Uso

```blade
{{-- El widget se incluye automáticamente en el layout --}}
@auth
    <x-chat-widget :business="auth()->user()" />
@endauth
```

## Pruebas

### Probar con chat activado
1. Registrar un negocio nuevo
2. Marcar "Módulo de Chat"
3. Completar registro e iniciar sesión
4. Verificar botón flotante en esquina inferior derecha
5. Click en botón para abrir chat
6. Enviar mensaje de prueba

### Probar sin chat
1. Iniciar sesión con negocio sin chat activado
2. Verificar que NO aparece el botón flotante
3. Verificar que NO aparece el banner en dashboard

## Mantenimiento

- El módulo usa Tailwind CSS (ya incluido)
- No requiere dependencias adicionales de npm
- Compatible con Alpine.js (ya incluido en el layout)

## Notas Importantes

⚠️ **Modo Desarrollo:** Actualmente el chat simula respuestas. NO envía/recibe mensajes reales.

✅ **Producción Ready:** La UI está lista. Solo falta integrar backend.

🔒 **Seguridad:** Validar permisos en API antes de enviar/recibir mensajes.

## Soporte

Para preguntas sobre la integración del módulo de chat, contactar al equipo de desarrollo de CETAM.
