<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-card">
      
      <div class="modal-header">
        <h3>
          <b-icon :icon="isEdit ? 'pencil-square' : 'plus-circle'" variant="primary" class="mr-2"></b-icon>
          {{ isEdit ? 'Editar Tarefa' : 'Nova Tarefa' }}
        </h3>
        <button class="close-btn" @click="$emit('close')">
          <b-icon icon="x-lg" aria-label="Fechar"></b-icon>
        </button>
      </div>

      <form @submit.prevent="salvar" class="modal-body">
        
        <div v-if="localError" class="alert-error-custom">
          <b-icon icon="exclamation-triangle-fill" class="mr-2"></b-icon>
          {{ localError }}
        </div>

        <div class="form-group">
          <label>Título <span class="required">*</span></label>
          <input 
            type="text" 
            v-model="form.titulo" 
            class="form-input"
            placeholder="Ex: Implementar autenticação JWT"
            
            :class="{ 'input-error': validationErrors.titulo }"
          />
          <div v-if="validationErrors.titulo" class="error-message">
            {{ validationErrors.titulo }}
          </div>
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea 
            v-model="form.descricao" 
            class="form-textarea"
            rows="3"
            placeholder="Detalhes sobre a tarefa..."
          ></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Status <span class="required">*</span></label>
            <select 
              v-model="form.status" 
              class="form-select custom-select-style" 
              
              :class="{ 'input-error': validationErrors.status }"
            >
              <option value="" disabled>Selecione o Status</option>
              <option value="pendente">Pendente</option>
              <option value="em_andamento">Em Andamento</option>
              <option value="concluida">Concluída</option>
            </select>
            <div v-if="validationErrors.status" class="error-message">
              {{ validationErrors.status }}
            </div>
          </div>

          <div class="form-group">
            <label>Prioridade <span class="required">*</span></label>
            <select 
              v-model="form.prioridade" 
              class="form-select custom-select-style" 
              
              :class="{ 'input-error': validationErrors.prioridade }"
            >
              <option value="" disabled>Selecione a Prioridade</option>
              <option value="baixa">Baixa</option>
              <option value="media">Média</option>
              <option value="alta">Alta</option>
            </select>
            <div v-if="validationErrors.prioridade" class="error-message">
              {{ validationErrors.prioridade }}
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Data Limite</label>
          <input 
            type="date" 
            v-model="form.data_limite" 
            class="form-input"
            :class="{ 'input-error': validationErrors.data_limite }"
          />
          <div v-if="validationErrors.data_limite" class="error-message">
            {{ validationErrors.data_limite }}
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="$emit('close')">
            Cancelar
          </button>
          <button type="submit" class="btn-save" :disabled="loading">
            <span v-if="loading">
              <b-spinner small type="grow" class="mr-1"></b-spinner>
              Salvando...
            </span>
            <span v-else>
              <b-icon :icon="isEdit ? 'check-circle' : 'save'"></b-icon>
              {{ isEdit ? 'Atualizar Tarefa' : 'Salvar Tarefa' }}
            </span>
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
        localError: null,
        // Novo objeto para armazenar erros de validação de campo
        validationErrors: {} 
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
      } else {
        this.form.status = ''; 
        this.form.prioridade = '';
      }
    },
    
    methods: {
      /**
      * Validação manual dos campos antes de enviar para o backend.
      * @returns {boolean} Se o formulário é válido.
      */
      validateForm() {
        this.validationErrors = {};
        let isValid = true;
        const today = new Date().setHours(0, 0, 0, 0);

        // 1. Título
        if (!this.form.titulo || this.form.titulo.trim().length === 0) {
          this.validationErrors.titulo = 'O título da tarefa é obrigatório.';
          isValid = false;
        } else if (this.form.titulo.length < 5) {
          this.validationErrors.titulo = 'O título deve ter pelo menos 5 caracteres.';
          isValid = false;
        }
        
        // 2. Status
        const validStatus = ['pendente', 'em_andamento', 'concluida'];
        if (!this.form.status || !validStatus.includes(this.form.status)) {
          this.validationErrors.status = 'Selecione um status válido para a tarefa.';
          isValid = false;
        }

        // 3. Prioridade
        const validPrioridades = ['baixa', 'media', 'alta'];
        if (!this.form.prioridade || !validPrioridades.includes(this.form.prioridade)) {
          this.validationErrors.prioridade = 'Selecione uma prioridade válida.';
          isValid = false;
        }

        // 4. Data Limite (Regra de Negócio: Não pode ser no passado)
        if (this.form.data_limite) {
          const dueDate = new Date(this.form.data_limite).setHours(0, 0, 0, 0);
          if (dueDate < today) {
            this.validationErrors.data_limite = 'A data limite não pode ser uma data passada.';
            isValid = false;
          }
        }

        // Se houver erros de validação local, exibe uma mensagem geral
        if (!isValid) {
          this.localError = 'Por favor, corrija os erros nos campos antes de salvar.';
        } else {
          this.localError = null;
        }

        return isValid;
      },

      async salvar() {
        // 1. Validação Local
        if (!this.validateForm()) {
          return; // Impede o envio se a validação falhar
        }
        
        this.loading = true;
        this.localError = null;
        this.validationErrors = {}; // Limpa erros de campo após validação local

        try {
          const payload = { ...this.form };
          
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
          // 2. Tratamento de Erros do Backend
          const responseData = err.response?.data;
          
          if (responseData?.errors) {
            // Se o backend retornar um objeto de erros de validação
            this.localError = 'O servidor encontrou problemas de validação. Verifique os campos abaixo.';
            
            // Mapeia erros do backend para o objeto validationErrors
            Object.keys(responseData.errors).forEach(key => {
              // Usa apenas a primeira mensagem de erro para cada campo
              this.validationErrors[key] = responseData.errors[key][0]; 
            });

          } else {
            // Erro de API genérico
            this.localError = responseData?.message || 'Erro inesperado ao salvar. Tente novamente.';
          }
          
          console.error(err);
        }
        
        this.loading = false;
      }
    }
  };
</script>

<style scoped>
/* ================================================= */
/* ESTILOS DE VALIDAÇÃO */
/* ================================================= */

/* Cor da borda vermelha quando há erro */
.input-error {
  border-color: #ef4444 !important; /* Vermelho mais vibrante */
}

/* Mensagem de erro de campo */
.error-message {
  color: #ef4444;
  font-size: 0.8rem;
  margin-top: 4px;
  font-weight: 600;
}

/* Alteração na mensagem de erro geral para destacar que é local/do servidor */
.alert-error-custom {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 0.95rem;
  font-weight: 600;
  display: flex;
  align-items: center;
}

/* ================================================= */
/* ESTILOS SIST. */
/* ================================================= */

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
  backdrop-filter: blur(3px); 
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
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
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
  padding: 16px 24px;
  border-bottom: 1px solid #f3f4f6;
  background-color: #ffffff;
  position: sticky;
  top: 0;
  z-index: 10;
}

.modal-header h3 {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  line-height: 1;
  border-radius: 50%;
  transition: all 0.2s;
}

.close-btn:hover {
  color: #ef4444;
  background-color: #fef2f2;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  color: #374151;
  font-size: 0.9rem;
  font-weight: 700;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.required {
  color: #ef4444;
  margin-left: 2px;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  color: #1f2937;
  transition: all 0.2s;
  background-color: #f9fafb;
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #9ca3af;
  font-style: italic;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
  background-color: #ffffff;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.custom-select-style {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 14px 10px;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-row .form-group {
  flex: 1;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel {
  padding: 10px 20px;
  border: 1px solid #d1d5db;
  background: white;
  color: #6b7280;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
  color: #374151;
}

.btn-save {
  padding: 10px 24px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.2s, transform 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #1e40af;
  transform: translateY(-1px);
}

.btn-save:disabled {
  background: #93c5fd;
  opacity: 0.8;
  cursor: not-allowed;
  transform: none;
}

.btn-save .b-icon {
  margin-right: 5px;
}

@media (max-width: 640px) {
  .modal-card {
    border-radius: 0;
    max-width: 100%;
    width: 100%;
    max-height: 100vh;
  }

  .form-row {
    flex-direction: column;
    gap: 0;
  }
}
</style>