import { defineStore } from 'pinia'
import axios from 'axios'
import type { Proxy } from '@/types'

interface ProxyState {
  proxies: Proxy[]
  loading: boolean
  error: string | null
}

export const useProxyStore = defineStore('proxy', {
  state: (): ProxyState => ({
    proxies: [],
    loading: false,
    error: null,
  }),

  getters: {
    activeProxies: (state) => state.proxies.filter((p) => p.status === 'active'),
  },

  actions: {
    async fetchAll() {
      this.loading = true
      this.error = null
      try {
        const { data } = await axios.get<{ data: Proxy[] }>('/api/proxies')
        this.proxies = data.data
      } catch (e: unknown) {
        this.error = e instanceof Error ? e.message : 'Failed to fetch proxies'
      } finally {
        this.loading = false
      }
    },

    async create(proxy: Omit<Proxy, 'id' | 'created_at' | 'updated_at' | 'status' | 'last_checked_at'>) {
      const { data } = await axios.post<{ data: Proxy }>('/api/proxies', proxy)
      this.proxies.unshift(data.data)
      return data.data
    },

    async update(id: number, proxy: Partial<Proxy>) {
      const { data } = await axios.put<{ data: Proxy }>(`/api/proxies/${id}`, proxy)
      const idx = this.proxies.findIndex((p) => p.id === id)
      if (idx !== -1) this.proxies[idx] = data.data
      return data.data
    },

    async remove(id: number) {
      await axios.delete(`/api/proxies/${id}`)
      this.proxies = this.proxies.filter((p) => p.id !== id)
    },

    async check(id: number) {
      const { data } = await axios.post<{ data: Proxy }>(`/api/proxies/${id}/check`)
      const idx = this.proxies.findIndex((p) => p.id === id)
      if (idx !== -1) this.proxies[idx] = data.data
      return data.data
    },

    async checkAll() {
      this.loading = true
      this.error = null
      try {
        const { data } = await axios.post<{ data: Proxy[] }>('/api/proxies/check-all')
        for (const proxy of data.data) {
          const idx = this.proxies.findIndex((p) => p.id === proxy.id)
          if (idx !== -1) this.proxies[idx] = proxy
        }
      } catch (e: unknown) {
        this.error = e instanceof Error ? e.message : 'Failed to check all proxies'
      } finally {
        this.loading = false
      }
    },
  },
})
