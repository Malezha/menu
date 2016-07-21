<?php if ($canDisplay) :?>
<li<?=$attributes;?>>
    <a href="<?=$url;?>"<?=$linkAttributes;?>><?=$title;?></a>
    <?=$builder->render($renderView);?>
</li>
<?php endif;?>
