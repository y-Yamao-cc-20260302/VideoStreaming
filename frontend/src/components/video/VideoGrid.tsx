import type { VideoSummary } from '../../types'
import VideoCard from './VideoCard'

export default function VideoGrid({ videos }: { videos: VideoSummary[] }) {
  if (videos.length === 0) {
    return <p className="text-sm text-gray-500 py-8 text-center">作品がありません</p>
  }
  return (
    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
      {videos.map((v) => (
        <VideoCard key={v.id} video={v} />
      ))}
    </div>
  )
}
