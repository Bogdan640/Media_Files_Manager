// src/app/componets/media-details/media-details.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { MediaService } from '../../services/media.service';
import { Media } from '../../models/media-model';

@Component({
  selector: 'app-media-details',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './media-details.component.html',
  styleUrls: ['./media-details.component.css']
})
export class MediaDetailsComponent implements OnInit {
  mediaId: number = 0;
  mediaDetails: Media | null = null;
  loading: boolean = true;
  error: string = '';

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
      this.loadMediaDetails();
    });
  }

  loadMediaDetails(): void {
    this.loading = true;
    this.mediaService.getMediaById(this.mediaId).subscribe({
      next: (data) => {
        this.mediaDetails = data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading media details:', error);
        this.error = 'Error loading media details. Please try again.';
        this.loading = false;
      }
    });
  }

  deleteMedia(): void {
    if (confirm('Are you sure you want to delete this media file?')) {
      this.mediaService.deleteMedia(this.mediaId).subscribe({
        next: (response) => {
          alert(response.message || 'Media deleted successfully');
          this.router.navigate(['/']);
        },
        error: (error) => {
          console.error('Error deleting media:', error);
          alert('Error deleting media. Please try again.');
        }
      });
    }
  }

  navigateToEdit(): void {
    this.router.navigate(['/media/edit', this.mediaId]);
  }

  navigateToHome(): void {
    this.router.navigate(['/']);
  }
}
