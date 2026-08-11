<?php

/*
|--------------------------------------------------------------------------
| Technology catalogue
|--------------------------------------------------------------------------
|
| The vocabulary behind every "Texnologiyalar" picker in the admin panel and
| the badges the React site renders for a project or a service. Modelled on
| the same catalogue templates.uz uses, so a technology carries one name and
| one look across both sites.
|
| `icon` is the base name of an SVG under storage/app/public/media/tech,
| served by nginx as /media/tech/{icon}.svg. Entries with a null icon have no
| devicon of their own — they render as a plain badge in the brand colour.
| `color` is the technology's real brand colour; the badge derives its text,
| border and tint from it so several badges side by side stay distinct.
|
*/

return [

    // ── Languages & markup ────────────────────────────────────────────────
    'HTML'                 => ['icon' => 'html5',        'color' => '#E34F26'],
    'CSS'                  => ['icon' => 'css3',         'color' => '#1572B6'],
    'JavaScript'           => ['icon' => 'javascript',   'color' => '#F7DF1E'],
    'TypeScript'           => ['icon' => 'typescript',   'color' => '#3178C6'],
    'PHP'                  => ['icon' => 'php',          'color' => '#777BB4'],
    'Java'                 => ['icon' => 'java',         'color' => '#EA2D2E'],
    'Python'               => ['icon' => 'python',       'color' => '#3776AB'],
    'JSON'                 => ['icon' => null,           'color' => '#8B95A5'],

    // ── Frameworks & libraries ────────────────────────────────────────────
    'Laravel'              => ['icon' => 'laravel',      'color' => '#FF2D20'],
    'Blade'                => ['icon' => 'laravel',      'color' => '#FF2D20'],
    'Livewire'             => ['icon' => 'livewire',     'color' => '#FB70A9'],
    'React'                => ['icon' => 'react',        'color' => '#61DAFB'],
    'Vue.js'               => ['icon' => 'vuejs',        'color' => '#4FC08D'],
    'Next.js'              => ['icon' => 'nextjs',       'color' => '#8B95A5'],
    'Node.js'              => ['icon' => 'nodejs',       'color' => '#5FA04E'],
    'Alpine.js'            => ['icon' => 'alpinejs',     'color' => '#8BC0D0'],
    'jQuery'               => ['icon' => 'jquery',       'color' => '#0769AD'],
    'Bootstrap'            => ['icon' => 'bootstrap',    'color' => '#7952B3'],
    'Tailwind CSS'         => ['icon' => 'tailwindcss',  'color' => '#38BDF8'],
    'SASS'                 => ['icon' => 'sass',         'color' => '#CC6699'],
    'Framer Motion'        => ['icon' => 'framermotion', 'color' => '#0055FF'],
    'SweetAlert2'          => ['icon' => null,           'color' => '#3085D6'],
    'AJAX'                 => ['icon' => null,           'color' => '#4B9CD3'],
    'Swing'                => ['icon' => 'java',         'color' => '#EA2D2E'],
    'AWT'                  => ['icon' => 'java',         'color' => '#EA2D2E'],

    // ── Databases & storage ───────────────────────────────────────────────
    'MySQL'                => ['icon' => 'mysql',        'color' => '#4479A1'],
    'MySQLi'               => ['icon' => 'mysql',        'color' => '#4479A1'],
    'PostgreSQL'           => ['icon' => 'postgresql',   'color' => '#4169E1'],
    'MongoDB'              => ['icon' => 'mongodb',      'color' => '#47A248'],
    'SQLite'               => ['icon' => 'sqlite',       'color' => '#003B57'],
    'Redis'                => ['icon' => 'redis',        'color' => '#FF4438'],
    'Room DB'              => ['icon' => 'android',      'color' => '#3DDC84'],
    'localStorage'         => ['icon' => null,           'color' => '#8B95A5'],
    'Database Wrapper'     => ['icon' => null,           'color' => '#8B95A5'],
    'Prepared Statements'  => ['icon' => null,           'color' => '#8B95A5'],

    // ── Platforms & tooling ───────────────────────────────────────────────
    'Android'              => ['icon' => 'android',      'color' => '#3DDC84'],
    'Vite'                 => ['icon' => 'vitejs',       'color' => '#A259FF'],
    'Docker'               => ['icon' => 'docker',       'color' => '#2496ED'],
    'Git'                  => ['icon' => 'git',          'color' => '#F05032'],
    'Figma'                => ['icon' => 'figma',        'color' => '#F24E1E'],

    // ── APIs & integrations ───────────────────────────────────────────────
    'Google Maps API'      => ['icon' => 'google',       'color' => '#4285F4'],
    'Gemini API'           => ['icon' => 'google',       'color' => '#4285F4'],
    'Click API'            => ['icon' => null,           'color' => '#00A5DF'],
    'Payment Integration'  => ['icon' => null,           'color' => '#00A5DF'],
    'PHPMailer'            => ['icon' => null,           'color' => '#E8A33D'],
    'SMTP'                 => ['icon' => null,           'color' => '#E8A33D'],

    // ── Concepts & capabilities ───────────────────────────────────────────
    'OOP'                  => ['icon' => null,           'color' => '#8B95A5'],
    'Security'             => ['icon' => null,           'color' => '#22C55E'],
    'Authentication'       => ['icon' => null,           'color' => '#22C55E'],
    'AES Encryption'       => ['icon' => null,           'color' => '#22C55E'],
    'Sessions'             => ['icon' => null,           'color' => '#22C55E'],
    'RBAC'                 => ['icon' => null,           'color' => '#22C55E'],
    'Admin Panel'          => ['icon' => null,           'color' => '#8B95A5'],
    'Responsive Design'    => ['icon' => null,           'color' => '#8B95A5'],
    'UI Animation'         => ['icon' => null,           'color' => '#8B95A5'],
    'PDF Export'           => ['icon' => null,           'color' => '#8B95A5'],
    'E-commerce'           => ['icon' => null,           'color' => '#8B95A5'],
    'Education System'     => ['icon' => null,           'color' => '#8B95A5'],
    'Game Logic'           => ['icon' => null,           'color' => '#8B95A5'],
    'Multiplayer'          => ['icon' => null,           'color' => '#8B95A5'],

    // ── Disciplines ───────────────────────────────────────────────────────
    // Services are tagged by the kind of work they cover, not only by the
    // stack they are built on, so these sit in the same picker.
    'Frontend'             => ['icon' => null,           'color' => '#38BDF8'],
    'Backend'              => ['icon' => null,           'color' => '#A78BFA'],
    'FullStack'            => ['icon' => null,           'color' => '#F472B6'],
];
