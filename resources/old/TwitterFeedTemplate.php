<?php use ClanAOD\Helpers; ?>

<h1>Twitter Activity</h1>
<ul>
    <?php foreach ($feed as $entry): ?>
        <li><a href="https://twitter.com/officialclanaod"
               target="_blank">@officialclanaod</a>
            <?php if (is_object($entry->retweeted_status)): ?>
                <?= Helpers::twitterize($entry->retweeted_status->text); ?>
            <?php else: ?>
                <?= Helpers::twitterize($entry->text); ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
