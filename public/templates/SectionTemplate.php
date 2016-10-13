<?php
/**
 * Section Shortcode Template
 */
?>

<section class="<?= $sectionClasses ?>">
    <div class="content-container <?= $centerContent ?>">
        <?php if ( ! empty($sectionImage)): ?>
            <div class="short-width">
                <div class="blurb">
                    <h1><?= $attr['section_title'] ?></h1>
                    <?= wpautop($content) ?>
                </div>
                <div class="section-image">
                    <?= $sectionImage ?>
                </div>
            </div>
        <?php else: ?>
            <h1><?= $attr['section_title'] ?></h1>
            <div class="blurb">
                <?= wpautop($content) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
