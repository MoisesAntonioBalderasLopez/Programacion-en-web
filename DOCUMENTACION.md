# 📄 Documentación — Sistema de Búsqueda y Filtros

## Página de Renta de Carros XYZ

**Materia:** Programación en Web  
**Autor:** Moisés Antonio Balderas López  
**Fecha:** 16 de agosto de 2026

---

## 📑 Índice

1. [Descripción General](#1-descripción-general)
2. [¿Qué se Implementó?](#2-qué-se-implementó)
3. [Archivos Modificados](#3-archivos-modificados)
4. [Guía de Uso — Cómo Probar el Sistema](#4-guía-de-uso--cómo-probar-el-sistema)
5. [Pruebas Realizadas](#5-pruebas-realizadas)
6. [Tecnologías Utilizadas](#6-tecnologías-utilizadas)

---

## 1. Descripción General

Se implementó un **sistema de búsqueda con JavaScript** en la página de renta de carros, que permite a los usuarios buscar vehículos por criterios como **marca** y **modelo**. La búsqueda es **dinámica** y actualiza los resultados en **tiempo real** sin recargar la página, brindando una experiencia de usuario fluida.

---

## 2. ¿Qué se Implementó?

Se agregaron **dos controles de búsqueda/filtrado** en la página `vehiculos.html`:

| Control | Tipo | Función |
|---------|------|---------|
| **Campo de búsqueda** | `<input type="text">` | Busca en tiempo real por marca o modelo conforme el usuario escribe |
| **Filtro por marca** | `<select>` (menú desplegable) | Filtra los vehículos mostrando solo los de la marca seleccionada |

### Características:

- 🔍 **Búsqueda en tiempo real** — Los resultados se filtran al instante mientras se escribe, sin presionar Enter.
- 🏷️ **Filtro por marca** — Menú desplegable con las 8 marcas disponibles + opción "Todas las marcas".
- 🔄 **Filtros combinados** — Ambos filtros funcionan juntos (texto + marca).
- 📱 **Sin recarga de página** — Todo el filtrado ocurre del lado del cliente con JavaScript puro.
- ❌ **Mensaje de "sin resultados"** — Cuando no hay coincidencias, se muestra un aviso claro.

---

## 3. Archivos Modificados

Se modificaron **3 archivos** del proyecto:

---

### 3.1. `vehiculos.html` — Controles de búsqueda en el HTML

**¿Qué se hizo?** Se agregó un contenedor con el campo de búsqueda y el filtro por marca, ubicado entre el título "Nuestra Flota" y el grid de vehículos.

**Código agregado:**

```html
<div class="busqueda-container">
    <div class="busqueda-campo">
        <label for="busqueda-vehiculo">🔍 Buscar:</label>
        <input type="text" id="busqueda-vehiculo" placeholder="Buscar por marca o modelo...">
    </div>
    <div class="busqueda-campo">
        <label for="filtro-marca">🏷️ Filtrar por marca:</label>
        <select id="filtro-marca">
            <option value="">Todas las marcas</option>
            <option value="Toyota">Toyota</option>
            <option value="Honda">Honda</option>
            <!-- ... demás marcas -->
        </select>
    </div>
</div>
```

---

### 3.2. `vehiculos.js` — Lógica de filtrado en JavaScript

**¿Qué se hizo?** Se agregaron/modificaron las siguientes funciones:

#### Nueva función: `filtrarVehiculos()`

Esta es la función principal del sistema de búsqueda. Se ejecuta cada vez que el usuario escribe en el buscador o cambia el filtro de marca.

```javascript
function filtrarVehiculos() {
    var textoBusqueda = document.getElementById("busqueda-vehiculo").value.toLowerCase().trim();
    var marcaSeleccionada = document.getElementById("filtro-marca").value;

    var resultados = [];

    for (var i = 0; i < vehiculos.length; i++) {
        var vehiculo = vehiculos[i];
        // Busca coincidencias parciales en marca y modelo (insensible a mayúsculas)
        var coincideTexto = vehiculo.marca.toLowerCase().indexOf(textoBusqueda) !== -1 ||
                            vehiculo.modelo.toLowerCase().indexOf(textoBusqueda) !== -1;
        // Filtra por marca si se seleccionó una específica
        var coincideMarca = marcaSeleccionada === "" || vehiculo.marca === marcaSeleccionada;

        if (coincideTexto && coincideMarca) {
            resultados.push(vehiculo);
        }
    }

    generarListaVehiculos(resultados);
}
```

#### Función modificada: `generarListaVehiculos(vehiculosAMostrar)`

Se modificó para aceptar un parámetro opcional con la lista filtrada. Si no se pasa parámetro, muestra todos. También se agregó el mensaje de "sin resultados".

#### Listeners registrados en `DOMContentLoaded`:

```javascript
// Búsqueda en tiempo real al escribir
var campoBusqueda = document.getElementById("busqueda-vehiculo");
campoBusqueda.addEventListener("input", filtrarVehiculos);

// Filtro al cambiar la marca seleccionada
var filtroMarca = document.getElementById("filtro-marca");
filtroMarca.addEventListener("change", filtrarVehiculos);
```

- **`input`** → Se dispara con cada tecla, dando resultados en tiempo real.
- **`change`** → Se dispara al seleccionar una opción diferente en el `<select>`.

---

### 3.3. `vehiculos.css` — Estilos de los controles

**¿Qué se hizo?** Se agregaron estilos para la barra de búsqueda, el filtro y el mensaje de sin resultados, manteniendo el tema oscuro del sitio.

**Clases CSS agregadas:**

| Clase | Descripción |
|-------|-------------|
| `.busqueda-container` | Contenedor flex que agrupa ambos controles |
| `.busqueda-campo` | Cada campo individual (label + input/select) |
| `.busqueda-campo input:focus` | Efecto de borde rojo al enfocar el campo |
| `.sin-resultados` | Mensaje centrado cuando no hay coincidencias |

---

## 4. Guía de Uso — Cómo Probar el Sistema

### Paso 1: Abrir la página

Abrir `vehiculos.html` en el navegador (o a través de XAMPP en `http://localhost/Programacion-en-web/vehiculos.html`).

> 📸 **CAPTURA 1:** Captura de la página completa mostrando la barra de búsqueda y filtro con todos los vehículos visibles.

---

### Paso 2: Probar la búsqueda por texto

Escribir "Toyota" en el campo de búsqueda. Solo debe aparecer el Toyota Corolla.

> 📸 **CAPTURA 2:** Captura con "Toyota" escrito en el buscador y solo el Toyota Corolla visible en el grid.

---

### Paso 3: Probar búsqueda parcial

Borrar el texto anterior y escribir "mus". Debe aparecer solo el Ford Mustang.

> 📸 **CAPTURA 3:** Captura con "mus" escrito en el buscador y solo el Ford Mustang visible.

---

### Paso 4: Probar el filtro por marca

Limpiar el buscador. Seleccionar "BMW" en el menú desplegable de marcas. Solo debe aparecer el BMW Serie 3.

> 📸 **CAPTURA 4:** Captura con el filtro "BMW" seleccionado y solo el BMW Serie 3 visible.

---

### Paso 5: Probar la combinación de filtros

Seleccionar "Ford" en el filtro de marca y escribir "mus" en el buscador. Solo debe aparecer el Ford Mustang.

> 📸 **CAPTURA 5:** Captura con filtro "Ford" + texto "mus" mostrando solo el Mustang.

---

### Paso 6: Probar búsqueda sin resultados

Escribir "Ferrari" en el buscador. Debe aparecer el mensaje "No se encontraron vehículos que coincidan con tu búsqueda."

> 📸 **CAPTURA 6:** Captura con "Ferrari" escrito y el mensaje de sin resultados visible.

---

### Paso 7: Restablecer filtros

Borrar el texto del buscador y seleccionar "Todas las marcas". Deben reaparecer los 8 vehículos.

> 📸 **CAPTURA 7:** Captura con todos los filtros restablecidos y los 8 vehículos visibles nuevamente.

---

## 5. Pruebas Realizadas

### 5.1. Pruebas de Búsqueda por Texto

| # | Caso de Prueba | Entrada | Resultado Esperado | ¿Funciona? |
|---|----------------|---------|-------------------|------------|
| 1 | Búsqueda por marca completa | "Toyota" | Solo aparece Toyota Corolla | ✅ |
| 2 | Búsqueda por modelo | "Mustang" | Solo aparece Ford Mustang | ✅ |
| 3 | Búsqueda parcial | "Cor" | Aparece Toyota Corolla | ✅ |
| 4 | Insensible a mayúsculas | "bmw" | Aparece BMW Serie 3 | ✅ |
| 5 | Sin resultados | "Ferrari" | Mensaje "No se encontraron vehículos..." | ✅ |
| 6 | Campo vacío | "" | Se muestran los 8 vehículos | ✅ |

### 5.2. Pruebas de Filtro por Marca

| # | Caso de Prueba | Selección | Resultado Esperado | ¿Funciona? |
|---|----------------|-----------|-------------------|------------|
| 1 | Todas las marcas | "Todas las marcas" | Se muestran los 8 vehículos | ✅ |
| 2 | Marca específica | "Honda" | Solo aparece Honda Civic | ✅ |
| 3 | Filtro + búsqueda | Marca: "Ford" + texto: "mus" | Solo aparece Ford Mustang | ✅ |

### 5.3. Pruebas de Tiempo Real

| # | Caso de Prueba | Acción | Resultado Esperado | ¿Funciona? |
|---|----------------|--------|-------------------|------------|
| 1 | Actualización al escribir | Escribir letra por letra | Resultados se actualizan con cada tecla | ✅ |
| 2 | Actualización al borrar | Borrar texto | Resultados reaparecen conforme se borra | ✅ |
| 3 | Sin recarga de página | Usar búsqueda y filtros | La página NO se recarga | ✅ |

---

## 6. Tecnologías Utilizadas

| Tecnología | Uso en este sistema |
|------------|-------------------|
| **HTML5** | Estructura de los controles de búsqueda (`<input>`, `<select>`) |
| **CSS3** | Estilos del buscador, animaciones de focus, mensaje sin resultados |
| **JavaScript (Vanilla)** | Lógica de filtrado en tiempo real, manipulación del DOM, eventos `input` y `change` |

### Puntos clave:

- **Sin librerías externas** — Todo se hizo con JavaScript puro, sin jQuery ni frameworks.
- **Evento `input`** — Se usa en vez de `keyup` para capturar también pegado de texto y autocompletado.
- **`toLowerCase()` + `indexOf()`** — Para búsqueda parcial insensible a mayúsculas/minúsculas.
- **`trim()`** — Para ignorar espacios al inicio/final del texto buscado.

---

> **Nota:** Para que el sistema funcione correctamente, solo es necesario abrir `vehiculos.html` en un navegador web moderno. No requiere servidor ni instalación adicional (aunque también funciona desde XAMPP).
