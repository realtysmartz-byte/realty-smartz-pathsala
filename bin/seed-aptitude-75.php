<?php
/**
 * Seeder Script for 75 Curated Aptitude Assessment Questions
 * 5 Sets (Set A, B, C, D, E) x 15 Questions = 75 Total Questions
 * Each Set:
 *   - 3 Logical Reasoning (Cat 1)
 *   - 3 Quantitative Aptitude (Cat 2)
 *   - 3 Verbal Ability / English (Cat 3)
 *   - 3 Real Estate Basics (Cat 4)
 *   - 3 Sales & Negotiation Aptitude (Cat 5)
 */

define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

use App\Config\Database;

$db = Database::getConnection();

echo "========================================================\n";
echo "SEEDING 75 QUESTIONS ACROSS 5 SETS (15 QUESTIONS / SET)\n";
echo "========================================================\n\n";

// 1. Ensure tests config id = 1 is configured for 10 mins and 15 questions
$db->exec("
    INSERT INTO `tests` (`id`, `title`, `description`, `total_questions`, `time_limit_minutes`, `passing_percentage`, `negative_marking_per_question`, `randomize_questions`, `randomize_options`, `test_order`, `is_active`, `is_archived`, `created_at`)
    VALUES (1, 'Realty Smartz Pathshala Aptitude Assessment', 'Standard 15-question evaluation covering logical reasoning, quant, english, real estate basics, and sales aptitude.', 15, 10, 50.00, 0.00, 0, 0, 'after_documents', 1, 0, NOW())
    ON DUPLICATE KEY UPDATE 
        `title` = VALUES(`title`),
        `total_questions` = 15,
        `time_limit_minutes` = 10,
        `passing_percentage` = 50.00,
        `negative_marking_per_question` = 0.00,
        `is_active` = 1
");
echo "✓ Configured test id=1 (15 questions, 10 minutes duration, 50% pass benchmark).\n";

// 2. Ensure sets A, B, C, D, E exist in aptitude_sets
$setsData = [
    1 => ['set_code' => 'Set A', 'name' => 'Assessment Set A - Aptitude & Real Estate', 'description' => 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation'],
    2 => ['set_code' => 'Set B', 'name' => 'Assessment Set B - Aptitude & Real Estate', 'description' => 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation'],
    3 => ['set_code' => 'Set C', 'name' => 'Assessment Set C - Aptitude & Real Estate', 'description' => 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation'],
    4 => ['set_code' => 'Set D', 'name' => 'Assessment Set D - Aptitude & Real Estate', 'description' => 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation'],
    5 => ['set_code' => 'Set E', 'name' => 'Assessment Set E - Aptitude & Real Estate', 'description' => 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation']
];

foreach ($setsData as $sId => $s) {
    $db->prepare("
        INSERT INTO `aptitude_sets` (`id`, `set_code`, `name`, `description`, `times_assigned`, `is_active`)
        VALUES (:id, :code, :name, :desc, 0, 1)
        ON DUPLICATE KEY UPDATE `set_code` = VALUES(`set_code`), `name` = VALUES(`name`), `description` = VALUES(`description`), `is_active` = 1
    ")->execute([
        ':id'   => $sId,
        ':code' => $s['set_code'],
        ':name' => $s['name'],
        ':desc' => $s['description']
    ]);
}
echo "✓ Verified 5 Aptitude Sets (Set A, B, C, D, E).\n";

// Clear previous questions safely
$db->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->exec("TRUNCATE TABLE `options`");
$db->exec("TRUNCATE TABLE `questions`");
$db->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "✓ Reset questions and options tables.\n";

// 3. Define 75 Questions Dataset
$questions = [

    // =========================================================================
    // SET A (Questions 1 - 15)
    // =========================================================================

    // Cat 1: Logical Reasoning (Set A, Q1-3)
    [
        'set_id' => 1, 'category_id' => 1,
        'question' => 'Find the next number in the sequence: 4, 9, 19, 39, 79, ?',
        'explanation' => 'The pattern is: (Previous number * 2) + 1. So 79 * 2 + 1 = 159.',
        'options' => [
            ['text' => '149', 'correct' => 0],
            ['text' => '159', 'correct' => 1],
            ['text' => '169', 'correct' => 0],
            ['text' => '158', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 1,
        'question' => 'Pointing to a photograph of a man, Vikas said, "His mother is the only daughter of my mother." How is Vikas related to the man?',
        'explanation' => 'Only daughter of Vikas\'s mother is Vikas\'s sister. Her son is the man in the photo. Hence, Vikas is the maternal uncle (Mama).',
        'options' => [
            ['text' => 'Brother', 'correct' => 0],
            ['text' => 'Father', 'correct' => 0],
            ['text' => 'Maternal Uncle', 'correct' => 1],
            ['text' => 'Grandfather', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 1,
        'question' => 'If DELHI is coded as 73541 and CALCUTTA as 82589662, how is CALICUT coded?',
        'explanation' => 'Direct letter-to-digit substitution: C=8, A=2, L=5, I=1, C=8, U=9, T=6. Result is 8251896.',
        'options' => [
            ['text' => '5279431', 'correct' => 0],
            ['text' => '8251896', 'correct' => 1],
            ['text' => '8543691', 'correct' => 0],
            ['text' => '8251869', 'correct' => 0],
        ]
    ],

    // Cat 2: Quantitative Aptitude (Set A, Q4-6)
    [
        'set_id' => 1, 'category_id' => 2,
        'question' => 'A real estate broker sells a 3 BHK luxury apartment for ₹1.60 Crore. If the agreed brokerage is 1.5%, what is the broker\'s commission?',
        'explanation' => '1.5% of 1,60,00,000 = (1.5 / 100) * 1,60,00,000 = ₹2,40,000.',
        'options' => [
            ['text' => '₹1,80,000', 'correct' => 0],
            ['text' => '₹2,40,000', 'correct' => 1],
            ['text' => '₹3,20,000', 'correct' => 0],
            ['text' => '₹2,00,000', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 2,
        'question' => 'An office plot measures 60 feet by 90 feet. What is the area of this plot in square yards (Gaj)?',
        'explanation' => 'Area in sq ft = 60 * 90 = 5400 sq ft. Since 1 sq yard = 9 sq ft, Area in sq yards = 5400 / 9 = 600 sq yards.',
        'options' => [
            ['text' => '540 sq yards', 'correct' => 0],
            ['text' => '600 sq yards', 'correct' => 1],
            ['text' => '450 sq yards', 'correct' => 0],
            ['text' => '720 sq yards', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 2,
        'question' => 'A commercial shop bought for ₹40 Lakh is sold for ₹48 Lakh. What is the percentage profit earned on this investment?',
        'explanation' => 'Profit = 48 - 40 = 8 Lakh. Percentage profit = (8 / 40) * 100 = 20%.',
        'options' => [
            ['text' => '16.67%', 'correct' => 0],
            ['text' => '20.00%', 'correct' => 1],
            ['text' => '25.00%', 'correct' => 0],
            ['text' => '18.50%', 'correct' => 0],
        ]
    ],

    // Cat 3: Verbal Ability / English (Set A, Q7-9)
    [
        'set_id' => 1, 'category_id' => 3,
        'question' => 'Choose the grammatically correct sentence for formal business communication with an HNI client:',
        'explanation' => 'Option B maintains formal, courteous tone, proper conditional agreement, and professional vocabulary.',
        'options' => [
            ['text' => 'Kindly reverts back with your feedback asap.', 'correct' => 0],
            ['text' => 'Please let us know your convenient time for the site inspection.', 'correct' => 1],
            ['text' => 'Can you came tomorrow morning at 10 AM to property site?', 'correct' => 0],
            ['text' => 'We is waiting for your confirmation regarding the unit booking.', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 3,
        'question' => 'Select the word most similar in meaning to "PRUDENT":',
        'explanation' => 'Prudent means acting with or showing care, caution and good judgement for the future. "Wise / Cautious" is the exact synonym.',
        'options' => [
            ['text' => 'Careless', 'correct' => 0],
            ['text' => 'Wise', 'correct' => 1],
            ['text' => 'Aggressive', 'correct' => 0],
            ['text' => 'Hesitant', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 3,
        'question' => 'Fill in the blank: "The high-rise tower project was delayed _______ unseasonal monsoon rains and material shortages."',
        'explanation' => '"due to" correctly introduces the causative noun phrase explaining the delay.',
        'options' => [
            ['text' => 'in order to', 'correct' => 0],
            ['text' => 'due to', 'correct' => 1],
            ['text' => 'even though', 'correct' => 0],
            ['text' => 'despite of', 'correct' => 0],
        ]
    ],

    // Cat 4: Real Estate Basics (Set A, Q10-12)
    [
        'set_id' => 1, 'category_id' => 4,
        'question' => 'Under RERA (Real Estate Regulation and Development Act), what minimum percentage of the funds collected from homebuyers must be deposited into a dedicated escrow account?',
        'explanation' => 'Under Section 4(2)(l)(D) of RERA, 70% of the amounts realized for the real estate project from allottees must be deposited into a separate scheduled bank account exclusively to cover construction and land cost.',
        'options' => [
            ['text' => '50%', 'correct' => 0],
            ['text' => '70%', 'correct' => 1],
            ['text' => '80%', 'correct' => 0],
            ['text' => '100%', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 4,
        'question' => 'What is the precise definition of "Carpet Area" as mandated by RERA?',
        'explanation' => 'RERA mandates carpet area as the net usable floor area of an apartment, excluding the area covered by external walls, service shafts, and exclusive balcony/verandah, but including the area covered by internal partition walls.',
        'options' => [
            ['text' => 'Total built area including balcony, common corridors and clubhouse share', 'correct' => 0],
            ['text' => 'Net usable floor area of an apartment excluding external walls and balcony, but including internal walls', 'correct' => 1],
            ['text' => 'Plinth area plus super area loading percentage', 'correct' => 0],
            ['text' => 'Total boundary area enclosed by the structural pillars only', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 4,
        'question' => 'What is "Circle Rate" (also called Collector Rate / Guidance Value) in Indian property transactions?',
        'explanation' => 'The Circle Rate is the minimum government-mandated price per unit area below which a property transaction cannot be registered in the sub-registrar office for stamp duty calculation.',
        'options' => [
            ['text' => 'The highest auction price recorded in a municipal ward', 'correct' => 0],
            ['text' => 'The minimum threshold rate fixed by government for property stamp duty & registration', 'correct' => 1],
            ['text' => 'The developer\'s pre-launch introductory rate for investors', 'correct' => 0],
            ['text' => 'The bank\'s distressed liquidation evaluation rate', 'correct' => 0],
        ]
    ],

    // Cat 5: Sales & Negotiation (Set A, Q13-15)
    [
        'set_id' => 1, 'category_id' => 5,
        'question' => 'A prospective buyer says during a site visit: "The property is great, but the price is ₹15 Lakh above my budget." What is the best initial response?',
        'explanation' => 'Acknowledging the budget, validating priorities, and exploring payment plans or value breakdown before immediately discounting preserves both credibility and developer margin.',
        'options' => [
            ['text' => 'Immediately agree to slash the price by ₹15 Lakh to close on the spot', 'correct' => 0],
            ['text' => 'Acknowledge budget, understand payment milestones, and showcase the flexible construction-linked payment plan & long-term appreciation', 'correct' => 1],
            ['text' => 'Tell the client they cannot afford luxury properties in this area', 'correct' => 0],
            ['text' => 'Walk away and refuse to show further units to this prospect', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 5,
        'question' => 'What is the primary objective of a "Discovery Call" with an inbound real estate lead?',
        'explanation' => 'A discovery call identifies BANT (Budget, Authority, Need/Preferences, and Timeline) so the property consultant presents only relevant, high-probability options.',
        'options' => [
            ['text' => 'To immediately pressure the buyer to swipe a booking token', 'correct' => 0],
            ['text' => 'To uncover the buyer\'s genuine requirements, budget, purpose (end-use vs investment), and decision timeline', 'correct' => 1],
            ['text' => 'To talk non-stop about all 20 projects your agency represents', 'correct' => 0],
            ['text' => 'To find out the client\'s credit card limit for token swipe', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 1, 'category_id' => 5,
        'question' => 'Which closing technique is demonstrated when a sales executive asks: "Would you prefer the corner park-facing unit on the 8th floor or the courtyard view on the 12th floor for your family?"',
        'explanation' => 'This is the "Alternative Choice Close" (assumptive close), which shifts the client\'s decision from "whether to buy" to "which unit suits them best".',
        'options' => [
            ['text' => 'Sharp Angle Close', 'correct' => 0],
            ['text' => 'Alternative Choice Close', 'correct' => 1],
            ['text' => 'High-Pressure Takeaway Close', 'correct' => 0],
            ['text' => 'Ben Franklin Balance Sheet Close', 'correct' => 0],
        ]
    ],


    // =========================================================================
    // SET B (Questions 16 - 30)
    // =========================================================================

    // Cat 1: Logical Reasoning (Set B, Q1-3)
    [
        'set_id' => 2, 'category_id' => 1,
        'question' => 'Statements: All builders are licensed. Some licensed individuals are architects. Conclusion I: Some architects are builders. Conclusion II: All architects are licensed.',
        'explanation' => 'From the premises, we know builders are subset of licensed, and some licensed are architects. We cannot conclude that architects are builders, nor that all architects are licensed. Neither follows.',
        'options' => [
            ['text' => 'Only Conclusion I follows', 'correct' => 0],
            ['text' => 'Neither Conclusion I nor II follows', 'correct' => 1],
            ['text' => 'Only Conclusion II follows', 'correct' => 0],
            ['text' => 'Both Conclusions follow', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 1,
        'question' => 'Anurag walks 10 meters North, turns Right and walks 15 meters, then turns Right again and walks 10 meters. In which direction and how far is he from his starting point?',
        'explanation' => 'Moving 10m N, then 15m E, then 10m S puts Anurag directly 15 meters East of his starting position.',
        'options' => [
            ['text' => '15 meters West', 'correct' => 0],
            ['text' => '15 meters East', 'correct' => 1],
            ['text' => '10 meters East', 'correct' => 0],
            ['text' => '25 meters North-East', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 1,
        'question' => 'Which word does NOT belong with the others in a commercial real estate context?',
        'explanation' => 'Leasehold, Freehold, and Registry pertain to legal land ownership titles. Subprime relates to high-risk credit lending.',
        'options' => [
            ['text' => 'Freehold', 'correct' => 0],
            ['text' => 'Leasehold', 'correct' => 0],
            ['text' => 'Subprime', 'correct' => 1],
            ['text' => 'Conveyance', 'correct' => 0],
        ]
    ],

    // Cat 2: Quantitative Aptitude (Set B, Q4-6)
    [
        'set_id' => 2, 'category_id' => 2,
        'question' => 'A residential floor has a super built-up area of 2,200 sq ft and a loading factor of 25% over the carpet area. What is the approximate carpet area of the unit?',
        'explanation' => 'Super Built-up = Carpet Area * (1 + 0.25). So Carpet Area = 2200 / 1.25 = 1,760 sq ft.',
        'options' => [
            ['text' => '1,650 sq ft', 'correct' => 0],
            ['text' => '1,760 sq ft', 'correct' => 1],
            ['text' => '1,820 sq ft', 'correct' => 0],
            ['text' => '1,950 sq ft', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 2,
        'question' => 'An investor deposits ₹50,00,000 in a fixed real estate yield bond offering 9% simple interest per annum. What total interest will be earned over 3 years?',
        'explanation' => 'SI = (P * R * T) / 100 = (50,00,000 * 9 * 3) / 100 = ₹13,50,000.',
        'options' => [
            ['text' => '₹11,25,000', 'correct' => 0],
            ['text' => '₹13,50,000', 'correct' => 1],
            ['text' => '₹15,00,000', 'correct' => 0],
            ['text' => '₹12,75,000', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 2,
        'question' => 'If 5 sales associates can complete 40 physical site tours in 4 days, how many site tours can 8 sales associates complete in 6 days at the same pace?',
        'explanation' => 'Work per person per day = 40 / (5 * 4) = 2 tours/day. For 8 associates in 6 days = 8 * 6 * 2 = 96 site tours.',
        'options' => [
            ['text' => '80 site tours', 'correct' => 0],
            ['text' => '96 site tours', 'correct' => 1],
            ['text' => '112 site tours', 'correct' => 0],
            ['text' => '88 site tours', 'correct' => 0],
        ]
    ],

    // Cat 3: Verbal Ability / English (Set B, Q7-9)
    [
        'set_id' => 2, 'category_id' => 3,
        'question' => 'Identify the sentence with correct punctuation and professional tone:',
        'explanation' => 'Option B uses correct capitalization, comma placement, and precise business English.',
        'options' => [
            ['text' => 'we sent you the brochure check it and tell us fast.', 'correct' => 0],
            ['text' => 'As requested, we have enclosed the master layout and payment plan for your review.', 'correct' => 1],
            ['text' => 'As requested we have enclosed, the layout plan and brochure for your review;', 'correct' => 0],
            ['text' => 'Brochure is attached please see it today only without fail.', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 3,
        'question' => 'Choose the antonym for the word "TRANSPARENT" in property deals:',
        'explanation' => 'Transparent means open, clear and candid. "Opaque" means obscure, cloudy and concealed.',
        'options' => [
            ['text' => 'Candid', 'correct' => 0],
            ['text' => 'Opaque', 'correct' => 1],
            ['text' => 'Explicit', 'correct' => 0],
            ['text' => 'Lucid', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 3,
        'question' => 'Fill in the blank with the appropriate preposition: "The developer has complied _______ all statutory fire safety norms."',
        'explanation' => 'The verb "comply" is standardly paired with the preposition "with" (complied with).',
        'options' => [
            ['text' => 'to', 'correct' => 0],
            ['text' => 'with', 'correct' => 1],
            ['text' => 'against', 'correct' => 0],
            ['text' => 'by', 'correct' => 0],
        ]
    ],

    // Cat 4: Real Estate Basics (Set B, Q10-12)
    [
        'set_id' => 2, 'category_id' => 4,
        'question' => 'What does FAR (Floor Area Ratio) or FSI (Floor Space Index) represent in urban planning?',
        'explanation' => 'FAR is the ratio of a building\'s total gross floor area (built-up area) to the total area of the land parcel upon which it stands.',
        'options' => [
            ['text' => 'Ratio of carpet area to parking slots', 'correct' => 0],
            ['text' => 'Ratio of total covered floor area to the total plot area', 'correct' => 1],
            ['text' => 'Ratio of open green lawn area to concrete footprint', 'correct' => 0],
            ['text' => 'Ratio of residential towers to commercial retail shops', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 4,
        'question' => 'What is an "Encumbrance Certificate" (EC) in property legal due diligence?',
        'explanation' => 'An Encumbrance Certificate verifies whether a property is free from any legal claims, existing mortgages, bank liens, or legal encumbrances during a specified duration.',
        'options' => [
            ['text' => 'A certificate showing electricity and water bill clearance only', 'correct' => 0],
            ['text' => 'A document proving the property is free from mortgages, legal disputes, and financial liabilities', 'correct' => 1],
            ['text' => 'A certificate issued by an architect confirming seismic resistance', 'correct' => 0],
            ['text' => 'An environmental green-building rating score', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 4,
        'question' => 'In Haryana (Gurgaon/Faridabad), who is the primary statutory regulator for real estate projects and agent registration?',
        'explanation' => 'HRERA (Haryana Real Estate Regulatory Authority), with benches in Gurugram and Panchkula, regulates and registers projects and brokers in Haryana.',
        'options' => [
            ['text' => 'DDA (Delhi Development Authority)', 'correct' => 0],
            ['text' => 'HRERA (Haryana Real Estate Regulatory Authority)', 'correct' => 1],
            ['text' => 'HUDA / HSVP only', 'correct' => 0],
            ['text' => 'RERA Mumbai Bench', 'correct' => 0],
        ]
    ],

    // Cat 5: Sales & Negotiation (Set B, Q13-15)
    [
        'set_id' => 2, 'category_id' => 5,
        'question' => 'A buyer is hesitant, stating: "I want to wait for interest rates to drop before making a purchase." How should you address this?',
        'explanation' => 'Explaining that property capital appreciation typically outpaces minor interest rate cuts—and that loans can be refinanced later while unit selection cannot—creates balanced urgency.',
        'options' => [
            ['text' => 'Mock the buyer\'s economic understanding', 'correct' => 0],
            ['text' => 'Explain that property prices usually rise faster than interest savings, and home loans can always be refinanced when rates soften', 'correct' => 1],
            ['text' => 'Promise the buyer a personal cash kickback on the EMI', 'correct' => 0],
            ['text' => 'Immediately agree and advise the buyer not to purchase real estate for 2 years', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 5,
        'question' => 'What does "Active Listening" signify during a face-to-face consultation with property investors?',
        'explanation' => 'Active listening involves concentrating fully, clarifying underlying motivations (e.g. child\'s schooling, rental yield), and summarizing before pitching solutions.',
        'options' => [
            ['text' => 'Nodding constantly while checking WhatsApp messages on your mobile', 'correct' => 0],
            ['text' => 'Focusing completely on the client\'s words, noting priorities, clarifying pain points, and validating needs before proposing units', 'correct' => 1],
            ['text' => 'Interrupting the client every 30 seconds to quote developer awards', 'correct' => 0],
            ['text' => 'Memorizing the client\'s family members\' names to flatter them', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 2, 'category_id' => 5,
        'question' => 'When a client says "Send me the details on WhatsApp, I will review later", what is the most effective proactive response?',
        'explanation' => 'Sending the brief while anchoring a specific follow-up time prevents the lead from going cold while maintaining professional discipline.',
        'options' => [
            ['text' => 'Send 50 uncompressed PDF files and never follow up', 'correct' => 0],
            ['text' => 'Share the curated digital dossier immediately on WhatsApp, and schedule a specific 10-minute follow-up slot the next afternoon', 'correct' => 1],
            ['text' => 'Tell the client you only communicate via registered postal mail', 'correct' => 0],
            ['text' => 'Repeatedly call them every hour until they answer', 'correct' => 0],
        ]
    ],


    // =========================================================================
    // SET C (Questions 31 - 45)
    // =========================================================================

    // Cat 1: Logical Reasoning (Set C, Q1-3)
    [
        'set_id' => 3, 'category_id' => 1,
        'question' => 'Complete the letter series: BDF, HJL, NPR, ?',
        'explanation' => 'Letters skip 1 letter within groups (B, D, F) and start the next group 2 letters after the end (F + 2 = H, L + 2 = N, R + 2 = T). The next group is TVX.',
        'options' => [
            ['text' => 'SUW', 'correct' => 0],
            ['text' => 'TVX', 'correct' => 1],
            ['text' => 'TWY', 'correct' => 0],
            ['text' => 'UWZ', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 1,
        'question' => 'In a row of 40 luxury villa buyers, Manish is 14th from the left end. What is his rank from the right end?',
        'explanation' => 'Rank from other end = Total - Given Rank + 1 = 40 - 14 + 1 = 27th.',
        'options' => [
            ['text' => '26th', 'correct' => 0],
            ['text' => '27th', 'correct' => 1],
            ['text' => '28th', 'correct' => 0],
            ['text' => '25th', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 1,
        'question' => 'If REALTY is coded as 18-5-1-12-20-25, what is the code for SMART?',
        'explanation' => 'Letters correspond directly to their alphabetical positions: S=19, M=13, A=1, R=18, T=20.',
        'options' => [
            ['text' => '19-12-1-17-20', 'correct' => 0],
            ['text' => '19-13-1-18-20', 'correct' => 1],
            ['text' => '18-13-1-18-19', 'correct' => 0],
            ['text' => '20-14-2-19-21', 'correct' => 0],
        ]
    ],

    // Cat 2: Quantitative Aptitude (Set C, Q4-6)
    [
        'set_id' => 3, 'category_id' => 2,
        'question' => 'A commercial floor generates a monthly rental income of ₹1,50,000. If the total acquisition cost was ₹2.50 Crore, what is the Gross Rental Yield?',
        'explanation' => 'Annual Rent = 1,50,000 * 12 = ₹18,00,000. Gross Yield = (18,00,000 / 2,50,00,000) * 100 = 7.2%.',
        'options' => [
            ['text' => '6.0%', 'correct' => 0],
            ['text' => '7.2%', 'correct' => 1],
            ['text' => '8.5%', 'correct' => 0],
            ['text' => '9.0%', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 2,
        'question' => 'The ratio of investment between two partners in a land deal is 3:5. If the total annual capital gain is ₹64,00,000, what is the larger share?',
        'explanation' => 'Total parts = 3 + 5 = 8 parts. 1 part = 64,00,000 / 8 = 8,00,000. Larger share (5 parts) = 5 * 8,00,000 = ₹40,00,000.',
        'options' => [
            ['text' => '₹36,00,000', 'correct' => 0],
            ['text' => '₹40,00,000', 'correct' => 1],
            ['text' => '₹44,00,000', 'correct' => 0],
            ['text' => '₹48,00,000', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 2,
        'question' => 'A home loan of ₹80 Lakh is sanctioned at 8.5% annual interest. What is the approximate interest payable in the first month alone?',
        'explanation' => 'Monthly interest = (80,00,000 * 0.085) / 12 = 6,80,000 / 12 ≈ ₹56,667.',
        'options' => [
            ['text' => '₹48,500', 'correct' => 0],
            ['text' => '₹56,667', 'correct' => 1],
            ['text' => '₹62,000', 'correct' => 0],
            ['text' => '₹70,000', 'correct' => 0],
        ]
    ],

    // Cat 3: Verbal Ability / English (Set C, Q7-9)
    [
        'set_id' => 3, 'category_id' => 3,
        'question' => 'Select the most professional phrase to conclude a client email pitch:',
        'explanation' => '"I look forward to assisting you in finding your ideal property" is courteous, client-centric, and professional.',
        'options' => [
            ['text' => 'Hope you buy this flat today.', 'correct' => 0],
            ['text' => 'I look forward to assisting you in finding your ideal property investment.', 'correct' => 1],
            ['text' => 'Call me immediately if you have money ready.', 'correct' => 0],
            ['text' => 'Thanks for taking my email and revert fast.', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 3,
        'question' => 'Choose the correctly spelled word:',
        'explanation' => '"ACCOMMODATION" is spelled with two \'c\'s and two \'m\'s.',
        'options' => [
            ['text' => 'Acommodation', 'correct' => 0],
            ['text' => 'Accommodation', 'correct' => 1],
            ['text' => 'Accomodation', 'correct' => 0],
            ['text' => 'Acomodation', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 3,
        'question' => 'Identify the synonym for "TENACIOUS" in sales:',
        'explanation' => 'Tenacious means persistent, determined, and holding firm. "Persistent" is the direct synonym.',
        'options' => [
            ['text' => 'Timid', 'correct' => 0],
            ['text' => 'Persistent', 'correct' => 1],
            ['text' => 'Casual', 'correct' => 0],
            ['text' => 'Passive', 'correct' => 0],
        ]
    ],

    // Cat 4: Real Estate Basics (Set C, Q10-12)
    [
        'set_id' => 3, 'category_id' => 4,
        'question' => 'What is the standard purpose of an "Occupancy Certificate" (OC) issued by civic authorities?',
        'explanation' => 'An OC certifies that a building has been constructed strictly in accordance with approved municipal plans and is safe for human habitation.',
        'options' => [
            ['text' => 'Permission to start excavation and foundation laying', 'correct' => 0],
            ['text' => 'Official certification that the building is compliant with building codes and fit for residential occupation', 'correct' => 1],
            ['text' => 'Approval for commercial signboards and hoarding displays', 'correct' => 0],
            ['text' => 'A deed transferring land ownership between family members', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 4,
        'question' => 'In property transactions in India, what is the role of Stamp Duty?',
        'explanation' => 'Stamp duty is a state government tax levied on legal property transfer documents to validate and legally record the transaction.',
        'options' => [
            ['text' => 'A fee paid directly to the private developer for clubhouse maintenance', 'correct' => 0],
            ['text' => 'A state tax levied on property documents to establish legal ownership and validity in court', 'correct' => 1],
            ['text' => 'A refundable deposit kept with the central Reserve Bank of India', 'correct' => 0],
            ['text' => 'An optional insurance premium covering interior painting', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 4,
        'question' => 'What does "Freehold Property" mean in contrast to "Leasehold Property"?',
        'explanation' => 'Freehold ownership grants complete, indefinite title to the land and structure with full transfer rights and no expiration period.',
        'options' => [
            ['text' => 'The property can only be rented to relatives without charge', 'correct' => 0],
            ['text' => 'Outright perpetual ownership of both the land and building with full transferability', 'correct' => 1],
            ['text' => 'A government lease that automatically expires every 99 years', 'correct' => 0],
            ['text' => 'An agricultural land parcel that cannot have masonry structures', 'correct' => 0],
        ]
    ],

    // Cat 5: Sales & Negotiation (Set C, Q13-15)
    [
        'set_id' => 3, 'category_id' => 5,
        'question' => 'A customer says: "Developer X across the road is offering units ₹500/sq ft cheaper than your project." How should a professional sales consultant respond?',
        'explanation' => 'Selling value over raw price by highlighting superior specifications, clubhouse amenities, construction speed, and track record justifies price differentiation without attacking rivals.',
        'options' => [
            ['text' => 'Tell the client Developer X uses inferior materials and will fail', 'correct' => 0],
            ['text' => 'Highlight your project\'s superior construction quality, RERA milestones, brand track record, and deliverable inclusions that more than offset the price gap', 'correct' => 1],
            ['text' => 'Immediately give up and advise the customer to book with Developer X', 'correct' => 0],
            ['text' => 'Argue aggressively with the client and demand proof of quotes', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 5,
        'question' => 'What is the "Urgency Technique" in high-demand real estate launches, and how must it be ethically applied?',
        'explanation' => 'Ethical urgency communicates verified facts (e.g. only 4 garden-facing units remaining at current launch pricing) to prompt timely action without fraudulent claims.',
        'options' => [
            ['text' => 'Falsely claiming a fake buyer is sitting outside with cash to force a panic booking', 'correct' => 0],
            ['text' => 'Highlighting authentic, factual constraints like upcoming price escalations or limited inventory of premium unit layouts', 'correct' => 1],
            ['text' => 'Locking the client in the conference room until they sign the application form', 'correct' => 0],
            ['text' => 'Promising unrealistic 100% price appreciation within 30 days', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 3, 'category_id' => 5,
        'question' => 'Which CRM habit is most vital for high-performing real estate sales professionals?',
        'explanation' => 'Accurate, timely logging of lead interactions, next follow-up dates, and personal preferences ensures consistent nurturing and high pipeline velocity.',
        'options' => [
            ['text' => 'Updating leads once every 3 months only', 'correct' => 0],
            ['text' => 'Disciplined daily logging of client conversation notes, preference changes, and punctual follow-up scheduling', 'correct' => 1],
            ['text' => 'Marking every uninterested lead as booked to win internal awards', 'correct' => 0],
            ['text' => 'Deleting customer phone numbers after one unanswered call', 'correct' => 0],
        ]
    ],


    // =========================================================================
    // SET D (Questions 46 - 60)
    // =========================================================================

    // Cat 1: Logical Reasoning (Set D, Q1-3)
    [
        'set_id' => 4, 'category_id' => 1,
        'question' => 'Look at the series: 2, 6, 12, 20, 30, 42, ? What number comes next?',
        'explanation' => 'Differences are increasing by 2: +4, +6, +8, +10, +12, +14. So 42 + 14 = 56 (or n^2 + n: 7^2 + 7 = 56).',
        'options' => [
            ['text' => '54', 'correct' => 0],
            ['text' => '56', 'correct' => 1],
            ['text' => '58', 'correct' => 0],
            ['text' => '60', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 1,
        'question' => 'If CLOCK is written as KCOLC, how is RESIDENCE written in that code?',
        'explanation' => 'The word is spelled backwards in reverse order: E-C-N-E-D-I-S-E-R.',
        'options' => [
            ['text' => 'ECNEDISRE', 'correct' => 0],
            ['text' => 'ECNEDISER', 'correct' => 1],
            ['text' => 'ENCSEDERI', 'correct' => 0],
            ['text' => 'ECNEDSIRR', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 1,
        'question' => 'Five commercial towers A, B, C, D, and E are situated in a tech park. B is North of A. C is East of B. D is South of C. What is the direction of D from A?',
        'explanation' => 'A to B is North. B to C is East. C to D is South (equal to B to A). Therefore, D is directly East of A.',
        'options' => [
            ['text' => 'North-West', 'correct' => 0],
            ['text' => 'East', 'correct' => 1],
            ['text' => 'South-West', 'correct' => 0],
            ['text' => 'North', 'correct' => 0],
        ]
    ],

    // Cat 2: Quantitative Aptitude (Set D, Q4-6)
    [
        'set_id' => 4, 'category_id' => 2,
        'question' => 'An investor purchases a plot of land for ₹80 Lakh. Over 2 years, the plot appreciates by 15% in year 1 and 20% in year 2. What is its value at the end of 2 years?',
        'explanation' => 'Year 1: 80 Lakh * 1.15 = 92 Lakh. Year 2: 92 Lakh * 1.20 = ₹110.4 Lakh (₹1.104 Crore).',
        'options' => [
            ['text' => '₹108.0 Lakh', 'correct' => 0],
            ['text' => '₹110.4 Lakh', 'correct' => 1],
            ['text' => '₹115.0 Lakh', 'correct' => 0],
            ['text' => '₹104.2 Lakh', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 2,
        'question' => 'A showroom with a carpet area of 1,200 sq ft has a monthly maintenance fee of ₹6.50 per sq ft. What is the total maintenance cost per annum?',
        'explanation' => 'Monthly charge = 1200 * 6.50 = ₹7,800. Annual charge = 7,800 * 12 = ₹93,600.',
        'options' => [
            ['text' => '₹78,000', 'correct' => 0],
            ['text' => '₹93,600', 'correct' => 1],
            ['text' => '₹86,400', 'correct' => 0],
            ['text' => '₹1,02,000', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 2,
        'question' => 'In a residential project of 240 units, 75% of units have been booked in Phase 1. How many units remain unsold?',
        'explanation' => 'Unsold percentage = 25%. Unsold units = 0.25 * 240 = 60 units.',
        'options' => [
            ['text' => '45 units', 'correct' => 0],
            ['text' => '60 units', 'correct' => 1],
            ['text' => '72 units', 'correct' => 0],
            ['text' => '80 units', 'correct' => 0],
        ]
    ],

    // Cat 3: Verbal Ability / English (Set D, Q7-9)
    [
        'set_id' => 4, 'category_id' => 3,
        'question' => 'Select the most appropriate word to fill in the blank: "All land acquisition documents have been _______ verified by our legal advocates."',
        'explanation' => '"meticulously" (meaning with great attention to detail) fits best in legal scrutiny context.',
        'options' => [
            ['text' => 'casually', 'correct' => 0],
            ['text' => 'meticulously', 'correct' => 1],
            ['text' => 'hastily', 'correct' => 0],
            ['text' => 'tentatively', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 3,
        'question' => 'Choose the sentence with the correct subject-verb agreement:',
        'explanation' => '"The team of senior real estate consultants is" takes singular verb because subject is "team".',
        'options' => [
            ['text' => 'The team of senior real estate consultants are inspecting the clubhouse.', 'correct' => 0],
            ['text' => 'The team of senior real estate consultants is inspecting the clubhouse.', 'correct' => 1],
            ['text' => 'The team of senior real estate consultants have inspect the clubhouse.', 'correct' => 0],
            ['text' => 'The team of senior real estate consultants were inspecting since morning.', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 3,
        'question' => 'What is the meaning of the idiom "to seal the deal"?',
        'explanation' => '"To seal the deal" means to finalize or close a business agreement or contract successfully.',
        'options' => [
            ['text' => 'To cancel a scheduled meeting', 'correct' => 0],
            ['text' => 'To finalize and formally close a transaction', 'correct' => 1],
            ['text' => 'To print physical stickers on paper', 'correct' => 0],
            ['text' => 'To compromise on company policy', 'correct' => 0],
        ]
    ],

    // Cat 4: Real Estate Basics (Set D, Q10-12)
    [
        'set_id' => 4, 'category_id' => 4,
        'question' => 'What is the key difference between an "Agreement for Sale" and a "Sale Deed" (Conveyance Deed)?',
        'explanation' => 'An Agreement for Sale is a promise to transfer property in the future upon conditions being met, whereas a registered Sale Deed actually executes the immediate legal transfer of ownership.',
        'options' => [
            ['text' => 'There is no legal difference between them', 'correct' => 0],
            ['text' => 'Agreement for Sale promises future terms, while the Sale Deed legally transfers ownership upon registration', 'correct' => 1],
            ['text' => 'Agreement for Sale is only for agricultural land, while Sale Deed is for apartments', 'correct' => 0],
            ['text' => 'Sale Deed requires no stamp duty while Agreement for Sale requires full stamp duty', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 4,
        'question' => 'Under Indian banking practice, what does LTV (Loan-to-Value) ratio designate for home buyers?',
        'explanation' => 'LTV is the percentage of property value that a financial institution or bank will lend as a home loan (e.g. 80% LTV means the bank finances 80% and the buyer provides 20% down payment).',
        'options' => [
            ['text' => 'The ratio of land tax to valuation', 'correct' => 0],
            ['text' => 'The proportion of property value that a bank can finance as a loan', 'correct' => 1],
            ['text' => 'The ratio of lease tenure to villa size', 'correct' => 0],
            ['text' => 'The ratio of live leads to total visitors', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 4,
        'question' => 'What is a "Possession Letter" in the real estate handover process?',
        'explanation' => 'A Possession Letter is issued by the developer confirming that the buyer has fulfilled all financial obligations and is granted physical possession and keys of the property unit.',
        'options' => [
            ['text' => 'A receipt for the initial booking token only', 'correct' => 0],
            ['text' => 'An official document issued by the developer handing over physical keys and occupancy rights to the buyer', 'correct' => 1],
            ['text' => 'A bank notice demanding early mortgage repayment', 'correct' => 0],
            ['text' => 'A police verification certificate for tenant leasing', 'correct' => 0],
        ]
    ],

    // Cat 5: Sales & Negotiation (Set D, Q13-15)
    [
        'set_id' => 4, 'category_id' => 5,
        'question' => 'A prospective homebuyer brings their family for a second site visit and seems interested, but stays quiet. What should you do?',
        'explanation' => 'Encouraging family members to share their thoughts and vision for how they will use the space uncovers hidden objections and emotional buy-in.',
        'options' => [
            ['text' => 'Talk continuously without letting any family member speak', 'correct' => 0],
            ['text' => 'Ask open-ended questions inviting the spouse and family members to share their preferences and daily routines', 'correct' => 1],
            ['text' => 'Rush them through the show flat in 5 minutes to create artificial urgency', 'correct' => 0],
            ['text' => 'Leave the family alone in the room and walk away completely', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 5,
        'question' => 'How should a real estate sales executive handle a situation where a client asks a technical legal question they do not know the answer to?',
        'explanation' => 'Admitting you will verify with the legal/technical department and committing to a concrete callback time builds trust and prevents inaccurate commitments.',
        'options' => [
            ['text' => 'Make up a plausible-sounding answer on the spot to avoid appearing uninformed', 'correct' => 0],
            ['text' => 'Honestly acknowledge it, consult the in-house legal team, and provide verified documentation within a committed timeframe', 'correct' => 1],
            ['text' => 'Tell the client technical legal questions are irrelevant for homebuyers', 'correct' => 0],
            ['text' => 'Ignore the question and redirect to clubhouse amenities', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 4, 'category_id' => 5,
        'question' => 'What is "Referral Selling" and why is it considered the highest-converting channel in premium real estate?',
        'explanation' => 'Referral selling leverages the trust of satisfied existing homeowners who recommend friends and family, resulting in higher conversion and shorter sales cycles.',
        'options' => [
            ['text' => 'Buying scraped phone databases from third-party call centers', 'correct' => 0],
            ['text' => 'Generating warm prospective buyers through satisfied clients who advocate your service to peers and relatives', 'correct' => 1],
            ['text' => 'Posting identical unit ads on 50 free classified websites', 'correct' => 0],
            ['text' => 'Handing out paper flyers at traffic intersections', 'correct' => 0],
        ]
    ],


    // =========================================================================
    // SET E (Questions 61 - 75)
    // =========================================================================

    // Cat 1: Logical Reasoning (Set E, Q1-3)
    [
        'set_id' => 5, 'category_id' => 1,
        'question' => 'In a letter-shifting pattern, each letter in a word is moved forward by one position in the alphabet (for example, "CAT" becomes "DBU" as C→D, A→B, T→U). Following this rule, what does the word "SOLID" become?',
        'explanation' => 'Shifting each letter of SOLID forward by one position (S→T, O→P, L→M, I→J, D→E) gives TPMJE.',
        'options' => [
            ['text' => 'ROKHC', 'correct' => 0],
            ['text' => 'TPMJE', 'correct' => 1],
            ['text' => 'SPMKE', 'correct' => 0],
            ['text' => 'TQMKF', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 1,
        'question' => 'A clock shows 3:00. What is the angle between the hour hand and the minute hand?',
        'explanation' => 'At 3:00, the hour hand points to 3 and the minute hand points to 12. Each hour mark is 360 / 12 = 30 degrees. 3 * 30 = 90 degrees.',
        'options' => [
            ['text' => '60 degrees', 'correct' => 0],
            ['text' => '90 degrees', 'correct' => 1],
            ['text' => '75 degrees', 'correct' => 0],
            ['text' => '105 degrees', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 1,
        'question' => 'Which of the following numbers does NOT fit the pattern: 8, 27, 64, 100, 125, 216?',
        'explanation' => 'The pattern consists of perfect cubes: 2^3=8, 3^3=27, 4^3=64, 5^3=125, 6^3=216. 100 is 10^2, not a perfect cube.',
        'options' => [
            ['text' => '64', 'correct' => 0],
            ['text' => '100', 'correct' => 1],
            ['text' => '125', 'correct' => 0],
            ['text' => '216', 'correct' => 0],
        ]
    ],

    // Cat 2: Quantitative Aptitude (Set E, Q4-6)
    [
        'set_id' => 5, 'category_id' => 2,
        'question' => 'An apartment was listed for ₹1.25 Crore. After negotiations, it was closed for ₹1.15 Crore. What percentage discount was conceded by the seller?',
        'explanation' => 'Discount = 1.25 - 1.15 = 0.10 Crore. Percentage = (0.10 / 1.25) * 100 = 8.0%.',
        'options' => [
            ['text' => '6.5%', 'correct' => 0],
            ['text' => '8.0%', 'correct' => 1],
            ['text' => '10.0%', 'correct' => 0],
            ['text' => '7.5%', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 2,
        'question' => 'A rectangular living hall is 24 feet long and 18 feet wide. What is the cost of Italian marble flooring at ₹250 per sq ft?',
        'explanation' => 'Floor Area = 24 * 18 = 432 sq ft. Total Cost = 432 * 250 = ₹1,08,000.',
        'options' => [
            ['text' => '₹96,000', 'correct' => 0],
            ['text' => '₹1,08,000', 'correct' => 1],
            ['text' => '₹1,18,000', 'correct' => 0],
            ['text' => '₹1,25,000', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 2,
        'question' => 'An agency sold 15 units in Q1, 20 units in Q2, 25 units in Q3, and 30 units in Q4. What was the average number of units sold per quarter?',
        'explanation' => 'Average = (15 + 20 + 25 + 30) / 4 = 90 / 4 = 22.5 units.',
        'options' => [
            ['text' => '20 units', 'correct' => 0],
            ['text' => '22.5 units', 'correct' => 1],
            ['text' => '25 units', 'correct' => 0],
            ['text' => '24 units', 'correct' => 0],
        ]
    ],

    // Cat 3: Verbal Ability / English (Set E, Q7-9)
    [
        'set_id' => 5, 'category_id' => 3,
        'question' => 'Choose the word that best fits the blank: "The location offers _______ connectivity to Golf Course Road and the IGI Airport expressway."',
        'explanation' => '"seamless" (smooth, continuous, without friction) is the standard commercial real estate term for connectivity.',
        'options' => [
            ['text' => 'fragmented', 'correct' => 0],
            ['text' => 'seamless', 'correct' => 1],
            ['text' => 'tangled', 'correct' => 0],
            ['text' => 'cluttered', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 3,
        'question' => 'Identify the sentence free of redundancy and errors:',
        'explanation' => 'Option B avoids redundant expressions like "revert back" and maintains clean, executive phrasing.',
        'options' => [
            ['text' => 'Please revert back again when you are free.', 'correct' => 0],
            ['text' => 'Please let us know if you require any additional floor plans or documentation.', 'correct' => 1],
            ['text' => 'We will return back your call as early as feasible.', 'correct' => 0],
            ['text' => 'Kindly discuss together amongst yourselves and reply.', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 3,
        'question' => 'What is the closest synonym for the word "AUTHENTIC"?',
        'explanation' => '"Genuine" means authentic, real, and verified.',
        'options' => [
            ['text' => 'Spurious', 'correct' => 0],
            ['text' => 'Genuine', 'correct' => 1],
            ['text' => 'Artificial', 'correct' => 0],
            ['text' => 'Hypothetical', 'correct' => 0],
        ]
    ],

    // Cat 4: Real Estate Basics (Set E, Q10-12)
    [
        'set_id' => 5, 'category_id' => 4,
        'question' => 'What is the primary role of an Escrow Account in real estate project governance under RERA?',
        'explanation' => 'An escrow account ensures that allottee funds are ring-fenced and disbursed only strictly against certified stages of construction and land liabilities, preventing diversion to other ventures.',
        'options' => [
            ['text' => 'To earn maximum interest in speculative stock markets', 'correct' => 0],
            ['text' => 'To safeguard homebuyer funds and guarantee they are used solely for the specific project\'s construction and land expenses', 'correct' => 1],
            ['text' => 'To pay administrative fines to the municipal council', 'correct' => 0],
            ['text' => 'To distribute employee festival bonuses', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 4,
        'question' => 'What does "TDS under Section 194-IA" mandate when purchasing immovable property in India worth ₹50 Lakh or more?',
        'explanation' => 'Under Section 194-IA of the Income Tax Act, the buyer must deduct 1% TDS from the total sale consideration when purchasing immovable property exceeding ₹50 Lakh and deposit it with the government.',
        'options' => [
            ['text' => 'Buyer must deduct 5% TDS on all properties', 'correct' => 0],
            ['text' => 'Buyer must deduct 1% TDS on property transactions valued at ₹50 Lakh or above', 'correct' => 1],
            ['text' => 'Seller pays 2% extra tax to the sub-registrar office', 'correct' => 0],
            ['text' => 'Only commercial banks are permitted to deduct property TDS', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 4,
        'question' => 'What constitutes a "Plinth Area" of a building?',
        'explanation' => 'The plinth area is the built-up covered area measured at the floor level of the basement or of any story, taken from the outer dimensions of the walls.',
        'options' => [
            ['text' => 'The area of the swimming pool and children\'s playground', 'correct' => 0],
            ['text' => 'The total covered built-up area measured up to the external face of the perimeter walls', 'correct' => 1],
            ['text' => 'The carpet area excluding internal bathrooms', 'correct' => 0],
            ['text' => 'The total area of the open terrace roof', 'correct' => 0],
        ]
    ],

    // Cat 5: Sales & Negotiation (Set E, Q13-15)
    [
        'set_id' => 5, 'category_id' => 5,
        'question' => 'A client expresses interest but states: "I want to consult my family before making a final commitment." What is the best closing response?',
        'explanation' => 'Respecting the decision while facilitating the family review (e.g. providing a comprehensive summary or offering a virtual walkthrough for out-of-town members) maintains momentum without being overbearing.',
        'options' => [
            ['text' => 'Tell the client their family\'s opinion does not matter in financial deals', 'correct' => 0],
            ['text' => 'Support the decision wholeheartedly, share a clean comparison summary for their family discussion, and offer to host a brief walkthrough or call with the family', 'correct' => 1],
            ['text' => 'Refuse to let the client leave until they sign a binding undertaking', 'correct' => 0],
            ['text' => 'Immediately mark the client as lost and discard the lead', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 5,
        'question' => 'What is the "Feel, Felt, Found" method in overcoming property client objections?',
        'explanation' => 'It empathizes with the client ("I understand how you feel"), normalizes the concern ("other investors felt the same"), and presents evidence ("they found the return on investment exceeded expectations").',
        'options' => [
            ['text' => 'A physical handshake technique to assess client confidence', 'correct' => 0],
            ['text' => 'An empathy-driven objection handling model that validates concern, shares peer experience, and showcases proven positive outcome', 'correct' => 1],
            ['text' => 'An aggressive discounting scheme designed to panic buyers', 'correct' => 0],
            ['text' => 'A legal contract clause governing structural defects', 'correct' => 0],
        ]
    ],
    [
        'set_id' => 5, 'category_id' => 5,
        'question' => 'What distinguishes a consultative real estate advisor from a transactional broker?',
        'explanation' => 'A consultative advisor prioritizes the client\'s long-term wealth, lifestyle goals, and legal security rather than merely chasing quick one-off commission checks.',
        'options' => [
            ['text' => 'The consultative advisor charges double brokerage upfront', 'correct' => 0],
            ['text' => 'The consultative advisor focuses on holistic needs, risk mitigation, and long-term client value rather than rapid one-time sales', 'correct' => 1],
            ['text' => 'Transactional brokers always wear suits while advisors do not', 'correct' => 0],
            ['text' => 'There is no practical difference between them', 'correct' => 0],
        ]
    ]

];

// Insert all 75 questions into the database
$stmtInsQ = $db->prepare("
    INSERT INTO `questions` (`category_id`, `set_id`, `question_text`, `explanation`, `marks`, `difficulty`, `negative_marks`, `is_active`, `is_archived`, `created_at`)
    VALUES (:cat_id, :set_id, :q_text, :exp, 1, 'medium', 0.00, 1, 0, NOW())
");

$stmtInsOpt = $db->prepare("
    INSERT INTO `options` (`question_id`, `option_text`, `is_correct`, `option_order`)
    VALUES (:qid, :text, :corr, :ord)
");

$seededCount = 0;
foreach ($questions as $q) {
    $stmtInsQ->execute([
        ':cat_id'  => $q['category_id'],
        ':set_id'  => $q['set_id'],
        ':q_text'  => $q['question'],
        ':exp'     => $q['explanation']
    ]);
    $qid = (int)$db->lastInsertId();

    foreach ($q['options'] as $idx => $opt) {
        $stmtInsOpt->execute([
            ':qid'  => $qid,
            ':text' => $opt['text'],
            ':corr' => $opt['correct'],
            ':ord'  => $idx + 1
        ]);
    }
    $seededCount++;
}

echo "✓ Successfully seeded {$seededCount} questions across 5 sets (15 questions per set, exactly 3 per category) with 4 options each!\n\n";

// Verification breakdown
$summary = $db->query("
    SELECT s.set_code, c.name as category_name, COUNT(q.id) as q_count
    FROM questions q
    JOIN aptitude_sets s ON q.set_id = s.id
    JOIN test_categories c ON q.category_id = c.id
    GROUP BY s.id, c.id
    ORDER BY s.id, c.id
")->fetchAll(PDO::FETCH_ASSOC);

echo "--- SEEDED SUMMARY TABLE ---\n";
foreach ($summary as $row) {
    echo "  {$row['set_code']} | {$row['category_name']} : {$row['q_count']} questions\n";
}

echo "\n========================================================\n";
echo "SEEDING COMPLETED SUCCESSFULLY!\n";
echo "========================================================\n";
