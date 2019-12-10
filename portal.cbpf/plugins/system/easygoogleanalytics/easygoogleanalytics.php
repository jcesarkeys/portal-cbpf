<?php
/**
* @version		3.2
* @author		Michael A. Gilkes (michael@valorapps.com)
* @copyright	Michael Albert Gilkes
* @license		GNU/GPLv2
*/

/*

Easy Google Analytics for Joomla!
Copyright (C) 2011-2015  Michael Albert Gilkes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//imports
jimport('joomla.plugin.plugin'); //needed for MVC implementation

class plgSystemEasyGoogleAnalytics extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      public
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.6
	 */
	public function __construct(&$subject, $config)
	{
		// Note:
		// $config should contain the params
		parent::__construct($subject, $config);
	}
	
	/**
	 * @param	none
	 *
	 * @return	none
	 * @since	1.5.23
	 */
	public function onBeforeCompileHead()
	{
		// check whether plugin has been unpublished
		if ($this->params->get('enabled', 1))
		{
			if ($this->params->get('ega_admin') == true)
			{
				//get application instance
				$app = JFactory::getApplication();
				
				//check to see if we are in the front-end or back-end
				if($app->isAdmin())
				{
					return; //leave since we are in the back-end. bye.
				}
			}
			
			//get the parameters
			$profileID = $this->params->get('ega_profileid');
			$tracking = $this->params->get('ega_tracking');
			$hostname = $this->params->get('ega_hostname');
			$track = $this->params->get('ega_track');
			$jscript = $this->params->get('ega_jscript');
			$category = $this->params->get('ega_category');
			$skiplinks = $this->params->get('ega_skiplinks');
			
			//remove whitespace from skiplinks
			$skiplinks = preg_replace('/\s+/', '', $skiplinks);
			//replace any '.' with '\.'
			$skiplinks = str_replace('.', '\.', $skiplinks);
			
			//ensure the proper javascript libraries are loaded, instead of assuming
			if ($jscript == 'jquery')
			{
				JHtml::_('jquery.framework');
			}
			else //mootools
			{
				JHtml::_('behavior.framework');
			}
			
			//handle the completed asynchronous tracking code
			$this->customTrackingCode($profileID, $tracking, $hostname);
			
			//handle the tracking of outbound links
			if ($track == 1)
			{
				$this->trackOutboundLinks($category, $jscript, $skiplinks, $tracking);
			}
		}
	}
	
	/**
	* method that composes the tracking code for a single domain
	*/
	protected function customTrackingCode($profileID, $tracking, $hostname)
	{
		$script = "\n\n".'/*===  EASY GOOGLE ANALYTICS : START TRACKING CODE  ===*/';
		if ($tracking == 'universal')
		{
			$script.= "\n\t"."(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
			$script.= "\n\t"."(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
			$script.= "\n\t"."m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
			$script.= "\n\t"."})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
			$script.= "\n\t"."ga('create', '".$profileID."', '".$hostname."');";
			$script.= "\n\t"."ga('send', 'pageview');";
		}
		else
		{
			$script.= "\n\t"."var _gaq = _gaq || [];";
			$script.= "\n\t"."_gaq.push(['_setAccount', '".$profileID."']);";
			if ($tracking == 'subdomains')
			{
				$script.= "\n\t"."_gaq.push(['_setDomainName', '.".$hostname."']);";
			}
			elseif ($tracking == 'tld')
			{
				$script.= "\n\t"."_gaq.push(['_setDomainName', 'none']);";
				$script.= "\n\t"."_gaq.push(['_setAllowLinker', true]);";
			}
			$script.= "\n\t"."_gaq.push(['_trackPageview']);"."\n";
			$script.= "\n\t"."(function() {";
			$script.= "\n\t\t"."var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";
			$script.= "\n\t\t"."ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
			$script.= "\n\t\t"."var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
			$script.= "\n\t"."})();";
		}
		$script.= "\n".'/*===  EASY GOOGLE ANALYTICS : END TRACKING CODE  ===*/'."\n";
		
		//add the javascript to the head of the html document
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}
	
	//Reference: https://support.google.com/analytics/answer/1136920?hl=en
	protected function trackOutboundLinks($category, $jscript, $skiplinks, $tracking)
	{
		//add the trackEvent code
		$script = "\n".'/*===  EASY GOOGLE ANALYTICS : START OUTBOUND LINKS  ===*/'."\n";
		$script.= "function trackOutboundLink(link, category, action) { "."\n";
		$script.= "  try { "."\n";
		if ($tracking == 'universal')
		{
			$script.= "    var blank = 'inline';\n";
			$script.= "    if (link.getAttribute('target') == '_blank') {\n";
			$script.= "      blank = 'blank';\n";
			$script.= "    }\n";
			$script.= "    ga('send', 'event', category, action, blank, {'hitCallback': "."\n";
			$script.= "      function() {"."\n";
			if (empty($skiplinks))
			{
				$script.= "        if (blank == 'blank') {"."\n";
				/* Note: window.open() may trigger pop-up blockers. */
				$script.= "          window.open(action, '_blank');\n";
				$script.= "        } else {\n";
				$script.= "          document.location = action;"."\n";
				$script.= "        }\n";
			}
			else
			{
				$script.= "        var patt=/".$skiplinks."/i;"."\n";
				$script.= "        if (!patt.test(action)) {"."\n";
				$script.= "          if (blank == 'blank') {"."\n";
				/* Note: window.open() may trigger pop-up blockers. */
				$script.= "            window.open(action, '_blank');\n";
				$script.= "          } else {\n";
				$script.= "            document.location = action;"."\n";
				$script.= "          }\n";
				$script.= "        }"."\n";
			}
			$script.= "      }"."\n";
			$script.= "    });"."\n";
		}
		else
		{
			$script.= "    _gaq.push(['_trackEvent', category , action]); "."\n";
		}
		$script.= "  } catch(err){\n";
		$script.= "    if (window.console) { console.error(err); }\n";
		$script.= "  }\n";
		
		if ($tracking != 'universal')
		{
			$script.= "  setTimeout(function() {"."\n";
			if (empty($skiplinks))
			{
				$script.= "    if (link.getAttribute('target') == '_blank') {\n";
				/* Note: window.open() may trigger pop-up blockers. */
				$script.= "      window.open(action, '_blank');\n";
				$script.= "    } else {\n";
				$script.= "      document.location = action;"."\n";
				$script.= "    }\n";
			}
			else
			{
				$script.= "    var patt=/".$skiplinks."/i;"."\n";
				$script.= "    if (!patt.test(action)) {"."\n";
				$script.= "      if (link.getAttribute('target') == '_blank') {\n";
				/* Note: window.open() may trigger pop-up blockers. */
				$script.= "        window.open(action, '_blank');\n";
				$script.= "      } else {\n";
				$script.= "        document.location = action;"."\n";
				$script.= "      }\n";
				$script.= "    }"."\n";
			}
			$script.= "  }, 100);"."\n";
		}
		$script.= "}";
		$script.= "\n".'/*===  EASY GOOGLE ANALYTICS : END OUTBOUND LINKS  ===*/'."\n";
		
		//add the javascript to the head of the html document
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		//handling onClick links
		$js = "\n".'/*===  EASY GOOGLE ANALYTICS : START FIXING LINKS  ===*/'."\n";
		/* search for absolute URLs starting with http, then check if they are not site domain */
		if ($jscript == 'jquery')
		{
			$js.= "if(typeof jQuery == 'function') {"."\n";
			$js.= "  jQuery(function () {"."\n";
			$js.= "    jQuery('a[href^=\"http\"]:not([href*=\"'+document.domain+'\"])').click(function () {"."\n";
			$js.= "      trackOutboundLink(this, '".$category."', jQuery(this).attr('href')); return false;"."\n";
			$js.= "    });"."\n";
			$js.= "  });"."\n";
			$js.= "}";
		}
		else //$jscript == 'mootools'
		{
			$js.= "window.addEvent('domready', function(){"."\n";
			$js.= "  $$('a[href^=\"http\"]:not([href*=\"' + document.domain + '\"])').each(function(el){"."\n";
			$js.= "    el.addEvent('click', function(){"."\n";
			$js.= "       trackOutboundLink(this, '".$category."', el.getProperty('href')); return false;"."\n";
			$js.= "     });"."\n";
			$js.= "   });"."\n";
			$js.= "});";
		}
		$js.= "\n".'/*===  EASY GOOGLE ANALYTICS : END FIXING LINKS  ===*/'."\n";
	
		//add the javascript to the head of the html document
		$document->addScriptDeclaration($js);
	}
}