<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
});

add_filter('the_content', function ($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        $notice = '<div style="
            background:#f6f7f7;
            border-left:4px solid #2271b1;
            padding:12px;
            margin-bottom:16px;
            font-size:14px;
        ">
            <strong>Auto Blog Demo:</strong> This post was generated using a custom WordPress plugin.
        </div>';

        return $notice . $content;
    }

    return $content;
});
