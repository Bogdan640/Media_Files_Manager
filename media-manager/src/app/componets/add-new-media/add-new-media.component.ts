import { Component, OnInit, PLATFORM_ID, Inject } from '@angular/core';
import { CommonModule, isPlatformBrowser } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { MediaService } from '../../services/media.service';

@Component({
  selector: 'app-add-new-media',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './add-new-media.component.html',
  styleUrls: ['./add-new-media.component.css']
})
export class AddNewMediaComponent implements OnInit {
  genres: any[] = [];
  newMedia = {
    title: '',
    format_type: '',
    genre_id: '',
    file_path: '',
    description: ''
  };

  formatTypes = [
    { value: 'Audio', label: 'Audio' },
    { value: 'Video', label: 'Video' },
    { value: 'Image', label: 'Image' },
    { value: 'Document', label: 'Document' }
  ];

  constructor(
    private mediaService: MediaService,
    private router: Router,
    @Inject(PLATFORM_ID) private platformId: Object
  ) {}

  ngOnInit(): void {
    this.loadGenres();
  }

  loadGenres(): void {
    this.mediaService.getGenres().subscribe({
      next: (genres) => this.genres = genres,
      error: (error) => console.error('Error loading genres:', error)
    });
  }

  submitForm(): void {
    this.mediaService.addMedia(this.newMedia).subscribe({
      next: (response) => {
        alert(response.message || 'Media added successfully!');
        this.router.navigate(['/']);
      },
      error: (error) => console.error('Error adding media:', error)
    });
  }
}
