<?php
$minimumCorrect = old('minimum_correct');

if ($minimumCorrect === null && isset($settings)) {
    $minimumCorrect = $settings['minimum_correct'] ?? '';
}

$maxAttempts = old('max_attempts');

if ($maxAttempts === null && isset($settings)) {
    $maxAttempts = $settings['max_attempts'] ?? '';
}

$timer = old('timer');

if ($timer === null && isset($settings)) {
    $timer = $settings['timer'] ?? '';
}

$resetMinutes = old('reset_minutes');

if ($resetMinutes === null && isset($settings)) {
    $resetMinutes = $settings['reset_minutes'] ?? '';
}
?>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label" for="minimum_correct">Minimum Correct to Pass</label>

        <input
            class="form-control <?= invalid('minimum_correct') ?>"
            type="number"
            id="minimum_correct"
            name="minimum_correct"
            value="<?= $minimumCorrect ?>"
            min="1"
            max="5"
            required
        >

        <?php if (hasFlash('errors.minimum_correct')) : ?>

            <?= error('minimum_correct') ?>

        <?php else : ?>

            <p class="form-hint" id="minimumCorrectHint">
                Out of questions.
            </p>

        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label" for="max_attempts">Max Attempts</label>

        <input
            class="form-control <?= invalid('max_attempts') ?>"
            type="number"
            id="max_attempts"
            name="max_attempts"
            value="<?= $maxAttempts ?>"
            min="1"
            required
        >

        <?php if (hasFlash('errors.max_attempts')) : ?>

            <?= error('max_attempts') ?>

        <?php else : ?>

            <p class="form-hint">
                Maximum quiz attempts per session.
            </p>

        <?php endif; ?>
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label class="form-label" for="timer">Timer (minutes)</label>

        <input
            class="form-control <?= invalid('timer') ?>"
            type="number"
            id="timer"
            name="timer"
            value="<?= $timer ?>"
            min="1"
            required
        >

        <?php if (hasFlash('errors.timer')) : ?>

            <?= error('timer') ?>

        <?php else : ?>

            <p class="form-hint">
                Time limit to complete the quiz.
            </p>

        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label" for="reset_minutes">Reset After (minutes)</label>

        <input
            class="form-control <?= invalid('reset_minutes') ?>"
            type="number"
            id="reset_minutes"
            name="reset_minutes"
            value="<?= $resetMinutes ?>"
            min="0"
            required
        >

        <?php if (hasFlash('errors.reset_minutes')) : ?>

            <?= error('reset_minutes') ?>

        <?php else : ?>

            <p class="form-hint">
                Cooldown before students can retry after using all attempts.
            </p>

        <?php endif; ?>
    </div>
</div>