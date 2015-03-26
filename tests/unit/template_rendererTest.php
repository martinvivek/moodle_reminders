<?php

require_once(__DIR__ . '/../../template_renderer.php');

class template_rendererTest extends PHPUnit_Framework_TestCase {
    // Make sure that the template renderer can render existing templates and inlinify css
    public function test_render() {
        $env = array('name' => 'Andy');
        $renderer = new \template_renderer(false);

        $html = $renderer->render('tests/test_renderer.twig', 'tests/test_renderer.css', $env);
        // Test Variable Insertion
        $this->assertContains('Hello Andy!', $html);
        // Test conversion of external css to inline styles
        $this->assertContains('color: red', $html);
    }
}
