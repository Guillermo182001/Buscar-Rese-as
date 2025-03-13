<script>
export default {
  name: 'PaginationControls',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    totalPages: {
      type: Number,
      required: true
    },
    maxVisibleButtons: {
      type: Number,
      default: 5
    }
  },
  computed: {
    /**
     * Calcula los botones de página que deben mostrarse
     */
    paginationButtons() {
      const buttons = [];
      
      if (this.totalPages <= this.maxVisibleButtons) {
        // Si hay menos páginas que botones visibles, mostrar todas
        for (let i = 1; i <= this.totalPages; i++) {
          buttons.push(i);
        }
      } else {
        // Siempre mostrar la primera página
        buttons.push(1);
        
        let startPage = Math.max(2, this.currentPage - Math.floor(this.maxVisibleButtons / 2));
        let endPage = Math.min(this.totalPages - 1, startPage + this.maxVisibleButtons - 3);
        
        // Ajustar si estamos cerca del inicio
        if (startPage > 2) {
          buttons.push('...');
        }
        
        // Agregar páginas intermedias
        for (let i = startPage; i <= endPage; i++) {
          buttons.push(i);
        }
        
        // Ajustar si estamos cerca del final
        if (endPage < this.totalPages - 1) {
          buttons.push('...');
        }
        
        // Siempre mostrar la última página
        buttons.push(this.totalPages);
      }
      
      return buttons;
    },
    
    /**
     * Verifica si se puede ir a la página anterior
     */
    canGoPrevious() {
      return this.currentPage > 1;
    },
    
    /**
     * Verifica si se puede ir a la página siguiente
     */
    canGoNext() {
      return this.currentPage < this.totalPages;
    }
  },
  methods: {
    /**
     * Emite evento para cambiar de página
     */
    changePage(page) {
      if (page === '...' || page === this.currentPage) return;
      this.$emit('page-change', page);
    },
    
    /**
     * Va a la página anterior
     */
    previousPage() {
      if (this.canGoPrevious) {
        this.$emit('page-change', this.currentPage - 1);
      }
    },
    
    /**
     * Va a la página siguiente
     */
    nextPage() {
      if (this.canGoNext) {
        this.$emit('page-change', this.currentPage + 1);
      }
    }
  }
};
</script>

<template>
  <div v-if="totalPages > 1" class="pagination-container">
    <button 
      @click="previousPage" 
      :disabled="!canGoPrevious"
      class="pagination-button"
      :class="{ 'disabled': !canGoPrevious }"
      aria-label="Página anterior"
    >
      &laquo;
    </button>
    
    <button 
      v-for="(page, index) in paginationButtons" 
      :key="index"
      @click="changePage(page)"
      class="pagination-button"
      :class="{ 
        'active': page === currentPage,
        'ellipsis': page === '...'
      }"
      :disabled="page === '...'"
    >
      {{ page }}
    </button>
    
    <button 
      @click="nextPage" 
      :disabled="!canGoNext"
      class="pagination-button"
      :class="{ 'disabled': !canGoNext }"
      aria-label="Página siguiente"
    >
      &raquo;
    </button>
  </div>
</template>

<style scoped>
.pagination-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 20px 0;
  gap: 5px;
}

.pagination-button {
  display: flex;
  justify-content: center;
  align-items: center;
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border: 1px solid #ddd;
  background-color: white;
  color: #333;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-button:hover:not(.disabled):not(.active):not(.ellipsis) {
  background-color: #f5f5f5;
  border-color: #ccc;
}

.pagination-button.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination-button.disabled,
.pagination-button.ellipsis {
  cursor: default;
  color: #999;
}

.pagination-button.disabled {
  opacity: 0.5;
}
</style>

