<ul class="tabs">

	<?php foreach($links as $link): ?>
		<li><?= $html->langLink($link['label'], '/admin/'. $link['url'], $link['attributes'], false); ?></li>
	<?php endforeach; ?>
	
</ul>
<div class="clear"></div>