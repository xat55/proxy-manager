<template>
  <div class="min-h-dvh bg-neutral-50 dark:bg-neutral-900">
    <div class="max-w-5xl mx-auto px-4 py-8">
      <!-- Header -->
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">Proxy Manager</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
          Manage and monitor your proxy servers
        </p>
      </header>

      <!-- Actions -->
      <div class="flex items-center gap-3 mb-6">
        <button
            @click="openCreateForm"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Proxy
        </button>
        <button
            @click="store.checkAll()"
            :disabled="store.loading"
            class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-800 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors disabled:opacity-50 flex items-center gap-2"
        >
          <svg
              class="w-4 h-4"
              :class="{ 'animate-spin': store.loading }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          Check All
        </button>
      </div>

      <!-- Form Modal -->
      <div
          v-if="showForm"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
          @click.self="closeForm"
      >
        <div class="w-full max-w-lg mx-4 bg-white dark:bg-neutral-800 rounded-xl shadow-xl p-6">
          <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
            {{ editingProxy ? 'Edit Proxy' : 'Add Proxy' }}
          </h2>
          <ProxyForm
              :proxy="editingProxy"
              @save="handleSave"
              @cancel="closeForm"
          />
        </div>
      </div>

      <!-- Loading -->
      <div
          v-if="store.loading && store.proxies.length === 0"
          class="text-center py-12 text-neutral-500"
      >
        Loading proxies…
      </div>

      <!-- Error -->
      <div
          v-if="store.error"
          class="mb-4 px-4 py-3 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-lg text-sm"
      >
        {{ store.error }}
      </div>

      <!-- Empty state -->
      <div
          v-if="!store.loading && store.proxies.length === 0 && !store.error"
          class="text-center py-16"
      >
        <p class="text-neutral-400 dark:text-neutral-500 text-lg">No proxies yet</p>
        <p class="text-neutral-400 dark:text-neutral-500 text-sm mt-1">Click "Add Proxy" to get started</p>
      </div>

      <!-- Table -->
      <div
          v-if="store.proxies.length > 0"
          class="overflow-hidden bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700"
      >
        <table class="w-full text-sm">
          <thead>
          <tr class="bg-neutral-50 dark:bg-neutral-800/50 border-b border-neutral-200 dark:border-neutral-700">
            <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-400">Address</th>
            <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-400">Type</th>
            <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-400">Status</th>
            <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-400">Last Checked</th>
            <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-400">Actions</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
          <tr
              v-for="proxy in store.proxies"
              :key="proxy.id"
              class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors"
          >
            <td class="px-4 py-3 font-mono text-sm text-neutral-900 dark:text-neutral-100">
              {{ proxy.ip }}:{{ proxy.port }}
            </td>
            <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300 uppercase">
              {{ proxy.type }}
            </td>
            <td class="px-4 py-3">
              <ProxyStatusBadge :status="proxy.status" />
            </td>
            <td class="px-4 py-3 text-neutral-500 dark:text-neutral-400 text-xs">
              {{ formatDate(proxy.last_checked_at) }}
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                    @click="handleCheck(proxy)"
                    :disabled="proxy.status === 'checking'"
                    class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors disabled:opacity-50"
                >
                  Check
                </button>
                <button
                    @click="openEditForm(proxy)"
                    class="px-3 py-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-700 rounded-md hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors"
                >
                  Edit
                </button>
                <button
                    @click="handleDelete(proxy)"
                    class="px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 rounded-md hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer stats -->
      <div
          v-if="store.proxies.length > 0"
          class="mt-4 text-xs text-neutral-400 dark:text-neutral-500 text-center"
      >
        {{ store.proxies.length }} proxy{{ store.proxies.length !== 1 ? 'ies' : 'y' }} total
        · {{ store.activeProxies.length }} active
        · Auto-refreshes every 5 minutes
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { useProxyStore } from '@/stores/proxyStore'
import ProxyStatusBadge from '@/components/ProxyStatusBadge.vue'
import ProxyForm from '@/components/ProxyForm.vue'
import type { Proxy } from '@/types'
import type { ProxyFormData } from '@/components/ProxyForm.vue'

const store = useProxyStore()

const showForm = ref(false)
const editingProxy = ref<Proxy | null>(null)
let refreshInterval: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  store.fetchAll()
  refreshInterval = setInterval(() => store.fetchAll(), 5 * 60 * 1000)
})

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
})

function openCreateForm() {
  editingProxy.value = null
  showForm.value = true
}

function openEditForm(proxy: Proxy) {
  editingProxy.value = proxy
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editingProxy.value = null
}

async function handleSave(data: ProxyFormData) {
  try {
    if (editingProxy.value) {
      await store.update(editingProxy.value.id, data)
    } else {
      await store.create(data)
    }
    closeForm()
  } catch (e: unknown) {
    alert(e instanceof Error ? e.message : 'Operation failed')
  }
}

async function handleDelete(proxy: Proxy) {
  if (!confirm(`Delete proxy ${proxy.ip}:${proxy.port}?`)) return
  try {
    await store.remove(proxy.id)
  } catch (e: unknown) {
    alert(e instanceof Error ? e.message : 'Delete failed')
  }
}

async function handleCheck(proxy: Proxy) {
  try {
    await store.check(proxy.id)
  } catch (e: unknown) {
    alert(e instanceof Error ? e.message : 'Check failed')
  }
}

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString()
}
</script>
