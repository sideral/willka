<div class="layout">
	
	<?php foreach($rows as $columns): ?>

		<div>
			<?php $num_cols = count($columns); ?>
			<?php for($i=0; $i < $num_cols; $i++) : ?>
				<div class="layout-column <?= $i == $num_cols-1? 'last-column':''; ?>" style="width:<?= (100 - 2*($num_cols-1))/$num_cols ?>%">
					<?php foreach($columns[$i] as $element): ?>
						<?= $element->getHtml(); ?>
					<?php endforeach;?>
				</div>
			<?php endfor; ?>

			<div class="clear"></div>
		</div>

	<?php endforeach; ?>

	<div class="clear"></div>
</div>