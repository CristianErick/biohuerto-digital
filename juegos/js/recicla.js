/**
 * CLASIFICA Y RECICLA — Semillitas del Futuro
 * Arrastrar y soltar + tocar para clasificar (funciona también en móvil)
 */

(function () {
    'use strict';

    const CONTENEDORES = {
        organicos: { nombre: 'Orgánicos', icono: '🍎' },
        plastico: { nombre: 'Plástico', icono: '🥤' },
        papel: { nombre: 'Papel', icono: '📄' },
        vidrio: { nombre: 'Vidrio', icono: '🍾' }
    };

    const ITEMS = [
        { id: 'cascara_banana', nombre: 'Cáscara de banana', contenedor: 'organicos', icono: '🍌' },
        { id: 'cascaras_huevo', nombre: 'Cáscaras de huevo', contenedor: 'organicos', icono: '🥚' },
        { id: 'hojas_secas', nombre: 'Hojas secas', contenedor: 'organicos', icono: '🍂' },
        { id: 'restos_comida', nombre: 'Restos de comida', contenedor: 'organicos', icono: '🥗' },
        { id: 'cascara_naranja', nombre: 'Cáscara de naranja', contenedor: 'organicos', icono: '🍊' },
        { id: 'cascara_manzana', nombre: 'Cáscara de manzana', contenedor: 'organicos', icono: '🍏' },
        { id: 'posca_cafe', nombre: 'Poso de café', contenedor: 'organicos', icono: '☕' },
        { id: 'botella_plastico', nombre: 'Botella de plástico', contenedor: 'plastico', icono: '🥤' },
        { id: 'envase_yogurt', nombre: 'Envase de yogurt', contenedor: 'plastico', icono: '🥛' },
        { id: 'bolsa_plastico', nombre: 'Bolsa plástica', contenedor: 'plastico', icono: '🛍️' },
        { id: 'tapones', nombre: 'Tapones de plástico', contenedor: 'plastico', icono: '🔘' },
        { id: 'envase_shampoo', nombre: 'Envase de shampoo', contenedor: 'plastico', icono: '🧴' },
        { id: 'tapa_botella', nombre: 'Tapa de botella', contenedor: 'plastico', icono: '🧷' },
        { id: 'periodico', nombre: 'Periódico', contenedor: 'papel', icono: '📰' },
        { id: 'carton', nombre: 'Cartón', contenedor: 'papel', icono: '📦' },
        { id: 'hoja_papel', nombre: 'Hoja de papel', contenedor: 'papel', icono: '📄' },
        { id: 'caja_carton', nombre: 'Caja de cartón', contenedor: 'papel', icono: '📦' },
        { id: 'servilletas_usadas', nombre: 'Servilletas usadas', contenedor: 'papel', icono: '🧻' },
        { id: 'libro_viejo', nombre: 'Libro viejo', contenedor: 'papel', icono: '📚' },
        { id: 'botella_vidrio', nombre: 'Botella de vidrio', contenedor: 'vidrio', icono: '🍾' },
        { id: 'frasco_vidrio', nombre: 'Frasco de vidrio', contenedor: 'vidrio', icono: '🫙' },
        { id: 'botella_perfume', nombre: 'Botella de perfume', contenedor: 'vidrio', icono: '🧴' },
        { id: 'vaso_vidrio', nombre: 'Vaso de vidrio', contenedor: 'vidrio', icono: '🥛' },
        { id: 'espejo_roto', nombre: 'Espejo roto', contenedor: 'vidrio', icono: '🪞' },
        { id: 'lampadilla', nombre: 'Bombilla de vidrio', contenedor: 'vidrio', icono: '💡' }
    ];

    const ITEMS_POR_CONTENEDOR = 3;

    const estado = {
        items: [],
        colocados: [],
        seleccionado: null,
        arrastrando: null,
        porContenedor: {},
        segundos: 0,
        temporizador: null,
        terminado: false
    };

    function mezclar(array) {
        const copia = [...array];
        for (let i = copia.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [copia[i], copia[j]] = [copia[j], copia[i]];
        }
        return copia;
    }

    function formatearTiempo(segundos) {
        const m = Math.floor(segundos / 60);
        const s = segundos % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function pintarTiempo() {
        const el = document.getElementById('recicla-tiempo');
        if (el) el.textContent = formatearTiempo(estado.segundos);
    }

    function detenerTemporizador() {
        if (estado.temporizador) {
            clearInterval(estado.temporizador);
            estado.temporizador = null;
        }
    }

    function iniciarTemporizador() {
        detenerTemporizador();
        estado.segundos = 0;
        pintarTiempo();
        estado.temporizador = setInterval(() => {
            estado.segundos++;
            pintarTiempo();
        }, 1000);
    }

    function mensaje(texto, tipo) {
        const feedback = document.getElementById('recicla-feedback');
        if (!feedback) return;
        feedback.textContent = texto;
        feedback.className = 'recicla-feedback show ' + (tipo || 'info');
        clearTimeout(feedback._ocultar);
        feedback._ocultar = setTimeout(() => {
            feedback.classList.remove('show');
        }, 2200);
    }

    function actualizarContadores() {
        const total = document.getElementById('recicla-total');
        const maximo = document.getElementById('recicla-total-max');
        if (total) total.textContent = String(estado.colocados.length);
        if (maximo) maximo.textContent = String(estado.items.length);
    }

    function crearContenedores() {
        const contenedor = document.getElementById('recicla-contenedores');
        if (!contenedor) return;

        contenedor.innerHTML = Object.keys(CONTENEDORES).map((clave) => {
            const info = CONTENEDORES[clave];
            return '<div class="recicla-container" id="contenedor-' + clave + '" data-contenedor="' + clave + '">' +
                '<div class="recicla-container__icon">' + info.icono + '</div>' +
                '<div class="recicla-container__name">' + info.nombre + '</div>' +
                '<div class="recicla-container__count" id="cuenta-' + clave + '">0</div>' +
            '</div>';
        }).join('');

        Object.keys(CONTENEDORES).forEach((clave) => {
            const caja = document.getElementById('contenedor-' + clave);

            caja.addEventListener('dragover', (e) => {
                e.preventDefault();
                caja.classList.add('active');
            });
            caja.addEventListener('dragleave', () => caja.classList.remove('active'));
            caja.addEventListener('drop', (e) => {
                e.preventDefault();
                caja.classList.remove('active');
                const id = e.dataTransfer.getData('text/plain') || estado.arrastrando;
                if (id) intentarColocar(id, clave);
            });
            caja.addEventListener('click', () => {
                if (estado.seleccionado) intentarColocar(estado.seleccionado, clave);
            });
        });
    }

    function crearItems() {
        const contenedor = document.getElementById('recicla-items');
        if (!contenedor) return;

        contenedor.innerHTML = estado.items.map((item) => (
            '<div class="recicla-item" id="item-' + item.id + '" data-item="' + item.id + '" ' +
                'draggable="true" role="button" tabindex="0" aria-label="' + item.nombre + '">' +
                '<div class="recicla-item__icon">' + item.icono + '</div>' +
                '<div class="recicla-item__name">' + item.nombre + '</div>' +
            '</div>'
        )).join('');

        estado.items.forEach((item) => {
            const el = document.getElementById('item-' + item.id);

            el.addEventListener('dragstart', (e) => {
                if (el.dataset.colocado === 'true') {
                    e.preventDefault();
                    return;
                }
                estado.arrastrando = item.id;
                estado.seleccionado = item.id;
                resaltarSeleccionado();
                if (e.dataTransfer) {
                    e.dataTransfer.setData('text/plain', item.id);
                    e.dataTransfer.effectAllowed = 'move';
                }
                el.classList.add('dragging');
            });

            el.addEventListener('dragend', () => {
                el.classList.remove('dragging');
                estado.arrastrando = null;
            });

            el.addEventListener('click', () => {
                if (el.dataset.colocado === 'true') return;
                if (estado.seleccionado === item.id) {
                    estado.seleccionado = null;
                } else {
                    estado.seleccionado = item.id;
                    mensaje('Toca el contenedor correcto para "' + item.nombre + '".', 'info');
                }
                resaltarSeleccionado();
            });

            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    el.click();
                }
            });
        });
    }

    function resaltarSeleccionado() {
        document.querySelectorAll('.recicla-item').forEach((el) => {
            el.classList.toggle('selected', el.dataset.item === estado.seleccionado);
        });
        document.querySelectorAll('.recicla-container').forEach((el) => {
            el.classList.toggle('esperando', Boolean(estado.seleccionado));
        });
    }

    function intentarColocar(itemId, claveContenedor) {
        if (estado.terminado) return;
        if (estado.colocados.includes(itemId)) return;

        const item = estado.items.find((i) => i.id === itemId);
        const caja = CONTENEDORES[claveContenedor];
        if (!item || !caja) return;

        if (item.contenedor !== claveContenedor) {
            mensaje('"' + item.nombre + '" no va en ' + caja.nombre + '. Inténtalo otra vez.', 'error');
            const el = document.getElementById('item-' + itemId);
            if (el) {
                el.classList.add('error');
                setTimeout(() => el.classList.remove('error'), 600);
            }
            return;
        }

        // Correcto
        estado.colocados.push(itemId);
        estado.porContenedor[claveContenedor] = (estado.porContenedor[claveContenedor] || 0) + 1;
        estado.seleccionado = null;

        const el = document.getElementById('item-' + itemId);
        if (el) {
            el.dataset.colocado = 'true';
            el.classList.remove('selected', 'dragging');
            el.classList.add('dropped');
            el.setAttribute('aria-disabled', 'true');
            el.setAttribute('draggable', 'false');
        }

        const cuenta = document.getElementById('cuenta-' + claveContenedor);
        if (cuenta) cuenta.textContent = String(estado.porContenedor[claveContenedor]);

        actualizarContadores();
        resaltarSeleccionado();
        mensaje('¡Correcto! "' + item.nombre + '" va en ' + caja.nombre + '.', 'success');

        if (window.showToast) {
            window.showToast('¡Correcto!', 'success');
        }

        if (estado.colocados.length >= estado.items.length) {
            estado.terminado = true;
            setTimeout(mostrarVictoria, 600);
        }
    }

    function mostrarVictoria() {
        detenerTemporizador();

        const cuerpo = document.getElementById('recicla-cuerpo');
        const resultado = document.getElementById('recicla-resultado');
        if (!resultado) return;

        if (cuerpo) cuerpo.style.display = 'none';

        resultado.innerHTML =
            '<div class="juego-resultado__icon">' +
                '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v4a5 5 0 0 1-10 0z"/>' +
                    '<path d="M7 4H4v2a4 4 0 0 0 4 4"/><path d="M17 4h3v2a4 4 0 0 1-4 4"/>' +
                '</svg>' +
            '</div>' +
            '<h3 class="juego-resultado__title">¡Felicidades!</h3>' +
            '<p class="juego-resultado__text">Clasificaste los ' + estado.items.length +
                ' residuos en <strong>' + formatearTiempo(estado.segundos) + '</strong>.</p>' +
            '<button type="button" class="btn btn--primary" onclick="iniciarRecicla()">Jugar de nuevo</button>';

        resultado.classList.add('show');
    }

    function iniciarRecicla() {
        detenerTemporizador();

        estado.colocados = [];
        estado.seleccionado = null;
        estado.arrastrando = null;
        estado.terminado = false;
        estado.segundos = 0;
        estado.porContenedor = {};

        // 3 residuos aleatorios de cada categoría (12 por partida)
        estado.items = mezclar(Object.keys(CONTENEDORES)).reduce((lista, clave) => {
            const grupo = mezclar(ITEMS.filter((item) => item.contenedor === clave));
            return lista.concat(grupo.slice(0, ITEMS_POR_CONTENEDOR));
        }, []);
        estado.items = mezclar(estado.items);

        const cuerpo = document.getElementById('recicla-cuerpo');
        const resultado = document.getElementById('recicla-resultado');
        if (cuerpo) cuerpo.style.display = '';
        if (resultado) {
            resultado.classList.remove('show');
            resultado.innerHTML = '';
        }

        crearContenedores();
        crearItems();
        actualizarContadores();
        resaltarSeleccionado();

        const feedback = document.getElementById('recicla-feedback');
        if (feedback) feedback.classList.remove('show');

        iniciarTemporizador();
    }

    window.iniciarRecicla = iniciarRecicla;
    window.detenerRecicla = detenerTemporizador;
})();
