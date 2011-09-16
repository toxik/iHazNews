<?php
class Model_Meniu extends Model_Abstract {
	protected $menus;
	
	function init() {
		// populate the menus
		$this->menus = array(
			'about' => array(
				'/home/index/'				=> 'Home',
				'/statice/team'				=> 'Despre Echipa', 
				'/statice/project'			=> 'Despre Proiect', 
				'/statice/privacy'			=> 'Privacy Policy', 
				'/statice/contact'			=> 'Contact'
			),
			'guest' => array(
				'/home/index'				=> 'Guest',
				'/administrator'			=> 'Administrator',
				'/furnizor'					=> 'Furnizor',
				'/user'						=> 'Utilizator'
			),
			'P' => array(
				'/furnizor/index'			=> 'Home',
				'/furnizor/siteMng'			=> 'Site management',
				'/furnizor/statistics'		=> 'Statistici',
				'/furnizor/widgetCreation'	=> 'Generare widget'
			),
			'U' => array(
				'/user/index'				=> 'Home',
				'Settings'					=> array(
					'/user/account'			=> 'Account',
					'/user/newsletter'		=> 'Newsletter',
					'/user/newsgroups'		=> 'Newsgroups',
					'/user/interese'		=> 'Interese'
				),
				'/user/browse'				=> 'Browse Sites',
//				'/user/search'				=> 'Search'
			),
			'A' => array(
				'/administrator/index'		=> 'Home',
				'/administrator/users'		=> 'Admin Users',
				'/administrator/websites'	=> 'Admin Websites',
				'/administrator/newsgroups'	=> 'Admin Newsgroups',
				'/administrator/interese'	=> 'Admin Interese'
			)
		);
	}
	
	function printMenu( $whichType = 'auto' ) {
		if ($whichType == 'about')
			echo $this->traverseMenu($this->menus['about']);
		if ($whichType == 'auto')
			if (!$_SESSION['auth']['USER_TYPE'])
				echo $this->traverseMenu($this->menus['about']);
			else 
				echo $this->traverseMenu($this->menus[$_SESSION['auth']['USER_TYPE']]);
	}
	
	private function traverseMenu($menu, $title = '') {
		$currentMenu = ($title ? '<a>'.$title.'</a>' :  '').'<ul>';
		if ($menu)
		foreach($menu as $url => $title)
			$currentMenu .= '<li'.$this->decorate($menu, $title, $url).'>'.( 
								is_array($title) ? 	$this->traverseMenu($title, $url) : 
									'<a href="'.$url.'" title="'.$title.'">'.$title.'</a>' )
							. '</li>';
		return $currentMenu . '</ul>';
	}
	
	private function decorate( $menu, $element, $url ) {
		$dec = ' class="';
		if (end($menu) == $element)
			$dec .= 'last ';
		
		$currentPath = '/' . MODULE . '/' . ACTION;
		
		if (substr( $url, 0, strlen($currentPath)) == $currentPath)
			$dec .= 'selected';
		
		return strlen($dec) == 8 ? '' : $dec.'"';
	}
}