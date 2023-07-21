<?php
/**
 * lastcomp Plugin: Compare the timestamps of the last modification of two pages
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Hans-Jürgen Schümmer
 *             adapted from "modcomp Plugin" by Dennis Ploeger
 
 * Syntax:
 * ~~LASTCOMP|<page1>~~
 * or
 * ~~LASTCOMP|<page1>|<page2>~~

 * Examples:
 * ~~LASTCOMP|ktg00:m0000068.004.v01~~
 *             compares the current wiki page with the page 'ktg00:m0000068.004.v01'
 * ~~LASTCOMP|ktg00:m0000068.004.v01|playground:playground~~
 *             compares the wiki page 'ktg00:m0000068.004.v01' with the page 'playground:playground'
 *
 * Additionally required:
 * wrap plugin
**/


if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_lastcomp extends DokuWiki_Syntax_Plugin {
    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }
    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'normal';
    }
    /**
     * Where to sort in?
     */ 
    function getSort(){
        return 160;
    }
    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~LASTCOMP[^~]*~~',$mode,'plugin_lastcomp');
    }
    /**
     * Handle the match
     */

	function handle($match, $state, $pos, Doku_Handler $handler){

		global $ID, $INFO, $conf;
		
		$basis = DOKU_URL . "doku.php?id=";
		
		// erste Seite auslesen
		$match = str_replace("~~lastcomp", '', $match);
		$match = str_replace("~~", '', $match);
		$typ = explode('|',$match);
		
		$verz1 = $typ[1];
		if (!empty($typ[2])) {
			$verz2 = $typ[2];
		} else {
			$verz2 = $ID;
		}


		$id_save = $ID;
		$ID = $verz1;
		$tmp_info = pageinfo();
		$mod1 = $tmp_info['lastmod'];
		$ID = $id_save;

		$id_save = $ID;
		$ID = $verz2;
		$tmp_info = pageinfo();
		$mod2 = $tmp_info['lastmod'];
		$ID = $id_save;


		$titel1 = substr($verz1,strrpos($verz1,":")+1);
		$titel2 = substr($verz2,strrpos($verz2,":")+1);

		$link1 = "<a href = $basis.$verz1 title=$titel1>$titel1</a>";
		$link2 = "<a href = $basis.$verz2 title=$titel2>$titel2</a>";
		
		$datum1 = strftime($conf['dformat'], $mod1);
		$datum2 = strftime($conf['dformat'], $mod2);

		$txt1 = $this->getLang('txt1');
		$txt2 = $this->getLang('txt2');
		$txt3 = $this->getLang('txt3');

		if ($mod1 > $mod2) {
			$mod = "<div class='wrap_alert wrap_right';>";
			$mod .= $txt1 . "<br>'" . $link1 . "' (". $datum1 . ")<br>" . $txt2 . "<br>'" . $link2 . "' (" . $datum2 . ")<br>" . $txt3;
			$mod .= "</div>";
		} else {
			$mod = "<div class='wrap_info wrap_right';>";
			$mod .= $txt1 . "<br>'" . $link2 . "' (". $datum2 . ")<br>" . $txt2 . "<br>'" . $link1 . "' (". $datum1 . ")";
			$mod .= "</div>";
		}
		
		return $mod;
	}

    /**
     * Create output
     */
	function render($mode, Doku_Renderer $renderer, $data) {
		if($mode == 'xhtml'){
			$renderer->doc .= $data;
			return true;
		}
		return false;
	}

}

?>
