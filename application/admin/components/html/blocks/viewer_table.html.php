<?php if($dataset->data):?>

	<?php if(!$dataset->list): ?>

		<table class="dataset <?= $dataset->orientation; ?> <?= $dataset->css_class; ?>">

			<?php if($dataset->orientation == 'vertical'): ?>

				<?php foreach($dataset->data as $header => $columns): ?>
					<tr>
						<td class="viewer-header <?= $dataset->orientation; ?>">
							<?php if(isset($dataset->header[$header])):?>
								<?= $html->escape($dataset->header[$header]); ?>
							<?php else: ?>
								<?= $html->escape($header); ?>
							<?php endif;?>
						</td>

						<?php foreach($columns as $column):?>
							<td class="<?= $dataset->orientation; ?>">
								<?php if(!is_object($column)): ?>
									<?php if(!isset($dataset->links[$header])):?>
										<?= nl2br($html->escape($column)); ?>
									<?php else:?>
										<?= $html->link($html->escape($column), $dataset->links[$header], array('class' => 'viewer-link')); ?>
									<?php endif; ?>	
								<?php else: ?>
									<?= $this->load->block('component', array('template' => 'components/viewer_table',	'values' => array('dataset' => $column))); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

			<?php elseif($dataset->orientation == 'horizontal'): ?>
				<tr>
					<?php foreach($dataset->data[0] as $header => $dummy):?>
						<td class="viewer-header <?= $dataset->orientation; ?>">
							<?php if(isset($dataset->header[$header])):?>
								<?= $html->escape($dataset->header[$header]); ?>
							<?php else: ?>
								<?= $html->escape($header); ?>
							<?php endif;?>
						</td>
					<?php endforeach; ?>
				</tr>

				<?php foreach($dataset->data as $columns):?>
					<tr>
						<?php foreach($columns as $header => $column):?>
							<td class="<?= $dataset->orientation; ?>">
								<?php if(!is_object($column)): ?>
									<?php if(!isset($dataset->links[$header])):?>
										<?= nl2br($html->escape($column)); ?>
									<?php else:?>
										<?= $html->link($html->escape($column), $dataset->links[$header], array('class' => 'viewer-link')); ?>
									<?php endif; ?>	
								<?php else: ?>
									<?= $this->load->block('component', array('template' => 'components/viewer_table',	'values' => array('dataset' => $column))); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

			<?php elseif($dataset->orientation == 'registry'): ?>
				<?php foreach($dataset->data as $columns):?>
					<tbody>
						<?php foreach($columns as $header => $column):?>
							<tr>
								<td class="viewer-header <?= $dataset->orientation; ?>">
									<?php if(isset($dataset->header[$header])):?>
										<?= $html->escape($dataset->header[$header]); ?>
									<?php else: ?>
										<?= $html->escape($header); ?>
									<?php endif;?>
								</td>
								<td class="<?= $dataset->orientation; ?>">
									<?php if(!is_object($column)): ?>
										<?php if(!isset($dataset->links[$header])):?>
											<?= nl2br($html->escape($column)); ?>
										<?php else:?>
											<?= $html->link($html->escape($column), $dataset->links[$header], array('class' => 'viewer-link')); ?>
										<?php endif; ?>			
									<?php else: ?>
										<?= $this->load->block('component', array('template' => 'components/viewer_table',	'values' => array('dataset' => $column))); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				<?php endforeach; ?>
			<?php endif; ?>

		</table>

	<?php else: ?>

		<?php if($dataset->orientation == 'vertical' || $dataset->orientation == 'registry'): ?>
			<ul class="<?= $dataset->css_class; ?>">
				<?php foreach($dataset->data as $header => $data):?>
					<li><?= $data ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<div class="comma-list">
				<?= implode(', ',$dataset->data); ?>
			</div>	
		<?php endif; ?>

	<?php endif;?>

<?php endif; ?>
