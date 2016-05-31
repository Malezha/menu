<div class="div-menu">
    <?php if ($menu) :?>
        <<?php echo $menu->getType() . $menu->buildAttributes();?>>
            <?php foreach ($menu->all() as $element) :?>
                <?php echo $element->render($renderView);?>
            <?php endforeach;?>
        </<?php echo $menu->getType();?>>
    <?php endif;?>
</div>