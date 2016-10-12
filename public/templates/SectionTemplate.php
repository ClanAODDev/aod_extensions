<?php
/**
 * Section Shortcode Template
 */
?>

<section class="<?= $sectionClasses ?>">
    <div class="content-container <?= $centerContent ?>">
        <h1><?= $attr['section_title'] ?></h1>
        <?php if ( ! empty($sectionImage)): ?>
            <p class="short-width"><?= $content ?></p>
        <?php else: ?>
            <p><?= $content ?></p>
        <?php endif; ?>
    </div>
</section>
