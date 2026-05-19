import { useEffect, useState } from 'react'
import { authApi } from '../../api/auth'
import { useAuth } from '../../contexts/AuthContext'

export default function ProfilePage() {
  const { user, refreshUser } = useAuth()
  const [form, setForm] = useState({ name: '', nickname: '', email: '' })
  const [message, setMessage] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    if (user) {
      setForm({ name: user.name, nickname: user.nickname ?? '', email: user.email })
    }
  }, [user])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setMessage(null)
    setError(null)
    setSaving(true)
    try {
      await authApi.updateProfile({
        name: form.name,
        nickname: form.nickname || null,
        email: form.email,
      })
      await refreshUser()
      setMessage('プロフィールを更新しました')
    } catch {
      setError('更新に失敗しました')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="max-w-md">
      <h1 className="text-lg font-bold mb-4">プロフィール</h1>
      {message && <p className="text-sm text-green-700 mb-3">{message}</p>}
      {error && <p className="text-sm text-red-600 mb-3">{error}</p>}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium mb-1">氏名</label>
          <input
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
            required
          />
        </div>
        <div>
          <label className="block text-sm font-medium mb-1">ニックネーム</label>
          <input
            value={form.nickname}
            onChange={(e) => setForm({ ...form, nickname: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label className="block text-sm font-medium mb-1">メールアドレス</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
            required
          />
        </div>
        <button
          type="submit"
          disabled={saving}
          className="bg-gray-900 text-white px-4 py-2 text-sm disabled:opacity-50"
        >
          {saving ? '保存中...' : '保存'}
        </button>
      </form>
    </div>
  )
}
