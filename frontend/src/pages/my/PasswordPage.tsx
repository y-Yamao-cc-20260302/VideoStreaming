import { useState } from 'react'
import { authApi } from '../../api/auth'

export default function PasswordPage() {
  const [form, setForm] = useState({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  })
  const [message, setMessage] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setMessage(null)
    setError(null)
    if (form.new_password !== form.new_password_confirmation) {
      setError('新しいパスワードが一致しません')
      return
    }
    setSaving(true)
    try {
      await authApi.changePassword(
        form.current_password,
        form.new_password,
        form.new_password_confirmation
      )
      setMessage('パスワードを変更しました')
      setForm({ current_password: '', new_password: '', new_password_confirmation: '' })
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } }
      setError(e.response?.data?.message ?? '変更に失敗しました')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="max-w-md">
      <h1 className="text-lg font-bold mb-4">パスワード変更</h1>
      {message && <p className="text-sm text-green-700 mb-3">{message}</p>}
      {error && <p className="text-sm text-red-600 mb-3">{error}</p>}
      <form onSubmit={handleSubmit} className="space-y-4">
        {[
          { key: 'current_password', label: '現在のパスワード' },
          { key: 'new_password', label: '新しいパスワード（8文字以上）' },
          { key: 'new_password_confirmation', label: '新しいパスワード（確認）' },
        ].map((f) => (
          <div key={f.key}>
            <label className="block text-sm font-medium mb-1">{f.label}</label>
            <input
              type="password"
              value={form[f.key as keyof typeof form]}
              onChange={(e) => setForm({ ...form, [f.key]: e.target.value })}
              className="w-full border border-gray-200 px-3 py-2 text-sm"
              required
            />
          </div>
        ))}
        <button
          type="submit"
          disabled={saving}
          className="bg-gray-900 text-white px-4 py-2 text-sm disabled:opacity-50"
        >
          {saving ? '変更中...' : '変更'}
        </button>
      </form>
    </div>
  )
}
