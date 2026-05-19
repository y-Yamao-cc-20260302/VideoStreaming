import { useCallback, useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import VideoPlayer from '../../components/video/VideoPlayer'
import ReviewSection from './ReviewSection'
import { videosApi } from '../../api/videos'
import { favoritesApi } from '../../api/favorites'
import { useAuth } from '../../contexts/AuthContext'
import type { VideoDetail } from '../../types'
import { formatDate, formatDuration } from '../../utils/format'

export default function VideoDetailPage() {
  const { id } = useParams<{ id: string }>()
  const videoId = Number(id)
  const { isAuthenticated } = useAuth()
  const navigate = useNavigate()
  const [video, setVideo] = useState<VideoDetail | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    setLoading(true)
    videosApi
      .show(videoId)
      .then((r) => setVideo(r.data))
      .finally(() => setLoading(false))
  }, [videoId])

  const handleProgress = useCallback(
    (sec: number) => {
      if (!isAuthenticated) return
      videosApi.reportProgress(videoId, sec).catch(() => {})
    },
    [isAuthenticated, videoId]
  )

  const toggleFavorite = async () => {
    if (!isAuthenticated) {
      navigate('/login?redirect=' + encodeURIComponent(window.location.pathname))
      return
    }
    if (!video) return
    if (video.is_favored) {
      await favoritesApi.remove(video.id)
      setVideo({ ...video, is_favored: false })
    } else {
      await favoritesApi.add(video.id)
      setVideo({ ...video, is_favored: true })
    }
  }

  if (loading) return <p className="text-sm text-gray-500">読み込み中...</p>
  if (!video) return <p className="text-sm text-gray-500">作品が見つかりませんでした</p>

  return (
    <div>
      <VideoPlayer
        streamUrl={video.stream_url}
        initialPositionSec={video.progress_sec}
        onProgress={handleProgress}
      />

      <div className="mt-4 flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold">{video.title}</h1>
          <p className="text-sm text-gray-500 mt-1">
            {video.category?.name}
            {video.release_date && ` ・ ${formatDate(video.release_date)}`}
            {video.duration_sec > 0 && ` ・ ${formatDuration(video.duration_sec)}`}
          </p>
          {video.genres && video.genres.length > 0 && (
            <div className="flex gap-2 mt-2">
              {video.genres.map((g) => (
                <span key={g.id} className="text-xs bg-gray-100 px-2 py-1">
                  {g.name}
                </span>
              ))}
            </div>
          )}
        </div>
        <button
          onClick={toggleFavorite}
          className={`px-4 py-2 text-sm border ${
            video.is_favored ? 'bg-yellow-100 border-yellow-400' : 'border-gray-300'
          }`}
        >
          {video.is_favored ? '★ マイリスト登録済み' : '☆ マイリストに追加'}
        </button>
      </div>

      <div className="mt-4 text-sm">
        評価:{' '}
        {video.rating_avg != null ? (
          <>
            ★ {video.rating_avg.toFixed(1)}（{video.rating_count} 件）
          </>
        ) : (
          '評価なし'
        )}
      </div>

      {video.description && (
        <section className="mt-6">
          <h2 className="text-lg font-bold mb-2">あらすじ</h2>
          <p className="text-sm whitespace-pre-wrap leading-relaxed">{video.description}</p>
        </section>
      )}

      <ReviewSection videoId={video.id} />
    </div>
  )
}
