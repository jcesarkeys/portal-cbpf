<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('syw.headerfilescache');

class TC_CSSFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';
		
		$theme = $params->get('theme', 'original');
		$variables[] = 'theme';
		
		// show
				
		$show_links = true;
		if ($params->get('lf1', 'none') == 'none' && $params->get('lf2', 'none') == 'none' && $params->get('lf3', 'none') == 'none' && $params->get('lf4', 'none') == 'none' && $params->get('lf5', 'none') == 'none') {
			$show_links = false;
		}
		$variables[] = 'show_links';
		
		$show_vcard = $params->get('s_v', false);
		$variables[] = 'show_vcard';
		
		$show_picture = $params->get('s_pic', true);
		$variables[] = 'show_picture';
		
		$show_text = true;
		$variables[] = 'show_text';
		
		// card
		
		$card_width = $params->get('card_w', 100);
		$variables[] = 'card_width';
		
		$card_width_unit = $params->get('card_w_u', '%');
		$variables[] = 'card_width_unit';
		
		$card_height = $params->get('card_h', '');
		$variables[] = 'card_height';
		
		$bgimage = $params->get('bgimage', '');
		$variables[] = 'bgimage';
		
		$bgcolor1 = trim($params->get('bgcolor1', ''));
		$variables[] = 'bgcolor1';
		
		$bgcolor2 = trim($params->get('bgcolor2', ''));
		$variables[] = 'bgcolor2';
				
		// picture
		
		$border_width = $params->get('border_w', 0);
		$variables[] = 'border_width';
		
		$border_radius = $params->get('border_r', 0);
		$variables[] = 'border_radius';
				
		$picture_width = $params->get('pic_w', 100);		
		$picture_height = $params->get('pic_h', 120);
		
		$picture_width = $picture_width - $border_width * 2;
		$variables[] = 'picture_width';
		
		$picture_height = $picture_height - $border_width * 2;
		$variables[] = 'picture_height';
		
		$pic_bgcolor = trim($params->get('pic_bgcolor', '')) != '' ? trim($params->get('pic_bgcolor', '')) : 'transparent';
		$variables[] = 'pic_bgcolor';
		
		$picture_shadow = $params->get('pic_shadow', false);
		$variables[] = 'picture_shadow';
		
		$picture_shadow_width = 0; // TODO add parameter
		if ($picture_shadow) {
			$picture_shadow_width = 8;
		}		
		$variables[] = 'picture_shadow_width';
		
		$hover_extra_margin = $params->get('pic_hover_m', 0);
		$variables[] = 'hover_extra_margin';
		
		$overflow = $params->get('overflow', false);
		$variables[] = 'overflow';
		
		// text
		
		$font_size = $params->get('font_s', '14');
		$variables[] = 'font_size';
		
		$fontcolor = trim($params->get('fontcolor', ''));
		$variables[] = 'fontcolor';
		
		$iconfont_size = $params->get('ifont_s', '1');
		$variables[] = 'iconfont_size';
		
		$iconfont_color = trim($params->get('ifont_c', '#000000'));
		$variables[] = 'iconfont_color';
		
		$label_width = $params->get('lbl_w', '0');
		$variables[] = 'label_width';
		
		$force_one_line = $params->get('force_one_line', true);
		$variables[] = 'force_one_line';
		
		// animation
		
		$animated = false;
		if ($params->get('carousel_config', 'none') != 'none') {
			$animated = true;
		}
		$variables[] = 'animated';
		
		$arrow_size = $params->get('arrowsize', 1);
		$variables[] = 'arrow_size';
		
		$arrow_offset = $params->get('arrowoffset', 0);
		$variables[] = 'arrow_offset';
		
		// calculated variables
		
		$border_radius_img = $border_radius;
		if ($border_width > 0 && $border_radius >= $border_width) {
			$border_radius_img -= $border_width;
		}
		$variables[] = 'border_radius_img';
		
		// calculate margins if card width is in %
		$margin_in_perc = 0;
		if ($card_width_unit == '%') {
			$cards_per_row = (int)(100 / $card_width);
			$left_for_margins = 100 - ($cards_per_row * $card_width);
			$margin_in_perc = $left_for_margins / ($cards_per_row * 2);
		}
		$variables[] = 'margin_in_perc';
		
		// set all necessary parameters
		$this->params = compact($variables);		
	}
	
	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);
	
		// 		if (function_exists('ob_gzhandler')) { // TODO not tested
		// 			ob_start('ob_gzhandler');
		// 		} else {
		ob_start();
		//		}
	
		// set the header
		$this->sendHttpHeaders('css');
		
		include 'themes/style.css.php';
		include 'themes/'.$theme.'/style.css.php';
		
		return $this->compress(ob_get_clean());
	}
	
}

class TC_JSFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';

		$card_width = $params->get('card_w', 100); // % : it is always in percentages when the caching occurs
		if ($card_width <= 0 || $card_width > 100) {
			$card_width = 100;
		}
		$variables[] = 'card_width';

		$min_width = trim($params->get('min_card_w', '')); // px : there is always a width when the caching occurs
		$variables[] = 'min_width';
		
		$max_width = trim($params->get('max_card_w', '')); // px
		if (empty($max_width)) {
			$max_width = '-1';
		}
		$variables[] = 'max_width';

		$margin_min_width = 3; // px
		$variables[] = 'margin_min_width';

		$margin_error = 1; // px
		$variables[] = 'margin_error';
			
		// set all necessary parameters
		$this->params = compact($variables);
	}

	public function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);

		// 		if (function_exists('ob_gzhandler')) { // not tested
		// 			ob_start('ob_gzhandler');
		// 		} else {
		ob_start();
		// 		}

		// set the header
		$this->sendHttpHeaders('js');

		echo 'jQuery(document).ready(function ($) { ';
		
			echo 'var person = $(".te_'.$suffix.' .person"); ';
			echo 'var personlist = $(".te_'.$suffix.' .personlist"); ';
			
			echo 'if (person != null) { ';
				echo 'resize_te_cards(); ';
			echo '} ';
			
			echo '$(window).resize(function() { ';
				echo 'if (person != null) { ';
					echo 'resize_te_cards(); ';
				echo '} ';
			echo '}); ';
		
			echo 'function resize_te_cards() { ';
		
				echo 'var container_width = personlist.width(); ';
		
				echo 'var cards_per_row = 1; ';
		
				echo 'var card_width = Math.floor(container_width * '.$card_width.' / 100); ';
		         
			    echo 'if (card_width < '.$min_width.') { ';
			    
			    	echo 'if (container_width < '.$min_width.') { ';
			    		echo 'card_width = container_width; ';
			    	echo '} else { ';
			    		echo 'card_width = '.$min_width.'; ';
			    	echo '} ';
			    	
		        echo '} else if ('.$max_width.' > 0 && card_width > '.$max_width.') { ';
		        	echo 'card_width = '.$max_width.'; ';
		        echo '} ';
		        
		        echo 'if ('.$card_width.' <= 50) { ';
			        echo 'cards_per_row = Math.floor(container_width / card_width); ';
			        
			        echo 'if (cards_per_row == 1) { ';
			        	echo 'if ('.$max_width.' < 0) { ';
			        		echo 'card_width = container_width; ';
			        	echo '} else { ';
			        		echo 'if (container_width > '.$max_width.') { ';  
			        			echo 'card_width = '.$max_width.'; ';
			        		echo '} else { ';
				        		echo 'card_width = container_width; ';
			        		echo '} ';
			        	echo '} ';
			        echo '} else { ';
			        	echo 'card_width = Math.floor(container_width / cards_per_row) - ('.$margin_min_width.' * cards_per_row); ';
			        	echo 'if ('.$max_width.' > 0 && card_width > '.$max_width.') { ';
			        		echo 'card_width = '.$max_width.'; ';
			        	echo '} ';
			        echo '} ';
			    echo '} else { '; // we can never have 2 cards on the same row			    	
			    	echo 'if ('.$max_width.' < 0) { ';
		        		echo 'card_width = container_width; ';
		        	echo '} else { ';
		        		echo 'if (container_width > '.$max_width.') { ';
		        			echo 'card_width = '.$max_width.'; ';	        		
		        		echo '} else { ';
			        		echo 'card_width = container_width; ';
		        		echo '} ';
		        	echo '} ';
			    echo '} ';
		        
		        echo 'var left_for_margins = container_width - (cards_per_row * card_width); ';
				echo 'var margin_width = Math.floor(left_for_margins / (cards_per_row * 2)) - '.$margin_error.'; ';        
		        
		        echo 'person.each(function() { ';
		            echo '$(this).width(card_width + "px"); ';
		            echo '$(this).css("margin-left", margin_width + "px"); ';
			        echo '$(this).css("margin-right", margin_width + "px"); ';
			        
			        echo 'if (card_width >= '.$min_width.') { ';
			        
			        	echo 'if ($(this).hasClass("pl")) { ';
		        			echo '$(this).addClass("picture_left"); '; 
		        			echo '$(this).removeClass("pl"); ';
		        			echo '$(this).removeClass("picture_top"); ';
		        		echo '} else if ($(this).hasClass("pr")) { ';
		        			echo '$(this).addClass("picture_right"); ';
		        			echo '$(this).removeClass("pr"); ';
		        			echo '$(this).removeClass("picture_top"); ';
		        		echo '} ';
						
						echo 'if ($(this).hasClass("gpl")) { ';
							echo '$(this).addClass("ghost_picture_left"); ';
							echo '$(this).removeClass("gpl"); ';
							echo '$(this).removeClass("ghost_picture_top"); ';
						echo '} else if ($(this).hasClass("gpr")) { ';
							echo '$(this).addClass("ghost_picture_right"); ';
							echo '$(this).removeClass("gpr"); ';
							echo '$(this).removeClass("ghost_picture_top"); ';
						echo '} ';
						
					echo '} else if (container_width < '.$min_width.') { ';
					
						echo 'if ($(this).hasClass("picture_left")) { ';
		        			echo '$(this).addClass("pl"); ';
		        			echo '$(this).removeClass("picture_left"); ';
		        			echo '$(this).addClass("picture_top"); ';
		        		echo '} else if ($(this).hasClass("picture_right")) { ';
		        			echo '$(this).addClass("pr"); ';
		        			echo '$(this).removeClass("picture_right"); ';
		        			echo '$(this).addClass("picture_top"); ';
		        		echo '} ';
			    		
			    		echo 'if ($(this).hasClass("ghost_picture_left")) { ';
			    			echo '$(this).addClass("gpl"); ';
			    			echo '$(this).removeClass("ghost_picture_left"); ';
			    			echo '$(this).addClass("ghost_picture_top"); ';
			    		echo '} else if ($(this).hasClass("ghost_picture_right")) { ';
			    			echo '$(this).addClass("gpr"); ';
			    			echo '$(this).removeClass("ghost_picture_right"); ';
			    			echo '$(this).addClass("ghost_picture_top"); ';
			    		echo '} ';
			    		
			    	echo '} ';
			        
		        echo '}); ';
			echo '} ';
			
		echo '}); ';
			
		return ob_get_clean();
	}
	
}

class TC_JSAnimationFileCache extends SYWHeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$suffix = $params->get('suffix');
		$variables[] = 'suffix';
		
		$card_width = $params->get('card_w', 100); // px
		$variables[] = 'card_width';
		
		$horizontal = false;
		if ($params->get('carousel_config', 'none') == 'h') {
			$horizontal = true;
		}
		$variables[] = 'horizontal';
		
		$visible_items = trim($params->get('visible_items', ''));
		if (!$horizontal && empty($visible_items)) {
			$visible_items = 1;
		}
		$variables[] = 'visible_items';
		
		$direction = 'left';
		if (!$horizontal) {
			$direction = 'up';
		}
		$variables[] = 'direction';
		
		$move_at_once = $params->get('moveatonce', 'all');
		if ($move_at_once == 'all') {
			$move_at_once = $visible_items;
		} else {
			$move_at_once = 1;
		}
		$variables[] = 'move_at_once';
		
		$show_arrows = false;
		if ($params->get('arrows', 'none') != 'none') {
			$show_arrows = true;
		}
		$variables[] = 'show_arrows';
		
		$auto = $params->get('auto', 1);
		$variables[] = 'auto';
		
		$speed = $params->get('speed', 1000);
		$variables[] = 'speed';
		
		$interval = $params->get('interval', 3000);
		$variables[] = 'interval';
			
		// set all necessary parameters
		$this->params = compact($variables);
	}

	public function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);

// 		if (function_exists('ob_gzhandler')) { // not tested
// 			ob_start('ob_gzhandler');
// 		} else {
 			ob_start();
// 		}

		// set the header
		$this->sendHttpHeaders('js');

		echo '(function($){';
			echo '$(window).load(function() {';
			
				echo '$(".te_'.$suffix.' .personlist").carouFredSel({';
				
					echo 'direction: "'.$direction.'",';
					if ($horizontal) {
						echo 'height: "auto",';
						echo 'width: "100%",';
					} else {
						echo 'height: "variable",';
						echo 'width: "100%",';
					}
						
					//echo 'padding: [0, 50],'; // does not work
						
					if ($show_arrows) {
						echo 'prev: "#prev_'.$suffix.'",';
						echo 'next: "#next_'.$suffix.'",';
					}
						
					echo 'items: {';
					if ($horizontal) {
						echo 'width: "'.$card_width.'px",';
						echo 'height: "100%",';
						if (empty($visible_items)) {
							echo 'visible: "variable"';
						} else {
							echo 'visible: {';
								echo 'min: 1,';
								echo 'max: '.$visible_items;
							echo '}';
						}
					} else {
						echo 'width: "variable",';
						echo 'height: "variable",';
						echo 'visible: '.$visible_items.',';
						echo 'minimum: 1';
					}
					echo '},';
						
					echo 'scroll: {';
					if (!empty($move_at_once)) {
						if ($move_at_once > 1) {
							echo 'items: {';
							echo 'visible: {';
							echo 'min: 1,';
							echo 'max: '.$move_at_once;
							echo '}';
							echo '},';
						} else {
							echo 'items: '.$move_at_once.',';
						}
					}
						echo 'fx: "scroll",';
						echo 'duration: '.$speed.',';
						echo 'pauseOnHover: true';
					echo '},';
						
					echo 'auto: {';
					if (!$auto) {
						echo 'play: false,';
					}
						echo 'timeoutDuration: '.$interval;
					echo '},';
						
					echo 'cookie: ".te_'.$suffix.'",';
						
					echo 'swipe: {';
						echo 'onTouch: true,';
						echo 'onMouse: true';
					echo '},';
					
					echo 'onCreate: function ($data) {';
						echo '$(".te_'.$suffix.' .personlist").trigger("updateSizes");';
					echo '}';
						
					echo '}, {';
						
					echo 'wrapper: {';
						echo 'element: "div",';
						echo 'classname: "tc_carousel_wrapper"';
					echo '}';
				
				echo '}).hide().fadeIn("slow");';
				
				echo '$(window).resize();'; // fixes centering of elements on first load
				//echo '$(".wl_carousel_wrapper").find("ul").trigger("updateSizes");'; does not solve the issue properly				
			
			echo '});';
		echo '})(jQuery);';
			
		return $this->compress(ob_get_clean(), false);
	}
	
}
