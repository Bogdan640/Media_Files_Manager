<?php
// src/pages/view.php
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
    <title>Media Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        .details { margin-top: 20px; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .detail-row { margin-bottom: 15px; }
        .label { font-weight: bold; margin-bottom: 5px; }
        .value { padding: 5px 0; }
        .description { white-space: pre-wrap; }
        .actions { margin-top: 20px; }
        .btn { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px; }
        .btn-edit { background-color: #2196F3; }
        .btn-delete { background-color: #f44336; }
    </style>
</head>
<body>
<div class="container">
    <h1>Media Details</h1>

    <div id="media-details" class="details">
        <!-- Media details will be loaded via AJAX -->
        <p>Loading...</p>
    </div>

    <div class="actions">
        <a href="index.php" class="btn">Back to List</a>
        <a id="edit-link" href="#" class="btn btn-edit">Edit</a>
        <button id="delete-btn" class="btn btn-delete">Delete</button>
    </div>
</div>

<script>
    const mediaId = '<?php echo $mediaId; ?>';

    // Load media details when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadMediaDetails(mediaId);
    });

    // Function to load media details
    function loadMediaDetails(id) {
        fetch(`../api/api.php?action=media_details&id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                const detailsContainer = document.getElementById('media-details');

                // Update edit link
                document.getElementById('edit-link').href = `edit.php?id=${id}`;

                // Format the details HTML
                detailsContainer.innerHTML = `
                    <div class="detail-row">
                        <div class="label">Title:</div>
                        <div class="value">${data.title}</div>
                    </div>
                    <div class="detail-row">
                        <div class="label">Format:</div>
                        <div class="value">${data.format_type}</div>
                    </div>
                    <div class="detail-row">
                        <div class="label">Genre:</div>
                        <div class="value">${data.genre || 'None'}</div>
                    </div>
                    ${data.file_path ? `
                    <div class="detail-row">
                        <div class="label">File Path:</div>
                        <div class="value">${data.file_path}</div>
                    </div>` : ''}
                    ${data.description ? `
                    <div class="detail-row">
                        <div class="label">Description:</div>
                        <div class="value description">${data.description}</div>
                    </div>` : ''}
                `;
            })
            .catch(error => {
                console.error('Error loading media details:', error);
                document.getElementById('media-details').innerHTML = '<p>Error loading media details. Please try again.</p>';
            });
    }

    // Handle delete button
    document.getElementById('delete-btn').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this media file?')) {
            fetch(`../api/api.php?action=delete_media&id=${mediaId}`, {
                method: 'DELETE'
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    window.location.href = 'index.php';
                })
                .catch(error => {
                    console.error('Error deleting media:', error);
                    alert('Error deleting media. Please try again.');
                });
        }
    });



</script>
</body>
</html>