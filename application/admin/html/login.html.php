<div id="login-form">
	
	<?= $form_helper->errorMessage($login->getErrorSummary(), $login->getName()); ?>

	<?= $login->open(); ?>
		
		<?= $login->module; ?>
		<?= $login->next; ?>

		<fieldset>
			
			<h2><?= $this->lang->get('/auth/auth')->login_title; ?></h2>
			
			<dl>
				<dt>
					<?= $form_helper->label($this->lang->get('/auth/auth')->username, $login->username);?>
				</dt>
				<dd>
					<?= $login->username; ?>
				</dd>
				<dt>
					<?= $form_helper->label($this->lang->get('/auth/auth')->password , $login->password);?>
				</dt>
				<dd>
					<?= $login->password; ?>
				</dd>
				<!--<dt>

				</dt>
				<dd>
					<?= $login->remember; ?>
					<?= $form_helper->label($this->lang->get('/auth/auth')->remember_me, $login->remember);?>
				</dd>-->

				<dt>&nbsp;</dt>
				<dd id="enviar_auth">
					<?= $login->submit_image->setSource('/admin/images/login.png'); ?>
				</dd>
			</dl>
		</fieldset>

	<?= $login->close(); ?>
</div>
