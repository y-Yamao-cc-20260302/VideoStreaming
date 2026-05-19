import { useEffect, useState } from 'react'
import {
  subscriptionPlansApi,
  subscriptionsApi,
} from '../../api/subscriptions'
import { useAuth } from '../../contexts/AuthContext'
import type { Subscription, SubscriptionPlan } from '../../types'
import { formatDate, formatJpy } from '../../utils/format'

export default function SubscriptionPage() {
  const { refreshUser } = useAuth()
  const [plans, setPlans] = useState<SubscriptionPlan[]>([])
  const [current, setCurrent] = useState<Subscription | null>(null)
  const [message, setMessage] = useState<string | null>(null)
  const [busy, setBusy] = useState(false)

  const load = async () => {
    const [p, c] = await Promise.all([
      subscriptionPlansApi.list(),
      subscriptionsApi.current(),
    ])
    setPlans(p.data.data)
    setCurrent(c.data)
  }

  useEffect(() => {
    load().catch(() => {})
  }, [])

  const subscribe = async (code: string) => {
    setBusy(true)
    setMessage(null)
    try {
      await subscriptionsApi.subscribe(code)
      await load()
      await refreshUser()
      setMessage('プランを変更しました')
    } finally {
      setBusy(false)
    }
  }

  const cancel = async () => {
    if (!confirm('現在のプランを解約しますか？')) return
    setBusy(true)
    try {
      await subscriptionsApi.cancel()
      await load()
      await refreshUser()
      setMessage('プランを解約しました')
    } finally {
      setBusy(false)
    }
  }

  return (
    <div>
      <h1 className="text-lg font-bold mb-4">プラン管理</h1>
      {message && <p className="text-sm text-green-700 mb-3">{message}</p>}
      <div className="border p-4 mb-6 bg-gray-50">
        <h2 className="text-sm font-semibold mb-2">現在のプラン</h2>
        {current ? (
          <div className="text-sm">
            <p>
              {current.plan_name}（{formatJpy(current.price_jpy ?? 0)} / 月）
            </p>
            <p className="text-xs text-gray-500 mt-1">
              開始: {formatDate(current.started_at)}
            </p>
            <button
              onClick={cancel}
              disabled={busy}
              className="mt-3 text-sm border border-red-400 text-red-600 px-3 py-1.5"
            >
              解約する
            </button>
          </div>
        ) : (
          <p className="text-sm text-gray-500">未加入</p>
        )}
      </div>

      <h2 className="text-sm font-semibold mb-3">利用可能なプラン</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {plans.map((p) => (
          <div key={p.id} className="border p-4 flex flex-col">
            <h3 className="font-bold">{p.name}</h3>
            <p className="text-2xl my-2">{formatJpy(p.price_jpy)}<span className="text-xs">/月</span></p>
            <p className="text-xs text-gray-600 flex-1 whitespace-pre-wrap">{p.description}</p>
            <button
              onClick={() => subscribe(p.code)}
              disabled={busy || current?.plan_code === p.code}
              className="mt-3 bg-gray-900 text-white px-4 py-2 text-sm disabled:opacity-50"
            >
              {current?.plan_code === p.code ? '加入中' : 'このプランにする'}
            </button>
          </div>
        ))}
      </div>
    </div>
  )
}
