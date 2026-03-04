<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/notifications.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user']['id'];
$notifications = getUserNotifications($user_id, $conn);

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return $diff . ' seconds ago';
    $diff = round($diff / 60);
    if ($diff < 60) return $diff . ' minutes ago';
    $diff = round($diff / 60);
    if ($diff < 24) return $diff . ' hours ago';
    $diff = round($diff / 24);
    if ($diff < 7) return $diff . ' days ago';
    return date('M j, Y', $timestamp);
}
?>
<?php include 'header.php'; ?>

<body>

<div class="main-container">
  <aside class="sidebar">
    <h3>Notifications</h3>
    <ul class="notification-types">
      <li><a href="#" class="active">All</a></li>
      <li><a href="#">Likes</a></li>
      <li><a href="#">Comments</a></li>
    </ul>
  </aside>
  
  <main class="notifications-content">
    <h1>Notifications</h1>
    
    <div class="notification-list">
      <?php if (empty($notifications)): ?>
        <div class="notification">
          <div class="notification-details">
            <p>No notifications yet.</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
          <div class="notification">
            <img src="<?php echo $notif['actor_avatar'] ? $notif['actor_avatar'] : 'images/default-avatar.jpg'; ?>" alt="User" class="notification-avatar">
            <div class="notification-details">
              <p>
                <strong><?php echo htmlspecialchars($notif['actor_name']); ?></strong>
                <?php if ($notif['type'] === 'like'): ?>
                  liked your recipe <strong><?php echo htmlspecialchars($notif['recipe_title']); ?></strong>
                <?php elseif ($notif['type'] === 'comment'): ?>
                  commented on your recipe: "<?php echo htmlspecialchars($notif['content']); ?>"
                <?php endif; ?>
              </p>
              <span class="notification-time"><?php echo timeAgo($notif['created_at']); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

  <link rel="stylesheet" href="notification.css">

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

<?php include 'footer.php'; ?>