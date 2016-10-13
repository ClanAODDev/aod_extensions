<?php
/**
 * Section Shortcode Template
 */
?>

<section class="<?= $sectionClasses ?>">
    <div class="content-container <?= $centerContent ?>">
        <h1><?= $attr['section_title'] ?></h1>
        <?php if ( ! empty($sectionImage)): ?>
            <div class="short-width"><?= wpautop($content) ?></div>
        <?php else: ?>
            <?= wpautop($content) ?>
        <?php endif; ?>
    </div>
</section>
