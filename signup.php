<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (emailExists($email)) {
        $error = 'Email already exists';
    } else {
        if (register($name, $email, $password)) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Linamnam</title>
  <meta name="description" content="Create an account to share and discover Filipino recipes">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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

<main class="main-content">
  <div class="signup-container">
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
      <form method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required minlength="6">
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
        </div>

        <button type="submit" class="btn">Sign Up</button>
      </form>

      <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
      </div>
    <?php else: ?>
      <div class="login-link">
        <a href="login.php">Go to Login Page</a>
      </div>
    <?php endif; ?>
  </div>
</main>

</body>
</html>