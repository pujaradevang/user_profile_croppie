<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link        http://example.com/
 * @since      1.0.0
 *
 * @package    User_profile_croppie
 * @subpackage User_profile_croppie/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    User_profile_croppie
 * @subpackage User_profile_croppie/public
 * @author     Devang Pujara <pujaradevang@gmail.com>
 */
class User_profile_croppie_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_profile_croppie_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_profile_croppie_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/user_profile_croppie-public.css', array(), $this->version, 'all' );
    		wp_enqueue_style( 'croppie-css', plugin_dir_url( __FILE__ ) . 'css/croppie.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_profile_croppie_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_profile_croppie_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
    		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/user_profile_croppie-public.js', array( 'jquery' ), $this->version, true );
    		wp_localize_script( $this->plugin_name, 'wp_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'croppie', plugin_dir_url( __FILE__ ) . 'js/croppie.js', array( 'jquery' ), $this->version, false );
    

	}

	  /**
	   * This filter insures users only see their own media
	   */
  	public function filter_media( $query ) {
	    // admins get to see everything
	    if ( ! current_user_can( 'manage_options' ) )
	      $query['author'] = get_current_user_id();
	    return $query;
  	}

   	public function user_profile_field_func( $atts ) {
	    $user_id = get_current_user_id();
	    $user_profile_url = get_user_meta($user_id,'user_meta_image',true);
	    if(!isset($user_profile_url)){
	      $user_profile_url = get_avatar_url( $user_id, $args );
	    }
		$user_profile_field = '';
		$user_profile_field .= '<div class="profile-pic col-md-4 col-sm-4 col-xs-12">
                <div class="upload-pic">
                                        <label class="file-upload-label" for="fileUpload">
                      <input type="file" id="fileUpload" value="image" class="hidden">
                      <div class="user-profile-image">
			  <img class="img-responsive" src="'.$user_profile_url.'" alt="">
			<div class="edit-image">
                          <div class="text-edit-image"><span class="dashicons dashicons-upload"></span></div>
                        </div>
                      </div>
                    </label>
                     <div class="crop">
                        <div id="upload-demo"></div>
                        <input class="btn" type="submit" id="upload-image" name="upload_image" value="UPDATE PICTURE" style="display:none;">
                    </div>
                </div>
              </div>';
   		return $user_profile_field;
	}

	public function pf_upload_image($f,$pid,$t='',$c='') {
  		wp_update_attachment_metadata( $pid, $f );
  		if( !empty( $_FILES[$f]['name'] )) { //New upload
   			// require_once( ABSPATH . 'wp-admin/includes/file.php' );
   			// include_once( ABSPATH . 'wp-admin/includes/image.php' );
    		// $override['action'] = 'editpost';
    		$override['test_form'] = false;
    		$file = wp_handle_upload( $_FILES[$f], $override );
 
    		if ( isset( $file['error'] )) {
      			return new WP_Error( 'upload_error', $file['error'] );
    		}
 
    		$file_type = wp_check_filetype($_FILES[$f]['name'], array(
      			'jpg|jpeg' =>  'image/jpeg',
  				'gif' =>  'image/gif',
      			'png' =>  'image/png',
    		));
    		if ($file_type['type']) {
      			$name_parts = pathinfo( $file['file'] );
      			$name = $file['filename'];
      			$type = $file['type'];
      			$title = $t ? $t : $name;
      			$content = $c;
 
				$attachment = array(
        			'post_title' =>  $title,
        			'post_type' =>  'attachment',
        			'post_content' =>  $content,
        			'post_parent' =>  $pid,
        			'post_mime_type' =>  $type,
        			'guid' =>  $file['url'],
      			);
 
			foreach( get_intermediate_image_sizes() as $s ) {
        		$sizes[$s] = array( 'width' =>  '', 'height' =>  '', 'crop' =>  true );
        		$sizes[$s]['width'] = get_option( "{$s}_size_w" ); // For default sizes set in options
        		$sizes[$s]['height'] = get_option( "{$s}_size_h" ); // For default sizes set in options
        		$sizes[$s]['crop'] = get_option( "{$s}_crop" ); // For default sizes set in options
      		}
 
      		$sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );
 
      		foreach( $sizes as $size =>  $size_data ) {
        		$resized = image_make_intermediate_size( $file['file'], $size_data['width'], $size_data['height'], $size_data['crop'] );
        		if ( $resized )
          		$metadata['sizes'][$size] = $resized;
      		}

      		$attach_id = wp_insert_attachment( $attachment, $file['file'] );
 
		    if ( !is_wp_error( $attach_id )) {
        		$attach_meta = wp_generate_attachment_metadata( $attach_id, $file['file'] );
        		wp_update_attachment_metadata( $attach_id, $attach_meta );
      		}
   
     		return array(
      			'pid' => $pid,
      			'url' => $file['url'],
      			'file'=> $file,
      			'attach_id'=> $attach_id
     		);
    		}
  		}
	} 

	public function update_user_profile_picture( ) {
	if (isset($_FILES)) {      
		if (!empty($_FILES)) {
            	# code...
            	$_FILES['file_upload']['name'] = "user_".$_REQUEST['user_id'].".png";
              	$file_att_upload = $this->pf_upload_image('file_upload','user_'.$_REQUEST['user_id']);
              	//echo "<pre>";print_r($file_att_upload);exit;
             	//  $test = update_field('field_59cb0d7bb0e8f',$att['attach_id'],'user_'.$_REQUEST['user_id']);//change {field_key} to actual key
            	$user_profile_field_upload = update_user_meta( $_REQUEST['user_id'], 'user_meta_image', $file_att_upload['url'] );
             	//var_dump($att);
             	//exit();
            	echo json_encode(['result' => $user_profile_field_upload]);
        	}
    	}else {
        	echo json_encode(['result' => false]);
    	}
	    die();
	}

	public function user_profile_field_id(){    
 		$user_id = get_current_user_id();
	?>
    	<input type="hidden" id="croppie_user_id" value="<?php echo $user_id ?>" />
	<?php }
}
