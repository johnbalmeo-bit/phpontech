<?php
require_once 'config.php';
require_once 'functions.php';

function login($email, $password) {
    global $conn;
    
    $email = sanitize($email);
    $password = sanitize($password);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            return true;
        }
    }
    return false;
}

function register($name, $email, $password) {
    global $conn;
    
    $name = sanitize($name);
    $email = sanitize($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, avatar) VALUES (?, ?, ?, 'images/profile.jpg')");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    return $stmt->execute();
}

function emailExists($email) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    return $stmt->num_rows > 0;
}
?>