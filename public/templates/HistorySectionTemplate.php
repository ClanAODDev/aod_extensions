<div class="cd-timeline-block">

    <div class="cd-timeline-img cd-location">
        <img src="<?php echo get_template_directory_uri() . "/public/images/cd-icon-location.svg" ?>" alt="Picture">
    </div>

    <div class="cd-timeline-content">
        <h2><?= $attr['section_title'] ?></h2>
        <?= wpautop($content) ?>
        <h2 class="cd-date"><?= $attr['date_text'] ?></h2>
    </div>
</div>