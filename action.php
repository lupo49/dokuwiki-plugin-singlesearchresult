<?php
/**
 * DokuWiki Plugin singlesearchresult (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Matthias Schulte <dokuwiki@lupo49.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_LF')) define('DOKU_LF', "\n");
if(!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_singlesearchresult extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler &$controller) {
        $controller->register_hook('SEARCH_QUERY_FULLPAGE', 'AFTER', $this, 'handle_search_query_pagelookup');
    }

    public function handle_search_query_pagelookup(Doku_Event &$event, $param) {
        global $conf;
        $result = $event->result;

        // Only one page found, skip result overview and open the found page
        if(count($result) == 1) {
            $pageid = key($result);

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
