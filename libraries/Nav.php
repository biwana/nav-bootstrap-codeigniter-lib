<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * Bootstrap 3 Nav builder for CodeIgniter
 * http://getbootstrap.com/   
 * http://ellislab.com/codeigniter
 * 
 * This library takes an array and creates a Bootstrap 3 Nav list.
 *
 * @author		Brian Iwana
 * @copyright           Copyright (c) 2014, Brian Iwana.
 * @license		https://github.com/biwana/nav-bootstrap-codeigniter-lib/blob/master/LICENSE
 * @link		http://www.brianiwana.com
 * @requires            Bootstrap 3 http://getbootstrap.com/
 */
// ------------------------------------------------------------------------

/**
 * Nav
 *
 * Takes in an array and builds a Bootstrap 3 Nav list.
 * 
 * Examples:
 *
 * @access	public
 * @param	string
 * @param       bool
 * @return	string
 */
class Nav {

    // Default configuration
    private $config = array(
        'ul_class' => 'nav',            // classes of the list container
        'type_class' => 'navbar-nav',   // nav type class, default choices are navbar-nav, nav-pills, nav-tabs
        'active_class' => 'active',     // class given to the active li
        'ul_id' => NULL,                // id of the list container
        'arrow_class' => 'caret',       //class of the dropdown arrow
    );
    protected $active = NULL;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * Accepts a config array (optional)
     *
     * @access	public
     * @param	array	config preferences
     * @return	void
     */
    public function __construct($config = array()) {
        $CI = & get_instance();
        $CI->load->helper('url');
        $this->initialize($config);
    }

    // --------------------------------------------------------------------

    /**
     * Initialize preferences
     *
     * @access	public
     * @param	array
     * @return	void
     */
    public function initialize($config = array()) {
        if (is_array($config)) {
            foreach ($config as $k => $v) {
                $this->config[$k] = $v;
            }
        }
    }
    
    // --------------------------------------------------------------------

    /**
     * Render Nav
     *
     * @access	public
     * @param	array menu item data
     * @param	string the active menu item
     * @return	string
     */
    public function render($items, $active = NULL, $config = NULL) {
        if (!empty($config)) {
            $this->initialize($config);
        }
        
        if (isset($this->config['ul_class'])) {
            if (is_array($this->config['ul_class'])) {
                $attributes['class'] = implode(' ', $this->config['ul_class']) . ' ' . $this->config['type_class'];
            } elseif (is_string($this->config['ul_class']) AND strlen($this->config['ul_class']) > 0) {
                $attributes['class'] = $this->config['ul_class'] . ' ' . $this->config['type_class'];
            }
        }

        if (isset($this->config['ul_id'])) {
            $attributes['id'] = $this->config['ul_id'];
        }
        $nav = $this->_list($this->_normalize($items), $active, $attributes);
        return $nav;
    }

    // --------------------------------------------------------------------

    /**
     * changes the parent system into a embedded array
     *
     * @access	public
     * @param	array list
     * @return	array
     */
    private function _normalize($list) {
        // If an array wasn't submitted there's nothing to do...
        if (!is_array($list)) {
            return $list;
        }
        $ret = array();

        foreach ($list as $key => $val) {
            $val['location'] = isset($val['location']) ? $val['location'] : $key;
            $val['label'] = isset($val['label']) ? $val['label'] : $key;
            $val['class'] = isset($val['class']) ? ' '.$val['class'] : '';
            $val['id'] = isset($val['id']) ? " id='{$val['id']}'" : '';
            
            if (isset($val['parent_id'])) {
                if ( isset($ret[$val['parent_id']])) { 
                    // if parent doesn't exist, the children wont either
                    $ret[$val['parent_id']]['children'][$key] = $val;
                }
            } else {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    // --------------------------------------------------------------------

    /**
     * Formats list
     *
     * @access	private
     * @param	array menu item data
     * @param	string the active menu item
     * @return	string
     */
    private function _list($list, $active = null, $attributes = '', $depth = 0) {
        // If an array wasn't submitted there's nothing to do...
        if (!is_array($list)) {
            return $list;
        }

        // Were any attributes submitted?  If so generate a string
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' ' . $key . '="' . $val . '"';
            }
            $attributes = $atts;
        } elseif (is_string($attributes) AND strlen($attributes) > 0) {
            $attributes = ' ' . $attributes;
        }

        // Write the opening list tag
        $out = str_repeat(" ", $depth);
        $out .= "<ul" . $attributes . ">\n";

        // Cycle through the list elements.  If an array is
        // encountered we will recursively call _list()
        foreach ($list as $key => $val) {
            $class = $val['class'];
            $out .= str_repeat(" ", $depth + 2);
            if (isset($val['divider'])) {
                $out .= "<li{$val['id']} class='divider{$class}'>";
            } else {
                if (!empty($val['children'])) {
                    $out .= "<li{$val['id']} class='dropdown{$class}'>\n";
                    $label = $val['label'];
                    $label .= isset($this->config['arrow_class']) ? " <span class='{$this->config['arrow_class']}'></span>" : '';
                    $out .= str_repeat(" ", $depth + 4);
                    $out .= "<a class='dropdown-toggle' data-toggle='dropdown' href='#'>{$label}</a>"; 
                    $out .= "\n";
                    $out .= $this->_list($val['children'], $active, 'class="dropdown-menu"', $depth + 6);
                } else {
                    if ($key == $active) {
                        $out .= "<li{$val['id']} class='active{$class}'>";
                    } else { 
                        $class_att = !empty($class) ? " class='{$class}'" : '';
                        $out .= "<li{$val['id']}{$class_att}>";
                    }
                    $base_url = !empty($val['no_base']) ? '' : base_url();
                    $out .= "<a href='{$base_url}{$val['location']}'>{$val['label']}</a>";
                }
            }
            $out .= "</li>\n";
        }

        // Set the indentation for the closing tag
        $out .= str_repeat(" ", $depth);

        // Write the closing list tag
        $out .= "</ul>\n";

        return $out;
    }

    // --------------------------------------------------------------------

    /**
     * Easy list type renders. Optional shortcuts
     *
     * @access	public
     * @param	array menu item data
     * @param	string the active menu item
     * @return	string
     */
    public function render_navbar($items, $active = NULL) {
        $this->config['type_class'] = 'navbar-nav';
        return $this->render($items, $active);        
    }
    public function render_pills($items, $active = NULL) {
        $this->config['type_class'] = 'nav-pills';
        return $this->render($items, $active);        
    }
    public function render_tabs($items, $active = NULL) {
        $this->config['type_class'] = 'nav-tabs';
        return $this->render($items, $active);        
    }
}
