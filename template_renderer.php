<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');

/** The twig template source files are stored here */
define('TEMPLATE_DIR', __DIR__ . '/templates');
define('TWIG_CACHE_DIR', __DIR__ . '/twig_cache');
define('IMG_DIR', '/local/moodle_reminders/images/');

/**
 * Renders templates stored in the TEMPLATE_DIR directory and caches them
 */
class template_renderer {
    public $twig;

    static function sort_by_filter($arr, $property, $direction = 'desc') {
        usort($arr, function($item1, $item2) use ($property, $direction) {
            $item1_arr = (array) $item1;
            $item2_arr = (array) $item2;

            $result =  $item1_arr[$property] > $item2_arr[$property];

            return $direction == 'desc' ? $result : !$result;
        });
        return $arr;
    }

    /**
     * Creates a twig loader and environment
     * @param $cache boolean
     */
    function __construct($cache = true) {
        $loader = new Twig_Loader_Filesystem(TEMPLATE_DIR);
        $config = array();
        if ($cache) $config['cache'] = TWIG_CACHE_DIR;
        $this->twig = new Twig_Environment($loader, $config);
        $this->twig->addFilter('sort_by', new Twig_Filter_Function('template_renderer::sort_by_filter'));
    }

    /**
     * @param $file_name
     * @param $style_name
     * @param $vars
     * @return string Rendered template
     */
    public function render($file_name, $style_name, $vars = array()) {
        $cssInliner = new TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
        $cssInliner->setHTML($this->twig->render($file_name, $vars));
        $cssInliner->setCSS($this->twig->render($style_name, $vars));
        $html_with_inline_styles = $cssInliner->convert();

        global $CFG;
        return str_replace('images/', $CFG->wwwroot . IMG_DIR, $html_with_inline_styles);
    }
}