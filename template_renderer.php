<?php

require_once(__DIR__ . '/vendor/autoload.php');

/** The twig template source files are stored here */
define('TEMPLATE_DIR', __DIR__ . '/templates');
define('TWIG_CACHE_DIR', __DIR__ . '/twig_cache');

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
        return $cssInliner->convert();
    }
}