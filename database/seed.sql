-- ================================================================
-- Realty Smartz Pathshala - Seed Data
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Default Administrator & HR Accounts
INSERT INTO `users` (`id`, `full_name`, `email`, `mobile`, `password_hash`, `role`, `status`, `email_verified_at`, `is_locked`, `failed_login_attempts`, `is_archived`, `created_at`)
VALUES
(1, 'Super Admin', 'admin@realtysmartzpathshala.in', '+919876543210', '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'super_admin', 'approved', NOW(), 0, 0, 0, NOW()),
(2, 'HR Manager', 'hr@realtysmartzpathshala.in', '+919876543211', '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'hr_manager', 'approved', NOW(), 0, 0, 0, NOW()),
(3, 'Reviewer Officer', 'reviewer@realtysmartzpathshala.in', '+919876543212', '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'reviewer', 'approved', NOW(), 0, 0, 0, NOW())
ON DUPLICATE KEY UPDATE `email`=`email`;

-- 2. System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Realty Smartz Pathshala', 'general'),
('site_tagline', 'Master the Art of Real Estate | India''s #1 Real Estate Training', 'general'),
('contact_email', 'info@realtysmartzpathshala.in', 'contact'),
('hr_email', 'hr@realtysmartzpathshala.in', 'contact'),
('contact_phone', '+91 9999 888 777', 'contact'),
('contact_address', 'Golf Course Road, Sector 54, Gurgaon, Haryana 122011, India', 'contact'),
('working_hours', 'Mon - Sat: 9:30 AM - 6:30 PM (Sunday Closed)', 'contact'),
('instagram_url', 'https://www.instagram.com/realtysmartz/', 'social'),
('linkedin_url', 'https://www.linkedin.com/company/realtysmartz/', 'social'),
('youtube_url', 'https://www.youtube.com/@realtysmartz', 'social'),
('smtp_host', 'smtp.gmail.com', 'email'),
('smtp_port', '587', 'email'),
('smtp_user', 'no-reply@realtysmartzpathshala.in', 'email'),
('smtp_pass', '', 'email'),
('smtp_secure', 'tls', 'email'),
('smtp_from_name', 'Realty Smartz Pathshala', 'email'),
('smtp_from_email', 'no-reply@realtysmartzpathshala.in', 'email'),
('stats_trained', '500+', 'stats'),
('stats_projects', '50+', 'stats'),
('stats_success_rate', '4.9/5', 'stats'),
('stats_placement_rate', '98%', 'stats'),
('stats_years_active', '10+ Years', 'stats'),
('stats_avg_deal', '₹2.4 Cr', 'stats'),
('doc_mandatory_photo', '1', 'documents'),
('doc_mandatory_study', '1', 'documents'),
('doc_mandatory_offer', '0', 'documents'),
('doc_mandatory_relieving', '0', 'documents'),
('doc_mandatory_cheque', '1', 'documents'),
('doc_mandatory_aadhaar', '1', 'documents'),
('doc_mandatory_pan', '1', 'documents'),
('test_order', 'after_documents', 'aptitude'),
('app_version', '1.0.0', 'system'),
('last_deployed_at', NOW(), 'system')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- 3. Aptitude Test Categories
INSERT INTO `test_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Logical Reasoning', 'logical-reasoning', 'Puzzles, sequences, analogies and analytical deductions'),
(2, 'Quantitative Aptitude', 'quantitative-aptitude', 'Percentages, ratios, profit & loss, speed & area estimations'),
(3, 'English & Communication', 'english-verbal', 'Grammar, vocabulary, comprehension and professional email etiquette'),
(4, 'Real Estate Basics', 'real-estate-basics', 'Terminology, RERA guidelines, carpet vs super area, property documentation'),
(5, 'Sales & Client Negotiation', 'sales-negotiation', 'Client objection handling, lead qualification, closing psychology')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 4. Sample Question Bank (5 per category = 25 questions)
INSERT INTO `questions` (`id`, `category_id`, `question_text`, `explanation`, `marks`, `negative_marks`, `is_active`) VALUES
-- Category 1: Logical Reasoning
(1, 1, 'In a sales team, Aman is taller than Bharat but shorter than Chetan. Dinesh is taller than Aman but shorter than Chetan. Who is the tallest?', 'Chetan is taller than Aman, and Chetan is taller than Dinesh. Dinesh is taller than Aman who is taller than Bharat. Therefore, Chetan is the tallest.', 1, 0.25, 1),
(2, 1, 'Complete the pattern sequence: 4, 9, 19, 39, 79, ?', 'Each step is: (x * 2) + 1. So 79 * 2 + 1 = 159.', 1, 0.25, 1),
(3, 1, 'Statement: "All luxury projects in Gurgaon have clubhouses. Project X has a clubhouse." Conclusion: Project X is definitely a luxury project in Gurgaon.', 'False. Non-luxury projects can also have clubhouses. It is an illicit conversion.', 1, 0.25, 1),
(4, 1, 'Pointing to a portrait of a builder, a consultant said, "His mother is the only daughter of my mother." How is the consultant related to the builder?', 'The only daughter of my mother is the consultant herself (or sister). So the consultant is the mother.', 1, 0.25, 1),
(5, 1, 'If GURGAON is coded as HVSJBPO, how will NOIDA be coded in the same pattern?', 'Each character is shifted forward by +1. N->O, O->P, I->J, D->E, A->B. So OPJEB.', 1, 0.25, 1),

-- Category 2: Quantitative Aptitude
(6, 2, 'A luxury apartment was purchased for ₹2.00 Crore and sold for ₹2.40 Crore after 1 year. What is the profit percentage?', 'Profit = 0.40 Crore. Profit % = (0.40 / 2.00) * 100 = 20%.', 1, 0.25, 1),
(7, 2, 'If 1 square yard is equal to 9 square feet, how many square feet are in a 250 sq. yard plot in Pataudi?', '250 * 9 = 2250 square feet.', 1, 0.25, 1),
(8, 2, 'A real estate broker charges 1% commission on a deal closed at ₹3.50 Crore. How much brokerage does he earn?', '1% of 3,50,00,000 = ₹3,50,000.', 1, 0.25, 1),
(9, 2, 'A buyer pays ₹60 Lakhs as 20% down payment for a villa. What is the total cost of the villa?', 'Total cost = ₹60 Lakhs / 0.20 = ₹300 Lakhs = ₹3.00 Crore.', 1, 0.25, 1),
(10, 2, 'The ratio of residential to commercial plots in a township is 5:2. If there are 140 commercial plots, how many residential plots are there?', '2 units = 140, so 1 unit = 70. Residential = 5 * 70 = 350.', 1, 0.25, 1),

-- Category 3: English & Verbal
(11, 3, 'Choose the most grammatically correct sentence for client communication:', '"We are delighted to share the exclusive brochure for your kind perusal." is the most professional and polished sentence.', 1, 0.25, 1),
(12, 3, 'Select the synonym for "EXPEDITE":', 'Expedite means to make an action or process happen sooner or be accomplished more quickly (Accelerate).', 1, 0.25, 1),
(13, 3, 'Fill in the blank: The client was impressed _____ the transparent documentation process.', 'The correct preposition with impressed is "with" or "by". "by" fits standard professional usage here.', 1, 0.25, 1),
(14, 3, 'Choose the word opposite in meaning to "AFFLUENT":', 'Affluent means wealthy or prosperous. The opposite is Impoverished / Poor.', 1, 0.25, 1),
(15, 3, 'Identify the correctly spelled word:', 'Conveyance is spelled C-O-N-V-E-Y-A-N-C-E.', 1, 0.25, 1),

-- Category 4: Real Estate Basics
(16, 4, 'Under RERA (Real Estate Regulatory Authority), developers are legally required to sell properties based on which area?', 'RERA mandates that all apartments must be priced and sold exclusively on Carpet Area.', 1, 0.25, 1),
(17, 4, 'What does the term "Carpet Area" mean in real estate?', 'The net usable floor area of an apartment, excluding the area covered by external walls but including internal partition walls.', 1, 0.25, 1),
(18, 4, 'What is the full form of RERA in the Indian real estate market?', 'Real Estate (Regulation and Development) Act.', 1, 0.25, 1),
(19, 4, 'What is an "Encumbrance Certificate" used for?', 'It proves that a property is free from any legal, monetary, or mortgage liabilities and has a clear title.', 1, 0.25, 1),
(20, 4, 'What document is officially granted by municipal authorities certifying that a building is safe and fit for living?', 'Occupancy Certificate (OC).', 1, 0.25, 1),

-- Category 5: Sales & Negotiation
(21, 5, 'When a prospective high-net-worth buyer says, "The price per sq.ft is too high," what is the best first response?', 'Acknowledge the concern, ask what they are comparing it against, and present the unmatched lifestyle, location, and appreciation value.', 1, 0.25, 1),
(22, 5, 'In the sales pipeline, what does "Lead Qualification" mean?', 'Evaluating if a prospect has the budget, authority, need, and timeline (BANT) to make a purchase decision.', 1, 0.25, 1),
(23, 5, 'What is the primary objective of a property Site Visit?', 'To transition intellectual interest into an emotional connection and confidence to advance to booking.', 1, 0.25, 1),
(24, 5, 'What is a "warm lead" in real estate sales?', 'A client who has explicitly expressed interest or requested a callback or site visit regarding a specific project.', 1, 0.25, 1),
(25, 5, 'What is the most effective way to close a hesitant buyer concerned about future price drops?', 'Show historical micro-market appreciation trends, limited inventory in that tower, and upcoming infrastructure drivers.', 1, 0.25, 1)
ON DUPLICATE KEY UPDATE `question_text`=VALUES(`question_text`);

-- 5. Options for Questions
INSERT INTO `options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(1, 'Aman', 0, 1), (1, 'Bharat', 0, 2), (1, 'Chetan', 1, 3), (1, 'Dinesh', 0, 4),
(2, '149', 0, 1), (2, '159', 1, 2), (2, '160', 0, 3), (2, '179', 0, 4),
(3, 'Definitely True', 0, 1), (3, 'Definitely False / Invalid deduction', 1, 2), (3, 'Partially True', 0, 3), (3, 'Data Insufficient', 0, 4),
(4, 'Mother', 1, 1), (4, 'Sister', 0, 2), (4, 'Aunt', 0, 3), (4, 'Daughter', 0, 4),
(5, 'OPJEB', 1, 1), (5, 'OPJDC', 0, 2), (5, 'PNJEB', 0, 3), (5, 'OPIEB', 0, 4),

(6, '15%', 0, 1), (6, '20%', 1, 2), (6, '25%', 0, 3), (6, '40%', 0, 4),
(7, '2000 sq.ft', 0, 1), (7, '2250 sq.ft', 1, 2), (7, '2500 sq.ft', 0, 3), (7, '1800 sq.ft', 0, 4),
(8, '₹35,000', 0, 1), (8, '₹1,50,000', 0, 2), (8, '₹3,50,000', 1, 3), (8, '₹35,00,000', 0, 4),
(9, '₹2.50 Crore', 0, 1), (9, '₹3.00 Crore', 1, 2), (9, '₹3.60 Crore', 0, 3), (9, '₹4.00 Crore', 0, 4),
(10, '250', 0, 1), (10, '300', 0, 2), (10, '350', 1, 3), (10, '400', 0, 4),

(11, 'We are delighted to share the exclusive brochure for your kind perusal.', 1, 1),
(11, 'We are delighting in giving you the brochure to look.', 0, 2),
(11, 'Here brochure is attached, see it fast.', 0, 3),
(11, 'Brochure sending to you kindly look.', 0, 4),
(12, 'Delay', 0, 1), (12, 'Accelerate', 1, 2), (12, 'Complicate', 0, 3), (12, 'Cancel', 0, 4),
(13, 'with', 0, 1), (13, 'by', 1, 2), (13, 'at', 0, 3), (13, 'on', 0, 4),
(14, 'Wealthy', 0, 1), (14, 'Luxurious', 0, 2), (14, 'Impoverished', 1, 3), (14, 'Generous', 0, 4),
(15, 'Conveyance', 1, 1), (15, 'Convayance', 0, 2), (15, 'Convaence', 0, 3), (15, 'Conveiyance', 0, 4),

(16, 'Super Built-up Area', 0, 1), (16, 'Carpet Area', 1, 2), (16, 'Plinth Area', 0, 3), (16, 'Plot Area', 0, 4),
(17, 'Net usable floor area inside walls', 1, 1), (17, 'Balcony + parking area', 0, 2), (17, 'Elevator and staircase space', 0, 3), (17, 'Total building boundary', 0, 4),
(18, 'Real Estate (Regulation and Development) Act', 1, 1), (18, 'Regional Estate Rights Authority', 0, 2), (18, 'Real Estate Revenue Agency', 0, 3), (18, 'Residential Estate Registration Association', 0, 4),
(19, 'Certifies electricity connection', 0, 1), (19, 'Certifies property is free from legal liabilities and mortgages', 1, 2), (19, 'Calculates annual property tax', 0, 3), (19, 'Approves architectural blueprint', 0, 4),
(20, 'Commencement Certificate (CC)', 0, 1), (20, 'Occupancy Certificate (OC)', 1, 2), (20, 'NOC from Fire Department', 0, 3), (20, 'Possession Intimation Letter', 0, 4),

(21, 'Immediately offer a 10% discount', 0, 1),
(21, 'Acknowledge the price, ask what they are comparing it against, and demonstrate unique value/specifications', 1, 2),
(21, 'Argue that real estate is always expensive', 0, 3),
(21, 'End the discussion and call another prospect', 0, 4),
(22, 'Assessing budget, decision authority, need, and buying timeline', 1, 1),
(22, 'Getting the client to pay the advance immediately', 0, 2),
(22, 'Sending spam emails to 500 contacts daily', 0, 3),
(22, 'Checking if the client is active on WhatsApp', 0, 4),
(23, 'To show the building and emotionally connect the client to their future home/investment', 1, 1),
(23, 'To waste an afternoon', 0, 2),
(23, 'Only to take photos for social media', 0, 3),
(23, 'To collect parking charges', 0, 4),
(24, 'A client who has expressed explicit interest and asked for detailed project information', 1, 1),
(24, 'A random phone number from a telephone directory', 0, 2),
(24, 'A client who rejected all proposals', 0, 3),
(24, 'A previous employee of the company', 0, 4),
(25, 'Show micro-market appreciation trends, limited inventory, and upcoming civic infrastructure', 1, 1),
(25, 'Promise guaranteed doubling of money in 6 months', 0, 2),
(25, 'Tell them prices will crash next week', 0, 3),
(25, 'Ask them to make a quick guess', 0, 4);

-- 6. Default Test Configuration
INSERT INTO `tests` (`id`, `title`, `description`, `total_questions`, `time_limit_minutes`, `passing_percentage`, `negative_marking_per_question`, `randomize_questions`, `randomize_options`, `test_order`, `is_active`)
VALUES (1, 'Realty Smartz Core Onboarding Aptitude & Domain Assessment', 'Comprehensive 25-question evaluation covering logical deductions, quantitative calculations, verbal communication, real estate regulations, and sales closing techniques.', 25, 30, 60.00, 0.25, 1, 1, 'after_documents', 1)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 7. Seed Real Estate Projects (Matching realtysmartzpathshala.in)
INSERT INTO `projects` (`id`, `title`, `slug`, `category`, `location`, `price_range`, `type_config`, `description`, `highlights`, `cover_image`, `is_featured`, `is_published`, `order_num`) VALUES
(1, 'Shapoorji Pallonji', 'shapoorji-pallonji-gurgaon', 'featured', 'Gurgaon, Sector 102', '₹3.20 Cr+', '3 BHK & 4 BHK Luxury Residences', 'An ultra-luxurious residential landmark featuring signature architecture, expansive green views, and world-class sports amenities on the Dwarka Expressway.', '["Ultra-luxury clubhouse", "Olympic size swimming pool", "Air purification system", "Direct Dwarka Expressway connectivity"]', '/assets/img/projects/shapoorji.jpg', 1, 1, 1),
(2, 'DLF Privana', 'dlf-privana-sector-76', 'luxury', 'Sector 76, Gurgaon', '₹6.50 Cr+', '4 BHK Ultra Luxury High-Rise', 'DLF Privana represents the pinnacle of luxury living in Gurgaon, nested amidst the scenic Aravalli hills with expansive low-density planning and bespoke master craftsmanship.', '["Nestled near Aravalli green corridor", "Grand triple-height lobbies", "Low density 4-to-a-core layout", "Private elevators"]', '/assets/img/projects/dlf-privana.jpg', 1, 1, 2),
(3, 'Signature Global Daxin', 'signature-global-daxin-sohna', 'new', 'Sohna, South Gurgaon', '₹1.15 Cr+', 'Premium Independent Floors & 3 BHK', 'Strategically situated on the Sohna Elevated Corridor, offering serene foothill tranquility with immediate access to Cyber City and Rajiv Chowk.', '["Sohna elevated corridor 15 mins to Rajiv Chowk", "Zero pollution living", "Exclusive basement & terrace rights", "Dedicated car parking"]', '/assets/img/projects/signature-global.jpg', 1, 1, 3),
(4, 'Heritage Plots Pataudi', 'heritage-plots-pataudi', 'new', 'Pataudi Road, Gurgaon', '₹65 Lakh+', 'Freehold Residential Plotted Development', 'Gated township with wide internal boulevards, underground cabling, lush fruit orchards, and lucrative appreciation along the growth corridor.', '["100% Freehold RERA approved plots", "Gated security with CCTV surveillance", "Clubhouse & jogging tracks", "Proximity to upcoming orbital rail corridor"]', '/assets/img/projects/heritage-plots.jpg', 0, 1, 4),
(5, 'Omaxe The State', 'omaxe-the-state-dwarka', 'featured', 'Sector 19B, Dwarka, Delhi', '₹85 Lakh+', 'Integrated Commercial, Sports Arena & Retail', 'North India''s largest commercial-cum-sports destination adjacent to Yashobhoomi Convention Center and Bharat Vandana Park.', '["Adjacent to Asia''s largest convention center", "ICC standard cricket stadium", "High footfall retail high street", "International food court & 5-star club"]', '/assets/img/projects/omaxe.jpg', 1, 1, 5),
(6, 'Ganga Realty Fusion 85', 'ganga-realty-sector-85', 'luxury', 'Sector 85, Gurgaon', '₹1.80 Cr+', '3 BHK & 4 BHK Pure Luxury Towers', 'Architectural marvel combining futuristic smart-home automation with AI-powered security, private deck gardens, and infinity sky lounges.', '["AI-powered smart residential towers", "Infinity sky pool overlooking skyline", "Zero vehicular movement on ground level", "Near proposed metro station"]', '/assets/img/projects/ganga-realty.jpg', 1, 1, 6),
(7, 'Trehan Iris Broadway', 'trehan-sector-35-gurgaon', 'featured', 'Sector 35, Sohna Road', '₹95 Lakh+', 'Commercial High Street & Office Spaces', 'High-street retail destination capturing over 50,000 captive families with prime road frontage and double-height showroom units.', '["3-side open corner plot", "Anchored by national hypermarket", "Dedicated entertainment zone with 6-screen multiplex", "Assured high rental yield"]', '/assets/img/projects/trehan.jpg', 0, 1, 7),
(8, 'Dubai Luxury Projects', 'dubai-luxury-residences-uae', 'intl', 'Downtown & Business Bay, UAE', 'AED 1.2M+ (₹2.7 Cr+)', 'Waterfront Apartments & Sky Villas', 'High-ROI offshore investment portfolio with 0% income tax, 10-year Golden Visa eligibility, and rental yields averaging 8-10% tax-free.', '["10-Year UAE Golden Visa eligibility", "Tax-free rental yields up to 10%", "Private beach & marina promenade", "Flexible 80/20 post-handover payment plan"]', '/assets/img/projects/dubai.jpg', 1, 1, 8),
(9, 'M3M Capital', 'm3m-capital-dwarka-expressway', 'luxury', 'Sector 113, Dwarka Expressway', '₹2.80 Cr+', 'Golf-Themed Luxury 3.5 & 4.5 BHK', '0 km from Delhi border, overlooking 60-meter wide arterial boulevards with private golf putting greens, sky club, and valet concierge.', '["Zero km from Delhi border", "Golf-inspired landscaped gardens", "Huge 75,000 sq.ft luxury clubhouse", "Multi-tier security protocol"]', '/assets/img/projects/m3m.jpg', 0, 1, 9),
(10, 'Danube Properties Dubai', 'danube-properties-maritime-city', 'intl', 'Maritime City & JLT, Dubai', 'AED 950K+ (₹2.1 Cr+)', 'Fully Furnished Designer Waterfront Suites', 'Fully furnished luxury studio to 3 BHK suites with over 40 resort-grade amenities and Danube''s hallmark 1% per month payment plan.', '["Famous 1% per month payment plan", "Doctor on call & private infinity pool in apartment", "100% freehold foreign ownership", "Handover with Italian designer furnishings"]', '/assets/img/projects/danube.jpg', 0, 1, 10)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 8. Seed Blog Categories & Posts
INSERT INTO `blog_categories` (`id`, `name`, `slug`) VALUES
(1, 'Career & Training', 'career-training'),
(2, 'Market Trends', 'market-trends'),
(3, 'Sales Mastery', 'sales-mastery'),
(4, 'Legal & Regulations', 'legal-regulations')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `author_name`, `reading_time_minutes`, `is_published`) VALUES
(1, 1, 'Why Real Estate Sales is India''s Highest Earning Career in 2026', 'why-real-estate-sales-is-indias-highest-earning-career', 'Discover why real estate consultation in Gurgaon and NCR is generating record seven-figure compensation packages for trained young professionals.', '<p>The Indian real estate sector is undergoing an unprecedented institutional revolution. With Gurgaon remaining the powerhouse of luxury housing and commercial absorption, skilled advisors are closing multi-crore transactions monthly.</p><p>At <strong>Realty Smartz Pathshala</strong>, we bridge the critical gap between academic education and battlefield sales mastery. Our candidates learn live market dynamics, investor psychographics, and relationship-driven closing techniques that guarantee exceptional career trajectories.</p>', 'Realty Smartz Editorial', 5, 1),
(2, 3, '5 Core Closing Skills Every Luxury Property Consultant Needs', '5-core-closing-skills-every-luxury-property-consultant-needs', 'Explore the psychological and consultative strategies that turn casual inquiries into multi-crore asset purchases.', '<p>Closing ultra-luxury real estate deals requires far more than reciting floor plans and price lists. High Net Worth Individuals (HNWIs) purchase based on confidentiality, prestige, lifestyle enhancement, and wealth preservation.</p><p>Key skills include consultative listening, deep micro-market price knowledge, objection de-escalation, and creating authentic urgency without sounding desperate.</p>', 'Chief Sales Mentor', 4, 1),
(3, 4, 'Understanding Circle Rates, RERA, and Carpet Area Calculations', 'understanding-circle-rates-rera-carpet-area-calculations', 'A definitive guide for real estate candidates to master property documentation and legal transparency.', '<p>A credible real estate advisor is first and foremost a trusted legal advisor. Understanding the fine difference between carpet area, plinth area, and super built-up area ensures you protect your clients from misguidance and build lifelong goodwill.</p>', 'Legal Compliance Team', 6, 1)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 9. Seed Videos
INSERT INTO `videos` (`id`, `title`, `youtube_url`, `youtube_id`, `description`, `is_active`, `order_num`) VALUES
(1, 'Inside Realty Smartz Pathshala: Transforming Careers in Real Estate', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Experience how our classroom training, live site exposures, and senior mentorship empower fresh graduates and professionals.', 1, 1),
(2, 'Gurgaon Real Estate Market Masterclass 2026', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Deep dive into micro-market trends along Dwarka Expressway, Golf Course Extension, and Southern Peripheral Road.', 1, 2)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 10. Seed FAQs
INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `order_num`, `is_active`) VALUES
(1, 'Who is eligible to enroll in Realty Smartz Pathshala?', 'Any enthusiastic graduate, final-year student, or working professional passionate about high-earning sales careers in real estate can register. Freshers are warmly welcome with our tailored foundation modules.', 'onboarding', 1, 1),
(2, 'What documents do I need to complete my onboarding profile?', 'You will need: a passport size photograph, Aadhaar card, PAN card, highest educational degree/mark sheet, cancelled cheque or bank passbook copy, and (for experienced candidates) your previous offer letter and relieving letter.', 'onboarding', 2, 1),
(3, 'What happens if my document upload fails or is rejected?', 'If a document is unclear or incorrect, our review team will notify you with specific guidance. You can simply log into your portal and re-upload only that requested document without re-doing the whole profile.', 'documents', 3, 1),
(4, 'How does the Aptitude Test work?', 'The test consists of 25 multiple-choice questions over 30 minutes, covering logic, quant, communication, and real estate awareness. It includes anti-cheat protection. You can review your scorecard immediately upon submission.', 'aptitude', 4, 1),
(5, 'Is placement assistance provided after clearing onboarding?', 'Yes! 98% of candidates who complete the onboarding and Pathshala training program are placed directly in high-performing sales teams with leading developers and consultancy firms in Gurgaon.', 'placement', 5, 1)
ON DUPLICATE KEY UPDATE `question`=VALUES(`question`);

-- 11. Initial Migration Record
INSERT INTO `migrations` (`migration_name`, `executed_at`)
VALUES ('001_create_initial_schema.sql', NOW())
ON DUPLICATE KEY UPDATE `migration_name`=`migration_name`;

SET FOREIGN_KEY_CHECKS = 1;
