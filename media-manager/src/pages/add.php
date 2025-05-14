// add.php
<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Media</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn-cancel { background-color: #f44336; margin-left: 10px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
<div class="container">
    <h1>Add New Media</h1>

    <form id="add-media-form">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="format_type">Format Type</label>
            <select id="format_type" name="format_type" required>
                <option value="">Select a format</option>
                <option value="Audio">Audio</option>
                <option value="Video">Video</option>
                <option value="Image">Image</option>
                <option value="Document">Document</option>
            </select>
        </div>

        <div class="form-group">
            <label for="genre_id">Genre</label>
            <select id="genre_id" name="genre_id" required>
                <option value="">Select a genre</option>
                <!-- Will be populated via AJAX -->
            </select>
        </div>

        <div class="form-group">
            <label for="file_path">File Path</label>
            <input type="text" id="file_path" name="file_path" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <button type="submit" class="btn">Add Media</button>
        <a href="index.php" class="btn btn-cancel">Cancel</a>
    </form>
</div>

<script>
    // Load genres for the dropdown
    fetch('../api/api.php?action=genres')
        .then(response => response.json())
        .then(data => {
            const genreSelect = document.getElementById('genre_id');
            data.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre.id;
                option.textContent = genre.name;
                genreSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading genres:', error));

    // Form submission
    document.getElementById('add-media-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const media = {
            title: document.getElementById('title').value,
            format_type: document.getElementById('format_type').value,
            genre_id: document.getElementById('genre_id').value,
            file_path: document.getElementById('file_path').value,
            description: document.getElementById('description').value
        };

        fetch('../api/api.php?action=add_media', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(media)
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = 'index.php';
            })
            .catch(error => console.error('Error adding media:', error));
    });
</script>
</body>
</html>