<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_Post;
use WP_Term;
use WP_User;

final class PostRepository
{

    public function __construct() {}

    /**
     * Mengambil post terbaru
     *
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     */
    public function recent(int $limit = 7, array $args = []): array
    {
        $defaults = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'DESC',
            'posts_per_page' => $limit,
        ];

        $query = new \WP_Query(array_merge($defaults, $args));

        return $query->posts;
    }

    /**
     * Mengambil pengguna yang memuat post
     *
     * @param int $postId ID post yang diambil
     * @return WP_User|false Pengguna yang memuat post, atau false jika post tidak ditemukan
     */
    public function ofUser(int $postId): WP_User|false
    {
        $post = WP_Post::get_instance($postId);

        if (!$post) {
            return false;
        }

        $user = get_user_by('ID', $post->post_author);
        return $user ?: false;
    }

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
     * Mengambil daftar kategori berdasarkan ID post
     *
     * @param int $postId ID post yang diambil
     * @return WP_Term[] Daftar kategori dalam format array objek WP_Term
     */
    public function categories(int $postId): array
    {

        // Mengambil kategori menggunakan WordPress API
        $categories = wp_get_post_categories($postId, ['fields' => 'all']);

        // Jika tidak ada kategori, kembalikan array kosong
        if (empty($categories)) {
            return [];
        }

        // Konversi kategori ke objek TermDto
        $result = [];
        foreach ($categories as $category) {
            if ($category instanceof WP_Term) {
                $result[] = $category;
            }
        }

        return $result;
    }

    /**
     * Mengambil daftar taksonomi berdasarkan ID post
     *  
     * @param int $postId ID post yang diambil
     * @param string $post_type Jenis post (mis: 'post', 'page')
     * @return WP_Term[] Daftar taksonomi dalam format array objek WP_Term
     */
    public function taxonomies(int $postId, string $post_type): array
    {
        // Mengambil semua taxonomy terms yang terkait dengan post
        $taxonomies = [];

        // Dapatkan semua registered taxonomies
        $registered_taxonomies = get_taxonomies(['object_type' => [$post_type]]);

        foreach ($registered_taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($postId, $taxonomy);

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    if ($term instanceof WP_Term) {
                        $taxonomies[] = $term;
                    }
                }
            }
        }

        return $taxonomies;
    }
}
