<section class="<?= $attr['section_name'] ?>">
    <div class="content-container <?= ((bool) $attr['centered']) ? 'centered' : null; ?>">
        <h1><?= $attr['section_title'] ?></h1>
        <?= $content ?>
    </div>
</section>
