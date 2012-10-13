<?= $this->load->block('message', array('messages' => $messages, 'component' => $_component)); ?>

<div class="menu-box">
	<h2><?= $title; ?></h2>
	<ul>
		<?php foreach($entries as $entry): ?>
			<li>
				<?= $html->langLink($entry[0], '/admin/'.$entry[1]); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

