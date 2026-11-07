/**
 * SOPA DE LETRAS VERDE — Semillitas del Futuro
 * Vanilla JavaScript · sin dependencias
 */

(function () {
    'use strict';

    // Palabras en español, todas de 3 a 10 letras (caben en la cuadrícula 10x10)
    const PALABRAS = [
        'PLANTA', 'SEMILLA', 'AGUA', 'SOL', 'TIERRA', 'HOJA', 'RAIZ', 'TALLO', 'FLOR', 'ABONO',
        'MADERA', 'ARBOL', 'FLORES', 'SEMILLAS', 'RAICES', 'COMPOST', 'RIEGO', 'NUTRIENTES',
        'ECOLOGIA', 'NATURALEZA', 'AMBIENTE', 'RECICLAR', 'REUTILIZAR', 'ORGANICO', 'INORGANICO',
        'VIDRIO', 'PAPEL', 'PLASTICO', 'METAL', 'FRESCO', 'HERBICIDA', 'PESTICIDA', 'SUELO',
        'CULTIVO', 'HUMUS', 'REGAR', 'BROTE', 'JARDIN', 'MACETA', 'DRENAJE', 'OXIGENO',
        'CLOROFILA', 'GERMINAR', 'COSECHA', 'INJERTO', 'AZADON', 'TIJERA', 'REGADERA',
        'MANGUERA', 'CANTERO', 'LOMBRICES', 'HUMEDAD', 'LLUVIA', 'VIENTO', 'ENERGIA',
        'CONSUMO', 'BASURA', 'RESIDUO', 'LATON', 'CARTON', 'PERIODICO'
    ];

    const TAM = 10;
    const MAX_PALABRAS = 10;
    const DIRECCIONES = [
        [0, 1], [1, 0], [1, 1], [-1, 1],
        [0, -1], [-1, 0], [-1, -1], [1, -1]
    ];

    const estado = {
        palabras: [],
        encontradas: [],
        celdas: [],
        inicio: null,
        seleccion: [],
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

    function puedeEscribir(grid, palabra, fila, col, df, dc) {
        for (let i = 0; i < palabra.length; i++) {
            const f = fila + df * i;
            const c = col + dc * i;
            if (f < 0 || f >= TAM || c < 0 || c >= TAM) return false;
            if (grid[f][c] !== '' && grid[f][c] !== palabra[i]) return false;
        }
        return true;
    }

    function intentarTablero(palabras) {
        const grid = Array.from({ length: TAM }, () => Array(TAM).fill(''));
        const colocadas = [];

        for (const palabra of palabras) {
            let colocada = false;

            for (let intento = 0; intento < 400 && !colocada; intento++) {
                const [df, dc] = DIRECCIONES[Math.floor(Math.random() * DIRECCIONES.length)];
                const fila = Math.floor(Math.random() * TAM);
                const col = Math.floor(Math.random() * TAM);

                if (puedeEscribir(grid, palabra, fila, col, df, dc)) {
                    for (let i = 0; i < palabra.length; i++) {
                        grid[fila + df * i][col + dc * i] = palabra[i];
                    }
                    colocada = true;
                }
            }

            if (colocada) colocadas.push(palabra);
        }

        return { grid, colocadas };
    }

    function generarTablero(palabras) {
        // Colocar primero las palabras largas facilita que todas entren
        const ordenadas = [...palabras].sort((a, b) => b.length - a.length);
        let mejor = null;

        for (let intento = 0; intento < 60; intento++) {
            const resultado = intentarTablero(ordenadas);

            if (resultado.colocadas.length === palabras.length) {
                return resultado;
            }
            if (!mejor || resultado.colocadas.length > mejor.colocadas.length) {
                mejor = resultado;
            }
        }

        return mejor;
    }

    function rellenar(grid) {
        const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for (let f = 0; f < TAM; f++) {
            for (let c = 0; c < TAM; c++) {
                if (!grid[f][c]) {
                    grid[f][c] = letras[Math.floor(Math.random() * letras.length)];
                }
            }
        }
    }

    function actualizarContadores() {
        const total = document.getElementById('sopa-total');
        const maximo = document.getElementById('sopa-total-max');
        if (total) total.textContent = String(estado.encontradas.length);
        if (maximo) maximo.textContent = String(estado.palabras.length);
    }

    function pintarTiempo() {
        const el = document.getElementById('sopa-tiempo');
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

    function renderListaPalabras() {
        const lista = document.getElementById('sopa-lista-palabras');
        if (!lista) return;

        lista.innerHTML = estado.palabras.map((palabra) => {
            const encontrada = estado.encontradas.includes(palabra);
            return '<div class="sopa-word' + (encontrada ? ' found' : '') + '" ' +
                'data-palabra="' + palabra + '">' + palabra + '</div>';
        }).join('');
    }

    function renderTablero(grid) {
        const contenedor = document.getElementById('sopa-grid');
        if (!contenedor) return;

        contenedor.innerHTML = '';
        estado.celdas = [];

        for (let f = 0; f < TAM; f++) {
            estado.celdas[f] = [];
            for (let c = 0; c < TAM; c++) {
                const celda = document.createElement('div');
                celda.className = 'sopa-cell';
                celda.textContent = grid[f][c];
                celda.dataset.fila = f;
                celda.dataset.col = c;
                celda.addEventListener('click', () => clicCelda(f, c));
                contenedor.appendChild(celda);
                estado.celdas[f][c] = celda;
            }
        }
    }

    function limpiarSeleccion() {
        estado.seleccion.forEach((celda) => {
            celda.classList.remove('selected', 'wrong');
        });
        estado.seleccion = [];
        estado.inicio = null;
    }

    function marcarInicio(f, c) {
        limpiarSeleccion();
        estado.inicio = { f, c };
        const celda = estado.celdas[f][c];
        celda.classList.add('selected');
        estado.seleccion = [celda];
    }

    function clicCelda(f, c) {
        if (estado.terminado) return;

        const celda = estado.celdas[f][c];
        if (!celda) return;

        if (!estado.inicio) {
            marcarInicio(f, c);
            return;
        }

        if (estado.inicio.f === f && estado.inicio.c === c) {
            limpiarSeleccion();
            return;
        }

        const df = f - estado.inicio.f;
        const dc = c - estado.inicio.c;
        const esRecta = (df === 0 && dc !== 0) || (dc === 0 && df !== 0) || Math.abs(df) === Math.abs(dc);

        if (!esRecta) {
            marcarInicio(f, c);
            return;
        }

        const pasoF = Math.sign(df);
        const pasoC = Math.sign(dc);
        const largo = Math.max(Math.abs(df), Math.abs(dc)) + 1;
        const celdas = [];

        for (let i = 0; i < largo; i++) {
            celdas.push(estado.celdas[estado.inicio.f + pasoF * i][estado.inicio.c + pasoC * i]);
        }

        const texto = celdas.map((item) => item.textContent).join('');
        const inverso = [...texto].reverse().join('');
        const encontrada = estado.palabras.find((p) => p === texto || p === inverso);

        limpiarSeleccion();
        estado.seleccion = celdas;

        if (encontrada && estado.encontradas.includes(encontrada)) {
            celdas.forEach((item) => item.classList.remove('selected'));
            estado.seleccion = [];
            return;
        }

        if (encontrada) {
            celdas.forEach((item) => {
                item.classList.remove('selected');
                item.classList.add('found');
            });
            estado.encontradas.push(encontrada);

            const chip = document.querySelector('.sopa-word[data-palabra="' + encontrada + '"]');
            if (chip) chip.classList.add('found');

            estado.seleccion = [];
            actualizarContadores();

            if (window.showToast) {
                window.showToast('¡Encontraste "' + encontrada + '"!', 'success');
            }

            if (estado.encontradas.length >= estado.palabras.length) {
                estado.terminado = true;
                setTimeout(mostrarVictoria, 600);
            }
            return;
        }

        // Selección incorrecta: parpadeo rojo y se limpia sola
        celdas.forEach((item) => item.classList.add('wrong'));
        setTimeout(() => {
            celdas.forEach((item) => item.classList.remove('selected', 'wrong'));
        }, 550);
        estado.seleccion = [];
    }

    function mostrarVictoria() {
        detenerTemporizador();

        const cuerpo = document.getElementById('sopa-cuerpo');
        const resultado = document.getElementById('sopa-resultado');
        if (!resultado) return;

        if (cuerpo) cuerpo.style.display = 'none';

        resultado.innerHTML =
            '<div class="juego-resultado__icon">' +
                '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v4a5 5 0 0 1-10 0z"/>' +
                    '<path d="M7 4H4v2a4 4 0 0 0 4 4"/><path d="M17 4h3v2a4 4 0 0 1-4 4"/>' +
                '</svg>' +
            '</div>' +
            '<h3 class="juego-resultado__title">¡Excelente trabajo!</h3>' +
            '<p class="juego-resultado__text">Encontraste las ' + estado.palabras.length +
                ' palabras en <strong>' + formatearTiempo(estado.segundos) + '</strong>.</p>' +
            '<button type="button" class="btn btn--primary" onclick="iniciarSopa()">Jugar de nuevo</button>';

        resultado.classList.add('show');
    }

    function iniciarSopa() {
        detenerTemporizador();

        estado.terminado = false;
        estado.encontradas = [];
        estado.seleccion = [];
        estado.inicio = null;
        estado.segundos = 0;

        const cuerpo = document.getElementById('sopa-cuerpo');
        const resultado = document.getElementById('sopa-resultado');
        if (cuerpo) cuerpo.style.display = '';
        if (resultado) {
            resultado.classList.remove('show');
            resultado.innerHTML = '';
        }

        // 10 palabras distintas en cada partida
        estado.palabras = mezclar(PALABRAS).slice(0, MAX_PALABRAS);

        const tablero = generarTablero(estado.palabras);

        // Si alguna palabra no se pudo colocar, solo se juega con las colocadas
        estado.palabras = estado.palabras.filter((palabra) => tablero.colocadas.includes(palabra));

        rellenar(tablero.grid);

        renderTablero(tablero.grid);
        renderListaPalabras();
        actualizarContadores();
        iniciarTemporizador();
    }

    window.iniciarSopa = iniciarSopa;
    window.detenerSopa = detenerTemporizador;
})();
