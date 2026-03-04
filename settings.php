<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = $_SESSION['user'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? $user['name'];
    $email = $_POST['email'] ?? $user['email'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate and update
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user['id']);
            $stmt->execute();
            $success = 'Password updated successfully';
        }
    }
    
    // Update name and email if no errors
    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user['id']);
        if ($stmt->execute()) {
            // Update session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $success = $success ? $success . ' and profile updated' : 'Profile updated successfully';
        } else {
            $error = 'Failed to update profile';
        }
    }
}
?>

<?php include 'header.php'; ?>

<body >

<div class="main-container">
  <aside class="sidebar">
    <h3>Settings</h3>
    <ul class="categories-list">
      <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
         <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Account Settings</a></li>
    </ul>
  </aside>
  
  <main class="settings-content">
    <h1>Account Settings</h1>
    
    <?php if ($error): ?>
      <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="settings-form">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
      </div>
      
      <div class="form-group">
        <label for="current_password">Current Password (leave blank to keep unchanged)</label>
        <input type="password" id="current_password" name="current_password">
      </div>
      
      <div class="form-group">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password">
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password">
      </div>
      
      <button type="submit" class="btn-save">Save Changes</button>
    </form>
  </main>
</div>

<style>
   body {
      font-family: 'Poppins', sans-serif;
      background: url('images/backgroundsign.jpg') center/cover no-repeat fixed;
      color: var(--dark);
      line-height: 1.6;
    }
    
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(255, 255, 255, 0.45);
      z-index: -1;
    }
.settings-content {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 10px;
  padding: 2rem;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  border: 1px solid rgba(0,0,0,0.1);
  flex-grow: 1;
}

.settings-form {
  max-width: 600px;
  margin-top: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
}

.btn-save {
  background-color: var(--logo-dark-orange);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-save:hover {
  background-color: var(--logo-brown);
}

.alert {
  padding: 1rem;
  border-radius: 5px;
  margin-bottom: 1.5rem;
}

.alert.error {
  background-color: rgba(230, 57, 70, 0.1);
  color: #e63946;
}

.alert.success {
  background-color: rgba(46, 204, 113, 0.1);
  color: #2ecc71;
}

.categories-list a.active {
  color: var(--logo-dark-orange);
  font-weight: 600;
}
</style>


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