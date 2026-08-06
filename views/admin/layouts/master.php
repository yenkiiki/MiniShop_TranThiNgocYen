<?php require_once __DIR__ . '/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/sidebar.php'; ?>
        
        <main class="col py-3">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>