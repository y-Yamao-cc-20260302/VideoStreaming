import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { subscriptionPlansApi } from '../api/subscriptions'
import { useAuth } from '../contexts/AuthContext'
import type { SubscriptionPlan } from '../types'
import { formatJpy } from '../utils/format'

export default function PlansPage() {
  const { isAuthenticated } = useAuth()
  const [plans, setPlans] = useState<SubscriptionPlan[]>([])

  useEffect(() => {
    subscriptionPlansApi.list().then((r) => setPlans(r.data.data))
  }, [])

  return (
    <div>
      <h1 className="text-xl font-bold mb-6">料金プラン</h1>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {plans.map((p) => (
          <div key={p.id} className="border bg-white p-6 flex flex-col">
            <h2 className="text-lg font-bold">{p.name}</h2>
            <p className="text-3xl my-3">
              {formatJpy(p.price_jpy)}
              <span className="text-sm">/月</span>
            </p>
            <p className="text-sm text-gray-600 flex-1 whitespace-pre-wrap">{p.description}</p>
            {isAuthenticated ? (
              <Link
                to="/my/subscription"
                className="mt-4 bg-gray-900 text-white px-4 py-2 text-sm text-center"
              >
                プランを選択
              </Link>
            ) : (
              <Link
                to="/register"
                className="mt-4 bg-gray-900 text-white px-4 py-2 text-sm text-center"
              >
                登録して始める
              </Link>
            )}
          </div>
        ))}
      </div>
    </div>
  )
}
