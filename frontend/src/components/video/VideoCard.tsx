import { Link } from 'react-router-dom'
import type { VideoSummary } from '../../types'
import { formatDuration } from '../../utils/format'

export default function VideoCard({ video }: { video: VideoSummary }) {
  return (
    <Link to={`/videos/${video.id}`} className="block group">
      <div className="aspect-video bg-gray-200 overflow-hidden">
        {video.thumbnail_url ? (
          <img
            src={video.thumbnail_url}
            alt={video.title}
            className="w-full h-full object-cover group-hover:opacity-80 transition-opacity"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-gray-400 text-xs">
            No Thumbnail
          </div>
        )}
      </div>
      <div className="mt-2">
        <h3 className="text-sm font-semibold line-clamp-2 group-hover:underline">
          {video.title}
        </h3>
        <p className="text-xs text-gray-500 mt-1">
          {video.category?.name}
          {video.duration_sec > 0 && ` ・ ${formatDuration(video.duration_sec)}`}
        </p>
      </div>
    </Link>
  )
}
