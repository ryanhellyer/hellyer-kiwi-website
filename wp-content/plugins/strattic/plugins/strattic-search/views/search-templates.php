<?php
/**
 * Search Templates setting screen.
 *
 * @package Strattic Search
 */

?>
<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( 'strattic-search-templates' ); ?>
		<h1><?php esc_html_e( 'Strattic: Search Templates', 'strattic' ); ?></h1>
		<p class="stuffbox" id="template-tags">
			<strong><?php esc_html_e( 'Template tags:', 'strattic' ); ?></strong>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{id}}">{{id}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{path}}">{{path}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{url}}">{{url}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{date}}">{{date}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{time}}">{{time}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{title}}">{{title}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{thumbnail}}">{{thumbnail}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{excerpt}}">{{excerpt}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{content}}">{{content}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{author.nickname}}">{{author.nickname}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{author.firstName}}">{{author.firstName}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{author.lastName}}">{{author.lastName}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{author.name}}">{{author.name}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{search_string}}">{{search_string}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{number}}">{{number}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="{{{nav}}}">{{{nav}}}</code>
			<code data-tooltip="<?php esc_attr_e( 'Click to copy.', 'strattic' ); ?>" data-copy="[strattic_search_form]">[strattic_search_form]</code>
		</p>
		<?php
		foreach ( $this->fields() as $field ) {
			if ( 'editor' !== $field['type'] ) {
				continue; // Ignore everything but editor fields as others are handled on another admin page
			}
			$slug           = $field['slug'];
			$label          = $field['label'];
			$description    = $field['description'];
			$template       = $field['template'];
			$template_label = $field['template_label'];

			?>
			<h2><?php echo esc_html( $label ); ?></h2>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<a href="#<?php echo esc_attr( $template ); ?>">
				<?php
				echo esc_html(
					sprintf(
						// translators: A translated label describing the template.
						__( 'Show %1$s Template HTML', 'strattic' ),
						$template_label
					)
				);
				?>
			</a>
			<p>
				<textarea id="<?php echo esc_attr( $slug ); ?>" class="code-editor" name="<?php echo esc_attr( self::TEMPLATES_OPTION . '[' . $slug . ']' ); ?>"><?php echo $this->get_template( $slug ); /* No escaping due to use of HTML here */ ?></textarea> <?php // @codingStandardsIgnoreLine ?>
			</p>
			<?php
		}
		?>

		<div id="template-output-one-result" class="modal">
			<div>
				<a href="#close" title="Close" class="close"><?php esc_html_e( 'Close', 'strattic' ); ?></a>
				<h2><?php esc_html_e( 'Output for a single search result:', 'strattic' ); ?></h2>
				<p>
					<textarea class="code-editor" id="search-results"><?php echo esc_textarea( $search_results ); ?></textarea>
				</p>
			</div>
		</div>
		<div id="template-output-many-results" class="modal">
			<div>
				<a href="#close" title="Close" class="close"><?php esc_html_e( 'Close', 'strattic' ); ?></a>
				<h2><?php esc_html_e( 'Output for many search results:', 'strattic' ); ?></h2>
				<p>
					<textarea class="code-editor" id="search-many-results"><?php echo esc_textarea( $search_many_results ); ?></textarea>
				</p>
			</div>
		</div>
		<div id="template-output-no-results" class="modal">
			<div>
				<a href="#close" title="Close" class="close"><?php esc_html_e( 'Close', 'strattic' ); ?></a>
				<h2><?php esc_html_e( 'Output for no search results:', 'strattic' ); ?></h2>
				<p>
					<textarea class="code-editor" id="search-no-results"><?php echo esc_textarea( $search_no_results ); ?></textarea>
				</p>
			</div>
		</div>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save', 'strattic' ); ?>" />
		</p>
	</form>
</div>
