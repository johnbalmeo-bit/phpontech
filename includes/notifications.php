<?php
// Notification helper functions for likes and comments (no notifications table needed)
function getUserNotifications($user_id, $conn) {
    // Get all users who liked the user's recipes
    $likes_sql = "SELECT l.created_at, 'like' as type, u.name as actor_name, u.avatar as actor_avatar, r.title as recipe_title, NULL as content
        FROM likes l
        JOIN users u ON l.user_id = u.id
        JOIN recipes r ON l.recipe_id = r.id
        WHERE r.user_id = ? AND l.user_id != ?";
    $likes_stmt = $conn->prepare($likes_sql);
    $likes_stmt->bind_param('ii', $user_id, $user_id);
    $likes_stmt->execute();
    $likes_result = $likes_stmt->get_result();
    $likes = $likes_result->fetch_all(MYSQLI_ASSOC);

    // Get all users who commented on the user's recipes
    $comments_sql = "SELECT c.created_at, 'comment' as type, u.name as actor_name, u.avatar as actor_avatar, r.title as recipe_title, c.content
        FROM comments c
        JOIN users u ON c.user_id = u.id
        JOIN recipes r ON c.recipe_id = r.id
        WHERE r.user_id = ? AND c.user_id != ?";
    $comments_stmt = $conn->prepare($comments_sql);
    $comments_stmt->bind_param('ii', $user_id, $user_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();
    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);

    // Merge and sort by created_at DESC
    $all = array_merge($likes, $comments);
    usort($all, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $all;
}
