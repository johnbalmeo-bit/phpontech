<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = $_SESSION['user'];
?>

<?php include 'header.php'; ?>

<div class="main-container">
  <aside class="sidebar">
    <h3>My Recipes</h3>
    <ul class="categories-list">
      <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
      <li><a href="saved.php" class="active"><i class="fas fa-bookmark"></i> Saved Recipes</a></li>
      <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
  </aside>
  
  <main class="saved-recipes">
    <h1>Saved Recipes</h1>
    
    <div class="recipe-grid">
      <!-- Sample saved recipes -->
      <div class="recipe-card">
        <img src="images/sinigang.jpg" alt="Sinigang" class="recipe-img">
        <div class="recipe-content">
          <div class="recipe-header">
            <div class="recipe-user">
              <img src="images/profile.jpg" alt="User Profile">
              <div class="recipe-user-info">
                <h4>Juan Dela Cruz</h4>
                <p>2 days ago</p>
              </div>
            </div>
          </div>
          <h3 class="recipe-title">Sinigang na Baboy</h3>
          <div class="recipe-actions">
            <button class="action-button">
              <i class="fas fa-heart"></i> 124
            </button>
            <button class="action-button">
              <i class="fas fa-comment"></i> 23
            </button>
            <button class="action-button unsave-btn">
              <i class="fas fa-bookmark"></i> Saved
            </button>
          </div>
        </div>
      </div>
      
      <div class="recipe-card">
        <img src="images/kakanin.jpg" alt="Kakanin" class="recipe-img">
        <div class="recipe-content">
          <div class="recipe-header">
            <div class="recipe-user">
              <img src="images/profile.jpg" alt="User Profile">
              <div class="recipe-user-info">
                <h4>Maria Santos</h4>
                <p>1 week ago</p>
              </div>
            </div>
          </div>
          <h3 class="recipe-title">Kakanin Special</h3>
          <div class="recipe-actions">
            <button class="action-button">
              <i class="fas fa-heart"></i> 89
            </button>
            <button class="action-button">
              <i class="fas fa-comment"></i> 15
            </button>
            <button class="action-button unsave-btn">
              <i class="fas fa-bookmark"></i> Saved
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<style>
.saved-recipes {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 10px;
  padding: 2rem;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  border: 1px solid rgba(0,0,0,0.1);
  flex-grow: 1;
}

.recipe-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.recipe-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.recipe-card:hover {
  transform: translateY(-5px);
}

.recipe-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.recipe-content {
  padding: 1rem;
}

.recipe-header {
  margin-bottom: 1rem;
}

.recipe-user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.recipe-user img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.recipe-user-info h4 {
  font-size: 1rem;
  margin: 0;
}

.recipe-user-info p {
  font-size: 0.8rem;
  color: var(--gray);
  margin: 0;
}

.recipe-title {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  color: var(--logo-dark-orange);
}

.recipe-actions {
  display: flex;
  justify-content: space-between;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  color: var(--gray);
  cursor: pointer;
  transition: all 0.3s;
}

.action-button:hover {
  color: var(--logo-dark-orange);
}

.unsave-btn {
  color: var(--logo-dark-orange);
}
</style>

<?php include 'footer.php'; ?>