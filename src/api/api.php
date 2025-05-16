<?php
header('Content-Type: application/json');
ini_set('display_errors', 0); // Turn off display errors for API responses
error_reporting(E_ALL); // Still report errors, but don't display them

require_once '../config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($action) {
        case 'genres':
            getGenres();
            break;
        case 'add_genre':
            $data = json_decode(file_get_contents('php://input'), true);
            addGenre($data);
            break;
        case 'delete_genre':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            deleteGenre($id);
            break;
        case 'media':
            getMediaFiles();
            break;
        case 'media_by_genre':
            $genreId = isset($_GET['genre_id']) ? $_GET['genre_id'] : '';
            getMediaFilesByGenre($genreId);
            break;
        case 'media_details':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            getMediaFileDetails($id);
            break;
        case 'add_media':
            $data = json_decode(file_get_contents('php://input'), true);
            addMediaFile($data);
            break;
        case 'update_media':
            $data = json_decode(file_get_contents('php://input'), true);
            updateMediaFile($data);
            break;
        case 'delete_media':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            deleteMediaFile($id);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    exit;
}

// Function to get all genres
function getGenres() {
    try {
        $conn = getDbConnection();
        $stmt = $conn->query("SELECT * FROM genres ORDER BY name");
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($genres);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading genres: ' . $e->getMessage()]);
    }
}


function addGenre($data) {
    if (!isset($data['name']) || empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Genre name is required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO genres (name) VALUES (:name)");
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->execute();
        $id = $conn->lastInsertId();

        echo json_encode(['message' => 'Genre added successfully', 'id' => $id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error adding genre: ' . $e->getMessage()]);
    }
}

function deleteGenre($id) {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Genre ID is required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("DELETE FROM genres WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Genre deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Genre not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error deleting genre: ' . $e->getMessage()]);
    }
}


function getMediaFiles() {
    try {
        $conn = getDbConnection();
        $stmt = $conn->query("
            SELECT m.*, g.name as genre
            FROM media_files m
            LEFT JOIN genres g ON m.genre_id = g.id
            ORDER BY m.title
        ");
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($media);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading media files: ' . $e->getMessage()]);
    }
}


function getMediaFilesByGenre($genreId) {
    if (!$genreId) {
        http_response_code(400);
        echo json_encode(['error' => 'Genre ID is required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT m.*, g.name as genre
            FROM media_files m
            LEFT JOIN genres g ON m.genre_id = g.id
            WHERE m.genre_id = :genre_id
            ORDER BY m.title
        ");
        $stmt->bindParam(':genre_id', $genreId, PDO::PARAM_INT);
        $stmt->execute();
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($media);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error loading media files: ' . $e->getMessage()]);
    }
}

function getMediaFileDetails($id) {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Media ID is required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT m.*, g.name as genre
            FROM media_files m
            LEFT JOIN genres g ON m.genre_id = g.id
            WHERE m.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $media = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($media) {
            echo json_encode($media);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Media not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching media details: ' . $e->getMessage()]);
    }
}

function addMediaFile($data) {
    if (!isset($data['title']) || empty($data['title']) || !isset($data['format_type']) || empty($data['format_type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and format type are required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            INSERT INTO media_files
            (title, format_type, genre_id, file_path, description)
            VALUES (:title, :format_type, :genre_id, :file_path, :description)
        ");

        $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindParam(':format_type', $data['format_type'], PDO::PARAM_STR);
        $genre_id = !empty($data['genre_id']) ? $data['genre_id'] : null;
        $stmt->bindParam(':genre_id', $genre_id, PDO::PARAM_INT);
        $file_path = !empty($data['file_path']) ? $data['file_path'] : '';
        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
        $description = !empty($data['description']) ? $data['description'] : '';
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        $stmt->execute();
        $id = $conn->lastInsertId();

        echo json_encode(['message' => 'Media added successfully', 'id' => $id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error adding media file: ' . $e->getMessage()]);
    }
}

function updateMediaFile($data) {
    if (!isset($data['id']) || empty($data['id']) || !isset($data['title']) || empty($data['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Media ID and title are required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            UPDATE media_files
            SET title = :title,
                format_type = :format_type,
                genre_id = :genre_id,
                file_path = :file_path,
                description = :description
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindParam(':format_type', $data['format_type'], PDO::PARAM_STR);
        $genre_id = !empty($data['genre_id']) ? $data['genre_id'] : null;
        $stmt->bindParam(':genre_id', $genre_id, PDO::PARAM_INT);
        $file_path = !empty($data['file_path']) ? $data['file_path'] : '';
        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
        $description = !empty($data['description']) ? $data['description'] : '';
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode(['message' => 'Media updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating media file: ' . $e->getMessage()]);
    }
}

function deleteMediaFile($id) {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Media ID is required']);
        return;
    }

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("DELETE FROM media_files WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Media deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Media not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error deleting media file: ' . $e->getMessage()]);
    }
}