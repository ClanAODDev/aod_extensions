<div class="timeline-block">

    <div class="timeline-bullet cd-location">
        <img src="<?php echo get_template_directory_uri() . "/public/images/cd-icon-location.svg" ?>" alt="Picture">
    </div>

    <div class="timeline-content">
        <h2><?= $attr['section_title'] ?></h2>
        <?= wpautop($content) ?>
    </div>
</div>