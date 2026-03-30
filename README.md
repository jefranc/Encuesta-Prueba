# Sistema de Encuestas Dinámico

Aplicación web desarrollada en Laravel 12 que permite a los usuarios responder encuestas de satisfacción de forma dinámica y visualizar estadísticas de cumplimiento en tiempo real.

---

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación y Despliegue](#instalación-y-despliegue)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Manual Técnico](#manual-técnico)
- [Manual de Usuario](#manual-de-usuario)

---

## Requisitos

| Componente | Versión mínima |
|---|---|
| PHP | 8.2 |
| Composer | 2.x |
| MySQL | 5.7+ |
| Servidor web | Apache / Nginx / PHP built-in |

---

## Instalación y Despliegue

### 1. Clonar el repositorio

```bash
git clone https://github.com/jefranc/Encuesta-Prueba.git
cd Encuesta-Prueba
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar el entorno

Copiar el archivo de entorno y editarlo:

```bash
cp .env.example .env
```

Abrir `.env` y configurar la conexión a base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prueba_developer
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 5. Crear la base de datos

Desde MySQL crear la base de datos:

```sql
CREATE DATABASE prueba_developer CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y carga las 6 encuestas con sus 31 preguntas iniciales.

### 7. Levantar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://127.0.0.1:8000`.

---

## Estructura del Proyecto

```
encuestas-app/
├── app/
│   ├── Http/Controllers/
│   │   └── EncuestaController.php     # Lógica principal
│   └── Models/
│       ├── Encuesta.php
│       ├── PreguntaEncuesta.php
│       ├── RespuestaEncuesta.php
│       └── RespuestaPregunta.php
├── database/
│   ├── migrations/                    # 4 migraciones del sistema
│   └── seeders/
│       └── EncuestaSeeder.php         # Datos iniciales
├── resources/views/
│   ├── layouts/app.blade.php          # Layout base
│   └── encuestas/
│       ├── index.blade.php            # Formulario de encuesta
│       └── estadisticas.blade.php     # Vista de estadísticas
└── routes/
    └── web.php                        # Definición de rutas
```

---

## Manual Técnico

### Base de Datos

El sistema maneja 4 tablas con las siguientes relaciones:

```
encuestas (1) ──< preguntas_encuesta (N)
encuestas (1) ──< respuesta_encuesta (N)
respuesta_encuesta (1) ──< respuesta_pregunta (N)
preguntas_encuesta (1) ──< respuesta_pregunta (N)
```

**Tablas con llave primaria compuesta:**

- `preguntas_encuesta`: `(num_pregunta, codigo_encuesta)`
- `respuesta_pregunta`: `(num_pregunta, codigo_encuesta, codigo_respuesta)`

Los modelos correspondientes declaran `$primaryKey = null` e `$incrementing = false` para compatibilidad con Eloquent.

### Rutas

| Método | URI | Acción |
|---|---|---|
| GET | `/` | Muestra el formulario de encuesta |
| GET | `/preguntas/{codigo}` | Devuelve preguntas en JSON (AJAX) |
| POST | `/guardar` | Persiste las respuestas |
| GET | `/estadisticas` | Muestra la tabla de estadísticas |

### Lógica de Negocio

**Factor de calificación:** cada respuesta del usuario (valor 1–5) se multiplica por `4` antes de guardarse en el campo `califica` de `respuesta_pregunta`.

```php
'califica' => $valor * 4  // rango resultante: 4 – 20
```

**Puntaje máximo posible por encuesta:**

```
max_posible = N° preguntas × 5 × 4
```

**Promedio por encuestado:**

```
promedio = suma_total_califica / total_encuestados
```

**Porcentaje de cumplimiento:**

```
porcentaje = (promedio / max_posible) × 100
```

### AJAX

La carga de preguntas es dinámica mediante `fetch()` nativo:

```javascript
const response = await fetch(`/preguntas/${codigo}`, {
    headers: { 'Accept': 'application/json' }
});
const data = await response.json();
```

El controlador devuelve un JSON con el nombre de la encuesta y el listado de preguntas. El DOM se construye en el cliente sin recargar la página.

### Validación

Las reglas de validación se generan dinámicamente según las preguntas de la encuesta seleccionada. Todas las preguntas son obligatorias con valores entre 1 y 5.

---

## Manual de Usuario

### Completar una Encuesta

1. Ingresar a la aplicación en `http://127.0.0.1:8000`
2. En el selector desplegable, elegir el tipo de atención a evaluar (Vial, Médica, Legal, etc.)
3. Las preguntas correspondientes aparecerán automáticamente debajo
4. Para cada pregunta, seleccionar una calificación del **1 (Muy malo)** al **5 (Excelente)**
5. Una vez respondidas todas las preguntas, presionar el botón **Enviar Encuesta**
6. Al enviar, el sistema redirige automáticamente a la pantalla de estadísticas

> Todas las preguntas deben ser respondidas. Si falta alguna, el sistema mostrará un mensaje de error indicando cuál completar.

### Ver Estadísticas

Acceder desde la barra de navegación en **Estadísticas** o directamente en `http://127.0.0.1:8000/estadisticas`.

La tabla muestra por cada encuesta:

| Columna | Descripción |
|---|---|
| ID | Identificador de la encuesta |
| Descripción | Nombre del tipo de atención |
| N° Preguntas | Cantidad de preguntas de esa encuesta |
| Encuestados | Total de personas que la respondieron |
| Puntaje Máx. | Puntaje máximo posible para esa encuesta |
| Promedio Obtenido | Promedio de puntaje sobre los encuestados |
| % Cumplimiento | Porcentaje del promedio sobre el máximo posible |

El porcentaje de cumplimiento se representa con una barra de progreso con código de color:

| Color | Rango |
|---|---|
| Verde | 80% o más |
| Celeste | 60% – 79% |
| Amarillo | 40% – 59% |
| Rojo | Menos del 40% |
