import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Pagination from '../../components/common/Pagination'
import { watchHistoriesApi } from '../../api/watchHistories'
import type { Paginated, WatchHistoryItem } from '../../types'
import { formatDateTime } from '../../utils/format'

export default function HistoryPage() {
  const [result, setResult] = useState<Paginated<WatchHistoryItem> | null>(null)
  const [page, setPage] = useState(1)

  useEffect(() => {
    watchHistoriesApi.list(page).then((r) => setResult(r.data))
  }, [page])

  return (
    <div>
      <h1 className="text-lg font-bold mb-4">視聴履歴</h1>
      {result && result.data.length === 0 && (
        <p className="text-sm text-gray-500">視聴履歴がありません</p>
      )}
      <ul className="divide-y">
        {result?.data.map((h) => (
          <li key={h.video.id} className="py-3 flex items-center gap-4">
            <Link to={`/videos/${h.video.id}`} className="w-32 aspect-video bg-gray-200 shrink-0">
              {h.video.thumbnail_url && (
                <img src={h.video.thumbnail_url} alt="" className="w-full h-full object-cover" />
              )}
            </Link>
            <div className="flex-1">
              <Link to={`/videos/${h.video.id}`} className="font-semibold hover:underline">
                {h.video.title}
              </Link>
              <p className="text-xs text-gray-500 mt-1">
                {formatDateTime(h.watched_at)} ・ 進捗 {h.progress_sec} 秒
              </p>
            </div>
          </li>
        ))}
      </ul>
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
