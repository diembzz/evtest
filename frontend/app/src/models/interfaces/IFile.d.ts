declare interface IFile {
  id: number
  base: string
  path: string
  src: string
  created_at: string
  updated_at: string
  events: IEvent[]
}
