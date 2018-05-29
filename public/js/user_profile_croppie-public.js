(function( $ ) {
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	jQuery(document).ready(function($) {        	
      		var userId = $("#croppie_user_id").val();
      		//alert(userId);
        	jQuery(".crop").hide();
        	var $uploadCrop;
        	function readFile(input) {
            	if (input) {
                	var reader = new FileReader();
                reader.onload = function (e) {
                    jQuery('#upload-demo').addClass('ready');
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function(){
                        console.log('jQuery bind complete');
                    });
                }
                reader.readAsDataURL(input);
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

			/*jQuery('#fileUpload').on('change',function(e){
            	jQuery(".file-upload-label").hide();
            	jQuery(".crop, #upload-image").show();
            	readFile(this);
        	});*/

			jQuery(document).on('click','#upload-image', function (e) {
        		var userId = $("#croppie_user_id").val();
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
	                    url         : wp_ajax.ajax_url,
	                    type        : "POST",
	                    data        : formData,
	                    dataType    : 'json',
	                    processData : false,
	                    contentType : false,
	                    cache       : false,
	                    success     : function (response) {
	                        if ( !response.result ) {
	                            alert('Oops. Something went wrong. Please try again.');

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
	         * Convert a URL string in a Blob according to the data and contentType.
	         * 
	         */

        	function toDataUrl(url, callback) {
			    var xhr = new XMLHttpRequest();
			    xhr.onload = function() {
			        var reader = new FileReader();
			        reader.onloadend = function() {
			            callback(reader.result);
			        }
			        reader.readAsDataURL(xhr.response);
			    };
			    xhr.open('GET', url);
			    xhr.responseType = 'blob';
			    xhr.send();
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

	        //jQuery for WordPress media upload
	        var file_frame; // variable for the wp.media file_frame

			// attach a click event (or whatever you want) to some element on your page
			$( 'input#fileUpload' ).on( 'click', function( event ) {
				event.preventDefault();

				// if the file_frame has already been created, just reuse it
				if ( file_frame ) {
					file_frame.open();
					return;
				}

				file_frame = wp.media.frames.file_frame = wp.media({
					title: $( this ).data( 'uploader_title' ),
					button: {
						text: $( this ).data( 'uploader_button_text' ),
					},
					multiple: false // set this to true for multiple file selection
				});

				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();

					// do something with the file here
					$(".file-upload-label").hide();
            		$(".crop, #upload-image").show();
            		//$('#fileUpload').trigger("change");
            		toDataUrl(attachment.url, function(myBase64) {
    				//console.log(myBase64); // myBase64 is the base64 string
					var blob  = base64_to_image(myBase64);
    				readFile(blob);
					});
				});
				file_frame.open();
			});
		});
})( jQuery );
