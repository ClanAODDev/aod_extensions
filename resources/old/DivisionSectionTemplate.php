<?php
/**
 * Division Section Template
 */

use ClanAOD\Helpers;

?>
<div class="section-sub-section automenu">
    <?php if ( ! empty($attr['section_title'])): ?>
        <h2 id="<?= Helpers::anchored($attr['section_title']); ?>"><?= $attr['section_title'] ?></h2>
    <?php endif; ?>
    <?= wpautop($content) ?>
</div>



