// src/app/componets/main-page/main-page.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { MediaService } from '../../services/media.service';
import { Media } from '../../models/media-model';

@Component({
  selector: 'app-main-page',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './main-page.component.html',
  styleUrl: './main-page.component.css'
})
export class MainPageComponent implements OnInit {
  mediaItems: Media[] = [];
  genres: any[] = [];
  isLoading: boolean = false;
  selectedGenreId: number = 0;

  constructor(
    private mediaService: MediaService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadGenres();
    this.loadMedia();
  }

  loadGenres(): void {
    this.mediaService.getGenres().subscribe({
      next: (genres) => {
        this.genres = genres;
      },
      error: (error) => console.error('Error loading genres:', error)
    });
  }

  loadMedia(): void {
    this.isLoading = true;
    if (this.selectedGenreId > 0) {
      this.mediaService.getMediaByGenre(this.selectedGenreId).subscribe({
        next: (data) => {
          this.mediaItems = data;
          this.isLoading = false;
        },
        error: (error) => {
          console.error('Error loading media files:', error);
          this.isLoading = false;
        }
      });
    } else {
      this.mediaService.getMediaItems().subscribe({
        next: (data) => {
          this.mediaItems = data;
          this.isLoading = false;
        },
        error: (error) => {
          console.error('Error loading media files:', error);
          this.isLoading = false;
        }
      });
    }
  }

  filterByGenre(): void {
    // Just load the media without saving to localStorage
    this.loadMedia();
  }

  viewMedia(id: number): void {
    this.router.navigate(['/media', id]);
  }

  editMedia(id: number): void {
    this.router.navigate(['/media/edit', id]);
  }

  deleteMedia(id: number): void {
    if (confirm('Are you sure you want to delete this media file?')) {
      this.mediaService.deleteMedia(id).subscribe({
        next: (response) => {
          alert(response.message || 'Media deleted successfully');
          this.loadMedia();
        },
        error: (error) => console.error('Error deleting media:', error)
      });
    }
  }

  navigateToAdd(): void {
    this.router.navigate(['/add']);
  }

  navigateToGenres(): void {
    this.router.navigate(['/genres']);
  }
}
