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
			$bckg = "backg";
	    $background = "background";
	    $button = "button";
	    $value = "";
	    $bg_value = "27343C";
	    $btn_value = "fc2e5a";
	    $file_value = "";
	    $bck_img = "";
	    //additional information for redirection after submission (slightly repeated)
	    $action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=themee';
			$attributes = array('class' => 'themee-form', 'id' => 'index');
			$themesDir = PATH_THEMES;
			

		//poll the db for themee table
		$results = ee()->db->get('themee');

	    if ($results->num_rows() > 0){
		    $bg_value = ($results->row('background_css')) ? $results->row('background_css') : $bg_value;
		    $btn_value = ($results->row('button_css')) ? $results->row('button_css') : $btn_value;
		    $file_value = ($results->row('logo_file_name')) ? $results->row('logo_file_name') : $file_value;
		    $bck_img = ($results->row('bckg_file_name')) ? $results->row('bckg_file_name') : $bck_img;
		    
		    //find the actual directory name of the uploaded logo 
			  $query = mysql_query("SELECT exp_upload_prefs.url FROM exp_themee LEFT JOIN exp_upload_prefs ON exp_themee.logo_dir_id=exp_upload_prefs.id");
				$result = mysql_fetch_assoc($query);
			
			//current editing
			
			if(mysql_num_rows($query) > 0){
				//if there are files in the database, check them and get the file urls like so
				$filedir = $result['url'];
				$pattern = '/{filedir_[0-9]}/i'; 
				$replace = "";
				$real_logo_file_name =  preg_replace($pattern, $replace, $file_value);
				$real_bckg_file_name =  preg_replace($pattern, $replace, $bck_img);
				
				//put it together for the css
				$filepath = $filedir.$real_logo_file_name;
				$filepathbg = $filedir.$real_bckg_file_name;
			}else{
				//nothing is in the database yet, so put blanks in 
				$filepath = "";
				$filepathbg = "";
			}
    }else{
		   
		    
	    }
    
		//check for a set value, if set use the post, else use the value in the database
		$bg_value = (isset($_POST['background'])) ? $_POST['background'] :$bg_value;
	  $btn_value = (isset($_POST['button'])) ? $_POST['button'] : $btn_value;
	  
	  
	  if($results->row('logo_file_name') != "" || $results->row('logo_file_name') != "{filedir_}"){
		   $file_value = (!empty($_POST['logo_hidden_dir'])) ? "{filedir_".$_POST['logo_hidden_dir']."}".$_POST['logo_hidden_file'] : $results->row('logo_file_name');
	  }
	  
	  if($results->row('bckg_file_name') != "" || $results->row('bckg_file_name') != "{filedir_}"){
		   $bck_img = (!empty($_POST['backg_hidden_dir'])) ? "{filedir_".$_POST['backg_hidden_dir']."}".$_POST['backg_hidden_file'] : $results->row('bckg_file_name');
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
		ee()->table->add_row("Tiled Background Image",ee()->file_field->field($bckg, $data=$bck_img, $allowed_file_dirs = 'all', $content_type = 'all'));
		ee()->table->add_row("Button/Label Color", form_input($button, $btn_value));
		
		//generate form tags
		$form = form_open($action_url, $attributes, $logo, $bckg);
		
		//themee submission - wat?
		if(isset($_POST['logo_hidden_file']) && isset($_POST['logo_hidden_dir']) || isset($_POST['backg_hidden_file']) && isset($_POST['backg_directory'])){
			
			//post values
			$background = ee()->input->post('background');
			$button = ee()->input->post('button');
			$logoDirectory = ee()->input->post('logo_directory');
			$bckgDirectory = ee()->input->post('backg_directory');
			
			
			if($_POST['logo_hidden_dir'] != 0 && $_POST['logo_hidden_file'] != ""){
				$logoName ="{filedir_".$logoDirectory."}". ee()->input->post('logo_hidden_file');
			}else{
				$logoName ="";
			}
			
			if($_POST['backg_hidden_dir'] != 0 && $_POST['backg_hidden_dir'] != ""){
				$bckgName ="{filedir_".$bckgDirectory."}". ee()->input->post('backg_hidden_file');
			}else{
				$bckgName ="";
			}

			$results = ee()->db->select('logo_file_name')->get('themee');
			$results2 = ee()->db->select('bckg_file_name')->get('themee');
			
			if ($results->num_rows() > 0 || $results2->num_rows() > 0){
				//update the database table 
				ee()->db->update(
					    'themee',
					    array(
					        'logo_file_name'  => $logoName,
					        'logo_dir_id' => $logoDirectory,
					        'bckg_file_name'  => $bckgName,
					        'bckg_dir_id' => $bckgDirectory,
					        'button_css'   => $button,
					        'background_css'=> $background
					    ));
			
		//open the login css file for writing	
	  $file = $themesDir."cp_themes/default/css/login.css";
	  $current = file_get_contents($file);
		
		$results = ee()->db->get('themee');

    if ($results->num_rows() > 0){
	    
	    //find the actual directory name of the uploaded logo 
		  $query = mysql_query("SELECT exp_upload_prefs.url FROM exp_themee LEFT JOIN exp_upload_prefs ON exp_themee.logo_dir_id=exp_upload_prefs.id");
			$result = mysql_fetch_assoc($query);
			
			//current editing
			
			if(!empty($filedir)){
				$filedir = $result['url'];
				
				$pattern = '/{filedir_[0-9]}/i'; 
				$replace = "";
				$real_logo_file_name =  preg_replace($pattern, $replace, $file_value);
				$real_bckg_file_name =  preg_replace($pattern, $replace, $bck_img);
				
				//put it together for the css
				$filepath = $filedir.$real_logo_file_name;
				$bckgpath = $filedir.$real_bckg_file_name;
			}else{
				$filepath = "";
				$bckgpath = "";
			}
    }
		
		//modifications that are appended to the login.css file
		$data= "
			body {
				/*background-color:	#".$bg_value." !important;*/
				background: #".$bg_value." url('".$bckgpath."')  repeat center top !important;
			}
			
			#content  {
				background:	url('".$filepath."') no-repeat center 2em !important;
				-webkit-background-size: 75%;
		    -moz-background-size: 75%;
		    -o-background-size: 75%;
		    background-size: 75%;
		    padding: 200px 40px 5px 20px;
		    margin: 50px auto 0 auto;
		    padding-bottom: 2.5em;
			}
			
			#content dt, #content label{
				color:				#".$btn_value." !important;
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
					        'logo_file_name'  => $logoName,
					        'logo_dir_id' => $logoDirectory,
					        'bckg_file_name'  => $bckgName,
					        'bckg_dir_id' => $bckgDirectory,
					        'button_css'   => $button,
					        'background_css'=> $background
					    ));
					    
					    
					    
	$file = $themesDir."cp_themes/default/css/login.css";
	$current = file_get_contents($file);
		
		$results = ee()->db->get('themee');

    if ($results->num_rows() > 0 || $results2->num_rows() > 0){

	    $bg_value = ($results->row('background_css')) ? $results->row('background_css') : "";
	    $btn_value = ($results->row('button_css')) ? $results->row('button_css') : "";
	    $file_value = ($results->row('logo_file_name')) ? $results->row('logo_file_name') : "";
		  $bck_img = ($results->row('bckg_file_name')) ? $results->row('bckg_file_name') : ""; 
   }
	 	
	 	
		$data.= "		
			body {
				/*background-color:	#".$bg_value." !important;*/
				background: #".$bg_value." url('".$bck_img."')  repeat center top !important;
			}
			
			#content  {
				background:	url('".$file_value."') no-repeat center 2em !important;
				-webkit-background-size: 75%;
		    -moz-background-size: 75%;
		    -o-background-size: 75%;
		    background-size: 75%;
		    padding: 200px 40px 5px 20px;
		    margin: 50px auto 0 auto;
		    padding-bottom: 2.5em;

			}
			
			#content dt, #content label{
				color:				#".$btn_value." !important;
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
	
		return $form.ee()->table->generate()."<input class='btn' type='submit' value='Submit'/> <script>$(document).ready(function(){

	
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