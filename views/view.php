<?php if ($menu) :?>
    <<?= $menu->getType() . $menu->buildAttributes();?>>
        <?php foreach ($menu->all() as $element) :?>
            <?php if ($element instanceof \Malezha\Menu\Contracts\ElementFactory) $element = $element->build(); ?>
            <?= $element->render($renderView);?>
        <?php endforeach;?>
    </<?= $menu->getType();?>>
<?php endif;?>