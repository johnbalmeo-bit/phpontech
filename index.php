<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get recent recipes with like/comment counts and user info
$query = "SELECT r.*, u.name as user_name, u.avatar as user_avatar,
          COUNT(DISTINCT l.id) as like_count,
          COUNT(DISTINCT c.id) as comment_count,
          MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as is_liked,
          MAX(CASE WHEN s.user_id = ? THEN 1 ELSE 0 END) as is_saved
          FROM recipes r
          JOIN users u ON r.user_id = u.id
          LEFT JOIN likes l ON r.id = l.recipe_id
          LEFT JOIN comments c ON r.id = c.recipe_id
          LEFT JOIN saved_recipes s ON r.id = s.recipe_id AND s.user_id = ?
          GROUP BY r.id
          ORDER BY r.created_at DESC
          LIMIT 10";

$user_id = isLoggedIn() ? $_SESSION['user']['id'] : 0;
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get comments for each recipe
foreach ($recipes as &$recipe) {
    $stmt = $conn->prepare("SELECT c.*, u.name as user_name, u.avatar as user_avatar 
                           FROM comments c 
                           JOIN users u ON c.user_id = u.id 
                           WHERE c.recipe_id = ? 
                           ORDER BY c.created_at DESC 
                           LIMIT 3");
    $stmt->bind_param("i", $recipe['id']);
    $stmt->execute();
    $recipe['comments'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
unset($recipe); // Break the reference
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Linamnam | Share & Discover Filipino Recipes</title>
  <meta name="description" content="Share your Filipino food creations, discover authentic recipes, and connect with fellow food lovers.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="styles5.css">
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-container">
        <img src="images/LOGO-LINAMNAM.png" alt="Linamnam Logo" class="logo">
        <div class="logo-name">LINAMNAM</div>
      </div>
      
    
      <nav class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="explore.php"><i class="fas fa-compass"></i> Explore</a>
        <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
        <?php if(isset($_SESSION['user'])): ?>
          <div class="profile-menu">
            <img src="<?php echo $_SESSION['user']['avatar']; ?>" alt="Profile" class="user-avatar">
            <div class="dropdown-content">
              <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
              <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
          <a href="signup.php" class="signup-button">Sign Up</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  
   <div class="main-container">
    <!-- Left Sidebar with Categories -->
    <aside class="sidebar">
      <h3>Categories</h3>
      <ul class="categories-list">
        <li><a href="all_recipes.php"><i class="fas fa-utensils"></i> All Recipes</a></li>
        <li><a href="main_dishes.php"><i class="fas fa-drumstick-bite"></i> Main Dishes</a></li>
        <li><a href="kakanin.php"><i class="fas fa-bread-slice"></i> Kakanin</a></li>
        <li><a href="desserts.php"><i class="fas fa-ice-cream"></i> Desserts</a></li>
        <li><a href="drinks.php"><i class="fas fa-mug-hot"></i> Drinks</a></li>
        <li><a href="vegetarian.php"><i class="fas fa-leaf"></i> Vegetarian</a></li>
            </ul>
    </aside>
    
      <main class="recipe-feed">
      <?php if(isset($_SESSION['user'])): ?>
      <div class="create-post">
        <form class="post-form" action="post_recipe.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="recipe-title">Recipe Title</label>
            <input type="text" id="recipe-title" name="title" placeholder="What's your recipe called?" required>
          </div>
          
          <div class="form-group">
            <label for="recipe-category">Category</label>
            <select id="recipe-category" name="category" required>
              <option value="">Select a category</option>
              <option value="Main Dishes">Main Dishes</option>
              <option value="Kakanin">Kakanin</option>
              <option value="Desserts">Desserts</option>
              <option value="Drinks">Drinks</option>
              <option value="Vegetarian">Vegetarian</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="recipe-description">Description</label>
            <textarea id="recipe-description" name="description" placeholder="Describe your recipe and share the ingredients and steps..." required></textarea>
          </div>
          
          <div class="post-actions">
            <div class="file-upload">
              <input type="file" id="recipe-image" name="image" accept="image/*">
              <label for="recipe-image" class="file-upload-label">
                <i class="fas fa-camera"></i> Add Photo
              </label>
            </div>
            <button type="submit" class="post-button">Post</button>
          </div>
        </form>
      </div>
      <?php else: ?>
      <div class="create-post" style="text-align: center; padding: 2rem;">
        <h3>Join our community to share your recipes!</h3>
        <a href="signup.php" class="signup-button" style="display: inline-block; margin-top: 1rem;">
          <i class="fas fa-user-plus"></i> Sign Up Now
        </a>
      </div>
      <?php endif; ?>
      
      <!-- Recipe Posts -->
      <?php foreach ($recipes as $recipe): ?>
      <div class="recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
        <img src="<?php echo $recipe['image'] ?: 'images/recipe-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
        <div class="recipe-content">
          <div class="recipe-header">
            <div class="recipe-user">
              <img src="<?php echo $recipe['user_avatar']; ?>" alt="<?php echo htmlspecialchars($recipe['user_name']); ?>">
              <div class="recipe-user-info">
                <h4><?php echo htmlspecialchars($recipe['user_name']); ?></h4>
                <p><?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></p>
              </div>
            </div>
            <i class="fas fa-ellipsis-h"></i>
          </div>
          
          <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
          <p class="recipe-description"><?php echo htmlspecialchars($recipe['description']); ?></p>
          
          <div class="recipe-details">
            <div class="recipe-detail">
              <i class="fas fa-clock"></i>
              <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
            </div>
            <div class="recipe-detail">
              <i class="fas fa-utensils"></i>
              <span><?php echo $recipe['servings']; ?> servings</span>
            </div>
            <div class="recipe-detail">
              <i class="fas fa-fire"></i>
              <span><?php echo $recipe['difficulty']; ?></span>
            </div>
          </div>
          
          <div class="recipe-actions">
            <button class="action-button like-btn" data-recipe-id="<?php echo $recipe['id']; ?>">
              <i class="<?php echo $recipe['is_liked'] ? 'fas liked' : 'far'; ?> fa-heart"></i>
              <span class="like-count"><?php echo $recipe['like_count']; ?></span>
            </button>
            <button class="action-button comment-toggle">
              <i class="far fa-comment"></i>
              <span class="comment-count"><?php echo $recipe['comment_count']; ?></span>
            </button>
            <button class="action-button save-btn <?php echo $recipe['is_saved'] ? 'saved' : ''; ?>" data-recipe-id="<?php echo $recipe['id']; ?>">
              <i class="<?php echo $recipe['is_saved'] ? 'fas' : 'far'; ?> fa-bookmark"></i>
            </button>
          </div>
          
          <div class="recipe-comments">
            <div class="comments-list">
              <?php foreach ($recipe['comments'] as $comment): ?>
                <div class="comment">
                  <img src="<?php echo $comment['user_avatar']; ?>" alt="<?php echo htmlspecialchars($comment['user_name']); ?>" class="comment-avatar">
                  <div class="comment-content">
                    <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    <small><?php echo date('M j, g:i a', strtotime($comment['created_at'])); ?></small>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            
            <?php if(isset($_SESSION['user'])): ?>
            <form class="comment-form" data-recipe-id="<?php echo $recipe['id']; ?>"
                  data-user-name="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>"
                  data-user-avatar="<?php echo $_SESSION['user']['avatar']; ?>">
              <textarea placeholder="Add a comment..." required></textarea>
              <button type="submit">Post</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </main>
    
   
    
  </div>
  
  <footer>
    <div class="footer-links">
      <a href="about.php">About</a>
      
      <a href="help.php">Help</a>
      <a href="privacy.php">Privacy</a>
      <a href="terms.php">Terms</a>
    </div>
    
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-youtube"></i></a>
    </div>
    
    <p>&copy; <?php echo date('Y'); ?> Linamnam. Celebrating Filipino culinary heritage.</p>
  </footer>

  <script>
  // Toggle comments visibility
  document.querySelectorAll('.comment-toggle').forEach(btn => {
      btn.addEventListener('click', function() {
          const commentsSection = this.closest('.recipe-card').querySelector('.recipe-comments');
          commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
      });
  });
  </script>

  <script src="js/main.js"></script>
</body>
</html>