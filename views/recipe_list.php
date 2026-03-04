<?php
// Example recipe list view
?>
<div class="recipe-list">
  <?php foreach ($recipes as $recipe): ?>
    <div class="recipe-card">
      <img src="<?= htmlspecialchars($recipe->image) ?>" alt="<?= htmlspecialchars($recipe->title) ?>" />
      <h3><?= htmlspecialchars($recipe->title) ?></h3>
      <p><?= htmlspecialchars($recipe->description) ?></p>
      <button class="btn" onclick="ajaxAction({action: 'like', recipe_id: <?= $recipe->id ?>, csrf_token: '<?= $_SESSION['csrf_token'] ?>'})">Like</button>
      <button class="btn" onclick="ajaxAction({action: 'save', recipe_id: <?= $recipe->id ?>, csrf_token: '<?= $_SESSION['csrf_token'] ?>'})">Save</button>
    </div>
  <?php endforeach; ?>
</div>
