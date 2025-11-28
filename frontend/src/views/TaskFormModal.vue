<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-card">
      <div class="modal-header">
        <h3>{{ isEdit ? '✏️ Editar Tarefa' : '➕ Nova Tarefa' }}</h3>
        <button class="close-btn" @click="$emit('close')">✕</button>
      </div>

      <form @submit.prevent="salvar" class="modal-body">
        <div v-if="error" class="alert-error">
          {{ error }}
        </div>

        <!-- TÍTULO -->
        <div class="form-group">
          <label>Título *</label>
          <input 
            type="text" 
            v-model="form.titulo" 
            class="form-input"
            placeholder="Ex: Implementar autenticação JWT"
            required 
          />
        </div>

        <!-- DESCRIÇÃO -->
        <div class="form-group">
          <label>Descrição</label>
          <textarea 
            v-model="form.descricao" 
            class="form-textarea"
            rows="4"
            placeholder="Detalhes sobre a tarefa..."
          ></textarea>
        </div>

        <!-- STATUS E PRIORIDADE -->
        <div class="form-row">
          <div class="form-group">
            <label>Status *</label>
            <select v-model="form.status" class="form-select" required>
              <option value="pendente">Pendente</option>
              <option value="em_andamento">Em Andamento</option>
              <option value="concluida">Concluída</option>
            </select>
          </div>

          <div class="form-group">
            <label>Prioridade *</label>
            <select v-model="form.prioridade" class="form-select" required>
              <option value="baixa">Baixa</option>
              <option value="media">Média</option>
              <option value="alta">Alta</option>
            </select>
          </div>
        </div>

        <!-- DATA LIMITE -->
        <div class="form-group">
          <label>Data Limite</label>
          <input 
            type="date" 
            v-model="form.data_limite" 
            class="form-input"
          />
        </div>

        <!-- BOTÕES -->
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="$emit('close')">
            Cancelar
          </button>
          <button type="submit" class="btn-save" :disabled="loading">
            <span v-if="loading">Salvando...</span>
            <span v-else>{{ isEdit ? 'Atualizar' : 'Criar Tarefa' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "TaskFormModal",
  
  props: {
    tarefa: {
      type: Object,
      default: null
    }
  },
  
  data() {
    return {
      form: {
        titulo: '',
        descricao: '',
        status: 'pendente',
        prioridade: 'media',
        data_limite: ''
      },
      loading: false,
      error: null
    };
  },
  
  computed: {
    isEdit() {
      return !!this.tarefa;
    }
  },
  
  mounted() {
    if (this.tarefa) {
      this.form = {
        titulo: this.tarefa.titulo,
        descricao: this.tarefa.descricao || '',
        status: this.tarefa.status,
        prioridade: this.tarefa.prioridade,
        data_limite: this.tarefa.data_limite ? this.tarefa.data_limite.split('T')[0] : ''
      };
    }
  },
  
  methods: {
    async salvar() {
      this.loading = true;
      this.error = null;
      
      try {
        const payload = { ...this.form };
        
        // Remove data_limite se estiver vazia
        if (!payload.data_limite) {
          delete payload.data_limite;
        }
        
        if (this.isEdit) {
          await this.$http.put(`/tarefas/${this.tarefa.id}`, payload);
        } else {
          await this.$http.post('/tarefas', payload);
        }
        
        this.$emit('saved');
      } catch (err) {
        this.error = err.response?.data?.message || 'Erro ao salvar tarefa';
        console.error(err);
      }
      
      this.loading = false;
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
  z-index: 1000;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-card {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: auto;
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

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  color: #1f2937;
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  line-height: 1;
}

.close-btn:hover {
  color: #1f2937;
}

.modal-body {
  padding: 24px;
}

.alert-error {
  background: #fee2e2;
  border: 1px solid #fca5a5;
  color: #991b1b;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 14px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  color: #374151;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 6px;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
  transition: all 0.2s;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #1a73e8;
  box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.form-textarea {
  resize: vertical;
  font-family: inherit;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel {
  padding: 10px 20px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f3f4f6;
}

.btn-save {
  padding: 10px 24px;
  background: #1a73e8;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #1557b0;
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>