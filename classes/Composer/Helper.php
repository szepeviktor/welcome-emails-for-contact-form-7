<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

class Helper {

    /**
     * Attributes:
     * - width
     * - columns
     * - padding
     * - responsive
     *
     * @param string[] $items
     * @param array $attrs
     * @return string
     */
    static function grid($items = [], $attrs = []) {
        $attrs = wp_parse_args($attrs, ['width' => 600, 'columns' => 2, 'padding' => 10, 'responsive' => true]);
        $width = (int) $attrs['width'];
        $columns = (int) $attrs['columns'];
        $padding = (int) $attrs['padding'];
        $column_width = ($width - $padding * $columns) / $columns;
        $td_width = (int) (100 / $columns);
        $chunks = array_chunk($items, $columns);

        if ($attrs['responsive']) {

            $e = '';
            foreach ($chunks as &$chunk) {
                $e .= '<div style="text-align:center;font-size:0;">';
                $e .= '<!--[if mso]><table role="presentation" width="100%"><tr><![endif]-->';
                foreach ($chunk as &$item) {
                    $e .= '<!--[if mso]><td width="' . esc_attr($td_width) . '%" style="width:' . esc_attr($td_width) . '%;padding:' . esc_attr($padding) . 'px" valign="top"><![endif]-->';

                    $e .= '<div class="m-mw-100" style="width:100%;max-width:' . esc_attr($column_width) . 'px;display:inline-block;vertical-align: top;box-sizing: border-box;">';

                    // This element to add padding without deal with border-box not well supported
                    $e .= '<div style="padding:' . esc_attr($padding) . 'px;">';
                    $e .= $item;
                    $e .= '</div>';
                    $e .= '</div>';

                    $e .= '<!--[if mso]></td><![endif]-->';
                }
                $e .= '<!--[if mso]></tr></table><![endif]-->';
                $e .= '</div>';
            }

            return $e;
        } else {
            $e = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 100%!important">';
            foreach ($chunks as &$chunk) {
                $e .= '<tr>';
                foreach ($chunk as &$item) {
                    $e .= '<td width="' . esc_attr($td_width) . '%" style="width:' . esc_attr($td_width) . '%; padding:' . esc_attr($padding) . 'px" valign="top">';
                    $e .= $item;
                    $e .= '</td>';
                }
                $e .= '</tr>';
            }
            $e .= '</table>';
            return $e;
        }
    }

}
