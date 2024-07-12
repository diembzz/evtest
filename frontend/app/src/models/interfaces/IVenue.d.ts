declare interface IVenue {
  id: number
  name: string
  created_at: string
  updated_at: string
  events: IEvent[]
}
