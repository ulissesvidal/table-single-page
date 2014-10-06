<?php
class ConfigArray {
	private $cfgArray;
	
	public function addConfig($type, $id, $name, $val, $label) {
		$this->cfgArray[$id]=new Config($type, $id, $name, $val, $label);
	}
	public function __construct($url){
		echo "<link rel=\"stylesheet\" href=\"".$url."components/com_grid/config/config.css\" type=\"text/css\">";
	}
	public function printLink($col, $value) {
		echo "<a onClick=\"moreSettings('".$col."')\">More ...</a>";
		echo "<input type=\"hidden\" id=\"moreConfig_".$col."\" name=moreConfig[".$col."] value=\"".$value."\" />";
	}
	public function includeForm() {
		echo $this;
	}
	public function __toString() {
		$r.="<div id=\"moreSettings\">";
		$r.="<a onclick=\"closeMoreSettings();\" id=\"close\">X</a>";
		$r.="<fieldset class=\"adminform\">";
		$r.="<legend><span id=\"title\"></span></legend>";
		$r.="<table id=\"configArray\">";
		foreach ($this->cfgArray as $config) {
			$r.="<tr>";
			$r.="<td class=\"key\">".$config->getLabel().":</td>";
			$r.="<td class=\"secondColumn\">".$config."</td>";
			$r.="</tr>";
		}
		$r.="</table>";
		$r.="</fieldset>";
		$r.="<br/><div id=\"buttons\"></div>";
		$r.="</div>";
		return $r;
	}
}


class Config {
	//TYPES OF CONFIG
	public static $text = "text";			//Input type=text
	public static $bigText = "bigText";		//textarea
	public static $radioYesNo = "radioYesNo";
	public static $select = "select";
	public static $color = "color";

	private $inputType;
	private $value;
	private $id;
	private $name;
	private $label;

	function getLabel() {
		return $this->label;
	}
	function getID() {
		return $this->id;
	}

	function __construct($type, $id, $name, $val, $label) {
		$this->inputType	= $type;
		$this->id			= $id;
		$this->name			= $name;
		$this->value		= $val;
		$this->label		= $label;
	}

	public function __toString() {
		switch($this->inputType) {
			case 'text': {
				return "<input type=\"text\" id=\"".$this->id."\" name=\"".$this->name."\" value=\"".$this->value."\" />";
			} break;
			case 'bigtext': {
				return "<textarea id=\"".$this->id."\" name=\"".$this->name."\">".$this->value."</textarea>";
			} break;
			case 'radioYesNo': {
				return "<input type=\"radio\" value=\"1\" name=\"".$this->name."\" /> Yes ".
						"<input type=\"radio\" value=\"0\" name=\"".$this->name."\" /> No";
			} break;
			case 'select': {
				$r = "<select id=\"".$this->id."\" name=\"".$this->name."\">";
				foreach (explode("|", $this->value) as $option)
				$r.= "<option>".$option."</option>";
				$r.= "</select/>";
				return $r;
			} break;
			case 'color': {
				return "<input class=\"color\" type=\"text\" name=\"".$this->name."\" id=\"".$this->id."\"
							size=\"6\" maxlength=\"6\" />";
					
			} break;
		}
	}
}
?>