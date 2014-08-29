<?php
/**
 * Plugin Name: Dev Themer
 * Plugin URI:  http://funkhaus.us/
 * Description: Switch to a dev theme when administrator is logged in
 * Version:     1.0
 * Author:      John Robson, Funkhaus
 * Author URI:  http://funkhaus.us
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'template', 'devt_switch_theme' );
add_filter( 'stylesheet', 'devt_switch_theme' ); // only WP 3* and below
add_filter( 'option_template', 'devt_switch_theme' );
add_filter( 'option_stylesheet', 'devt_switch_theme' );

function devt_switch_theme( $template = '' ) {

	// Get all themes
	$themes = wp_get_themes();

	// Check if user is administrator, set template if so
	if ( current_user_can( 'manage_options' ) ) {
		$selected = get_option('devt_admin_theme');
		if ( isset($themes[$selected]) ) {
			$template = $selected;
		}
	} elseif ( wp_is_mobile() ) {
		$selected = get_option('devt_mobile_theme');
		if ( isset($themes[$selected]) ) {
			$template = $selected;
		}
	}

	return $template;
}


/*
 * Create settings page for plugin.
 */

	/* Call Settings Page */
	function devt_settings_page() { ?>

		<div class="wrap">
			<h2>Dev Themer Options</h2>
			<form action="options.php" method="post" id="devt_settings">
				<?php settings_fields('devt_settings'); ?>
				<?php $themes = wp_get_themes(); ?>
				<?php $current_theme = wp_get_theme(); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label>Admins users will see:</label></th>
							<td>
								<select name="devt_admin_theme" id="devt_admin_theme">
									<option value="0" <?php if ( ! get_option('devt_admin_theme')) echo 'selected'; ?>>Default Theme</option>
									<?php foreach ( $themes as $slug => $theme ) : ?>
										<option value="<?php echo $slug; ?>" <?php selected( get_option('devt_admin_theme'), $slug ); ?>><?php echo $theme->get( 'Name' ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>

						</tr>
						<tr valign="top">
							<th scope="row"><label>Mobile users will see:</label></th>
							<td>
								<select name="devt_mobile_theme" id="devt_mobile_theme">
									<option value="0" <?php if ( ! get_option('devt_mobile_theme')) echo 'selected'; ?>>Default Theme</option>
									<?php foreach ( $themes as $slug => $theme ) : ?>
										<option value="<?php echo $slug; ?>" <?php selected( get_option('devt_mobile_theme'), $slug ); ?>><?php echo $theme->get( 'Name' ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
			</form>
		</div>

		<?php
	}

	/* Save Takeover Settings */
	function devt_settings_init(){
		register_setting('devt_settings', 'devt_admin_theme');
		register_setting('devt_settings', 'devt_mobile_theme');
	}
	add_action('admin_init', 'devt_settings_init');

	function devt_add_settings() {
		add_submenu_page( 'tools.php', 'Dev Themer', 'Dev Themer', 'manage_options', 'devt_settings', 'devt_settings_page' );
	}

	add_action('admin_menu','devt_add_settings');

?>