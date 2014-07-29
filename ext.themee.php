<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*Expression Engine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ThemEE Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Michael McGhee
 * @link		http://michael.mcghee.me
 */

class Themee_ext {
	
	public $settings 		= array();
	public $description		= 'Replaces login theme and adds a little flavor to the CMS';
	public $docs_url		= 'http://papercutinteractive.com';
	public $name			= 'ThemEE';
	public $settings_exist	= 'n';
	public $version			= '0.8';
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();
		
		$hooks = array(
			'cp_css_end'	=> 'add_css',
			'cp_js_end'	=> 'add_js'
		);

		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize($this->settings),
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);

			$this->EE->db->insert('extensions', $data);			
		}
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * add_css
	 *
	 * @param 
	 * @return 
	 */
	public function add_css($data)
	{
		// Add Code for the cp_css_end hook here.  
		$results = ee()->db->get('themee');

    if ($results->num_rows() > 0){
	  	$bg_value = (isset($_POST['background'])) ? $_POST['background'] : $results->row('background_css');
			$btn_value = (isset($_POST['button'])) ? $_POST['button'] : $results->row('button_css');
    }
		
		
		//CMS css modifications
		$data .="#activeUser,.submit,table.mainTable th.headerSortUp, table.mainTable th.headerSortDown,#navigationTabs li li.hover,#navigationTabs li li.hover, #navigationTabs ul li li.hover,#navigationTabs li li.parent:focus,
#navigationTabs li li.parent.active,#navigationTabs li li.parent:focus > a,
#navigationTabs li li.parent.active > a,#navigationTabs li li.parent:focus > a:after,
#navigationTabs li li.parent.active > a:after {background: #".$bg_value." !important }
						#breadCrumb li.last,#navigationTabs li #addQuickTab, #navigationTabs li #addQuickTab:link,#navigationTabs li a.first_level:hover,#activeUser a:link, #activeUser a:visited,.pageContents span.button .submit, 
span.button .submit{color: #".$btn_value." !important }
#navigationTabs li li.parent:focus > a:after, #navigationTabs li li.parent.active > a:after{border: none !important}
#navigationTabs li a.first_level, #navigationTabs li a.first_level:link, #navigationTabs li a.first_level:visited{background-image: none;}
#navigationTabs li.active a.first_level, #navigationTabs li:active a.first_level, #navigationTabs li a.first_level:hover{background-image: none; background-color: #".$bg_value." !important}
#navigationTabs li a.first_level:hover{background-image: none;}
";
		
		

		
		
		return $data;
	}

	// ----------------------------------------------------------------------
	
	/**
	 * add_js
	 *
	 * @param 
	 * @return 
	 */
	public function add_js($data)
	{

	$data.= "";
	return $data;
		// Add Code for the cp_js_end hook here.  
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.themee.php */
/* Location: /system/expressionengine/third_party/themee/ext.themee.php */
