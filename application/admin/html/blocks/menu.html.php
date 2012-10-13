
<ul id="menu">
	
	<?php foreach($entries as $entry): ?>
		<li>
			
			<?= $html->link($entry['entry'][0],'/admin/'.$entry['entry'][1], array('class' => 'main-menu-entry '.($entry['entry'][2] || count($entries) == 1?'current-main-entry':''))); ?>
			
			<?php if($entry['subentries']): ?>
			
				<ul style="<?= $entry['entry'][2]|| count($entries) == 1?'display:block':''; ?>">
					<?php foreach($entry['subentries'] as $subentry): ?>
						<li>
							<?= $html->link($subentry[0],'/admin/'.$subentry[1], array('class' => 'secondary-menu-entry '.($subentry[2]?'selected-entry':''))); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			
			<?php endif; ?>
			
		</li>

	<?php endforeach; ?>
	
</ul>
