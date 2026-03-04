<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to post a recipe';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validate and sanitize input
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? '';
$user_id = $_SESSION['user']['id'];

// Validate required fields
if (empty($title) || empty($description) || empty($category)) {
    $_SESSION['error'] = 'Please fill in all required fields';
    header('Location: index.php');
    exit;
}

// Validate category
$valid_categories = ['Main Dishes', 'Kakanin', 'Desserts', 'Drinks', 'Vegetarian'];
if (!in_array($category, $valid_categories)) {
    $_SESSION['error'] = 'Invalid recipe category';
    header('Location: index.php');
    exit;
}

// Handle file upload
$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validate image
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
    
    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['error'] = 'Only JPG, PNG, and GIF images are allowed';
        header('Location: index.php');
        exit;
    }
    
    // Generate unique filename
    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
        $image_path = $file_path;
    }
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO recipes (user_id, title, description, category, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $description, $category, $image_path);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Recipe posted successfully!';
} else {
    $_SESSION['error'] = 'Failed to post recipe. Please try again.';
}

header('Location: index.php');
exit;
?>