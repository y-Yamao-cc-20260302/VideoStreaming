import { useEffect, useState } from 'react'
import VideoGrid from '../../components/video/VideoGrid'
import Pagination from '../../components/common/Pagination'
import { favoritesApi } from '../../api/favorites'
import type { Paginated, VideoSummary } from '../../types'

export default function FavoritesPage() {
  const [result, setResult] = useState<Paginated<VideoSummary> | null>(null)
  const [page, setPage] = useState(1)

  useEffect(() => {
    favoritesApi.list(page).then((r) => setResult(r.data))
  }, [page])

  return (
    <div>
      <h1 className="text-lg font-bold mb-4">マイリスト</h1>
      {result && (
        <>
          <VideoGrid videos={result.data} />
          <Pagination
            currentPage={result.meta.current_page}
            lastPage={result.meta.last_page}
            onPageChange={setPage}
          />
        </>
      )}
    </div>
  )
}
