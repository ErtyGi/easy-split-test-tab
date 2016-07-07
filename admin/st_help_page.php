<?php



// The help page
?>
	<div class="wrap">
			<img class="icon32" src="<?php echo EASY_SPLITTEST_TAB_URL ?>/img/st_logo_32x32.png" /> 
			<h2><?php _e('Easy Split Test Tab::Help',EASY_SPLITTEST_TAB_DOMAIN);?></h2>
				<table style="widht:100%" cellspacing="0" cellpadding="2">
					<tr>
						<td style="widht:60%" valign="top">						
						<h3 class="title"><?php _e('Description',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
						<p> <?php _e('Through the Google Experiments and Easy Split Test Tab  you can be able to create your perfect website.
						A website that reaches purposes easly through the experiments.',EASY_SPLITTEST_TAB_DOMAIN);?></p>												
						<p> <?php _e('Helps to decide which pages generate greater results in the completion of a goal (the sale of a product, sending a request etc..).
						The plugin keeps track of all the experiments within your site. ',EASY_SPLITTEST_TAB_DOMAIN);?></p>												
						
						
								<h3 class="title"><?php _e('First Step: Create your original Page',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
								<p><?php _e('You need to create your landing page on ',EASY_SPLITTEST_TAB_DOMAIN); ?><a href="<?php echo admin_url( 'edit.php?post_type=page'); ?>"><?php echo _e('pages area',EASY_SPLITTEST_TAB_DOMAIN);?></a></p>
							
								<h3 class="title"><?php _e('Second Step: Create your variation pages',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
								<p><?php _e('Next you have to create different variant of your ',EASY_SPLITTEST_TAB_DOMAIN); ?><a href="<?php echo admin_url( 'edit.php?post_type=page'); ?>"><?php echo _e('landing page / sale page',EASY_SPLITTEST_TAB_DOMAIN);?></a></p>
							
								<h3 class="title"><?php _e('Third Step: Create your experiment on Google Analytics',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
								
								
								<h3 class="title"><?php _e('Fourth step: Create your experiment on Easy Split Test Tab',EASY_SPLITTEST_TAB_DOMAIN);?></h3>
								<p><?php _e('Inser to ',EASY_SPLITTEST_TAB_DOMAIN); ?><a href="<?php echo admin_url( 'admin.php?page=easy-split-test-tab'); ?>"><?php echo _e('Experiments',EASY_SPLITTEST_TAB_DOMAIN);?></a> <?php _e(' your project.',EASY_SPLITTEST_TAB_DOMAIN); ?></p>
							
						</td>
						<td style="widht:5%" valign="top">&nbsp;</td>
						<td style="width:30%;" valign="top">						
						<h2><?php _e('Last post from Blog Prima Posizione',EASY_SPLITTEST_TAB_DOMAIN); ?></h2>
				<?php // Get RSS Feed(s)
				  include_once(ABSPATH . WPINC . '/rss.php');
				  $rss = fetch_rss('http://blog.prima-posizione.it/feed-pp');
				  $maxitems = 6;
				  $items = array_slice($rss->items, 0, $maxitems);
				?>

				<ul>
				  <?php if (empty($items)): ?>
					<li><?php _e('No items',EASY_SPLITTEST_TAB_DOMAIN);?></li>
				  <?php else:
					  foreach ( $items as $item ):
						?>
						<li>
						  <a href='<?php echo $item['link']; ?>' title='<?php echo $item['title']; ?>'>
							<?php echo $item['title']; ?>
						  </a>
						</li>
						<li><img src="<?php echo $item['url']; ?>" width="90" style="padding:2px 5px;" align="left" /><?php echo $item['description']; ?></li>
						<?php
					  endforeach;
					endif;
				  ?>
				</ul>
					<div id="contact">
						  <p><?php _e('Follow us:',EASY_SPLITTEST_TAB_DOMAIN); ?></p>
						  
							<a href="http://www.facebook.com/Prima.Posizione" target="_blank" title="Facebook" alt="Facebook"><img src="<?php echo EASY_SPLITTEST_TAB_URL."/img/"; ?>SetSize2424-facebook.png" alt="facebook"></a>
						  
							<a href="http://www.youtube.com/user/primaposizione" target="_blank" title="Youtube" alt="Youtube"><img src="<?php echo EASY_SPLITTEST_TAB_URL."/img/"; ?>SetSize2424-youtube.png" alt="youtube"></a>
						  
							<a href="http://twitter.com/#!/primaposizione" target="_blank" title="Twitter" alt="Twitter"><img src="<?php echo EASY_SPLITTEST_TAB_URL."/img/"; ?>SetSize2424-twitter.png" alt="twitter"></a>
						  
							<a href="http://www.linkedin.com/company/prima-posizione-srl" target="_blank" title="LinkedIn" alt="LinkedIn"><img src="<?php echo EASY_SPLITTEST_TAB_URL."/img/"; ?>SetSize2424-linkedin.png" alt="linkedin"></a>
						  
							<a href="https://plus.google.com/114773099956851509896/posts" target="_blank" title="Google Plus+" alt="Google Plus+"><img src="<?php echo EASY_SPLITTEST_TAB_URL."/img/"; ?>SetSize2424-googleplus.png" alt="googleplus"></a>
						  
						  <div class="clear"></div>
						  
					</div>
						</td>
						<td style="widht:5%" valign="top">&nbsp;</td>
					</tr>
				</table>

 </div>
		