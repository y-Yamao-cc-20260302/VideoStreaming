import {Link} from 'react-router-dom'
import type { CastSummary } from "../../types"

export default function CastGrid({cast}:{cast:CastSummary}) {
    return (
    <Link to={`/casts/${cast.id}`} className="block group">
        <div className="aspect-video bg-gray-200 overflow-hidden">
        {cast.picture_path ? (
            <img
            src={cast.picture_path}
            alt={cast.name}
            className="w-full h-full object-cover group-hover:opacity-80 transition-opacity"
            />
        ) : (
            <div className="w-full h-full flex items-center justify-center text-gray-400 text-xs">
            No Thumbnail
            </div>
        )}
        </div>
        <div className="mt-2">
        <h3 className="text-sm font-semibold line-clamp-2 group-hover:underline text-center">
            {cast.name}
        </h3>
        </div>
    </Link>
    )
}