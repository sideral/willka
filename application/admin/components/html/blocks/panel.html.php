<?php if($title):?>
	<h2 class="panel-title">
		<?= $html->escape($title);?>
	</h2>
<?php endif;?>
	
<div class="action-bar">
	

	<?php foreach($links as $link): ?>
		<?= $html->langLink($link['label'], '/admin/'. $link['url'], $link['attributes'], false); ?>
	<?php endforeach; ?>

	<div class="clear"></div>
</div>
