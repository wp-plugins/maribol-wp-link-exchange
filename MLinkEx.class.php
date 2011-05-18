<?php

	if(!class_exists('MlinkEx')){
		class MLinkEx{

			public $url; // url to parse
			public $find; // find this url on the above page
			
			/* get url html source */
			public function getContents(){
				return file_get_contents($this->url);
			}
			
			/* a preg match that gets all the links on parsed page */
			public function getUrls(){
				preg_match_all('|<a(.*)>(.*)</a>|isU', $this->getContents(), $data);
				return (is_array($data)) ? $data[0] : false;
			}
			
			/* this function cleans the url like: `http://www.example.com/` will result `example.com` */
			public function clearUrl($return){
				$return = str_replace(array('http://', 'https://'), '', $return);
				$return = (array)explode('/', $return);
				return $return[0];
			}
			
			/* find matched urls tyo your website */
			public function matchUrls(){
				$urls = array();
				foreach($this->getAtributes() as $id=>$link){
					if($this->clearUrl($link['href']) == $this->clearUrl($this->find)){
						$urls[] = $link;
					}
				}
				return $urls;
			}
			
			/* count all the external links found on parsed page */
			public function countExternalLinks(){
				$urls = array();
				foreach($this->getAtributes() as $id=>$link){
					if($this->clearUrl($link['href']) != $this->clearUrl($this->url)){
						$urls[] = $link;
					}
				}
				return $urls;
			}
			
			/* expand a link from `/index.php` to http://www.example.com/index.php */
			public function expandLink($links,$URI){

				preg_match("/^[^\?]+/",$URI,$match);

				$match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|","",$match[0]);
				$match = preg_replace("|/$|","",$match);
				$match_part = parse_url($match);
				$match_root =
				$match_part["scheme"]."://".$match_part["host"];

				$search = array( 	"|^http://".$this->clearUrl($this->url)."|i",
									"|^(\/)|i",
									"|^(?!http://)(?!mailto:)|i",
									"|/\./|",
									"|/[^\/]+/\.\./|"
								);

				$replace = array(	"",
									$match_root."/",
									$match."/",
									"/",
									"/"
								);

				$expandedLinks = preg_replace($search,$replace,$links);

				return $expandedLinks;
			}
			
			/* this is the function that gathers all the atributes send by $this->getUrls() */
			public function getAtributes(){
				$links = $this->getUrls();
				
				foreach($links as $id=>$link){
					$add = '';
					preg_match('/(rel\=\"(.*)\"|rel\=\'(.*)\')/isU', $link, $rel); 
					$add['rel'] = (isset($rel[2])) ? explode(' ', $rel[2]) : '' ;
					if($add['rel'] == ''){
						$add['rel'] = (isset($rel[3])) ? $rel[3] : '';
					}
					
					preg_match('/(target\=\"(.*)\"|target\=\'(.*)\')/isU', $link, $target); 
					$add['target'] = (isset($target[2])) ? $target[2] : '' ;
					if($add['target'] == ''){
						$add['target'] = (isset($target[3])) ? $target[3] : '';
					}
					
					preg_match('/(href\=\"(.*)\"|href\=\'(.*)\')/isU', $link, $href); 
					$add['href'] = (isset($href[2])) ? $this->expandLink($href[2], $this->url)  : '';
					if($add['href'] == ''){
						$add['href'] = (isset($href[3])) ? $this->expandLink($href[3], $this->url) : '';
					}
					
					preg_match('/(title\=\"(.*)\"|title\=\'(.*)\')/isU', $link, $title); 
					$add['title'] = (isset($title[2])) ? $title[2] : '' ;
					if($add['title'] == ''){
						$add['title'] = (isset($title[3])) ? $title[3] : '';
					}
					
					preg_match('|>(.*)<\/a>|isU', $link, $anchor); 
					$add['anchor'] = (isset($anchor[1])) ? $anchor[1] : '' ;
					
					if(isset($add['href'])){
						$data[$id] = $add;
					}
				}
				
				return $data;
				
			}

		}
	}

?>