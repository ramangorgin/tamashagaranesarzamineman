{{-- Global Snackbar Component --}}
<div id="snackbar-container" class="snackbar-container"></div>

@push('styles')
<style>
.snackbar-container {
  position: fixed;
  top: 20px;
  left: 20px;
  right: 20px;
  z-index: 10000;
  pointer-events: none;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-width: 400px;
  margin: 0 auto;
}

.snackbar {
  background: #fff;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  pointer-events: auto;
  animation: slideInLeft 0.3s ease-out;
  border-right: 4px solid;
  min-width: 280px;
  max-width: 100%;
}

.snackbar.snackbar-success {
  border-color: #22c55e;
}

.snackbar.snackbar-success .snackbar-icon {
  color: #22c55e;
}

.snackbar.snackbar-error {
  border-color: #ef4444;
}

.snackbar.snackbar-error .snackbar-icon {
  color: #ef4444;
}

.snackbar.snackbar-warning {
  border-color: #f59e0b;
}

.snackbar.snackbar-warning .snackbar-icon {
  color: #f59e0b;
}

.snackbar.snackbar-info {
  border-color: #3b82f6;
}

.snackbar.snackbar-info .snackbar-icon {
  color: #3b82f6;
}

.snackbar-icon {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.snackbar-content {
  flex: 1;
  font-size: 0.9rem;
  line-height: 1.5;
  color: #1e293b;
}

.snackbar-close {
  background: none;
  border: none;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: color 0.2s;
}

.snackbar-close:hover {
  color: #1e293b;
}

.snackbar.hiding {
  animation: slideOutLeft 0.3s ease-in forwards;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideOutLeft {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(-100%);
  }
}

/* Mobile Responsive */
@media (max-width: 576px) {
  .snackbar-container {
    left: 10px;
    right: 10px;
    top: 10px;
  }

  .snackbar {
    min-width: auto;
    padding: 0.875rem 1rem;
  }

  .snackbar-content {
    font-size: 0.85rem;
  }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
  'use strict';

  const container = document.getElementById('snackbar-container');
  if (!container) return;

  /**
   * Show a snackbar message
   * @param {string} type - 'success', 'error', 'warning', 'info'
   * @param {string} message - Message text
   * @param {number} duration - Auto-dismiss duration in ms (default: 5000)
   */
  window.showSnackbar = function(type, message, duration = 5000) {
    const snackbar = document.createElement('div');
    snackbar.className = `snackbar snackbar-${type}`;

    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info: 'bi-info-circle-fill'
    };

    snackbar.innerHTML = `
      <i class="bi ${icons[type] || icons.info} snackbar-icon"></i>
      <div class="snackbar-content">${message}</div>
      <button type="button" class="snackbar-close" aria-label="بستن">
        <i class="bi bi-x"></i>
      </button>
    `;

    container.appendChild(snackbar);

    // Auto-dismiss
    let timeoutId = setTimeout(() => {
      dismissSnackbar(snackbar);
    }, duration);

    // Manual close
    const closeBtn = snackbar.querySelector('.snackbar-close');
    closeBtn.addEventListener('click', () => {
      clearTimeout(timeoutId);
      dismissSnackbar(snackbar);
    });

    // Return dismiss function for manual control
    return () => {
      clearTimeout(timeoutId);
      dismissSnackbar(snackbar);
    };
  };

  function dismissSnackbar(snackbar) {
    snackbar.classList.add('hiding');
    setTimeout(() => {
      if (snackbar.parentNode) {
        snackbar.parentNode.removeChild(snackbar);
      }
    }, 300);
  }

  // Convenience functions
  window.showSuccess = (message, duration) => showSnackbar('success', message, duration);
  window.showError = (message, duration) => showSnackbar('error', message, duration);
  window.showWarning = (message, duration) => showSnackbar('warning', message, duration);
  window.showInfo = (message, duration) => showSnackbar('info', message, duration);
})();
</script>
@endpush

