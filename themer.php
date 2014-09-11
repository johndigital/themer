<?php
/**
 * Plugin Name: Themer
 * Plugin URI:  http://funkhaus.us/
 * Description: Switch to an alternate theme for admins and/or mobile users
 * Version:     1.1
 * Author:      John Robson, Funkhaus
 * Author URI:  http://funkhaus.us
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'template', 'themer_switch_theme' );
add_filter( 'stylesheet', 'themer_switch_theme' ); // only WP 3* and below
add_filter( 'option_template', 'themer_switch_theme' );
add_filter( 'option_stylesheet', 'themer_switch_theme' );



/*
 * Code that actually handles the switch of the theme
 */
	function themer_switch_theme( $template = '' ) {
	
		// Get all themes
		$themes = wp_get_themes();
	
		// Check if user is administrator, set template if so
		if ( current_user_can( 'manage_options' ) ) {
			$selected = get_option('themer_admin_theme');
			if ( isset($themes[$selected]) ) {
				$template = $selected;
			}
		} elseif ( wp_is_mobile() ) {
			$selected = get_option('themer_mobile_theme');
			if ( isset($themes[$selected]) ) {
				$template = $selected;
			}
		}
	
		return $template;
	}


/*
 * Create settings page for plugin.
 */
	function themer_settings_page() 
	{
		?>

		<div class="wrap">
			<h2>Themer Options</h2>
			<form action="options.php" method="post" id="themer_settings">
				<?php 
					// Output settings field
					settings_fields('themer_settings');
					
					// Get themes
					$themes = wp_get_themes();
					$current_theme = wp_get_theme();
				?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label>Admins users will see:</label></th>
							<td>
								<select name="themer_admin_theme" id="themer_admin_theme">
									<option value="0" <?php if ( ! get_option('themer_admin_theme')) echo 'selected'; ?>>Default Theme</option>
									
									<?php foreach ( $themes as $slug => $theme ) : ?>
										<option value="<?php echo $slug; ?>" <?php selected( get_option('themer_admin_theme'), $slug ); ?>><?php echo $theme->get( 'Name' ); ?></option>
									<?php endforeach; ?>
									
								</select>
							</td>

						</tr>
						<tr valign="top">
							<th scope="row"><label>Mobile users will see:</label></th>
							<td>
								<select name="themer_mobile_theme" id="themer_mobile_theme">
									<option value="0" <?php if ( ! get_option('themer_mobile_theme')) echo 'selected'; ?>>Default Theme</option>
									
									<?php foreach ( $themes as $slug => $theme ) : ?>
										<option value="<?php echo $slug; ?>" <?php selected( get_option('themer_mobile_theme'), $slug ); ?>><?php echo $theme->get( 'Name' ); ?></option>
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

/* 
 * Save Takeover Settings 
 */
 	/* Register new settings */
	function themer_settings_init(){
		register_setting('themer_settings', 'themer_admin_theme');
		register_setting('themer_settings', 'themer_mobile_theme');
	}
	add_action('admin_init', 'themer_settings_init');

 	/* Add to menu */
	function themer_add_settings() {
		add_submenu_page( 'tools.php', 'Themer', 'Themer', 'manage_options', 'themer_settings', 'themer_settings_page' );
	}
	add_action('admin_menu','themer_add_settings');
?>