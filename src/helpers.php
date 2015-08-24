<?php

if (! function_exists('build_html_attributes')) {
    /**
     * @param array $attributes
     * @return string
     */
    function build_html_attributes(array $attributes)
    {
        $html = '';

        if(count($attributes) > 0) {
            $html .= ' ';
        }

        foreach ($attributes as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return rtrim($html);
    }
}