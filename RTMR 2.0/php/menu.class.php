
<?php
/* These menu classes are used to display the relevant menu buttons depending on the users permissions
*/
require_once("menucrud.class.php");
class Menu {
	private $menulist=[];
	
	public function __construct($menulevel) {
		$this->setMenuItems($menulevel);
	}
	
	private function setMenuItems($menulevel) {
		$source=new MenuCRUD();
		$this->menulist=$source->getMenu($menulevel);
	}
	
	public function __toString() {
		$menustr="";
		foreach($this->menulist as $menuitem) {
			$menustr.="<li><button class='menubutton'><a class='menubuttontext' href='".$menuitem['url']."'>".$menuitem['pagename']."</a></button></li>";
		}
		return $menustr;
	}
}
?>