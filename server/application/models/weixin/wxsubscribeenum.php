<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxSubscribeEnum extends CI_Model{
	public $enums = array(
		array(1,'GRAPHIC','多图文'),
        array(2,'SINGLEGRAPHIC', '单图文'),
 		array(3,'THETEXT', '文字'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
