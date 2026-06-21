<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Developer Profile
    |--------------------------------------------------------------------------
    |
    | Used by the landing page (`/`). Override anything via .env.
    |
    */

    'name' => env('DEV_NAME', 'Your Name'),

    'title' => env('DEV_TITLE', 'Full-stack Developer'),

    'tagline' => env('DEV_TAGLINE', 'I build things on the web.'),

    'bio' => env('DEV_BIO', 'Laravel & PHP developer. Interested in clean architecture, AI integrations, and developer experience. This site itself is the demo — every API endpoint is live below.'),

    'location' => env('DEV_LOCATION', 'Earth'),

    'avatar_url' => env('DEV_AVATAR_URL'),

    'resume_url' => env('DEV_RESUME_URL'),

    'email' => env('DEV_EMAIL', 'hello@example.com'),

    'social' => [
        'github' => env('DEV_GITHUB_URL'),
        'linkedin' => env('DEV_LINKEDIN_URL'),
        'twitter' => env('DEV_TWITTER_URL'),
        'telegram' => env('DEV_TELEGRAM_URL'),
    ],

    'skills' => array_filter([
        ['name' => 'PHP', 'level' => 95, 'category' => 'backend'],
        ['name' => 'Laravel', 'level' => 92, 'category' => 'backend'],
        ['name' => 'MySQL', 'level' => 85, 'category' => 'backend'],
        ['name' => 'REST APIs', 'level' => 90, 'category' => 'backend'],
        ['name' => 'OpenAI / LLMs', 'level' => 75, 'category' => 'ai'],
        ['name' => 'Docker', 'level' => 80, 'category' => 'devops'],
        ['name' => 'JavaScript', 'level' => 78, 'category' => 'frontend'],
        ['name' => 'Alpine.js', 'level' => 70, 'category' => 'frontend'],
        ['name' => 'Tailwind CSS', 'level' => 85, 'category' => 'frontend'],
        ['name' => 'Git', 'level' => 88, 'category' => 'tools'],
    ]),

    'projects' => array_filter([
        [
            'name' => 'Contact Form API',
            'description' => 'The very site you are looking at. Laravel 13 + MySQL + OpenAI. Graceful degradation, rate limiting, observability.',
            'url' => '/',
            'repo' => null,
            'tags' => ['Laravel', 'MySQL', 'OpenAI', 'Tailwind', 'Alpine.js'],
        ],
        [
            'name' => 'API Documentation',
            'description' => 'Auto-generated OpenAPI 3.0.3 spec + Postman collection via Scribe.',
            'url' => '/docs',
            'repo' => null,
            'tags' => ['Scribe', 'OpenAPI'],
        ],
    ]),
];
