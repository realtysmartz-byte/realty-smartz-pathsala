<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Blog {
    public static function getPosts(int $page = 1, int $perPage = 6, ?string $categorySlug = null, ?string $search = null): array {
        try {
            $db = Database::getConnection();

            $offset = ($page - 1) * $perPage;
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM `blog_posts` p
                    LEFT JOIN `blog_categories` c ON p.category_id = c.id
                    WHERE p.is_published = 1 AND p.is_archived = 0";

            $countSql = "SELECT COUNT(*) FROM `blog_posts` p
                         LEFT JOIN `blog_categories` c ON p.category_id = c.id
                         WHERE p.is_published = 1 AND p.is_archived = 0";

            $params = [];

            if ($categorySlug) {
                $sql .= " AND c.slug = :cat";
                $countSql .= " AND c.slug = :cat";
                $params[':cat'] = $categorySlug;
            }

            if (!empty($search)) {
                $sql .= " AND (p.title LIKE :search OR p.excerpt LIKE :search OR p.content LIKE :search)";
                $countSql .= " AND (p.title LIKE :search OR p.excerpt LIKE :search OR p.content LIKE :search)";
                $params[':search'] = '%' . trim($search) . '%';
            }

            $sql .= " ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset";

            $stmtCount = $db->prepare($countSql);
            foreach ($params as $k => $v) {
                $stmtCount->bindValue($k, $v);
            }
            $stmtCount->execute();
            $total = (int) $stmtCount->fetchColumn();

            $stmt = $db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'data' => $stmt->fetchAll(),
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => (int) ceil($total / $perPage)
            ];
        } catch (\Throwable $e) {
            $seedBlogs = [
                ['id' => 1, 'title' => 'Top 5 Real Estate Career Skills That Guarantee High Commission in Gurgaon', 'slug' => 'top-5-real-estate-career-skills-gurgaon', 'category_name' => 'Career Guidance', 'excerpt' => 'Discover the essential negotiation, legal compliance, and CRM practices top Gurgaon brokers use to close multi-crore luxury deals.', 'author_name' => 'Realty Smartz Faculty', 'published_at' => date('Y-m-d H:i:s'), 'cover_image' => ''],
                ['id' => 2, 'title' => 'Understanding Dwarka Expressway: The High-Yield Micro Market of 2026', 'slug' => 'understanding-dwarka-expressway-market-2026', 'category_name' => 'Market Insights', 'excerpt' => 'An in-depth analysis of capital appreciation, luxury inventory launches, and institutional hiring trends along the Dwarka Expressway corridor.', 'author_name' => 'Research Desk', 'published_at' => date('Y-m-d H:i:s', time() - 86400), 'cover_image' => ''],
                ['id' => 3, 'title' => 'How Realty Smartz Pathshala Bridges the Real Estate Talent Gap in India', 'slug' => 'how-pathshala-bridges-real-estate-talent-gap', 'category_name' => 'Training & Mentorship', 'excerpt' => 'Why traditional MBAs fall short in real-world property pitching and how 100% practical, project-based onboarding prepares day-one ready sales professionals.', 'author_name' => 'Placement Director', 'published_at' => date('Y-m-d H:i:s', time() - 172800), 'cover_image' => '']
            ];
            return [
                'data' => $seedBlogs,
                'total' => count($seedBlogs),
                'page' => 1,
                'per_page' => $perPage,
                'last_page' => 1
            ];
        }
    }

    public static function findBySlug(string $slug): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM `blog_posts` p
                LEFT JOIN `blog_categories` c ON p.category_id = c.id
                WHERE p.slug = :slug AND p.is_archived = 0 LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            return $stmt->fetch() ?: null;
        } catch (\Throwable $e) {
            $posts = self::getPosts(1, 10)['data'];
            foreach ($posts as $p) {
                if ($p['slug'] === $slug) {
                    $p['content'] = $p['content'] ?? $p['excerpt'];
                    return $p;
                }
            }
            return null;
        }
    }

    public static function getCategories(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT c.*, COUNT(p.id) as post_count
                FROM `blog_categories` c
                LEFT JOIN `blog_posts` p ON c.id = p.category_id AND p.is_published = 1 AND p.is_archived = 0
                GROUP BY c.id
                ORDER BY c.name ASC");
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [
                ['id' => 1, 'name' => 'Career Guidance', 'slug' => 'career-guidance', 'post_count' => 1],
                ['id' => 2, 'name' => 'Market Insights', 'slug' => 'market-insights', 'post_count' => 1],
                ['id' => 3, 'name' => 'Training & Mentorship', 'slug' => 'training-mentorship', 'post_count' => 1]
            ];
        }
    }
}
