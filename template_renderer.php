<?php

namespace local_moodle_reminders;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');

use \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles as CssToInlineConverter;

/** The twig template source files are stored here */
define('TEMPLATE_DIR', __DIR__ . '/templates');
define('TWIG_CACHE_DIR', __DIR__ . '/twig_cache');
define('IMG_DIR', '/local/moodle_reminders/images/');

/**
 * Renders templates stored in the TEMPLATE_DIR directory and caches them
 */
class template_renderer {
    public $twig;

    /**
     * Creates a twig loader and environment
     * @param $cache boolean
     */
    function __construct($cache = true) {
        $loader = new \Twig_Loader_Filesystem(TEMPLATE_DIR);
        $config = array();
        if ($cache) $config['cache'] = TWIG_CACHE_DIR;
        $this->twig = new \Twig_Environment($loader, $config);
    }

    /**
     * A simple alias the twig render function
     * @param $file_name string Location of file relative to TEMPLATE_DIR
     * @param $vars mixed Objects to be used by Twig
     * @return string Rendered template
     */
    public function render($file_name, $vars) {
        return $this->twig->render($file_name, (array) $vars);
    }

    /**
     * Renders html with twig, generates inline css, sets image paths to full url of image folder
     * @param $file_name string Location of file relative to TEMPLATE_DIR
     * @param $stylesheet string stylesheet to convert to inline css
     * @param $vars mixed Objects to be used by Twig
     * @return string Rendered template
     */
    public function render_email($file_name, $stylesheet, $vars = array()) {
        $cssInliner = new CssToInlineConverter();
        $cssInliner->setHTML($this->twig->render($file_name, (array) $vars));
        $cssInliner->setCSS($this->twig->render($stylesheet));
        $html_with_inline_styles = $cssInliner->convert();

        global $CFG;
        return str_replace('images/', $CFG->wwwroot . IMG_DIR, $html_with_inline_styles);
    }
}