// src/app/app.routes.ts
import { Routes } from '@angular/router';
import { MainPageComponent } from './componets/main-page/main-page.component';
import { AddNewMediaComponent } from './componets/add-new-media/add-new-media.component';
import { GenreManagementComponent } from './componets/manage-genres/manage-genres.component';
import { MediaDetailsComponent } from './componets/media-details/media-details.component';
import { EditMediaComponent } from './componets/edit-media/edit-media.component';

export const routes: Routes = [
  { path: '', component: MainPageComponent },
  { path: 'add', component: AddNewMediaComponent },
  { path: 'genres', component: GenreManagementComponent },
  { path: 'media/edit/:id', component: EditMediaComponent },  // This should come before the next route
  { path: 'media/:id', component: MediaDetailsComponent },
  { path: '**', redirectTo: '' }
];
