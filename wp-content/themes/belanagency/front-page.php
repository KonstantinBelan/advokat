<?php
/**
 * Front Page Template
 * Matches index.html
 *
 * @package BelanAgency
 */

get_header();

// 1. Hero Section
get_template_part('template-parts/section', 'hero');

// 2. Advantages Section
get_template_part('template-parts/section', 'advantages');

// 3. Expertise Section
get_template_part('template-parts/section', 'expertise');

// 4. About Section
get_template_part('template-parts/section', 'about');

// 5. Media Section
get_template_part('template-parts/section', 'media');

// 6. Services Preview Section
get_template_part('template-parts/section', 'services');

// 7. Cases Section
get_template_part('template-parts/section', 'cases');

// 8. Reviews Section
get_template_part('template-parts/section', 'reviews');

// 9. CTA Form Section
get_template_part('template-parts/section', 'cta');

// 10. Articles Section
get_template_part('template-parts/section', 'articles');

// 11. FAQ Section
get_template_part('template-parts/section', 'faq');

// 12. Help Banner Section
get_template_part('template-parts/section', 'help');

get_footer();
