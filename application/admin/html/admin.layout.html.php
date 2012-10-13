<?= $document_opening; ?>

	<head> 
		<?= $head_html; ?>
	</head>
	
	<body>
	
		<div id="sidebar">
			
			<div id="header">
				<h1>
					 <?= $html->link(AppConfig::TITLE, '/admin'); ?> 
				</h1>
				<?= $this->lang->admin; ?>

				<div id="basic-actions">
					<strong><?= $this->plugin->Auth->get('username'); ?></strong> <br/>
					<?= $html->link($this->lang->visit, '/', array('target' => '_blank')); ?> | 
					<?= $html->link($this->lang->settings, $settings); ?> |
					<?= $html->link($this->lang->logout, '/auth/logout_process'); ?>
				</div>
			</div>
			
			<?= $this->load->block('menu', array('entries' => $menu_entries)); ?>
			
		</div>

		<div id="main-column">
			<?= $requested_page; ?>
		</div>

		<div class="clear"></div>

		<div class="footer"></div>

	</body>
	
<?= $document_closing; ?>
