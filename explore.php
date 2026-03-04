<?php
session_start();

require_once 'includes/config.php'; // This initializes $conn
require_once 'includes/functions.php';

// Initialize variables
$searchQuery = '';
$categoryFilter = '';
$sortBy = 'liked';
$recipes = [];
$error = '';

// Process search and filters if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
        $categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'liked';
        
        // Build the base query
        $user_id = isLoggedIn() ? $_SESSION['user']['id'] : 0;
        $query = "SELECT r.*, u.name as user_name, u.avatar as user_avatar,
                  COUNT(DISTINCT l.id) as like_count,
                  COUNT(DISTINCT c.id) as comment_count,
                  MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as is_liked,
                  MAX(CASE WHEN s.user_id = ? THEN 1 ELSE 0 END) as is_saved
                  FROM recipes r
                  JOIN users u ON r.user_id = u.id
                  LEFT JOIN likes l ON r.id = l.recipe_id
                  LEFT JOIN comments c ON r.id = c.recipe_id
                  LEFT JOIN saved_recipes s ON r.id = s.recipe_id AND s.user_id = ?";
        
        $whereClauses = [];
        $params = [];
        $types = '';
        
        if (!empty($searchQuery)) {
            $whereClauses[] = "(r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?)";
            $params[] = "%$searchQuery%";
            $params[] = "%$searchQuery%";
            $params[] = "%$searchQuery%";
            $types .= 'sss';
        }
        
        if (!empty($categoryFilter) && $categoryFilter !== 'all') {
            $whereClauses[] = "r.category = ?";
            $params[] = $categoryFilter;
            $types .= 's';
        }
        
        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $query .= " GROUP BY r.id";
        
        // Sorting options
        switch ($sortBy) {
            case 'newest':
                $query .= " ORDER BY r.created_at DESC";
                break;
            case 'liked':
            default:
                $query .= " ORDER BY like_count DESC";
                break;
        }
        
        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Always bind user_id, user_id, user_id first (for is_liked, is_saved, saved_recipes join)
        $baseParams = [$user_id, $user_id, $user_id];
        $baseTypes = 'iii';
        if (!empty($params)) {
            $stmt->bind_param($baseTypes . $types, ...$baseParams, ...$params);
        } else {
            $stmt->bind_param($baseTypes, ...$baseParams);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $recipes = $result->fetch_all(MYSQLI_ASSOC);
        
        // Fetch comments for each recipe
        foreach ($recipes as &$recipe) {
            $stmt2 = $conn->prepare("SELECT c.*, u.name as user_name, u.avatar as user_avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = ? ORDER BY c.created_at DESC LIMIT 3");
            $stmt2->bind_param("i", $recipe['id']);
            $stmt2->execute();
            $recipe['comments'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        unset($recipe);
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<?php include 'header.php'; ?>

<div class="main-container">
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
  
  <main class="explore-content">
    <h1>Explore Filipino Recipes</h1>
    
    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="GET" action="explore.php" class="search-filters">
      <div class="search-bar">
        <input type="text" name="search" placeholder="Search recipes..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
      </div>
    </form>
    
    <div class="recipe-grid">
      <?php if (empty($recipes)): ?>
        <div class="no-results">
          <p>No recipes found matching your search criteria.</p>
        </div>
      <?php else: ?>
        <?php foreach ($recipes as $recipe): ?>
          <div class="recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
            <?php 
            // Handle image path - ensure it's properly formatted
            $imagePath = 'images/default_recipe.jpg'; // Default image
            
            // Check if image exists in the database
            if (!empty($recipe['image'])) {
                // Check if the path is relative or absolute
                if (file_exists($recipe['image'])) {
                    // If the full path exists, use it
                    $imagePath = $recipe['image'];
                } elseif (file_exists('uploads/' . $recipe['image'])) {
                    // If it exists in uploads directory
                    $imagePath = 'uploads/' . $recipe['image'];
                } elseif (file_exists('images/' . $recipe['image'])) {
                    // If it exists in images directory
                    $imagePath = 'images/' . $recipe['image'];
                }
            }
            ?>
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-img">
            <div class="recipe-content">
              <div class="recipe-header">
                <div class="recipe-user">
                  <img src="<?php echo htmlspecialchars($recipe['user_avatar'] ?: 'images/default-avatar.jpg'); ?>" alt="<?php echo htmlspecialchars($recipe['user_name']); ?>" class="comment-avatar" style="width:32px;height:32px;">
                  <span><?php echo htmlspecialchars($recipe['user_name']); ?></span>
                </div>
                <span class="recipe-date"><?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></span>
              </div>
              <h3 class="recipe-title">
                <a href="recipe.php?id=<?php echo $recipe['id']; ?>">
                  <?php echo htmlspecialchars($recipe['title']); ?>
                </a>
              </h3>
              <p class="recipe-category"><?php echo htmlspecialchars($recipe['category']); ?></p>
              <p class="recipe-description"><?php echo htmlspecialchars($recipe['description']); ?></p>
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
              <div class="recipe-comments" style="display:none;">
                <div class="comments-list">
                  <?php foreach ($recipe['comments'] as $comment): ?>
                    <div class="comment">
                      <img src="<?php echo htmlspecialchars($comment['user_avatar'] ?: 'images/default-avatar.jpg'); ?>" alt="<?php echo htmlspecialchars($comment['user_name']); ?>" class="comment-avatar">
                      <div class="comment-content">
                        <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                        <p><?php echo htmlspecialchars($comment['content']); ?></p>
                        <small><?php echo date('M j, g:i a', strtotime($comment['created_at'])); ?></small>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php if (isLoggedIn()): ?>
                <form class="comment-form" data-recipe-id="<?php echo $recipe['id']; ?>"
                      data-user-name="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>"
                      data-user-avatar="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?? 'images/default-avatar.jpg'); ?>">
                  <textarea placeholder="Add a comment..." required></textarea>
                  <button type="submit">Post</button>
                </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>
 <link rel="stylesheet" href="styles4.css">

<?php include 'footer.php'; ?>
<script src="js/main.js"></script>
<script>
// Toggle comments visibility
document.querySelectorAll('.comment-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentsSection = this.closest('.recipe-card').querySelector('.recipe-comments');
        commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
    });
});

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