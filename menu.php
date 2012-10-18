<div id="main-menu" class="linear-menu">
	<?php 
		wp_nav_menu(
			array(
				'theme_location'	=>	'primary',
				'container'			=>	'',
				'depth'				=>	1,
				'walker'			=>	new RemoveSpacesWalkerNavMenu()
			)
		);
	?>	
</div>