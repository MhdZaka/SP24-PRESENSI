<div class="top-bar">
    <h1><?= ucfirst($tab) ?></h1>
    <div class="user-info">
        <i class="fas fa-user-circle"></i> 
        <?= htmlspecialchars($_SESSION['user']['first_name'] ?? $_SESSION['user']['username'] ?? 'User') ?>
    </div>
</div>