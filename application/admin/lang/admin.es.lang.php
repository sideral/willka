<?php

	class AdminLang extends Lang{

		public $admin = 'Administrador de Contenidos';
		public $content = "Contenido";
		public $home = 'Inicio';
		
		public $error = 'Ha ocurrido un error desconocido!';

		public $settings = 'Ajustes';
		public $logout = "Salir";
		public $visit = 'Ver Sitio';

		public $change_password = "Cambiar Contraseña";
		public $old_password = 'Contraseña Actual';
		public $new_password = 'Contraseña Nueva';
		public $retype_password = 'Repetir Contraseña';
		public $password_change_notice = "Cambia debajo el password del usuario";
		public $password_invalid = 'La contraseña no es correcta';
		public $password_too_short = 'La contraseña debe tener 6 caracteres como mínimo';
		public $password_no_match = 'Las contraseñas no coinciden';

		public $settings_msg = array('pass_change' => array('Su contraseña ha sido cambiada exitosamente.',
															'Ocurrió un error al cambiar la contraseña.'));

		public $select = '---- Seleccionar ----';

		public $list = array(
			'title' => 'Lista de items',
			'empty' => "No se han encontrado registros.",
			'operations' => array(
				'add'	=> 'Añadir',
				'edit'	=> 'Editar',
				'delete'=> 'Eliminar',
				'explore' => 'Visitar',
				'order'	=> 'Ordenar'
			),
			'messages' => array(
				 'add'		=> array("El item ha sido añadido", 'Hubo un error al añadir el item'),
				 'edit'		=> array("El item ha sido actualizado", 'Hubo un error al actualizar el item'),
				 'delete'	=> array("El item ha sido eliminado", 'Hubo un error al eliminar el item')
			),
			'confirm' => array("Estás seguro que deseas eliminar", "y toda la información relacionada?")
		);

		public $gallery = array(
			'title' => 'Fotos de la galería',
			'empty' => "La galería está vacía",
			'operations' => array(
				'add'	=> 'Añadir',
				'edit'	=> 'Editar',
				'delete'=> 'Eliminar',
				'explore' => 'Visitar'
			),
			'messages' => array(
				 'add'		=> array('success' => "La imagen ha sido añadida",
									 'failure' => 'Hubo un error al añadir la imagen'),
				 'edit'		=> array('success' => "La imagen ha sido actualizada",
									 'failure' => 'Hubo un error al actualizar la imagen'),
				 'delete'	=> array('success' => "La imagen ha sido eliminada",
									 'failure' => 'Hubo un error al eliminar la imagen')
			)
		);

		public $viewer = array(
			'boolean' => array('No', 'Sí')
		);

		public $table = array(
			'title' => 'Lista de items',
			'empty' => "No se han encontrado registros.",
			'operations' => array(
				'add'	=> 'Añadir',
				'edit'	=> 'Editar',
				'delete'=> 'Eliminar',
				'explore' => 'Visitar',
				'order'	=> 'Ordenar'
			),
			'messages' => array(
				 'add'		=> array("El item ha sido añadido", 'Hubo un error al añadir el item'),
				 'edit'		=> array("El item ha sido actualizado", 'Hubo un error al actualizar el item'),
				 'delete'	=> array("El item ha sido eliminado", 'Hubo un error al eliminar el item')
			),
			'confirm' => array("Confirma que desea eliminar ", "y toda la información relacionada?")
		);
		
		public $summary = array(
			'all' => 'Ver Todos'
		);
		
	}
