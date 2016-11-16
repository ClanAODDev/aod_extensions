<?php use ClanAOD\Helpers; ?>

<h1>Twitter Activity</h1>
<ul>
    <?php foreach ($feed as $entry): ?>
        <li><a href="https://twitter.com/officialclanaod"
               target="_blank">@officialclanaod</a>
            <?= Helpers::urlify($entry->text) ?>
        </li>
    <?php endforeach; ?>
</ul>
