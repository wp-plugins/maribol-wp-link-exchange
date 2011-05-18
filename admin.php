<?php

	add_action('admin_menu', 'mt_add_pages');
	
	if(!function_exists('isInstalled')){
		function isInstalled(){
			global $dbdata,$wpdb;
			$dbdata = @mysql_fetch_object(@mysql_query(' SELECT * FROM '.$wpdb->prefix.'options WHERE  option_name="mlinkex" LIMIT 1 '));
			if($dbdata->option_name != ''){
				return true;
			}else{
				return false;
			}
		}
	}
		
	if(!function_exists('mt_add_pages')){
		function mt_add_pages() {
			global $dbdata;
			add_menu_page('Link Exchange', 'Link Exchange', 'administrator', 'mlinkex', 'mlinkex', plugins_url('maribol-wp-link-exchange/link.png'));

			if(isInstalled()){
				add_submenu_page('mlinkex', 'Links', 'Links', 'administrator', 'mlinkex_links', 'mlinkex_links');
				add_submenu_page('mlinkex', 'Add link', 'Add link', 'administrator', 'mlinkex_links&action=add', 'mlinkex_links');
				add_submenu_page('mlinkex', 'Options', 'Options', 'administrator', 'mlinkex_options', 'mlinkex_options');
				add_submenu_page('mlinkex', 'Template settings', 'Template settings', 'administrator', 'mlinkex_template', 'mlinkex_template');
				add_submenu_page('mlinkex', 'Donate', '<b style="color:#cc0000;">Donate</b>', 'administrator', 'donate', 'donate');
			}else{
				add_submenu_page('mlinkex', 'Install', 'Install', 'administrator', 'mlinkex_install', 'mlinkex_install');
			}
			
			remove_submenu_page('mlinkex','mlinkex');
		}
	}
	
	if(!function_exists('mlinkex_links')){
		function mlinkex_links(){
		global $wpdb;
		// echo $wpdb->prefix;
			if(isInstalled()){
				echo'<div class="wrap">';
				if($_GET['action']=='add'){
					if($_POST['save'] != ''){
						if($_POST['anchor'] == '' || $_POST['title'] == '' || $_POST['url'] == '' || $_POST['add_nofollow'] == ''){
							$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All fields are required.</b></p></div>';
						}else{
							$checkExist = mysql_num_rows(mysql_query(' SELECT id FROM '.$wpdb->prefix.'mlinkex WHERE url LIKE "%'.mysql_real_escape_string(clearUrl($_POST['url'])).'%" LIMIT 1'));
							if($checkExist > 0){
								$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>This link already exists.</b></p></div>';
							}else{
								mysql_query('INSERT INTO '.$wpdb->prefix.'mlinkex (`anchor`,`title`,`url`,`add_nofollow`) VALUES ("'.mysql_real_escape_string($_POST['anchor']).'","'.mysql_real_escape_string($_POST['title']).'","'.mysql_real_escape_string($_POST['url']).'","'.mysql_real_escape_string($_POST['add_nofollow']).'")') or die(mysql_error());
								unset($_POST);
								$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>Your link has been added.</b></p></div>';
							}
						}
					}
					?>
					<h2>Add link - Maribol Link Exchange <a href="admin.php?page=mlinkex_links" class="button add-new-h2">Back to links</a></h2>
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Add new link</label></h3>
									<div class="inside">
										<?php echo $msg;?><br />
										<form action="" method="post">
										<table class="editform" cellspacing="2" cellpadding="5" width="500">
											<tbody>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="anchor">Achor:</label></th>
													<td><input value="<?php echo $_POST['anchor']; ?>" type="text" name="anchor" class="code" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="title">Title:</label></th>
													<td><input value="<?php echo $_POST['title']; ?>" type="text" name="title" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Address:</label></th>
													<td><input value="<?php echo $_POST['url']; ?>" type="text" name="url" size="30" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add link with rel="nofollow"</label></th>
													<td>
													<select name="add_nofollow">
														<option <?php echo ($_POST['add_nofollow'] == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($_POST['add_nofollow'] == 1) ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
											</tbody>
										</table>
										<br />
										<input type="submit" name="save" class="button-primary" value="Add link">
									</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?
				}elseif($_GET['action']=='edit'){
					if($_POST['save'] != ''){
						if($_POST['anchor'] == '' || $_POST['title'] == '' || $_POST['url'] == '' || $_POST['add_nofollow'] == ''){
							$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All fields are required.</b></p></div>';
						}else{
							$checkExist = mysql_num_rows(mysql_query(' SELECT id FROM '.$wpdb->prefix.'mlinkex WHERE url LIKE "%'.mysql_real_escape_string(clearUrl($_POST['url'])).'%" AND id != "'.(int)$_GET['id'].'" LIMIT 1'));
							if($checkExist > 0){
								$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>This link already exists.</b></p></div>';
							}else{
								mysql_query(' UPDATE '.$wpdb->prefix.'mlinkex SET status="'.mysql_real_escape_string($_POST['status']).'", anchor="'.mysql_real_escape_string($_POST['anchor']).'", title="'.mysql_real_escape_string($_POST['title']).'", url="'.mysql_real_escape_string($_POST['url']).'", add_nofollow="'.mysql_real_escape_string($_POST['add_nofollow']).'" WHERE id="'.(int)$_GET['id'].'"');
								$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>Your link has been updated.</b></p></div>';
							}
						}
					}
					$item = mysql_fetch_object(mysql_query(' SELECT * FROM '.$wpdb->prefix.'mlinkex WHERE id="'.(int)$_GET['id'].'"'));
					?>
					<h2>Add link - Maribol Link Exchange <a href="admin.php?page=mlinkex_links" class="button add-new-h2">Back to links</a></h2>
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Add new link</label></h3>
									<div class="inside">
										<?php echo $msg;?><br />
										<form action="" method="post">
										<table class="editform" cellspacing="2" cellpadding="5" width="500">
											<tbody>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Status</label></th>
													<td>
													<select name="status">
														<option <?php echo ($item->status == 0) ? 'selected' : '';?> value="0">Inactive</option>
														<option <?php echo ($item->status == 1) ? 'selected' : '';?> value="1">Active</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="anchor">Achor:</label></th>
													<td><input value="<?php echo $item->anchor; ?>" type="text" name="anchor" class="code" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="title">Title:</label></th>
													<td><input value="<?php echo $item->title; ?>" type="text" name="title" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Address:</label></th>
													<td><input value="<?php echo $item->url; ?>" type="text" name="url" size="30" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add link with rel="nofollow"</label></th>
													<td>
													<select name="add_nofollow">
														<option <?php echo ($item->add_nofollow == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($item->add_nofollow == 1) ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">External links:</label></th>
													<td><?php echo $item->external_links; ?></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Internal links:</label></th>
													<td><?php echo ((int)$item->total_links - (int)$item->external_links); ?></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Pagerank:</label></th>
													<td><?php echo $item->pagerank; ?></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Date added:</label></th>
													<td><?php echo @date('d-m-Y', $item->date); ?></td>
												</tr>
											</tbody>
										</table>
										<br />
										<input type="submit" name="save" class="button-primary" value="Edit link">
									</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?
				}else{
					?>
					<h2>Links - Maribol Link Exchange <a href="admin.php?page=mlinkex_links&action=add" class="button add-new-h2">Add New</a></h2>
					<?php
					if($_POST['doaction'] != ''){
						if(is_array($_POST['link'])){
							if($_POST['action']=='delete'){
								foreach($_POST['link'] as $linkid){
									mysql_query(' DELETE FROM '.$wpdb->prefix.'mlinkex WHERE id="'.(int)$linkid.'"');
								}
								echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All selected links were deleted.</b></p></div><br />';
							}elseif($_POST['action']=='activate'){
								foreach($_POST['link'] as $linkid){
									mysql_query(' UPDATE '.$wpdb->prefix.'mlinkex SET status="1" WHERE id="'.(int)$linkid.'"');
								}
								echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All selected links are now active</b></p></div><br />';
							}elseif($_POST['action']=='dezactivate'){
								foreach($_POST['link'] as $linkid){
									mysql_query(' UPDATE '.$wpdb->prefix.'mlinkex SET status="0" WHERE id="'.(int)$linkid.'"');
								}
								echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All selected links are now inactive</b></p></div><br />';
							}
						}else{
							echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>No links selected</b></p></div><br />';
						}
					}elseif($_GET['action']=='delete'){
						mysql_query(' DELETE FROM '.$wpdb->prefix.'mlinkex WHERE id="'.(int)$_GET['id'].'"');
						echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>Link deleted.</b></p></div><br />';
					}elseif($_GET['action']=='status'){
						mysql_query(' UPDATE '.$wpdb->prefix.'mlinkex SET status="'.(int)$_GET['value'].'" WHERE id="'.(int)$_GET['id'].'"');
						echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>Link status changed.</b></p></div><br />';
					}
					if($_GET['installed'] == 'true'){
						echo '<div style="padding:0 0 0 10px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>Maribol WP Link Exchange has been installed.</b></p></div><br />';
					}
					?>
					<form action="admin.php?page=mlinkex_links" method="post">
						<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
							<thead>
							<tr>
								<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
								<th scope="col" class="manage-column column-name sortable desc" style=""><span>Achor</span></th>
								<th scope="col" class="manage-column column-url sortable desc" style=""><span>URL</span></th>
								<th scope="col" class="manage-column" style="width:110px;">External Links</th>
								<th scope="col" class="manage-column" style="width:110px;">Internal Links</th>
								<th scope="col" class="manage-column" style="width:80px;"><span>Pagerank</span></th>
								<th scope="col" class="manage-column" style="width:100px;"><span>Date added</span></th>
								<th scope="col" class="manage-column" style="width:70px;"><span>Status</span></th>
							</tr>
							</thead>
							<tbody id="the-list">
								<?php
									$i = 0;
									$sql = mysql_query(' SELECT * FROM '.$wpdb->prefix.'mlinkex ORDER BY date DESC');
									while($item = mysql_fetch_object($sql)){
								?>
								<tr id="link-1" valign="middle"<?php echo ($i % 2) ? '' : 'class="alternate"';?>>
									<th scope="row" class="check-column"><input type="checkbox" name="link[]" value="<?php echo $item->id;?>"></th>
									<td class="column-name">
										<strong><?php echo $item->anchor;?></strong>
										<div class="row-actions">
											<span class="edit"><a href="admin.php?page=mlinkex_links&action=edit&id=<?php echo $item->id;?>">Edit</a> | </span>
											<span class="delete"><a class="submitdelete" href="admin.php?page=mlinkex_links&action=delete&id=<?php echo $item->id;?>" onclick="if ( confirm( 'You are about to delete this link \'Cancel\' to stop, \'OK\' to delete.' ) ) { return true;}return false;">Delete</a> | </span>
											<span><a href="admin.php?page=mlinkex_links&action=status&value=<?php echo ($item->status == 1) ? '0' : '1';?>&id=<?php echo $item->id;?>"><?php echo ($item->status == 1) ? 'Dezactivate' : 'Activate';?></a></span>
										</div>
									
									</td>
									<td class="column-url"><a href="<?php echo $item->url;?>" title="Visit site" target="_blank"><?php echo $item->url;?></a></td>
									<td><center><?php echo $item->external_links;?></center></td>
									<td><center><?php echo ((int)$item->total_links - (int)$item->external_links);?></center></td>
									<td><center><?php echo $item->pagerank;?></center></td>
									<td><center><?php echo @date('d-m-Y', $item->date);?></center></td>
									<td><center><?php echo ($item->status == '1') ? '<b style="color:green;">Active</b>' : '<b style="color:#cc0000;">Inactive</b>';?></center></td>
								</tr>
								<?php $i++;}?>
							</tbody>
						</table>
						<?php if($i==0){?><div style="padding:5px;background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">There are no links in database</div><br /><?php }?>
						<div class="alignleft actions">
							<select name="action">
								<option value="dezactivate">Dezactivate selected</option>
								<option value="activate">Activate selected</option>
								<option value="delete">Delete selected</option>
							</select>
							<input type="submit" name="doaction" class="button-secondary action" value="Apply">
						</div>
					</form>
					<?
				}
				echo'</div>';
			}
		}
	}

	if(!function_exists('mlinkex_options')){
		function mlinkex_options() {
			global $wpdb;
			if(isInstalled()){
				$msg = '';
				if($_POST['save'] != ''){
					if($_POST['anchor'] == '' || $_POST['title'] == '' || $_POST['url'] == '' || $_POST['add_nofollow'] == '' || $_POST['nofollow'] == '' || $_POST['other_anchor'] == '' || $_POST['other_title'] == '' || $_POST['max_ex_links'] == '' || $_POST['add_link'] == ''){
						$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All fields are required.</b></p></div><br />';
					}else{
						$postdata = array(
							'anchor'=> $_POST['anchor'],
							'title'=> $_POST['title'],
							'url'=> $_POST['url'],
							'backlink'=> $_POST['backlink'],
							'add_nofollow'=> $_POST['add_nofollow'],
							'nofollow'=> $_POST['nofollow'],
							'other_anchor'=> $_POST['other_anchor'],
							'other_title'=> $_POST['other_title'],
							'pr_min'=> $_POST['pr_min'],
							'max_ex_links'=> $_POST['max_ex_links'],
							'add_link'=> $_POST['add_link']
						);
						mysql_query(' UPDATE '.$wpdb->prefix.'options SET option_value="'.mysql_real_escape_string(serialize($postdata)).'" WHERE option_name="mlinkex"');
						$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>You have successfully updated the options</b></p></div><br />';

					}
				}
				$options = mysql_fetch_object(mysql_query(' SELECT * FROM '.$wpdb->prefix.'options WHERE option_name="mlinkex" LIMIT 1'));
				$option = @unserialize($options->option_value);
				// print_r($option);
				
				?>
				<div class="wrap">
					<h2>Options - Maribol Link Exchange</h2>
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Options</label></h3>
									<div class="inside">
										<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p>Please read the <b>read me file</b> first to understand all the fields.</p></div><br />
										<?php echo $msg;?>
										<form action="" method="post">
										<table class="editform" cellspacing="2" cellpadding="5" width="500">
											<tbody>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="anchor">Achor:</label></th>
													<td><input value="<?php echo ($option['anchor'] != '') ? $option['anchor'] : bloginfo( 'name' ); ?>" type="text" name="anchor" class="code" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="title">Title:</label></th>
													<td><input value="<?php echo ($option['title'] != '') ? $option['title'] : bloginfo( 'name' ); ?>" type="text" name="title" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Address:</label></th>
													<td><input value="<?php echo ($option['url'] != '') ? $option['url'] : bloginfo( 'url' ); ?>" type="text" name="url" size="30" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Backlink required?</label></th>
													<td>
													<select name="backlink">
														<option <?php echo ($_POST['backlink'] == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($_POST['backlink'] == 1) ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add links with rel="nofollow"</label></th>
													<td>
													<select name="add_nofollow">
														<option <?php echo ($option['add_nofollow'] == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($option['add_nofollow'] == 1) ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow nofollow links?</label></th>
													<td>
													<input type="radio" name="nofollow" value="0" <?php echo ($option['nofollow']=='0'||$option['nofollow']=='') ? 'checked' : '';?>>
													No
													<input type="radio" name="nofollow" value="1" <?php echo ($option['nofollow']=='1') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow other anchor?</label></th>
													<td>
													<input type="radio" name="other_anchor" value="0" <?php echo ($option['other_anchor']=='0'||$option['other_anchor']=='') ? 'checked' : '';?>>
													No
													<input type="radio" name="other_anchor" value="1" <?php echo ($option['other_anchor']=='1') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow other title?</label></th>
													<td>
													<input type="radio" name="other_title" value="0" <?php echo ($option['other_title']=='0') ? 'checked' : '';?>>
													No
													<input type="radio" name="other_title" value="1" <?php echo ($option['other_title']=='1'||$option['other_title']=='') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Minimum PageRank</label></th>
													<td>
													<select name="pr_min">
														<?php
														for($i=0;$i<=10;$i++){
															$selected = ($option['pr_min'] == $i) ? 'selected' : '';
															echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
														}
														?>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Max. external links</label></th>
													<td>
													<input type="text" name="max_ex_links" size="30" class="code" tabindex="1" value="<?php echo ($option['max_ex_links'] != '') ? $option['max_ex_links'] : '0';?>" style="width:100px;">
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add link page</label></th>
													<td>
													<input type="text" name="add_link" class="code" tabindex="1" value="<?php echo ($option['add_link'] != '') ? $option['add_link'] : ''; ?>" style="width:300px;">
													</td>
												</tr>
											</tbody>
										</table>
										<br />
										<input type="submit" name="save" class="button-primary" value="Update Options">
									</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?
			}
		}
	}

	if(!function_exists('mlinkex_template')){
		function mlinkex_template() {
			global $wpdb;
			if(isInstalled()){
				
				if($_POST['save'] != ''){
				
					$fh_form = @fopen(PLUGIN_DIR.'/form.html', 'w');
					fwrite($fh_form, stripslashes($_POST['form_html']));
					fclose($fh_form);
					
					$fh_link_added = @fopen(PLUGIN_DIR.'/link_added.html', 'w');
					fwrite($fh_link_added, stripslashes($_POST['link_added_html']));
					fclose($fh_link_added);
					
					$fh_links_html = @fopen(PLUGIN_DIR.'/links.html', 'w');
					fwrite($fh_links_html, stripslashes($_POST['links_html']));
					fclose($fh_links_html);
					
					$fh_link_html = @fopen(PLUGIN_DIR.'/link.html', 'w');
					fwrite($fh_link_html, stripslashes($_POST['link_html']));
					fclose($fh_link_html);
					
					
					$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;padding:5px;">Templates saved.</div><br />';
				}
			
				$form_html = @file_get_contents(PLUGIN_DIR.'/form.html');
				$link_added_html = @file_get_contents(PLUGIN_DIR.'/link_added.html');
				$links_html = @file_get_contents(PLUGIN_DIR.'/links.html');
				$link_html = @file_get_contents(PLUGIN_DIR.'/link.html');

			?>
			<form action="" method="post">
				<div class="wrap">
					<h2>Template settings - Maribol Link Exchange</h2>
					<?php echo $msg;?>
					<div id="poststuff" class="metabox-holder has-right-sidebar alignleft" style="width:510px;height:200px;">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Anchor Template</label></h3>
									<div class="inside">
										<textarea style="width:100%;height:100px;" name="link_html"><?php echo htmlentities($link_html);?></textarea>
										<br />
										<b>Tags:</b><br />
										<em>%url%</em> - URL Location<br />
										<em>%title%</em> - Link Title<br />
										<em>%rel%</em> - Link Relationship, if it has special relationship<br />
										<em>%anchor%</em> - Anchor name
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="poststuff" class="metabox-holder has-right-sidebar alignleft" style="height:200px;">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Link Added Message</label></h3>
									<div class="inside">
										<textarea style="width:100%;height:100px;" name="link_added_html"><?php echo htmlentities($link_added_html);?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="poststuff" class="metabox-holder has-right-sidebar alignleft" style="width:510px;">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Form Template</label></h3>
									<div class="inside">
										<textarea style="width:100%;height:400px;" name="form_html"><?php echo htmlentities($form_html);?></textarea>
										<br />
										<b>Tags:</b><br />
										<em>%myurl%</em> - It's replaced with the url from your options<br />
										<em>%mytitle%</em> - It's replaced with the title from your options<br />
										<em>%mytitle%</em> - It's replaced with the title from your options<br />
										<em>%myanchor%</em> - It's replaced with the anchor from your options<br />
										<em>%result_msg%</em> - The result message from <b>Link Added Message</b> or the error message<br />
										<em>%anchor%</em> - User submited anchor<br />
										<em>%title%</em> - User submited title<br />
										<em>%url%</em> - User submited url<br />
										<em>%email%</em> - User submited email<br />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="poststuff" class="metabox-holder has-right-sidebar alignleft">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Links template</label></h3>
									<div class="inside">
										<textarea style="width:100%;height:400px;" name="links_html"><?php echo htmlentities($links_html);?></textarea>
										<br />
										<b>Tags:</b><br />
										<em>%links%</em> - Here are the links being displayed<br />
										<em>%add_link%</em> - Contains a link to the link exchange page/form
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				<input type="submit" name="save" class="button-primary" value="Save templates"><br /><br />
			</form>
				<?
			}
		}
	}
	if(!function_exists('mlinkex_install')){
		function mlinkex_install() {
			global $wpdb;
			if(!isInstalled()){
				$msg = '';
				if($_POST['save'] != ''){
					if($_POST['anchor'] == '' || $_POST['title'] == '' || $_POST['url'] == '' || $_POST['backlink'] == '' || $_POST['add_nofollow'] == '' || $_POST['nofollow'] == '' || $_POST['other_anchor'] == '' || $_POST['other_title'] == '' || $_POST['max_ex_links'] == ''){
						$msg = '<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p><b>All fields are required.</b></p></div><br />';
					}else{
						$postdata = array(
							'anchor'=> $_POST['anchor'],
							'title'=> $_POST['title'],
							'url'=> $_POST['url'],
							'backlink'=> $_POST['backlink'],
							'add_nofollow'=> $_POST['add_nofollow'],
							'nofollow'=> $_POST['nofollow'],
							'other_anchor'=> $_POST['other_anchor'],
							'other_title'=> $_POST['other_title'],
							'pr_min'=> $_POST['pr_min'],
							'max_ex_links'=> $_POST['max_ex_links'],
							'add_link'=> $_POST['add_link']
						);
						mysql_query('INSERT INTO '.$wpdb->prefix.'options (`option_name`,`option_value`,`autoload`) VALUES ("mlinkex","'.mysql_real_escape_string(serialize($postdata)).'","no")') or die(mysql_error());
						mysql_query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mlinkex` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `anchor` varchar(200) NOT NULL,  `title` varchar(200) NOT NULL,  `url` varchar(200) NOT NULL,  `add_nofollow` enum('0','1') NOT NULL DEFAULT '0',  `external_links` int(11) NOT NULL,  `total_links` int(11) NOT NULL,  `pagerank` int(2) NOT NULL,  `date` varchar(30) NOT NULL,  `email` varchar(100) NOT NULL,  `status` enum('0','1') NOT NULL DEFAULT '1',  PRIMARY KEY (`id`))") or die(mysql_error());
						// mysql_query("CREATE TABLE `".$wpdb->prefix."mlinkex` (`id` INT NOT NULL AUTO_INCREMENT, `anchor` VARCHAR(200) NOT NULL, `title` VARCHAR(200) NOT NULL, `url` VARCHAR(200) NOT NULL, `external_links` INT(11) NOT NULL, `total_links` INT(11) NOT NULL, `pagerank` INT(2) NOT NULL, `date` VARCHAR(30) NOT NULL, `status` ENUM('0','1') NOT NULL DEFAULT '1', PRIMARY KEY (`id`));") or die(mysql_error());
						echo '<meta http-equiv="refresh" content="0;url=admin.php?page=mlinkex_links&installed=true" />';
					}
				}
				?>
				<div class="wrap">
					<h2>Install Maribol Link Exchange</h2>
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="post-body">
							<div id="post-body-content" style="width:500px;">
								<div id="linkxfndiv" class="stuffbox" style="background:#fff;">
									<h3><label for="link_url">Options</label></h3>
									<div class="inside">
										<div style="background:lightYellow;border:1px solid #E6DB55;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><p>Please read the <b>read me file</b> first to understand all the fields.</p></div><br />
										<?php echo $msg;?>
										<form action="" method="post">
										<table class="editform" cellspacing="2" cellpadding="5" width="500">
											<tbody>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="anchor">Achor:</label></th>
													<td><input value="<?php echo ($_POST['anchor'] != '') ? $_POST['anchor'] : bloginfo( 'name' ); ?>" type="text" name="anchor" class="code" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="title">Title:</label></th>
													<td><input value="<?php echo ($_POST['title'] != '') ? $_POST['title'] : bloginfo( 'name' ); ?>" type="text" name="title" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="url">Address:</label></th>
													<td><input value="<?php echo ($_POST['url'] != '') ? $_POST['url'] : bloginfo( 'url' ); ?>" type="text" name="url" size="30" class="code" tabindex="1" value="" style="width:300px;"></td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Backlink required?</label></th>
													<td>
													<select name="backlink">
														<option <?php echo ($_POST['backlink'] == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($_POST['backlink'] == 1 || $_POST['backlink'] == '') ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add links with rel="nofollow"</label></th>
													<td>
													<select name="add_nofollow">
														<option <?php echo ($_POST['add_nofollow'] == 0) ? 'selected' : '';?> value="0">No</option>
														<option <?php echo ($_POST['add_nofollow'] == 1) ? 'selected' : '';?> value="1">Yes</option>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow nofollow links?</label></th>
													<td>
													<input type="radio" name="nofollow" value="0" <?php echo ($_POST['nofollow']=='0'||$_POST['nofollow']=='') ? 'checked' : '';?>>
													No
													<input type="radio" name="nofollow" value="1" <?php echo ($_POST['nofollow']=='1') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow other anchor?</label></th>
													<td>
													<input type="radio" name="other_anchor" value="0" <?php echo ($_POST['other_anchor']=='0'||$_POST['other_anchor']=='') ? 'checked' : '';?>>
													No
													<input type="radio" name="other_anchor" value="1" <?php echo ($_POST['other_anchor']=='1') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Allow other title?</label></th>
													<td>
													<input type="radio" name="other_title" value="0" <?php echo ($_POST['other_title']=='0') ? 'checked' : '';?>>
													No
													<input type="radio" name="other_title" value="1" <?php echo ($_POST['other_title']=='1'||$_POST['other_title']=='') ? 'checked' : '';?>>
													Yes
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Minimum PageRank</label></th>
													<td>
													<select name="pr_min">
														<?php
														for($i=0;$i<=10;$i++){
															$selected = ($_POST['pr_min'] == $i) ? 'selected' : '';
															echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
														}
														?>
													</select>
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Max. external links</label></th>
													<td>
													<input type="text" name="max_ex_links" size="30" class="code" tabindex="1" value="<?php echo ($_POST['max_ex_links'] != '') ? $_POST['max_ex_links'] : '0';?>" style="width:100px;">
													</td>
												</tr>
												<tr>
													<th style="height:25px;width: 150px;text-align:left;" scope="row"><label for="nofollow">Add link page</label></th>
													<td>
													<input type="text" name="add_link" class="code" tabindex="1" value="<?php echo ($_POST['add_link'] != '') ? $_POST['add_link'] : ''; ?>" style="width:300px;">
													</td>
												</tr>
											</tbody>
										</table>
										<br />
										<input type="submit" name="save" class="button-primary" value="Install">
									</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?
			}
		}
	}
	
	if(!function_exists('clearUrl')){
		function clearUrl($return){
			$return = str_replace(array('http://', 'https://', 'www'), '', $return);
			$return = (array)explode('/', $return);
			return $return[0];
		}
	}
	
	if(!function_exists('donate')){
		function donate(){
			echo'<div class="wrap"><h2>Please wait...</h2></div>';
			echo'<script>window.location="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=83LGSR69WZAEY";</script>';
		}
	}
	
?>