<?php
// src/pages/genres.php
require_once '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genres</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; text-decoration: none; }
        .btn-delete { background-color: #f44336; }
        .form-container { margin-top: 20px; padding: 20px; background-color: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Genres</h1>
    <a href="index.php" class="btn">Back to Media List</a>

    <div id="genre-list-container">
        <h2>Current Genres</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="genre-list">
            <!-- Genres will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h2>Add New Genre</h2>
        <input type="text" id="genre-name" placeholder="Genre Name">
        <button onclick="addGenre()" class="btn">Add Genre</button>
        <p id="add-result"></p>
    </div>
</div>

<script>
    // Load genres when the page loads
    document.addEventListener('DOMContentLoaded', loadGenres);

    // Function to load genres
    function loadGenres() {
        fetch('../api/api.php?action=genres')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const genreList = document.getElementById('genre-list');
                genreList.innerHTML = '';

                data.forEach(genre => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${genre.name}</td>
                        <td>
                            <button onclick="deleteGenre(${genre.id}, '${genre.name}')" class="btn btn-delete">Delete</button>
                        </td>
                    `;
                    genreList.appendChild(row);
                });

                if (data.length === 0) {
                    genreList.innerHTML = '<tr><td colspan="2">No genres found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading genres:', error);
                document.getElementById('genre-list').innerHTML =
                    `<tr><td colspan="2">Error loading genres. Please try again.</td></tr>`;
            });
    }

    // Function to add a new genre
    function addGenre() {
        const genreName = document.getElementById('genre-name').value.trim();

        if (!genreName) {
            document.getElementById('add-result').textContent = 'Please enter a genre name';
            return;
        }

        fetch('../api/api.php?action=add_genre', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: genreName })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('add-result').textContent = data.message;
                document.getElementById('genre-name').value = '';
                loadGenres(); // Reload the genre list
            })
            .catch(error => {
                console.error('Error adding genre:', error);
                document.getElementById('add-result').textContent = 'Error adding genre. Please try again.';
            });
    }

    // Function to delete a genre
    function deleteGenre(id, name) {
        if (confirm(`Are you sure you want to delete the genre "${name}"?`)) {
            fetch(`../api/api.php?action=delete_genre&id=${id}`, {
                method: 'DELETE'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    loadGenres(); // Reload the genre list
                })
                .catch(error => {
                    console.error('Error deleting genre:', error);
                    alert('Error deleting genre. Please try again.');
                });
        }
    }
</script>
</body>
</html>