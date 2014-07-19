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
 * themEE Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Somebody Somewhere
 * @link		
 */

class Themee_mcp {
	
	public $return_data;
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=themee';
		
		$this->EE->cp->set_right_nav(array(
			'module_home'	=> $this->_base_url,
			// Add more right nav items here.
		));
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
	
		$this->EE->cp->add_to_head('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />');


		//$this->EE->cp->set_variable('cp_page_title',  lang('themee_module_name'));
		$this->EE->view->cp_page_title = lang('themee_module_name');
		ee()->load->library('javascript');
	    ee()->load->helper('form');
	    ee()->load->library('table');
	    ee()->load->library('file_field');
	
	    ee()->view->cp_page_title = lang('ThemEE');
	
	    $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=themee'.AMP.'method=index';
	    $vars['form_hidden'] = NULL;
	    $vars['files'] = array();
			$logo = "logo";
	    $background = "background";
	    $button = "button";
	    $value = "";
	    $bg_value = "27343C";
	    $btn_value = "fc2e5a";
	    $file_value = "";
	    //additional informatioin for redirection after submission (slightly repeated)
	    $action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=themee';
		$attributes = array('class' => 'themee-form', 'id' => 'index');
		
		//poll the db for themee table
		$results = ee()->db->get('themee');
		
	    $bg_value = ($results->row('background_css')) ? $results->row('background_css') : "";
	    $btn_value = ($results->row('button_css')) ? $results->row('button_css') : "";
	    $file_value = ($results->row('file_name')) ? $results->row('file_name') : "";
	    
	    if ($results->num_rows() > 0){
	    
	    //find the actual directory name of the uploaded logo 
		  $query = mysql_query("SELECT exp_upload_prefs.url FROM exp_themee LEFT JOIN exp_upload_prefs ON exp_themee.dir_id=exp_upload_prefs.id");
			$result = mysql_fetch_assoc($query);
			
			//current editing
			
			if(mysql_num_rows($query) > 0){
				$filedir = $result['url'];
				
				
				$pattern = '/{filedir_[0-9]}/i'; 
				$replace = "";
				$real_file_name =  preg_replace($pattern, $replace, $file_value);
				
				//put it together for the css
				$filepath = $filedir."a".$real_file_name;
			}else{
				$filepath = "";
			}
    }
    
		//check for a set value, if set use the post, else use the value in the database
	  $bg_value = (isset($_POST['background'])) ? $_POST['background'] : $results->row('background_css');
	  $btn_value = (isset($_POST['button'])) ? $_POST['button'] : $results->row('button_css');
	  
	  
	  if($results->row('file_name') != ""){
		   $file_value = (isset($_POST['logo_hidden_dir'])) ? "{filedir_".$_POST['logo_hidden_dir']."}".$_POST['logo_hidden_file'] : $results->row('file_name');
	  }
	  


		

    
    $vars['options'] = array('edit' => lang('edit_selected'), 'delete' => lang('delete_selected'));
    ee()->file_field->browser($endpoint_url="");


    //load color picker
    ee()->cp->load_package_css('colpick');
    ee()->cp->load_package_js('colpick');
    
    
    //load table items
    ee()->table->set_heading('Setting', 'Value');    
		ee()->table->add_row("Logo Image",ee()->file_field->field($logo, $data=$file_value, $allowed_file_dirs = 'all', $content_type = 'all'));
		ee()->table->add_row("Background Color", form_input($background, $bg_value));
		ee()->table->add_row("Button Color", form_input($button, $btn_value));
		
		//generate form tags
		$form = form_open($action_url, $attributes, $logo);
		
		//themee submission		
		if(isset($_POST['logo_hidden_file']) && isset($_POST['logo_hidden_dir'])){
			
			//post values
			$background = ee()->input->post('background');
			$button = ee()->input->post('button');
			$logoDirectory = ee()->input->post('logo_directory');
			
			if($_POST['logo_hidden_dir'] != 0 && $_POST['logo_hidden_file'] != ""){
				$logoName ="{filedir_".$logoDirectory."}". ee()->input->post('logo_hidden_file');
			}else{
				$logoName ="";
			}
			
			//var_dump($_POST);
			
			$results = ee()->db->select('file_name')->get('themee');
			
			if ($results->num_rows() > 0)
			{
				
				//update the database table 
				ee()->db->update(
					    'themee',
					    array(
					        'file_name'  => $logoName,
					        'dir_id' => $logoDirectory,
					        'button_css'   => $button,
					        'background_css'=> $background
					    ));
			
		//open the login css file for writing	
	  $file = "../themes/cp_themes/default/css/login.css";
	  $current = file_get_contents($file);
		
		$results = ee()->db->get('themee');

    if ($results->num_rows() > 0){
	    
	    //find the actual directory name of the uploaded logo 
		  $query = mysql_query("SELECT exp_upload_prefs.url FROM exp_themee LEFT JOIN exp_upload_prefs ON exp_themee.dir_id=exp_upload_prefs.id");
			$result = mysql_fetch_assoc($query);
			
			//current editing
			
			if(!empty($filedir)){
				$filedir = $result['url'];
				
				$pattern = '/{filedir_[0-9]}/i'; 
				$replace = "";
				$real_file_name =  preg_replace($pattern, $replace, $file_value);
				
				//put it together for the css
				$filepath = $filedir.$real_file_name;
			}else{
				$filepath = "";
			}
    }
		
		//modifications that are appended to the login.css file
		$data= "		
			body {
				background-color:	#".$bg_value." !important;
			}
			
			#content  {
				background:	url('".$filepath."') no-repeat center top !important;
				-webkit-background-size: 75%;
		    -moz-background-size: 75%;
		    -o-background-size: 75%;
		    background-size: 75%;
			}
			
			form {
				padding-top: 50px;
			}
			
			#branding {
				text-align:			center !important;
				padding-right:		0px !important;
			}
			
			a:link, a:visited {
				color:				#".$btn_value." !important;
			}
			
			a:hover {
				color:				#".$btn_value." !important;
			}
			
			input.submit {
				background:			#".$btn_value." !important;
			}";
				
			
			$current .= $data;
			file_put_contents($file, $current);
								
			}
else{
			//insert the default values if not posting
				
						ee()->db->insert(
					    'themee',
					    array(
					        'file_name'  => $logoName,
					        'dir_id' => $logoDirectory,
					        'button_css'   => $button,
					        'background_css'=> $background
					    ));
					    
					    
					    
	$file = "../themes/cp_themes/default/css/login.css";
	$current = file_get_contents($file);
		
		$results = ee()->db->get('themee');

    if ($results->num_rows() > 0){

	    $bg_value = ($results->row('background_css')) ? $results->row('background_css') : "";
	    $btn_value = ($results->row('button_css')) ? $results->row('button_css') : "";
	    $file_value = ($results->row('file_name')) ? $results->row('file_name') : "";
   }
	 	
	 	
		$data.= "		
			body {
				background-color:	#".$bg_value." !important;
			}
			
			#content  {
				background:	url('".$file_value."') no-repeat center top !important;
				-webkit-background-size: 75%;
		    -moz-background-size: 75%;
		    -o-background-size: 75%;
		    background-size: 75%;
			}
			
			form {
				padding-top: 50px;
			}
			
			#branding {
				text-align:			center !important;
				padding-right:		0px !important;
			}
			
			a:link, a:visited {
				color:				#".$btn_value." !important;
			}
			
			a:hover {
				color:				#".$btn_value." !important;
			}
			
			input.submit {
				background:			#".$btn_value." !important;
			}";
				
			$current .= $data;
			file_put_contents($file, $current);    
			}
		}



		

		
		//var_dump($background, $button, $logoName, $logoDirectory);
	
		return $form.ee()->table->generate()."<input type='submit' value='submit'/> <script>$(document).ready(function(){

	
		$('.mainTable input[type=text]').colpick({
		layout:'hex',
		submit:0,
		colorScheme:'dark',
		onChange:function(hsb,hex,rgb,el,bySetColor) {
			$(el).css('border-color','#'+hex);
			// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
			if(!bySetColor) $(el).val(hex);
		}
		}).keyup(function(){
			$(this).colpickSetColor(this.value);
		});
		
			$('.mainTable input[type=text]').each(function(){
		    if($(this).val())
		        $(this).css('border-color', '#'+$(this).val())
		});

		});
		</script>";  
    }

}
/* End of file mcp.themee.php */
/* Location: /system/expressionengine/third_party/themee/mcp.themee.php */