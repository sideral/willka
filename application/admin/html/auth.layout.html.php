<?= $document_opening; ?>

	<head>
		<?= $head_html; ?>
	</head>

	<body id="body-login">

		<div id="login-column">
			<div id="header">
				<h1>
					 <?= $html->link(AppConfig::TITLE, '/admin'); ?> 
				</h1>
				<?= $this->lang->admin; ?>
			</div>
			<?= $requested_page; ?>
		</div>

		<div class="clear"></div>

		<div class="footer"></div>


	</body>

<?= $document_closing; ?>
