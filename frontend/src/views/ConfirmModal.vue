<template>
  <div class="modal-overlay" @click.self="$emit('cancel')">
    <div class="confirm-card">
      <div class="confirm-icon">⚠️</div>
      <h3>{{ titulo }}</h3>
      <p>{{ mensagem }}</p>
      
      <div class="confirm-actions">
        <button class="btn-cancel" @click="$emit('cancel')">
          Cancelar
        </button>
        <button class="btn-confirm" @click="$emit('confirm')">
          Confirmar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ConfirmModal",
  
  props: {
    titulo: {
      type: String,
      default: 'Confirmar Ação'
    },
    mensagem: {
      type: String,
      default: 'Tem certeza que deseja continuar?'
    }
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1001;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.confirm-card {
  background: white;
  border-radius: 12px;
  padding: 32px;
  width: 90%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.confirm-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.confirm-card h3 {
  color: #1f2937;
  font-size: 20px;
  font-weight: 600;
  margin: 0 0 12px 0;
}

.confirm-card p {
  color: #6b7280;
  font-size: 14px;
  margin: 0 0 24px 0;
  line-height: 1.5;
}

.confirm-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.btn-cancel,
.btn-confirm {
  padding: 10px 24px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.btn-cancel {
  background: #e5e7eb;
  color: #374151;
}

.btn-cancel:hover {
  background: #d1d5db;
}

.btn-confirm {
  background: #ef4444;
  color: white;
}

.btn-confirm:hover {
  background: #dc2626;
}
</style>