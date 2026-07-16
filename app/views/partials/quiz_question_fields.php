<?php
    $q = $q ?? [];
    $questionText = $q['question'] ?? '';
    $options = $q['options'] ?? [1 => '', 2 => '', 3 => '', 4 => ''];
    $correct = $q['correct'] ?? ''; 
?>


<div class="form-group">
    <label class="form-label">Question</label>
    <textarea
        class="form-control <?= hasFlash("errors.quiz.$index.question") ? 'error' : '' ?>"
        name="quiz[<?= $index ?>][question]"
        rows="2"
        placeholder="e.g. What does PHP stand for?"
    ><?= htmlspecialchars($questionText) ?></textarea>
    <?= error("quiz.$index.question") ?>
</div>

<div class="quiz-options">
    <?php for ($i = 1; $i <= 4; $i++) : ?>
        <div class="quiz-option">
            <input
                type="radio"
                name="quiz[<?= $index ?>][correct]"
                value="<?= $i ?>"
                <?= (string) $correct === (string) $i ? 'checked' : '' ?>
            >
            <input
                class="form-control <?= hasFlash("errors.quiz.$index.options.$i") ? 'error' : '' ?>"
                type="text"
                name="quiz[<?= $index ?>][options][<?= $i ?>]"
                value="<?= htmlspecialchars($options[$i] ?? '') ?>"
                placeholder="Option <?= chr(64 + $i) ?>"
            >
            <?= error("quiz.$index.options.$i") ?>
        </div>
    <?php endfor; ?>
    <?= error("quiz.$index.correct") ?>
</div>