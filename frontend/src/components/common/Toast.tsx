import type { Toast as ToastType } from '../../hooks/useToast'

interface Props {
  toasts: ToastType[]
  onDismiss: (id: string) => void
}

export default function ToastContainer({ toasts, onDismiss }: Props) {
  return (
    <div className="fixed bottom-4 left-1/2 -translate-x-1/2 flex flex-col gap-2 z-50 w-full max-w-sm px-4">
      {toasts.map((toast) => (
        <div
          key={toast.id}
          className={`flex items-center justify-between gap-3 px-4 py-3 rounded shadow-lg text-sm text-white
            ${toast.type === 'error' ? 'bg-red-600' : toast.type === 'info' ? 'bg-blue-600' : 'bg-gray-900'}`}
        >
          <span>{toast.message}</span>
          <div className="flex items-center gap-2 shrink-0">
            {toast.action && (
              <button
                onClick={() => { toast.action!.onClick(); onDismiss(toast.id) }}
                className="underline font-medium"
              >
                {toast.action.label}
              </button>
            )}
            <button onClick={() => onDismiss(toast.id)} className="opacity-70 hover:opacity-100">✕</button>
          </div>
        </div>
      ))}
    </div>
  )
}
