declare interface IEvent {
  id: number
  venue_id: number
  poster_id: number
  name: string
  event_date: string
  created_at: string
  updated_at: string
  venue: IVenue
  poster: IFile | {src: string}
}
