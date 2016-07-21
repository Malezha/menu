<div class="div-menu">
    <?php if ($menu) :?>
        <<?php echo $menu->getType() . $menu->buildAttributes();?>>
            <?php foreach ($menu->all() as $element) :?>
                <?php if ($element instanceof \Malezha\Menu\Contracts\ElementFactory) $element = $element->build(); ?>
                <?php echo $element->render($renderView);?>
            <?php endforeach;?>
        </<?php echo $menu->getType();?>>
    <?php endif;?>
</div>