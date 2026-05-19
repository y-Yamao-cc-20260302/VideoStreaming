import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import { authApi } from '../api/auth'
import type { User } from '../types'

interface AuthContextValue {
  user: User | null
  isLoading: boolean
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  register: (payload: {
    name: string
    nickname?: string
    email: string
    password: string
    password_confirmation: string
  }) => Promise<void>
  logout: () => Promise<void>
  refreshUser: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | null>(null)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  const fetchUser = useCallback(async () => {
    const { data } = await authApi.me()
    setUser(data)
  }, [])

  useEffect(() => {
    const token = localStorage.getItem('access_token')
    if (!token) {
      setIsLoading(false)
      return
    }
    fetchUser()
      .catch(() => localStorage.removeItem('access_token'))
      .finally(() => setIsLoading(false))
  }, [fetchUser])

  const login = useCallback(
    async (email: string, password: string) => {
      const { data } = await authApi.login(email, password)
      localStorage.setItem('access_token', data.access_token)
      await fetchUser()
    },
    [fetchUser]
  )

  const register = useCallback(
    async (payload: {
      name: string
      nickname?: string
      email: string
      password: string
      password_confirmation: string
    }) => {
      const { data } = await authApi.register(payload)
      localStorage.setItem('access_token', data.access_token)
      await fetchUser()
    },
    [fetchUser]
  )

  const logout = useCallback(async () => {
    await authApi.logout().catch(() => {})
    localStorage.removeItem('access_token')
    setUser(null)
  }, [])

  return (
    <AuthContext.Provider
      value={{
        user,
        isLoading,
        isAuthenticated: !!user,
        login,
        register,
        logout,
        refreshUser: fetchUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
