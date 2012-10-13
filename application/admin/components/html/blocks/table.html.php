<?php foreach($custom_scripts as $script):?>
	<?= $html->javascript($script); ?>
<?php endforeach;?>
<?php foreach($custom_styles as $style):?>
	<?= $html->css($style); ?>
<?php endforeach;?>

<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<script type="text/javascript">
	function listDeleteConfirmation(name){
		return "<?= $this->lang->table['confirm'][0]; ?> " + name +  " <?= $this->lang->table['confirm'][1]; ?>";
	}
</script>

<?php if(isset($operations['add']) && $operations['add'] != null): ?>

	<?= $html->langLink($html->img('/admin/components/images/list/add.png', $this->lang->list['operations']['add']) . ' ' . $this->lang->list['operations']['add'], '/admin/'. $operations['add']['url'],
			array('class' => 'action-button', 'style' => 'float:right;margin:5px 0px;'), false) ?>

<?php endif; ?>

<h2 class="list-title">

	<?= $html->escape($title);?>

	<?php foreach($filter as $input): ?>
		<?= '&#8226;'.$input; ?>
	<?php endforeach; ?>

</h2>

<?php if($datasource): ?>

<div class="table" data-component="<?= $_component; ?>">

	<table class="<?= isset($operations['order']) ? 'ordered':'' ?>">
		<thead>
			<tr>
				<?php foreach($columns as $column => $header): ?>
					<th>
						<?php if(isset($header_links[$column])): ?>
							<?php if($current_column_sort[0] == $column):?>
								<?php if($current_column_sort[1] == 'asc'):?>
									<?= $html->img('/admin/components/images/list/down_arrow.gif'); ?>
								<?php else: ?>
									<?= $html->img('/admin/components/images/list/up_arrow.gif'); ?>
								<?php endif; ?>
							<?php endif; ?>
							<?= $html->link($header, $header_links[$column]);?>
						<?php else: ?>
							<?= $header;?>
						<?php endif; ?>					
					</th>
				<?php endforeach; ?>
				<th>
					
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($datasource as $data): ?>
				<tr data-id="<?= $data[$id_row]; ?>" >

					<?php foreach($columns as $column => $header): ?>
						<td>
							<?= nl2br($html->escape($data[$column])); ?>
						</td>
					<?php endforeach; ?>					

					<td class="action-icons order">

						<?php foreach($extra_operations as $name => $extra): ?>
							<?= $html->langLink($html->img($extra[1], ''), '/admin/'. $data['_url_'.$name], null, false);?>
						<?php endforeach; ?>

						<?php if(isset($operations['order']) && $operations['order'] != null): ?>
							<?= $html->img('/admin/components/images/list/up_down.png', $this->lang->list['operations']['order'], array('class' => 'order-handler', 'data-url' => 'admin/'.$data['_url_order'])); ?>
						<?php endif; ?>

						<?php if(isset($operations['explore']) && $operations['explore'] != null): ?>
							<?= $html->langLink($html->img('/admin/components/images/list/explore.png', $this->lang->list['operations']['explore'], array('title' => $this->lang->list['operations']['explore'])), '/admin/'. $data['_url_explore'], null, false);?>
						<?php endif; ?>

						<?php if(isset($operations['edit']) && $operations['edit'] != null): ?>
							<?= $html->langLink($html->img('/admin/components/images/list/edit.png', $this->lang->list['operations']['edit'], array('title' => $this->lang->list['operations']['edit'])), '/admin/'. $data['_url_edit'], null, false);?>
						<?php endif; ?>

						<?php if(isset($operations['delete']) && $operations['delete'] !== null && $operations['delete'] !== false): ?>
							
							<a href="<?= 'admin/'.$data['_url_delete']; ?>" class="confirm-delete" title="<?= $data[$title_row] ?>" >
								<?= $html->img('/admin/components/images/list/delete.png',
									$this->lang->list['operations']['delete'],
									array('title' => $this->lang->list['operations']['delete'])
								); ?>
							</a>
						
						<?php endif; ?>

					</td>
				</tr>
			<?php endforeach; ?>
			
		</tbody>

	</table>
	
</div>
	
<?= $pagination; ?>

<?php else:?>
	<div class="neutral-message">
		<?= $this->lang->table['empty']; ?>
	</div>
	

<?php endif; ?>


