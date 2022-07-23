<?php
get_header();
?>
<main id="primary" class="site-main">
	<div class="container">
		<div class="d-flex align-items-center justify-content-center flex-column py-5">
			<h1 class="display-1 fw-bold text-black text-center">404</h1>
			<div class="row justify-content-center">
				<div class="col-12 col-lg-8">
					<p class="text-center">
						<?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', '_s'); ?>
					</p>
					<div class="d-flex justify-content-center mt-4">
						<?php
						get_search_form();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
