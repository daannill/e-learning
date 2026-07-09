<?php if(hasFlash('success')): ?>
    <div class="alert alert-success alert-floating">
        <?= flash('success') ?>
    </div>
<?php endif; ?>