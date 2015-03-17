<?php

require_once(__DIR__ . '/vendor/autoload.php');

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
        $loader = new Twig_Loader_Filesystem(TEMPLATE_DIR);
        $config = array();
        if ($cache) $config['cache'] = TWIG_CACHE_DIR;
        $this->twig = new Twig_Environment($loader, $config);
        function i18n($string_name) {
            return get_string($string_name, 'local_moodle_reminders');
        }
        $this->twig->addFilter('i18n', new Twig_Filter_Function('i18n'));
    }

    /**
     * @param $file_name
     * @param $style_name
     * @param $vars
     * @return string Rendered template
     */
    function render($file_name, $style_name, $vars) {
        $cssInliner = new TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
        $cssInliner->setHTML($this->twig->render($file_name, $vars));
        $cssInliner->setCSS($this->twig->render($style_name, $vars));
        $html_with_inline_styles = $cssInliner->convert();

        global $CFG;
        return str_replace('images/', $CFG->wwwroot . IMG_DIR, $html_with_inline_styles);
    }
}