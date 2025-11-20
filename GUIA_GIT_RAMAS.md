# 🌿 Guía de Git - Trabajar con Ramas (Branches)

## 🎯 ¿Qué es una Rama?

Una rama en Git es como una **línea de tiempo paralela** de tu proyecto. Te permite:

- ✅ Hacer cambios sin afectar el código principal
- ✅ Experimentar con nuevas funcionalidades
- ✅ Trabajar en varias cosas a la vez
- ✅ Revertir cambios fácilmente si algo sale mal

---

## 📊 Flujo Típico con Ramas

```
main (rama principal)
    │
    ├─── feature/nueva-funcion (rama de desarrollo)
    │    │
    │    ├─ cambio 1
    │    ├─ cambio 2
    │    └─ cambio 3
    │
    └─── (merge) ← fusionar cuando todo funcione
```

---

## 🚀 Comandos Básicos de Ramas

### 1. Ver Ramas Existentes

```bash
# Ver todas las ramas locales
git branch

# Ver todas las ramas (locales y remotas)
git branch -a

# Ver rama actual
git branch --show-current
```

---

### 2. Crear una Nueva Rama

```bash
# Crear rama y quedarte en la actual
git branch nombre-rama

# Crear rama y cambiar a ella (RECOMENDADO)
git checkout -b nombre-rama

# Sintaxis moderna (Git 2.23+)
git switch -c nombre-rama
```

**Ejemplos:**
```bash
# Para desarrollo
git checkout -b development

# Para una nueva funcionalidad
git checkout -b feature/sistema-pagos

# Para corregir un bug
git checkout -b fix/error-login

# Para tu trabajo personal
git checkout -b alan/experimentos
```

---

### 3. Cambiar entre Ramas

```bash
# Volver a la rama principal
git checkout main

# Cambiar a otra rama
git checkout nombre-rama

# Sintaxis moderna
git switch main
git switch nombre-rama
```

---

### 4. Hacer Commits en tu Rama

```bash
# Hacer cambios en tus archivos...

# Ver qué cambió
git status

# Agregar cambios
git add .

# Hacer commit
git commit -m "Descripción de los cambios"
```

---

### 5. Subir Rama a GitHub

```bash
# Primera vez que subes la rama
git push -u origin nombre-rama

# Siguientes veces
git push
```

---

### 6. Fusionar Rama con Main (Merge)

Cuando todo funcione bien:

```bash
# 1. Cambiar a main
git checkout main

# 2. Fusionar tu rama
git merge nombre-rama

# 3. Subir a GitHub
git push
```

---

### 7. Eliminar una Rama

```bash
# Eliminar rama local (después de fusionar)
git branch -d nombre-rama

# Eliminar rama remota (en GitHub)
git push origin --delete nombre-rama

# Forzar eliminación (si no has fusionado)
git branch -D nombre-rama
```

---

## 🎓 Flujo Recomendado para Ti

### Setup Inicial (Solo UNA vez)

```bash
# 1. Asegúrate de estar en la rama principal
git branch --show-current

# Si no estás en main, cámbiala
git checkout -b main

# 2. Subir a GitHub (primera vez)
git remote add origin https://github.com/TU_USUARIO/order-qr-system.git
git push -u origin main
```

---

### Workflow Diario

#### Opción A: Rama de Desarrollo

```bash
# 1. Crear rama de desarrollo (primera vez)
git checkout -b development

# 2. Hacer cambios en tus archivos...

# 3. Guardar cambios
git add .
git commit -m "Descripción de cambios"

# 4. Subir a GitHub
git push -u origin development

# 5. Cuando todo funcione, fusionar con main
git checkout main
git merge development
git push
```

#### Opción B: Ramas por Funcionalidad

```bash
# 1. Crear rama para nueva funcionalidad
git checkout -b feature/notificaciones-push

# 2. Trabajar en la funcionalidad...

# 3. Guardar cambios
git add .
git commit -m "Agregar sistema de notificaciones push"

# 4. Subir a GitHub
git push -u origin feature/notificaciones-push

# 5. Cuando funcione, crear Pull Request en GitHub
# O fusionar localmente:
git checkout main
git merge feature/notificaciones-push
git push

# 6. Eliminar rama ya fusionada
git branch -d feature/notificaciones-push
```

---

## 🛡️ Estrategia Segura para Ti

### Estructura de Ramas Recomendada:

```
main              ← Código estable, siempre funcionando
│
├── development   ← Desarrollo activo
│   │
│   ├── feature/pagos
│   ├── feature/chat
│   └── fix/bug-qr
│
└── production    ← Solo para deploy (opcional)
```

### Comandos para Implementar:

```bash
# 1. Setup inicial
git checkout main
git push -u origin main

# 2. Crear rama de desarrollo
git checkout -b development
git push -u origin development

# 3. Trabajar siempre en development o ramas de features
git checkout development
# ... hacer cambios ...
git add .
git commit -m "Cambios seguros"
git push

# 4. Solo fusionar a main cuando TODO funcione
git checkout main
git merge development
git push
```

---

## 📋 Ejemplo Práctico Completo

### Escenario: Quieres agregar un sistema de reportes

```bash
# 1. Asegúrate de estar actualizado
git checkout main
git pull

# 2. Crear rama para la nueva funcionalidad
git checkout -b feature/reportes

# 3. Ver en qué rama estás
git branch --show-current
# Debería mostrar: feature/reportes

# 4. Hacer cambios en tu código...
# (Editar archivos, agregar funcionalidades, etc.)

# 5. Guardar cambios frecuentemente
git add .
git commit -m "Agregar modelo de Reportes"

# 6. Seguir trabajando...
git add .
git commit -m "Agregar controlador de Reportes"

# 7. Probar que todo funcione
php artisan serve
# ... probar en navegador ...

# 8. Si algo sale mal, puedes revertir
git log  # Ver commits
git reset --hard COMMIT_ID  # Volver a un commit anterior

# 9. Cuando TODO funcione bien, subir a GitHub
git push -u origin feature/reportes

# 10. Fusionar con main (cuando estés 100% seguro)
git checkout main
git merge feature/reportes

# 11. Subir main actualizado
git push

# 12. Eliminar rama ya fusionada
git branch -d feature/reportes
git push origin --delete feature/reportes
```

---

## 🚨 Comandos de Emergencia

### "¡Cometí un error en el código!"

```bash
# Ver cambios sin guardar
git status

# Descartar TODOS los cambios sin guardar
git restore .

# Descartar cambios en un archivo específico
git restore nombre-archivo.php
```

### "¡Hice commit de algo malo!"

```bash
# Deshacer el último commit (mantiene cambios)
git reset --soft HEAD~1

# Deshacer el último commit (ELIMINA cambios)
git reset --hard HEAD~1

# Ver historial de commits
git log --oneline
```

### "¡Quiero volver a cómo estaba antes!"

```bash
# Ver todos los commits
git log --oneline

# Volver a un commit específico
git reset --hard COMMIT_ID

# Ejemplo:
git reset --hard a1b2c3d
```

### "¡Empujé código malo a GitHub!"

```bash
# Revertir último commit en GitHub
git revert HEAD
git push

# O forzar push (CUIDADO)
git reset --hard HEAD~1
git push --force
```

---

## 🎯 Flujo Recomendado: Primer Uso

```bash
# 1. Verificar estado actual
git status

# 2. Crear rama inicial
git checkout -b main

# 3. Agregar todos los archivos
git add .

# 4. Primer commit
git commit -m "Initial commit: Order QR System"

# 5. Conectar con GitHub
git remote add origin https://github.com/TU_USUARIO/order-qr-system.git

# 6. Subir a GitHub
git push -u origin main

# 7. Crear rama de desarrollo para trabajo diario
git checkout -b development

# 8. Subir rama de desarrollo
git push -u origin development

# ✅ Ahora tienes:
# - main: código estable
# - development: para trabajar sin miedo
```

---

## 📊 Ver Diferencias entre Ramas

```bash
# Ver diferencias entre ramas
git diff main development

# Ver qué commits tiene una rama que otra no
git log main..development

# Ver archivos modificados
git diff --name-only main development
```

---

## 🔄 Actualizar tu Rama desde Main

Si main se actualizó y quieres traer esos cambios a tu rama:

```bash
# Estando en tu rama de desarrollo
git checkout development

# Traer cambios de main
git merge main

# O usar rebase (más limpio)
git rebase main
```

---

## ✅ Buenas Prácticas

1. **Nombres de ramas descriptivos:**
   - ✅ `feature/sistema-notificaciones`
   - ✅ `fix/error-login`
   - ✅ `hotfix/qr-no-genera`
   - ❌ `cambios`
   - ❌ `test`
   - ❌ `asdf`

2. **Commits frecuentes con mensajes claros:**
   - ✅ `"Agregar validación de email en registro"`
   - ✅ `"Fix: Corregir error al generar QR"`
   - ✅ `"Update: Mejorar diseño de dashboard"`
   - ❌ `"cambios"`
   - ❌ `"fix"`
   - ❌ `"asdf"`

3. **Probar antes de fusionar:**
   - ✅ Ejecutar tests
   - ✅ Probar manualmente
   - ✅ Verificar que no hay errores
   - Solo entonces: `git merge`

4. **Mantener main limpio:**
   - `main` = código que funciona al 100%
   - Nunca hacer commits directos a `main`
   - Siempre trabajar en ramas

---

## 🎓 Resumen Ultra Rápido

```bash
# Crear rama
git checkout -b mi-rama

# Hacer cambios y guardar
git add .
git commit -m "Descripción"

# Subir a GitHub
git push -u origin mi-rama

# Volver a main
git checkout main

# Fusionar (cuando funcione)
git merge mi-rama

# Subir main
git push
```

---

## 📞 Ayuda Visual

### Estado Actual (usar siempre)
```bash
git status
git branch --show-current
```

### Ver Historial
```bash
git log --oneline --graph --all
```

### Árbol de Ramas Visual
```bash
git log --oneline --graph --decorate --all
```

---

## 🚀 Para Empezar HOY

```bash
# 1. Crear rama de desarrollo
git checkout -b development

# 2. Trabajar ahí
# ... hacer cambios ...

# 3. Guardar cambios
git add .
git commit -m "Trabajando en desarrollo"

# 4. Subir
git push -u origin development

# ✅ Ahora main está seguro!
# ✅ Trabajas en development sin miedo
```

---

**¡Listo! Ahora puedes trabajar sin miedo a romper nada.** 🎉

Siempre que quieras experimentar, crea una rama nueva. Si algo sale mal, simplemente la eliminas y listo.
