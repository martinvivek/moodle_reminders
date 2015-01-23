<?php

require_once(__DIR__ . '/vendor/autoload.php');

/** The twig template source files are stored here */
define('TEMPLATE_DIR', __DIR__ . '/templates');
define('TWIG_CACHE_DIR', __DIR__ . '/twig_cache');

/**
 * Renders templates stored in the TEMPLATE_DIR directory and caches them
 */
class TemplateRenderer {
    public $twig;

    /**
     * Creates a twig loader and environment
     */
    function __construct() {
        $loader = new Twig_Loader_Filesystem(TEMPLATE_DIR);
        $this->twig = new Twig_Environment($loader, array('cache' => TWIG_CACHE_DIR));
    }

    /**
     * @param $file_name
     * @param $vars
     * @return string Rendered template
     */
    function render($file_name, $vars) {
        return $this->twig->render($file_name, $vars);
    }
}