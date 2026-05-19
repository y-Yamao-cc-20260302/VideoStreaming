import { useEffect, useState } from 'react'
import VideoSection from '../components/video/VideoSection'
import { videosApi } from '../api/videos'
import { useAuth } from '../contexts/AuthContext'
import type { VideoSummary } from '../types'

export default function HomePage() {
  const { isAuthenticated } = useAuth()
  const [newReleases, setNewReleases] = useState<VideoSummary[]>([])
  const [popular, setPopular] = useState<VideoSummary[]>([])
  const [recommended, setRecommended] = useState<VideoSummary[]>([])

  useEffect(() => {
    videosApi.newReleases().then((r) => setNewReleases(r.data.data)).catch(() => {})
    videosApi.popular().then((r) => setPopular(r.data.data)).catch(() => {})
    if (isAuthenticated) {
      videosApi.recommended().then((r) => setRecommended(r.data.data)).catch(() => {})
    } else {
      setRecommended([])
    }
  }, [isAuthenticated])

  return (
    <div>
      {isAuthenticated && <VideoSection title="あなたへのおすすめ" videos={recommended} />}
      <VideoSection title="新着" videos={newReleases} />
      <VideoSection title="人気（直近 7 日）" videos={popular} />
      {newReleases.length === 0 && popular.length === 0 && (
        <p className="text-center text-gray-500 py-12">作品がまだありません</p>
      )}
    </div>
  )
}
