<template>
  <span
      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
      :class="colorMap[status]"
  >
    <span
        v-if="status === 'active'"
        class="w-1.5 h-1.5 rounded-full bg-emerald-500"
    />
    <span
        v-else-if="status === 'error'"
        class="w-1.5 h-1.5 rounded-full bg-rose-500"
    />
    <span
        v-else-if="status === 'checking'"
        class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"
    />
    <span
        v-else
        class="w-1.5 h-1.5 rounded-full bg-neutral-400"
    />
    {{ labelMap[status] }}
  </span>
</template>
<script setup lang="ts">
defineProps<{
  status: 'active' | 'inactive' | 'checking' | 'error'
}>()

const colorMap: Record<string, string> = {
  active: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
  inactive: 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300',
  checking: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
  error: 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200',
}

const labelMap: Record<string, string> = {
  active: 'Active',
  inactive: 'Inactive',
  checking: 'Checking…',
  error: 'Error',
}
</script>
