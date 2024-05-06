<?php

namespace Automation\Composer;

defined('ABSPATH') || exit;

$max = (int) $options['max'];
$filters['posts_per_page'] = $max;

$show_date = true;
$show_author = true;
$show_image = !empty($options['show_image']);

$posts = get_posts($filters);

foreach ($posts as $p) {
    $p->url = get_permalink($p);
    //$p->excerpt = get_the_excerpt($p);
    $p->title = get_the_title($p);
    $thumbnail_id = get_post_thumbnail_id($p);
    $p->image = '';
    if ($thumbnail_id) {
        $src = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
        $p->image = $src[0];
    }

    $p->meta = [];

    if ($show_date) {
        $p->meta[] = get_the_date('', $p);
    }

    if ($show_author) {
        $author_object = get_user_by('id', $p->post_author);
        if ($author_object) {
            $p->meta[] = $author_object->display_name;
        }
    }
}

include __DIR__ . '/layout_' . ((int) $options['layout']) . '.php';
?>
