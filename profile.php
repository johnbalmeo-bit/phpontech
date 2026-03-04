<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = $_SESSION['user'];

// Fetch all recipes by this user
$stmt = $conn->prepare("SELECT r.*, 
    (SELECT COUNT(*) FROM likes l WHERE l.recipe_id = r.id) as like_count,
    (SELECT COUNT(*) FROM comments c WHERE c.recipe_id = r.id) as comment_count
    FROM recipes r WHERE r.user_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$user_recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile | Linamnam</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="profile.css">

</head>
<body >
  <?php include 'header.php'; ?>
  
  <div class="profile-container">
    <div class="profile-header">
      <img src="<?php echo $user['avatar']; ?>" alt="Profile Picture" class="profile-avatar">
      <div class="profile-info">
        <h1><?php echo htmlspecialchars($user['name']); ?></h1>
        <p>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
        
        <div class="profile-stats">
          <div class="stat-item">
            <div class="stat-number">42</div>
            <div class="stat-label">Recipes</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">1.2K</div>
            <div class="stat-label">Followers</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">356</div>
            <div class="stat-label">Following</div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="profile-recipes">
      <h2>My Recipes</h2>
      <div class="feed-list">
        <?php if (empty($user_recipes)): ?>
          <div>No recipes posted yet.</div>
        <?php else: ?>
          <?php foreach ($user_recipes as $recipe): ?>
            <?php
            $imgFile = isset($recipe['image']) ? $recipe['image'] : '';
            if (!empty($imgFile) && file_exists('images/' . basename($imgFile))) {
                $imagePath = 'images/' . basename($imgFile);
            } elseif (!empty($imgFile) && file_exists('uploads/' . basename($imgFile))) {
                $imagePath = 'uploads/' . basename($imgFile);
            } else {
                $imagePath = 'images/default_recipe.jpg';
            }
            // Fetch category name if available
            $categoryName = '';
            if (!empty($recipe['category_id'])) {
              $catStmt = $conn->prepare('SELECT name FROM categories WHERE id = ?');
              $catStmt->bind_param('i', $recipe['category_id']);
              $catStmt->execute();
              $catStmt->bind_result($categoryName);
              $catStmt->fetch();
              $catStmt->close();
            }
            ?>
            <div class="feed-card" data-recipe-id="<?php echo $recipe['id']; ?>">
              <div class="feed-header" style="display:flex;align-items:center;gap:1rem;margin-bottom:0.7rem;">
                <img src="<?php echo $user['avatar']; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" class="feed-avatar" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                <div>
                  <span class="feed-username" style="font-weight:600;">You</span>
                  <span style="color:#888;font-size:0.95em;margin-left:0.5em;"><i class="far fa-clock"></i> <?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></span>
                </div>
              </div>
              <div class="feed-body" style="display:flex;gap:1.5rem;">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="feed-img" style="width:220px;height:150px;object-fit:cover;border-radius:10px;flex-shrink:0;">
                <div style="flex:1;">
                  <h3 class="feed-title" style="margin-bottom:0.3rem;font-size:1.3em;">
                    <i class="fas fa-utensils" style="color:#D35400;"></i>
                    <a href="recipe.php?id=<?php echo $recipe['id']; ?>" style="color:#D35400;text-decoration:none;">
                      <?php echo htmlspecialchars($recipe['title']); ?>
                    </a>
                  </h3>
                  <?php if ($categoryName): ?>
                    <div class="feed-category" style="margin-bottom:0.5rem;font-size:0.98em;">
                      <i class="fas fa-tag" style="color:#E67E22;"></i> <strong>Category:</strong> <?php echo htmlspecialchars($categoryName); ?>
                    </div>
                  <?php endif; ?>
                  <div class="feed-description" style="margin-bottom:0.7rem;font-size:1em;">
                    <i class="fas fa-align-left" style="color:#6c757d;"></i> <strong>Description:</strong> <?php echo htmlspecialchars($recipe['description']); ?>
                  </div>
                  <div class="feed-actions" style="display:flex;align-items:center;gap:1.5rem;margin-bottom:0.7rem;">
                    <span style="display:flex;align-items:center;gap:0.4em;">
                      <i class="fas fa-heart" style="color:#e63946;"></i> <?php echo $recipe['like_count']; ?>
                    </span>
                    <span style="display:flex;align-items:center;gap:0.4em;">
                      <i class="fas fa-comment" style="color:#D35400;"></i> <?php echo $recipe['comment_count']; ?>
                    </span>
                    <button class="action-button like-btn" data-recipe-id="<?php echo $recipe['id']; ?>" style="background:none;border:none;color:#D35400;cursor:pointer;font-size:1em;">
                      <i class="far fa-heart"></i> Like
                    </button>
                    <button class="action-button comment-toggle" style="background:none;border:none;color:#D35400;cursor:pointer;font-size:1em;">
                      <i class="far fa-comment"></i> Comment
                    </button>
                    <button class="action-button save-btn" data-recipe-id="<?php echo $recipe['id']; ?>" style="background:none;border:none;color:#D35400;cursor:pointer;font-size:1em;">
                      <i class="far fa-bookmark"></i> Save
                    </button>
                  </div>
                  <div class="feed-comments" style="margin-top:0.5rem;">
                    <div class="comments-list">
                      <?php
                      $stmt2 = $conn->prepare("SELECT c.*, u.name as user_name, u.avatar as user_avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = ? ORDER BY c.created_at DESC LIMIT 3");
                      $stmt2->bind_param("i", $recipe['id']);
                      $stmt2->execute();
                      $comments = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                      foreach ($comments as $comment): ?>
                        <div class="comment" style="display:flex;align-items:flex-start;margin-bottom:0.7rem;">
                          <img src="<?php echo $comment['user_avatar'] ?: 'images/default-avatar.jpg'; ?>" alt="<?php echo htmlspecialchars($comment['user_name']); ?>" class="comment-avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:0.7rem;">
                          <div class="comment-content" style="background:#f5f5f5;padding:0.7rem 1rem;border-radius:10px;flex:1;">
                            <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                            <p style="margin:0.2rem 0 0.3rem 0;"> <?php echo htmlspecialchars($comment['content']); ?></p>
                            <small style="color:#888;"><i class="far fa-clock"></i> <?php echo date('M j, g:i a', strtotime($comment['created_at'])); ?></small>
                          </div>
                        </div>
                      <?php endforeach; ?>
                      <?php if (empty($comments)): ?>
                        <div style="color:#888;font-style:italic;">No comments yet. Be the first to comment!</div>
                      <?php endif; ?>
                    </div>
                    <?php if (isLoggedIn()): ?>
                    <form class="comment-form" data-recipe-id="<?php echo $recipe['id']; ?>"
                          data-user-name="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>"
                          data-user-avatar="<?php echo $_SESSION['user']['avatar'] ?? 'images/default-avatar.jpg'; ?>"
                          style="margin-top:0.7rem;display:flex;gap:0.5rem;align-items:center;">
                      <textarea placeholder="Add a comment..." required style="flex:1;padding:0.5rem;border-radius:8px;border:1px solid #ddd;"></textarea>
                      <button type="submit" style="background:#D35400;color:#fff;border:none;padding:0.5rem 1.2rem;border-radius:8px;cursor:pointer;">Post</button>
                    </form>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <?php include 'footer.php'; ?>
  
  <script>
    // Profile dropdown toggle
    function toggleDropdown(img) {
      document.querySelectorAll('.dropdown-content').forEach(function(drop) {
        if (!drop.contains(img)) drop.style.display = 'none';
      });
      var dropdown = img.nextElementSibling;
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    document.addEventListener('click', function(e) {
      if (!e.target.classList.contains('user-avatar')) {
        document.querySelectorAll('.dropdown-content').forEach(function(drop) {
          drop.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>