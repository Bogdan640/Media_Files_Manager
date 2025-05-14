<?php
// src/pages/index.php
require_once '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multimedia Collection</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; text-decoration: none; }
        .btn-edit { background-color: #2196F3; }
        .btn-delete { background-color: #f44336; }
        .filter-container { margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>Multimedia Collection</h1>

    <div class="filter-container">
        <label for="genre-filter">Filter by Genre:</label>
        <select id="genre-filter">
            <option value="0">All Genres</option>
            <!-- Genres will be loaded via AJAX -->
        </select>
    </div>

    <div id="last-filter"></div>

    <a href="add.php" class="btn">Add New Media</a>
    <a href="genres.php" class="btn">Manage Genres</a>

    <table id="media-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Format</th>
            <th>Genre</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="media-list">
        <!-- Media files will be loaded via AJAX -->
        </tbody>
    </table>
</div>

<script>
    // Load genres for the filter
    fetch('../api/api.php?action=genres')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            const genreFilter = document.getElementById('genre-filter');
            data.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre.id;
                option.textContent = genre.name;
                genreFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading genres:', error));

    // Load all media files initially
    loadMediaFiles();

    // Add event listener for filter change
    document.getElementById('genre-filter').addEventListener('change', function() {
        const genreId = this.value;
        const genreName = this.options[this.selectedIndex].text;

        loadMediaFiles(genreId);

        // Save filter to localStorage
        localStorage.setItem('lastGenreFilter', genreId);
        localStorage.setItem('lastGenreName', genreName);

        // Display the last filter used
        document.getElementById('last-filter').textContent = `Last filter: ${genreName}`;
    });

    // Function to load media files
    function loadMediaFiles(genreId = 0) {
        const url = genreId > 0 ? `../api/api.php?action=media_by_genre&genre_id=${genreId}` : '../api/api.php?action=media';

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                const mediaList = document.getElementById('media-list');
                mediaList.innerHTML = '';

                data.forEach(item => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                            <td>${item.title}</td>
                            <td>${item.format_type}</td>
                            <td>${item.genre || 'None'}</td>
                            <td class="actions">
                                <a href="view.php?id=${item.id}" class="btn">View</a>
                                <a href="edit.php?id=${item.id}" class="btn btn-edit">Edit</a>
                                <button onclick="deleteMedia(${item.id})" class="btn btn-delete">Delete</button>
                            </td>
                        `;

                    mediaList.appendChild(row);
                });

                if (data.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="4">No media files found.</td>';
                    mediaList.appendChild(row);
                }
            })
            .catch(error => console.error('Error loading media files:', error));
    }

    // Function to delete media
    function deleteMedia(id) {
        if (confirm('Are you sure you want to delete this media file?')) {
            fetch(`../api/api.php?action=delete_media&id=${id}`, {
                method: 'DELETE'
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    loadMediaFiles(document.getElementById('genre-filter').value);
                })
                .catch(error => console.error('Error deleting media:', error));
        }
    }

    // Check if there's a saved filter
    document.addEventListener('DOMContentLoaded', function() {
        const lastGenreFilter = localStorage.getItem('lastGenreFilter');
        const lastGenreName = localStorage.getItem('lastGenreName');

        if (lastGenreFilter && lastGenreName) {
            document.getElementById('genre-filter').value = lastGenreFilter;
            document.getElementById('last-filter').textContent = `Last filter: ${lastGenreName}`;
            loadMediaFiles(lastGenreFilter);
        }
    });
</script>
</body>
</html>