<?php if ($canDisplay) :?>
    <li<?php echo $attributes;?>>
        <a href="<?php echo $url;?>"<?php echo $linkAttributes;?>><?php echo $title;?></a>
        <?php echo $builder->render($renderView);?>
    </li>
<?php endif;?>
