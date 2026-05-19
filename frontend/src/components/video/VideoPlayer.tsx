import { useEffect, useRef } from 'react'

interface Props {
  streamUrl: string
  initialPositionSec?: number
  onProgress?: (sec: number) => void
}

export default function VideoPlayer({ streamUrl, initialPositionSec = 0, onProgress }: Props) {
  const ref = useRef<HTMLVideoElement>(null)

  useEffect(() => {
    const el = ref.current
    if (!el) return
    if (initialPositionSec > 0) {
      const onLoaded = () => {
        try {
          el.currentTime = initialPositionSec
        } catch {
          /* noop */
        }
      }
      el.addEventListener('loadedmetadata', onLoaded, { once: true })
      return () => el.removeEventListener('loadedmetadata', onLoaded)
    }
  }, [initialPositionSec])

  useEffect(() => {
    const el = ref.current
    if (!el || !onProgress) return
    const handler = () => onProgress(Math.floor(el.currentTime))
    const interval = window.setInterval(() => {
      if (!el.paused && !el.ended) handler()
    }, 10000)
    el.addEventListener('pause', handler)
    el.addEventListener('ended', handler)
    return () => {
      window.clearInterval(interval)
      el.removeEventListener('pause', handler)
      el.removeEventListener('ended', handler)
    }
  }, [onProgress])

  return (
    <video
      ref={ref}
      src={streamUrl}
      controls
      className="w-full aspect-video bg-black"
    >
      お使いのブラウザは動画再生に対応していません。
    </video>
  )
}
