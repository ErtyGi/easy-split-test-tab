
		<div class="wrap">
			<img class="icon32" src="<?php echo EASY_SPLITTEST_TAB_URL ?>/img/st_logo_32x32.png" />
			<h2><?php _e('Easy Split Test Tab: Options',EASY_SPLITTEST_TAB_DOMAIN); 			
			?></h2>	                 	
			<form method="post" action="options.php"> 
		<?php settings_fields( 'default' ); ?>
		<h3><?php _e('Update the following options.',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_splittest_tab_credits"><?php _e('Add Credits',EASY_SPLITTEST_TAB_DOMAIN);?></label></th>
					<td>
					
					<?php $easy_splittest_tab_credits = get_option('easy_splittest_tab_credits'); ?>
					<select name="easy_splittest_tab_credits" id="easy_splittest_tab_credits">
					<option value="1" <?php if($easy_splittest_tab_credits == "1") { echo "selected='selected'"; } ?>><?php _e('YES',EASY_SPLITTEST_TAB_DOMAIN);?></option>
					<option value="0" <?php if($easy_splittest_tab_credits == "0") { echo "selected='selected'"; } ?>><?php _e('No',EASY_SPLITTEST_TAB_DOMAIN);?></option>
					</select>
					
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="easy_splittest_tab_noindex"><?php _e('Add noindex, nofollow variante pages',EASY_SPLITTEST_TAB_DOMAIN);?></label></th>
					<td>
					<?php $easy_splittest_tab_noindex = get_option('easy_splittest_tab_noindex'); ?>
					<select name="easy_splittest_tab_noindex" id="easy_splittest_tab_noindex">
					<option value="1" <?php if($easy_splittest_tab_noindex == "1") { echo "selected='selected'"; } ?>><?php _e('YES',EASY_SPLITTEST_TAB_DOMAIN);?></option>
					<option value="0" <?php if($easy_splittest_tab_noindex == "0") { echo "selected='selected'"; } ?>><?php _e('NO',EASY_SPLITTEST_TAB_DOMAIN);?></option>
					</select>
					</td>
				</tr>
			</table>
		<?php submit_button(); ?>
	</form>
			
			