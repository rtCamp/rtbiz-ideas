<?php
wp_enqueue_script( 'jquery-form', array( 'jquery' ), false, true );
?>
<script>
	jQuery(document).ready(function($) {

		jQuery('#btninsertIdeaFormSubmit').click(function(e) {
			e.preventDefault();
			data = new FormData();
			data.append("action", 'wpideas_insert_new_idea');
			data.append("txtIdeaTitle", $('#txtIdeaTitle').val());
			data.append("txtIdeaContent", $('#txtIdeaContent').val());
			data.append("product_id", $('#product_id').val());
			data.append("product", $('#product').val());
			// Get the selected files from the input.
			var files = document.getElementById('file').files;
			// Loop through each of the selected files.
			for (var i = 0; i < files.length; i++) {
				var file = files[i];

				// Add the file to the request.
				data.append('upload[]', file, file.name);
			}
			//data.append("upload", $('#file').get(0).files[0]);
			$.ajax({
				url: rt_wpideas_ajax_url,
				type: 'POST',
				data: data,
				processData: false,
				contentType: false,
				success: function(res) {
					try {
						var json = JSON.parse(res);

						if (json.title) {
							$('#txtIdeaTitleError').html(json.title);
							$('#txtIdeaTitleError').show();
						} else {
							$('#txtIdeaTitleError').hide();
						}
						if (json.content) {
							$('#txtIdeaContentError').html(json.content);
							$('#txtIdeaContentError').show();
						} else {
							$('#txtIdeaContentError').hide();
						}
						if (json.product) {
							$('#txtIdeaProductError').html(json.product);
							$('#txtIdeaProductError').show();
						} else {
							$('#txtIdeaProductError').hide();
						}
					}
					catch (e)
					{
						alert(res);
						tb_remove();
						if (res === 'product') {
							$("body, html").animate({
								scrollTop: $('#tab-ideas_tab').offset().top
							}, 600);
						}
						$('#txtSearchIdea').val('');
						$('#txtSearchIdea').keyup();
						$('#lblIdeaSuccess').show();
					}
					jQuery('article:nth-child(1)').addClass('sticky');
					setTimeout(function() {
						jQuery('article:nth-child(1)').removeClass('sticky');
						$('#lblIdeaSuccess').hide();
					}, 2000);
				},
			});
		});
	});

</script>
<form id="insertIdeaForm" method="post" enctype="multipart/form-data" action="">
	<h2>Suggest New Idea</h2>
	<div>
		<label for="txtIdeaTitle"><?php _e( 'Title:', 'wp-ideas' ) ?></label>

		<input type="text" name="txtIdeaTitle" id="txtIdeaTitle" class="required" value="<?php if ( isset( $_POST[ 'txtIdeaTitle' ] ) ) echo $_POST[ 'txtIdeaTitle' ]; ?>" />

		<label class="error" id="txtIdeaTitleError" style="display:none;"></label>

	</div>

	<div>
		<label for="txtIdeaContent"><?php _e( 'Detail:', 'wp-ideas' ) ?></label>

		<textarea name="txtIdeaContent" id="txtIdeaContent" style="height:250px;" class="required"><?php
			if ( isset( $_POST[ 'txtIdeaContent' ] ) ) {
				if ( function_exists( 'stripslashes' ) ) {
					echo stripslashes( $_POST[ 'txtIdeaContent' ] );
				} else {
					echo $_POST[ 'txtIdeaContent' ];
				}
			}
			?></textarea>
		<label class="error" id="txtIdeaContentError" style="display:none;"></label>
	</div>
	<?php
	if ( get_post_type() != 'product' ) {
		?> 
		<div>
			<select class="required" id="product_id" name="product_id">
				<option value=""> Select Product </option>
				<?php
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => -1,
				);

				query_posts( $args );
				while ( have_posts() ) : the_post();
					echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
				endwhile;
				?>
			</select> 
			<label class="error" id="txtIdeaProductError" style="display:none;"></label>
		</div>

		<?php
	}
	?>

	<div>
		<input type="file" name="upload[]" id="file" multiple />
	</div>

	<div>
		<?php if ( get_post_type() == 'product' && is_single() ) { ?>
			<input type="hidden" id="product_id" name="product_id" value="<?php
			global $post;
			echo $post -> ID;
			?>" /><input type="hidden" id="product" name="product" value="product" />
			   <?php } ?>
		<input type="hidden" name="submitted" id="submitted" value="true" />
		<?php wp_nonce_field( 'idea_nonce', 'idea_nonce_field' ); ?>
		<input type="button" id="btninsertIdeaFormSubmit" value="<?php _e( 'Submit My Idea', 'wp-ideas' ) ?>" />
		<a href="javascript:tb_remove();" id="insertIdeaFormCancel">Cancel</a>
	</div>
	<div id="output1"></div>
</form>