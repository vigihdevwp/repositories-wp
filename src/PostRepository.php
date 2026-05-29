<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_Post;
use WP_Term;

final class PostRepository
{

    public function __construct() {}

    /**
     * Mengambil post secara acak
     *
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     */
    public function random(int $limit = 7, array $args = []): array
    {
        $defaults = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'rand',
            'posts_per_page' => $limit,
        ];

        $query = new \WP_Query(array_merge($defaults, $args));

        return $query->posts;
    }

    /**
     * Mengambil post terkait (memerlukan post context)
     *
     * @param int $postId ID post yang diambil
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     */
    public function related(int $postId, int $limit = 7, array $args = []): array
    {
        $post = WP_Post::get_instance($postId);

        if (!$post) {
            return [];
        }

        $defaults = [
            'post_type' => $post->post_type,
            'post_status' => $post->post_status,
            'posts_per_page' => $limit,
            'post__not_in' => [$post->ID],
            'category__in' => wp_get_post_categories($post->ID),
        ];

        $query = new \WP_Query(array_merge($defaults, $args));

        return $query->posts;
    }

    /**
     * Mengambil daftar kategori dari post
     * 
     * @param int $postId ID post yang diambil
     * @return WP_Term[] Array of WP_Term objects
     */
    public function categories(int $postId): array
    {
        $category = new CategoryRepository();
        return $category->byPost($postId);
    }
}
