<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<div class="form">

	<?= $form->open(); ?>

		<div class="input-submit">
			<button type="submit" ><?= $html->img('save_'.Lang::getCurrent().'.png'); ?></button>

			<?php if($back): ?>
				<button type="submit" id="button_sub" ><?= $html->img('save_and_back_'.Lang::getCurrent().'.png'); ?></button>
				<script type="text/javascript">
					var form = "<?= $form->getId(); ?>";
					jQuery('#button_sub').mousedown(function(){
						var action = $('#'+form).attr('action');
						action += 'back=0';
						$('#'+form).attr('action', action);
					});
				</script>
			<?php endif; ?>

		</div>

		<h2><?= $html->escape($title); ?></h2>

		<fieldset style="display:none">
			<legend></legend>
			<?php foreach($form as $element):?>
				<?php if($element instanceof InputHidden): ?>
					<?= $element; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</fieldset>


		<fieldset <?= $group['attributes'] ?>>
			<legend class="<?= !$group['legend'] ? 'hidden' : ''?>"><?= $group['legend'] ?></legend>

			<dl>
				<?php foreach($form as $name => $element):?>

					<?php if($element instanceof InputHidden){continue;} ?>

					<?php if($element instanceof InputCheckable): ?>

						<dt class="<?= isset($group['classes'][$name]) ? $group['classes'][$name]:''; ?>"></dt>
						<dd class="<?= isset($group['classes'][$name]) ? $group['classes'][$name]:''; ?>">
							<?= $element; ?> <?= $element->getLabel(); ?>
							<?php if($element->getData('note')): ?>
								<div class="input_note"><?= $element->getData('note'); ?></div>
							<?php endif; ?>
							<?= $form_helper->componentErrorMessage($element); ?>
						</dd>

					<?php else: ?>

						<dt class="<?= isset($group['classes'][$name]) ? $group['classes'][$name]:''; ?>">
							<?= $element->getLabel(); ?>
						</dt>

						<dd class="<?= isset($group['classes'][$name]) ? $group['classes'][$name]:''; ?>">
							<?= $element; ?>
							<?php if($element->getData('note')): ?>
								<div class="input_note"><?= $element->getData('note'); ?></div>
							<?php endif; ?>
							<?= $form_helper->componentErrorMessage($element); ?>
						</dd>

					<?php endif; ?>

				<?php endforeach; ?>

			</dl>

		</fieldset>



	<?= $form->close(); ?>
</div>
