<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_Term;

final class CategoryRepository
{
    public function __construct() {}

    /**
     * Mendapatkan semua kategori
     *
     * @return WP_Term[] Daftar kategori dalam format array objek WP_Term
     */
    public function all(): array
    {
        return get_categories();
    }

    /**
     * Mendapatkan kategori berdasarkan ID
     *
     * @return WP_Term|null Instance WP_Term object jika kategori ditemukan, null jika tidak
     */
    public function find(int $id): ?WP_Term
    {
        $category = get_category($id);
        return $category instanceof WP_Term ? $category : null;
    }

    /**
     * Mengambil daftar kategori berdasarkan ID post
     *
     * @return WP_Term[] Daftar kategori dalam format array objek WP_Term
     */
    public function byPost(int $postId): array
    {
        return get_the_category($postId);
    }
}
