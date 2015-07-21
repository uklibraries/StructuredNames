<?php
/**
 * StructuredNames
 * 
 * @copyright Copyright 2015 Michael Slone 
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 */

/**
 * The StructuredNames plugin.
 * 
 * @package Omeka\Plugins\StructuredNames
 */
class StructuredNamesPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'admin_head',
    );

    protected $_filters = array(
        'element_types_info',
    );

    public function hookAdminHead($args) {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $module = $request->getModuleName();
        if (is_null($module)) {
            $module = 'default';
        }
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($module === 'default'
            && $controller === 'items'
            && in_array($action, array('add', 'edit')))
        {
            queue_js_file('structured_name');
        }
    }

    public function sNameInterviewer($text, $args) {
        return $this->_sNameField($text, $args);
    }

    public function sNameInterviewee($text, $args) {
        return $this->_sNameField($text, $args);
    }

    public function _sNameField($text, $args) {
        $text = str_replace('&quot;', '"', $text);
        $m = json_decode($text, true);
        $keys = array('first', 'middle', 'last');
        $pieces = array();
        foreach ($keys as $key) {
            if (isset($m[$key])) {
                $pieces[] = $m[$key];
            }
        }
        return implode(' ', $pieces);
    }

    public function filterElementTypesInfo($types) {
        $types['name'] = array(
            'label' => __('Name'),
            'filters' => array(
                'ElementInput' => array($this, 'filterElementInput'),
                'Display' => array($this, 'filterDisplay'),
            ),
            'hooks' => array(
            ),
        );
        return $types;
    }

    public function filterElementInput($components, $args) {
        $view = get_view();
        $element = $args['element'];
        $element_id = $element->id;
        $index = $args['index'];
        $name = "Elements[$element_id][$index][text]";
        $id = "Elements-$element_id-$index-text";
        $first_id = "$id-first";
        $middle_id = "$id-middle";
        $last_id = "$id-last";
        $value = $args['value'];
        if ($value === '') {
            $value = '{"first":"Insert","middle":"Name","last":"Here"}';
        }
        $m = json_decode($value, true);
        $pieces = array(
            $m['first'],
            $m['middle'],
            $m['last'],
        );
        $value = json_encode(array(
            'first' => $m['first'],
            'middle' => $m['middle'],
            'last' => $m['last'],
        ));
        $serial = implode(' ', $pieces);

        $components['input'] = <<<EOT
<input type="hidden"
       name="{$view->escape($name)}"
       id="{$view->escape($id)}"
       data-type="name"
       data-first="{$view->escape($first_id)}"
       data-middle="{$view->escape($middle_id)}"
       data-last="{$view->escape($last_id)}"
       value="{$view->escape($value)}"/>
<input type="text"
       id="{$view->escape($first_id)}"
       value="{$view->escape($m['first'])}"
       data-parent="{$view->escape($id)}"/>
<input type="text"
       id="{$view->escape($middle_id)}"
       value="{$view->escape($m['middle'])}"
       data-parent="{$view->escape($id)}"/>
<input type="text"
       id="{$view->escape($last_id)}"
       value="{$view->escape($m['last'])}"
       data-parent="{$view->escape($id)}"/>
EOT;

        $components['html_checkbox'] = NULL;
        return $components;
    }

    public function filterDisplay($text, $args) {
        $text = str_replace('&quot;', '"', $text);
        $m = json_decode($text, true);
        $keys = array('first', 'middle', 'last');
        $pieces = array();
        foreach ($keys as $key) {
            if (isset($m[$key])) {
                $pieces[] = $m[$key];
            }
        }
        return implode(' ', $pieces);
    }
}
