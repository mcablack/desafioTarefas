<template>
  <div class="tasks-page">

    <!-- HEADER -->
    <div class="page-header">
      <h2>📋 Gerenciamento de Tarefas</h2>
      <div class="header-actions">
        <button class="btn-primary" @click="openCreateModal">+ Nova Tarefa</button>
        <button class="btn-csv" @click="exportCSV">📥 Exportar CSV</button>
      </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-bar">
      <div class="filter-group">
        <label>Status:</label>
        <select v-model="filtros.status" @change="loadTarefas" class="filter-select">
          <option value="">Todos</option>
          <option value="pendente">Pendente</option>
          <option value="em_andamento">Em Andamento</option>
          <option value="concluida">Concluída</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Prioridade:</label>
        <select v-model="filtros.prioridade" @change="loadTarefas" class="filter-select">
          <option value="">Todas</option>
          <option value="baixa">Baixa</option>
          <option value="media">Média</option>
          <option value="alta">Alta</option>
        </select>
      </div>

      <button class="btn-secondary" @click="limparFiltros">Limpar Filtros</button>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading">Carregando tarefas...</div>

    <!-- TABELA -->
    <div v-else class="table-container">
      <table class="table-tasks">
        <thead>
          <tr>
            <th>#</th>
            <th>Título</th>
            <th>Status</th>
            <th>Prioridade</th>
            <th>Data Limite</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tarefa in tarefas" :key="tarefa.id">
            <td>{{ tarefa.id }}</td>
            <td>
              <strong>{{ tarefa.titulo }}</strong>
              <p class="descricao-preview">{{ tarefa.descricao || 'Sem descrição' }}</p>
            </td>
            <td>
              <span :class="['badge', 'badge-' + tarefa.status]">
                {{ formatStatus(tarefa.status) }}
              </span>
            </td>
            <td>
              <span :class="['badge-prioridade', 'prioridade-' + tarefa.prioridade]">
                {{ formatPrioridade(tarefa.prioridade) }}
              </span>
            </td>
            <td>{{ formatDate(tarefa.data_limite) }}</td>
            <td class="actions">
              <button class="btn-icon btn-edit" @click="openEditModal(tarefa)" title="Editar">✏️</button>
              <button class="btn-icon btn-delete" @click="confirmDelete(tarefa)" title="Excluir">🗑️</button>
            </td>
          </tr>
          <tr v-if="tarefas.length === 0">
            <td colspan="6" class="empty-state">Nenhuma tarefa encontrada. Crie sua primeira tarefa!</td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINAÇÃO -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="btn-page">Anterior</button>
        <span class="page-info">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="btn-page">Próxima</button>
      </div>
    </div>

    <!-- MODAIS -->
    <TaskFormModal v-if="showModal" :tarefa="tarefaEditando" @close="closeModal" @saved="onTarefaSaved" />
    <ConfirmModal v-if="showDeleteModal" titulo="Confirmar Exclusão" mensagem="Tem certeza que deseja excluir esta tarefa?" @confirm="deleteTarefa" @cancel="showDeleteModal = false" />
  </div>
</template>

<script>
import TaskFormModal from './TaskFormModal.vue';
import ConfirmModal from './ConfirmModal.vue';

export default {
  name: "TaskList",
  components: { TaskFormModal, ConfirmModal },
  data() {
    return {
      tarefas: [],
      loading: false,
      showModal: false,
      showDeleteModal: false,
      tarefaEditando: null,
      tarefaParaDeletar: null,
      filtros: { status: '', prioridade: '' },
      pagination: { current_page: 1, last_page: 1, total: 0 }
    };
  },
  mounted() { this.loadTarefas(); },
  methods: {
    async loadTarefas(page = 1) {
      this.loading = true;
      try {
        const params = { page, status: this.filtros.status, prioridade: this.filtros.prioridade };
        const response = await this.$http.get('/tarefas', { params });
        this.tarefas = response.data.data;
        this.pagination = { current_page: response.data.current_page, last_page: response.data.last_page, total: response.data.total };
      } catch (error) { console.error(error); }
      this.loading = false;
    },
    openCreateModal() { this.tarefaEditando = null; this.showModal = true; },
    openEditModal(tarefa) { this.tarefaEditando = { ...tarefa }; this.showModal = true; },
    closeModal() { this.showModal = false; this.tarefaEditando = null; },
    onTarefaSaved() { this.closeModal(); this.loadTarefas(this.pagination.current_page); },
    confirmDelete(tarefa) { this.tarefaParaDeletar = tarefa; this.showDeleteModal = true; },
    async deleteTarefa() {
      try { await this.$http.delete(`/tarefas/${this.tarefaParaDeletar.id}`); this.showDeleteModal = false; this.loadTarefas(this.pagination.current_page); } 
      catch (error) { console.error(error); }
    },
    limparFiltros() { this.filtros.status = ''; this.filtros.prioridade = ''; this.loadTarefas(); },
    changePage(page) { this.loadTarefas(page); },
    formatStatus(status) { const map = { pendente:'Pendente', em_andamento:'Em Andamento', concluida:'Concluída'}; return map[status] || status; },
    formatPrioridade(prioridade) { const map = { baixa:'Baixa', media:'Média', alta:'Alta'}; return map[prioridade] || prioridade; },
    formatDate(date) { return date ? new Date(date).toLocaleDateString('pt-BR') : 'Sem prazo'; },
    exportCSV() {
      let csv = 'ID,Título,Status,Prioridade,Data Limite\n';
      this.tarefas.forEach(t => csv += `${t.id},"${t.titulo}",${this.formatStatus(t.status)},${this.formatPrioridade(t.prioridade)},${this.formatDate(t.data_limite)}\n`);
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.setAttribute("download", "tarefas.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
};
</script>

<style scoped>
.tasks-page { max-width: 1200px; margin: 0 auto; padding: 16px; }

/* Header */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; }
.page-header h2 { font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
.header-actions { display: flex; gap: 12px; flex-wrap: wrap; }

/* Botões */
.btn-primary { background:#1a73e8;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-primary:hover { background:#1557b0; transform: translateY(-1px); }

.btn-csv { background:#10b981;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-csv:hover { background:#059669; transform: translateY(-1px); }

.btn-secondary { background:#6b7280;color:white;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:14px; transition:all 0.2s;}
.btn-secondary:hover { background:#4b5563; }

/* Filters */
.filters-bar { display:flex; flex-wrap: wrap; gap:16px; padding:16px; background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
.filter-group { display:flex; flex-direction:column; gap:4px; }
.filter-select { padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; min-width:140px; }

/* Table */
.table-container { background:white; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.table-tasks { width:100%; border-collapse:collapse; }
.table-tasks th, .table-tasks td { padding:14px 16px; text-align:left; }
.table-tasks thead { background:#f3f4f6; font-weight:600; color:#374151; text-transform:uppercase; font-size:13px; }
.table-tasks tbody tr { transition: background 0.2s; border-top:1px solid #e5e7eb; }
.table-tasks tbody tr:hover { background:#eff6ff; }
.descricao-preview { font-size:13px; color:#6b7280; margin-top:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:300px; }

/* Badges */
.badge, .badge-prioridade { padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; text-transform:uppercase; }
.badge-pendente { background:#fef3c7; color:#92400e; }
.badge-em_andamento { background:#dbeafe; color:#1e40af; }
.badge-concluida { background:#d1fae5; color:#065f46; }
.prioridade-baixa { background:#e5e7eb; color:#374151; }
.prioridade-media { background:#fef3c7; color:#92400e; }
.prioridade-alta { background:#fee2e2; color:#991b1b; }

/* Actions */
.actions { display:flex; gap:8px; }
.btn-icon { background:none;border:none;font-size:18px;cursor:pointer;padding:4px 8px;border-radius:6px;transition:background 0.2s; }
.btn-edit:hover { background:#dbeafe; }
.btn-delete:hover { background:#fee2e2; }

/* Empty state */
.empty-state { text-align:center; padding:40px; color:#9ca3af; font-style:italic; }

/* Pagination */
.pagination { display:flex; justify-content:center; align-items:center; gap:16px; padding:20px; border-top:1px solid #e5e7eb; }
.btn-page { padding:8px 16px; border:1px solid #d1d5db; border-radius:8px; background:white; cursor:pointer; }
.btn-page:hover:not(:disabled) { background:#f3f4f6; }
.btn-page:disabled { opacity:0.5; cursor:not-allowed; }
.page-info { color:#6b7280; font-size:14px; }

/* Responsividade */
@media(max-width:768px){
  .filters-bar, .page-header { flex-direction:column; align-items:flex-start; }
  .header-actions { width:100%; justify-content:flex-start; gap:8px; }
}
</style>
