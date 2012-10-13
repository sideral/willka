<?= '<?php '; ?>


class <?= ucfirst($module); ?>ConfigUtility extends AdminConfigUtility{
	
	function configureSchema($schema){
		
<?php foreach($tables as $table_name => $table): ?>
	
		$schema->addTable('<?= $table_name; ?>',
			 array(
	<?php foreach($table as $i=> $column): ?>
			'<?= $column['Field']; ?>' => array('<?= $column['Type']; ?>', '<?= $column['Field']; ?>')<?= $i == count($table)-1 ? '':',' ?> 
	<?php endforeach; ?>
				
			)
		);
		
<?php endforeach; ?>

	}	
	
}
