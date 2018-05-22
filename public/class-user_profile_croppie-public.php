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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/user_profile_croppie-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'croppie', plugin_dir_url( __FILE__ ) . 'js/croppie.js', array( 'jquery' ), $this->version, false );
    

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
                        <span class="profile-loader">
                          <img src="" class="ajax-loader" style="display: none;">                  
                          </span>
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

	public function user_profile_field_script(){    
 		$user_id = get_current_user_id();
	?>

	<script type="text/javascript">
    	jQuery(document).ready(function($) {        	
      		var userId = <?php echo $user_id; ?>;
        	jQuery(".crop").hide();
        	var $uploadCrop;
        	function readFile(input) {
            	if (input.files && input.files[0]) {
                	var reader = new FileReader();
                reader.onload = function (e) {
                    jQuery('#upload-demo').addClass('ready');
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function(){
                        console.log('jQuery bind complete');
                    });
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
        	}

	        $uploadCrop = jQuery('#upload-demo').croppie({
    	        enableExif: true,
        	    enableOrientation: true,
            	viewport: {
                	width: 250,
                	height: 250,
                	type: 'square'
            	},
            	boundary: {
	                width: 300,
    	            height: 300
        	    }
        	});

			jQuery('#fileUpload').on('change',function(e){
            	jQuery(".file-upload-label").hide();
            	jQuery(".crop, #upload-image").show();
            	readFile(this);
        	});

			jQuery(document).on('click','#upload-image', function (e) {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        	jQuery('.ajax-loader').show();
	        		$uploadCrop.croppie('result', {
                		type: 'canvas',
                		size: 'viewport',
                		format: 'png'
            		}).then(function (canvas) {
                		popupResult({
                    		src: canvas
                		});
                	var formData = new FormData();
                    //urltoFile(canvas, 'user_' + userId + '.png').then(function(file){});

                	var base_file = base64_to_image(canvas, 'user_' + userId + '.png');  
                	formData.append('file_upload', base_file );
                	formData.append('user_id', (typeof userId == 'undefined' ? 0 : userId));
                	formData.append('action','update_user_profile_picture');

                	jQuery.ajax({
	                    url         : ajaxurl,
	                    type        : "POST",
	                    data        : formData,
	                    dataType    : 'json',
	                    processData : false,
	                    contentType : false,
	                    cache       : false,
	                    success     : function (response) {
	                       jQuery('.ajax-loader').hide();
	                        if ( !response.result ) {
	                            swal('Oops. Something went wrong. Please try again.');

	                        }
	                        setTimeout(function() { location.reload(); }, 500 );
	                    }
                	});
            	});
        	});

           function popupResult(result) {
            	jQuery('.user-profile-image img').attr('src', result.src);
            	jQuery('#label-sidebar-inner img.profile-pic').attr('src', result.src);
        	}

           //return a promise that resolves with a File instance
        	function urltoFile(url, filename, mimeType){
                      
          	mimeType = mimeType || (url.match(/^data:([^;]+);/)||'')[1];
          	return (fetch(url)
                  .then(function(res){return res.arrayBuffer();})
                  .then(function(buf){return new File([buf], filename, {type:mimeType});})
          	);
        	}

	        /**
	         * Convert a base64 string in a Blob according to the data and contentType.
	         * 
	         * @param b64Data {String} Pure base64 string without contentType
	         * @param contentType {String} the content type of the file i.e (image/jpeg - image/png - text/plain)
	         * @param sliceSize {Int} SliceSize to process the byteCharacters
	         * @see http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
	         * @return Blob
	         */

	        function base64_to_image(url, filename, mimeType){

	          // Get the form element withot jQuery
	          //var form = document.getElementById("myAwesomeForm");

	          var ImageURL = url;

	          // Split the base64 string in data and contentType
	          var block = ImageURL.split(";");

	          // Get the content type of the image
	          var contentType = block[0].split(":")[1];// In this case "image/gif"

	          // get the real base64 content of the file
	          var realData = block[1].split(",")[1];// In this case "R0lGODlhPQBEAPeoAJosM...."


	          // Convert it to a blob to upload
	          var blob = b64toBlob(realData, contentType);

	          // Create a FormData and append the file with "image" as parameter name
	          //var formDataToUpload = new FormData(form);
	          //formDataToUpload.append("image", blob);

	          return blob;
	          
	        }

	        function b64toBlob(b64Data, contentType, sliceSize) {
	          contentType = contentType || '';
	          sliceSize = sliceSize || 512;

	          var byteCharacters = atob(b64Data);
	          var byteArrays = [];

	          for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
	              var slice = byteCharacters.slice(offset, offset + sliceSize);

	              var byteNumbers = new Array(slice.length);
	              for (var i = 0; i < slice.length; i++) {
	                  byteNumbers[i] = slice.charCodeAt(i);
	              }

	              var byteArray = new Uint8Array(byteNumbers);

	              byteArrays.push(byteArray);
	          }

	          var blob = new Blob(byteArrays, {type: contentType});

	          return blob;
	        }
		});
</script>
<?php }
}
