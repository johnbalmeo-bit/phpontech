<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($email, $password)) {
        redirect('index.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Linamnam</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="styleslogin.css">
   <link rel="stylesheet" href="stylessignup.css">
 
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
  
  <?php if(isset($_SESSION['user'])): ?>
    <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
    <div class="profile-menu">
      <img src="<?php echo $_SESSION['user']['avatar']; ?>" alt="Profile" class="user-avatar">
      <div class="dropdown-content">
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="saved.php"><i class="fas fa-bookmark"></i> Saved Recipes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  <?php else: ?>
    <a href="login.php?redirect=notification"><i class="fas fa-bell"></i> Notifications</a>
    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="signup.php" class="signup-button">Sign Up</a>
  <?php endif; ?>
</nav>

  </div>
</header>
  <div class="login-container">
    <div class="logo-container">
      <img src="images/LOGO-LINAMNAM.png" alt="Linamnam Logo" class="logo">
      <div class="logo-name">LINAMNAM</div>
    </div>
    
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      
      <button type="submit" class="btn">Login</button>
    </form>
    
    <div class="register-link">
      Don't have an account? <a href="signup.php">Sign up</a>
    </div>
  </div>
</body>
</html>