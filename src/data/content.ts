import {
  Code,              // React
  FileCode,          // TypeScript
  Palette,           // Tailwind CSS
  FileText,          // PHP
  Server,            // Laravel
  Database,          // MySQL
  Monitor,           // HTML
  BookOpen,          // CSS
  Package,           // Bootstrap
  Brush,             // Sass
  Terminal,          // JavaScript
  Network,           // API
  Award,             // Awards
  Users,             // Users
  Star,              // Rating
  Coffee,            // Break
  Compass,           // Direction
  Lightbulb,         // Idea
  HeartHandshake,    // Partnership
  ShieldCheck,       // Security
  Rocket,            // Startup
  MessageCircle,     // Chat
  Code2,             // Coding
  BookOpenCheck,     // Learning done
  GraduationCap,     // Education
  Bot,               // Chatbot
  Layout             // Layout
} from 'lucide-react';

// Personal Info
export const personalInfo = {
  name: {
    en: "Iqbolshoh Ilhomjonov",
    uz: "Iqbolshoh Ilhomjonov",
    ru: "Икболшох Илхомжонов",
    tj: "Иқболшоҳ Илҳомҷонов"
  },
  location: {
    en: "Samarkand, Uzbekistan",
    uz: "Samarqand, O‘zbekiston",
    ru: "Самарканд, Узбекистан",
    tj: "Самарқанд, Ӯзбекистон"
  },
  avatar: "/images/logos/iqbolshoh.jpg",
  social: {
    email: {
      label: "iilhomjonov777@gmail.com",
      link: "mailto:iilhomjonov777@gmail.com",
    },
    phone: {
      label: "+998 (99) 779 93 33",
      link: "tel:+998997799333",
    },
    github: {
      label: "GitHub",
      link: "https://github.com/Iqbolshoh",
    },
    linkedin: {
      label: "LinkedIn",
      link: "https://linkedin.com/in/iqbolshoh",
    },
    telegram: {
      label: "Telegram",
      link: "https://t.me/iqbolshoh_777",
    },
    instagram: {
      label: "Instagram",
      link: "https://instagram.com/Iqbolshoh_777",
    },
  },
};

// Tech Stack
export const techStack = [
  // ==== Frontend ====
  { name: "HTML", icon: Monitor, level: 98 },
  { name: "CSS", icon: BookOpen, level: 94 },
  { name: "JavaScript", icon: Terminal, level: 92 },
  { name: "TypeScript", icon: FileCode, level: 88 },
  { name: "Bootstrap", icon: Package, level: 80 },
  { name: "Sass", icon: Brush, level: 76 },
  { name: "Tailwind CSS", icon: Palette, level: 95 },
  { name: "React", icon: Code, level: 91 },

  // ==== Backend ====
  { name: "PHP", icon: FileText, level: 90 },
  { name: "Laravel", icon: Server, level: 96 },
  { name: "MySQL", icon: Database, level: 88 },
  { name: "REST API", icon: Network, level: 87 }
];

// Stats
export const stats = [
  {
    icon: Star,
    value: '3+',
    label: {
      en: 'Years of Experience',
      uz: 'Tajriba yillari',
      ru: 'Годы опыта',
      tj: 'Солҳои таҷриба',
    },
  },
  {
    icon: Award,
    value: '50+',
    label: {
      en: 'Projects Completed',
      uz: 'Tugallangan loyihalar',
      ru: 'Завершённые проекты',
      tj: 'Лоиҳаҳои ба анҷом расида',
    },
  },
  {
    icon: Users,
    value: '25+',
    label: {
      en: 'Satisfied Clients',
      uz: 'Qoniqarli mijozlar',
      ru: 'Довольные клиенты',
      tj: 'Мизоҷони қаноатманд',
    },
  },
  {
    icon: Coffee,
    value: '1000+',
    label: {
      en: 'Cups of Coffee Consumed',
      uz: 'Ichilgan qahva piyolalari',
      ru: 'Выпитые чашки кофе',
      tj: 'Пиёлаҳои қаҳваи нӯшидашуда',
    },
  },
];

// Projects
export const projects = [
  {
    id: 1,
    name: {
      en: "Templates.uz",
      uz: "Templates.uz",
      ru: "Templates.uz",
      tj: "Templates.uz"
    },
    description: {
      en: "A SaaS platform to build professional websites using drag & drop tools with Laravel backend and React frontend.",
      uz: "Laravel va React asosida professional saytlar yaratish uchun drag & drop vositalari bilan SaaS platforma.",
      ru: "SaaS-платформа для создания профессиональных сайтов с помощью drag & drop инструментов на Laravel и React.",
      tj: "Платформаи SaaS барои сохтани сайтҳои касбӣ бо истифодаи drag & drop бо backend-и Laravel ва frontend-и React."
    },
    image: "https://images.pexels.com/photos/196644/pexels-photo-196644.jpeg?auto=compress&cs=tinysrgb&w=800",
    tech: ["Laravel", "React", "Vite", "MySQL"],
    liveDemo: "https://templates.uz",
    github: "https://github.com/iqbolshoh/templates-uz",
    featured: true,
    category: "Full-Stack"
  },
];

// Highlights
export const highlights = [
  {
    icon: Award,
    text: {
      en: "Three or more years of hands-on programming experience",
      uz: "Uch yoki undan ortiq yillik amaliy dasturlash tajribasi",
      ru: "Три или более лет практического опыта программирования",
      tj: "Се ё бештар сол таҷрибаи амалии барномасозӣ",
    },
  },
  {
    icon: Compass,
    text: {
      en: "Guided by purpose and grounded in values",
      uz: "Maqsad bilan yo‘naltirilgan va qadriyatlarga asoslangan",
      ru: "Руководствуюсь целью и основываюсь на ценностях",
      tj: "Роҳнамоиишуда бо ҳадаф ва бунёдшуда бар арзишҳо",
    },
  },
  {
    icon: Lightbulb,
    text: {
      en: "Transforming complex ideas into clean and efficient code",
      uz: "Murakkab g‘oyalarni toza va samarali kodga aylantirish",
      ru: "Преобразование сложных идей в чистый и эффективный код",
      tj: "Табдили андешаҳои мураккаб ба коди пок ва самаранок",
    },
  },
  {
    icon: HeartHandshake,
    text: {
      en: "Providing mentorship with sincerity and growing through meaningful impact",
      uz: "Samimiylik bilan mentorlik qilish va mazmunli ta’sir orqali o‘sish",
      ru: "Наставничество с искренностью и рост через значимое влияние",
      tj: "Менторӣ бо ихлосмандӣ ва рушд тавассути таъсири арзишманд",
    },
  },
  {
    icon: ShieldCheck,
    text: {
      en: "Dedicated to ethical principles and secure technology practices",
      uz: "Axloqiy tamoyillar va xavfsiz texnologik amaliyotlarga sodiqman",
      ru: "Привержен этическим принципам и безопасным технологиям",
      tj: "Ба меъёрҳои ахлоқӣ ва усулҳои бехатари технологии содиқам",
    },
  },
  {
    icon: Rocket,
    text: {
      en: "Founder of the Templates.uz Software-as-a-Service platform",
      uz: "Templates.uz dasturiy xizmat platformasining asoschisi",
      ru: "Основатель платформы программного обеспечения как услуги Templates.uz",
      tj: "Асосгузори платформаи барнома ҳамчун хидмат — Templates.uz",
    },
  },
];

// Journey
export const journey = [
  {
    year: "2022",
    title: {
      en: "Started University",
      uz: "Universitetni boshladim",
      ru: "Начал обучение в университете",
      tj: "Оғози донишгоҳ"
    },
    description: {
      en: "Began Software Engineering at Samarkand State University",
      uz: "Samarkand Davlat Universitetida Dasturiy Injenerlik yo‘nalishini boshladim",
      ru: "Начал изучение программной инженерии в Самаркандском Государственном Университете",
      tj: "Дар Донишгоҳи Давлатии Самарқанд муҳандисии нармафзорро оғоз кардам"
    }
  },
  {
    year: "2022",
    title: {
      en: "Frontend Foundations",
      uz: "Frontend asoslarini o‘rgandim",
      ru: "Изучил основы frontend",
      tj: "Асосҳои frontend-ро омӯхтам"
    },
    description: {
      en: "Started learning HTML, CSS, JavaScript, and React—my first step into the frontend world",
      uz: "HTML, CSS, JavaScript va Reactni o‘rganishni boshladim — frontend dunyosiga birinchi qadammim",
      ru: "Начал изучать HTML, CSS, JavaScript и React — мой первый шаг в мир frontend",
      tj: "Омӯзиши HTML, CSS, JavaScript ва React-ро оғоз кардам — қадами аввалини ман ба ҷаҳони frontend"
    }
  },
  {
    year: "2023",
    title: {
      en: "Backend Breakthrough",
      uz: "Backend rivoji",
      ru: "Прорыв в backend",
      tj: "Пешравӣ дар backend"
    },
    description: {
      en: "Dived into Laravel and REST APIs, fell in love with backend development",
      uz: "Laravel va REST API-larni chuqur o‘rgandim, backend dasturlashga oshiq bo‘ldim",
      ru: "Глубоко изучил Laravel и REST API — влюбился в backend разработку",
      tj: "Laravel ва REST API-ро амиқ омӯхтам ва ба backend дил бастам"
    }
  },
  {
    year: "2023",
    title: {
      en: "First Real Freelance Project",
      uz: "Birinchi haqiqiy freelancer loyihasi",
      ru: "Первый реальный фриланс-проект",
      tj: "Аввалин лоиҳаи воқеии фриланс"
    },
    description: {
      en: "Completed a full-stack client project from scratch using Laravel and React",
      uz: "Laravel va React asosida to‘liq mijoz loyihasini noldan yaratdim",
      ru: "Завершил клиентский full-stack проект с нуля, используя Laravel и React",
      tj: "Лоиҳаи пурраи муштариро бо истифода аз Laravel ва React аз сифр анҷом додам"
    }
  },
  {
    year: "2024",
    title: {
      en: "Contributed to Open Source",
      uz: "Open Source’ga hissa qo‘shdim",
      ru: "Внёс вклад в Open Source",
      tj: "Ба Open Source саҳм гузоштам"
    },
    description: {
      en: "Published several public repositories and joined global developer communities",
      uz: "Bir nechta ochiq repositorylarni e’lon qildim va jahon dasturchilar hamjamiyatiga qo‘shildim",
      ru: "Опубликовал несколько открытых репозиториев и присоединился к мировым сообществам разработчиков",
      tj: "Якчанд репозиторияи оммавиро нашр кардам ва ба ҷомеаҳои байналмилалии барномасозон пайвастам"
    }
  },
  {
    year: "2024",
    title: {
      en: "Portfolio and Personal Website",
      uz: "Portfolio va shaxsiy veb-sayt",
      ru: "Портфолио и персональный веб-сайт",
      tj: "Портфолио ва сомонаи шахсӣ"
    },
    description: {
      en: "Launched iqbolshoh.uz to showcase my work, applications, and skills",
      uz: "Iqbolshoh.uz saytini ishga tushirdim — ishlarim, ilovalarim va ko‘nikmalarimni namoyish etish uchun",
      ru: "Запустил iqbolshoh.uz для демонстрации своих работ, приложений и навыков",
      tj: "Iqbolshoh.uz-ро барои намоиши корҳо, барномаҳо ва малакаҳоям оғоз кардам"
    }
  },
  {
    year: "2025",
    title: {
      en: "Templates.uz SaaS Platform",
      uz: "Templates.uz SaaS platformasi",
      ru: "SaaS платформа Templates.uz",
      tj: "Платформаи SaaS-и Templates.uz"
    },
    description: {
      en: "Launched a Laravel and React based SaaS platform to simplify website building",
      uz: "Laravel va React asosida Templates.uz platformasini sayt yaratishni soddalashtirish uchun ishga tushirdim",
      ru: "Запустил SaaS платформу Templates.uz на базе Laravel и React для упрощения создания сайтов",
      tj: "Платформаи Templates.uz-ро бо истифода аз Laravel ва React барои содда кардани сохтани сомонаҳо оғоз кардам"
    }
  },
  {
    year: "2025",
    title: {
      en: "Mentored Over 20 Developers",
      uz: "20 dan ortiq dasturchiga ta’lim berdim",
      ru: "Наставлял более 20 разработчиков",
      tj: "Ба беш аз 20 барномасоз роҳнамоӣ кардам"
    },
    description: {
      en: "Taught software development from scratch using C++, React, and Laravel through practical lessons",
      uz: "C++, React va Laravel yordamida dasturlashni amaliy darslar orqali noldan o‘rgatdim",
      ru: "Обучал программированию с нуля на C++, React и Laravel через практические занятия",
      tj: "Ба барномасозӣ аз сифр тавассути дарсҳои амалӣ бо истифода аз C++, React ва Laravel омӯзиш додам"
    }
  },
  {
    year: "2025",
    title: {
      en: "50+ Public Projects",
      uz: "50+ ochiq loyiha",
      ru: "Более 50 открытых проектов",
      tj: "50+ лоиҳаи оммавӣ"
    },
    description: {
      en: "Published over 50 open-source/public projects on GitHub and other platforms",
      uz: "GitHub va boshqa platformalarda 50 dan ortiq ochiq loyiha e’lon qildim",
      ru: "Опубликовал более 50 открытых проектов на GitHub и других платформах",
      tj: "Дар GitHub ва дигар платформаҳо зиёда аз 50 лоиҳаи оммавиро нашр кардам"
    }
  },
  {
    year: "2025",
    title: {
      en: "Educational Content Creator",
      uz: "Ta’limiy kontent yaratdim",
      ru: "Создатель образовательного контента",
      tj: "Муаллифи контенти таълимӣ"
    },
    description: {
      en: "Started sharing free software tutorials on Instagram and YouTube",
      uz: "Instagram va YouTube’da dasturlash bo‘yicha bepul darslar ulashishni boshladim",
      ru: "Начал делиться бесплатными уроками по программированию в Instagram и YouTube",
      tj: "Нашри дарсҳои ройгон оид ба барномасозиро дар Instagram ва YouTube оғоз кардам"
    }
  },
  {
    year: "2025",
    title: {
      en: "Freelance Career Growth",
      uz: "Freelancerlikdagi yutuqlar",
      ru: "Рост карьеры во фрилансе",
      tj: "Пешравии касбӣ дар фриланс"
    },
    description: {
      en: "Worked with multiple clients to build real-world digital solutions",
      uz: "Bir nechta mijozlar bilan haqiqiy raqamli yechimlar ustida ishladim",
      ru: "Работал с несколькими клиентами над созданием реальных цифровых решений",
      tj: "Бо муштариёни зиёд барои сохтани ҳалли рақамии воқеӣ ҳамкорӣ кардам"
    }
  }
];

// Beyond
export const beyond = [
  {
    icon: GraduationCap,
    title: {
      en: "Mentoring",
      uz: "Mentorlik",
      ru: "Наставничество",
      tj: "Менторинг"
    },
    description: {
      en: "Taught more than twenty students from scratch in C++, frontend, and backend through structured lessons, real projects, and practical support.",
      uz: "Yigirmadan ortiq shogirdlarga C++, frontend va backendni noldan o‘rgatdim — tuzilgan darslar, real loyihalar va amaliy yordam orqali.",
      ru: "Обучил более двадцати учеников C++, фронтенду и бэкенду с нуля — через структурированные уроки, реальные проекты и практическую поддержку.",
      tj: "Беш аз бист шогирдро дар C++, frontend ва backend аз сифр омӯзонидам — бо дарсҳои мураттаб, лоиҳаҳои воқеӣ ва дастгирии амалӣ."
    }
  },
  {
    icon: Code2,
    title: {
      en: "Open Source Contributions",
      uz: "Ochiq manbali hissa",
      ru: "Вклад в Open Source",
      tj: "Саҳмгузорӣ дар Open Source"
    },
    description: {
      en: "Actively contribute to GitHub by sharing open repositories, reusable code, templates, and development tools that support the programming community.",
      uz: "GitHub’da ochiq repositorylar, qayta ishlatiladigan kodlar, shablonlar va dasturlash vositalarini ulashib, dasturchilar jamiyatiga hissa qo‘shaman.",
      ru: "Активно делюсь на GitHub открытыми репозиториями, многократно используемым кодом, шаблонами и инструментами, полезными для сообщества разработчиков.",
      tj: "Дар GitHub repository-ҳои кушода, коди такроран истифодашаванда, шаблонҳо ва абзорҳои рушдро мубодила карда, ба ҷомеаи барномасозон саҳм мегузорам."
    }
  },
  {
    icon: HeartHandshake,
    title: {
      en: "Ethical Technology",
      uz: "Axloqiy texnologiya",
      ru: "Этичные технологии",
      tj: "Технологияи ахлоқӣ"
    },
    description: {
      en: "I develop software that protects user privacy, upholds moral values, and contributes to solving social problems with good intentions.",
      uz: "Foydalanuvchi maxfiyligini himoya qiladigan, axloqiy qadriyatlarni saqlaydigan va ijobiy niyatlar bilan ijtimoiy muammolarni hal qiladigan dasturlar yarataman.",
      ru: "Создаю программное обеспечение, которое защищает конфиденциальность пользователей, поддерживает моральные ценности и решает социальные задачи с добрыми намерениями.",
      tj: "Нармавсореро таҳия мекунам, ки махфияти корбарро ҳифз мекунад, арзишҳои ахлоқиро риоя мекунад ва бо нияти нек барои ҳалли мушкилоти иҷтимоӣ хизмат мерасонад."
    }
  },
  {
    icon: MessageCircle,
    title: {
      en: "Community Engagement",
      uz: "Jamoaviy faollik",
      ru: "Участие в сообществе",
      tj: "Фаъолият дар ҷомеа"
    },
    description: {
      en: "Regularly publish tutorials, programming tips, and developer lifestyle content on Instagram and YouTube to educate and inspire others.",
      uz: "Instagram va YouTube’da muntazam darslar, dasturlash bo‘yicha maslahatlar va dasturchilar hayoti haqida kontent ulashaman — o‘rgatish va ilhom berish maqsadida.",
      ru: "Регулярно публикую туториалы, советы по программированию и контент о жизни разработчиков в Instagram и YouTube, чтобы обучать и вдохновлять других.",
      tj: "Дар Instagram ва YouTube пайваста дарсҳо, маслиҳатҳои барномасозӣ ва мазмуни ҳаёти барномасозонро нашр мекунам — бо ҳадафи омӯзиш ва илҳомбахшӣ."
    }
  },
  {
    icon: BookOpenCheck,
    title: {
      en: "Lifelong Learning",
      uz: "Umrbod o‘rganish",
      ru: "Обучение на протяжении всей жизни",
      tj: "Омӯзиши пайвастаи умрбод"
    },
    description: {
      en: "I continuously invest time in learning—from upgrading Laravel to mastering TypeScript—focusing on daily improvement and professional growth.",
      uz: "Laravel yangilanishlaridan tortib TypeScript’ni mukammal o‘rganishgacha — har kuni bilim va rivojlanishga vaqt ajrataman.",
      ru: "Постоянно уделяю время обучению — от обновлений Laravel до глубокого освоения TypeScript, с упором на ежедневное развитие.",
      tj: "Аз навсозии Laravel то омӯхтани амиқи TypeScript — пайваста барои дониш ва рушди касбӣ вақт ҷудо мекунам."
    }
  }
];

// Services
export const services = [
  {
    id: 1,
    category: "frontend",
    icon: Palette,
    price: "$100+",
    title: {
      en: "UI/UX Design",
      uz: "UI/UX Dizayn",
      ru: "UI/UX дизайн",
      tj: "Тарроҳии UI/UX"
    },
    description: {
      en: "Clean, modern, pixel-perfect interfaces with Tailwind CSS and Figma.",
      uz: "Tailwind CSS va Figma asosida zamonaviy va mukammal dizaynlar.",
      ru: "Современный и точный дизайн с Tailwind CSS и Figma.",
      tj: "Тарроҳии муосири пикселӣ бо Figma ва Tailwind CSS."
    },
    tech: ["Frontend", "Figma", "Tailwind"],
    features: {
      en: ["Figma to Code", "Mobile First", "Tailwind CSS", "Modern Aesthetic"],
      uz: ["Figma'dan kodga", "Mobilga mos", "Tailwind CSS", "Zamonaviy ko‘rinish"],
      ru: ["Figma в код", "Мобильная адаптация", "Tailwind CSS", "Современный стиль"],
      tj: ["Аз Figma ба код", "Мутобиқ ба мобил", "Tailwind CSS", "Услуби муосир"]
    }
  },
  {
    id: 2,
    category: "backend",
    icon: Network,
    price: "$150+",
    title: {
      en: "Custom API Services",
      uz: "Moslashuvchan API Xizmatlari",
      ru: "API под заказ",
      tj: "Хизматрасонии API"
    },
    description: {
      en: "Secure and scalable RESTful APIs for mobile and web apps.",
      uz: "Mobil va veb ilovalar uchun xavfsiz REST APIlar yozish.",
      ru: "Безопасные REST API для приложений.",
      tj: "API-ҳои бехатар барои сомона ва мобил."
    },
    features: {
      en: ["Custom Routes", "JWT", "Secure Tokens", "Error Handling"],
      uz: ["Maxsus endpointlar", "JWT", "Token xavfsizligi", "Xatolik nazorati"],
      ru: ["Собственные пути", "JWT", "Безопасность", "Обработка ошибок"],
      tj: ["Endpoint махсус", "JWT", "Амнияти токен", "Назорати хатогиҳо"]
    }
  },
  {
    id: 3,
    category: "backend",
    icon: ShieldCheck,
    price: "$180+",
    title: {
      en: "Security Optimization",
      uz: "Xavfsizlikni Yaxshilash",
      ru: "Оптимизация Безопасности",
      tj: "Бехатарии Пешрафта"
    },
    description: {
      en: "Hardening web apps with secure headers, validations, and attack protection.",
      uz: "Saytingizni xavfsiz qilish: validatsiya, xavfsiz headerlar, himoya.",
      ru: "Защита приложений: валидация, безопасные заголовки, анти-атаки.",
      tj: "Муҳофизати сомона: валидация, header-и амн, зидди ҳамлаҳо."
    },
    features: {
      en: ["Validation", "Rate Limiting", "Secure Headers", "CSRF/XSS Defense"],
      uz: ["Validatsiya", "Limitlar", "Headerlar", "CSRF/XSS himoya"],
      ru: ["Валидация", "Ограничения", "Заголовки", "Защита от CSRF/XSS"],
      tj: ["Валидация", "Маҳдудиятҳо", "Header-ҳои амн", "Ҳимояи CSRF/XSS"]
    }
  },
  {
    id: 4,
    category: "frontend",
    icon: Monitor,
    price: "$200",
    title: {
      en: "Basic Frontend Website",
      uz: "Oddiy Frontend Sayt",
      ru: "Базовый Frontend сайт",
      tj: "Сайти оддии Frontend"
    },
    description: {
      en: "Responsive 3-page static website, perfect for portfolios and personal brands.",
      uz: "Portfolyo va brendlar uchun 3 sahifalik responsiv sayt.",
      ru: "3-страничный адаптивный сайт для портфолио и брендов.",
      tj: "Сомонаи ҷавобгӯ бо 3 саҳифа барои портфолио ва брендҳо."
    },
    tech: ["Frontend", "HTML", "CSS", "JavaScript"],
    features: {
      en: ["3 Pages", "Responsive Design", "SEO Friendly", "Performance Optimized"],
      uz: ["3 sahifa", "Responsiv dizayn", "SEO mos", "Tez ishlash"],
      ru: ["3 страницы", "Адаптивный дизайн", "SEO", "Быстрая загрузка"],
      tj: ["3 саҳифа", "Тарҳи ҷавобгӯ", "SEO", "Суръати хуб"]
    }
  },
  {
    id: 5,
    category: "special",
    icon: Bot,
    price: "$250+",
    title: {
      en: "Chatbot Development",
      uz: "Chatbot Yaratish",
      ru: "Разработка Чат-бота",
      tj: "Таҳияи Чатбот"
    },
    description: {
      en: "Telegram or web chatbots to automate support, orders, and interaction.",
      uz: "Telegram yoki sayt uchun avtomatlashtirilgan chatbotlar.",
      ru: "Чат-боты для Telegram или веба для автоматизации задач.",
      tj: "Чатботҳо барои Telegram ё сомона барои автоматизатсия."
    },
    features: {
      en: ["Telegram Bot", "Web Chatbot", "Webhook", "AI Logic"],
      uz: ["Telegram bot", "Web chatbot", "Webhook", "AI mantiq"],
      ru: ["Telegram-бот", "Веб-бот", "Webhook", "AI логика"],
      tj: ["Боти Telegram", "Чатботи веб", "Webhook", "Мантиқ"]
    }
  },
  {
    id: 6,
    category: "frontend",
    icon: Layout,
    price: "$300+",
    title: {
      en: "Advanced Frontend Website",
      uz: "Kengaytirilgan Frontend Sayt",
      ru: "Продвинутый Frontend сайт",
      tj: "Сайти пурра Frontend"
    },
    description: {
      en: "Multi-page frontend site with animations, effects, and premium layout.",
      uz: "Animatsiyali, ko‘p sahifali, sifatli frontend sayt.",
      ru: "Многостраничный сайт с эффектами и хорошим дизайном.",
      tj: "Сайти чандсаҳифа бо аниматсия ва тарҳи зебо."
    },
    tech: ["Frontend", "React", "Framer Motion"],
    features: {
      en: ["Animations", "Custom Layouts", "Scroll Effects", "Multiple Pages"],
      uz: ["Animatsiya", "Moslashuvchan sahifalar", "Scroll effektlar", "Ko‘p sahifalar"],
      ru: ["Анимации", "Индивидуальная вёрстка", "Эффекты", "Много страниц"],
      tj: ["Аниматсияҳо", "Тарҳи махсус", "Эффектҳо", "Саҳифаҳои зиёд"]
    }
  },
  {
    id: 7,
    category: "fullstack",
    icon: Server,
    price: "$400+",
    title: {
      en: "Dynamic Website",
      uz: "Dinamik Sayt",
      ru: "Динамический сайт",
      tj: "Сомонаи динамикӣ"
    },
    description: {
      en: "Fully dynamic website using Laravel and React for interactive experience.",
      uz: "Laravel va React asosida foydalanuvchiga interaktiv sayt.",
      ru: "Интерактивный сайт с Laravel и React.",
      tj: "Сомонаи интерактивӣ бо Laravel ва React."
    },
    tech: ["FullStack", "Laravel", "React"],
    features: {
      en: ["Laravel + React", "Interactive UI", "Form Handling", "Database Connected"],
      uz: ["Laravel + React", "Interaktiv interfeys", "Forma ishlov", "Bazaga ulangan"],
      ru: ["Laravel + React", "Интерактивность", "Обработка форм", "БД подключено"],
      tj: ["Laravel + React", "Интерфейси фаъол", "Кори формҳо", "Бо пойгоҳ"]
    }
  },
  {
    id: 8,
    category: "backend",
    icon: Database,
    price: "$500+",
    title: {
      en: "Backend + Admin Panel",
      uz: "Backend + Admin Panel",
      ru: "Бэкенд + Админка",
      tj: "Бекенд + Панели маъмурӣ"
    },
    description: {
      en: "Custom backend system with admin dashboard, user management, and secure API.",
      uz: "Admin paneli, foydalanuvchi boshqaruvi va xavfsiz API bilan backend.",
      ru: "Бэкенд с админкой, управлением пользователей и безопасным API.",
      tj: "Бекенд бо панели маъмурӣ ва API-и бехатар."
    },
    features: {
      en: ["Admin Panel", "User Management", "JWT Auth", "REST API"],
      uz: ["Admin panel", "Foydalanuvchi boshqaruvi", "JWT autent.", "REST API"],
      ru: ["Админка", "Пользователи", "JWT авторизация", "REST API"],
      tj: ["Панел", "Корбарон", "JWT аутент.", "REST API"]
    }
  },
  {
    id: 9,
    category: "fullstack",
    icon: Code,
    price: "$600+",
    title: {
      en: "Full-Stack Application",
      uz: "Full-Stack Ilova",
      ru: "Фуллстек Приложение",
      tj: "Иловаи Full-Stack"
    },
    description: {
      en: "End-to-end app with full frontend and backend logic — ideal for startups.",
      uz: "Frontend va backendni o‘z ichiga olgan to‘liq ilova — startaplar uchun mos.",
      ru: "Полное приложение с фронтом и бэкендом — для стартапов.",
      tj: "Илова бо фронт ва бекенд — барои стартапҳо."
    },
    features: {
      en: ["Full Flow", "Laravel + React", "Advanced Auth", "Scalable DB"],
      uz: ["To‘liq jarayon", "Laravel + React", "Murakkab auth", "Katta DB"],
      ru: ["Весь поток", "Laravel + React", "Сложная авторизация", "Большая БД"],
      tj: ["Ҳама қисмҳо", "Laravel + React", "Авторизатсияи пурра", "Пойгоҳи калон"]
    }
  }
];

// Process Steps
export const processSteps = [
  {
    step: "01",
    title: {
      en: "Discovery",
      uz: "Tahlil",
      ru: "Изучение",
      tj: "Омӯзиш"
    },
    description: {
      en: "Understanding your requirements, goals, and target audience",
      uz: "Talablaringizni, maqsadlaringizni va maqsadli auditoriyangizni tushunish",
      ru: "Понимание ваших требований, целей и целевой аудитории",
      tj: "Фаҳмидани талабот, ҳадафҳо ва аудиторияи мақсаднок"
    }
  },
  {
    step: "02",
    title: {
      en: "Planning",
      uz: "Rejalashtirish",
      ru: "Планирование",
      tj: "Нақшакашӣ"
    },
    description: {
      en: "Creating a detailed project roadmap and technical architecture",
      uz: "Batafsil loyiha yo‘l xaritasi va texnik me’moriy tuzilma yaratish",
      ru: "Создание подробной дорожной карты проекта и технической архитектуры",
      tj: "Эҷоди нақшаи муфассали лоиҳа ва меъмории техникӣ"
    }
  },
  {
    step: "03",
    title: {
      en: "Development",
      uz: "Dasturlash",
      ru: "Разработка",
      tj: "Таҳия"
    },
    description: {
      en: "Building your solution with clean, scalable, and maintainable code",
      uz: "Toza, kengaytiriladigan va texnik xizmat ko‘rsatish mumkin bo‘lgan kod bilan yechim yaratish",
      ru: "Создание решения с чистым, масштабируемым и поддерживаемым кодом",
      tj: "Сохтани ҳалли масъала бо коди пок, васеъшаванда ва нигоҳдошташаванда"
    }
  },
  {
    step: "04",
    title: {
      en: "Delivery",
      uz: "Yetkazib berish",
      ru: "Доставка",
      tj: "Супоридан"
    },
    description: {
      en: "Testing, deployment, and ongoing support to ensure success",
      uz: "Test qilish, joylashtirish va doimiy yordam ko‘rsatish orqali muvaffaqiyatni ta'minlash",
      ru: "Тестирование, развертывание и постоянная поддержка для обеспечения успеха",
      tj: "Санҷиш, татбиқ ва дастгирии доимӣ барои таъмин намудани муваффақият"
    }
  }
];

// Blog Posts
export const blogPosts = [
  {
    id: 1,
    title: {
      en: "The New Look of iqbolshoh.uz – A Digital Masterpiece",
      uz: "Yangi iqbolshoh.uz – Raqamli san’at asari",
      ru: "Новый iqbolshoh.uz – Цифровой шедевр",
      tj: "iqbolshoh.uz-и нав – Шоҳкори рақамӣ"
    },
    excerpt: {
      en: "Today, iqbolshoh.uz got a stunning redesign! A clean UI, elegant layout, powerful multilingual support, and vibrant animations make it a must-see portfolio for developers and clients alike. From the moment you land on the homepage, you are greeted with fluid transitions, clear sections, and intuitive navigation. It reflects not just the skills of a developer, but the vision of a brand. Designed with React and Laravel, it balances beauty with functionality, bringing every element to life. Whether you're a potential client or a curious coder, this site will leave you inspired and impressed.",
      uz: "Bugun iqbolshoh.uz sayti hayratlanarli darajada yangilandi! Toza interfeys, nafis tuzilma, kuchli ko‘p tilli qo‘llab-quvvatlash va jonli animatsiyalar uni har qanday dasturchi va mijoz uchun ko‘rishga arzigulik portfelga aylantiradi. Bosh sahifaga kirgan zahoti, silliq o‘tishlar, aniq bo‘limlar va tushunarli navigatsiya ko‘zingizga tashlanadi. Bu nafaqat dasturchi mahoratini, balki shaxsiy brend qarashini ham aks ettiradi. React va Laravel asosida ishlab chiqilgan bu sayt funksionallik va estetikani uyg‘unlashtirib, har bir elementni jonlantiradi. Siz mijoz bo‘lasizmi yoki shunchaki o‘rganayotgan dasturchi — bu sayt sizni ilhomlantiradi va qoyil qoldiradi.",
      ru: "Сегодня сайт iqbolshoh.uz получил потрясающий редизайн! Чистый пользовательский интерфейс, элегантная структура, мощная многоязычная поддержка и яркие анимации делают его портфолио, которое обязательно стоит увидеть как разработчикам, так и клиентам. С первой секунды на главной странице вас встречают плавные переходы, логично структурированные блоки и интуитивная навигация. Этот сайт отражает не только технические навыки разработчика, но и стратегическое видение личного бренда. Построенный на базе React и Laravel, он гармонично сочетает красоту с функциональностью. Будь вы клиент или программист — вы обязательно вдохновитесь этим шедевром.",
      tj: "Имрӯз сомонаи iqbolshoh.uz бо намуди нави аҷиб навсозӣ гардид! Интерфейси тоза, тарҳи зебо, дастгирии пурқуввати бисёрзабона ва аниматсияҳои рангин онро ба як портфолои воқеан арзандаи дидан барои ҳам барномасозон ва ҳам муштариён табдил додаанд. Аз лаҳзаи аввал, ки ба саҳифаи асосӣ ворид мешавед, гузаришҳои мулоим, қисмҳои возеҳ ва роҳнамоии фаҳмост. Ин сомона на танҳо маҳоратҳои техникии барномасозро нишон медиҳад, балки диди равшани шахсии ӯро низ. Бо истифода аз React ва Laravel сохта шудааст ва зебоӣ ва функсионалиятро ба таври мутавозин муттаҳид мекунад. Новобаста аз он ки шумо муштарӣ ҳастед ё шогирди ҳаваскор — ин сомона шуморо илҳом мебахшад ва дар таассур мегузорад."
    },
    featured: true,
    date: "2025-06-26",
    readTime: "3 min read",
    tags: ["Portfolio", "Design", "Branding"],
    image: "/images/blogs/blog-1.png",
    slug: "new-design-iqbolshoh-uz"
  }
];