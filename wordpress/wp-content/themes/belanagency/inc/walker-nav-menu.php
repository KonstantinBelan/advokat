<?php
/**
 * Navigation Walkers for Desktop, Mobile, and Footer Menus
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Desktop Header Navigation Walker
 */
class Belan_Desktop_Nav_Walker extends Walker_Nav_Menu {

    private $parent_has_grandchildren = false;

    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if (!empty($children_elements[$element->ID])) {
            $element->has_children = true;
            $has_grandchildren = false;
            foreach ($children_elements[$element->ID] as $child) {
                if (!empty($children_elements[$child->ID])) {
                    $has_grandchildren = true;
                    break;
                }
            }
            $element->has_grandchildren = $has_grandchildren;
        } else {
            $element->has_children = false;
            $element->has_grandchildren = false;
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        if ($depth === 0) {
            $dropdown_class = $this->parent_has_grandchildren ? 'header-dropdown header-dropdown--services' : 'header-dropdown header-dropdown--simple';
            $output .= "\n{$indent}<div class=\"{$dropdown_class}\">\n{$indent}\t<ul class=\"header-dropdown__list\">\n";
        } elseif ($depth === 1) {
            $output .= "\n{$indent}<div class=\"header-submenu\">\n{$indent}\t<ul class=\"header-submenu__list\">\n";
        } else {
            $output .= "\n{$indent}<ul class=\"sub-menu\">\n";
        }
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        if ($depth === 0) {
            $output .= "{$indent}\t</ul>\n{$indent}</div>\n";
        } elseif ($depth === 1) {
            $output .= "{$indent}\t</ul>\n{$indent}</div>\n";
        } else {
            $output .= "{$indent}</ul>\n";
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $url   = !empty($item->url) ? esc_url($item->url) : '#';
        $title = apply_filters('the_title', $item->title, $item->ID);

        if ($depth === 0) {
            if (!empty($item->has_children)) {
                $this->parent_has_grandchildren = !empty($item->has_grandchildren);
                $output .= "{$indent}<li class=\"header-menu__item-has-dropdown\">\n";
                $output .= "{$indent}\t<a class=\"header-menu__item\" href=\"{$url}\">\n";
                $output .= "{$indent}\t\t" . esc_html($title) . "\n";
                $output .= "{$indent}\t\t<svg class=\"header-menu__arrow\" width=\"10\" height=\"6\" viewBox=\"0 0 10 6\" fill=\"none\">\n";
                $output .= "{$indent}\t\t\t<path d=\"M1 1L5 5L9 1\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n";
                $output .= "{$indent}\t\t</svg>\n";
                $output .= "{$indent}\t</a>\n";
            } else {
                $output .= "{$indent}<li><a class=\"header-menu__item\" href=\"{$url}\">" . esc_html($title) . "</a>";
            }
        } elseif ($depth === 1) {
            if (!empty($item->has_children)) {
                $output .= "{$indent}<li class=\"header-dropdown__item-has-submenu\">\n";
                $output .= "{$indent}\t<a href=\"{$url}\" class=\"header-dropdown__link\">\n";
                $output .= "{$indent}\t\t<span>" . esc_html($title) . "</span>\n";
                $output .= "{$indent}\t\t<svg class=\"header-dropdown__chevron\" width=\"6\" height=\"10\" viewBox=\"0 0 6 10\" fill=\"none\">\n";
                $output .= "{$indent}\t\t\t<path d=\"M1 9L5 5L1 1\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n";
                $output .= "{$indent}\t\t</svg>\n";
                $output .= "{$indent}\t</a>\n";
            } else {
                $output .= "{$indent}<li><a href=\"{$url}\" class=\"header-dropdown__link\"><span>" . esc_html($title) . "</span></a>";
            }
        } elseif ($depth === 2) {
            $output .= "{$indent}<li><a href=\"{$url}\" class=\"header-submenu__link\">" . esc_html($title) . "</a>";
        } else {
            $output .= "{$indent}<li><a href=\"{$url}\">" . esc_html($title) . "</a>";
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Mobile Header Navigation Walker
 */
class Belan_Mobile_Nav_Walker extends Walker_Nav_Menu {

    private $parent_has_grandchildren = false;

    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if (!empty($children_elements[$element->ID])) {
            $element->has_children = true;
            $has_grandchildren = false;
            foreach ($children_elements[$element->ID] as $child) {
                if (!empty($children_elements[$child->ID])) {
                    $has_grandchildren = true;
                    break;
                }
            }
            $element->has_grandchildren = $has_grandchildren;
        } else {
            $element->has_children = false;
            $element->has_grandchildren = false;
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        if ($depth === 0) {
            $output .= "\n{$indent}<div class=\"header-mobile-dropdown__submenu\">\n";
            if (!$this->parent_has_grandchildren) {
                $output .= "{$indent}\t<ul class=\"header-mobile-dropdown__sublist\">\n";
            }
        } elseif ($depth === 1) {
            $output .= "\n{$indent}\t<ul class=\"header-mobile-dropdown__sublist\">\n";
        } else {
            $output .= "\n{$indent}<ul class=\"sub-menu\">\n";
        }
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        if ($depth === 0) {
            if (!$this->parent_has_grandchildren) {
                $output .= "{$indent}\t</ul>\n";
            }
            $output .= "{$indent}</div>\n";
        } elseif ($depth === 1) {
            $output .= "{$indent}\t</ul>\n";
        } else {
            $output .= "{$indent}</ul>\n";
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $url   = !empty($item->url) ? esc_url($item->url) : '#';
        $title = apply_filters('the_title', $item->title, $item->ID);

        if ($depth === 0) {
            if (!empty($item->has_children)) {
                $this->parent_has_grandchildren = !empty($item->has_grandchildren);
                $output .= "{$indent}<li class=\"header-mobile-dropdown__group\">\n";
                $output .= "{$indent}\t<div class=\"header-mobile-dropdown__toggle\">\n";
                $output .= "{$indent}\t\t<a class=\"header-mobile-dropdown__item\" href=\"{$url}\">" . esc_html($title) . "</a>\n";
                $output .= "{$indent}\t\t<button type=\"button\" class=\"header-mobile-dropdown__arrow-btn\" aria-label=\"Развернуть " . esc_attr($title) . "\">\n";
                $output .= "{$indent}\t\t\t<svg width=\"10\" height=\"6\" viewBox=\"0 0 10 6\" fill=\"none\">\n";
                $output .= "{$indent}\t\t\t\t<path d=\"M1 1L5 5L9 1\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n";
                $output .= "{$indent}\t\t\t</svg>\n";
                $output .= "{$indent}\t\t</button>\n";
                $output .= "{$indent}\t</div>\n";
            } else {
                $output .= "{$indent}<li><a class=\"header-mobile-dropdown__item\" href=\"{$url}\">" . esc_html($title) . "</a>";
            }
        } elseif ($depth === 1) {
            if (!empty($item->has_children)) {
                $output .= "{$indent}<div class=\"header-mobile-dropdown__category\">\n";
                $output .= "{$indent}\t<a href=\"{$url}\" class=\"header-mobile-dropdown__category-title\">" . esc_html($title) . ":</a>";
            } else {
                $output .= "{$indent}<li><a href=\"{$url}\" class=\"header-mobile-dropdown__subitem\">" . esc_html($title) . "</a>";
            }
        } elseif ($depth === 2) {
            $output .= "{$indent}<li><a href=\"{$url}\" class=\"header-mobile-dropdown__subitem\">" . esc_html($title) . "</a>";
        } else {
            $output .= "{$indent}<li><a href=\"{$url}\">" . esc_html($title) . "</a>";
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 1 && !empty($item->has_children)) {
            $output .= "</div>\n";
        } else {
            $output .= "</li>\n";
        }
    }
}

/**
 * Footer Navigation Walker
 */
class Belan_Footer_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        // Footer menu is flat
    }
    public function end_lvl(&$output, $depth = 0, $args = null) {
        // Footer menu is flat
    }
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $url   = !empty($item->url) ? esc_url($item->url) : '#';
        $title = apply_filters('the_title', $item->title, $item->ID);
        $output .= "<li><a class=\"footer__menu__item\" href=\"{$url}\">" . esc_html($title) . "</a>";
    }
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
