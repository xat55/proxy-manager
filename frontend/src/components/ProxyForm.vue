<template>
  <form
      @submit.prevent="onSubmit"
      class="space-y-4"
  >
    <div>
      <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">IP Address</label>
      <input
          v-model="form.ip"
          type="text"
          required
          placeholder="192.168.1.1"
          class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
      >
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Port</label>
        <input
            v-model.number="form.port"
            type="number"
            required
            min="1"
            max="65535"
            placeholder="3128"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
      </div>

      <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Type</label>
        <select
            v-model="form.type"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
          <option value="http">HTTP</option>
          <option value="https">HTTPS</option>
          <option value="socks4">SOCKS4</option>
          <option value="socks5">SOCKS5</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Username (optional)</label>
        <input
            v-model="form.username"
            type="text"
            placeholder="user"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
      </div>

      <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Password (optional)</label>
        <input
            v-model="form.password"
            type="password"
            placeholder="****"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        >
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <button
          type="button"
          @click="emit('cancel')"
          class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors"
      >
        Cancel
      </button>
      <button
          type="submit"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
      >
        {{ isEdit ? 'Update' : 'Add' }} Proxy
      </button>
    </div>
  </form>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Proxy } from '@/types'

const props = defineProps<{
  proxy?: Proxy | null
}>()

const emit = defineEmits<{
  save: [data: ProxyFormData]
  cancel: []
}>()

export interface ProxyFormData {
  ip: string
  port: number
  type: 'http' | 'https' | 'socks4' | 'socks5'
  username: string | null
  password: string | null
}

const form = ref<ProxyFormData>({
  ip: '',
  port: 3128,
  type: 'http',
  username: null,
  password: null,
})

const isEdit = ref(false)

watch(
  () => props.proxy,
  (proxy) => {
    if (proxy) {
      isEdit.value = true
      form.value = {
        ip: proxy.ip,
        port: proxy.port,
        type: proxy.type,
        username: proxy.username,
        password: proxy.password,
      }
    } else {
      isEdit.value = false
      form.value = { ip: '', port: 3128, type: 'http', username: null, password: null }
    }
  },
  { immediate: true },
)

function onSubmit() {
  emit('save', { ...form.value })
}
</script>
