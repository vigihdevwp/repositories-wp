<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_Post;
use WP_Term;
use WP_User;

final class PostsRepository
{
    private ?WP_Post $post;

    /**
     * Mengambil post secara acak
     *
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     */
    public static function randomPosts(int $limit = 7, array $args = []): array
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
     * Mengambil post terbaru
     *
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     */
    public static function recentPosts(int $limit = 7, array $args = []): array
    {
        $defaults = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $limit,
        ];

        $query = new \WP_Query(array_merge($defaults, $args));

        return $query->posts;
    }

    public function __construct()
    {
        global $post;
        $this->post = $post;
    }

    /**
     * Mengambil post terkait (memerlukan post context)
     *
     * @param int $limit Jumlah post yang diambil
     * @param array $args Additional WP_Query arguments
     * @return WP_Post[] Array of WP_Post objects
     * @throws \RuntimeException Jika tidak ada post context
     */
    public function relatedPosts(int $limit = 7, array $args = []): array
    {
        if (!$this->post) {
            throw new \RuntimeException('Post context diperlukan untuk related posts');
        }

        $defaults = [
            'post_type' => $this->post->post_type,
            'post_status' => $this->post->post_status,
            'posts_per_page' => $limit,
            'post__not_in' => [$this->post->ID],
            'category__in' => wp_get_post_categories($this->post->ID),
        ];

        $query = new \WP_Query(array_merge($defaults, $args));

        return $query->posts;
    }

    /**
     * Mengambil daftar kategori berdasarkan ID post
     *
     * @return WP_Term[] Daftar kategori dalam format array objek WP_Term
     */
    public function getCategories(): array
    {

        // Mengambil kategori menggunakan WordPress API
        $categories = wp_get_post_categories($this->post->ID, ['fields' => 'all']);

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
     * @return WP_Term[] Daftar taksonomi dalam format array objek WP_Term
     */
    public function getTaxonomies(): array
    {
        // Mengambil semua taxonomy terms yang terkait dengan post
        $taxonomies = [];

        // Dapatkan semua registered taxonomies
        $registered_taxonomies = get_taxonomies(['object_type' => [$this->post->post_type]]);

        foreach ($registered_taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($this->post->ID, $taxonomy);

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

    /**
     * Mendapatkan author dari post
     *
     * @return WP_User|null Instance WP_User object jika author ditemukan, null jika tidak
     */
    public function getAuthor(): ?WP_User
    {
        $authorId = $this->post->post_author;

        if ($authorId <= 0) {
            return null;
        }

        $user = get_user_by('ID', $authorId);

        if (!$user) {
            return null;
        }

        return $user;
    }
}
