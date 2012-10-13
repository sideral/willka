<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<script type="text/javascript">
	function listDeleteConfirmation(name){
		return "<?= $this->lang->list['confirm'][0]; ?> '" + name +  "' <?= $this->lang->list['confirm'][1]; ?>";
	}
</script>

<?php if(isset($operations['add']) && $operations['add'] != null): ?>

	<?= $html->langLink($html->img('/admin/components/images/gallery/add.png', $this->lang->list['operations']['add']) . ' ' . $this->lang->gallery['operations']['add'], '/admin/'. $operations['add']['url'],
			array('class' => 'action-button', 'style' => 'float:right;margin:5px 0px;'), false); ?>

<?php endif; ?>


<h2><?= $html->escape($title);?></h2>

<?php if($datasource): ?>

<div class="gallery" >

	<ul class="<?= isset($operations['order']) ? 'ordered':'' ?>">

		<?php $i=0; ?>

		<?php foreach($datasource as $data): ?>

			<li style="<?= $i%5 == 0 && !isset($operations['order'])  ? "clear:both":""; ?>" class="ui-state-default" data-id="<?= $data[0]; ?>">

				<div class="picture">

					<?php $img = $html->absoluteImg(UrlHelper::resource('admin/'.$image_base. '/'. $data[1]), $data[1], array('width' => 160, 'height' => 160)); ?>

					<?php if(isset($operations['view']) && $operations['view'] != null): ?>
						
					<?php elseif(isset($operations['edit'])): ?>

						<?= $html->langLink($img, '/admin/'. $data['_url_edit'], null, false);?>
						<?php if(isset($data[2])): ?>
							<div class="detail"><?= $data[2];?></div>
						<?php endif; ?>
					<?php else: ?>
						<?= $img; ?>
					<?php endif; ?>

				</div>

				<div class="action-buttons">

					<?php if(isset($operations['order']) && $operations['order'] != null): ?>
						<?= $html->img('/admin/components/images/list/up_down.png', $this->lang->list['operations']['order'], array('class' => 'order-handler', 'data-url' => 'admin/'.$data['_url_order'])); ?>
					<?php endif; ?>

					<?php if(isset($operations['edit']) && $operations['edit'] != null): ?>
						<?= $html->langLink($html->img('/admin/components/images/list/edit.png', $this->lang->list['operations']['edit'], array('title' => $this->lang->list['operations']['edit'])),'/admin/'.  $data['_url_edit'], null, false);?>
					<?php endif; ?>

					<?php if(isset($operations['delete']) && $operations['delete'] !== null): ?>
						
						<a href="<?= 'admin/'.$data['_url_delete']; ?>" class="confirm-delete" title="<?= $data[1] ?>" >
							<?= $html->img('/admin/components/images/list/delete.png',
								$this->lang->list['operations']['delete'],
								array('title' => $this->lang->list['operations']['delete'])
							); ?>
						</a>
					<?php endif; ?>

				</div>
			</li>

			<?php $i++; ?>

		<?php endforeach; ?>
		<li class="clear" style="height:1px;width:1px;"></li>
	</ul>
</div>

	
<?php else: ?>
	<div class="empty-list">
		<?= $this->lang->gallery['empty']; ?>
	</div>
<?php endif; ?>
<div class="clear"></div>
