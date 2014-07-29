<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
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
 * themEE Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Somebody Somewhere
 * @link		
 */

class Themee_upd {
	
	public $version = '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Themee',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);
		
		$this->EE->db->insert('modules', $mod_data);
		
		 $this->EE->load->dbforge();
		/**
		 * In order to setup your custom tables, uncomment the line above, and 
		 * start adding them below!
		 */
		
		
		$fields = array(
	    'logo_id'   => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
	    'logo_dir_id'    => array('type' => 'int', 'constraint'  => '4'),
	    'logo_file_name' => array('type' => 'varchar', 'constraint' => '250'),
	    'logo_file_title'    => array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL),
	    'bckg_dir_id'    => array('type' => 'int', 'constraint'  => '4'),
	    'bckg_file_name' => array('type' => 'varchar', 'constraint' => '250'),
	    'bckg_file_title'    => array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL),
	    'member_access' => array('type' => 'varchar', 'constraint' => '250', 'default' => 'all'),
	    'background_css' => array('type' => 'varchar', 'constraint' => '250', 'default' => NULL),
			'button_css' => array('type' => 'varchar', 'constraint' => '250', 'default' => NULL)
    );

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('logo_id', TRUE);
		
		ee()->dbforge->create_table('themee');
		
		unset($fields);
		
		ee()->load->library('layout');
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Themee'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Themee')
					 ->delete('modules');
		
		// Delete your custom tables & any ACT rows 
		// you have in the actions table
		
		ee()->load->dbforge();

    ee()->db->select('module_id');
    $query = ee()->db->get_where('modules', array('module_name' => 'Themee'));
    
    ee()->dbforge->drop_table('themee');
        
    ee()->load->library('layout');
		
		
		//get the default css and reload it so the user has something

		$data = file_get_contents(PATH_THIRD."/themee/css/login_default.css", FILE_USE_INCLUDE_PATH);
	  $file = "themes/cp_themes/default/css/login.css";
		 
		 /* create a stream context telling PHP to overwrite the file */ 
		 $options = array('ftp' => array('overwrite' => true)); 
		 $stream = stream_context_create($options); 
		  
		 /* and finally, put the contents */ 
		 file_put_contents($file, $data, 0, $stream); 

		
		
		
		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		// If you have updates, drop 'em in here.
		
		

		
		
		
		
		
		return TRUE;
	}
	
}
/* End of file upd.themee.php */
/* Location: /system/expressionengine/third_party/themee/upd.themee.php */