<ul>
    <?php foreach ($threads->channel->item as $thread): ?>
        <li><a href="<?= $thread->guid ?>" target="_blank"><?= $thread->title ?></a><br />Posted <?= $thread->pubDate ?></li>
    <?php endforeach; ?>
</ul>
