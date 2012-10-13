
<div class="summary">
	
	<?= $html->link($all_text, $all_url, array('class' => 'all')) ?>
	<h2><?= $title; ?></h2> 
	
	<?php if($entries): ?>
	
		<table>
			<?php foreach($entries as $entry): ?>
				<tr>
					<td>
						<?= $entry[0]; ?>
					</td>
					<?php if(isset($entry[1])): ?>
						<td class="cell-right">
							<?= $entry[1]; ?>
						</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</table>
	
	<?php else: ?>
	
		<em><?= $this->lang->table['empty']; ?></em>
	
	<?php endif; ?>

</div>

