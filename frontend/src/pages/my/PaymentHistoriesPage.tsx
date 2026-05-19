import { useEffect, useState } from 'react'
import Pagination from '../../components/common/Pagination'
import { paymentHistoriesApi } from '../../api/subscriptions'
import type { Paginated, PaymentHistoryItem } from '../../types'
import { formatDateTime, formatJpy } from '../../utils/format'

export default function PaymentHistoriesPage() {
  const [result, setResult] = useState<Paginated<PaymentHistoryItem> | null>(null)
  const [page, setPage] = useState(1)

  useEffect(() => {
    paymentHistoriesApi.list(page).then((r) => setResult(r.data))
  }, [page])

  return (
    <div>
      <h1 className="text-lg font-bold mb-4">課金履歴</h1>
      <table className="w-full text-sm">
        <thead className="border-b">
          <tr>
            <th className="text-left py-2">日時</th>
            <th className="text-left py-2">プラン</th>
            <th className="text-right py-2">金額</th>
          </tr>
        </thead>
        <tbody>
          {result?.data.map((p) => (
            <tr key={p.id} className="border-b">
              <td className="py-2">{formatDateTime(p.paid_at)}</td>
              <td className="py-2">{p.plan_name ?? '-'}</td>
              <td className="py-2 text-right">{formatJpy(p.amount_jpy)}</td>
            </tr>
          ))}
          {result && result.data.length === 0 && (
            <tr>
              <td colSpan={3} className="py-6 text-center text-gray-500">
                履歴がありません
              </td>
            </tr>
          )}
        </tbody>
      </table>
      {result && (
        <Pagination
          currentPage={result.meta.current_page}
          lastPage={result.meta.last_page}
          onPageChange={setPage}
        />
      )}
    </div>
  )
}
