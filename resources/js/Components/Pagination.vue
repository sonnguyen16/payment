<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  links: Array,
  meta: Object
})

// Tạo pagination links thông minh
const smartLinks = computed(() => {
  if (!props.links || props.links.length <= 3) {
    return props.links
  }

  const links = [...props.links]
  const currentPage = props.meta?.current_page || 1
  const lastPage = props.meta?.last_page || 1
  
  // Nếu có ít hơn 10 trang, hiển thị tất cả
  if (lastPage <= 10) {
    return links
  }

  // Lấy prev và next buttons
  const prevButton = links[0]
  const nextButton = links[links.length - 1]
  
  // Tạo smart pagination
  const smartPagination = [prevButton]
  
  // Luôn hiển thị trang 1
  if (currentPage > 4) {
    smartPagination.push({
      url: links[1].url.replace(/page=\d+/, 'page=1'),
      label: '1',
      active: false
    })
    
    // Thêm dấu ... nếu cần
    if (currentPage > 5) {
      smartPagination.push({
        url: null,
        label: '...',
        active: false
      })
    }
  }
  
  // Hiển thị các trang xung quanh trang hiện tại
  const start = Math.max(1, currentPage - 2)
  const end = Math.min(lastPage, currentPage + 2)
  
  for (let i = start; i <= end; i++) {
    const existingLink = links.find(link => 
      link.label === i.toString() || 
      (link.active && i === currentPage)
    )
    
    if (existingLink) {
      smartPagination.push(existingLink)
    } else {
      smartPagination.push({
        url: links[1].url.replace(/page=\d+/, `page=${i}`),
        label: i.toString(),
        active: i === currentPage
      })
    }
  }
  
  // Luôn hiển thị trang cuối
  if (currentPage < lastPage - 3) {
    // Thêm dấu ... nếu cần
    if (currentPage < lastPage - 4) {
      smartPagination.push({
        url: null,
        label: '...',
        active: false
      })
    }
    
    smartPagination.push({
      url: links[1].url.replace(/page=\d+/, `page=${lastPage}`),
      label: lastPage.toString(),
      active: false
    })
  }
  
  smartPagination.push(nextButton)
  
  return smartPagination
})

// Jump to page functionality
const jumpPage = ref('')
const showJumpInput = ref(false)

const jumpToPage = () => {
  const page = parseInt(jumpPage.value)
  const lastPage = props.meta?.last_page || 1
  
  if (page >= 1 && page <= lastPage && props.links?.[1]?.url) {
    const url = props.links[1].url.replace(/page=\d+/, `page=${page}`)
    router.get(url, {}, { preserveScroll: true })
    jumpPage.value = ''
    showJumpInput.value = false
  }
}

const toggleJumpInput = () => {
  showJumpInput.value = !showJumpInput.value
  if (showJumpInput.value) {
    setTimeout(() => {
      document.querySelector('.jump-page-input')?.focus()
    }, 100)
  }
}
</script>

<template>
  <div class="card-footer clearfix" v-if="links && links.length > 3">
    <div class="row">
      <div class="col-sm-6">
        <div class="dataTables_info" v-if="meta">
          Hiển thị {{ meta.from || 0 }} đến {{ meta.to || 0 }} 
          trong tổng số {{ meta.total || 0 }} bản ghi
        </div>
      </div>
      <div class="col-sm-6">
        <div class="float-right d-flex align-items-center">
          <!-- Jump to page input -->
          <div class="jump-to-page mr-3" v-if="meta && meta.last_page > 10">
            <div class="input-group input-group-sm" v-if="showJumpInput">
              <input 
                type="number" 
                class="form-control jump-page-input" 
                v-model="jumpPage"
                :placeholder="`1-${meta.last_page}`"
                :min="1"
                :max="meta.last_page"
                @keyup.enter="jumpToPage"
                @blur="showJumpInput = false"
                style="width: 80px;"
              >
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" @click="jumpToPage">
                  <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            </div>
            <button 
              v-else
              class="btn btn-sm btn-outline-secondary"
              @click="toggleJumpInput"
              title="Nhảy đến trang"
            >
              <i class="fas fa-search"></i> Trang
            </button>
          </div>
          
          <!-- Pagination navigation -->
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm m-0">
              <li 
                v-for="(link, index) in smartLinks" 
                :key="`${link.label}-${index}`" 
                class="page-item" 
                :class="{ 
                  active: link.active, 
                  disabled: !link.url,
                  'ellipsis': link.label === '...'
                }"
              >
                <Link 
                  v-if="link.url" 
                  :href="link.url" 
                  class="page-link" 
                  v-html="link.label"
                  preserve-scroll
                ></Link>
                <span 
                  v-else 
                  class="page-link" 
                  v-html="link.label"
                ></span>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dataTables_info {
  padding-top: 8px;
  font-size: 0.875rem;
  color: #6c757d;
}

.pagination {
  margin-bottom: 0;
}

.page-link {
  font-size: 0.875rem;
  padding: 0.375rem 0.75rem;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  cursor: auto;
  background-color: #fff;
  border-color: #dee2e6;
}

.page-item.ellipsis .page-link {
  border: none;
  background: none;
  color: #6c757d;
  cursor: default;
}

.page-item.ellipsis .page-link:hover {
  background: none;
  color: #6c757d;
}

.jump-to-page .input-group {
  min-width: 120px;
}

.jump-to-page .form-control {
  text-align: center;
}

.jump-to-page .btn {
  white-space: nowrap;
}
</style>
