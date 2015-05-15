<?php
/**
 * DokuWiki Plugin singlesearchresult (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Matthias Schulte <dokuwiki@lupo49.de>, Florian Straub <flominator@gmx.net>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_LF')) define('DOKU_LF', "\n");
if(!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_singlesearchresult extends DokuWiki_Action_Plugin {

	public $num_page_titles;
	public $the_one_hit;

    public function register(Doku_Event_Handler &$controller) {
        $controller->register_hook('SEARCH_QUERY_FULLPAGE', 'AFTER', $this, 'handle_search_query_fullpage');
		
		$controller->register_hook('SEARCH_QUERY_PAGELOOKUP', 'AFTER', $this, 'handle_search_query_pagelookup');
    }

	public function handle_search_query_pagelookup(Doku_Event &$event, $param) 
	{
		$this->num_page_titles=count($event->result);
		
		if($this->num_page_titles==1)
		{
			$this->the_one_hit = key($event->result);
		}
				
	}
    public function handle_search_query_fullpage(Doku_Event &$event, $param) {
        global $conf;
		$pageid ="";
		if(count($result)<=1 && $this->num_page_titles == 1)
		{
			$pageid= $this->the_one_hit;
		}
		
		if(count($event->result) == 1 && $this->num_page_titles == 0) 
		{
			$pageid = key($event->result);
		}
		
        // Only one page found, skip result overview and open the found page
        if($pageid != "") {

            if($_SERVER['REMOTE_USER']) {
                $perm = auth_quickaclcheck($pageid);
            } else {
                $perm = auth_aclcheck($pageid, '', null);
            }

            if($perm > AUTH_NONE) {
                if($conf['allowdebug']) {
                    msg("Only one page found, skipping result overview. Redirect to: ".$pageid);
                }
                $link = wl($pageid, '', true);
                print "<script type='text/javascript'>window.location.href='$link'</script>";
            }
        }
    }
}

// vim:ts=4:sw=4:et:
