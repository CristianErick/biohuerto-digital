/**
 * SEMILLITAS DEL FUTURO — JavaScript Principal
 * Vanilla ES6+ · Fetch nativo · Sin Emojis
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
  // ─── Navegación Móvil ──────────────────────────────────
  const navToggle = document.getElementById('navToggle') || document.querySelector('.nav__toggle');
  const navMenu   = document.getElementById('navMenu')   || document.querySelector('.nav__menu');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
      const isOpen = navMenu.classList.toggle('active');
      navToggle.setAttribute('aria-expanded', isOpen);
    });

    // Cerrar menú al hacer clic en un enlace (mobile)
    navMenu.querySelectorAll('.nav__link').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ─── Animación suave de entrada para tarjetas ──────────
  const cards = document.querySelectorAll('.card, .planta-card-mobile, .material-card');

  if ('IntersectionObserver' in window && cards.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08 }
    );

    cards.forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(16px)';
      card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      observer.observe(card);
    });
  }

  // ─── Cierre de Alertas Interactivas ────────────────────
  document.querySelectorAll('.alert-banner__close').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const banner = e.target.closest('.alert-banner');
      if (banner) {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-8px)';
        banner.style.transition = 'opacity 0.25s, transform 0.25s';
        setTimeout(() => banner.remove(), 260);
      }
    });
  });

  // ─── Marcar enlace activo en el nav ────────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav__link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && (href === currentPath || (currentPath.includes(href) && href !== '../index.php' && href !== 'index.php'))) {
      link.classList.add('nav__link--active');
    }
  });

  // ─── Escáner QR con Cámara Automática ──────────────────
  // La cámara ahora se muestra dentro de un recuadro de apuntado y
  // decodifica el código QR (BarcodeDetector nativo o jsQR de respaldo),
  // abriendo directamente la ficha de la planta detectada.
  const btnEscanearQR = document.getElementById('btnEscanearQR');
  const modalQR = document.getElementById('modalQR');

  let escaneoActivo = false;
  let escaneoStream = null;
  let escaneoLoop = null;
  let escaneoObserver = null;
  let escaneoDetectado = false;
  let escaneoTextoPrevio = null;
  let escaneoJsQR = null;

  if (btnEscanearQR && modalQR) {
    btnEscanearQR.addEventListener('click', async () => {
      modalQR.classList.add('active');
      await iniciarCamaraQR();
    });
  }

  function detenerEscaneoQR() {
    if (escaneoLoop) { clearTimeout(escaneoLoop); escaneoLoop = null; }
    if (escaneoStream) {
      escaneoStream.getTracks().forEach(track => track.stop());
      escaneoStream = null;
    }
    if (escaneoObserver) { escaneoObserver.disconnect(); escaneoObserver = null; }
    const visor = modalQR && modalQR.querySelector('.qr-scan-visor');
    if (visor && visor.parentNode) visor.parentNode.removeChild(visor);
    escaneoActivo = false;
    escaneoDetectado = false;
    escaneoTextoPrevio = null;
  }

  function cargarJsQR() {
    if (window.jsQR) return Promise.resolve(window.jsQR);
    if (!escaneoJsQR) {
      escaneoJsQR = new Promise((ok, mal) => {
        const guion = document.createElement('script');
        guion.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
        guion.onload = () => (window.jsQR ? ok(window.jsQR) : mal(new Error('jsQR')));
        guion.onerror = () => mal(new Error('jsQR'));
        document.head.appendChild(guion);
      });
    }
    return escaneoJsQR;
  }

  // Convierte el texto leído en la URL de la ficha de la planta
  function destinoDesdeQR(valor) {
    if (/^https?:\/\//i.test(valor)) return valor;
    if (/^\d+$/.test(valor)) return 'planta.php?id=' + valor;
    if (/^[a-z][a-z0-9+.-]*:/i.test(valor)) return null;
    if (/^[/?#]/.test(valor)) return valor;
    if (/^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}([/?#]|$)/i.test(valor)) return 'https://' + valor;
    return null;
  }

  function alEscanearQR(destino) {
    escaneoDetectado = true;
    if (navigator.vibrate) { try { navigator.vibrate(120); } catch (e) {} }
    showToast('¡Código QR leído! Abriendo ficha…', 'success');
    setTimeout(() => {
      detenerEscaneoQR();
      if (modalQR) modalQR.classList.remove('active');
      window.location.assign(destino);
    }, 300);
  }

  async function iniciarCamaraQR() {
    if (escaneoActivo) return;
    escaneoActivo = true;
    escaneoDetectado = false;

    const modalSheet = modalQR && modalQR.querySelector('.modal-sheet');
    if (!modalSheet || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      escaneoActivo = false;
      showToast('Tu navegador no permite usar la cámara para escanear.', 'danger');
      return;
    }

    // ── Visor con recuadro de apuntado ──
    const visor = document.createElement('div');
    visor.className = 'qr-scan-visor';
    visor.style.cssText = 'position:relative;width:min(100%,300px,38vh);aspect-ratio:1/1;background:#0f172a;border-radius:12px;overflow:hidden;margin:0 auto 12px;';

    const video = document.createElement('video');
    video.setAttribute('playsinline', '');
    video.muted = true;
    video.autoplay = true;
    video.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';

    const marco = document.createElement('div');
    marco.style.cssText = 'position:absolute;top:12%;right:12%;bottom:12%;left:12%;border-radius:12px;box-shadow:0 0 0 9999px rgba(15,23,42,0.55);pointer-events:none;';

    [['top', 'left'], ['top', 'right'], ['bottom', 'left'], ['bottom', 'right']].forEach(pos => {
      const v = pos[0];
      const h = pos[1];
      const borde = '4px solid var(--color-primary, #10b981)';
      const esquina = document.createElement('div');
      esquina.style.cssText = 'position:absolute;width:24px;height:24px;pointer-events:none;' +
        v + ':4px;' + h + ':4px;' +
        (v === 'top' ? 'border-top:' + borde + ';' : 'border-bottom:' + borde + ';') +
        (h === 'left' ? 'border-left:' + borde + ';' : 'border-right:' + borde + ';') +
        'border-' + v + '-' + h + '-radius:8px;';
      marco.appendChild(esquina);
    });

    visor.appendChild(video);
    visor.appendChild(marco);
    modalSheet.insertBefore(visor, modalSheet.firstElementChild);

    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: 'environment' } },
        audio: false
      });
      escaneoStream = stream;
      video.srcObject = stream;
      try { await video.play(); } catch (e) {}

      // Detener cámara al cerrar modal
      escaneoObserver = new MutationObserver(() => {
        if (!modalQR.classList.contains('active')) detenerEscaneoQR();
      });
      escaneoObserver.observe(modalQR, { attributes: true, attributeFilter: ['class'] });

      // Motor de decodificación: nativo (Android/Chrome) o jsQR de respaldo
      let detector = null;
      if (window.BarcodeDetector) {
        try {
          let qrSoportado = true;
          if (BarcodeDetector.getSupportedFormats) {
            const formatos = await BarcodeDetector.getSupportedFormats();
            qrSoportado = formatos.includes('qr_code');
          }
          if (qrSoportado) detector = new BarcodeDetector({ formats: ['qr_code'] });
        } catch (e) { detector = null; }
      }
      if (!detector) {
        try { await cargarJsQR(); }
        catch (e) { showToast('No se pudo cargar el lector de QR. Revisa tu conexión e inténtalo de nuevo.', 'danger'); }
      }

      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d', { willReadFrequently: true });

      const leerConJsQR = (v) => {
        if (!v.videoWidth || v.readyState < 2) return null;
        const ancho = Math.min(v.videoWidth, 800);
        canvas.width = ancho;
        canvas.height = Math.round((v.videoHeight / v.videoWidth) * ancho);
        ctx.drawImage(v, 0, 0, canvas.width, canvas.height);
        const px = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const cod = window.jsQR(px.data, px.width, px.height);
        return cod ? cod.data : null;
      };

      const bucle = async () => {
        if (!escaneoActivo || escaneoDetectado || !escaneoStream) return;
        let texto = null;
        try {
          if (detector) {
            const codigos = await detector.detect(video);
            if (codigos && codigos.length) texto = codigos[0].rawValue;
          } else if (window.jsQR) {
            texto = leerConJsQR(video);
          }
        } catch (e) {
          // Detector nativo no disponible en este navegador → jsQR
          if (detector) {
            detector = null;
            cargarJsQR().catch(() => {});
          }
        }

        if (texto) {
          const destino = destinoDesdeQR(String(texto).trim());
          if (destino) { alEscanearQR(destino); return; }
          if (texto !== escaneoTextoPrevio) {
            escaneoTextoPrevio = texto;
            showToast('QR leído: ' + String(texto).substring(0, 80), 'info');
          }
        }
        escaneoLoop = setTimeout(bucle, detector ? 40 : 110);
      };

      bucle();
    } catch (err) {
      console.error('Error accediendo a la cámara:', err);
      detenerEscaneoQR();
      showToast('No se pudo acceder a la cámara. Asegúrate de otorgar permisos.', 'danger');
    }
  }
});

// ═══ Función Toast Global (reemplaza a alert()) ═══
window.showToast = function(mensaje, tipo = 'info') {
  // Eliminar toasts existentes
  const existentes = document.querySelectorAll('.toast');
  existentes.forEach(t => t.remove());

  const contenedor = document.getElementById('toast-container') || (() => {
    const c = document.createElement('div');
    c.id = 'toast-container';
    c.className = 'toast-container';
    document.body.appendChild(c);
    return c;
  })();

  const iconos = {
    success: '✓',
    warning: '⚠',
    danger: '✕',
    info: 'ℹ'
  };

  const toast = document.createElement('div');
  toast.className = `toast toast--${tipo}`;
  toast.innerHTML = `
    <span class="toast__icon">${iconos[tipo] || iconos.info}</span>
    <span class="toast__message">${mensaje}</span>
    <button class="toast__close" onclick="this.parentElement.remove()">✕</button>
  `;

  contenedor.appendChild(toast);

  // Auto-remover después de 4 segundos
  setTimeout(() => {
    if (toast.parentNode) toast.remove();
  }, 4000);
};

// ═══ Manejador de botones "Consultar" (data-enlace) ═══
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.btn-consultar');
  if (!btn) return;

  const enlace = btn.getAttribute('data-enlace');

  if (enlace && enlace !== '#') {
    window.open(enlace, '_blank');
  } else {
    showToast('Este recurso no tiene enlace disponible.', 'info');
  }
});

// ═══ Confirmación bonita para eliminar (reemplaza a confirm()) ═══
window.confirmDelete = function(mensaje, url) {
  const existentes = document.querySelectorAll('.confirm-modal');
  existentes.forEach(m => m.remove());

  const modal = document.createElement('div');
  modal.className = 'confirm-modal';
  modal.innerHTML = `
    <div class="confirm-modal__backdrop"></div>
    <div class="confirm-modal__card">
      <div class="confirm-modal__header">
        <span class="confirm-modal__icon">⚠</span>
        <span class="confirm-modal__title">Confirmar eliminación</span>
      </div>
      <div class="confirm-modal__body">
        <p>${mensaje}</p>
      </div>
      <div class="confirm-modal__actions">
        <button class="confirm-modal__btn cancel" onclick="this.closest('.confirm-modal').remove()">
          Cancelar
        </button>
        <button class="confirm-modal__btn delete" onclick="window.location.href='${url}'">
          Sí, eliminar
        </button>
      </div>
    </div>
  `;

  document.body.appendChild(modal);
  modal.querySelector('.confirm-modal__backdrop').addEventListener('click', () => {
    modal.remove();
  });
};
