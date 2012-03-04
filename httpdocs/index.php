<!DOCTYPE HTML>
<html>
	<head>
		<title>Testing PLUpload</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="js/plupload.js"></script>
		<script src="js/plupload.flash.js"></script>
		<script src="js/plupload.html5.js"></script>
	</head>

	<body>

    	<div id="upload_container">
    			<button id="pickfile">Select File</button>
    		<div id="upload_status"></div>
		</div>

		

		<script>
		var uploader;	//The uploader object
		var debug = true;

		jQuery(document).ready(function(){


			console.log("Initializing plupload");

			//Create new Plupload object
			uploader = new plupload.Uploader({
				runtimes : 'flash',
				browse_button : 'pickfile',
				container : 'upload_container',
				max_file_size : '1000mb',
				chunk_size : '250kb',
				multi_selection: false,
				url: 'upload.php',
				flash_swf_url : 'js/plupload.flash.swf',
				filters : [{title : "Test files", extensions : "jpg,mp4,mov"}],
				multipart : true,
				multipart_params : {
					authenticity_token : "test",
					submission_id : "test",
					_awards3d_session : "test"
				}
			});	



			//On initialization...
			uploader.bind('Init', function(up, params) {
				//Nothing here..
				});
			
			//Initialize the uploader
			uploader.init();

			//Debug the uploader info
			if (debug) {
				console.log("Plupload initialized");
				console.debug(uploader);
				console.log("Plupload runtime: " + uploader.runtime);
			}

			/**
			 * When someone adds a file
			 */
			uploader.bind('FilesAdded', function(up, files) {
				
				//Remove all the other files
				if (uploader.files.length > 1) {
					if (debug) console.log("Removing files");
					for (i=0; i<uploader.files.length-1; i++){
						uploader.removeFile(uploader.files[i]);
					}			
				}

				//If the image preview is showing, hide it
				if (jQuery('#upload_preview').is(':visible')) {
					jQuery('#upload_preview').slideUp();
				}

				//If the existing preview is showing, hide it
				if (jQuery('#existing_preview').is(':visible')) {
					jQuery('#existing_preview').slideUp();
				}

				
				//Upload the file
				uploader.start();
				
			});
				

			/**
			 * When a chunk has been uploaded
			 */
			uploader.bind('ChunkUploaded', function(up, file, response){
				
				response = jQuery.parseJSON( response.response );

				if (debug) {
					console.log("Chunk uploaded:");
					console.debug(response);
				}
				
				if (response.error){
					uploader.trigger('Error', {
						code : response.error.code,
						message : response.error.message,
						file : file
					});
				}
				
			});
			
			/**
			 * Upload Progress
			 */
			uploader.bind('UploadProgress', function(up, file) {

				if (debug) {
					console.log("Upload progress: " + file.loaded + " out of " + file.size + " bytes");
					console.debug(file);
				}

				if (file.percent == 100) {
					jQuery('#upload_status').html("File uploaded.");
				} else {
					jQuery('#upload_status').html(file.percent + "%");
				}
			});

			/**
			 * Error
			 */
			uploader.bind('Error', function(up, err) {
				
				jQuery('#upload_status').append("<div>Error: " + err.code +
					", Message: " + err.message +
					(err.file ? ", File: " + err.file.name : "") +
					"</div>"
					);

				up.refresh(); // Reposition Flash/Silverlight
				
			});

			/**
			 * Upload is finished
			 */
			uploader.bind('FileUploaded', function(up, file) {
				
				jQuery('#upload_status').html("File uploaded.");
				
			});	


		});

		</script>

	</body>
</html>