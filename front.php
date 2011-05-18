<?php

	if(!function_exists('deposit_page_content')){
		function deposit_page_content($content) {
			if(strpos($content, '%mlinkex%') > 0){
				$deposit_page_content = loadAddForm();
				$content=str_ireplace('%mlinkex%',$deposit_page_content,$content);
			}
			return $content;
		}
	}
	add_filter("the_content", "deposit_page_content");
	
	if(!function_exists('loadAddForm')){
		function loadAddForm(){
			global $wpdb;
			if(!file_exists(TPL_FILE)){
				return '<b style="color:#cc0000">Plugin template file is missing:</b> '.PLUGIN_DIR.'/form.html';
			}else{
				$err = 0;
				require(PLUGIN_DIR.'/MLinkEx.class.php');
				$settings = mysql_fetch_object(mysql_query(' SELECT option_value FROM  '.$wpdb->prefix.'options WHERE option_name="mlinkex" LIMIT 1'));
				$settings = unserialize($settings->option_value);
				if($_POST['save'] != ''){
					$mlinkex = new MLinkEx();
					
					$anchor = $_POST['anchor'];
					$title = ($_POST['title'] == '') ? $_POST['anchor'] : $_POST['title'];
					$url = $_POST['url'];
					$email = $_POST['email'];
					
					if($anchor == ''){
						$result_msg = 'The anchor is required.<br />';
						$err = 1;
					}
					
					if($url == ''){
						$result_msg .= 'The url is required.<br />';
						$err = 1;
					}
					
					if($url != '' && !isUrl($url)){
						$result_msg .= 'The url is invalid.<br />';
						$err = 1;
					}
					
					if($url != '' && mysql_num_rows(mysql_query(' SELECT id FROM '.$wpdb->prefix.'mlinkex WHERE url="'.'http://'.$mlinkex->clearUrl($url).'"'))){
						$result_msg .= 'This link is already in our database.<br />';
						$err = 1;
					}
					
					if($email == ''){
						$result_msg .= 'The email is required.<br />';
						$err = 1;
					}
					
					if($email != '' && !isEmail($email)){
						$result_msg .= 'The email is invalid.<br />';
						$err = 1;
					}
					
					if($err == 0){
						require(PLUGIN_DIR.'/googlepr.php');
						
						$mlinkex->find = $settings['url'];
						$mlinkex->url = 'http://'.$mlinkex->clearUrl($url);
						$pagerank = getpagerank($mlinkex->clearUrl($url));
						
						$matchUrls = $mlinkex->matchUrls();
						$countExternalLinks = (int)@count($mlinkex->countExternalLinks());
						
						if($settings['backlink'] == 1){
							if(is_array($matchUrls[0])){ 
								if(is_array($matchUrls[0]['rel']) && in_array('nofollow', $matchUrls[0]['rel']) && $settings['nofollow'] == 0){
									$result_msg .= 'We\'ve found a link to our page. But we don\'t accept nofollow links.';
									$err = 1;
								}elseif($settings['other_anchor'] == 0 && $matchUrls[0]['anchor'] != $settings['anchor']){
									$result_msg .= 'The link on your page doesn\'t have our anchor.';
									$err = 1;
								}elseif($settings['other_title'] == 0 && $matchUrls[0]['title'] != $settings['title']){
									$result_msg .= 'The link on your page doesn\'t have our title.';
									$err = 1;
								}elseif($settings['pr_min'] > 0 && $pagerank < $settings['pr_min']){
									$result_msg .= 'Sorry, your pagerank is to low. We require minimum pagerank '.$settings['pr_min'].'.';
									$err = 1;
								}elseif($settings['max_ex_links'] > 0 && $countExternalLinks > $settings['max_ex_links']){
									$result_msg .= 'You have to many external links on your site ('.$countExternalLinks.'). We allow only '.$settings['max_ex_links'].' max. external links';
									$err = 1;
								}
							}else{
								$err = 1;
								$result_msg .= 'We couldn\'t find any link to us on your page.';
							}
						}
						
						if($err == 0){
							$totalLinks = (int)@count($mlinkex->getAtributes());
							mysql_query('
							INSERT INTO '.$wpdb->prefix.'mlinkex
							(`anchor`,`title`,`url`,`add_nofollow`,`external_links`,`total_links`,`pagerank`,`date`,`email`,`status`) VALUES
							("'.$anchor.'","'.$title.'","'.'http://'.$mlinkex->clearUrl($url).'","0","'.$countExternalLinks.'","'.$totalLinks.'","'.$pagerank.'","'.time().'","'.$email.'","1")');
							
							if(file_exists(PLUGIN_DIR.'/link_added.html')){
								$result_msg = @file_get_contents(PLUGIN_DIR.'/link_added.html');
							}else{
								$result_msg = '<b style="color:green;">Your link has been submited. Add another one?</b>';
							}
							
							$anchor = '';
							$title = '';
							$url = '';
							$email = '';
						}
						
					}
					
				}
				
				$tpl = @file_get_contents(TPL_FILE);
				$tpl = str_replace('%result_msg%', ($err == 1) ? '<b style="color:#cc0000;">Error</b><br />'.$result_msg : $result_msg, $tpl);
				$tpl = str_replace('%anchor%', $anchor, $tpl);
				$tpl = str_replace('%title%', $title, $tpl);
				$tpl = str_replace('%url%', $url, $tpl);
				$tpl = str_replace('%email%', $email, $tpl);
				
				$tpl = str_replace('%myanchor%', $settings['anchor'], $tpl);
				$tpl = str_replace('%mytitle%', $settings['title'], $tpl);
				$tpl = str_replace('%myurl%', $settings['url'], $tpl);
				
				
				return $tpl;
			}
		}
	}
	
	if(!function_exists('isUrl')){
		function isUrl($url){
			  return preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url);		 
		}
	}
	
	if(!function_exists('isEmail')){
		function isEmail($string){
			  return preg_match('([a-zA-Z0-9\._-]+[@][a-zA-Z0-9\._-]+\.[a-zA-Z]{2,5})', $string);		 
		}
	}
	
?>