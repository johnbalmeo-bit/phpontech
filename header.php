<?php

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
   <link rel="stylesheet" href="styles2.css">
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-container">
        <img src="images/LOGO-LINAMNAM.png" alt="Linamnam Logo - Filipino Culinary Heritage" class="logo">
        <div class="logo-name">LINAMNAM</div>
      </div>
      
    
      <nav class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="explore.php"><i class="fas fa-compass"></i> Explore</a>
        <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
        <?php if(isset($_SESSION['user'])): ?>
          <div class="profile-menu" tabindex="0">
            <img src="<?php echo $_SESSION['user']['avatar']; ?>" alt="Profile" class="user-avatar" onclick="toggleDropdown(event)">
            <div class="dropdown-content">
              <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
              <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          </div>
          <script>
          function toggleDropdown(e) {
            e.stopPropagation();
            var menu = e.target.closest('.profile-menu');
            var dropdown = menu.querySelector('.dropdown-content');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
          }
          document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-content').forEach(function(drop) {
              drop.style.display = 'none';
            });
          });
          </script>
        <?php else: ?>
          <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
          <a href="signup.php" class="signup-button">Sign Up</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>