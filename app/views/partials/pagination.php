<div class="pagination">

    <?php if ($page > 1) : ?>

        <a
            href="<?= buildQuery(['page' => $page - 1]) ?>"
            class="pagination-btn pagination-nav"
            aria-label="Previous page"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-left"></use>
            </svg>
        </a>

    <?php else : ?>

        <button
            type="button"
            class="pagination-btn pagination-nav"
            disabled
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-left"></use>
            </svg>
        </button>

    <?php endif; ?>


    <?php foreach (paginate($page, $totalPages) as $item) : ?>

        <?php if ($item === '...') : ?>

            <span class="pagination-ellipsis">
                &hellip;
            </span>

        <?php else : ?>

            <a
                href="<?= buildQuery(['page' => $item]) ?>"
                class="pagination-btn <?= $item === $page ? 'active' : '' ?>"
                <?= $item === $page ? 'aria-current="page"' : '' ?>
            >
                <?= $item ?>
            </a>

        <?php endif; ?>

    <?php endforeach; ?>


    <?php if ($page < $totalPages) : ?>

        <a
            href="<?= buildQuery(['page' => $page + 1]) ?>"
            class="pagination-btn pagination-nav"
            aria-label="Next page"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right"></use>
            </svg>
        </a>

    <?php else : ?>

        <button
            type="button"
            class="pagination-btn pagination-nav"
            disabled
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right"></use>
            </svg>
        </button>

    <?php endif; ?>

</div>