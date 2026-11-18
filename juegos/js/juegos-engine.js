/**
 * SEMILLITAS DEL FUTURO — MOTOR DE JUEGOS EDUCATIVOS POR CICLOS
 * Ciclo III (1° y 2°), Ciclo IV (3° y 4°), Ciclo V (5° y 6°)
 * Vanilla JS ES6+ · Web Audio API · Anti-Spam Click · Gran Variedad de Preguntas
 */

class GameManager {
  constructor() {
    this.timer = null;
    this.seconds = 0;
    this.score = 0;
    this.juegoActual = null;
    this.modalEl = document.getElementById('modalJuegoGlobal');
    this.bodyEl = document.getElementById('juegoContenidoDinamico');
    this.titleEl = document.getElementById('juegoTituloModal');
    this.timerEl = document.getElementById('juegoTimerTexto');
    this.scoreEl = document.getElementById('juegoPuntosTexto');
    this.victoriaOverlay = document.getElementById('victoriaOverlay');
  }

  abrir(tipo) {
    this.juegoActual = tipo;
    this.seconds = 0;
    this.score = 0;
    clearInterval(this.timer);

    if (this.victoriaOverlay) this.victoriaOverlay.classList.remove('active');
    this.modalEl.classList.add('active');
    document.body.style.overflow = 'hidden';

    this.actualizarHUD();
    this.timer = setInterval(() => {
      this.seconds++;
      this.actualizarHUD();
    }, 1000);

    if (typeof this[tipo] === 'function') {
      this[tipo]();
    } else {
      this.bodyEl.innerHTML = `<p style="padding:20px; text-align:center;">Juego en preparación.</p>`;
    }

    if (window.bioSound) window.bioSound.playClick();
  }

  cerrar() {
    clearInterval(this.timer);
    this.modalEl.classList.remove('active');
    if (this.victoriaOverlay) this.victoriaOverlay.classList.remove('active');
    document.body.style.overflow = '';
    this.bodyEl.innerHTML = '';
  }

  actualizarHUD() {
    const mins = String(Math.floor(this.seconds / 60)).padStart(2, '0');
    const secs = String(this.seconds % 60).padStart(2, '0');
    if (this.timerEl) this.timerEl.textContent = `${mins}:${secs}`;
    if (this.scoreEl) this.scoreEl.textContent = `${this.score} pts`;
  }

  sumarPuntos(pts) {
    this.score += pts;
    this.actualizarHUD();
    if (window.bioSound) window.bioSound.playSuccess();
  }

  errorFeedback() {
    if (window.bioSound) window.bioSound.playError();
  }

  mostrarVictoria(mensaje, estrellas = 3) {
    clearInterval(this.timer);
    if (window.bioSound) window.bioSound.playVictory();

    const starIcons = document.querySelectorAll('.star-icon');
    starIcons.forEach((s, idx) => {
      s.classList.toggle('filled', idx < estrellas);
    });

    document.getElementById('victoriaMsg').textContent = mensaje;
    document.getElementById('victoriaTime').textContent = `Tiempo: ${this.seconds} segundos | Puntos: ${this.score}`;
    this.victoriaOverlay.classList.add('active');
  }

  // Helper para mezclar arrays
  shuffle(array) {
    const arr = [...array];
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  // Alias de compatibilidad
  trivia() { this.adivina(); }
  recicla() { this.comestibles(); }

  // ══════════════════════════════════════════════════════════════
  // CICLO III — EXPLORADORES (1.° y 2.° Grado)
  // ══════════════════════════════════════════════════════════════

  // 1. Identificar Partes de la Planta
  partesPlanta() {
    this.titleEl.textContent = 'Identifica las Partes de la Planta';
    const partes = [
      { id: 'flor', nombre: 'Flor', hint: 'Tiene pétalos de colores y atrae a las abejas' },
      { id: 'hoja', nombre: 'Hojas', hint: 'Son verdes y atrapan la luz del sol' },
      { id: 'fruto', nombre: 'Fruto', hint: 'Protege las semillas que darán nueva vida' },
      { id: 'tallo', nombre: 'Tallo', hint: 'Sostiene la planta y transporta el agua hacia arriba' },
      { id: 'raiz', nombre: 'Raíz', hint: 'Crece bajo tierra para fijar y beber agua' }
    ];

    let seleccion = null;
    let completadas = 0;

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Toca una palabra y luego toca su casilla correspondiente:
      </p>
      <div class="planta-drag-zone">
        <div class="parts-bank" id="partsBank">
          ${this.shuffle(partes).map(p => `
            <button type="button" class="part-chip" data-part="${p.id}">${p.nombre}</button>
          `).join('')}
        </div>
        <div class="planta-diagram">
          ${partes.map(p => `
            <div class="diagram-slot" data-slot="${p.id}">
              <span>[ ? ] ${p.hint}</span>
            </div>
          `).join('')}
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const chips = this.bodyEl.querySelectorAll('.part-chip');
    const slots = this.bodyEl.querySelectorAll('.diagram-slot');

    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');
        seleccion = chip.getAttribute('data-part');
        if (window.bioSound) window.bioSound.playClick();
      });
    });

    slots.forEach(slot => {
      slot.addEventListener('click', () => {
        if (!seleccion || slot.classList.contains('filled')) return;
        const slotId = slot.getAttribute('data-slot');

        if (slotId === seleccion) {
          slot.classList.add('filled');
          slot.style.pointerEvents = 'none'; // Bloqueo anti-spam
          const parteObj = partes.find(p => p.id === seleccion);
          slot.innerHTML = `<strong>${parteObj.nombre}</strong> &mdash; <span>¡Correcto!</span>`;

          const chipActivo = this.bodyEl.querySelector(`.part-chip[data-part="${seleccion}"]`);
          chipActivo.classList.add('used');
          chipActivo.classList.remove('selected');
          chipActivo.style.pointerEvents = 'none';

          this.sumarPuntos(20);
          completadas++;
          seleccion = null;

          if (completadas === partes.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Excelente trabajo! Has identificado todas las partes fundamentales de una planta.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          slot.style.borderColor = '#ef4444';
          setTimeout(() => slot.style.borderColor = '', 450);
        }
      });
    });
  }

  // 2. Memoria del Biohuerto (Variedad de hortalizas)
  memoria() {
    this.titleEl.textContent = 'Memoria del Biohuerto';
    const poolIconos = ['🥕', '🥬', '🍅', '🧅', '🍓', '🌱', '🌻', '🌽', '🫑', '🥔', '🐛', '🐝'];
    const seleccionados = this.shuffle(poolIconos).slice(0, 6);
    const cartas = this.shuffle([...seleccionados, ...seleccionados]);

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Encuentra las 6 parejas de hortalizas y amigos del huerto:
      </p>
      <div class="memory-grid" id="memoriaGrid">
        ${cartas.map((c, i) => `
          <div class="memory-card hidden-card" data-val="${c}" data-idx="${i}">
            <span>${c}</span>
          </div>
        `).join('')}
      </div>
    `;
    this.bodyEl.innerHTML = html;

    let primera = null;
    let segunda = null;
    let bloqueado = false;
    let pares = 0;

    const cards = this.bodyEl.querySelectorAll('.memory-card');
    cards.forEach(card => {
      card.addEventListener('click', () => {
        if (bloqueado || card === primera || card.classList.contains('matched')) return;

        card.classList.remove('hidden-card');
        if (window.bioSound) window.bioSound.playClick();

        if (!primera) {
          primera = card;
        } else {
          segunda = card;
          bloqueado = true;

          if (primera.getAttribute('data-val') === segunda.getAttribute('data-val')) {
            primera.classList.add('matched');
            segunda.classList.add('matched');
            primera.style.pointerEvents = 'none';
            segunda.style.pointerEvents = 'none';
            this.sumarPuntos(15);
            pares++;
            primera = null;
            segunda = null;
            bloqueado = false;

            if (pares === seleccionados.length) {
              setTimeout(() => {
                const estrellas = this.seconds < 35 ? 3 : (this.seconds < 55 ? 2 : 1);
                this.mostrarVictoria('¡Memoria fantástica! Encontraste todas las parejas del huerto.', estrellas);
              }, 500);
            }
          } else {
            this.errorFeedback();
            setTimeout(() => {
              primera.classList.add('hidden-card');
              segunda.classList.add('hidden-card');
              primera = null;
              segunda = null;
              bloqueado = false;
            }, 750);
          }
        }
      });
    });
  }

  // 3. ¿Qué necesita una planta para vivir? (Con distractores dinámicos)
  necesidades() {
    this.titleEl.textContent = '¿Qué necesita una planta para vivir?';
    const correctos = [
      { id: 'sol', nombre: 'Luz Solar', ok: true, icon: '☀️' },
      { id: 'agua', nombre: 'Agua Limpia', ok: true, icon: '💧' },
      { id: 'suelo', nombre: 'Tierra Fértil', ok: true, icon: '🪴' },
      { id: 'aire', nombre: 'Aire Puro', ok: true, icon: '💨' }
    ];

    const distractoresPool = [
      { id: 'caramelo', nombre: 'Caramelo', ok: false, icon: '🍬' },
      { id: 'gaseosa', nombre: 'Gaseosa', ok: false, icon: '🥤' },
      { id: 'celular', nombre: 'Celular', ok: false, icon: '📱' },
      { id: 'zapato', nombre: 'Zapatilla', ok: false, icon: '👟' },
      { id: 'hamburguesa', nombre: 'Hamburguesa', ok: false, icon: '🍔' },
      { id: 'pizza', nombre: 'Pizza', ok: false, icon: '🍕' },
      { id: 'patineta', nombre: 'Patineta', ok: false, icon: '🛹' },
      { id: 'pelota', nombre: 'Pelota', ok: false, icon: '⚽' },
      { id: 'radio', nombre: 'Radio', ok: false, icon: '📻' },
      { id: 'reloj', nombre: 'Reloj', ok: false, icon: '⏰' }
    ];

    const distractores = this.shuffle(distractoresPool).slice(0, 5);
    const elementos = this.shuffle([...correctos, ...distractores]);

    let correctas = 0;
    const meta = 4;

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Selecciona los <strong>4 elementos vitales</strong> que toda planta necesita para vivir:
      </p>
      <div class="needs-grid">
        ${elementos.map(e => `
          <button type="button" class="need-btn" data-ok="${e.ok}">
            <span style="font-size:1.8rem;">${e.icon}</span>
            <span>${e.nombre}</span>
          </button>
        `).join('')}
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const btns = this.bodyEl.querySelectorAll('.need-btn');
    btns.forEach(btn => {
      btn.addEventListener('click', () => {
        if (btn.classList.contains('correct') || btn.classList.contains('wrong')) return;
        btn.style.pointerEvents = 'none'; // Bloqueo inmediato anti-spam

        const esCorrecto = btn.getAttribute('data-ok') === 'true';
        if (esCorrecto) {
          btn.classList.add('correct');
          this.sumarPuntos(25);
          correctas++;
          if (correctas === meta) {
            btns.forEach(b => b.style.pointerEvents = 'none');
            setTimeout(() => {
              this.mostrarVictoria('¡Muy bien! Las plantas necesitan Sol, Agua, Tierra y Aire para vivir.', 3);
            }, 500);
          }
        } else {
          btn.classList.add('wrong');
          this.errorFeedback();
        }
      });
    });
  }

  // 4. Secuencia de Germinación (1 al 4)
  secuencia() {
    this.titleEl.textContent = 'Ordena la Germinación (1 al 4)';
    const pasos = [
      { id: 1, orden: 1, texto: '1. La semilla absorbe agua y se hincha en la tierra' },
      { id: 2, orden: 2, texto: '2. Asoma la radícula (raicita) hacia abajo' },
      { id: 3, orden: 3, texto: '3. El tallo crece hacia arriba buscando la luz solar' },
      { id: 4, orden: 4, texto: '4. Se abren las primeras hojas verdes (fotosíntesis)' }
    ];

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Toca los pasos en el <strong>orden correcto</strong> (del 1 al 4):
      </p>
      <div class="seq-grid" id="seqList">
        ${this.shuffle(pasos).map(p => `
          <div class="seq-item" data-orden="${p.orden}">
            <span class="seq-badge">?</span>
            <span style="font-size:0.92rem; font-weight:700;">${p.texto.substring(3)}</span>
          </div>
        `).join('')}
      </div>
    `;
    this.bodyEl.innerHTML = html;

    let siguiente = 1;
    const items = this.bodyEl.querySelectorAll('.seq-item');

    items.forEach(item => {
      item.addEventListener('click', () => {
        if (item.classList.contains('assigned')) return;
        const ord = parseInt(item.getAttribute('data-orden'));

        if (ord === siguiente) {
          item.classList.add('assigned');
          item.style.pointerEvents = 'none'; // Anti-spam
          item.querySelector('.seq-badge').textContent = siguiente;
          this.sumarPuntos(25);
          siguiente++;

          if (siguiente > 4) {
            setTimeout(() => {
              this.mostrarVictoria('¡Increíble! Has ordenado a la perfección el ciclo de la germinación.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          item.style.borderColor = '#ef4444';
          setTimeout(() => item.style.borderColor = '', 450);
        }
      });
    });
  }

  // 5. Clasificación: Cuidamos el Agua (Pool variado)
  accionesAgua() {
    this.titleEl.textContent = 'Clasificación: Cuidamos el Agua';
    const poolBien = [
      'Regar con regadera por las mañanas',
      'Aprovechar el agua de lavar frutas para regar',
      'Usar riego por goteo con botellas recicladas',
      'Cerrar el caño mientras nos enjabonamos las manos',
      'Reparar goteras en las piletas del colegio',
      'Colocar acolchado de paja para retener humedad'
    ];
    const poolMal = [
      'Dejar el caño abierto mientras se juega',
      'Tirar basura a los canales de regadío',
      'Manguerear el patio desperdiciando litros de agua',
      'Jugar carnavales con agua limpia en la escuela',
      'Regar al mediodía bajo el sol quemante',
      'Lavar cosas con agua corriendo sin balde'
    ];

    const acciones = this.shuffle([
      ...this.shuffle(poolBien).slice(0, 3).map((texto, i) => ({ id: `b_${i}`, texto, tipo: 'bien' })),
      ...this.shuffle(poolMal).slice(0, 3).map((texto, i) => ({ id: `m_${i}`, texto, tipo: 'mal' }))
    ]);

    let seleccion = null;
    let clasificadas = 0;

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Toca una acción y luego colócala en su columna correcta:
      </p>
      <div id="poolAcciones" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:12px;">
        ${acciones.map(a => `
          <button type="button" class="part-chip" data-id="${a.id}" data-tipo="${a.tipo}">${a.texto}</button>
        `).join('')}
      </div>
      <div class="water-zones">
        <div class="water-box water-box--good" data-dest="bien">
          <div class="water-box__head">Acciones que CUIDAN el Agua</div>
          <div class="water-box__items" id="boxBien"></div>
        </div>
        <div class="water-box water-box--bad" data-dest="mal">
          <div class="water-box__head">Acciones que PERJUDICAN</div>
          <div class="water-box__items" id="boxMal"></div>
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const chips = this.bodyEl.querySelectorAll('.part-chip');
    const boxes = this.bodyEl.querySelectorAll('.water-box');

    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');
        seleccion = chip;
        if (window.bioSound) window.bioSound.playClick();
      });
    });

    boxes.forEach(box => {
      box.addEventListener('click', () => {
        if (!seleccion) return;
        const dest = box.getAttribute('data-dest');
        const tipoChip = seleccion.getAttribute('data-tipo');

        if (dest === tipoChip) {
          const itemDiv = document.createElement('div');
          itemDiv.style.cssText = 'background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 8px; font-size:0.8rem; font-weight:700; margin-top:4px;';
          itemDiv.textContent = seleccion.textContent;
          box.querySelector('.water-box__items').appendChild(itemDiv);

          seleccion.remove(); // Se elimina de la selección para evitar multi-clicks
          seleccion = null;
          this.sumarPuntos(15);
          clasificadas++;

          if (clasificadas === acciones.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Gran trabajo de guardián ecológico! Conoces bien cómo proteger el agua.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          box.style.borderColor = '#ef4444';
          setTimeout(() => box.style.borderColor = '', 450);
        }
      });
    });
  }

  // ══════════════════════════════════════════════════════════════
  // CICLO IV — DETECTIVES DEL BIOHUERTO (3.° y 4.° Grado)
  // ══════════════════════════════════════════════════════════════

  // 1. Adivina el Cultivo (Pool de 14 casos)
  adivina() {
    this.titleEl.textContent = 'Detective: Adivina el Cultivo';
    const bancoCasos = [
      {
        pistas: ['Crezco bajo tierra en el huerto.', 'Mi color naranja es brillante y crujiente.', 'Tengo betacaroteno que protege tu visión.'],
        opciones: ['Zanahoria', 'Tomate', 'Acelga', 'Lechuga'],
        correcta: 'Zanahoria'
      },
      {
        pistas: ['Tengo un farolito de papel que me abriga.', 'Soy una superfruta andina dorada.', 'Los Incas me llamaban topotopo en el Cusco.'],
        opciones: ['Aguaymanto', 'Fresa', 'Cebolla', 'Rabanito'],
        correcta: 'Aguaymanto'
      },
      {
        pistas: ['Hago soltar lagrimitas al picarme en la cocina.', 'Tengo muchas capas apretadas y aroma fuerte.', 'Soy el corazón del aderezo peruano.'],
        opciones: ['Cebolla', 'Tomate', 'Espinaca', 'Beterraga'],
        correcta: 'Cebolla'
      },
      {
        pistas: ['Soy roja por fuera y blanca por dentro.', 'Tengo sabor refrescante y un toque picante.', '¡Crezco rapidísimo en solo 25 a 30 días!'],
        opciones: ['Rabanito', 'Fresa', 'Zanahoria', 'Lechuga'],
        correcta: 'Rabanito'
      },
      {
        pistas: ['Mis hojas son de un verde oscuro brillante.', 'Fui la comida favorita de Popeye el marino.', 'Tengo mucho hierro para que no te dé anemia.'],
        opciones: ['Espinaca', 'Acelga', 'Lechuga', 'Tomate'],
        correcta: 'Espinaca'
      },
      {
        pistas: ['Soy un fruto carnoso y jugoso de color rojo vivo.', 'Aunque me usan como verdura, botánicamente soy fruta.', 'Tengo mucho licopeno para el corazón.'],
        opciones: ['Tomate', 'Pimiento', 'Fresa', 'Rabanito'],
        correcta: 'Tomate'
      },
      {
        pistas: ['Soy la única fruta que lleva sus semillas por fuera.', 'Tengo unas 200 semillitas diminutas en mi piel.', 'Aporto más vitamina C que una naranja grande.'],
        opciones: ['Fresa', 'Tomate', 'Aguaymanto', 'Zanahoria'],
        correcta: 'Fresa'
      },
      {
        pistas: ['Pinto de morado las manos de quien me cosecha.', 'Crezco como una bola bajo tierra.', 'Sirvo como colorante natural andino y doy energía.'],
        opciones: ['Beterraga', 'Cebolla', 'Rabanito', 'Acelga'],
        correcta: 'Beterraga'
      },
      {
        pistas: ['Vivo en la oscuridad bajo el compost.', 'No tengo ojos pero como tierra y hojas secas.', 'Fabrico el humus más fértil del planeta.'],
        opciones: ['Lombriz', 'Caracol', 'Abeja', 'Mariquita'],
        correcta: 'Lombriz'
      },
      {
        pistas: ['Vuelo de flor en flor en el huerto.', 'Tengo rayas amarillas y negras y zumbido suave.', 'Polinizo las flores para que nazcan frutos.'],
        opciones: ['Abeja', 'Mariposa', 'Hormiga', 'Gusano'],
        correcta: 'Abeja'
      }
    ];

    const casos = this.shuffle(bancoCasos).slice(0, 4);
    let actual = 0;
    let bloqueado = false;

    const renderReto = () => {
      bloqueado = false;
      const r = casos[actual];
      const opcionesMezcladas = this.shuffle(r.opciones);

      let html = `
        <div style="background:#f8fafc; border:2px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <h4 style="font-family:var(--font-heading); color:#0284c7; margin:0;">Caso de Misterio #${actual + 1} de ${casos.length}</h4>
            <span style="font-size:0.75rem; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:999px; font-weight:800;">3 Pistas</span>
          </div>
          <ul style="margin:0; padding-left:20px; font-size:0.92rem; color:var(--color-text); line-height:1.6;">
            ${r.pistas.map(p => `<li>${p}</li>`).join('')}
          </ul>
        </div>
        <p style="font-weight:800; font-size:0.9rem; text-align:center; margin-bottom:8px;">¿De qué cultivo o amigo se trata?</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:8px; width:100%; box-sizing:border-box;" id="adivinaBtns">
          ${opcionesMezcladas.map(opt => `
            <button type="button" class="btn btn--outline btn-opt" data-val="${opt}">${opt}</button>
          `).join('')}
        </div>
      `;
      this.bodyEl.innerHTML = html;

      const btns = this.bodyEl.querySelectorAll('.btn-opt');
      btns.forEach(b => {
        b.addEventListener('click', () => {
          if (bloqueado) return; // Anti-spam click
          bloqueado = true;
          btns.forEach(btn => btn.style.pointerEvents = 'none');

          const val = b.getAttribute('data-val');
          if (val === r.correcta) {
            b.style.background = '#22c55e';
            b.style.color = '#ffffff';
            this.sumarPuntos(25);
            actual++;
            if (actual < casos.length) {
              setTimeout(renderReto, 800);
            } else {
              setTimeout(() => {
                this.mostrarVictoria('¡Todos los misterios resueltos! Eres un detective botánico brillante.', 3);
              }, 500);
            }
          } else {
            this.errorFeedback();
            b.style.background = '#ef4444';
            b.style.color = '#ffffff';
            // Mostrar cuál era la correcta
            btns.forEach(btn => {
              if (btn.getAttribute('data-val') === r.correcta) {
                btn.style.background = '#22c55e';
                btn.style.color = '#ffffff';
              }
            });
            actual++;
            if (actual < casos.length) {
              setTimeout(renderReto, 1300);
            } else {
              setTimeout(() => {
                this.mostrarVictoria(`¡Completaste la ronda de investigación! Puntos acumulados: ${this.score}`, 2);
              }, 600);
            }
          }
        });
      });
    };

    renderReto();
  }

  // 2. Une cada Parte con su Función (Pool variado)
  uneFunciones() {
    this.titleEl.textContent = 'Relaciona: Parte y Función';
    const poolPares = [
      { id: 1, parte: 'Raíz', funcion: 'Absorbe agua y fija la planta al suelo' },
      { id: 2, parte: 'Tallo', funcion: 'Sostiene hojas y transporta savia' },
      { id: 3, parte: 'Hojas', funcion: 'Realizan la fotosíntesis con luz solar' },
      { id: 4, parte: 'Flor', funcion: 'Órgano de reproducción y néctar' },
      { id: 5, parte: 'Fruto', funcion: 'Protege las semillas que darán nueva vida' },
      { id: 6, parte: 'Semilla', funcion: 'Guarda el embrión de una nueva planta' },
      { id: 7, parte: 'Lombriz', funcion: 'Fabrica humus orgánico oxigenando el suelo' },
      { id: 8, parte: 'Compostera', funcion: 'Transforma cáscaras en abono rico' },
      { id: 9, parte: 'Clorofila', funcion: 'Pigmento verde que atrapa la luz del sol' },
      { id: 10, parte: 'Penca (Tallo tierno)', funcion: 'Parte crujiente y carnosa de la acelga' }
    ];

    const seleccionados = this.shuffle(poolPares).slice(0, 5);
    const partesCol = this.shuffle(seleccionados);
    const funcCol = this.shuffle(seleccionados);

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Toca una parte (izquierda) y luego toca su función correspondiente (derecha):
      </p>
      <div class="matching-board">
        <div class="match-col" id="colPartes">
          ${partesCol.map(p => `
            <div class="match-item match-parte" data-id="${p.id}">${p.parte}</div>
          `).join('')}
        </div>
        <div class="match-col" id="colFuncs">
          ${funcCol.map(f => `
            <div class="match-item match-func" data-id="${f.id}">${f.funcion}</div>
          `).join('')}
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    let selParte = null;
    let aciertos = 0;

    const parteEls = this.bodyEl.querySelectorAll('.match-parte');
    const funcEls = this.bodyEl.querySelectorAll('.match-func');

    parteEls.forEach(el => {
      el.addEventListener('click', () => {
        if (el.classList.contains('paired')) return;
        parteEls.forEach(p => p.classList.remove('selected'));
        el.classList.add('selected');
        selParte = el;
        if (window.bioSound) window.bioSound.playClick();
      });
    });

    funcEls.forEach(el => {
      el.addEventListener('click', () => {
        if (!selParte || el.classList.contains('paired')) return;
        const idParte = selParte.getAttribute('data-id');
        const idFunc = el.getAttribute('data-id');

        if (idParte === idFunc) {
          selParte.classList.remove('selected');
          selParte.classList.add('paired');
          selParte.style.pointerEvents = 'none'; // Anti-spam click
          el.classList.add('paired');
          el.style.pointerEvents = 'none';

          this.sumarPuntos(20);
          selParte = null;
          aciertos++;

          if (aciertos === seleccionados.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Conexión perfecta! Comprendes a cabalidad las funciones botánicas.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          el.style.borderColor = '#ef4444';
          setTimeout(() => el.style.borderColor = '', 450);
        }
      });
    });
  }

  // 3. Verdadero o Falso ecológico (Pool de 20 preguntas)
  verdaderoFalso() {
    this.titleEl.textContent = 'Reto: Verdadero o Falso';
    const poolVF = [
      { texto: 'Las plantas absorben dióxido de carbono y liberan oxígeno durante la fotosíntesis.', vf: true, expl: '¡Verdadero! Son los pulmones verdes del colegio.' },
      { texto: 'El mejor momento para regar las plantas es a las 12 del mediodía bajo pleno sol.', vf: false, expl: '¡Falso! El sol intenso evapora el agua y las gotas hacen efecto lupa.' },
      { texto: 'El compost se elabora con cáscaras de frutas, verduras y hojas secas.', vf: true, expl: '¡Verdadero! Son residuos orgánicos que nutren la tierra.' },
      { texto: 'La acelga solo se puede cosechar una sola vez arrancando toda la raíz.', vf: false, expl: '¡Falso! Si cortas solo las hojas exteriores sigue rebrotando.' },
      { texto: 'Las lombrices de tierra son dañinas y se deben sacar del huerto.', vf: false, expl: '¡Falso! Las lombrices son grandes aliadas porque fabrican humus fértil.' },
      { texto: 'La zanahoria almacena sus nutrientes en la raíz engrosada.', vf: true, expl: '¡Verdadero! La parte naranja crujiente es su raíz principal.' },
      { texto: 'Los pesticidas químicos sintéticos son obligatorios para que crezca un huerto.', vf: false, expl: '¡Falso! En el biohuerto usamos abonos naturales sin tóxicos.' },
      { texto: 'El tomate es botánicamente una fruta porque nace de una flor fecundada.', vf: true, expl: '¡Verdadero! Aunque en cocina la usemos en ensaladas, es fruto.' },
      { texto: 'El aguaymanto es originario de los Andes del Perú.', vf: true, expl: '¡Verdadero! Los Incas lo cultivaban en el Valle Sagrado.' },
      { texto: 'Las semillas no necesitan agua para poder germinar.', vf: false, expl: '¡Falso! El agua ablanda la cáscara e inicia la germinación.' },
      { texto: 'La rotación de cultivos evita que las plagas se queden en el mismo bancal.', vf: true, expl: '¡Verdadero! Cambiar de cultivo cuida los nutrientes del suelo.' },
      { texto: 'El riego por goteo gasta más agua que usar una manguera abierta.', vf: false, expl: '¡Falso! El riego por goteo ahorra hasta un 60% de agua.' }
    ];

    const preguntas = this.shuffle(poolVF).slice(0, 5);
    let actual = 0;
    let bloqueado = false;

    const render = () => {
      bloqueado = false;
      const p = preguntas[actual];
      let html = `
        <div class="vf-card">
          <div style="font-size:0.8rem; font-weight:800; color:#3b82f6; margin-bottom:8px;">PREGUNTA ${actual + 1} DE ${preguntas.length}</div>
          <div class="vf-statement">"${p.texto}"</div>
          <div class="vf-actions">
            <button type="button" class="btn-vf btn-vf--v" data-ans="true">
              <span>✓ VERDADERO</span>
            </button>
            <button type="button" class="btn-vf btn-vf--f" data-ans="false">
              <span>✗ FALSO</span>
            </button>
          </div>
          <div id="vfFeedback" style="margin-top:12px; font-size:0.9rem; font-weight:700; min-height:24px;"></div>
        </div>
      `;
      this.bodyEl.innerHTML = html;

      const btns = this.bodyEl.querySelectorAll('.btn-vf');
      btns.forEach(b => {
        b.addEventListener('click', () => {
          if (bloqueado) return; // Anti-spam click
          bloqueado = true;
          btns.forEach(btn => btn.style.pointerEvents = 'none');

          const ans = b.getAttribute('data-ans') === 'true';
          const feedback = document.getElementById('vfFeedback');

          if (ans === p.vf) {
            feedback.style.color = '#15803d';
            feedback.textContent = p.expl;
            this.sumarPuntos(20);
            actual++;
            if (actual < preguntas.length) {
              setTimeout(render, 1100);
            } else {
              setTimeout(() => {
                this.mostrarVictoria('¡Excelente puntuación! Sabes mucho sobre ecología y biohuerto.', 3);
              }, 600);
            }
          } else {
            this.errorFeedback();
            feedback.style.color = '#b91c1c';
            feedback.textContent = '¡Incorrecto! ' + p.expl;
            actual++;
            if (actual < preguntas.length) {
              setTimeout(render, 1400);
            } else {
              setTimeout(() => {
                this.mostrarVictoria(`¡Fin de la trivia! Puntos acumulados: ${this.score}`, 2);
              }, 600);
            }
          }
        });
      });
    };

    render();
  }

  // 4. Clasifica según parte comestible (Pool de 16 cultivos)
  comestibles() {
    this.titleEl.textContent = 'Clasifica por Parte Comestible';
    const pool = [
      { nombre: 'Zanahoria', cat: 'raiz' },
      { nombre: 'Rabanito', cat: 'raiz' },
      { nombre: 'Beterraga', cat: 'raiz' },
      { nombre: 'Nabo', cat: 'raiz' },
      { nombre: 'Lechuga', cat: 'hoja' },
      { nombre: 'Acelga', cat: 'hoja' },
      { nombre: 'Espinaca', cat: 'hoja' },
      { nombre: 'Col verde', cat: 'hoja' },
      { nombre: 'Tomate', cat: 'fruto' },
      { nombre: 'Fresa', cat: 'fruto' },
      { nombre: 'Aguaymanto', cat: 'fruto' },
      { nombre: 'Pimiento', cat: 'fruto' },
      { nombre: 'Cebolla', cat: 'bulbo' },
      { nombre: 'Ajo', cat: 'bulbo' },
      { nombre: 'Poro', cat: 'bulbo' },
      { nombre: 'Cebollín', cat: 'bulbo' }
    ];

    const lista = this.shuffle(pool).slice(0, 8);
    let seleccion = null;
    let aciertos = 0;

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Selecciona un cultivo y colócalo en su canasta correspondiente:
      </p>
      <div id="poolCultivos" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:12px;">
        ${lista.map(c => `
          <button type="button" class="part-chip" data-cat="${c.cat}">${c.nombre}</button>
        `).join('')}
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; width:100%; box-sizing:border-box;">
        <div class="water-box" data-cat="hoja" style="border-color:#10b981;">
          <div class="water-box__head" style="color:#059669;">🍃 HOJAS</div>
          <div class="box-items"></div>
        </div>
        <div class="water-box" data-cat="raiz" style="border-color:#ea580c;">
          <div class="water-box__head" style="color:#c2410c;">🥕 RAÍCES</div>
          <div class="box-items"></div>
        </div>
        <div class="water-box" data-cat="fruto" style="border-color:#ef4444;">
          <div class="water-box__head" style="color:#dc2626;">🍎 FRUTOS</div>
          <div class="box-items"></div>
        </div>
        <div class="water-box" data-cat="bulbo" style="border-color:#8b5cf6;">
          <div class="water-box__head" style="color:#7c3aed;">🧅 BULBOS</div>
          <div class="box-items"></div>
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const chips = this.bodyEl.querySelectorAll('.part-chip');
    const boxes = this.bodyEl.querySelectorAll('.water-box');

    chips.forEach(c => {
      c.addEventListener('click', () => {
        chips.forEach(chip => chip.classList.remove('selected'));
        c.classList.add('selected');
        seleccion = c;
        if (window.bioSound) window.bioSound.playClick();
      });
    });

    boxes.forEach(b => {
      b.addEventListener('click', () => {
        if (!seleccion) return;
        const catBox = b.getAttribute('data-cat');
        const catChip = seleccion.getAttribute('data-cat');

        if (catBox === catChip) {
          const item = document.createElement('div');
          item.style.cssText = 'background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; padding:4px 8px; font-size:0.8rem; font-weight:700; margin-top:4px;';
          item.textContent = seleccion.textContent;
          b.querySelector('.box-items').appendChild(item);

          seleccion.remove(); // Se quita para evitar re-click
          seleccion = null;
          this.sumarPuntos(15);
          aciertos++;

          if (aciertos === lista.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Canastas completas! Sabes diferenciar qué parte de cada planta comemos.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          b.style.borderColor = '#ef4444';
          setTimeout(() => b.style.borderColor = '', 450);
        }
      });
    });
  }

  // 5. ¡Ayuda a Gotita! (Pool de 8 misiones)
  gotita() {
    this.titleEl.textContent = 'Misión: ¡Ayuda a Gotita!';
    const poolDilemas = [
      {
        situacion: 'Ves que un caño del colegio gotea sin parar después del recreo. ¿Qué haces?',
        opciones: [
          { texto: 'Cerrarlo bien y avisar al profesor si sigue fallando', ok: true },
          { texto: 'Ignorarlo e irme a jugar', ok: false },
          { texto: 'Jugar a mojar a mis compañeros', ok: false }
        ]
      },
      {
        situacion: 'Toca regar las lechugas del bancal escolar. ¿Cuál es el método más eficiente?',
        opciones: [
          { texto: 'Regar con regadera suave temprano en la mañana', ok: true },
          { texto: 'Inundar con manguera a presión al mediodía', ok: false },
          { texto: 'No regarlas nunca para no gastar agua', ok: false }
        ]
      },
      {
        situacion: 'En el comedor escolar sobró agua limpia de lavar las verduras. ¿Qué hacemos?',
        opciones: [
          { texto: 'Recolectarla en un balde para regar las plantas del huerto', ok: true },
          { texto: 'Tirarla directo al desagüe', ok: false },
          { texto: 'Echarle detergente y votarla a la tierra', ok: false }
        ]
      },
      {
        situacion: 'El pronóstico del clima dice que va a llover copiosamente en Puno. ¿Qué podemos hacer?',
        opciones: [
          { texto: 'Colocar canaletas y baldes limpios para cosechar agua de lluvia', ok: true },
          { texto: 'Regar igual con manguera antes de la lluvia', ok: false },
          { texto: 'Tapar las plantas con plástico para que no se mojen', ok: false }
        ]
      },
      {
        situacion: 'La tierra de los bancales se seca muy rápido por el viento de Jayllihuaya. ¿Cómo evitamos la evaporación?',
        opciones: [
          { texto: 'Colocar una capa de paja seca o mulch sobre la tierra', ok: true },
          { texto: 'Echar piedras calientes sobre las raíces', ok: false },
          { texto: 'Regar cada 15 minutos todo el día', ok: false }
        ]
      }
    ];

    const dilemas = this.shuffle(poolDilemas).slice(0, 3);
    let actual = 0;
    let bloqueado = false;

    const render = () => {
      bloqueado = false;
      const d = dilemas[actual];
      const opcionesShuffled = this.shuffle(d.opciones);

      let html = `
        <div style="background:#e0f2fe; border:2px solid #38bdf8; border-radius:12px; padding:16px; margin-bottom:12px; text-align:center;">
          <div style="font-size:2.4rem;">💧</div>
          <h4 style="font-family:var(--font-heading); color:#0369a1; margin:6px 0;">Misión de Gotita #${actual + 1} de ${dilemas.length}</h4>
          <p style="font-size:0.95rem; color:#0f172a; font-weight:700; margin:0;">"${d.situacion}"</p>
        </div>
        <div style="display:flex; flex-direction:column; gap:8px;">
          ${opcionesShuffled.map(opt => `
            <button type="button" class="btn btn--outline btn-gotita" data-ok="${opt.ok}" style="text-align:left; padding:12px 14px; font-weight:700; font-size:0.9rem;">
              ${opt.texto}
            </button>
          `).join('')}
        </div>
      `;
      this.bodyEl.innerHTML = html;

      const btns = this.bodyEl.querySelectorAll('.btn-gotita');
      btns.forEach(b => {
        b.addEventListener('click', () => {
          if (bloqueado) return; // Anti-spam click
          bloqueado = true;
          btns.forEach(btn => btn.style.pointerEvents = 'none');

          const esOk = b.getAttribute('data-ok') === 'true';
          if (esOk) {
            b.style.background = '#22c55e';
            b.style.color = '#ffffff';
            this.sumarPuntos(30);
            actual++;
            if (actual < dilemas.length) {
              setTimeout(render, 800);
            } else {
              setTimeout(() => {
                this.mostrarVictoria('¡Gotita está salvada y feliz! Eres un auténtico embajador del agua en Jayllihuaya.', 3);
              }, 500);
            }
          } else {
            this.errorFeedback();
            b.style.background = '#fee2e2';
            b.style.color = '#b91c1c';
            actual++;
            if (actual < dilemas.length) {
              setTimeout(render, 1200);
            } else {
              setTimeout(() => {
                this.mostrarVictoria(`¡Misiones completadas! Puntos acumulados: ${this.score}`, 2);
              }, 600);
            }
          }
        });
      });
    };

    render();
  }

  // ══════════════════════════════════════════════════════════════
  // CICLO V — MAESTROS DEL BIOHUERTO (5.° y 6.° Grado)
  // Gran variedad, generador robusto de sopa y crucigramas temáticos
  // ══════════════════════════════════════════════════════════════

  // 1. Sopa de Letras Interactiva con Algoritmo de Primera y Última Letra
  sopa() {
    this.titleEl.textContent = 'Sopa de Letras Botánica';

    // Banco de más de 40 palabras botánicas y agroecológicas
    const bancoPalabras = [
      'CLOROFILA', 'COMPOST', 'SEMILLA', 'RIEGO', 'FOTOSINTESIS',
      'POLEN', 'LOMBRICES', 'NUTRIENTES', 'GERMINAR', 'COSECHA',
      'BANCAL', 'SUSTRATO', 'ALMACIGO', 'TRASPLANTE', 'ESTOLON',
      'POTASIO', 'NITROGENO', 'FOSFORO', 'HUMUS', 'OXIGENO',
      'ESTOMAS', 'JAYLLIHUAYA', 'TITICACA', 'BIOHUERTO', 'PODAR',
      'ORGANICO', 'ECOLOGIA', 'FLORES', 'RAICES', 'HIERBA'
    ];

    // Seleccionar 5 palabras aleatorias que quepan en la matriz 10x10 (máx 10 letras)
    const candidatas = this.shuffle(bancoPalabras.filter(w => w.length <= 10)).slice(0, 5);

    const TAM = 10;
    const DIRECCIONES = [
      [0, 1],   // Derecha
      [1, 0],   // Abajo
      [1, 1],   // Diagonal abajo-der
      [-1, 1],  // Diagonal arriba-der
      [0, -1],  // Izquierda
      [-1, 0]   // Arriba
    ];

    // Función para intentar colocar palabras
    const construirTablero = (palabras) => {
      const grid = Array.from({ length: TAM }, () => Array(TAM).fill(''));
      const colocadas = [];

      for (const p of palabras) {
        let colocada = false;
        for (let intento = 0; intento < 300 && !colocada; intento++) {
          const [df, dc] = DIRECCIONES[Math.floor(Math.random() * DIRECCIONES.length)];
          const f = Math.floor(Math.random() * TAM);
          const c = Math.floor(Math.random() * TAM);

          // Verificar si cabe
          let cabe = true;
          for (let i = 0; i < p.length; i++) {
            const nf = f + df * i;
            const nc = c + dc * i;
            if (nf < 0 || nf >= TAM || nc < 0 || nc >= TAM) { cabe = false; break; }
            if (grid[nf][nc] !== '' && grid[nf][nc] !== p[i]) { cabe = false; break; }
          }

          if (cabe) {
            for (let i = 0; i < p.length; i++) {
              grid[f + df * i][c + dc * i] = p[i];
            }
            colocada = true;
            colocadas.push(p);
          }
        }
      }

      // Rellenar espacios vacíos
      const abc = 'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';
      for (let f = 0; f < TAM; f++) {
        for (let c = 0; c < TAM; c++) {
          if (!grid[f][c]) {
            grid[f][c] = abc[Math.floor(Math.random() * abc.length)];
          }
        }
      }

      return { grid, colocadas };
    };

    // Intentar armar tablero hasta que al menos 4-5 palabras entren
    let resTablero = construirTablero(candidatas);
    for (let intento = 0; intento < 10 && resTablero.colocadas.length < 4; intento++) {
      resTablero = construirTablero(candidatas);
    }

    const grid = resTablero.grid;
    const palabrasMeta = resTablero.colocadas;
    const encontradas = [];
    let celdaInicio = null;
    let seleccionTemporal = [];

    let html = `
      <div class="sopa-container">
        <p style="font-size:0.86rem; color:var(--color-text-muted); margin:0; text-align:center;">
          Toca la <strong>primera letra</strong> y luego la <strong>última letra</strong> de cada palabra:
        </p>

        <!-- Cuadrícula 10x10 -->
        <div class="sopa-grid-10" id="sopaGridTablero">
          ${grid.map((fila, f) => fila.map((letra, c) => `
            <button type="button" class="sopa-cell-btn" data-f="${f}" data-c="${c}">
              ${letra}
            </button>
          `).join('')).join('')}
        </div>

        <!-- Lista de palabras a buscar -->
        <div class="sopa-words-pills" id="sopaPills">
          ${palabrasMeta.map(w => `
            <span class="sopa-word-pill" data-word="${w}">
              <span>🔍</span> ${w}
            </span>
          `).join('')}
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const botonesCeldas = this.bodyEl.querySelectorAll('.sopa-cell-btn');
    const getCelda = (f, c) => this.bodyEl.querySelector(`.sopa-cell-btn[data-f="${f}"][data-c="${c}"]`);

    const limpiarSeleccion = () => {
      seleccionTemporal.forEach(cell => {
        if (!cell.classList.contains('found')) {
          cell.classList.remove('selected', 'line-preview', 'wrong');
        }
      });
      seleccionTemporal = [];
      celdaInicio = null;
    };

    // Click en celdas (Algoritmo Primera y Última letra)
    botonesCeldas.forEach(btn => {
      btn.addEventListener('click', () => {
        const f = parseInt(btn.getAttribute('data-f'));
        const c = parseInt(btn.getAttribute('data-c'));

        // Si es el primer click
        if (!celdaInicio) {
          limpiarSeleccion();
          celdaInicio = { f, c };
          btn.classList.add('selected');
          seleccionTemporal = [btn];
          if (window.bioSound) window.bioSound.playClick();
          return;
        }

        // Si vuelve a tocar la misma celda, cancela
        if (celdaInicio.f === f && celdaInicio.c === c) {
          limpiarSeleccion();
          return;
        }

        // Calcular dirección
        const df = f - celdaInicio.f;
        const dc = c - celdaInicio.c;
        const esHorizontal = (df === 0 && dc !== 0);
        const esVertical = (dc === 0 && df !== 0);
        const esDiagonal = (Math.abs(df) === Math.abs(dc));

        if (!esHorizontal && !esVertical && !esDiagonal) {
          // No es línea recta -> Nueva celda de inicio
          limpiarSeleccion();
          celdaInicio = { f, c };
          btn.classList.add('selected');
          seleccionTemporal = [btn];
          if (window.bioSound) window.bioSound.playClick();
          return;
        }

        // Trazar línea completa entre inicio y fin
        const pasoF = Math.sign(df);
        const pasoC = Math.sign(dc);
        const distancia = Math.max(Math.abs(df), Math.abs(dc)) + 1;
        const celdasLinea = [];

        for (let i = 0; i < distancia; i++) {
          const currF = celdaInicio.f + pasoF * i;
          const currC = celdaInicio.c + pasoC * i;
          const cEl = getCelda(currF, currC);
          if (cEl) celdasLinea.push(cEl);
        }

        const texto = celdasLinea.map(el => el.textContent.trim()).join('');
        const inverso = [...texto].reverse().join('');

        // Verificar si coincide con alguna palabra no encontrada
        const palabraHallada = palabrasMeta.find(p => (p === texto || p === inverso) && !encontradas.includes(p));

        if (palabraHallada) {
          // ¡Acierto!
          celdasLinea.forEach(el => {
            el.classList.remove('selected', 'line-preview');
            el.classList.add('found');
          });

          encontradas.push(palabraHallada);
          const pill = this.bodyEl.querySelector(`.sopa-word-pill[data-word="${palabraHallada}"]`);
          if (pill) {
            pill.classList.add('found');
            pill.innerHTML = `<span>✓</span> ${palabraHallada}`;
          }

          this.sumarPuntos(30);
          limpiarSeleccion();

          if (encontradas.length === palabrasMeta.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Extraordinario! Encontraste todas las palabras en la sopa botánica.', 3);
            }, 600);
          }
        } else {
          // Fallo: parpadeo rojo breve
          this.errorFeedback();
          celdasLinea.forEach(el => el.classList.add('wrong'));
          setTimeout(() => {
            celdasLinea.forEach(el => el.classList.remove('wrong'));
            limpiarSeleccion();
          }, 450);
        }
      });
    });
  }

  // 2. Crucigrama Didáctico con Múltiples Tableros Temáticos
  crucigrama() {
    this.titleEl.textContent = 'Crucigrama Botánico y Agrícola';

    // Banco de 4 Crucigramas Diferentes
    const crucis = [
      {
        nombreTema: 'Herramientas y Agua',
        cols: 5,
        grid: [
          // R A I Z (1H), A G U A (2V)
          [{ n: 1, s: 'R' }, { n: 2, s: 'A' }, { s: 'I' }, { s: 'Z' }, { b: 1 }],
          [{ b: 1 }, { s: 'G' }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ b: 1 }, { s: 'U' }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ b: 1 }, { s: 'A' }, { b: 1 }, { b: 1 }, { b: 1 }]
        ],
        pistas: [
          '<strong>1 Horizontal (4 letras):</strong> Órgano subterráneo que absorbe agua y nutrientes.',
          '<strong>2 Vertical (4 letras):</strong> Líquido vital transparente que riega el huerto.'
        ]
      },
      {
        nombreTema: 'La Fábrica Verde',
        cols: 5,
        grid: [
          // H O J A (1H), O L O R (2V)
          [{ n: 1, s: 'H' }, { n: 2, s: 'O' }, { s: 'J' }, { s: 'A' }, { b: 1 }],
          [{ b: 1 }, { s: 'L' }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ b: 1 }, { s: 'O' }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ b: 1 }, { s: 'R' }, { b: 1 }, { b: 1 }, { b: 1 }]
        ],
        pistas: [
          '<strong>1 Horizontal (4 letras):</strong> Órgano verde donde ocurre la fotosíntesis.',
          '<strong>2 Vertical (4 letras):</strong> Fragancia aromática de las flores del biohuerto.'
        ]
      },
      {
        nombreTema: 'El Suelo y el Sol',
        cols: 5,
        grid: [
          // S O L (1H), S U E L O (1V)
          [{ n: 1, s: 'S' }, { s: 'O' }, { s: 'L' }, { b: 1 }, { b: 1 }],
          [{ s: 'U' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ s: 'E' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ s: 'L' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ s: 'O' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }]
        ],
        pistas: [
          '<strong>1 Horizontal (3 letras):</strong> Estrella que brinda calor y energía lumínica.',
          '<strong>1 Vertical (5 letras):</strong> Capa fértil de tierra donde crecen las raíces.'
        ]
      },
      {
        nombreTema: 'Cultivo y Cuidado',
        cols: 5,
        grid: [
          // P O D A (1H), P A L A (1V)
          [{ n: 1, s: 'P' }, { s: 'O' }, { s: 'D' }, { s: 'A' }, { b: 1 }],
          [{ s: 'A' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ s: 'L' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }],
          [{ s: 'A' }, { b: 1 }, { b: 1 }, { b: 1 }, { b: 1 }]
        ],
        pistas: [
          '<strong>1 Horizontal (4 letras):</strong> Corte de ramitas secas para estimular la planta.',
          '<strong>1 Vertical (4 letras):</strong> Herramienta manual para remover la tierra del bancal.'
        ]
      }
    ];

    const crucigramaElegido = this.shuffle(crucis)[0];
    let yaGanado = false;

    let html = `
      <div style="text-align:center; margin-bottom:8px;">
        <span style="font-size:0.75rem; background:#ede9fe; color:#6d28d9; padding:2px 10px; border-radius:999px; font-weight:800;">
          Tema: ${crucigramaElegido.nombreTema}
        </span>
      </div>
      <p style="font-size:0.86rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Escribe las letras en las casillas correspondientes:
      </p>

      <div style="display:flex; justify-content:center; margin-bottom:12px;">
        <div class="crossword-grid" style="grid-template-columns:repeat(${crucigramaElegido.cols}, clamp(26px, 7vw, 36px));">
          ${crucigramaElegido.grid.map(fila => fila.map(celda => {
            if (celda.b) {
              return `<div class="cw-cell black"></div>`;
            }
            return `
              <div class="cw-cell">
                ${celda.n ? `<span class="cw-num">${celda.n}</span>` : ''}
                <input class="cw-input" maxlength="1" data-sol="${celda.s}" autocomplete="off">
              </div>
            `;
          }).join('')).join('')}
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; font-size:0.85rem; line-height:1.5;">
        ${crucigramaElegido.pistas.map(p => `<div>${p}</div>`).join('')}
      </div>

      <div style="text-align:center; margin-top:12px;">
        <button type="button" class="btn btn--primary" id="btnComprobarCW">
          <span>Comprobar Crucigrama</span>
        </button>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    const inputs = this.bodyEl.querySelectorAll('.cw-input');
    inputs.forEach((inp, idx) => {
      inp.addEventListener('input', () => {
        inp.value = inp.value.toUpperCase();
        if (inp.value && idx < inputs.length - 1) {
          inputs[idx + 1].focus();
        }
      });
    });

    const btnComprobar = document.getElementById('btnComprobarCW');
    btnComprobar.onclick = () => {
      if (yaGanado) return; // Anti-spam click

      let correctos = 0;
      inputs.forEach(inp => {
        const val = inp.value.trim().toUpperCase();
        const sol = inp.getAttribute('data-sol');
        if (val === sol) {
          inp.style.background = '#dcfce7';
          correctos++;
        } else {
          inp.style.background = '#fee2e2';
        }
      });

      if (correctos === inputs.length) {
        yaGanado = true;
        btnComprobar.style.pointerEvents = 'none';
        inputs.forEach(i => i.disabled = true);
        this.sumarPuntos(100);
        setTimeout(() => {
          this.mostrarVictoria('¡Crucigrama resuelto con éxito! Gran vocabulario agronómico.', 3);
        }, 500);
      } else {
        this.errorFeedback();
      }
    };
  }

  // 3. Emparejamiento Avanzado: Procesos y Nutrición (Pool de 16 pares científicos)
  procesos() {
    this.titleEl.textContent = 'Emparejamiento: Procesos y Nutrición';
    const poolCientifico = [
      { id: 1, concepto: 'Fotosíntesis', desc: 'Fabricación de glucosa y oxígeno a partir de luz solar y CO₂' },
      { id: 2, concepto: 'Clorofila', desc: 'Pigmento verde que atrapa la radiación lumínica en hojas' },
      { id: 3, concepto: 'Compostaje', desc: 'Descomposición aeróbica de residuos orgánicos por microorganismos' },
      { id: 4, concepto: 'Polinización', desc: 'Transferencia de polen de anteras a estigmas por abejas o viento' },
      { id: 5, concepto: 'Lombricultura', desc: 'Crianza de lombrices que transforman materia en humus fértil' },
      { id: 6, concepto: 'Transpiración', desc: 'Evaporación de agua a través de los estomas microscópicos' },
      { id: 7, concepto: 'Nitrógeno (N)', desc: 'Macronutriente vital para el follaje verde y crecimiento vigoroso' },
      { id: 8, concepto: 'Fósforo (P)', desc: 'Mineral clave para el desarrollo fuerte de las raíces y floración' },
      { id: 9, concepto: 'Potasio (K)', desc: 'Nutriente que regula el agua en las células y resiste heladas' },
      { id: 10, concepto: 'Savia Bruta', desc: 'Mezcla de agua y sales absorbidas que sube por el xilema' },
      { id: 11, concepto: 'Savia Elaborada', desc: 'Solución nutritiva rica en azúcares que desciende por el floema' },
      { id: 12, concepto: 'Rotación de Cultivos', desc: 'Alternar diferentes familias de plantas para no agotar el suelo' },
      { id: 13, concepto: 'Acolchado (Mulch)', desc: 'Capa protectora de paja que evita la evaporación del agua' },
      { id: 14, concepto: 'Estomas', desc: 'Poros microscópicos en hojas para intercambio gaseoso' }
    ];

    const seleccionados = this.shuffle(poolCientifico).slice(0, 4);
    const conceptosCol = this.shuffle(seleccionados);
    const descsCol = this.shuffle(seleccionados);

    let html = `
      <p style="font-size:0.9rem; color:var(--color-text-muted); margin:0 0 10px; text-align:center;">
        Toca un concepto (izquierda) y luego toca su definición científica (derecha):
      </p>
      <div class="matching-board">
        <div class="match-col" id="colConceptos">
          ${conceptosCol.map(c => `
            <div class="match-item match-c" data-id="${c.id}">${c.concepto}</div>
          `).join('')}
        </div>
        <div class="match-col" id="colDescs">
          ${descsCol.map(d => `
            <div class="match-item match-d" data-id="${d.id}" style="font-size:0.8rem; line-height:1.4;">${d.desc}</div>
          `).join('')}
        </div>
      </div>
    `;
    this.bodyEl.innerHTML = html;

    let sel = null;
    let aciertos = 0;

    const cEls = this.bodyEl.querySelectorAll('.match-c');
    const dEls = this.bodyEl.querySelectorAll('.match-d');

    cEls.forEach(c => {
      c.addEventListener('click', () => {
        if (c.classList.contains('paired')) return;
        cEls.forEach(x => x.classList.remove('selected'));
        c.classList.add('selected');
        sel = c;
        if (window.bioSound) window.bioSound.playClick();
      });
    });

    dEls.forEach(d => {
      d.addEventListener('click', () => {
        if (!sel || d.classList.contains('paired')) return;
        if (sel.getAttribute('data-id') === d.getAttribute('data-id')) {
          sel.classList.remove('selected');
          sel.classList.add('paired');
          sel.style.pointerEvents = 'none'; // Anti-spam click
          d.classList.add('paired');
          d.style.pointerEvents = 'none';

          this.sumarPuntos(30);
          sel = null;
          aciertos++;

          if (aciertos === seleccionados.length) {
            setTimeout(() => {
              this.mostrarVictoria('¡Nivel Maestro alcanzado! Dominas los procesos bioquímicos y ecológicos del biohuerto.', 3);
            }, 500);
          }
        } else {
          this.errorFeedback();
          d.style.borderColor = '#ef4444';
          setTimeout(() => d.style.borderColor = '', 450);
        }
      });
    });
  }
}

// Instancia global
window.gameManager = new GameManager();
