<?php
/**
 * DokuWiki Plugin scrapbook (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_scrapbook extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'handle_toolbar_define');
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handle_ajax_call');
    }

    /**
     * Register a new toolbar button
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_toolbar_define(Doku_Event $event, $param) {

        $event->data[] = array(
            'type' => 'plugin_scrapbook',
            'title' => 'insert from scrapbook',
            'icon' => DOKU_BASE . 'lib/plugins/scrapbook/icon.png',
        );
    }

    /**
     * List available templates
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_ajax_call(Doku_Event $event, $param) {
        if($event->data != 'plugin_scrapbook') return;
        $event->preventDefault();
        $event->stopPropagation();
        global $conf;
        global $lang;

        header('Content-Type: text/html; charset=utf-8');

        $data = array();
        search(
            $data,
            $conf['datadir'],
            'search_universal',
            array(
                'depth' => 1,
                'pagesonly' => true,
                'listfiles' => true,
                'firsthead' => true
            ),
            utf8_encodeFN(str_replace(':', '/', $this->getConf('ns')))
        );

        if(!$data) {
            echo '<div>'.$lang['nothingfound'].'</div>';
        } else foreach($data as $page) {
            if($page['title']) {
                $title = $page['title'];
            } else {
                $title = str_replace(array('_', '-'), array(' ', ' - '), noNS($page['id']));
            }
            echo '<button data-id="' . hsc($page['id']) . '">' . hsc($title) . '</button>';
        }
    }
}

// vim:ts=4:sw=4:et:
