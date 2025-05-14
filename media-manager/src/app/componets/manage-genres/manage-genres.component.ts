// src/app/componets/genre-management/genre-management.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { MediaService } from '../../services/media.service';

@Component({
  selector: 'app-genre-management',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: 'manage-genres.component.html',
  styleUrls: ['manage-genres.component.css']
})
export class GenreManagementComponent implements OnInit {
  genres: any[] = [];
  newGenreName: string = '';
  addResult: string = '';

  constructor(
    private mediaService: MediaService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadGenres();
  }

  loadGenres(): void {
    this.mediaService.getGenres().subscribe({
      next: (data) => {
        this.genres = data;
      },
      error: (error) => {
        console.error('Error loading genres:', error);
      }
    });
  }

  addGenre(): void {
    if (!this.newGenreName.trim()) {
      this.addResult = 'Please enter a genre name';
      return;
    }

    this.mediaService.addGenre(this.newGenreName).subscribe({
      next: (response) => {
        this.addResult = response.message || 'Genre added successfully';
        this.newGenreName = '';
        this.loadGenres();
      },
      error: (error) => {
        console.error('Error adding genre:', error);
        this.addResult = 'Error adding genre. Please try again.';
      }
    });
  }

  deleteGenre(id: number, name: string): void {
    if (confirm(`Are you sure you want to delete the genre "${name}"?`)) {
      this.mediaService.deleteGenre(id).subscribe({
        next: (response) => {
          alert(response.message || 'Genre deleted successfully');
          this.loadGenres();
        },
        error: (error) => {
          console.error('Error deleting genre:', error);
          alert('Error deleting genre. Please try again.');
        }
      });
    }
  }

  navigateToHome(): void {
    this.router.navigate(['/']);
  }
}
