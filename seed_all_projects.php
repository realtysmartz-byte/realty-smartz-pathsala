<?php
require __DIR__ . '/app/config/config.php';
require __DIR__ . '/app/config/Database.php';

$db = \App\Config\Database::getConnection();

$covers = [
    '/assets/img/gallery/towers.jpg',
    '/assets/img/gallery/topic1.png',
    '/assets/img/gallery/topic2.png',
    '/assets/img/gallery/topic3.png',
    '/assets/img/gallery/about2.png',
    '/assets/img/gallery/about3.png'
];

$projects = [
    [
        'order_num'    => 1,
        'title'        => 'Adani Samsara Ivana 2.0',
        'slug'         => 'adani-samsara-ivana-sector-63',
        'category'     => 'featured',
        'location'     => 'Sector 63, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹5.87 - 6.37 Cr',
        'type_config'  => '3 & 4 BHK Luxury Independent Floors',
        'description'  => 'Adani Samsara Ivana 2.0 represents the pinnacle of private low-density living on Golf Course Extension Road. Crafted across spacious 351 sq. yd. plots, each floor features private elevator access, dedicated basement or terrace rights, and exclusive membership to the grand Belvedere Club.',
        'highlights'   => [
            'Private elevator with direct home access',
            'Exclusive Belvedere Club membership with world-class wellness facilities',
            'Private basement with light-courtyards (lower floors) & private terrace (upper floors)',
            'Prime Golf Course Extension Road location with rapid metro connectivity'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 2,
        'title'        => 'Adani Samsara Vilasa',
        'slug'         => 'adani-samsara-vilasa-sector-63a',
        'category'     => 'luxury',
        'location'     => 'Sector 63A, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹5.87 - 6.50 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Luxury Independent Floors',
        'description'  => 'A joint venture between Adani Realty and Brahma Group, Samsara Vilasa offers low-rise residences designed for elite families seeking tranquility, exclusivity, and private open spaces on Golf Course Extension Road.',
        'highlights'   => [
            'Low density living on 300+ sq. yd. plots with dedicated stilt parking',
            'Over 1 lakh sq. ft. community clubhouse with heated pool and sports courts',
            'Private terrace garden and multipurpose air-conditioned basement suites',
            'Proximity to top international schools, business parks, and Cyber City'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 3,
        'title'        => 'Adani The Marq',
        'slug'         => 'adani-the-marq-sector-102a',
        'category'     => 'featured',
        'location'     => 'Sector 102A, Dwarka Expressway, Gurugram',
        'price_range'  => '₹3.99 - 5.29 Cr',
        'type_config'  => '3 & 4 BHK Luxury High-Rise Apartments',
        'description'  => 'Adani The Marq is an architectural marvel on the Dwarka Expressway corridor, providing panoramic 3-side open residences surrounded by manicured landscaped greens, grand water features, and resort-inspired recreation.',
        'highlights'   => [
            'Zero-km connectivity to 16-lane Dwarka Expressway and CPR',
            'Panoramic 3-side open green views with extra-wide running balconies',
            'Lavish clubhouse with Olympic-sized pool and indoor badminton arena',
            'IGBC Gold rated sustainable development with solar lighting & rainwater harvesting'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 4,
        'title'        => 'Ashiana Aaroham',
        'slug'         => 'ashiana-aaroham-sector-80',
        'category'     => 'new',
        'location'     => 'Sector 80, New Gurgaon',
        'price_range'  => '₹2.99 - 4.10 Cr',
        'type_config'  => '3 & 4 BHK Kid-Centric Luxury Residences',
        'description'  => 'Ashiana Aaroham is North India\'s premier kid-centric development spread over 10.8 acres off NH-48. Featuring on-site professional sports coaching, planned hobby learning hubs, and expansive open green areas for holistic child development.',
        'highlights'   => [
            'Pioneering kid-centric lifestyle with dedicated learning & activity hubs',
            'Low-density gated complex with over 75% lush open greenery',
            'Just 2 minutes from NH-48 and adjacent to Karma Lakelands golf greens',
            'Full-fledged clubhouse with temperature-controlled indoor pool and fitness gym'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 5,
        'title'        => 'BPTP Downtown 66',
        'slug'         => 'bptp-downtown-66-sector-66',
        'category'     => 'featured',
        'location'     => 'Sector 66, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹5.45 - 7.50 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Luxury High-Rise Residences',
        'description'  => 'BPTP Downtown 66 redefines the Gurgaon skyline with G+45 high-rise glass facade towers designed by world-renowned Aedas Singapore. Boasting just 4 residences per floor, high-speed elevators, and wraparound panoramic deck balconies.',
        'highlights'   => [
            'Iconic G+45 floor glass facade towers by Aedas Singapore',
            'Ultra low-density layout with only 4 residences per core',
            'Grand clubhouse with curated wellness decks and infinity sky pool',
            'Steps away from prime retail, fine dining, and Rapid Metro stations'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 6,
        'title'        => 'BPTP GAIA Residences',
        'slug'         => 'bptp-gaia-residences-sector-102',
        'category'     => 'featured',
        'location'     => 'Sector 102, Dwarka Expressway, Gurugram',
        'price_range'  => '₹3.80 - 4.50 Cr',
        'type_config'  => '3 BHK Eco-Luxury Glass High-Rise Residences',
        'description'  => 'Part of the prestigious 180+ acre BPTP Amstoria integrated township, BPTP GAIA Residences features full-height glass architecture, sky gardens at 400 ft, and 1.75 lakh sq. ft. of clubhouse and sports amenities.',
        'highlights'   => [
            'Part of the premier 180+ acre BPTP Amstoria township ecosystem',
            'Sky gardens and yoga observatory at 400 ft altitude',
            '1.75 lakh sq. ft. luxury clubhouse featuring Turkish Hammam and heated pools',
            '7 high-speed smart elevators per residential core for zero wait time'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 7,
        'title'        => 'Daxin Vistas',
        'slug'         => 'daxin-vistas-sector-36-sohna',
        'category'     => 'featured',
        'location'     => 'Sector 36, Sohna, South Gurugram',
        'price_range'  => '₹1.97 - 2.03 Cr',
        'type_config'  => '3 BHK Premium Independent Floors',
        'description'  => 'Daxin Vistas by Signature Global offers contemporary independent floors set against the picturesque backdrop of the Aravalli hills. Enjoy serene unpolluted air coupled with rapid connectivity via the Sohna Elevated Corridor and Delhi-Mumbai Expressway.',
        'highlights'   => [
            'Picturesque Aravalli foothill surroundings with fresh air microclimate',
            'Direct access to Delhi-Mumbai Expressway and 6-lane Sohna highway',
            'Independent floor living with private terrace and basement storage rights',
            'Gated township with 3-tier surveillance and community clubhouse'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 8,
        'title'        => 'Eldeco Camelot',
        'slug'         => 'eldeco-camelot-sector-17-dwarka',
        'category'     => 'featured',
        'location'     => 'Sector 17, Dwarka, New Delhi',
        'price_range'  => '₹8.26 - 12.00 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Luxury Residences',
        'description'  => 'Eldeco Camelot is an ultra-exclusive DDA-approved luxury residential landmark in Delhi\'s Sector 17 Dwarka. Combining 5-star hospitality, sprawling landscaped courtyards, and unmatched access to IGI Airport and Yashobhoomi.',
        'highlights'   => [
            'Rare DDA-sanctioned freehold ultra-luxury development in New Delhi',
            '5-star hospitality concierge and private double-height reception lobbies',
            'Only 10 minutes from IGI Airport Terminal 3 and Bharat Vandana Park',
            'State-of-the-art multi-tier recreational club and indoor sports complex'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 9,
        'title'        => 'Eldeco Terra & Sol',
        'slug'         => 'eldeco-terra-and-sol-sector-80',
        'category'     => 'featured',
        'location'     => 'Sector 80, Gurugram, Haryana',
        'price_range'  => '₹2.90 - 4.80 Cr',
        'type_config'  => '3 & 4 BHK Resort-Style High Rise Residences',
        'description'  => 'Spread across expansive green acreage off NH-48, Eldeco Terra & Sol is designed around resort living with sun-kissed open courtyards, organic landscaping, and uninterrupted views of the Aravalli forest reserves.',
        'highlights'   => [
            'Over 80% open landscaped area with organic herbal gardens and walking trails',
            'Uninterrupted views of the Aravalli hills and green golf course buffer',
            'Quick exit to NH-48, SPR, and the newly opened Cloverleaf flyover',
            'Resort-style infinity edge swimming pool with poolside cabanas'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 10,
        'title'        => 'Emaar Serenity Hills',
        'slug'         => 'emaar-serenity-hills-sector-86',
        'category'     => 'featured',
        'location'     => 'Sector 86, New Gurgaon',
        'price_range'  => '₹2.98 - 5.28 Cr',
        'type_config'  => '3 & 4 BHK Forest-Themed Luxury Living',
        'description'  => 'Emaar Serenity Hills brings Emaar\'s international architectural benchmark to New Gurgaon. Set amidst verdant surroundings, residences feature high ceilings, expansive balconies, and seamless access to central commercial hubs.',
        'highlights'   => [
            'Lush forest-themed development with over 70% open green space',
            'Signature Emaar master planning and international construction quality',
            'Proximity to reputed healthcare centers and world-class international schools',
            'Grand community clubhouse with temperature-controlled indoor pool'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 11,
        'title'        => 'Emaar Urban Ascent',
        'slug'         => 'emaar-urban-ascent-sector-112',
        'category'     => 'luxury',
        'location'     => 'Sector 112, Dwarka Expressway, Gurugram',
        'price_range'  => '₹3.57 - 5.28 Cr',
        'type_config'  => '3 & 4 BHK High-Rise Luxury Living',
        'description'  => 'Emaar Urban Ascent sits directly on the high-growth Dwarka Expressway, zero kilometers from the Delhi boundary. Boasting state-of-the-art glass architecture, double-height entrance pavilions, and panoramic views of Delhi-NCR.',
        'highlights'   => [
            'Zero km from Delhi border on the 16-lane access-controlled Dwarka Expressway',
            'Double-height grand entrance pavilions with biometric security',
            'World-class fitness gymnasium, squash courts, and outdoor sports arena',
            'High capital appreciation corridor adjacent to Yashobhoomi IICC & Diplomatic Enclave'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 12,
        'title'        => 'Experion The Trillion',
        'slug'         => 'experion-the-trillion-sector-48',
        'category'     => 'luxury',
        'location'     => 'Sector 48, Sohna Road, Gurugram',
        'price_range'  => '₹6.25 - 8.15 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Luxury Condominiums',
        'description'  => 'Backed by Singapore-headquartered Experion Holdings, The Trillion is an ultra-prime development on Sohna Road featuring 100% vehicle-free ground levels, EV charging infrastructure at every bay, and curated botanical gardens.',
        'highlights'   => [
            '100% vehicle-free ground podium level ensuring child and senior safety',
            'Direct access to Sohna Road and Rajiv Chowk interchange',
            'Dedicated EV charging infrastructure provided for every parking bay',
            'Curated botanical gardens, reflexology pathways, and luxury clubhouse'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 13,
        'title'        => 'Ganga Fusion 85',
        'slug'         => 'ganga-fusion-85-sector-85',
        'category'     => 'featured',
        'location'     => 'Sector 85, Dwarka Expressway, Gurugram',
        'price_range'  => '₹4.50 - 7.50 Cr',
        'type_config'  => '3 & 4 BHK AI-Powered Luxury Residences',
        'description'  => 'Ganga Fusion 85 is India\'s pioneer AI-powered residential resort community. Offering wrap-around panoramic sundecks, touchless home automation, and an experiential multi-tier clubhouse equipped with private cinema.',
        'highlights'   => [
            'Integrated AI smart home automation managing energy, lighting, and security',
            'Wrap-around panoramic sun decks with floor-to-ceiling glass railings',
            'Direct arterial access to CPR, SPR, and Delhi-Jaipur Highway (NH-48)',
            'Multi-level experiential club with private mini-theatre and rooftop café'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 14,
        'title'        => 'Ganga Nandaka',
        'slug'         => 'ganga-nandaka-sector-84',
        'category'     => 'featured',
        'location'     => 'Sector 84, Dwarka Expressway, Gurugram',
        'price_range'  => '₹3.50 - 9.00 Cr',
        'type_config'  => '3 & 4 BHK Pure Green High-Rise Homes',
        'description'  => 'Ganga Nandaka is an eco-luxury residential enclave centered on biophilic design principles. With private elevator foyers, Zen water features, and expansive living layouts overlooking verdant green belts.',
        'highlights'   => [
            'Over 80% dedicated to green foliage, water features, and Zen reflexology courts',
            'Private elevator access foyer for every apartment core',
            'Positioned at the major junction of NH-48 and the Dwarka Expressway',
            'Rooftop infinity swimming pool and high-performance fitness center'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 15,
        'title'        => 'Ganga Valley',
        'slug'         => 'ganga-valley-sector-78',
        'category'     => 'new',
        'location'     => 'Sector 78, Gurugram, Haryana',
        'price_range'  => '₹1.50 - 3.00 Cr',
        'type_config'  => '2 & 3 BHK Modern Living Residences',
        'description'  => 'Ganga Valley in Sector 78 combines high-efficiency modern homes with tranquil Aravalli valley views. Located near Golden Greens Golf Course, the gated complex provides smart layouts and vibrant community amenities.',
        'highlights'   => [
            'Tranquil setting near Golden Greens Golf Course and forest green belts',
            'Quick access to NH-48 and Delhi-Mumbai Industrial Corridor (DMIC)',
            'Maximum usable carpet area design with modular kitchen fittings',
            'Secure gated community with dedicated kids play zones and clubhouse'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 16,
        'title'        => 'Godrej Alira',
        'slug'         => 'godrej-alira-sector-39',
        'category'     => 'featured',
        'location'     => 'GH-01, Urban Estate II, Sector 39, Gurugram',
        'price_range'  => '₹6.25 - 8.50 Cr',
        'type_config'  => '3 & 4 BHK Bespoke Residences',
        'description'  => 'Godrej Alira brings bespoke luxury to prime central Gurugram, located right opposite Medanta The Medicity. Featuring low-density towers, biometric security access, and sweeping city skyline views.',
        'highlights'   => [
            'Prime central Gurugram address adjacent to Medanta Medicity and NH-48',
            'Ultra-low density towers with only 2 residences per floor',
            '5-tier security matrix with facial recognition and smart locks',
            'Lavish clubhouse curated with indoor temperature-controlled pool and sports lounge'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 17,
        'title'        => 'Godrej Miraya',
        'slug'         => 'godrej-miraya-sector-43',
        'category'     => 'luxury',
        'location'     => 'Sector 43, Golf Course Road, Gurugram',
        'price_range'  => '₹15.13 - 22.00 Cr',
        'type_config'  => '3, 4 & 5 BHK Super-Luxury Condominiums',
        'description'  => 'Godrej Miraya is the crown jewel on Golf Course Road, designed for India\'s business elite and CXOs. Featuring private lap pools, extra-wide sun terraces, and bespoke concierge services managed by luxury hospitality leaders.',
        'highlights'   => [
            'Ultra-prestigious Golf Course Road address with unparalleled exclusivity',
            'Private sky plunge pools and expansive wraparound outdoor terraces',
            'Bespoke 5-star concierge services and private double-height elevator foyers',
            'Adjacent to DLF Golf and Country Club and One Horizon Center'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 18,
        'title'        => 'Godrej Sora',
        'slug'         => 'godrej-sora-sector-53',
        'category'     => 'luxury',
        'location'     => 'Sector 53, Golf Course Road Corridor, Gurugram',
        'price_range'  => 'Price on Request / ₹7.50 - 12.00 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Luxury Sky Residences',
        'description'  => 'Godrej Sora merges Japanese minimalist architecture with ultra-modern smart living on the Golf Course Road corridor. Enjoy sky observatories, high-speed private lifts, and serene Zen courtyards in the heart of Gurgaon.',
        'highlights'   => [
            'Located in the ultra-luxury residential heart of Sector 53',
            'Japanese minimalist architectural philosophy curated by global designers',
            'Elevated sky observatory and stargazing deck on top floors',
            'Walking distance from Rapid Metro and premier retail avenues'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 19,
        'title'        => 'Godrej Vriksha',
        'slug'         => 'godrej-vriksha-sector-103',
        'category'     => 'featured',
        'location'     => 'Sector 103, Dwarka Expressway, Gurugram',
        'price_range'  => '₹3.70 - 7.00 Cr',
        'type_config'  => '3 & 4 BHK Nature-Inspired Luxury Living',
        'description'  => 'Godrej Vriksha is an urban forest-inspired luxury condominium development located directly on the Dwarka Expressway. Surrounded by over 800 mature native trees, water bodies, and state-of-the-art sports facilities.',
        'highlights'   => [
            'Dense urban forest landscape with over 800 mature native trees',
            'Triple-height entrance arrival lounges with cascading water features',
            'Just 15 minutes direct drive to IGI Airport Terminal 3',
            'Olympic-length swimming pool, squash courts, and professional jogging tracks'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 20,
        'title'        => 'Krisumi Waterside Residences',
        'slug'         => 'krisumi-waterside-residences-sector-36a',
        'category'     => 'featured',
        'location'     => 'Sector 36A, Dwarka Expressway, Gurugram',
        'price_range'  => '₹4.18 - 9.50 Cr',
        'type_config'  => '2, 3 & 4 BHK Indo-Japanese Engineered Homes',
        'description'  => 'Krisumi Waterside Residences is a prestigious joint venture between Japan\'s Sumitomo Corporation and Krishna Group. Designed by world-renowned Nikken Sekkei, homes feature Japanese engineering, serene water bodies, and the 65,000 sq. ft. Sudare clubhouse.',
        'highlights'   => [
            'Authentic Japanese craftsmanship and engineering by Sumitomo Corporation & Nikken Sekkei',
            'Expansive water cascades and Zen meditation rock gardens',
            'Direct adjacency to the upcoming 1000-acre Global City master plan',
            '65,000 sq. ft. Sudare clubhouse offering authentic Japanese dining, spa, and pools'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 21,
        'title'        => 'M3M ELIE SAAB Residences',
        'slug'         => 'm3m-elie-saab-residences-sector-111',
        'category'     => 'luxury',
        'location'     => 'Sector 111, Dwarka Expressway, Gurugram',
        'price_range'  => '₹15.00 - 20.00 Cr',
        'type_config'  => '4 & 5 BHK Haute Couture Designer Condominiums',
        'description'  => 'M3M India partners with haute couture fashion legend Elie Saab to present Delhi-NCR\'s premier branded residences. Featuring Italian marble interiors, custom brass finishes, private heated plunge pools, and uninterrupted skyline views.',
        'highlights'   => [
            'India\'s first branded residences styled by international fashion icon Elie Saab',
            'Private indoor heated plunge pools and custom couture interior finishes',
            'Triple-height Italian marble grand arrival lounge with private white-glove concierge',
            'Direct 0-km access to Delhi boundary and the upcoming Diplomatic Enclave II'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 22,
        'title'        => 'Max Estate 361',
        'slug'         => 'max-estate-361-sector-36a',
        'category'     => 'featured',
        'location'     => 'Sector 36A, Dwarka Expressway, Gurugram',
        'price_range'  => '₹7.00 - 12.00 Cr',
        'type_config'  => '3 & 4 BHK Intergenerational Wellbeing Residences',
        'description'  => 'Max Estate 361 is built around Max Group\'s renowned philosophy of "LiveWell" — intergenerational wellbeing. With advanced air purification, sound-dampened double glazing, senior care infrastructure, and IGBC Platinum green credentials.',
        'highlights'   => [
            'Comprehensive LiveWell wellbeing ecosystem with air purification & soundproofing',
            'Direct access to Dwarka Expressway and adjacent to upcoming Global City',
            'IGBC Platinum pre-certified sustainable building with zero surface traffic',
            'Integrated healthcare concierge and 24/7 on-call medical response room'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 23,
        'title'        => 'Oberoi Realty Three Sixty North',
        'slug'         => 'oberoi-realty-three-sixty-north-sector-58',
        'category'     => 'featured',
        'location'     => 'Sector 58, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹18.50 - 35.00 Cr',
        'type_config'  => '4 & 5 BHK Palatial Golf View Condominiums',
        'description'  => 'Oberoi Realty Three Sixty North marks Mumbai\'s premier luxury developer\'s debut in Delhi NCR. Rising majestically on Golf Course Extension Road, the palatial towers deliver breathtaking Aravalli hill views, private elevator foyers, and bespoke lifestyle amenities.',
        'highlights'   => [
            'Oberoi Realty\'s prestigious maiden flagship development in Delhi NCR',
            'Panoramic views overlooking the Aravalli hills and championship golf corridors',
            'Palatial layouts with dedicated staff quarters and private lift entrance foyers',
            '50-meter Olympic lap pool, private screening room, and international sports courts'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 24,
        'title'        => 'Signature Global Cloverdale',
        'slug'         => 'signature-global-cloverdale-sector-71',
        'category'     => 'luxury',
        'location'     => 'Sector 71, SPR Road, Gurugram',
        'price_range'  => '₹3.89 - 6.61 Cr',
        'type_config'  => '3 & 4 BHK Premium High-Rise Homes',
        'description'  => 'Signature Global Cloverdale is a flagship luxury residential development located strategically on Southern Peripheral Road. Featuring contemporary architecture, smart home integrations, and sprawling clubhouse amenities.',
        'highlights'   => [
            'Prime positioning on the rapidly expanding Southern Peripheral Road (SPR) corridor',
            'Surrounded by top multinational IT hubs and reputed educational institutions',
            'Double-height air-conditioned designer entrance lobbies with visitor lounges',
            'Sprawling multi-level clubhouse with heated swimming pool and sports facilities'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 25,
        'title'        => 'Signature Global Sarvam',
        'slug'         => 'signature-global-sarvam-sector-37d',
        'category'     => 'new',
        'location'     => 'Sector 37D, Dwarka Expressway, Gurugram',
        'price_range'  => '₹2.89 - 3.94 Cr',
        'type_config'  => '3 & 4 BHK Deluxe High-Rise Living',
        'description'  => 'Signature Global Sarvam delivers modern, connected luxury living in Sector 37D right on the Dwarka Expressway. Surrounded by established civic infrastructure, residences provide open green views and top-tier community features.',
        'highlights'   => [
            'Direct access to Dwarka Expressway, Pataudi Road, and the proposed metro station',
            'Over 80% open landscaped greens with jogging paths and open-air amphitheatre',
            'Fully loaded clubhouse with squash court, fitness center, and infinity pool',
            'Minutes from Euro International School, SGT University, and multi-specialty hospitals'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 26,
        'title'        => 'Smart World Edition',
        'slug'         => 'smart-world-edition-sector-66',
        'category'     => 'luxury',
        'location'     => 'Sector 66, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹6.50 - 7.70 Cr',
        'type_config'  => '3.5 & 4.5 BHK Luxury Lifestyle Condominiums',
        'description'  => 'Smart World Edition is an ultra-luxury address crafted on Golf Course Extension Road. Designed by leading Singapore architectural firms, towers feature striking glass facades, wrap-around sundecks, and a 2.5-acre central landscape with musical fountains.',
        'highlights'   => [
            'Designed by Singapore architectural masters with full glass facade towers',
            'Private elevator access and spacious wrap-around panoramic balconies',
            'Direct access to Golf Course Extension Road, Cyber City, and Sector 55-56 Metro',
            '2.5-acre central green landscape featuring musical fountains and aroma gardens'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 27,
        'title'        => 'Smart World Hero Palatial',
        'slug'         => 'smart-world-hero-palatial-sector-104',
        'category'     => 'luxury',
        'location'     => 'Sector 104, Dwarka Expressway, Gurugram',
        'price_range'  => '₹5.25 - 6.40 Cr',
        'type_config'  => '3 & 4 BHK Palatial Living',
        'description'  => 'Smart World Hero Palatial offers grand royal-themed residences on the Dwarka Expressway corridor. Offering sprawling floor plans, servant rooms, private elevator cores, and a 7-star clubhouse equipped with temperature-controlled indoor pool.',
        'highlights'   => [
            'Adjacent to the 16-lane Dwarka Expressway with seamless airport connectivity',
            'Grand 7-star clubhouse with temperature-controlled indoor pool and spa suites',
            'Spacious palatial layouts with dedicated servant quarters and private foyers',
            'Equipped with state-of-the-art smart home automation and touchless controls'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 28,
        'title'        => 'Smart World One DXP',
        'slug'         => 'smart-world-one-dxp-sector-113',
        'category'     => 'featured',
        'location'     => 'Sector 113, Dwarka Expressway, Gurugram',
        'price_range'  => '₹5.00 - 5.85 Cr',
        'type_config'  => '2.5, 3.5 & 4.5 BHK High-Rise Waterfront Homes',
        'description'  => 'Smart World One DXP is strategically situated at Sector 113, the closest high-rise project to Delhi on the Dwarka Expressway. Boasting artificial lagoons, co-working sky lounges, and 16 acres of resort-style landscape.',
        'highlights'   => [
            'Closest high-rise residential project to Delhi border on Dwarka Expressway',
            'Lagoons, water features, and expansive sports arena spread over 16 acres',
            'Curated co-working lounges, 24/7 café, and top-floor sky observatory',
            'Just 5 minutes from Yashobhoomi Convention Center and 15 minutes to IGI T3'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 29,
        'title'        => 'Smart World Sky Arc',
        'slug'         => 'smart-world-sky-arc-sector-69',
        'category'     => 'luxury',
        'location'     => 'Sector 69, Golf Course Extension / SPR, Gurugram',
        'price_range'  => '₹4.99 - 7.00 Cr',
        'type_config'  => '3.5 & 4.5 BHK Sky Residences',
        'description'  => 'Smart World Sky Arc features distinctive curvilinear towers offering uninterrupted 270-degree views of the Gurugram skyline. Enjoy an elevated sky track, triple-height sports club, and effortless connectivity to SPR and Sohna Road.',
        'highlights'   => [
            'Curvilinear architectural design delivering 270-degree panoramic skyline views',
            'Triple-height clubhouse featuring an elevated sky running track and sports courts',
            'High-speed elevators and dedicated service cores ensuring total privacy',
            'Minutes from Cyber City 2, Badshahpur hub, and Golf Course Extension Road'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 30,
        'title'        => 'Smart World The Nature Court',
        'slug'         => 'smart-world-the-nature-court-manesar',
        'category'     => 'featured',
        'location'     => 'M-9, M-10, M-11 Manesar, Gurugram',
        'price_range'  => '₹1.54 - 1.56 Cr',
        'type_config'  => 'Low-Rise Green Living Floors',
        'description'  => 'Smart World The Nature Court offers independent low-rise floors nestled inside Manesar\'s tranquil green enclave. Every home comes with dedicated parking, basement utility room, and access to a vibrant community club.',
        'highlights'   => [
            'Independent low-rise living within a peaceful, pollution-free green enclave',
            'Dedicated basement utility area and private terrace rights with each floor',
            'Direct access to KMP Expressway, NH-48, and IMT Manesar industrial hub',
            'Fully gated community with 24/7 CCTV surveillance and clubhouse amenities'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 31,
        'title'        => 'Smartworld The Loft',
        'slug'         => 'smartworld-the-loft-sector-66',
        'category'     => 'featured',
        'location'     => 'Sector 66, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹2.40 - 3.75 Cr',
        'type_config'  => 'Ultra-Modern Studio & Duplex Serviced Lofts',
        'description'  => 'Smartworld The Loft features soaring double-height ceilings with modern mezzanine living spaces on Golf Course Extension Road. Part of a bustling retail, entertainment, and commercial ecosystem with high leasing potential.',
        'highlights'   => [
            'Double-height loft ceilings with contemporary mezzanine architectural designs',
            'Integrated within an energetic commercial, retail, and culinary ecosystem',
            'High-yielding rental investment favored by CXOs and multinational professionals',
            'Fully automated smart concierge, keyless entry, and dedicated clubhouse'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 32,
        'title'        => 'SOBHA Crescent',
        'slug'         => 'sobha-crescent-sector-63a',
        'category'     => 'featured',
        'location'     => 'Sector 63A, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹5.69 - 7.41 Cr',
        'type_config'  => '3 & 4 BHK German-Engineered Luxury Residences',
        'description'  => 'SOBHA Crescent is crafted with SOBHA\'s world-renowned German engineering and backward-integrated precision. Spread over prime acreage in Sector 63A, homes boast zero vehicular surface traffic and expansive clubhouse amenities.',
        'highlights'   => [
            'German-engineered construction quality backed by legendary Sobha precision',
            'Sprawling central park with zero vehicular surface traffic for quiet living',
            'Multi-level exclusive clubhouse with indoor squash, badminton, and lap pool',
            'Prime Golf Course Extension Road address surrounded by established luxury villas'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 33,
        'title'        => 'SPJ Vedatam',
        'slug'         => 'spj-vedatam-sector-14',
        'category'     => 'new',
        'location'     => 'Sector 14, Old Delhi–Gurgaon Road, Gurugram',
        'price_range'  => '₹0.65 - 1.50 Cr',
        'type_config'  => 'High Street Retail & Premium Commercial Suites',
        'description'  => 'SPJ Vedatam is a boutique commercial and retail landmark in central Gurgaon\'s Sector 14 market hub. Boasting maximum street frontage, double-height retail shops, and huge captive footfalls from surrounding dense residential sectors.',
        'highlights'   => [
            'Prime high-density catchment area on Old Delhi-Gurgaon Road in Sector 14',
            'Double-height retail showrooms with maximum road-facing visibility',
            'High leasing demand from national brands, cafés, clinics, and professional firms',
            'Modern multi-level basement parking with high-speed passenger elevators'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 34,
        'title'        => 'The Dualis',
        'slug'         => 'the-dualis-sector-46',
        'category'     => 'featured',
        'location'     => 'Sector 46, Netaji Subhash Marg, Gurugram',
        'price_range'  => '₹5.49 - 7.40 Cr',
        'type_config'  => '3 & 4 BHK Dual Aspect Luxury Condos',
        'description'  => 'The Dualis by Shapoorji Pallonji Real Estate is an ultra-exclusive single-tower condominium on Netaji Subhash Marg, Sector 46. Every residence features dual-aspect natural lighting, cross ventilation, and rooftop infinity pool.',
        'highlights'   => [
            'Rare single-tower boutique luxury project with dual-aspect cross ventilation',
            'Established central location minutes from HUDA City Centre and Cyber Park',
            'Private clubhouse featuring rooftop infinity pool, sky deck, and gym',
            'Surrounded by premium schools like Amity, hospitals, and high-street markets'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 35,
        'title'        => 'The Omaxe State',
        'slug'         => 'the-omaxe-state-sector-19b-dwarka',
        'category'     => 'featured',
        'location'     => 'Sector 19B, Dwarka, New Delhi',
        'price_range'  => '₹0.52 - 2.50 Cr',
        'type_config'  => 'International Sports City Commercial & Retail',
        'description'  => 'The Omaxe State is India\'s premier integrated sports city and commercial retail hub in Sector 19B Dwarka. Featuring a 50,000-capacity international cricket stadium, 4.2 km of air-conditioned retail high streets, and sports clubs.',
        'highlights'   => [
            'India\'s first integrated sports city with international stadium and Olympic facilities',
            'Over 4.2 km of continuous air-conditioned high-street retail and dining boulevard',
            'Seamless access via UER-II, Dwarka Expressway, and Airport Express Metro',
            'Massive projected footfall hub with guaranteed year-round sports & cultural events'
        ],
        'cover_image'  => $covers[4],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 36,
        'title'        => 'Titanium SPR',
        'slug'         => 'titanium-spr-sector-71',
        'category'     => 'luxury',
        'location'     => 'Sector 71, SPR Road, Gurugram',
        'price_range'  => '₹5.15 - 7.18 Cr',
        'type_config'  => '3.5 & 4.5 BHK Iconic High-Rise Towers',
        'description'  => 'Titanium SPR stands tall as an iconic architectural skyscraper overlooking Southern Peripheral Road. Featuring futuristic curvilinear sky towers, observation decks at 450 ft, and 5 acres of landscaped sports amenities.',
        'highlights'   => [
            'Futuristic curvilinear architecture rising high above the SPR skyline',
            'Sky lounges, co-working pavilions, and observation deck at 450 ft altitude',
            'Expansive 5-acre landscaped sports and wellness arena with Olympic pools',
            'Direct connection to Sohna Elevated Highway, NH-48, and Golf Course Extension'
        ],
        'cover_image'  => $covers[5],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 37,
        'title'        => 'Tonino Lamborghini Residences',
        'slug'         => 'tonino-lamborghini-residences-sector-71',
        'category'     => 'featured',
        'location'     => 'Sector 71, SPR Road, Gurugram',
        'price_range'  => '₹4.49 - 6.50 Cr',
        'type_config'  => 'Italian Designer Branded Residences',
        'description'  => 'The first Tonino Lamborghini branded residential project in North India. Designed with Italian automotive elegance, featuring Italian furnishings, private screening rooms, cigar lounge, and bespoke lifestyle management.',
        'highlights'   => [
            'First-ever Tonino Lamborghini branded residential condominium in North India',
            'Authentic Italian design aesthetics, bespoke lighting, and custom furnishings',
            'Private screening cinema, cigar lounge, and temperature-controlled wine cellar',
            'White-glove VIP concierge services and personalized lifestyle management'
        ],
        'cover_image'  => $covers[0],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 38,
        'title'        => 'Trehan Luxury Floors Sector 71',
        'slug'         => 'trehan-luxury-floors-sector-71',
        'category'     => 'featured',
        'location'     => 'Sector 71, Southern Peripheral Road (SPR), Gurugram',
        'price_range'  => '₹2.25 - 3.10 Cr',
        'type_config'  => '3 BHK Low-Rise Luxury Independent Floors',
        'description'  => 'Trehan Luxury Floors in Sector 71 offers Stilt + 4 independent luxury floors on the Southern Peripheral Road. Enjoy private elevator access, dedicated parking, and private terrace garden options in an established residential community.',
        'highlights'   => [
            'Stilt + 4 low-rise floors with private elevator and reserved covered parking',
            'Private terrace garden and multipurpose basement room rights',
            'Walking distance from upcoming commercial and high-street shopping hubs',
            'Ready and near-possession inventory with immediate registry options'
        ],
        'cover_image'  => $covers[1],
        'is_featured'  => 1
    ],
    [
        'order_num'    => 39,
        'title'        => 'Trehan NKV Luxury Floors',
        'slug'         => 'trehan-nkv-luxury-floors-sector-35',
        'category'     => 'new',
        'location'     => 'NKV Golden Avenue, Sector 35, Sohna, Gurugram',
        'price_range'  => '₹1.52 - 1.75 Cr',
        'type_config'  => '3 BHK Stilt + 4 Independent Floors',
        'description'  => 'Located in NKV Golden Avenue, Sector 35 Sohna, Trehan NKV Luxury Floors offers independent living in the serene foothills of the Aravallis with effortless connectivity to the Delhi-Mumbai Expressway corridor.',
        'highlights'   => [
            'Strategically located with direct access to Delhi-Mumbai Expressway link road',
            'Fresh unpolluted air with picturesque panoramic views of the Aravalli range',
            'Thoughtfully designed 3 BHK layouts with modular kitchens and premium fittings',
            'Low community density with independent stilt parking and low maintenance'
        ],
        'cover_image'  => $covers[2],
        'is_featured'  => 0
    ],
    [
        'order_num'    => 40,
        'title'        => 'Trump Towers Delhi NCR',
        'slug'         => 'trump-towers-delhi-ncr-sector-65',
        'category'     => 'featured',
        'location'     => 'Sector 65, Golf Course Extension Road, Gurugram',
        'price_range'  => '₹11.74 - 20.15 Cr',
        'type_config'  => '3 & 4 BHK Ultra-Exclusive Glass Towers with Private Elevators',
        'description'  => 'Trump Towers Delhi NCR is the iconic ultra-exclusive address developed by Tribeca Developers and The Trump Organization. Featuring twin glass monolith towers, double-height living rooms, private elevators opening directly into residences, and the elite Trump Card privileges.',
        'highlights'   => [
            'Globally recognized luxury real estate brand and architectural icon in Delhi NCR',
            'Double-height floor-to-ceiling glass living rooms with sweeping golf views',
            'Private elevator landing directly into each apartment with biometric security',
            'Exclusive Trump Card privileges providing global concierge and hotel benefits'
        ],
        'cover_image'  => $covers[3],
        'is_featured'  => 1
    ]
];

echo "Beginning upsert of " . count($projects) . " projects...\n\n";

$upsertStmt = $db->prepare("INSERT INTO `projects`
    (`title`, `slug`, `category`, `location`, `price_range`, `type_config`, `description`, `highlights`, `cover_image`, `is_featured`, `is_published`, `order_num`, `created_at`)
    VALUES (:title, :slug, :cat, :loc, :price, :type, :desc, :high, :img, :feat, :pub, :ord, NOW())
    ON DUPLICATE KEY UPDATE
        `title` = VALUES(`title`),
        `category` = VALUES(`category`),
        `location` = VALUES(`location`),
        `price_range` = VALUES(`price_range`),
        `type_config` = VALUES(`type_config`),
        `description` = VALUES(`description`),
        `highlights` = VALUES(`highlights`),
        `cover_image` = VALUES(`cover_image`),
        `is_featured` = VALUES(`is_featured`),
        `is_published` = VALUES(`is_published`),
        `order_num` = VALUES(`order_num`),
        `is_archived` = 0
");

foreach ($projects as $p) {
    $upsertStmt->execute([
        ':title' => $p['title'],
        ':slug'  => $p['slug'],
        ':cat'   => $p['category'],
        ':loc'   => $p['location'],
        ':price' => $p['price_range'],
        ':type'  => $p['type_config'],
        ':desc'  => $p['description'],
        ':high'  => json_encode($p['highlights'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ':img'   => $p['cover_image'],
        ':feat'  => $p['is_featured'],
        ':pub'   => 1,
        ':ord'   => $p['order_num']
    ]);
    echo "✓ [{$p['order_num']}] {$p['title']} ({$p['slug']})\n";
}

// Archive any old test / dummy projects that are not part of the active inventory
$existingSlugs = array_column($projects, 'slug');
$inPlaceholders = implode(',', array_fill(0, count($existingSlugs), '?'));
$archiveStmt = $db->prepare("UPDATE `projects` SET `is_archived` = 1, `is_published` = 0 WHERE `slug` NOT IN ($inPlaceholders)");
$archiveStmt->execute($existingSlugs);

echo "\nDone! Total published active projects now in DB:\n";
$count = $db->query("SELECT COUNT(*) FROM `projects` WHERE `is_published` = 1 AND `is_archived` = 0")->fetchColumn();
echo "Active Published Count: {$count}\n";
