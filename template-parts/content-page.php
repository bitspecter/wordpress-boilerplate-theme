<div class="container">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="entry-header">
			<h1 class="title">
				<?= get_the_title() ?>
			</h1>
		</header>

		<?php if(has_post_thumbnail()): ?>
			<img src="<?php the_post_thumbnail_url($size = 'large'); ?>" alt="<?= get_the_title() ?>" class="img-fluid"></a>
		<?php endif; ?>


		<div class="entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__('Pages:', 'bitspecter'),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php if (get_edit_post_link()) : ?>
			<footer class="entry-footer">
				<?php
				edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__('Edit <span class="screen-reader-text">%s</span>', 'bitspecter'),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post(get_the_title())
					),
					'<span class="edit-link">',
					'</span>'
				);
				?>
			</footer>
		<?php endif; ?>
	</article>
</div>