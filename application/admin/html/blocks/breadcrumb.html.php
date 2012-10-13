
<div id="breadcrumb">

	<?php for($i=0; $i < count($links) - 1; $i++): ?>
		<?= $html->langLink($text->nonBreakingSpace($links[$i]['text']), $links[$i]['url'], null, false); ?>&nbsp;&gt;
	<?php endfor; ?>

	<?= $html->escape($links[$i]['text']); ?>&nbsp;&gt;

</div>


