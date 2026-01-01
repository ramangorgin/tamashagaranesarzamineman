{{-- Global Snackbar Component --}}
<div id="snackbar-container" class="snackbar-container"></div>

@push('styles')
<style>
.snackbar-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 10000;
  pointer-events: none;
  display: flex;
  flex-direction: column-reverse;
  gap: 0.75rem;
  max-width: 400px;
  width: auto;
}

.snackbar {
  background: #fff;
  border-radius: 0.75rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  pointer-events: auto;
  min-width: 300px;
  max-width: 100%;
  opacity: 0;
  transform: translateY(20px) scale(0.95);
  animation: snackbarSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
  border-right: 4px solid;
  position: relative;
}

.snackbar.snackbar-success {
  border-color: #22c55e;
  background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}

.snackbar.snackbar-success .snackbar-icon {
  color: #22c55e;
}

.snackbar.snackbar-error {
  border-color: #ef4444;
  background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.snackbar.snackbar-error .snackbar-icon {
  color: #ef4444;
}

.snackbar.snackbar-warning {
  border-color: #f59e0b;
  background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
}

.snackbar.snackbar-warning .snackbar-icon {
  color: #f59e0b;
}

.snackbar.snackbar-info {
  border-color: #3b82f6;
  background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
}

.snackbar.snackbar-info .snackbar-icon {
  color: #3b82f6;
}

.snackbar-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
  animation: iconPulse 0.6s ease-out;
}

.snackbar-content {
  flex: 1;
  font-size: 0.95rem;
  line-height: 1.6;
  color: #1e293b;
  font-weight: 500;
}

.snackbar-close {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0.25rem;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.snackbar-close:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #1e293b;
  transform: rotate(90deg);
}

.snackbar.hiding {
  animation: snackbarSlideOut 0.3s ease-in forwards;
}

@keyframes snackbarSlideIn {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes snackbarSlideOut {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
}

@keyframes iconPulse {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

/* Mobile Responsive */
@media (max-width: 576px) {
  .snackbar-container {
    bottom: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
  }

  .snackbar {
    min-width: auto;
    padding: 0.875rem 1rem;
  }

  .snackbar-content {
    font-size: 0.875rem;
  }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
  'use strict';

  // Prevent duplicate messages
  const messageHistory = new Set();
  const MESSAGE_TTL = 5000; // 5 seconds

  function getMessageKey(type, message) {
    return `${type}:${message}`;
  }

  function getOrCreateContainer() {
    let container = document.getElementById('snackbar-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'snackbar-container';
      container.className = 'snackbar-container';
      document.body.appendChild(container);
    }
    return container;
  }

  /**
   * Show a snackbar message
   * @param {string} type - 'success', 'error', 'warning', 'info'
   * @param {string} message - Message text
   * @param {number} duration - Auto-dismiss duration in ms (default: 5000)
   */
  window.showSnackbar = function(type, message, duration = 5000) {
    if (!message || !message.trim()) return;

    const messageKey = getMessageKey(type, message);
    
    // Prevent duplicate messages within TTL
    if (messageHistory.has(messageKey)) {
      return;
    }
    
    messageHistory.add(messageKey);
    
    // Remove from history after TTL
    setTimeout(() => {
      messageHistory.delete(messageKey);
    }, MESSAGE_TTL);

    const container = getOrCreateContainer();
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
      <div class="snackbar-content">${escapeHtml(message)}</div>
      <button type="button" class="snackbar-close" aria-label="بستن">
        <i class="bi bi-x"></i>
      </button>
    `;

    // Insert at the beginning (so newest appears at bottom)
    container.insertBefore(snackbar, container.firstChild);

    // Force reflow to trigger animation
    snackbar.offsetHeight;

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
    if (!snackbar || snackbar.classList.contains('hiding')) return;
    
    snackbar.classList.add('hiding');
    setTimeout(() => {
      if (snackbar.parentNode) {
        snackbar.parentNode.removeChild(snackbar);
      }
    }, 300);
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Convenience functions
  window.showSuccess = (message, duration) => showSnackbar('success', message, duration);
  window.showError = (message, duration) => showSnackbar('error', message, duration);
  window.showWarning = (message, duration) => showSnackbar('warning', message, duration);
  window.showInfo = (message, duration) => showSnackbar('info', message, duration);
})();
</script>
@endpush
