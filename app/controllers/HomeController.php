<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Helpers\Mailer;
use App\Helpers\Validator;

class HomeController {
    /**
     * Home Page (/)
     */
    public function index(): void {
        $videos = [];
        $faqs = [];

        try {
            $db = Database::getConnection();
            $stmtVideos = $db->query("SELECT * FROM `videos` WHERE `is_active` = 1 ORDER BY `order_num` ASC LIMIT 3");
            $videos = $stmtVideos->fetchAll();

            $stmtFaqs = $db->query("SELECT * FROM `faqs` WHERE `is_active` = 1 ORDER BY `order_num` ASC LIMIT 6");
            $faqs = $stmtFaqs->fetchAll();
        } catch (\Throwable $e) {
            $videos = [
                ['id' => 1, 'title' => 'Inside Gurgaon Luxury Real Estate Sales Pitching', 'youtube_id' => 'dQw4w9WgXcQ', 'order_num' => 1],
                ['id' => 2, 'title' => 'How to Close Multi-Crore Commercial Property Deals', 'youtube_id' => 'dQw4w9WgXcQ', 'order_num' => 2],
                ['id' => 3, 'title' => 'Realty Smartz Student Success Stories & Placement Highlights', 'youtube_id' => 'dQw4w9WgXcQ', 'order_num' => 3]
            ];
            $faqs = [
                ['id' => 1, 'question' => 'What qualifications are required to join Realty Smartz Pathshala?', 'answer' => 'We welcome graduates, final-year students, and working professionals from any discipline. Passion for high-ticket sales, strong communication, and client negotiation acumen are prioritized.'],
                ['id' => 2, 'question' => 'How does the Candidate Onboarding & Placement process work?', 'answer' => 'The onboarding process follows 5 structured steps: 1) Online Registration, 2) Email Verification, 3) Profile & KYC Document Upload, 4) Real Estate Aptitude Assessment, and 5) HR Verification & Interview Scheduling.'],
                ['id' => 3, 'question' => 'Are training sessions live or pre-recorded?', 'answer' => 'Pathshala combines rigorous classroom strategy sessions with actual on-ground site visits, developer pitch walkthroughs, and live developer sales training across Gurgaon and Dubai projects.']
            ];
        }

        // 1. Fetch featured projects for bento grid
        $projects = Project::getAllPublished();

        // 3. Fetch latest blog posts
        $latestBlogs = Blog::getPosts(1, 3);

        // 5. System Stats & Settings
        $settings = Setting::getAll();

        require APP_PATH . '/views/public/home.php';
    }

    /**
     * Projects Page (/projects)
     */
    public function projects(): void {
        $category = $_GET['cat'] ?? 'all';
        $projects = Project::getAllPublished($category);
        $settings = Setting::getAll();

        require APP_PATH . '/views/public/projects.php';
    }

    /**
     * Single Project Detail (/projects/{slug})
     */
    public function projectDetail(string $slug): void {
        $project = Project::findBySlug($slug);
        if (!$project) {
            http_response_code(404);
            require APP_PATH . '/views/public/404.php';
            return;
        }

        $relatedProjects = Project::getAllPublished($project['category']);
        $settings = Setting::getAll();

        require APP_PATH . '/views/public/project_detail.php';
    }

    /**
     * About Us Page (/about)
     */
    public function about(): void {
        $settings = Setting::getAll();
        require APP_PATH . '/views/public/about.php';
    }

    /**
     * Blog Listing (/blog)
     */
    public function blog(): void {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $categorySlug = !empty($_GET['category']) ? trim($_GET['category']) : null;
        $search = !empty($_GET['q']) ? trim($_GET['q']) : null;

        $postsData = Blog::getPosts($page, 6, $categorySlug, $search);
        $categories = Blog::getCategories();
        $settings = Setting::getAll();

        require APP_PATH . '/views/public/blog.php';
    }

    /**
     * Single Blog Post (/blog/{slug})
     */
    public function blogDetail(string $slug): void {
        $post = Blog::findBySlug($slug);
        if (!$post) {
            http_response_code(404);
            require APP_PATH . '/views/public/404.php';
            return;
        }

        $relatedPosts = Blog::getPosts(1, 3, $post['category_slug'] ?? null);
        $categories = Blog::getCategories();
        $settings = Setting::getAll();

        require APP_PATH . '/views/public/blog_detail.php';
    }

    /**
     * Contact Us Page (/contact)
     */
    public function contact(): void {
        $settings = Setting::getAll();
        require APP_PATH . '/views/public/contact.php';
    }

    /**
     * Submit Contact Form (POST /contact)
     */
    public function submitContact(): void {
        // Honeypot check for spam bots
        if (!empty($_POST['website_url'])) {
            // Bot filled out hidden honeypot field; silently respond success
            flash('success', 'Thank you! Your message has been received.');
            redirect('/contact');
            return;
        }

        // Validate fields
        $validator = Validator::make($_POST, [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'required|mobile',
            'message' => 'required|min:10|max:2000'
        ]);

        if ($validator->fails()) {
            $_SESSION['_old_input'] = $_POST;
            flash('error', $validator->first('name') ?: ($validator->first('email') ?: ($validator->first('phone') ?: $validator->first('message'))));
            redirect('/contact');
            return;
        }

        $name = trim($_POST['name']);
        $email = strtolower(trim($_POST['email']));
        $phone = preg_replace('/\D/', '', $_POST['phone']);
        $subject = trim($_POST['subject'] ?? 'General Enquiry');
        $message = trim($_POST['message']);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `enquiries` (`name`, `email`, `phone`, `subject`, `message`, `ip_address`, `created_at`)
            VALUES (:name, :email, :phone, :subj, :msg, :ip, NOW())");
        $stmt->execute([
            ':name'  => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subj'  => $subject,
            ':msg'   => $message,
            ':ip'    => $ip
        ]);

        // Email notification to HR / Admin
        $hrEmail = Setting::get('hr_email', 'hr@realtysmartzpathshala.in');
        $emailContent = "<p><strong>Name:</strong> " . e($name) . "</p>" .
                        "<p><strong>Email:</strong> " . e($email) . "</p>" .
                        "<p><strong>Phone:</strong> " . e($phone) . "</p>" .
                        "<p><strong>Subject:</strong> " . e($subject) . "</p>" .
                        "<p><strong>Message:</strong><br>" . nl2br(e($message)) . "</p>";

        Mailer::send($hrEmail, 'HR Desk', "New Website Enquiry from {$name}", Mailer::wrapLayout("New Contact Enquiry", $emailContent));

        flash('success', 'Thank you for getting in touch! Our counselling team will call you shortly.');
        redirect('/contact');
    }
}
