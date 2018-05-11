<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link        http://example.com/
 * @since      1.0.0
 *
 * @package    User_profile_croppie
 * @subpackage User_profile_croppie/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    User_profile_croppie
 * @subpackage User_profile_croppie/admin
 * @author     Devang Pujara <pujaradevang@gmail.com>
 */
class User_profile_croppie_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/user_profile_croppie-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/croppie.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/user_profile_croppie-admin.js', array( 'jquery' ), $this->version, false );

	}

	function user_profile_croppie_field( $user ) { ?>
 
    <h3><?php _e( 'Additional User Meta', 'textdomain' ); ?></h3>
 	<?php wp_enqueue_media(); ?>
    <table class="form-table">
 
        <tr>
            <th><label for="user_meta_image"><?php _e( 'A special image for each user', 'textdomain' ); ?></label></th>
            <td>
          
                <!-- Outputs the image after save -->
                <img src="<?php echo esc_url( get_the_author_meta( 'user_meta_image', $user->ID ) ); ?>" style="width:150px;"><br />
                <!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
                <input type="text" name="user_meta_image" id="user_meta_image" value="<?php echo esc_url_raw( get_the_author_meta( 'user_meta_image', $user->ID ) ); ?>" class="regular-text" />
                <!-- Outputs the save button -->
                <input type='button' class="additional-user-image button-primary" value="<?php _e( 'Upload Image', 'textdomain' ); ?>" id="uploadimage"/><br />
                <input type='hidden' name='image_attachment_id' id='image_attachment_id' value=''>
                <span class="description"><?php _e( 'Upload an additional image for your user profile.', 'textdomain' ); ?></span>
                
                  <?php /*
                <form id="um_form" method="POST">
                <div class="profile-pic col-md-4 col-sm-4 col-xs-12">
                <div class="upload-pic">
                                        <label class="file-upload-label" for="fileUpload">
                      <input type="file" id="fileUpload" value="image" class="hidden">
                      <div class="user-profile-image">
                                                  <img class="img-responsive" src="http://2.gravatar.com/avatar/274e9893bb0891e89afa6cbe11af1553?s=192&d=mm&r=g 2x" alt="">
                                                  <input type="hidden" id="imagebase64" name="imagebase64">    
                                                <div class="edit-image">                                                                                            
                          <div class="text-edit-image">Upload Image</div>
                        </div>
                      </div>
                    </label>
                     <div class="crop">
                        <div id="upload-demo"></div>
                        
                        <input class="btn" type="submit" id="upload-image" name="upload_image" value="UPDATE PICTURE" style="display:none;">     
                        <span class="profile-loader">
                          <img src="" class="ajax-loader" style="display: none;">                  
                          </span>
                    </div>

                </div>

              </div>
              </form>
              */ ?>
            </td>
        </tr>
 
    </table><!-- end form-table -->
	<?php } // additional_user_fields

	function save_user_profile_croppie_meta( $user_id ) {
  	// only saves if the current user can edit user profiles
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;
 
    update_usermeta( $user_id, 'user_meta_image', $_POST['user_meta_image'] );
	}

}
