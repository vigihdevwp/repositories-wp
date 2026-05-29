<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_Term;

final class TermRepository
{
    /**
     * Ambil semua term dari taxonomy
     * @param string $taxonomy  Taxonomy dari term
     * @return WP_Term[]  Daftar term dari taxonomy
     */
    public function all(string $taxonomy = 'category'): array
    {
        return get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);
    }

    /**
     * Cari term berdasarkan ID
     * @param int $id  ID dari term
     * @param string $taxonomy  Taxonomy dari term
     * @return WP_Term|null
     */
    public function find(int $id, string $taxonomy = 'category'): WP_Term|null
    {
        $term = get_term($id, $taxonomy);

        return $term instanceof WP_Term
            ? $term
            : null;
    }

    /**
     * Ambil term dari post
     * @param int $postId  ID dari post
     * @param string $taxonomy  Taxonomy dari term
     * @return WP_Term[]  Daftar term dari post
     */
    public function byPost(
        int $postId,
        string $taxonomy = 'category'
    ): array {
        return get_the_terms($postId, $taxonomy) ?: [];
    }

    /**
     * Cari term berdasarkan slug
     * @param string $slug  Slug dari term
     * @param string $taxonomy  Taxonomy dari term
     * @return WP_Term|null
     */
    public function bySlug(
        string $slug,
        string $taxonomy = 'category'
    ): WP_Term|null {
        $term = get_term_by('slug', $slug, $taxonomy);

        return $term instanceof WP_Term
            ? $term
            : null;
    }

    /**
     * Ambil child term
     * @param int $parentId  ID dari parent term
     * @param string $taxonomy  Taxonomy dari parent term
     * @return WP_Term[]  Daftar term child
     */
    public function children(
        int $parentId,
        string $taxonomy = 'category'
    ): array {
        return get_terms([
            'taxonomy' => $taxonomy,
            'parent'   => $parentId,
        ]);
    }
}
