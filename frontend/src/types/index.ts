export interface Proxy {
  id: number
  ip: string
  port: number
  type: 'http' | 'https' | 'socks4' | 'socks5'
  status: 'active' | 'inactive' | 'checking' | 'error'
  username: string | null
  password: string | null
  last_checked_at: string | null
  created_at: string
  updated_at: string
}
