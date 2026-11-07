/**
 * TRIVIA DEL BIOHUERTO — Juego de preguntas interactivas
 * Semillitas del Futuro · Vanilla JavaScript
 * 80 preguntas totales, 8 por juego (aleatorias y sin repetir entre partidas)
 */

const TRIVIA_PREGUNTAS_TOTALES = [
    {
        pregunta: "¿Cuál es la parte de la planta que absorbe agua y nutrientes del suelo?",
        opciones: ["Hojas", "Tallo", "Raíces", "Flores"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué proceso realizan las plantas para crear su propio alimento usando la luz solar?",
        opciones: ["Respiración", "Fotosíntesis", "Transpiración", "Germinación"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué gas liberan las plantas durante la fotosíntesis?",
        opciones: ["Dióxido de carbono", "Nitrógeno", "Oxígeno", "Hidrógeno"],
        respuesta: 2
    },
    {
        pregunta: "¿Cuál es la profundidad recomendada para sembrar semillas pequeñas como la lechuga?",
        opciones: ["Al doble de su tamaño", "Muy profundo", "Solo una fina capa de abono", "En la superficie"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué tipo de riego es recomendado para no desenterrar semillas recién puestas?",
        opciones: ["Manguera a presión", "Regadera de lluvia fina", "Cubeta directa", "No regar"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué insectos son considerados polinizadores importantes en el huerto?",
        opciones: ["Moscas", "Abejas y mariposas", "Hormigas", "Arañas"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es el abono orgánico?",
        opciones: ["Plástico reciclado", "Compost casero o humus de lombriz", "Químicos sintéticos", "Arena"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta realiza la fotosíntesis?",
        opciones: ["Raíces", "Tallo", "Hojas", "Flores"],
        respuesta: 2
    },
    {
        pregunta: "¿Cuál es el proceso por el cual las plantas liberan vapor de agua por las hojas?",
        opciones: ["Fotosíntesis", "Transpiración", "Respiración", "Germinación"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué nutriente es esencial para el crecimiento de las hojas y tallos?",
        opciones: ["Nitrógeno", "Fósforo", "Potasio", "Calcio"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué tipo de abono se obtiene de la descomposición de materia orgánica?",
        opciones: ["Abono químico", "Compost", "Arena", "Sal"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué herramientas son necesarias para sembrar en un huerto escolar?",
        opciones: ["Pala, regadera, guantes, rastrillo", "Martillo, clavos, sierra", "Computadora, impresora", "Papel, lápiz"],
        respuesta: 0
    },
    {
        pregunta: "¿Cuánto tiempo tarda aproximadamente en germinar una semilla de lechuga?",
        opciones: ["1 día", "3-7 días", "30 días", "6 meses"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué significa 'plagas' en un huerto?",
        opciones: ["Insectos que ayudan a las plantas", "Animales que dañan los cultivos", "Fertilizantes naturales", "Herramientas de jardinería"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué método natural se usa para controlar plagas sin químicos?",
        opciones: ["Insecticidas químicos", "Control biológico con insectos benéficos", "Fuego", "Ignorarlas"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es el compostaje?",
        opciones: ["Tirar basura al suelo", "Descomposición controlada de materia orgánica para hacer abono", "Quemar plantas", "Regar en exceso"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta transporta el agua y los nutrientes desde las raíces hasta las hojas?",
        opciones: ["Raíces", "Tallo", "Pétalos", "Semillas"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué tipo de suelo es mejor para la mayoría de las plantas de huerto?",
        opciones: ["Arena pura", "Arcilla pura", "Tierra suelta con buena materia orgánica", "Concreto"],
        respuesta: 2
    },
    {
        pregunta: "¿Cuál es la función de las flores en una planta?",
        opciones: ["Absorber agua", "Producir semillas y reproducir la planta", "Almacenar comida", "Proteger las raíces"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es la polinización?",
        opciones: ["Regar las plantas", "Transferir el polen de una flor a otra para formar semillas", "Podar hojas", "Cosechar frutos"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué animales ayudan con la polinización?",
        opciones: ["Perros y gatos", "Abejas, mariposas y colibríes", "Serpientes y lagartos", "Ratones y topillos"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué se debe hacer con las malezas en el huerto?",
        opciones: ["Dejarlas crecer", "Eliminarlas porque compiten por nutrientes y agua", "Regarlas más", "Cubrirlas con plástico"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es el mulching o acolchado?",
        opciones: ["Cubrir el suelo con material orgánico para retener humedad", "Pintar el suelo", "Quemar el suelo", "Dejar el suelo descubierto"],
        respuesta: 0
    },
    {
        pregunta: "¿Cuál es la mejor hora para regar el huerto?",
        opciones: ["Al mediodía bajo el sol", "Temprano en la mañana o al atardecer", "Por la noche con luna llena", "En cualquier momento"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué sucede si se riega en exceso una planta?",
        opciones: ["Crece más rápido", "Las raíces pueden pudrirse por falta de oxígeno", "Florece más", "No pasa nada"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué herramienta se usa para cavar y hacer hoyos para sembrar?",
        opciones: ["Regadera", "Pala o pico", "Tijeras de podar", "Rastrillo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es una plántula o semillero?",
        opciones: ["Una planta adulta", "Una planta joven en etapa temprana de crecimiento", "Una semilla seca", "Una maceta vacía"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué ventaja tiene usar semillas orgánicas?",
        opciones: ["Crecen más rápido", "No tienen químicos y son más saludables", "Son más baratas", "Florecen más"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es la asociación de cultivos?",
        opciones: ["Sembrar todo junto sin orden", "Sembrar diferentes plantas juntas que se benefician mutuamente", "Sembrar solo un tipo de planta", "No sembrar nada"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué planta se usa comúnmente para repeler insectos en el huerto?",
        opciones: ["Rosa", "Caléndula o albahaca", "Cactus", "Helecho"],
        respuesta: 1
    },
    {
        pregunta: "¿Cuánta luz solar necesitan la mayoría de las verduras de huerto?",
        opciones: ["Sin luz", "Menos de 2 horas", "Al menos 6 horas de sol directo", "Solo luz artificial"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué es el drenaje en un huerto?",
        opciones: ["Regar mucho", "Que el agua excedente pueda salir del suelo sin encharcar", "No regar nunca", "Usar manguera"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué se puede hacer con las cáscaras de huevo en el huerto?",
        opciones: ["Tirarlas a la basura", "Triturarlas y usarlas como fuente de calcio para las plantas", "Usarlas como macetas", "Pintarlas"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué herramienta se usa para podar ramas y hojas secas?",
        opciones: ["Pala", "Tijeras de podar o podadora", "Regadera", "Rastrillo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué es la rotación de cultivos?",
        opciones: ["Sembrar siempre lo mismo en el mismo lugar", "Cambiar el tipo de planta en cada parcela cada temporada para evitar agotar el suelo", "No sembrar nada", "Sembrar todo junto"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué color tiene la clorofila en las plantas?",
        opciones: ["Rojo", "Verde", "Azul", "Amarillo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la zanahoria?",
        opciones: ["Hoja", "Raíz", "Tallo", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la lechuga?",
        opciones: ["Raíz", "Tallo", "Hojas", "Flor"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué parte de la planta se come en el brócoli?",
        opciones: ["Raíz", "Flor e inmaduras y tallo", "Semilla", "Fruto"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el tomate?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la cebolla?",
        opciones: ["Hoja", "Tallo", "Bulbo (raíz modificada)", "Flor"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué parte de la planta se come en el apio?",
        opciones: ["Hoja", "Tallo", "Raíz", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el brócoli?",
        opciones: ["Flor y tallo", "Raíz", "Semilla", "Hoja"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en la espinaca?",
        opciones: ["Raíz", "Hoja", "Tallo", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el pepino?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el maíz?",
        opciones: ["Hoja", "Granos (semillas)", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el frijol?",
        opciones: ["Hoja", "Semilla", "Raíz", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el arroz?",
        opciones: ["Hoja", "Semilla (grano)", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la papa?",
        opciones: ["Hoja", "Tallo", "Tubérculo (tallo subterráneo modificado)", "Flor"],
        respuesta: 2
    },
    {
        pregunta: "¿Qué parte de la planta se come en la zanahoria?",
        opciones: ["Raíz", "Hoja", "Tallo", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el ajo?",
        opciones: ["Hoja", "Bulbo", "Raíz", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el brócoli?",
        opciones: ["Flor y tallo", "Raíz", "Semilla", "Hoja"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en la coliflor?",
        opciones: ["Flor (cogollo)", "Raíz", "Semilla", "Hoja"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el repollo?",
        opciones: ["Hojas que forman un cabezuelo", "Raíz", "Semilla", "Tallo"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en la acelga?",
        opciones: ["Hojas y tallos", "Raíz", "Semilla", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el pimiento?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la berenjena?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el calabacín?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el maíz dulce?",
        opciones: ["Hoja", "Granos (semillas)", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el guisante?",
        opciones: ["Hoja", "Semilla dentro de la vaina", "Raíz", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el ejote?",
        opciones: ["Hoja", "Vaina con semillas", "Raíz", "Flor"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el chayote?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el jitomate?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el chile?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en la calabaza?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el pepino?",
        opciones: ["Hoja", "Fruto", "Raíz", "Tallo"],
        respuesta: 1
    },
    {
        pregunta: "¿Qué parte de la planta se come en el rábano?",
        opciones: ["Raíz", "Hoja", "Tallo", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en la remolacha?",
        opciones: ["Raíz", "Hoja", "Tallo", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en la cebolla de verdeo?",
        opciones: ["Tallo y hojas", "Raíz", "Semilla", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el perejil?",
        opciones: ["Hojas", "Raíz", "Tallo", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el cilantro?",
        opciones: ["Hojas y semillas", "Raíz", "Tallo", "Flor"],
        respuesta: 0
    },
    {
        pregunta: "¿Qué parte de la planta se come en el orégano?",
        opciones: ["Hojas", "Raíz", "Tallo", "Flor"],
        respuesta: 0
    }
];

// Variable para recordar qué preguntas ya se mostraron en la sesión actual
let preguntasYaMostradas = [];

let triviaEstado = {
    indiceActual: 0,
    puntuacion: 0,
    respondiendo: false,
    preguntasSeleccionadas: [],
    opcionesMezcladas: [],
    respuestaCorrectaVisual: -1,
    respuestas: [],
    segundos: 0,
    temporizador: null
};

function mezclarArray(array) {
    const copia = [...array];
    for (let i = copia.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copia[i], copia[j]] = [copia[j], copia[i]];
    }
    return copia;
}

function iniciarTrivia() {
    // Filtrar preguntas que aún no se han mostrado en esta sesión
    let preguntasDisponibles = TRIVIA_PREGUNTAS_TOTALES.filter(p => !preguntasYaMostradas.includes(p.pregunta));
    
    // Si ya se mostraron todas, reiniciar la lista
    if (preguntasDisponibles.length < 8) {
        preguntasYaMostradas = [];
        preguntasDisponibles = [...TRIVIA_PREGUNTAS_TOTALES];
    }
    
    // Seleccionar 8 preguntas aleatorias de las disponibles
    const todasMezcladas = mezclarArray(preguntasDisponibles);
    triviaEstado.preguntasSeleccionadas = todasMezcladas.slice(0, 8);
    
    // Registrar estas preguntas como mostradas
    triviaEstado.preguntasSeleccionadas.forEach(p => {
        if (!preguntasYaMostradas.includes(p.pregunta)) {
            preguntasYaMostradas.push(p.pregunta);
        }
    });
    
    triviaEstado.indiceActual = 0;
    triviaEstado.puntuacion = 0;
    triviaEstado.respondiendo = false;
    triviaEstado.respuestas = [];
    iniciarTemporizadorTrivia();
    mostrarPreguntaTrivia();
}

function formatearTiempoTrivia(segundos) {
    const m = Math.floor(segundos / 60);
    const s = segundos % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function pintarTiempoTrivia() {
    const el = document.getElementById('trivia-tiempo');
    if (el) el.textContent = formatearTiempoTrivia(triviaEstado.segundos);
}

function detenerTemporizadorTrivia() {
    if (triviaEstado.temporizador) {
        clearInterval(triviaEstado.temporizador);
        triviaEstado.temporizador = null;
    }
}

function iniciarTemporizadorTrivia() {
    detenerTemporizadorTrivia();
    triviaEstado.segundos = 0;
    pintarTiempoTrivia();
    triviaEstado.temporizador = setInterval(() => {
        triviaEstado.segundos++;
        pintarTiempoTrivia();
    }, 1000);
}

function mostrarPreguntaTrivia() {
    const pregunta = triviaEstado.preguntasSeleccionadas[triviaEstado.indiceActual];
    const totalPreguntas = triviaEstado.preguntasSeleccionadas.length;
    const progreso = ((triviaEstado.indiceActual + 1) / totalPreguntas * 100).toFixed(0);
    
    // Mezclar opciones y guardar mapeo: índice visual -> índice original
    const opcionesConIndice = pregunta.opciones.map((opcion, i) => ({ opcion, indiceOriginal: i }));
    const opcionesMezcladas = mezclarArray(opcionesConIndice);
    
    // Guardar mapeo para saber cuál índice visual es la respuesta correcta
    triviaEstado.opcionesMezcladas = opcionesMezcladas;
    triviaEstado.respuestaCorrectaVisual = opcionesMezcladas.findIndex(
        item => item.indiceOriginal === pregunta.respuesta
    );
    
    const html = `
        <div class="trivia-progress">
            <span>Pregunta ${triviaEstado.indiceActual + 1} de ${totalPreguntas}</span>
            <span class="trivia-score">${triviaEstado.puntuacion} puntos</span>
        </div>
        <div class="trivia-question">
            <div class="trivia-question__text">${pregunta.pregunta}</div>
            <div class="trivia-options">
                ${opcionesMezcladas.map((item, i) => `
                    <button class="trivia-option" onclick="seleccionarRespuestaTrivia(${i})">
                        ${item.opcion}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
    
    document.getElementById('trivia-contenido').innerHTML = html;
    document.getElementById('trivia-resultado').style.display = 'none';
    document.getElementById('trivia-juego').style.display = 'block';
    document.getElementById('trivia-btn-reiniciar').style.display = 'none';
}

function seleccionarRespuestaTrivia(indiceVisual) {
    if (triviaEstado.respondiendo) return;
    triviaEstado.respondiendo = true;
    
    const pregunta = triviaEstado.preguntasSeleccionadas[triviaEstado.indiceActual];
    const opciones = document.querySelectorAll('.trivia-option');
    const btnSeleccionado = opciones[indiceVisual];
    const btnCorrecto = opciones[triviaEstado.respuestaCorrectaVisual];
    
    const esCorrecta = indiceVisual === triviaEstado.respuestaCorrectaVisual;
    
    if (esCorrecta) {
        triviaEstado.puntuacion += 10;
        btnSeleccionado.classList.add('correct');
        showToast('¡Correcto! +10 puntos', 'success');
    } else {
        btnSeleccionado.classList.add('wrong');
        btnCorrecto.classList.add('correct');
        showToast('Incorrecto. La respuesta era: ' + pregunta.opciones[pregunta.respuesta], 'danger');
    }
    
    setTimeout(() => {
        triviaEstado.indiceActual++;
        triviaEstado.respondiendo = false;
        
        if (triviaEstado.indiceActual < triviaEstado.preguntasSeleccionadas.length) {
            mostrarPreguntaTrivia();
        } else {
            mostrarResultadoTrivia();
        }
    }, 1500);
}

function mostrarResultadoTrivia() {
    detenerTemporizadorTrivia();
    const porcentaje = (triviaEstado.puntuacion / (triviaEstado.preguntasSeleccionadas.length * 10) * 100).toFixed(0);
    let mensaje = '';
    
    if (porcentaje >= 80) {
        mensaje = '¡Excelente! Eres un experto en botánica 🌿';
    } else if (porcentaje >= 50) {
        mensaje = 'Bien hecho, pero aún puedes aprender más sobre las plantas.';
    } else {
        mensaje = 'Sigue estudiando sobre la naturaleza y el huerto escolar.';
    }
    
    const html = `
        <div class="trivia-result">
            <div class="trivia-result__score">${triviaEstado.puntuacion} pts</div>
            <div class="trivia-result__message">${mensaje}</div>
            <div style="font-size: 0.875rem; color: var(--color-text-muted);">
                Acertaste ${triviaEstado.puntuacion / 10} de ${triviaEstado.preguntasSeleccionadas.length} preguntas (${porcentaje}%)
            </div>
        </div>
    `;
    
    document.getElementById('trivia-contenido').innerHTML = html;
    document.getElementById('trivia-juego').style.display = 'none';
    document.getElementById('trivia-resultado').style.display = 'block';
    document.getElementById('trivia-btn-reiniciar').style.display = 'inline-flex';
}

// Exponer funciones globalmente
window.iniciarTrivia = iniciarTrivia;
window.detenerTrivia = detenerTemporizadorTrivia;