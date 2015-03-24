<?php

if(!defined('CLI_SCRIPT')) define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../template_renderer.php');

class template_rendererTest extends PHPUnit_Framework_TestCase {
    // Make sure the sort by filter sorts correctly both ascending and descending
    public function test_sort_by_filter() {
        $arr_jumbled = array(
            array('id' => 1, 'some_prop' => 1),
            array('id' => 3, 'some_prop' => 3),
            array('id' => 2, 'some_prop' => 2)
        );

        $arr_sorted_desc = array(
            array('id' => 1, 'some_prop' => 1),
            array('id' => 2, 'some_prop' => 2),
            array('id' => 3, 'some_prop' => 3)
        );

        $arr_sorted_asc = array(
            array('id' => 3, 'some_prop' => 3),
            array('id' => 2, 'some_prop' => 2),
            array('id' => 1, 'some_prop' => 1)
        );

        // Make sure sort descending works
        $this->assertEquals(template_renderer::sort_by_filter($arr_jumbled, 'some_prop'), $arr_sorted_desc);
        $this->assertEquals(template_renderer::sort_by_filter($arr_jumbled, 'some_prop', 'desc'), $arr_sorted_desc);
        $this->assertNotEquals(template_renderer::sort_by_filter($arr_jumbled, 'some_prop', 'desc'), $arr_jumbled);

        // Make sure sort ascending works
        $this->assertEquals(template_renderer::sort_by_filter($arr_jumbled, 'some_prop', 'asc'), $arr_sorted_asc);
        $this->assertNotEquals(template_renderer::sort_by_filter($arr_jumbled, 'some_prop', 'asc'), $arr_jumbled);
    }

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
