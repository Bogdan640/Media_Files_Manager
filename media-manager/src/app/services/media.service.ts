import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Media } from '../models/media-model';

@Injectable({
  providedIn: 'root'
})
export class MediaService {
  private apiUrl = '../api/api.php';  // Update to match your PHP API path

  constructor(private http: HttpClient) {}

  getGenres(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}?action=genres`);
  }

  getMediaItems(): Observable<Media[]> {
    return this.http.get<Media[]>(`${this.apiUrl}?action=media`);
  }

  getMediaByGenre(genreId: number): Observable<Media[]> {
    return this.http.get<Media[]>(`${this.apiUrl}?action=media_by_genre&genre_id=${genreId}`);
  }

  getMediaById(id: number): Observable<Media> {
    return this.http.get<Media>(`${this.apiUrl}?action=media_details&id=${id}`);
  }

  addMedia(media: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?action=add_media`, media);
  }

  updateMedia(mediaData: any) {
    return this.http.post<any>(`${this.apiUrl}?action=update_media`, mediaData);
  }

  deleteMedia(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}?action=delete_media&id=${id}`);
  }
  // In your media.service.ts file


  addGenre(name: string) {
    return this.http.post<any>(`${this.apiUrl}?action=add_genre`, { name });
  }

  deleteGenre(id: number) {
    return this.http.delete<any>(`${this.apiUrl}?action=delete_genre&id=${id}`);
  }

}
