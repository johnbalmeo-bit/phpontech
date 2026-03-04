<?php
session_start();
require_once 'includes/functions.php';
$page_title = ucfirst(basename($_SERVER['PHP_SELF'], '.php'));
?>

<?php include 'header.php'; ?>



<div class="static-page">
  <h1><?php echo $page_title; ?></h1>
  
  <div class="page-content">
    <?php if ($page_title === 'About'): ?>
      <h2>About Linamnam</h2>
      <p>Linamnam is a platform dedicated to preserving and sharing Filipino culinary heritage. Our mission is to connect food lovers with authentic Filipino recipes and create a community where everyone can share their culinary creations.</p>
      
      <h3>Our Story</h3>
      <p>Founded in 2023, Linamnam started as a small project to document family recipes. It has since grown into a thriving community of home cooks and professional chefs alike.</p>
      
    
      
    <?php elseif ($page_title === 'Help'): ?>
      <h2>Help Center</h2>
      <div class="help-section">
        <h3>Getting Started</h3>
        <p>Learn how to create an account, post recipes, and interact with other members of the Linamnam community.</p>
      </div>
      
    <?php elseif ($page_title === 'Privacy'): ?>
      <h2>Privacy Policy</h2>
      <p>We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website.</p>
      
    <?php elseif ($page_title === 'Terms'): ?>
      <h2>Terms of Service</h2>
      <p>By accessing or using the Linamnam website, you agree to be bound by these terms of service. Please read them carefully before using our services.</p>
    <?php endif; ?>
  </div>
</div>

<link rel="stylesheet" href="styles3.css">

<?php include 'footer.php'; ?>