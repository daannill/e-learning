<div
    class="editor-wrapper <?= invalid('content') ?>"
    id="editor-wrapper"
>
    <div id="editor"></div>
</div>

<input
    type="hidden"
    id="content-input"
    name="content"
    value="<?= htmlspecialchars(old('content') ?: ($material['content'] ?? '')) ?>"
>

<?php if (hasFlash('errors.content')): ?>
    <?= error('content') ?>
<?php else: ?>
    <p class="form-hint">
        Write the learning material content here.
    </p>
<?php endif; ?>