// src/app/models/media.model.ts
export interface Media {
  id: number;
  title: string;
  description?: string;
  format_type: string;
  genre?: string;
  genre_id?: number;
  url?: string;
}
