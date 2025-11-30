<template>
  <div class="tasks-page">
    <b-card no-body class="mb-4 shadow-sm border-0">
      <b-card-body class="p-4">
        <b-row align-v="center">
          <b-col cols="12" md="6" class="mb-3 mb-md-0 d-flex align-items-center justify-content-between">
            <h3 class="mb-0 font-weight-bold">
              <b-icon icon="clipboard-check" variant="primary" class="mr-2"></b-icon>
              Gerenciamento de Tarefas
            </h3>
            <b-button variant="primary" size="md" @click="openCreateModal" class="d-none d-md-block btn-new-task">
              <b-icon icon="plus-circle"></b-icon>
              Nova Tarefa
            </b-button>
          </b-col>

          <b-col cols="12" md="6" class="text-md-right d-flex align-items-center justify-content-md-end">
            <b-button variant="primary" size="md" @click="openCreateModal" class="mr-3 d-md-none btn-new-task">
              <b-icon icon="plus-circle"></b-icon>
              Nova Tarefa
            </b-button>

            <b-button-group size="sm" class="export-buttons">
              <b-button variant="success" @click="exportarExcel">
                <b-icon icon="file-earmark-excel"></b-icon>
                Excel
              </b-button>
              <b-button variant="info" @click="exportarCsv">
                <b-icon icon="file-earmark-text"></b-icon>
                CSV
              </b-button>
            </b-button-group>
          </b-col>
        </b-row>
      </b-card-body>
    </b-card>

    <b-card no-body class="mb-4 shadow-sm border-0">
      <b-card-body class="p-3">
        <b-row align-v="end">
          <b-col cols="12" md="4" class="mb-2 mb-md-0">
            <b-form-group label="Status:" label-for="filter-status" class="mb-0 font-weight-bold small">
              <b-form-select
                id="filter-status"
                v-model="filtros.status"
                :options="statusOptions"
                @change="loadTarefas"
                size="sm"
                class="filter-select"
              ></b-form-select>
            </b-form-group>
          </b-col>

          <b-col cols="12" md="4" class="mb-2 mb-md-0">
            <b-form-group label="Prioridade:" label-for="filter-prioridade" class="mb-0 font-weight-bold small">
              <b-form-select
                id="filter-prioridade"
                v-model="filtros.prioridade"
                :options="prioridadeOptions"
                @change="loadTarefas"
                size="sm"
                class="filter-select"
              ></b-form-select>
            </b-form-group>
          </b-col>

          <b-col cols="12" md="4" class="text-md-right">
            <b-button variant="outline-secondary" size="sm" @click="limparFiltros" class="clear-filters-btn">
              <b-icon icon="x-circle"></b-icon>
              Limpar Filtros
            </b-button>
          </b-col>
        </b-row>
      </b-card-body>
    </b-card>

    <b-card no-body class="shadow-sm border-0">
      <b-table
        :items="tarefas"
        :fields="fields"
        :busy="loading"
        hover
        responsive
        show-empty
        empty-text="Nenhuma tarefa encontrada"
        class="mb-0"
      >
        <template #table-busy>
          <div class="text-center text-primary my-5">
            <b-spinner class="align-middle mr-2"></b-spinner>
            <strong>Carregando tarefas...</strong>
          </div>
        </template>

        <template #cell(id)="data">
          <b-badge variant="secondary" pill class="font-weight-bold id-badge">
            #{{ data.value }}
          </b-badge>
        </template>

        <template #cell(titulo)="data">
          <div>
            <div class="font-weight-bold text-dark mb-1 titulo-tarefa">
              {{ data.item.titulo }}
            </div>
            <small class="text-muted descricao-tarefa">
              {{ data.item.descricao || 'Sem descrição' }}
            </small>
          </div>
        </template>

        <template #cell(status)="data">
          <b-badge 
            :variant="getStatusVariant(data.value)" 
            pill 
            class="px-3 py-2 font-weight-bold status-badge"
          >
            {{ formatStatus(data.value) }}
          </b-badge>
        </template>

        <template #cell(prioridade)="data">
          <b-badge 
            :variant="getPrioridadeVariant(data.value)"
            class="px-3 py-2 font-weight-bold prioridade-badge"
          >
            <b-icon 
              :icon="getPrioridadeIcon(data.value)" 
              class="mr-1"
            ></b-icon>
            {{ formatPrioridade(data.value) }}
          </b-badge>
        </template>

        <template #cell(data_limite)="data">
          <div v-if="data.value" class="text-nowrap data-prazo">
            <b-icon icon="calendar-event" variant="secondary" class="mr-1"></b-icon>
            <span :class="isOverdue(data.value) ? 'text-danger font-weight-bold' : 'text-dark'">
              {{ formatDate(data.value) }}
            </span>
          </div>
          <small v-else class="text-muted">Sem prazo</small>
        </template>

        <template #cell(actions)="data">
          <b-button-group size="sm" class="table-actions-group">
            <b-button 
              variant="outline-primary" 
              @click="openEditModal(data.item)"
              v-b-tooltip.hover 
              title="Editar tarefa"
            >
              <b-icon icon="pencil-square"></b-icon>
            </b-button>
            <b-button 
              variant="outline-danger" 
              @click="confirmDelete(data.item)"
              v-b-tooltip.hover 
              title="Excluir tarefa"
            >
              <b-icon icon="trash"></b-icon>
            </b-button>
          </b-button-group>
        </template>
      </b-table>

      <b-card-footer 
        v-if="pagination.last_page > 1" 
        class="d-flex justify-content-between align-items-center bg-light"
      >
        <div class="text-muted small">
          <b-icon icon="info-circle" class="mr-1"></b-icon>
          Mostrando {{ tarefas.length }} de {{ pagination.total }} tarefas
        </div>
        <b-pagination
          v-model="pagination.current_page"
          :total-rows="pagination.total"
          :per-page="10"
          @change="changePage"
          align="center"
          size="sm"
          class="mb-0"
          first-number
          last-number
        ></b-pagination>
      </b-card-footer>
    </b-card>

    <TaskFormModal
      v-if="showModal"
      :tarefa="tarefaEditando"
      @close="closeModal"
      @saved="onTarefaSaved"
    />

    <b-modal
      v-model="showDeleteModal"
      title="Confirmar Exclusão"
      ok-variant="danger"
      ok-title="Sim, Excluir"
      cancel-title="Cancelar"
      @ok="deleteTarefa"
      centered
      size="sm"
    >
      <div class="text-center py-3">
        <b-icon 
          icon="exclamation-triangle-fill" 
          variant="warning" 
          font-scale="3"
          class="mb-3"
        ></b-icon>
        <p class="mb-0">
          Tem certeza que deseja excluir a tarefa<br>
          <strong class="text-primary">{{ tarefaParaDeletar?.titulo }}</strong>?
        </p>
      </div>
    </b-modal>
  </div>
</template>

<script>
import TaskFormModal from './TaskFormModal.vue';

export default {
  name: "TaskList",
  
  components: {
    TaskFormModal
  },
  
  data() {
    return {
      tarefas: [],
      loading: false,
      showModal: false,
      showDeleteModal: false,
      tarefaEditando: null,
      tarefaParaDeletar: null,
      
      filtros: {
        status: '',
        prioridade: ''
      },
      
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0
      },
      
      statusOptions: [
        { value: '', text: 'Todos os Status' },
        { value: 'pendente', text: 'Pendente' },
        { value: 'em_andamento', text: 'Em Andamento' },
        { value: 'concluida', text: 'Concluída' }
      ],
      
      prioridadeOptions: [
        { value: '', text: 'Todas as Prioridades' },
        { value: 'baixa', text: 'Baixa' },
        { value: 'media', text: 'Média' },
        { value: 'alta', text: 'Alta' }
      ],
      
      fields: [
        { 
          key: 'id', 
          label: '#', 
          sortable: true, 
          thClass: 'text-center bg-light', 
          tdClass: 'text-center align-middle',
          thStyle: { width: '80px' }
        },
        { 
          key: 'titulo', 
          label: 'Tarefa', 
          sortable: true,
          thClass: 'bg-light',
          tdClass: 'align-middle'
        },
        { 
          key: 'status', 
          label: 'Status', 
          sortable: true, 
          thClass: 'text-center bg-light', 
          tdClass: 'text-center align-middle',
          thStyle: { width: '150px' }
        },
        { 
          key: 'prioridade', 
          label: 'Prioridade', 
          sortable: true, 
          thClass: 'text-center bg-light', 
          tdClass: 'text-center align-middle',
          thStyle: { width: '150px' }
        },
        { 
          key: 'data_limite', 
          label: 'Prazo', 
          sortable: true, 
          thClass: 'text-center bg-light', 
          tdClass: 'text-center align-middle',
          thStyle: { width: '140px' }
        },
        { 
          key: 'actions', 
          label: 'Ações', 
          thClass: 'text-center bg-light', 
          tdClass: 'text-center align-middle',
          thStyle: { width: '120px' }
        }
      ]
    };
  },
  
  mounted() {
    this.loadTarefas();
  },
  
  methods: {
    async loadTarefas(page = 1) {
      this.loading = true;
      
      try {
        const params = {
          page,
          status: this.filtros.status,
          prioridade: this.filtros.prioridade
        };
        
        const response = await this.$http.get('/tarefas', { params });
        
        this.tarefas = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        };
      } catch (error) {
        this.$bvToast.toast('Erro ao carregar tarefas', {
          title: 'Erro',
          variant: 'danger',
          solid: true
        });
        console.error(error);
      }
      
      this.loading = false;
    },
    
    openCreateModal() {
      this.tarefaEditando = null;
      this.showModal = true;
    },
    
    openEditModal(tarefa) {
      this.tarefaEditando = { ...tarefa };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.tarefaEditando = null;
    },
    
    onTarefaSaved() {
      this.closeModal();
      this.loadTarefas(this.pagination.current_page);
      
      this.$bvToast.toast('Tarefa salva com sucesso!', {
        title: 'Sucesso',
        variant: 'success',
        solid: true,
        autoHideDelay: 3000
      });
    },
    
    confirmDelete(tarefa) {
      this.tarefaParaDeletar = tarefa;
      this.showDeleteModal = true;
    },
    
    async deleteTarefa() {
      try {
        await this.$http.delete(`/tarefas/${this.tarefaParaDeletar.id}`);
        
        this.$bvToast.toast('Tarefa excluída com sucesso!', {
          title: 'Sucesso',
          variant: 'success',
          solid: true,
          autoHideDelay: 3000
        });
        
        this.loadTarefas(this.pagination.current_page);
      } catch (error) {
        this.$bvToast.toast('Erro ao excluir tarefa', {
          title: 'Erro',
          variant: 'danger',
          solid: true
        });
        console.error(error);
      }
    },
    
    limparFiltros() {
      this.filtros.status = '';
      this.filtros.prioridade = '';
      this.loadTarefas();
    },
    
    changePage(page) {
      this.loadTarefas(page);
    },
    
    formatStatus(status) {
      const map = {
        'pendente': 'Pendente',
        'em_andamento': 'Em Andamento',
        'concluida': 'Concluída'
      };
      return map[status] || status;
    },
    
    formatPrioridade(prioridade) {
      const map = {
        'baixa': 'Baixa',
        'media': 'Média',
        'alta': 'Alta'
      };
      return map[prioridade] || prioridade;
    },
    
    formatDate(date) {
      if (!date) return 'Sem prazo';
      return new Date(date).toLocaleDateString('pt-BR');
    },
    
    isOverdue(date) {
      if (!date) return false;
      return new Date(date) < new Date();
    },
    
    getStatusVariant(status) {
      const variants = {
        'pendente': 'warning',
        'em_andamento': 'info',
        'concluida': 'success'
      };
      return variants[status] || 'secondary';
    },
    
    getPrioridadeVariant(prioridade) {
      const variants = {
        'baixa': 'secondary',
        'media': 'warning',
        'alta': 'danger'
      };
      return variants[prioridade] || 'secondary';
    },
    
    getPrioridadeIcon(prioridade) {
      const icons = {
        'baixa': 'arrow-down-circle',
        'media': 'dash-circle',
        'alta': 'arrow-up-circle'
      };
      return icons[prioridade] || 'circle';
    },
    
    async exportarExcel() {
      try {
        const params = {
          status: this.filtros.status,
          prioridade: this.filtros.prioridade
        };
        
        const response = await this.$http.get('/tarefas-export/excel', {
          params,
          responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `tarefas_${new Date().toISOString().split('T')[0]}.xlsx`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        
        this.$bvToast.toast('Arquivo Excel exportado com sucesso!', {
          title: 'Sucesso',
          variant: 'success',
          solid: true
        });
      } catch (error) {
        this.$bvToast.toast('Erro ao exportar para Excel', {
          title: 'Erro',
          variant: 'danger',
          solid: true
        });
        console.error(error);
      }
    },
    
    async exportarCsv() {
      try {
        const params = {
          status: this.filtros.status,
          prioridade: this.filtros.prioridade
        };
        
        const response = await this.$http.get('/tarefas-export/csv', {
          params,
          responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `tarefas_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        
        this.$bvToast.toast('Arquivo CSV exportado com sucesso!', {
          title: 'Sucesso',
          variant: 'success',
          solid: true
        });
      } catch (error) {
        this.$bvToast.toast('Erro ao exportar para CSV', {
          title: 'Erro',
          variant: 'danger',
          solid: true
        });
        console.error(error);
      }
    }
  }
};
</script>

---

<style scoped>
.tasks-page {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* ========== SELECTS DE FILTRO (Melhoria de Visibilidade) ========== */
/* Esta classe foi adicionada ao <b-form-select> */
.filter-select {
  background-color: #f8f9fa !important; /* Fundo mais claro para destacar */
  border: 1px solid #dee2e6 !important;
  border-radius: 8px !important; /* Borda mais arredondada */
  padding: 8px 12px !important; /* Mais padding para maior área de clique */
  font-size: 0.9rem !important;
  font-weight: 600 !important; /* Mais destaque */
  color: #343a40 !important;
  height: auto !important;
  /* Garante que o estilo seja aplicado sobre o padrão do Bootstrap */
  -webkit-appearance: none; 
  -moz-appearance: none;
  appearance: none;
  /* Adiciona um ícone de seta customizado para melhorar a estética */
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"); 
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 16px 12px;
}

.filter-select:focus {
  border-color: #0d6efd !important;
  box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25) !important;
}

/* Ajuste de alinhamento vertical para o botão "Limpar Filtros" */
.clear-filters-btn {
  margin-top: 5px; /* Para alinhar melhor com os selects */
}

/* ========== BOTÕES DO HEADER (CTA Principal) ========== */
/* Aplica-se ao botão Nova Tarefa */
.btn-new-task {
  font-size: 1rem !important;
  padding: 8px 16px !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.btn-new-task:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
}

/* Estilo para os botões de exportação agrupados */
.export-buttons .btn {
  font-size: 0.85rem !important;
  padding: 6px 12px !important;
  font-weight: 500;
}

/* ========== BOTÕES DE AÇÃO NA TABELA (Ajuste de Espaçamento) ========== */
/* Esta classe foi adicionada ao <b-button-group> da coluna 'actions' */
::v-deep .table-actions-group .btn {
  margin: 0 3px; /* Espaçamento horizontal sutil entre os botões */
  padding: 6px 8px !important;
}

::v-deep .btn-group .btn {
  /* Mantém estilos de botão de ação */
  padding: 6px 10px !important;
  border-radius: 6px !important;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

::v-deep .btn-outline-primary:hover,
::v-deep .btn-outline-danger:hover {
  /* Mantém estilos de hover com elevação */
  transform: translateY(-1px);
}

/* ========== ESTILOS GERAIS/EXISTENTES (Otimizados) ========== */

/* ========== CABEÇALHO DA TABELA ========== */
::v-deep .table th {
  background-color: #f1f3f5 !important;
  font-weight: 700;
  letter-spacing: 0.4px;
  color: #495057;
  font-size: 0.85rem;
  padding-top: 12px !important;
  padding-bottom: 12px !important;
  border-bottom: 2px solid #dee2e6 !important;
}

/* ========== CÉLULAS DA TABELA ========== */
::v-deep .table td {
  vertical-align: middle !important;
  font-size: 0.92rem;
  color: #212529 !important;
  padding: 14px 10px !important;
  border-color: #e9ecef !important;
}

/* ========== TEXTOS DAS CÉLULAS ========== */
::v-deep .titulo-tarefa {
  color: #212529 !important;
  font-size: 0.95rem;
}

::v-deep .descricao-tarefa {
  color: #6c757d !important;
  font-size: 0.85rem;
}

::v-deep .data-prazo span {
  color: #212529 !important;
  font-weight: 500;
}

::v-deep .text-dark {
  color: #212529 !important;
}

::v-deep .text-muted,
::v-deep small.text-muted {
  color: #6c757d !important;
}

/* ========== HOVER DA LINHA ========== */
::v-deep .table-hover tbody tr:hover {
  background-color: #f8f9fa !important;
  cursor: pointer;
}

/* ========== BADGES BASE ========== */
::v-deep .badge {
  font-size: 0.75rem !important;
  padding: 6px 12px !important;
  border-radius: 8px !important;
  text-transform: capitalize;
  letter-spacing: 0.3px;
}

/* ========== BADGE ID ========== */
::v-deep .id-badge.badge-secondary {
  background-color: #6c757d !important;
  color: #ffffff !important;
  font-weight: 700 !important;
}

/* ========== BADGES DE STATUS ========== */
::v-deep .status-badge.badge-warning {
  background-color: #ffc107 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  border: 1px solid #ffb300;
}

::v-deep .status-badge.badge-success {
  background-color: #198754 !important;
  color: #ffffff !important;
  font-weight: 700 !important;
  border: 1px solid #157347;
}

/* Adicionado estilo para Em Andamento (info) */
::v-deep .status-badge.badge-info {
  background-color: #0dcaf0 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  border: 1px solid #0ab4d6;
}

/* ========== BADGES DE PRIORIDADE ========== */
::v-deep .prioridade-badge.badge-secondary {
  background-color: #6c757d !important;
  color: #ffffff !important;
  font-weight: 700 !important;
  border: 1px solid #5c636a;
}

::v-deep .prioridade-badge.badge-warning {
  background-color: #ff9800 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  border: 1px solid #f57c00;
}

::v-deep .prioridade-badge.badge-danger {
  background-color: #dc3545 !important;
  color: #ffffff !important;
  font-weight: 700 !important;
  border: 1px solid #bb2d3b;
}

/* ========== ÍCONES NOS BADGES ========== */
::v-deep .badge .b-icon {
  vertical-align: middle;
  margin-right: 4px;
}

/* ========== PAGINAÇÃO ========== */
::v-deep .pagination .page-link {
  color: #0d6efd !important;
  border-radius: 5px !important;
  font-size: 0.85rem !important;
  font-weight: 500;
}

::v-deep .pagination .page-item.active .page-link {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
  color: white !important;
  font-weight: 600;
}

::v-deep .pagination .page-link:hover {
  background-color: #e7f1ff;
  color: #0d6efd !important;
}

/* ========== CARDS ========== */
::v-deep .card {
  border-radius: 10px !important;
  border: none;
}

::v-deep .card-body {
  border-radius: 10px;
}

::v-deep .shadow-sm {
  box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
}


/* ========== RESPONSIVIDADE ========== */
@media (max-width: 768px) {
  .tasks-page {
    padding: 10px;
  }
  
  ::v-deep .table {
    font-size: 0.85rem;
  }
  
  ::v-deep .badge {
    font-size: 0.7rem !important;
    padding: 4px 8px !important;
  }
  /* Ajuste para alinhar o título e o botão Nova Tarefa em mobile */
  .d-flex.align-items-center.justify-content-between {
    flex-wrap: wrap;
  }
  
  .btn-new-task.d-md-none {
    margin-right: 0 !important;
    margin-top: 10px;
    width: 100%;
  }

  /* Ajuste para alinhar os botões de exportação em mobile */
  .text-md-right.d-flex {
    justify-content: flex-start !important;
    margin-top: 10px;
  }
}
</style>