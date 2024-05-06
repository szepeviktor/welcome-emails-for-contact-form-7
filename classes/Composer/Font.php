<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

class Font {

    var $family;
    var $size;
    var $weight;
    var $color;
    var $line_height;

    /**
     * Print the CSS relative to a font, possibly scaling it.
     *
     * @param float $scale
     */
    function echo_css($scale = 1.0) {
        $scale = (float) $scale;

        echo 'font-size: ', round($this->size * $scale), 'px;';
        echo 'font-family: ', esc_html($this->family), ';';
        echo 'font-weight: ', esc_html($this->weight), ';';
        echo 'color: ', esc_html($this->color), ';';
        echo 'line-height: ', esc_html($this->line_height), ';';
    }

    /**
     * Build a Font object using the set of options in a block with the specified prefix.
     *
     * @param array $block_options
     * @param array $email_options
     * @param string $prefix
     * @return \Automation\Composer\Font
     */
    static function build($block_options, $email_options = [], $prefix = 'font_') {
        $font = new Font();

        // Removes the empty values so the defaults are used.
        $block_options = array_filter($block_options);

        $font->family = $block_options[$prefix . 'family'] ?? 'Verdana, Geneva, sans-serif';
        $font->size = (int) ($block_options[$prefix . 'size'] ?? 16);
        $font->color = $block_options[$prefix . 'color'] ?? '#444444';
        $font->weight = $block_options[$prefix . 'weight'] ?? 'normal';
        $font->line_height = $block_options[$prefix . 'line_height'] ?? 'normal';
        
        return $font;
    }
}
