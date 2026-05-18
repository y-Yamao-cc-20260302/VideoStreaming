import { useCallback, useState } from 'react'

export interface Toast {
  id: string
  message: string
  type: 'success' | 'error' | 'info'
  action?: { label: string; onClick: () => void }
}

export function useToast() {
  const [toasts, setToasts] = useState<Toast[]>([])

  const show = useCallback(
    (message: string, type: Toast['type'] = 'success', action?: Toast['action']) => {
      const id = crypto.randomUUID()
      setToasts((prev) => [...prev, { id, message, type, action }])
      setTimeout(() => setToasts((prev) => prev.filter((t) => t.id !== id)), 5000)
    },
    []
  )

  const dismiss = useCallback((id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id))
  }, [])

  return { toasts, show, dismiss }
}
