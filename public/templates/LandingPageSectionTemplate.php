<?php
/**
 * Section Shortcode Template
 */
?>

<section class="<?= $sectionClasses ?>" style="background: url(<?= ($sectionBg) ?: null ?>) <?= $sectionBgColor ?> no-repeat center 0">
    <div class="section-content-container <?= $centerContent ?>">
        <?php if ( ! empty($sectionImage)): ?>
            <div class="section--short-width">
                <div class="section-blurb">
                    <h1><?= $attr['section_title'] ?></h1>
                    <?= wpautop($content) ?>
                </div>
                <div class="section-image">
                    <?= $sectionImage ?>
                </div>
            </div>
        <?php else: ?>
            <h1><?= $attr['section_title'] ?></h1>
            <div class="section-blurb">
                <?= wpautop($content) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
