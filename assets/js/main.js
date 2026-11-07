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
  const btnEscanearQR = document.getElementById('btnEscanearQR');
  const modalQR = document.getElementById('modalQR');

  if (btnEscanearQR && modalQR) {
    btnEscanearQR.addEventListener('click', async () => {
      modalQR.classList.add('active');
      await iniciarCamaraQR();
    });
  }

  async function iniciarCamaraQR() {
    const video = document.createElement('video');
    video.setAttribute('playsinline', '');
    video.style.width = '100%';
    video.style.height = 'auto';
    video.style.borderRadius = 'var(--radius-md)';
    video.style.objectFit = 'cover';

    const modalSheet = modalQR.querySelector('.modal-sheet');
    if (modalSheet) {
      // Insertar video antes del contenido existente
      const firstChild = modalSheet.firstElementChild;
      modalSheet.insertBefore(video, firstChild);
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'environment' }
      });
      video.srcObject = stream;
      video.play();

      // Detener cámara al cerrar modal
      const observer = new MutationObserver(() => {
        if (!modalQR.classList.contains('active')) {
          const tracks = stream.getTracks();
          tracks.forEach(track => track.stop());
          observer.disconnect();
          if (video.parentNode) video.parentNode.removeChild(video);
        }
      });
      observer.observe(modalQR, { attributes: true, attributeFilter: ['class'] });
    } catch (err) {
      console.error('Error accediendo a la cámara:', err);
      video.remove();
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
