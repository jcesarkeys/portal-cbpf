<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

/**
 *
 * @author Olivier Buisard
 *
 * for Joomla 3+ ONLY
 *
 * field parameters
 * 
 * icons: coma separated transition names (no SYWicon- prefix)
 * icongroups: coma separated transition groups
 * emptyicon: the icon to show as default
 * buttonlabel: the label for the default button
 * buttonrole: [clear|default] 
 * help: help field
 * icomoon: add icomoon icons
 * editable: see the input field
 * 
 */
class JFormFieldSYWIconPicker extends JFormField 
{		
	public $type = 'SYWIconPicker';
	
	protected $icons;
	protected $icongroups;
	protected $emptyicon;
	protected $buttonlabel;
	protected $buttonrole;
	protected $help;
	protected $icomoon;
	protected $editable;
	
	static $icongrouplist = array('communications', 'equipment', 'transportation', 'location', 'social', 'agenda', 'finances', 'files', 'systems', 'accessibility', 'media', 'other');	
	static $li_icons = '';
		
	static function getIconGroup($icongroup) 
	{
		$icons = array();
		
		switch ($icongroup) {
			case 'communications':
				$icons[] = 'email';
				$icons[] = 'mail_outline';
				$icons[] = 'drafts';
				$icons[] = 'inbox2';				
				$icons[] = 'dialpad';
				$icons[] = 'phone-android';
				$icons[] = 'phone-iphone';
				$icons[] = 'phone';
				$icons[] = 'call';
				$icons[] = 'phone_forwarded';
				$icons[] = 'phone_in_talk';
				$icons[] = 'call_end';
				$icons[] = 'skype';
				$icons[] = 'fax';
				$icons[] = 'record_voice_over';
				$icons[] = 'voicemail';
				$icons[] = 'messenger';
				$icons[] = 'textsms';
				$icons[] = 'chat2';
				$icons[] = 'question_answer';
				break;
			case 'equipment':
				$icons[] = 'computer';
				$icons[] = 'desktop-windows';
				$icons[] = 'desktop_mac';
				$icons[] = 'tablet-android';
				$icons[] = 'tv';
				$icons[] = 'keyboard2';
				$icons[] = 'keyboard-voice';
				$icons[] = 'mouse2';
				$icons[] = 'watch';
				$icons[] = 'camera';
				$icons[] = 'camera-alt';
				$icons[] = 'devices_other';
				$icons[] = 'videogame_asset';				
				$icons[] = 'router';
				$icons[] = 'wifi';
				$icons[] = 'bluetooth';
				break;	
			case 'transportation':
				$icons[] = 'directions-bike';	
				$icons[] = 'directions-bus';	
				$icons[] = 'directions-car';	
				$icons[] = 'directions-ferry';	
				$icons[] = 'directions-subway';	
				$icons[] = 'directions-train';	
				$icons[] = 'directions-walk';
				$icons[] = 'subway';
				$icons[] = 'train';
				$icons[] = 'tram';
				$icons[] = 'local-shipping';	
				$icons[] = 'local-taxi';	
				$icons[] = 'traff';
				$icons[] = 'flight';	
				$icons[] = 'flight_land';
				$icons[] = 'flight_takeoff';
				$icons[] = 'motorcycle';
				$icons[] = 'airport_shuttle';				
				$icons[] = 'local_gas_station';				
				$icons[] = 'directions';
				$icons[] = 'directions2';
				break;
			case 'location':
				$icons[] = 'home';
				$icons[] = 'explore';
				$icons[] = 'my_location';
				$icons[] = 'location-on';
				$icons[] = 'location_searching';
				$icons[] = 'person_pin_circle';
				$icons[] = 'gps-fixed';
				$icons[] = 'near_me';
				$icons[] = 'hotel';	
				$icons[] = 'local-attraction';	
				$icons[] = 'local-bar';	
				$icons[] = 'local-cafe';	
				$icons[] = 'local-florist';	
				$icons[] = 'local-hospital';	
				$icons[] = 'local-library';
				$icons[] = 'books';
				$icons[] = 'local-mall';	
				$icons[] = 'local-parking';	
				$icons[] = 'local-pizza';	
				$icons[] = 'map';	
				$icons[] = 'navigation';	
				$icons[] = 'restaurant-menu';	
				$icons[] = 'store-mall-directory';	
				$icons[] = 'location-city';	
				$icons[] = 'publ';	
				$icons[] = 'school';	
				$icons[] = 'office';
				$icons[] = 'building';
				$icons[] = 'paw';
				$icons[] = 'spoon';
				$icons[] = 'futbol-o';
				$icons[] = 'weekend';
				$icons[] = 'airline_seat_individual_suite';
				$icons[] = 'airline_seat_recline_extra';
				$icons[] = 'airline_seat_recline_normal';
				$icons[] = 'wc';
				$icons[] = 'beach_access';
				$icons[] = 'business_center';
				$icons[] = 'casino';
				$icons[] = 'fitness_center';
				$icons[] = 'free_breakfast';
				$icons[] = 'golf_course';
				$icons[] = 'pool';
				$icons[] = 'theaters';				
				$icons[] = 'nature';
				$icons[] = 'nature_people';
				break;
			case 'social':
				$icons[] = 'group';	
				$icons[] = 'supervisor_account';
				$icons[] = 'person';
				$icons[] = 'person_outline';
				$icons[] = 'share2';
				$icons[] = 'omega';
				$icons[] = 'blogger';
				$icons[] = 'chat';
				$icons[] = 'comment';
				$icons[] = 'vcard';
				$icons[] = 'insert-emoticon';
				$icons[] = 'favorite';
				$icons[] = 'thumb-down';
				$icons[] = 'thumb-up';
				$icons[] = 'thumbs-up-down';
				$icons[] = 'star';
				$icons[] = 'star-half';
				$icons[] = 'star-outline';	
				$icons[] = 'flickr';
				$icons[] = 'vimeo';
				$icons[] = 'twitter';
				$icons[] = 'facebook';
				$icons[] = 'google';
				$icons[] = 'googleplus';
				$icons[] = 'pinterest';
				$icons[] = 'tumblr';
				$icons[] = 'linkedin';
				$icons[] = 'dribbble';
				$icons[] = 'stumbleupon';
				$icons[] = 'lastfm';
				$icons[] = 'spotify';
				$icons[] = 'instagram';
				$icons[] = 'circles';
				$icons[] = 'youtube-play';
				$icons[] = 'recent_actors';
				$icons[] = 'contacts';
				$icons[] = 'import_contacts';				
				break;
			case 'agenda':
				$icons[] = 'alarm';
				$icons[] = 'calendar';
				$icons[] = 'event';
				$icons[] = 'today';
				$icons[] = 'event-note';
				$icons[] = 'view-agenda';
				$icons[] = 'watch_later';
				$icons[] = 'timelapse';	
				$icons[] = 'timer';	
				$icons[] = 'access_time';
				$icons[] = 'event_seat';
				$icons[] = 'trophy';
				$icons[] = 'gift';
				$icons[] = 'cake';	
				$icons[] = 'birthday-cake';
				$icons[] = 'cake2';
				$icons[] = 'timeline';
				break;
			case 'finances':
				$icons[] = 'account-balance';
				$icons[] = 'account-box';
				$icons[] = 'credit-card';
				$icons[] = 'receipt';	
				$icons[] = 'shopping-cart';	
				$icons[] = 'wallet-giftcard';
				$icons[] = 'wallet-membership';	
				$icons[] = 'attach-money';
				$icons[] = 'paypal';
				$icons[] = 'google-wallet';
				$icons[] = 'cc-visa';
				$icons[] = 'cc-mastercard';
				$icons[] = 'cc-discover';
				$icons[] = 'cc-amex';				
				$icons[] = 'euro_symbol';
				break;
			case 'files':
				$icons[] = 'sd-storage';
				$icons[] = 'storage';
				$icons[] = 'attach-file';
				$icons[] = 'attachment';
				$icons[] = 'insert-drive-file';
				$icons[] = 'description';
				$icons[] = 'file-download';
				$icons[] = 'file-upload';
				$icons[] = 'folder';
				$icons[] = 'dropbox';
				$icons[] = 'evernote';
				$icons[] = 'picasa';
				$icons[] = 'archive';
				$icons[] = 'unarchive';
				$icons[] = 'cloud';
				break;
			case 'systems':
				$icons[] = 'stack-overflow';
				$icons[] = 'apple';
				$icons[] = 'windows';
				$icons[] = 'android';
				$icons[] = 'linux';
				$icons[] = 'wordpress';
				$icons[] = 'drupal';
				$icons[] = 'joomla';
				$icons[] = 'verified-user';
				$icons[] = 'security';
				$icons[] = 'bug-report';
				$icons[] = 'settings';				
				break;
			case 'accessibility':
				$icons[] = 'hearing';
				$icons[] = 'accessibility';
				$icons[] = 'accessible';
				$icons[] = 'touch_app';
				break;				
			case 'media':
				$icons[] = 'movie';
				$icons[] = 'movie_filter';
				$icons[] = 'subtitles';
				$icons[] = 'music_note';
				$icons[] = 'queue_music';
				$icons[] = 'music_video';
				$icons[] = 'playlist_play';
				$icons[] = 'playlist_add_check';
				$icons[] = 'slow_motion_video';
				$icons[] = 'album';
				$icons[] = 'speaker';
				$icons[] = 'camera_roll';
				$icons[] = 'photo_filter';
				$icons[] = 'radio';
				$icons[] = 'videocam';
				break;
				
			case 'other':
				$icons[] = 'assignment';
				$icons[] = 'book';
				$icons[] = 'bookmark';
				$icons[] = 'help';
				$icons[] = 'info2';
				$icons[] = 'label';
				$icons[] = 'language';
				$icons[] = 'language2';
				$icons[] = 'picture-in-picture';
				$icons[] = 'query-builder';
				$icons[] = 'stars';
				$icons[] = 'extension';
				$icons[] = 'dashboard';
				$icons[] = 'format-quote';
				$icons[] = 'view-carousel';
				$icons[] = 'visibility';
				$icons[] = 'equalizer';
				$icons[] = 'games';
				$icons[] = 'add-circle';
				$icons[] = 'content-paste';
				$icons[] = 'create';
				$icons[] = 'flag';
				$icons[] = 'forward';
				$icons[] = 'remove-circle';
				$icons[] = 'save';
				$icons[] = 'dvr';
				$icons[] = 'now-wallpaper';
				$icons[] = 'now-widgets';
				$icons[] = 'insert-photo';
				$icons[] = 'color-lens';
				$icons[] = 'filter-frames';
				$icons[] = 'healing';
				$icons[] = 'style';
				$icons[] = 'wb-sunny';
				$icons[] = 'apps';
				$icons[] = 'check-box';
				$icons[] = 'radio-button-on';
				$icons[] = 'pushpin';
				$icons[] = 'quotes-right';
				$icons[] = 'power-cord';
				$icons[] = 'tag';
				$icons[] = 'leaf';
				$icons[] = 'newspaper';
				$icons[] = 'lifebuoy';
				$icons[] = 'work';
				$icons[] = 'briefcase';
				$icons[] = 'hourglass';
				$icons[] = 'gauge';
				$icons[] = 'network';
				$icons[] = 'key';
				$icons[] = 'vpn_key';
				$icons[] = 'suitcase';
				$icons[] = 'light-bulb';
				$icons[] = 'box';
				$icons[] = 'ticket';
				$icons[] = 'rss';
				$icons[] = 'pie';
				$icons[] = 'lock';
				$icons[] = 'lock-open';
				$icons[] = 'info';
				$icons[] = 'docs';
				$icons[] = 'tag2';
				$icons[] = 'tags';
				$icons[] = 'chain';
				$icons[] = 'sitemap';
				$icons[] = 'new-releases';
				$icons[] = 'droplets';
				$icons[] = 'fiber_new';
				$icons[] = 'art_track';
				$icons[] = 'web_asset';
				$icons[] = 'highlight';
				$icons[] = 'developer_board';
				$icons[] = 'filter_vintage';
				$icons[] = 'power';
				$icons[] = 'build';
				$icons[] = 'fingerprint';
				$icons[] = 'pets';
				$icons[] = 'rowing';
				$icons[] = 'pan_tool';
				$icons[] = 'ac_unit';
				$icons[] = 'child_care';
				$icons[] = 'child_friendly';
				$icons[] = 'hot_tub';
				$icons[] = 'kitchen';
				$icons[] = 'room_service';
				$icons[] = 'smoke_free';
				$icons[] = 'smoking_rooms';
				$icons[] = 'spa';
				$icons[] = 'goat';
				$icons[] = 'update';
				$icons[] = 'launch';
				$icons[] = 'toys';
				break;
 			case 'icomoon': // icons compatible from J3.1 throughout J3.8
 				$icons[] = 'icomoon-home';
 				$icons[] = 'icomoon-user';
 				$icons[] = 'icomoon-lock';
 				$icons[] = 'icomoon-comment';
 				$icons[] = 'icomoon-comments-2';
 				$icons[] = 'icomoon-edit';
 				$icons[] = 'icomoon-pencil-2';
 				$icons[] = 'icomoon-folder-open';
 				$icons[] = 'icomoon-folder-close';
 				$icons[] = 'icomoon-picture';
 				$icons[] = 'icomoon-pictures';
 				$icons[] = 'icomoon-list';
 				$icons[] = 'icomoon-power-cord';
 				$icons[] = 'icomoon-cube';
 				$icons[] = 'icomoon-puzzle';
 				$icons[] = 'icomoon-flag';
 				$icons[] = 'icomoon-tools';
 				$icons[] = 'icomoon-cogs';
 				$icons[] = 'icomoon-options';
 				$icons[] = 'icomoon-equalizer';
 				$icons[] = 'icomoon-wrench';
 				$icons[] = 'icomoon-brush';
 				$icons[] = 'icomoon-eye';
 				$icons[] = 'icomoon-star-empty';
 				$icons[] = 'icomoon-star-2';
 				$icons[] = 'icomoon-star';
 				$icons[] = 'icomoon-calendar';
 				$icons[] = 'icomoon-calendar-2';
 				$icons[] = 'icomoon-help';
 				$icons[] = 'icomoon-support';
 				$icons[] = 'icomoon-warning';
 				$icons[] = 'icomoon-ok';
 				$icons[] = 'icomoon-cancel';
 				$icons[] = 'icomoon-minus';
 				$icons[] = 'icomoon-trash';
 				$icons[] = 'icomoon-mail';
 				$icons[] = 'icomoon-mail-2';
 				$icons[] = 'icomoon-unarchive';
 				$icons[] = 'icomoon-archive';
 				$icons[] = 'icomoon-box-add';
 				$icons[] = 'icomoon-box-remove';
 				$icons[] = 'icomoon-search';
 				$icons[] = 'icomoon-filter';
 				$icons[] = 'icomoon-camera';
 				$icons[] = 'icomoon-play';
 				$icons[] = 'icomoon-music';
 				$icons[] = 'icomoon-grid-view';
 				$icons[] = 'icomoon-grid-view-2';
 				$icons[] = 'icomoon-menu';
 				$icons[] = 'icomoon-thumbs-up';
 				$icons[] = 'icomoon-thumbs-down';
 				$icons[] = 'icomoon-remove';
 				$icons[] = 'icomoon-plus-2';
 				$icons[] = 'icomoon-minus-2';
 				$icons[] = 'icomoon-key';
 				$icons[] = 'icomoon-quote';
 				$icons[] = 'icomoon-quote-2';
 				$icons[] = 'icomoon-database';
 				$icons[] = 'icomoon-location';
 				$icons[] = 'icomoon-zoom-in';
 				$icons[] = 'icomoon-zoom-out';
 				$icons[] = 'icomoon-expand';
 				$icons[] = 'icomoon-contract';
 				$icons[] = 'icomoon-expand-2';
 				$icons[] = 'icomoon-contract-2';
 				$icons[] = 'icomoon-health';
 				$icons[] = 'icomoon-wand';
 				$icons[] = 'icomoon-refresh';
 				$icons[] = 'icomoon-vcard';
 				$icons[] = 'icomoon-clock';
 				$icons[] = 'icomoon-compass';
 				$icons[] = 'icomoon-address';
 				$icons[] = 'icomoon-feed';
 				$icons[] = 'icomoon-flag-2';
 				$icons[] = 'icomoon-pin';
 				$icons[] = 'icomoon-lamp';
 				$icons[] = 'icomoon-chart';
 				$icons[] = 'icomoon-bars';
 				$icons[] = 'icomoon-pie';
 				$icons[] = 'icomoon-dashboard';
 				$icons[] = 'icomoon-lightning';
 				$icons[] = 'icomoon-move';
 				$icons[] = 'icomoon-loop';
 				$icons[] = 'icomoon-shuffle';
 				$icons[] = 'icomoon-printer';
 				$icons[] = 'icomoon-color-palette';
 				$icons[] = 'icomoon-camera-2';
 				$icons[] = 'icomoon-file';
 				$icons[] = 'icomoon-cart';
 				$icons[] = 'icomoon-basket';
 				$icons[] = 'icomoon-broadcast';
 				$icons[] = 'icomoon-screen';
 				$icons[] = 'icomoon-tablet';
 				$icons[] = 'icomoon-mobile';
 				$icons[] = 'icomoon-users';
 				$icons[] = 'icomoon-briefcase';
 				$icons[] = 'icomoon-download';
 				$icons[] = 'icomoon-upload';
 				$icons[] = 'icomoon-bookmark';
 				$icons[] = 'icomoon-out-2';
 				
 				if (version_compare(JVERSION, '3.2', 'ge')) {
 					$icons[] = 'icomoon-joomla';
 					$icons[] = 'icomoon-link';
 					$icons[] = 'icomoon-phone';
 					$icons[] = 'icomoon-phone-2';
 					$icons[] = 'icomoon-tag';
 					$icons[] = 'icomoon-tag-2';
 					$icons[] = 'icomoon-tags';
 					$icons[] = 'icomoon-tags-2';
 					$icons[] = 'icomoon-equalizer';
 					$icons[] = 'icomoon-unlock';
 					$icons[] = 'icomoon-scissors';
 					$icons[] = 'icomoon-book';
 					$icons[] = 'icomoon-calendar-3';
 					$icons[] = 'icomoon-shield';
 					$icons[] = 'icomoon-heart';
 					$icons[] = 'icomoon-smiley-happy';
 					$icons[] = 'icomoon-smiley-happy-2';
 					$icons[] = 'icomoon-smiley-sad';
 					$icons[] = 'icomoon-smiley-sad-2';
 					$icons[] = 'icomoon-smiley-neutral';
 					$icons[] = 'icomoon-smiley-neutral-2';
 					$icons[] = 'icomoon-credit';
 					$icons[] = 'icomoon-credit-2';
 				}
		}
		
		$iconlist = '';
		foreach ($icons as $index => $icon_item) {
			
			if ($index == count($icons) - 1) {
				$iconlist .= '<li style="width: auto; float: left; margin: 2px 2px 10px 2px;" data-SYWicon="'.$icon_item.'">';
			} else {
				$iconlist .= '<li style="width: auto; float: left; margin: 2px;" data-SYWicon="'.$icon_item.'">';
			}
			$iconlist .= '<a href="#" class="label hvr-grow hasTooltip" style="padding: 8px; color: #fff; font-size: 1.4em" title="'.$icon_item.'" onclick="return false;"><i class="SYWicon-'.$icon_item.'"></i></a>';
			$iconlist .= '</li>';
		}
		
		return $iconlist;
	}
	
	static function getIcons($use_icomoon = false) 
	{		
		if (empty(self::$li_icons)) {
			
			if ($use_icomoon) {
				self::$icongrouplist[] = 'icomoon';
			}
			
			foreach (self::$icongrouplist as $icongrouplist_item) {
				self::$li_icons .= '<li class="divider" style="clear: both; width: auto; height: auto; padding: 3px; text-align: center; color: #797878 border: none;"><span>'.JText::_('LIB_SYW_ICONPICKER_ICONGROUP_'.strtoupper($icongrouplist_item)).'</span></li>';
				self::$li_icons .= self::getIconGroup($icongrouplist_item);
			}
		}
		
		return self::$li_icons;
	}
	
	protected function getInput() 
	{		
		$doc = JFactory::getDocument();	
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		JHtml::_('bootstrap.tooltip');

		JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);		
		JHtml::_('stylesheet', 'syw/2d-transitions-min.css', false, true);
		
		if ($this->icomoon) {
			JHtml::_('stylesheet', 'jui/icomoon.css', false, true); // make sure icomoon font is loaded
			JHtml::_('stylesheet', 'syw/fonts-icomoon-min.css', false, true);
		}
		
		$script = 'jQuery(document).ready(function () { ';	

			// after load, select the saved value
			$script .= 'if (jQuery(\'#' . $this->id . '\').val() != "") { ';
				$script .= 'jQuery("#'.$this->id.'_select li a").each(function() { ';
					$script .= 'if (jQuery(this).parent().attr(\'data-SYWicon\') == jQuery(\'#' . $this->id . '\').val()) { ';
						$script .= 'jQuery(this).addClass("label-success"); ';
					$script .= '} ';
				$script .= '}); ';
			$script .= '} ';
		
			$script .= 'jQuery("#'.$this->id.'_select li").click(function() { ';
				// de-select the previous value
				$script .= 'jQuery("#'.$this->id.'_select li a").each(function() { ';
					$script .= 'jQuery(this).removeClass("label-success"); ';
				$script .= '}); ';
				//
				$script .= 'jQuery(\'#' . $this->id . '\').val(jQuery(this).attr(\'data-SYWicon\')); ';
				$script .= 'jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-\' + jQuery(this).attr(\'data-SYWicon\')); ';
				if ($this->buttonrole == 'default') {
					$script .= 'jQuery("#'.$this->id.'_default").removeClass("btn-primary"); ';
				}
				$script .= 'jQuery(this).children(":first").addClass("label-success"); ';
			$script .= '}); ';
			
			$script .= 'jQuery("#'.$this->id.'_default").click(function() { ';
				$script .= 'jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'\'); ';
				if (empty($this->default)) {
					$script .= 'jQuery(\'#' . $this->id . '\').val(\'\'); ';
					$script .= 'jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-'.$this->emptyicon.'\'); ';
				} else {
					$script .= 'jQuery(\'#' . $this->id . '\').val(\''.$this->default.'\'); ';
					$script .= 'jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-'.$this->default.'\'); ';
				}
				if ($this->buttonrole == 'default') {
					$script .= 'jQuery("#'.$this->id.'_default").addClass("btn-primary"); ';
				}
				$script .= 'jQuery("#'.$this->id.'_select li a").removeClass("label-success"); ';
			$script .= '}); ';
		
			$script .= 'jQuery("#'.$this->id.'").change(function() { ';
				$script .= 'jQuery(\'#' . $this->id . '_icon\').attr(\'class\', \'SYWicon-\' + jQuery("#'.$this->id.'").val()); ';
			$script .= '}); ';
		
		$script .= '}); ';
		
		$doc->addScriptDeclaration($script);
					
		$html = '';
			
		$html .= '<div class="input-prepend input-append">';	
		
 		if (!empty($this->value)) {
 			$html .= '<div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->value.'"></i></div>';
 		} else {
			if (empty($this->default)) {
				$html .= '<div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->emptyicon.'"></i></div>';
			} else {
				$html .= '<div class="add-on"><i id="'.$this->id.'_icon" class="SYWicon-'.$this->default.'"></i></div>';
			}
 		}	

 		if ($this->editable) {
			$html .= '<input type="text" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" class="input-small" />';
		} else {
			//$html .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" />';
			$html .= '<input type="text" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" readonly="readonly" class="input-small" />';
		}
		
		$html .= '<div class="btn-group" style="display:inline-block;vertical-align:middle">';
			$html .= '<button id="'.$this->id.'_caret"'.($this->disabled ? ' disabled="disabled"' : '').' style="border-radius:0;margin-left:-1px;min-width:auto" class="btn dropdown-toggle hasTooltip" data-toggle="dropdown" title="' . JText::_('LIB_SYW_ICONPICKER_SELECTICON') . '">';
				$html .= '<span class="caret" style="margin-bottom:auto"></span>';
			$html .= '</button>';
			$html .= '<ul id="'.$this->id.'_select" class="dropdown-menu" style="min-width: 250px; max-height: 200px; overflow: auto">';
						
		if (isset($this->icons)) {			
			$icons = explode(",", $this->icons);
			foreach ($icons as $icon_item) {
				$html .= '<li style="width: auto; float: left; margin: 2px;" data-SYWicon="'.$icon_item.'"><a href="#" class="label hvr-grow hasTooltip" style="padding: 8px; color: #fff; font-size: 1.4em" title="'.$icon_item.'" onclick="return false;"><i class="SYWicon-'.$icon_item.'"></i></a></li>';
			}
		} else if (isset($this->icongroups)) {
			$icongroups = explode(",", $this->icongroups);
			foreach ($icongroups as $icongroup_item) {
				$html .= '<li class="divider" style="clear: both; width: auto; height: auto; padding: 3px; text-align: center; color: #797878; border: none;"><span>'.JText::_('LIB_SYW_ICONPICKER_ICONGROUP_'.strtoupper($icongroup_item)).'</span></li>';
				$html .= self::getIconGroup($icongroup_item);
			}
		} else {
			$html .= self::getIcons($this->icomoon); 
		}		
		
		$html .= '</ul>';
		$html .= '</div>';
		
		$default_class_extra = '';
		if (empty($this->value) || (!empty($this->default) && $this->default == $this->value)) {
			if ($this->buttonrole == 'default') {
				$default_class_extra = ' btn-primary';
			}
		}
		$html .= '<a id="'.$this->id.'_default"'.($this->disabled ? ' disabled="disabled"' : '').' class="btn'.$default_class_extra.' hasTooltip" title="'.htmlspecialchars($this->buttonlabel, ENT_COMPAT, 'UTF-8').'" href="#" onclick="return false;">';
		if ($this->buttonrole == 'clear') {
			$html .= '<i class="icon-remove"></i>';
		} else {
			$html .= $this->buttonlabel;
		}		
		$html .= '</a>';
				
		$html .= '</div>';
		
		if ($this->help) {
			$html .= '<span class="help-block">'.JText::_($this->help).'</span>';
		}
		
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->icons = isset($this->element['icons']) ? $this->element['icons'] : null;
			$this->icongroups = isset($this->element['icongroups']) ? $this->element['icongroups'] : null;
			
			$this->help = isset($this->element['help']) ? $this->element['help'] : '';
			if (strpos($this->id, 'X__') !== false) { // this happens if included in subform
				$this->help = JText::_('LIB_SYW_GLOBAL_UNSELECTABLE');
				$this->disabled = true;
			}
			
			$this->icomoon = isset($this->element['icomoon']) ? filter_var($this->element['icomoon'], FILTER_VALIDATE_BOOLEAN) : false;
			$this->editable = isset($this->element['editable']) ? filter_var($this->element['editable'], FILTER_VALIDATE_BOOLEAN) : false;
			$this->buttonrole = isset($this->element['buttonrole']) ? JText::_($this->element['buttonrole']) : 'default';
			$this->buttonlabel = isset($this->element['buttonlabel']) ? JText::_($this->element['buttonlabel']) : ($this->buttonrole == 'clear' ? JText::_('JCLEAR') : JText::_('JDEFAULT'));
			$this->emptyicon = isset($this->element['emptyicon']) ? $this->element['emptyicon'] : ($this->buttonrole == 'default' ? 'question' : '');
		}

		return $return;
	}

}
?>
