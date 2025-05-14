// src/app/componets/edit-media/edit-media.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { MediaService } from '../../services/media.service';
import { Media } from '../../models/media-model';

// Interface for the form data with proper types
interface MediaFormData {
  id: number;
  title: string;
  format_type: string;
  genre_id: string;  // Keep as string for form binding
  file_path: string;
  description: string;
}

@Component({
  selector: 'app-edit-media',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './edit-media.component.html',
  styleUrls: ['./edit-media.component.css']
})
export class EditMediaComponent implements OnInit {
  mediaId: number = 0;
  updateResult: string = '';
  updateSuccess: boolean = false;
  genres: any[] = [];

  mediaData: MediaFormData = {
    id: 0,
    title: '',
    format_type: '',
    genre_id: '',
    file_path: '',
    description: ''
  };

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private mediaService: MediaService
  ) {}

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      this.mediaId = +params['id'];
      if (!this.mediaId) {
        this.router.navigate(['/']);
        return;
      }
      this.loadGenres();
    });
  }

  loadGenres(): void {
    this.mediaService.getGenres().subscribe({
      next: (data) => {
        this.genres = data;
        this.loadMediaDetails();
      },
      error: (error) => console.error('Error loading genres:', error)
    });
  }

  loadMediaDetails(): void {
    this.mediaService.getMediaById(this.mediaId).subscribe({
      next: (data: any) => {  // Use 'any' to bypass strict type checking
        // Convert genre_id to string to match the form field type
        const genreId = data.genre_id ? String(data.genre_id) : '';

        this.mediaData = {
          id: this.mediaId,
          title: data.title,
          format_type: data.format_type,
          genre_id: genreId,
          file_path: data.file_path || '',
          description: data.description || ''
        };
      },
      error: (error) => {
        console.error('Error loading media details:', error);
        this.updateResult = 'Error loading media details';
        this.updateSuccess = false;
      }
    });
  }

  onSubmit(): void {
    // Create a copy of the data for submission
    const submitData = {
      ...this.mediaData,
      // Convert genre_id to number if it's not empty
      genre_id: this.mediaData.genre_id ? Number(this.mediaData.genre_id) : null
    };

    this.mediaService.updateMedia(submitData).subscribe({
      next: (response) => {
        this.updateResult = response.message || 'Media updated successfully';
        this.updateSuccess = true;

        // Redirect after a short delay
        setTimeout(() => {
          this.router.navigate(['/']);
        }, 1500);
      },
      error: (error) => {
        console.error('Error updating media:', error);
        this.updateResult = 'Error updating media. Please try again.';
        this.updateSuccess = false;
      }
    });
  }

  navigateToHome(): void {
    this.router.navigate(['/']);
  }
}
