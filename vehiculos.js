var vehiculos = [
    {
        id: 1,
        marca: "Toyota",
        modelo: "Corolla",
        anio: 2024,
        precio: 850,
        imagen: "images/vehiculos/toyota.jpg"
    },
    {
        id: 2,
        marca: "Honda",
        modelo: "Civic",
        anio: 2023,
        precio: 900,
        imagen: "images/vehiculos/honda.JPG"
    },
    {
        id: 3,
        marca: "Ford",
        modelo: "Mustang",
        anio: 2024,
        precio: 1500,
        imagen: "images/vehiculos/ford.jpg"
    },
    {
        id: 4,
        marca: "Chevrolet",
        modelo: "Camaro",
        anio: 2023,
        precio: 1400,
        imagen: "images/vehiculos/chevrolet.jpg"
    },
    {
        id: 5,
        marca: "BMW",
        modelo: "Serie 3",
        anio: 2024,
        precio: 1800,
        imagen: "images/vehiculos/BMW.jpg"
    },
    {
        id: 6,
        marca: "Mercedes-Benz",
        modelo: "Clase C",
        anio: 2024,
        precio: 2000,
        imagen: "images/vehiculos/Mercedes-Benz.jpg"
    },
    {
        id: 7,
        marca: "Nissan",
        modelo: "Sentra",
        anio: 2023,
        precio: 750,
        imagen: "images/vehiculos/Nissan.jpg"
    },
    {
        id: 8,
        marca: "Volkswagen",
        modelo: "Jetta",
        anio: 2024,
        precio: 800,
        imagen: "images/vehiculos/Volkswagen.jpg"
    }
];

var vehiculoSeleccionado = null;

function filtrarVehiculos() {
    var textoBusqueda = document.getElementById("busqueda-vehiculo").value.toLowerCase().trim();
    var marcaSeleccionada = document.getElementById("filtro-marca").value;

    var resultados = [];

    for (var i = 0; i < vehiculos.length; i++) {
        var vehiculo = vehiculos[i];
        var coincideTexto = vehiculo.marca.toLowerCase().indexOf(textoBusqueda) !== -1 ||
                            vehiculo.modelo.toLowerCase().indexOf(textoBusqueda) !== -1;
        var coincideMarca = marcaSeleccionada === "" || vehiculo.marca === marcaSeleccionada;

        if (coincideTexto && coincideMarca) {
            resultados.push(vehiculo);
        }
    }

    generarListaVehiculos(resultados);
}

function generarListaVehiculos(vehiculosAMostrar) {
    var lista = vehiculosAMostrar || vehiculos;
    var contenedor = document.getElementById("vehiculos-grid");
    contenedor.innerHTML = "";

    if (lista.length === 0) {
        contenedor.innerHTML = '<div class="sin-resultados">No se encontraron vehículos que coincidan con tu búsqueda.</div>';
        return;
    }

    for (var i = 0; i < lista.length; i++) {
        var vehiculo = lista[i];

        var tarjeta = document.createElement("div");
        tarjeta.className = "vehiculo-card";
        tarjeta.setAttribute("data-id", vehiculo.id);

        tarjeta.innerHTML =
            '<div class="vehiculo-card__check">✓</div>' +
            '<img class="vehiculo-card__imagen" src="' + vehiculo.imagen + '" alt="' + vehiculo.marca + ' ' + vehiculo.modelo + '">' +
            '<div class="vehiculo-card__marca">' + vehiculo.marca + '</div>' +
            '<div class="vehiculo-card__modelo">' + vehiculo.modelo + '</div>' +
            '<div class="vehiculo-card__detalles">' +
                '<span class="vehiculo-card__anio">' + vehiculo.anio + '</span>' +
                '<div>' +
                    '<span class="vehiculo-card__precio">$' + vehiculo.precio.toLocaleString("es-MX") + '</span>' +
                    '<span class="vehiculo-card__precio-label">MXN / día</span>' +
                '</div>' +
            '</div>';

        tarjeta.addEventListener("click", (function(v) {
            return function() {
                seleccionarVehiculo(v, this);
            };
        })(vehiculo));

        contenedor.appendChild(tarjeta);
    }
}

function seleccionarVehiculo(vehiculo, tarjeta) {
    var todasLasTarjetas = document.querySelectorAll(".vehiculo-card");
    for (var i = 0; i < todasLasTarjetas.length; i++) {
        todasLasTarjetas[i].classList.remove("selected");
    }

    tarjeta.classList.add("selected");
    vehiculoSeleccionado = vehiculo;

    var display = document.getElementById("vehiculo-seleccionado-display");
    display.className = "vehiculo-display has-vehicle";
    display.innerHTML =
        '<img src="' + vehiculo.imagen + '" alt="' + vehiculo.marca + '" class="vehiculo-display__img"> ' +
        '<strong>' + vehiculo.marca + ' ' + vehiculo.modelo + '</strong> — ' +
        vehiculo.anio + ' — $' + vehiculo.precio.toLocaleString("es-MX") + ' MXN/día';

    document.getElementById("seccion-reserva").scrollIntoView({
        behavior: "smooth",
        block: "center"
    });
}

function validarFormulario(fechaInicio, fechaFin) {
    if (!vehiculoSeleccionado) {
        alert("Por favor, selecciona un vehículo de la lista antes de reservar.");
        return false;
    }

    if (!fechaInicio || !fechaFin) {
        alert("Por favor, completa ambas fechas de reserva.");
        return false;
    }

    var inicio = new Date(fechaInicio);
    var fin = new Date(fechaFin);

    if (fin <= inicio) {
        alert("La fecha de finalización debe ser posterior a la fecha de inicio.");
        return false;
    }

    return true;
}

function calcularCostoTotal(fechaInicio, fechaFin, precioPorDia) {
    var inicio = new Date(fechaInicio);
    var fin = new Date(fechaFin);

    var diferenciaMs = fin.getTime() - inicio.getTime();
    var dias = Math.ceil(diferenciaMs / (1000 * 60 * 60 * 24));

    return {
        dias: dias,
        costoTotal: dias * precioPorDia
    };
}

function formatearFecha(fechaStr) {
    var opciones = { year: "numeric", month: "long", day: "numeric" };
    var fecha = new Date(fechaStr + "T00:00:00");
    return fecha.toLocaleDateString("es-MX", opciones);
}

function mostrarConfirmacion(vehiculo, fechaInicio, fechaFin, dias, costoTotal) {
    var contenedor = document.getElementById("confirmacion-reserva");

    contenedor.innerHTML =
        '<div class="confirmacion-section">' +
            '<div class="confirmacion-header">' +
                '<span class="confirmacion-icon">✅</span>' +
                '<div>' +
                    '<div class="confirmacion-titulo">¡Reserva Confirmada!</div>' +
                    '<div class="confirmacion-subtitulo">Resumen de tu reservación</div>' +
                '</div>' +
            '</div>' +
            '<div class="confirmacion-grid">' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Nombre del Vehículo</div>' +
                    '<div class="confirmacion-item__valor">' + vehiculo.marca + '</div>' +
                '</div>' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Modelo del Vehículo</div>' +
                    '<div class="confirmacion-item__valor">' + vehiculo.modelo + ' (' + vehiculo.anio + ')</div>' +
                '</div>' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Fecha de Inicio</div>' +
                    '<div class="confirmacion-item__valor">' + formatearFecha(fechaInicio) + '</div>' +
                '</div>' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Fecha de Finalización</div>' +
                    '<div class="confirmacion-item__valor">' + formatearFecha(fechaFin) + '</div>' +
                '</div>' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Fecha de Reserva</div>' +
                    '<div class="confirmacion-item__valor">' + dias + (dias === 1 ? ' día' : ' días') + '</div>' +
                '</div>' +
                '<div class="confirmacion-item">' +
                    '<div class="confirmacion-item__label">Precio por Día</div>' +
                    '<div class="confirmacion-item__valor">$' + vehiculo.precio.toLocaleString("es-MX") + ' MXN</div>' +
                '</div>' +
                '<div class="confirmacion-item confirmacion-total">' +
                    '<div class="confirmacion-item__label">Costo Total de la Renta</div>' +
                    '<div class="confirmacion-item__valor">$' + costoTotal.toLocaleString("es-MX") + ' MXN</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    contenedor.scrollIntoView({ behavior: "smooth", block: "center" });
}

function manejarEnvioFormulario(evento) {
    evento.preventDefault();

    var fechaInicio = document.getElementById("fecha-inicio").value;
    var fechaFin = document.getElementById("fecha-fin").value;

    if (!validarFormulario(fechaInicio, fechaFin)) {
        return;
    }

    var calculo = calcularCostoTotal(fechaInicio, fechaFin, vehiculoSeleccionado.precio);

    mostrarConfirmacion(
        vehiculoSeleccionado,
        fechaInicio,
        fechaFin,
        calculo.dias,
        calculo.costoTotal
    );
}

document.addEventListener("DOMContentLoaded", function() {
    generarListaVehiculos();

    var formulario = document.getElementById("formulario-reserva");
    formulario.addEventListener("submit", manejarEnvioFormulario);

    // Listeners para búsqueda en tiempo real
    var campoBusqueda = document.getElementById("busqueda-vehiculo");
    campoBusqueda.addEventListener("input", filtrarVehiculos);

    var filtroMarca = document.getElementById("filtro-marca");
    filtroMarca.addEventListener("change", filtrarVehiculos);
});
