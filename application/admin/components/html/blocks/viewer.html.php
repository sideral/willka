<?php foreach($custom_scripts as $script):?>
	<?= $html->javascript($script); ?>
<?php endforeach;?>
<?php foreach($custom_styles as $style):?>
	<?= $html->css($style); ?>
<?php endforeach;?>

<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<?php if(isset($operations['edit']) && $operations['edit'] != null): ?>

	<?= $html->langLink($html->img('list/edit.png', $this->lang->list['operations']['edit']) . ' ' . $this->lang->list['operations']['edit'], $operations['edit']['url'],
			array('class' => 'action-button', 'style' => 'float:right;'), false) ?>

<?php endif; ?>

<h2 class="viewer-title">
	<?= $html->escape($title);?>
	
	<?php foreach($filter as $input): ?>
		<?= '&#8226;'.$input; ?>
	<?php endforeach; ?>
	
</h2>

<div class="viewer">
	
	<?php foreach($rows as $row): ?>
		<table class="main">
			<tr>
				<?php foreach($row as $i => $section):?>
					<td class="section <?= $i==0?'first':''; ?>" <?= HtmlHelper::formatAttributes($section['attributes']); ?>>

						<?php if(!empty($section['title'])): ?>
							<h3><?= $section['title']; ?></h3>
						<?php endif; ?>

						<?= $this->load->block('component', array('template' => 'components/viewer_table',	'values' => array('dataset' => $section['dataset']))); ?>

					</td>		
				<?php endforeach; ?>
			</tr>
		</table>

	<?php endforeach; ?>

</div>
