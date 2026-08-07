<?php include "header.php"; ?>

<div class="container-fluid">
    <div class="row">
        <?php include "sidebar.php"; ?>
        
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <?= $content ?? '' ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>