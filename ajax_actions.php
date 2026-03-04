<?php

session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// CSRF protection
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token', 'code' => 403]);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in', 'code' => 401]);
    exit;
}

$action = $_POST['action'] ?? '';
$recipe_id = (int)($_POST['recipe_id'] ?? 0);
$user_id = (int)$_SESSION['user']['id'];

if (!recipeExists($conn, $recipe_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Recipe not found', 'code' => 404]);
    exit;
}

switch ($action) {
    case 'like':
        handleLike($conn, $user_id, $recipe_id);
        break;
    case 'save':
        handleSave($conn, $user_id, $recipe_id);
        break;
    case 'comment':
        handleComment($conn, $user_id, $recipe_id);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action', 'code' => 400]);
}

function handleLike($conn, $user_id, $recipe_id) {
    // ...existing code...
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'unlike', 'count' => getLikeCount($conn, $recipe_id), 'code' => 200]);
    } else {
        $stmt = $conn->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'like', 'count' => getLikeCount($conn, $recipe_id), 'code' => 200]);
    }
}

function handleSave($conn, $user_id, $recipe_id) {
    // ...existing code...
    $stmt = $conn->prepare("SELECT id FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'unsave', 'code' => 200]);
    } else {
        $stmt = $conn->prepare("INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'save', 'code' => 200]);
    }
}

function handleComment($conn, $user_id, $recipe_id) {
    $content = sanitizeInput($_POST['content'] ?? '');
    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty', 'code' => 422]);
        return;
    }
    if (strlen($content) > 500) {
        echo json_encode(['status' => 'error', 'message' => 'Comment too long', 'code' => 422]);
        return;
    }
    $stmt = $conn->prepare("INSERT INTO comments (user_id, recipe_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $recipe_id, $content);
    $stmt->execute();
    $comment_id = $stmt->insert_id;
    $stmt = $conn->prepare("SELECT c.*, u.name, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $comment = $stmt->get_result()->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'comment' => [
            'id' => $comment['id'],
            'content' => $comment['content'],
            'created_at' => $comment['created_at'],
            'user_name' => $comment['name'],
            'user_avatar' => $comment['avatar']
        ],
        'count' => getCommentCount($conn, $recipe_id),
        'code' => 200
    ]);
}

// Utility functions moved to includes/functions.php:
// - recipeExists($conn, $recipe_id)
// - getLikeCount($conn, $recipe_id)
// - getCommentCount($conn, $recipe_id)
// - sanitizeInput($input)
// - validateCsrfToken($token)

?>