<?php if ($menu) :?>
    <<?=$menu->getType() . $menu->buildAttributes();?>>
        <?php foreach ($menu->all() as $element) :?>
            <?php $isGroup = $element instanceof \Malezha\Menu\Contracts\SubMenu;?>
            <?php if ($isGroup) $item = $element->getItem(); else $item = $element;?>
            <?php if ($item->canDisplay()) :?>
                <li<?=$item->buildAttributes();?>>
                    <a href="<?=$item->getLink()->getUrl();?>"<?=$item->getLink()->buildAttributes();?>>
                        <?=$item->getLink()->getTitle();?>
                    </a>
                    <?php if ($isGroup) echo $element->getMenu()->render($renderView);?>
                </li>
            <?php endif;?>
        <?php endforeach;?>
    </<?=$menu->getType();?>>
<?php endif;?>