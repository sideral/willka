<?php foreach($custom_scripts as $script):?>
	<?= $html->javascript($script); ?>
<?php endforeach;?>
<?php foreach($custom_styles as $style):?>
	<?= $html->css($style); ?>
<?php endforeach;?>

<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<script type="text/javascript">
	function listDeleteConfirmation(name){
		return "<?= $this->lang->list['confirm'][0]; ?> '" + name +  "' <?= $this->lang->list['confirm'][1]; ?>";
	}
</script>

<?php if(isset($operations['add']) && $operations['add'] != null): ?>
	<?= $html->langLink($html->img('/admin/components/images/list/add.png', '/admin/'. $this->lang->list['operations']['add']) . ' ' . $this->lang->list['operations']['add'], '/admin/'. $operations['add']['url'],
			array('class' => 'action-button', 'style' => 'float:right;margin:5px 0px;'), false) ?>
<?php endif; ?>

<h2 class="list-title">

	<?= $html->escape($title);?>

	<?php foreach($filter as $input): ?>
		<?= '&#8226;'.$input; ?>
	<?php endforeach; ?>
	
</h2>

<div class="list" data-component="<?= $_component; ?>">

	<table class="<?= isset($operations['order']) ? 'ordered':'' ?>">

		<tbody>
			<?php foreach($datasource as $data): ?>
				<tr data-id="<?= $data[$column_indexes['id']]; ?>" >
					<td class="sheet">
						<?php if(isset($data[$column_indexes['image']]) && $data[$column_indexes['image']] && $icon_base): ?>
							<?= $html->img('/'.DEFAULT_MODULE.'/admin/'.$icon_base.'/small/'.$data[$column_indexes['image']]); ?>
						<?php else: ?>
							<?= $html->img('/admin/components/images/sheet.png', ''); ?>
						<?php endif; ?>
					</td>
					<td class="title">

						<?php if($row_operation == 'edit' && isset($operations['edit']) && $operations['edit'] != null): ?>
							<?= $html->langLink($html->escape($data[$column_indexes['title']]) . '<span>'. (isset($data[$column_indexes['subtitle']]) ? $html->escape($data[$column_indexes['subtitle']]) : '').'</span>',
									'/admin/'. $data['_url_edit'], array('class' => 'text'), false);?>
						<?php elseif($row_operation == 'explore' && isset($operations['explore']) && $operations['explore'] != null && !is_callable($operations['explore'])): ?>
							<?= $html->langLink($html->escape($data[$column_indexes['title']]) . '<span>'. (isset($data[$column_indexes['subtitle']]) ? $html->escape($data[$column_indexes['subtitle']]) : '').'</span>',
									'/admin/'. $data['_url_explore'], array('class' => 'text'), false);?>
						<?php else: ?>
							<div class="text">
								<?= $html->escape($data[$column_indexes['title']]); ?>
								<span><?= isset($data[$column_indexes['subtitle']]) ? $html->escape($data[$column_indexes['subtitle']]) : ''; ?></span>
							</div>
						<?php endif; ?>

					</td>
					
					<td class="action-icons order">

						<?php foreach($extra_operations as $name => $extra): ?>
							<?= $html->langLink($html->img($extra[1], ''), $data['_url_'.$name], array('target' => '_blank'), false);?>
						<?php endforeach; ?>

						<?php if(isset($operations['order']) && $operations['order'] != null): ?>
							<?= $html->img('/admin/components/images/list/up_down.png', $this->lang->list['operations']['order'], array('class' => 'order-handler', 'data-url' => 'admin/'.$data['_url_order'])); ?>
						<?php endif; ?>

						<?php if(isset($operations['explore']) && $operations['explore'] != null): ?>
							<?= $html->langLink($html->img('/admin/components/images/list/explore.png', $this->lang->list['operations']['explore'], array('title' => $this->lang->list['operations']['explore'])), '/admin/'.$data['_url_explore'], null, false);?>
						<?php endif; ?>

						<?php if(isset($operations['edit']) && $operations['edit'] != null): ?>
							<?= $html->langLink($html->img('/admin/components/images/list/edit.png', $this->lang->list['operations']['edit'], array('title' => $this->lang->list['operations']['edit'])), '/admin/'.$data['_url_edit'], null, false);?>
						<?php endif; ?>

						<?php if(isset($operations['delete']) && $operations['delete'] !== null && $operations['delete'] !== false): ?>
							<a href="<?= 'admin/'.$data['_url_delete']; ?>" class="confirm-delete" title="<?= $data[$column_indexes['title']] ?>" >
								<?= $html->img('/admin/components/images/list/delete.png',
									$this->lang->list['operations']['delete'],
									array('title' => $this->lang->list['operations']['delete'])
								); ?>
							</a>
						<?php endif; ?>

					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(!$datasource): ?>
				<tr class="empty-list">
					<td colspan ="2">
						<?= $this->lang->list['empty']; ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>

	</table>

	<?= $pagination; ?>

</div>


