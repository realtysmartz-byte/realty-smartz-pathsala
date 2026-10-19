<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Project {
    public static function getAllPublished(?string $category = null): array {
        try {
            $db = Database::getConnection();

            $sql = "SELECT * FROM `projects` WHERE `is_published` = 1 AND `is_archived` = 0";
            $params = [];

            if ($category && $category !== 'all') {
                $sql .= " AND `category` = :cat";
                $params[':cat'] = $category;
            }

            $sql .= " ORDER BY `order_num` ASC, `id` DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $projects = $stmt->fetchAll();

            foreach ($projects as &$p) {
                $p['highlights_array'] = !empty($p['highlights']) ? json_decode($p['highlights'], true) : [];
                if (!is_array($p['highlights_array'])) {
                    $p['highlights_array'] = array_filter(array_map('trim', explode("\n", $p['highlights'] ?? '')));
                }
            }

            return $projects;
        } catch (\Throwable $e) {
            return [
                ['id' => 1, 'title' => 'Shapoorji Pallonji Joyville', 'slug' => 'shapoorji-pallonji-gurgaon', 'developer' => 'Shapoorji Pallonji', 'location' => 'Sector 102, Dwarka Expressway, Gurgaon', 'price_range' => '₹3.2 Cr Onwards', 'property_type' => '3BHK & 4BHK Luxury Residences', 'category' => 'luxury', 'is_featured' => 1, 'highlights_array' => ['Ultra-luxury clubhouse', 'Dwarka expressway prime access', 'High capital appreciation corridor']],
                ['id' => 2, 'title' => 'DLF Privana South', 'slug' => 'dlf-privana-sector-76', 'developer' => 'DLF India', 'location' => 'Sector 76-77, Southern Peripheral Road, Gurgaon', 'price_range' => '₹6.5 Cr Onwards', 'property_type' => 'Ultra Luxury 4BHK Apartments', 'category' => 'luxury', 'is_featured' => 1, 'highlights_array' => ['Aravalli hill range views', 'Iconic gated township', 'DLF legacy architecture']],
                ['id' => 3, 'title' => 'Signature Global Daxin Vistas', 'slug' => 'signature-global-daxin-sohna', 'developer' => 'Signature Global', 'location' => 'Sohna Road, South of Gurgaon', 'price_range' => '₹1.4 Cr Onwards', 'property_type' => 'Premium Floors & Plots', 'category' => 'new_launch', 'is_featured' => 1, 'highlights_array' => ['Delhi-Mumbai expressway corridor', 'Low-rise luxury floors', 'High rental demand area']],
                ['id' => 4, 'title' => 'Heritage Plots Pataudi', 'slug' => 'heritage-plots-pataudi', 'developer' => 'Heritage Group', 'location' => 'Pataudi Road, NCR Gurgaon', 'price_range' => '₹65 Lakh Onwards', 'property_type' => 'Gated Residential Plots', 'category' => 'featured', 'is_featured' => 1, 'highlights_array' => ['Freehold registered plots', 'Rapid industrial hub vicinity', 'Instant possession readiness']],
                ['id' => 5, 'title' => 'Omaxe The State', 'slug' => 'omaxe-the-state-dwarka', 'developer' => 'Omaxe Group', 'location' => 'Sector 19B, Dwarka, Delhi NCR', 'price_range' => '₹75 Lakh Onwards', 'property_type' => 'Commercial Retail & Sports Complex', 'category' => 'commercial', 'is_featured' => 1, 'highlights_array' => ['India’s first integrated sports city commercial', 'Massive footfall hub', 'Guaranteed high leasing yields']],
                ['id' => 6, 'title' => 'Ganga Realty Fusion 85', 'slug' => 'ganga-realty-sector-85', 'developer' => 'Ganga Realty', 'location' => 'Sector 85, New Gurgaon', 'price_range' => '₹1.8 Cr Onwards', 'property_type' => '3BHK & 4BHK Resort Residences', 'category' => 'featured', 'is_featured' => 1, 'highlights_array' => ['Pure AI smart homes', 'Wrap-around panoramic sundecks', 'Direct connectivity to NH-48']],
                ['id' => 7, 'title' => 'Trehan Luxury Floors 35', 'slug' => 'trehan-sector-35', 'developer' => 'Trehan Home Developers', 'location' => 'Sector 35, Sohna, South Gurgaon', 'price_range' => '₹98 Lakh Onwards', 'property_type' => 'Independent Luxury Floors', 'category' => 'new_launch', 'is_featured' => 0, 'highlights_array' => ['Private terrace rights', 'Dedicated basement storage', 'Zero maintenance design']],
                ['id' => 8, 'title' => 'Sobha Hartland II Dubai', 'slug' => 'dubai-projects-sobha', 'developer' => 'Sobha Realty', 'location' => 'Mohammed Bin Rashid City, Dubai, UAE', 'price_range' => 'AED 1.8M (₹4.1 Cr+)', 'property_type' => 'Waterfront 1, 2 & 3 BR Residences', 'category' => 'international', 'is_featured' => 1, 'highlights_array' => ['Tax-free rental yields 8-10%', 'Golden visa eligibility', 'Crystal lagoon waterfront views']],
                ['id' => 9, 'title' => 'M3M Crown & Altitude', 'slug' => 'm3m-luxury-gurgaon', 'developer' => 'M3M India', 'location' => 'Sector 111, Dwarka Expressway, Gurgaon', 'price_range' => '₹2.8 Cr Onwards', 'property_type' => 'High-Rise Golf Residences', 'category' => 'luxury', 'is_featured' => 0, 'highlights_array' => ['0 km from Delhi border', 'Lake facing green spaces', 'Ultra-chic contemporary glass facades']],
                ['id' => 10, 'title' => 'Danube Diamondz & Oceanz', 'slug' => 'danube-properties-dubai', 'developer' => 'Danube Properties', 'location' => 'Jumeirah Lakes Towers (JLT), Dubai', 'price_range' => 'AED 1.1M (₹2.5 Cr+)', 'property_type' => 'Fully Furnished Luxury Apartments', 'category' => 'international', 'is_featured' => 1, 'highlights_array' => ['1% per month post-handover payment plan', 'High capital appreciation', 'Over 40 world-class amenities']]
            ];
        }
    }

    public static function findBySlug(string $slug): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM `projects` WHERE `slug` = :slug AND `is_archived` = 0 LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $project = $stmt->fetch();

            if ($project) {
                $project['highlights_array'] = !empty($project['highlights']) ? json_decode($project['highlights'], true) : [];
                if (!is_array($project['highlights_array'])) {
                    $project['highlights_array'] = array_filter(array_map('trim', explode("\n", $project['highlights'] ?? '')));
                }

                // Fetch extra images
                $stmtImg = $db->prepare("SELECT * FROM `project_images` WHERE `project_id` = :pid ORDER BY `order_num` ASC");
                $stmtImg->execute([':pid' => $project['id']]);
                $project['gallery'] = $stmtImg->fetchAll();
            }

            return $project ?: null;
        } catch (\Throwable $e) {
            $all = self::getAllPublished();
            foreach ($all as $p) {
                if ($p['slug'] === $slug) {
                    $p['description'] = $p['description'] ?? 'Exclusive premium residential real estate development offering world-class lifestyle, exceptional connectivity, and premier capital appreciation potential.';
                    $p['gallery'] = [];
                    return $p;
                }
            }
            return null;
        }
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `projects` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `projects`
            (`title`, `slug`, `category`, `location`, `price_range`, `type_config`, `description`, `highlights`, `cover_image`, `is_featured`, `is_published`, `order_num`, `created_at`)
            VALUES (:title, :slug, :cat, :loc, :price, :type, :desc, :high, :img, :feat, :pub, :ord, NOW())");

        $stmt->execute([
            ':title' => $data['title'],
            ':slug'  => $data['slug'],
            ':cat'   => $data['category'] ?? 'featured',
            ':loc'   => $data['location'],
            ':price' => $data['price_range'],
            ':type'  => $data['type_config'],
            ':desc'  => $data['description'] ?? null,
            ':high'  => is_array($data['highlights']) ? json_encode($data['highlights']) : $data['highlights'],
            ':img'   => $data['cover_image'],
            ':feat'  => !empty($data['is_featured']) ? 1 : 0,
            ':pub'   => !empty($data['is_published']) ? 1 : 0,
            ':ord'   => (int)($data['order_num'] ?? 0)
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `projects` SET
            `title` = :title,
            `slug` = :slug,
            `category` = :cat,
            `location` = :loc,
            `price_range` = :price,
            `type_config` = :type,
            `description` = :desc,
            `highlights` = :high,
            `cover_image` = :img,
            `is_featured` = :feat,
            `is_published` = :pub,
            `order_num` = :ord
            WHERE `id` = :id");

        $stmt->execute([
            ':title' => $data['title'],
            ':slug'  => $data['slug'],
            ':cat'   => $data['category'],
            ':loc'   => $data['location'],
            ':price' => $data['price_range'],
            ':type'  => $data['type_config'],
            ':desc'  => $data['description'],
            ':high'  => is_array($data['highlights']) ? json_encode($data['highlights']) : $data['highlights'],
            ':img'   => $data['cover_image'],
            ':feat'  => !empty($data['is_featured']) ? 1 : 0,
            ':pub'   => !empty($data['is_published']) ? 1 : 0,
            ':ord'   => (int)$data['order_num'],
            ':id'    => $id
        ]);
    }
}
