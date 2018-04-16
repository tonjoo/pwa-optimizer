<?php 

$options = get_option( 'pwa_optimizer' );

// echo "<pre>";
// print_r( $options );
// echo "</pre>";
// exit();

$offline_mode 	= $options['offline_mode'];
$assets 		= $options['assets'];
$manifest 		= $options['manifest'];
$lazyload 		= $options['lazyload'];

$files = array( 
	'root_directory' 	=> get_home_path(), 
	'service_worker' 	=> get_home_path().'sw.js', 
	// 'service_worker' 	=> tonjoo_pwa()->plugin_path().'/js/sw.js', 
	'manifest' 			=> get_home_path().'manifest.json', 
	'offline_page' 		=> get_home_path().'offline-page.html' 
); 

?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e( 'PWA Optimizer', 'tonjoo' ); ?></h1>
	<hr class="wp-header-end">

	<form method="post" role="form" action="" name="pwa_optimizer" class="pwa_optimizer" class="pwa_optimizer" autocomplete="off" enctype="multipart/form-data">
		<?php wp_nonce_field( 'pwa-optimizer', 'pwa_nonce' ); ?>

		<h2 class="nav-tab-wrapper">
			<a href="#offline-mode" class="nav-tab" id="offline-mode-tab"><?php _e( 'Offline Mode', 'tonjoo' ); ?></a>
			<a href="#assets" class="nav-tab" id="assets-tab"><?php _e( 'Assets', 'tonjoo' ); ?></a>
			<a href="#manifest" class="nav-tab" id="manifest-tab"><?php _e( 'Add to Homescreen', 'tonjoo' ); ?></a>
			<a href="#lazyload" class="nav-tab" id="lazyload-tab"><?php _e( 'LazyLoad', 'tonjoo' ); ?></a>
			<a href="#file-permission" class="nav-tab" id="file-permission-tab"><?php _e( 'Status', 'tonjoo' ); ?></a>
		</h2>

		<div class="metabox-holder">
			<!-- Offline Mode -->
			<div id="offline-mode" class="group" style="display: none;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for=""><?php _e( 'Status', 'tonjoo' ); ?></label></th>
							<td>
								<select name="pwa_optimizer[offline_mode][status]" class="regular">
									<option value="on" <?php selected( $offline_mode['status'], 'on', true ); ?>><?php _e( 'Enable', 'tonjoo' ); ?></option>
									<option value="off" <?php selected( $offline_mode['status'], 'off', true ); ?>><?php _e( 'Disable', 'tonjoo' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Offline Page', 'tonjoo' ); ?></label></th>
							<td>
								<textarea name="pwa_optimizer[offline_mode][offline_page]" class="pwa-editor-text"><?php echo stripslashes( $offline_mode['offline_page'] ); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Assets -->
			<div id="assets" class="group" style="display: none;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for=""><?php _e( 'Status', 'tonjoo' ); ?></label></th>
							<td>
								<select name="pwa_optimizer[assets][status]" class="regular">
									<option value="on" <?php selected( $assets['status'], 'on', true ); ?>><?php _e( 'Enable', 'tonjoo' ); ?></option>
									<option value="off" <?php selected( $assets['status'], 'off', true ); ?>><?php _e( 'Disable', 'tonjoo' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Never Cache Following Page', 'tonjoo' ); ?></label></th>
							<td>
								<textarea rows="8" cols="50" name="pwa_optimizer[assets][pgcache_reject_uri]" class="" placeholder="<?php echo '/wp-admin/'; ?>"><?php echo $assets['pgcache_reject_uri']; ?></textarea>
								<p class="description"><?php _e( 'Always ignore the specified pages / directories. Supports regular expressions. Must start and end with <code>/</code>. Example: <code>/wp-admin/</code>', 'tonjoo' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Manifest -->
			<div id="manifest" class="group" style="display: none;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for=""><?php _e( 'Status', 'tonjoo' ); ?></label></th>
							<td>
								<select name="pwa_optimizer[manifest][status]" class="regular">
									<option value="on" <?php selected( $manifest['status'], 'on', true ); ?>><?php _e( 'Enable', 'tonjoo' ); ?></option>
									<option value="off" <?php selected( $manifest['status'], 'off', true ); ?>><?php _e( 'Disable', 'tonjoo' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Application Name', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][app_name]" class="regular-text" value="<?php echo $manifest['app_name']; ?>" placeholder="<?php echo get_bloginfo('name'); ?>">
								<p class="description"><?php _e( 'Application Name', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Short Name', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][short_name]" class="regular-text" value="<?php echo $manifest['short_name']; ?>" placeholder="<?php echo get_bloginfo('name'); ?>">
								<p class="description"><?php _e( 'Short Name', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'App Description', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][app_description]" class="regular-text" value="<?php echo $manifest['app_description']; ?>" placeholder="<?php echo get_bloginfo('description'); ?>">
								<p class="description"><?php _e( 'Application Description', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Start URL', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][start_url]" class="regular-text" value="<?php echo $manifest['start_url']; ?>" placeholder="<?php echo get_bloginfo('url'); ?>">
								<p class="description"><?php _e( 'Start URL', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Theme Color', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][theme_color]" class="regular-text wp-color-picker-field" value="<?php echo $manifest['theme_color']; ?>">
								<p class="description"><?php _e( 'Theme Color', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Background Color', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[manifest][background_color]" class="regular-text wp-color-picker-field" value="<?php echo $manifest['background_color']; ?>">
								<p class="description"><?php _e( 'Splash Background Color', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Icons', 'tonjoo' ); ?></label></th>
							<td>
								<?php 
									$default_icons = array( 
										'logo_48', 'logo_96', 'logo_128', 
										'logo_144', 'logo_152', 'logo_192', 
										'logo_256', 'logo_384', 'logo_512' 
									);

									foreach ($default_icons as $icon) { 

										$key = $icon;
										$value = isset($manifest['icons'][$key]) ? $manifest['icons'][$key] : '';
										$size = str_replace( 'logo_', '', $key );

										?>
										<div class="list">
											<input type="text" name="pwa_optimizer[manifest][icons][<?php echo $key; ?>]" class="regular-text wpsa-url" value="<?php echo $value; ?>">
											<input type="button" class="button wpsa-browse" value="<?php _e( 'Choose File', 'tonjoo' ); ?>">
											<p class="description">
												<?php echo sprintf( __( 'Size %dx%dpx (.png) in pixel unit', 'tonjoo' ), $size, $size ); ?>
										</div>
										</p>
										<?php 
									}
								?>
								</p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Related Apps', 'tonjoo' ); ?></label></th>
							<td>
								<?php 
									$related_apps = $options['manifest']['related_apps'];

									$i = 0;
									foreach ($related_apps as $key => $value) { 

										$i++; 

										?>
										<div class="list">
											<input type="text" name="pwa_optimizer[manifest][related_apps][<?php echo $i; ?>][platform]" class="regular-text" value="<?php echo isset($value['platform']) ? $value['platform'] : ''; ?>" placeholder="<?php _e( 'platfrom', 'tonjoo' ); ?>">
											<input type="text" name="pwa_optimizer[manifest][related_apps][<?php echo $i; ?>][id]" class="regular-text" value="<?php echo isset($value['id']) ? $value['id'] : ''; ?>" placeholder="<?php _e( 'id', 'tonjoo' ); ?>">
										</div>
										<?php 
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- LazyLoad -->
			<div id="lazyload" class="group" style="display: none;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for=""><?php _e( 'Status', 'tonjoo' ); ?></label></th>
							<td>
								<select name="pwa_optimizer[lazyload][status]" class="regular">
									<option value="on" <?php selected( $lazyload['status'], 'on', true ); ?>><?php _e( 'Enable', 'tonjoo' ); ?></option>
									<option value="off" <?php selected( $lazyload['status'], 'off', true ); ?>><?php _e( 'Disable', 'tonjoo' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Preload Image', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[lazyload][preload_image]" class="regular-text" value="<?php echo $lazyload['preload_image']; ?>" placeholder="<?php _e( 'Preload Image', 'tonjoo' ); ?>">
								<p class="description"><?php _e( 'Base64 Encode', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'CSS Class', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[lazyload][css_class]" class="regular-text" value="<?php echo $lazyload['css_class']; ?>" placeholder="<?php _e( 'css class selector (separate with space)', 'tonjoo' ); ?>">
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Root Margin', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[lazyload][root_margin]" class="regular-text" value="<?php echo $lazyload['root_margin']; ?>" placeholder="<?php _e( 'Root Margin', 'tonjoo' ); ?>">
								<p class="description"><?php _e( 'Root Margin', 'tonjoo' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Threshold', 'tonjoo' ); ?></label></th>
							<td>
								<input type="text" name="pwa_optimizer[lazyload][threshold]" class="regular-text" value="<?php echo $lazyload['threshold']; ?>" placeholder="<?php _e( 'Threshold', 'tonjoo' ); ?>">
								<p class="description"><?php _e( 'Threshold', 'tonjoo' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- File Permission -->
			<div id="file-permission" class="group" style="display: none;">
				<table class="widefat tonjoo-pwa-permissions">
					<thead>
						<tr>
							<th style="width: 150px;"><?php _e( 'Filename', 'tonjoo' ); ?></th>
							<th><?php _e( 'File/Folder', 'tonjoo' ); ?></th>
							<th style="width: 50px;"><?php _e( 'Current Permission', 'tonjoo' ); ?></th>
							<th style="width: 50px;"><?php _e( 'Recommended Permission', 'tonjoo' ); ?></th>
							<th style="width: 150px;"><?php _e( 'Action', 'tonjoo' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach ($files as $key => $file) { 
								if( ! file_exists($file) ) 
									continue; 

								?>
								<tr class="<?php echo is_writable($file) ? 'is-writeable' : 'is-readable'; ?>">
									<td style="width: 150px;">
										<?php 
											if( 'root_directory' == $key ){ 
												echo __( 'root directory', 'tonjoo' );
											} else { 
												echo basename($file);
											}
										?>
									</td>
									<td><?php echo $file; ?></td>
									<td class="text-center current-permission" style="width: 50px;">
										<?php 
											clearstatcache();
											echo substr(sprintf("%o",fileperms($file)),-4); 
										?>
									</td>
									<td class="text-center" style="width: 50px;">755</td> <!-- // use is_dir() or is_file() to detect value is dir or file -->
									<td style="width: 150px;">
										<?php 
											if( is_writable($file) ){ // is_writable or is_readable
												echo __( 'No Action Required', 'tonjoo' );
											} else { 
												echo '<a href="#" class="button btn-pwa-change-permission" data-filename="'.$file.'">'.__( 'Set Is Writeable', 'tonjoo' ).'</a>';
											}
										?>
									</td>
								</tr>
								<?php 
							}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<?php submit_button( __( 'Save', 'tonjoo' ), 'primary left', 'submit', false ); ?>
	</form>
</div>
