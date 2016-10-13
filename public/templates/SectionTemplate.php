<?php
/**
 * Section Shortcode Template
 */
?>

<section class="<?= $sectionClasses ?>">
    <div class="content-container <?= $centerContent ?>">
        <h1><?= $attr['section_title'] ?></h1>
        <?php if ( ! empty($sectionImage)): ?>
            <div class="short-width">
                <div class="blurb">
                    <?= wpautop($content) ?>
                </div>
                <div class="section-image">
                    <?= $sectionImage ?>
                </div>
            </div>
        <?php else: ?>
            <div class="blurb">
                <?= wpautop($content) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
