import type { VideoSummary } from '../../types'
import VideoCard from './VideoCard'

interface Props {
  title: string
  videos: VideoSummary[]
}

export default function VideoSection({ title, videos }: Props) {
  if (videos.length === 0) return null
  return (
    <section className="mb-8">
      <h2 className="text-lg font-bold mb-3">{title}</h2>
      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        {videos.slice(0, 10).map((v) => (
          <VideoCard key={v.id} video={v} />
        ))}
      </div>
    </section>
  )
}
