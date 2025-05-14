<?php
// src/pages/edit.php
require_once '../config.php';

// Get the media ID from the URL
$mediaId = isset($_GET['id']) ? $_GET['id'] : '';

if (!$mediaId) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Media File</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        form { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        textarea { height: 150px; }
        .btn { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn-cancel { background-color: #f44336; margin-left: 10px; }
        #update-result { margin-top: 15px; padding: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Media File</h1>
    <div id="update-result"></div>

    <form id="edit-form">
        <input type="hidden" id="media-id" value="<?php echo htmlspecialchars($mediaId); ?>">

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="format-type">Format Type:</label>
            <input type="text" id="format-type" name="format_type" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre:</label>
            <select id="genre" name="genre_id">
                <option value="">Select Genre</option>
                <!-- Genres will be loaded via AJAX -->
            </select>
        </div>

        <div class="form-group">
            <label for="file-path">File Path:</label>
            <input type="text" id="file-path" name="file_path">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <button type="submit" class="btn">Update Media</button>
        <a href="index.php" class="btn btn-cancel">Cancel</a>
    </form>
</div>

<script>
    // Load media details when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const mediaId = document.getElementById('media-id').value;

        // Load genres
        fetch('../api/api.php?action=genres')
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                const genreSelect = document.getElementById('genre');
                data.forEach(genre => {
                    const option = document.createElement('option');
                    option.value = genre.id;
                    option.textContent = genre.name;
                    genreSelect.appendChild(option);
                });

                // After loading genres, load media details
                loadMediaDetails(mediaId);
            })
            .catch(error => console.error('Error loading genres:', error));
    });

    // Function to load media details
    function loadMediaDetails(id) {
        fetch(`../api/api.php?action=media_details&id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                // Populate form fields
                document.getElementById('title').value = data.title;
                document.getElementById('format-type').value = data.format_type;
                document.getElementById('genre').value = data.genre_id || '';
                document.getElementById('file-path').value = data.file_path || '';
                document.getElementById('description').value = data.description || '';
            })
            .catch(error => {
                console.error('Error loading media details:', error);
                document.getElementById('update-result').textContent = 'Error loading media details';
                document.getElementById('update-result').style.color = 'red';
            });
    }

    // Handle form submission
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const mediaData = {
            id: document.getElementById('media-id').value,
            title: document.getElementById('title').value,
            format_type: document.getElementById('format-type').value,
            genre_id: document.getElementById('genre').value,
            file_path: document.getElementById('file-path').value,
            description: document.getElementById('description').value
        };

        fetch('../api/api.php?action=update_media', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(mediaData)
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                document.getElementById('update-result').textContent = data.message;
                document.getElementById('update-result').style.color = 'green';

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            })
            .catch(error => {
                console.error('Error updating media:', error);
                document.getElementById('update-result').textContent = 'Error updating media. Please try again.';
                document.getElementById('update-result').style.color = 'red';
            });
    });
</script>
</body>
</html>