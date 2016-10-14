<?php
/**
 * Clan Announcements Template
 */
?>

<ul>
    <?php $i = 1;
    foreach ($threads->channel->item as $thread): ?>
        <?php if ($i <= $attrs['limit']): ?>
            <li>
                <a href="<?= $thread->guid ?>"><?= $thread->title ?></a>
                <br/>Posted <?= $thread->pubDate ?>
            </li>
        <?php endif;
        $i++; ?>
    <?php endforeach; ?>
</ul>
