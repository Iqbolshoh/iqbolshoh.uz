import {
  Code,         // React
  FileCode,     // TypeScript
  Palette,      // Tailwind CSS
  FileText,     // PHP
  Server,       // Laravel
  Database,     // MySQL
  Monitor,      // HTML
  BookOpen,     // CSS
  Package,      // Bootstrap
  Brush,        // Sass
  Terminal,     // JavaScript
  Network,      // API
  Award,        // Awards
  Users,        // Users
  Star,         // Rating
  Coffee,        // Relax/Break
  Compass,
  Lightbulb,
  HeartHandshake,
  ShieldCheck,
  Rocket,
  MessageCircle,
  Code2,
  BookOpenCheck,
  GraduationCap,
  Bot,
  Layout
} from 'lucide-react';

// Personal Info
export const personalInfo = {
  name: "Iqbolshoh Ilhomjonov",
  title: "Full-Stack Developer",
  location: "Samarkand, Uzbekistan",
  bio: "A passionate Full-Stack Developer with over 3 years of experience in Laravel, React, and Node.js. 3rd-year Software Engineering student at Samarkand State University.",
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
      uz: 'Bajargan loyihalar',
      ru: 'Завершённых проектов',
      tj: 'Лоиҳаҳои анҷомшуда',
    },
  },
  {
    icon: Users,
    value: '25+',
    label: {
      en: 'Happy Clients',
      uz: 'Mamnun mijozlar',
      ru: 'Довольные клиенты',
      tj: 'Мизоҷони хушнуд',
    },
  },
  {
    icon: Coffee,
    value: '1000+',
    label: {
      en: 'Cups of Coffee',
      uz: 'Ichilgan qahvalar',
      ru: 'Чашек кофе',
      tj: 'Кафеҳои нӯшида',
    },
  },
];

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
  {
    id: 2,
    name: {
      en: "Personal Portfolio",
      uz: "Shaxsiy Portfolio",
      ru: "Личный Портфолио",
      tj: "Портфолияи Шахсӣ"
    },
    description: {
      en: "Modern portfolio website showcasing my skills, projects, and professional journey in web development.",
      uz: "Mening portfolio saytim — ko'nikmalarim, loyihalarim va professionallik yo'lim haqida.",
      ru: "Современный сайт-портфолио, демонстрирующий мои навыки, проекты и профессиональный путь.",
      tj: "Сайти муосири портфолио ки маҳорати ман, лоиҳаҳо ва таҷрибаи касбии маро нишон медиҳад."
    },
    image: "https://images.pexels.com/photos/270404/pexels-photo-270404.jpeg?auto=compress&cs=tinysrgb&w=800",
    tech: ["React", "TypeScript", "Tailwind"],
    liveDemo: "https://iqbolshoh.uz",
    github: "https://github.com/iqbolshoh/portfolio",
    featured: true,
    category: "Frontend"
  },
  {
    id: 3,
    name: {
      en: "E-Commerce Platform",
      uz: "E-Tijorat Platformasi",
      ru: "Платформа электронной торговли",
      tj: "Платформаи Тиҷорати Электронӣ"
    },
    description: {
      en: "Full-featured online store with admin panel, payment integration, and inventory management.",
      uz: "To'liq funksional onlayn do'kon: admin panel, to'lov integratsiyasi, va ombor boshqaruvi bilan.",
      ru: "Полноценный интернет-магазин с админ-панелью, интеграцией платежей и управлением запасами.",
      tj: "Дӯкони онлайн бо тамоми функсияҳо: панели админ, пардохт ва идоракунии анбор."
    },
    image: "https://images.pexels.com/photos/230544/pexels-photo-230544.jpeg?auto=compress&cs=tinysrgb&w=800",
    tech: ["Laravel", "Vue.js", "Stripe API"],
    liveDemo: "#",
    github: "#",
    featured: false,
    category: "Full-Stack"
  }
];

// Highlights
export const highlights = [
  {
    icon: Award,
    text: {
      en: "3+ years of hands-on coding experience",
      uz: "3+ yillik amaliy kod yozish tajribasi",
      ru: "3+ года практического опыта программирования",
      tj: "3+ соли таҷрибаи амалии барномасозӣ",
    },
  },
  {
    icon: Compass,
    text: {
      en: "Guided by purpose, rooted in values",
      uz: "Maqsadga yo‘naltirilgan, qadriyatlarga asoslangan",
      ru: "Руководствуется целью и ценностями",
      tj: "Бо ҳадаф ва арзишҳо роҳнамоӣ мешавад",
    },
  },
  {
    icon: Lightbulb,
    text: {
      en: "Turning complex ideas into clean code",
      uz: "Murakkab g‘oyalarni toza kodga aylantirish",
      ru: "Преобразование сложных идей в чистый код",
      tj: "Табдили андешаҳои мураккаб ба коди соф",
    },
  },
  {
    icon: HeartHandshake,
    text: {
      en: "Mentoring with heart, growing with impact",
      uz: "Qalb bilan mentorlik, ta’sir bilan o‘sish",
      ru: "Наставничество с душой, рост с влиянием",
      tj: "Менторӣ бо дил, рушд бо таъсир",
    },
  },
  {
    icon: ShieldCheck,
    text: {
      en: "Committed to ethical and secure tech",
      uz: "Axloqiy va xavfsiz texnologiyaga sadoqat",
      ru: "Приверженность этичным и безопасным технологиям",
      tj: "Садоқат ба технологияҳои ахлоқӣ ва амн",
    },
  },
  {
    icon: Rocket,
    text: {
      en: "Founder of Templates.uz SaaS platform",
      uz: "Templates.uz SaaS asoschisi",
      ru: "Основатель SaaS платформы Templates.uz",
      tj: "Асосгузори платформаи Templates.uz",
    },
  },
];

// Journey
export const journey = [
  {
    year: "2022",
    title: {
      en: "Started University",
      uz: "Universitetga kirish",
      ru: "Начало университета",
      tj: "Оғози донишгоҳ"
    },
    description: {
      en: "Began Software Engineering at Samarkand State University",
      uz: "SamDUda Dasturiy injiniringni boshladim",
      ru: "Начал изучать программную инженерию в СамДУ",
      tj: "Муҳандисии нармафзорро дар СамДУ оғоз кардам"
    }
  },
  {
    year: "2022",
    title: {
      en: "Frontend Foundations",
      uz: "Frontend asoslari",
      ru: "Основы Frontend",
      tj: "Асосҳои Frontend"
    },
    description: {
      en: "Started learning HTML, CSS, JS, and React—my first step into frontend world.",
      uz: "HTML, CSS, JS va Reactni o‘rganishni boshladim — frontend olamiga birinchi qadammim.",
      ru: "Начал изучать HTML, CSS, JS и React — мой первый шаг в мир frontend.",
      tj: "HTML, CSS, JS ва React-ро омӯхтам — қадами аввалини ман ба ҷаҳони frontend."
    }
  },
  {
    year: "2023",
    title: {
      en: "Backend Breakthrough",
      uz: "Backend yuksalishi",
      ru: "Прорыв в Backend",
      tj: "Пешравӣ дар Backend"
    },
    description: {
      en: "Dived into Laravel and REST APIs, fell in love with backend development.",
      uz: "Laravel va REST API-larni chuqur o‘rgandim, backendga oshiq bo‘ldim.",
      ru: "Глубоко изучил Laravel и REST API — влюбился в backend.",
      tj: "Laravel ва REST API-ро амиқ омӯхтам ва ба backend дил бастам."
    }
  },
  {
    year: "2023",
    title: {
      en: "First Real Freelance Project",
      uz: "Birinchi real freelancer loyiha",
      ru: "Первый реальный фриланс проект",
      tj: "Аввалин лоиҳаи воқеии фриланс"
    },
    description: {
      en: "Completed a full-stack client project from scratch using Laravel + React.",
      uz: "Laravel + React asosida to‘liq mijoz loyihasini noldan bajardim.",
      ru: "Реализовал full-stack проект с нуля для клиента на Laravel + React.",
      tj: "Лоиҳаи пурраи full-stack-ро барои муштарӣ бо Laravel + React анҷом додам."
    }
  },
  {
    year: "2024",
    title: {
      en: "Contributed to Open Source",
      uz: "Open-sourcega hissa qo‘shdim",
      ru: "Внёс вклад в Open Source",
      tj: "Саҳм дар Open Source"
    },
    description: {
      en: "Published several public repositories and joined global dev communities.",
      uz: "Bir nechta open-source repositorylar e’lon qildim va jahon dev hamjamiyatiga qo‘shildim.",
      ru: "Опубликовал несколько open-source репозиториев и вступил в глобальные сообщества.",
      tj: "Якчанд repository-и open-source нашр кардам ва ба ҷомеаҳои ҷаҳонӣ пайвастам."
    }
  },
  {
    year: "2024",
    title: {
      en: "Portfolio & Personal Website",
      uz: "Portfolio va shaxsiy sayt",
      ru: "Портфолио и личный сайт",
      tj: "Портфолио ва сомонаи шахсӣ"
    },
    description: {
      en: "Launched iqbolshoh.uz to showcase my work, apps, and skills.",
      uz: "Iqbolshoh.uz saytini ishga tushirdim — barcha ishlarim va ilovalarim u yerda.",
      ru: "Запустил iqbolshoh.uz — там вся моя работа и проекты.",
      tj: "Iqbolshoh.uz-ро оғоз кардам — ҳама кору барномаҳои ман дар онҷост."
    }
  },
  {
    year: "2025",
    title: {
      en: "Templates.uz SaaS Platform",
      uz: "Templates.uz SaaS platformasi",
      ru: "Платформа Templates.uz",
      tj: "Платформаи Templates.uz"
    },
    description: {
      en: "Launched a Laravel + React SaaS platform to help users build websites easier.",
      uz: "Laravel + React asosida Templates.uz platformasini ishga tushirdim — sayt qurishni soddalashtirish uchun.",
      ru: "Запустил SaaS платформу на Laravel + React для упрощения создания сайтов.",
      tj: "Платформаи SaaS-ро бо Laravel + React оғоз кардам — барои осон кардани сохтани сомонаҳо."
    }
  },
  {
    year: "2025",
    title: {
      en: "Mentored 20+ Developers",
      uz: "20+ dasturchiga noldan ta’lim berdim",
      ru: "Обучил с нуля 20+ разработчиков",
      tj: "Ба 20+ барномасоз аз сифр таълим додам"
    },
    description: {
      en: "Taught 0 to 1 skills in C++, frontend (React), and backend (Laravel) through practical lessons and personal guidance.",
      uz: "C++, frontend (React) va backend (Laravel) yo‘nalishlarida amaliy darslar orqali 0 dan boshlab o‘rgatdim.",
      ru: "Обучал C++, frontend (React) и backend (Laravel) с нуля через практические уроки и личное наставничество.",
      tj: "Ба шогирдон аз сифр C++, frontend (React) ва backend (Laravel)-ро бо дарсҳои амалӣ ва роҳнамоии инфиродӣ омӯзонидам."
    }
  },
  {
    year: "2025",
    title: {
      en: "50+ Public Projects",
      uz: "50+ ommaviy loyiha",
      ru: "50+ публичных проектов",
      tj: "50+ лоиҳаи оммавӣ"
    },
    description: {
      en: "Published over 50 open/public projects across GitHub and platforms.",
      uz: "GitHub va boshqa platformalarda 50+ ochiq loyiha e’lon qildim.",
      ru: "Опубликовал 50+ проектов на GitHub и других платформах.",
      tj: "Дар GitHub ва дигар платформаҳо зиёда аз 50 лоиҳаро нашр кардам."
    }
  },
  {
    year: "2025",
    title: {
      en: "Educational Content Creator",
      uz: "Ta’limiy kontent muallifi",
      ru: "Создатель обучающего контента",
      tj: "Муаллифи контенти таълимӣ"
    },
    description: {
      en: "Started sharing free tutorials on Instagram and YouTube.",
      uz: "Instagram va YouTube’da bepul darsliklarni ulashishni boshladim.",
      ru: "Начал делиться бесплатными туториалами в Instagram и YouTube.",
      tj: "Доро дар Instagram ва YouTube дарсҳои ройгон нашр карданро оғоз кардам."
    }
  },
  {
    year: "2025",
    title: {
      en: "Freelance Career Growth",
      uz: "Freelancerlikda yuksalish",
      ru: "Рост фриланс карьеры",
      tj: "Рушди фриланс карйера"
    },
    description: {
      en: "Collaborated with multiple clients building real-world digital solutions.",
      uz: "Bir nechta mijoz bilan real raqamli loyihalar ustida ishladim.",
      ru: "Сотрудничал с клиентами, создавая цифровые решения.",
      tj: "Бо муштариёни зиёд барои сохтани ҳалли рақамии воқеӣ ҳамкорӣ кардам."
    }
  }
];


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
      en: "Guided 20+ students from zero to hero in C++, frontend, and backend with structured lessons, real-world projects, and hands-on support.",
      uz: "20+ shogirdlarga C++, frontend va backendni 0 dan o‘rgatdim — nazariyani amaliyot bilan uyg‘unlashtirib, mustahkam bilim berdim.",
      ru: "Наставлял более 20 учеников по C++, фронтенду и бэкенду с упором на практику, реальные проекты и развитие навыков.",
      tj: "Беш аз 20 шогирдро дар C++, frontend ва backend аз сифр омӯхтам — бо дарсҳои мураттаб ва таҷрибаи амалӣ."
    }
  },
  {
    icon: Code2,
    title: {
      en: "Open Source",
      uz: "Ochiq manba",
      ru: "Опен-сорс",
      tj: "Open Source"
    },
    description: {
      en: "Actively contribute to GitHub and public repositories, sharing reusable code, templates, and tools that help the community build faster.",
      uz: "GitHub orqali ochiq manbali loyihalarga hissa qo‘shaman — kodlar, shablonlar va foydali vositalarni baham ko‘raman.",
      ru: "Активно участвую в open-source проектах, делюсь шаблонами и полезными инструментами для быстрого развития.",
      tj: "Дар GitHub ва лоиҳаҳои open-source фаъолона иштирок мекунам — абзорҳо ва шаблонҳоро дастрас менамоям."
    }
  },
  {
    icon: HeartHandshake,
    title: {
      en: "Ethical Tech",
      uz: "Axloqiy texnologiya",
      ru: "Этичные технологии",
      tj: "Технологияи ахлоқӣ"
    },
    description: {
      en: "I strive to create software that respects user privacy, promotes positive values, and solves real-world problems for good causes.",
      uz: "Foydalanuvchi maxfiyligini hurmat qiladigan, ijobiy qadriyatlarni targ‘ib qiladigan va jamiyatga foyda keltiradigan dasturlar yarataman.",
      ru: "Создаю технологии, уважающие конфиденциальность и способствующие решению социальных задач с добрыми намерениями.",
      tj: "Нармавсореро месозам, ки ахлоқро риоя мекунад ва ба нафъи ҷомеа хизмат мекунад."
    }
  },
  {
    icon: MessageCircle,
    title: {
      en: "Community Engagement",
      uz: "Jamoaviy ishtirok",
      ru: "Вовлеченность в сообщество",
      tj: "Фаъолият дар ҷомеа"
    },
    description: {
      en: "Consistently share tutorials, coding tips, and dev lifestyle content on Instagram and YouTube to inspire and educate others.",
      uz: "Instagram va YouTube’da kod yozish darslari, maslahatlar va dasturchilar hayoti haqida kontent ulashib kelmoqdaman.",
      ru: "Делюсь лайфхаками, туториалами и мотивационным контентом в Instagram и YouTube для начинающих программистов.",
      tj: "Дар Instagram ва YouTube дарсҳо, маслиҳатҳо ва ҳаёти барномасозиро нақл мекунам."
    }
  },
  {
    icon: BookOpenCheck,
    title: {
      en: "Continuous Learning",
      uz: "Doimiy o‘rganish",
      ru: "Непрерывное обучение",
      tj: "Омӯзиши пайваста"
    },
    description: {
      en: "Always hungry for growth — from Laravel upgrades to mastering TypeScript, I dedicate time to sharpen my skills daily.",
      uz: "Laravel yangiliklaridan tortib TypeScript’gacha — har kuni bilimimni oshirishga vaqt ajrataman.",
      ru: "Постоянно учусь и слежу за новыми технологиями, прокачивая навыки каждый день.",
      tj: "Ҳар рӯз барои омӯхтани технологияҳои нав, малакаҳои худро такмил медиҳам."
    }
  }
];

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
      uz: "Tahlil bosqichi",
      ru: "Изучение",
      tj: "Омӯзиш"
    },
    description: {
      en: "Understanding your requirements, goals, and target audience",
      uz: "Talablaringiz, maqsadlaringiz va auditoriyangizni tushunish",
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
      uz: "Loyihangiz uchun batafsil reja va texnik arxitektura yaratish",
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
      uz: "Toza, kengaytiriladigan va saqlab bo‘ladigan kod bilan yechim yaratish",
      ru: "Создание решения с чистым, масштабируемым и поддерживаемым кодом",
      tj: "Сохтани ҳалли масъала бо коди пок ва нигоҳдошташаванда"
    }
  },
  {
    step: "04",
    title: {
      en: "Delivery",
      uz: "Yetkazish",
      ru: "Доставка",
      tj: "Супоридан"
    },
    description: {
      en: "Testing, deployment, and ongoing support to ensure success",
      uz: "Test qilish, ishga tushurish va doimiy texnik yordam",
      ru: "Тестирование, развертывание и постоянная поддержка",
      tj: "Санҷиш, татбиқ ва дастгирии доимӣ барои муваффақият"
    }
  }
];

// Blog Posts
export const blogPosts = [
  {
    id: 1,
    title: {
      en: "Top 10 Phrasal Verbs for Developers",
      uz: "Dasturchilar uchun 10 ta eng yaxshi frazeologik fe'l",
      ru: "Топ-10 фразовых глаголов для разработчиков",
      tj: "10 феълҳои иборати беҳтарин барои барномасозон"
    },
    excerpt: {
      en: "Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
      uz: "Texnologik suhbatlarda 'figure out', 'break down' va 'carry on' kabi fe'llardan qanday foydalanishni o'rganing.",
      ru: "Узнайте, как использовать фразовые глаголы как 'figure out', 'break down', 'carry on' в IT-разговорах.",
      tj: "Ёд гиред, ки чӣ гуна феълҳои иборавиеро монанди 'figure out', 'break down', 'carry on' дар муколамаҳои техникӣ истифода баред."
    },
    date: "2025-01-15",
    readTime: "5 min read",
    tags: ["English", "Communication"],
    image: "https://images.pexels.com/photos/1181671/pexels-photo-1181671.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "phrasal-verbs-developers"
  },
  {
    id: 2,
    title: {
      en: "Laravel vs Node.js – Which Backend to Choose?",
      uz: "Laravel vs Node.js – Qaysi Backendni Tanlash kerak?",
      ru: "Laravel против Node.js – Что выбрать?",
      tj: "Laravel ё Node.js – Кадомаш беҳтар аст?"
    },
    excerpt: {
      en: "An honest comparison between two backend giants from a full-stack developer's perspective.",
      uz: "Full-stack dasturchi nuqtai nazaridan ikki backend giganti haqida samimiy taqqoslash.",
      ru: "Честное сравнение двух гигантов бэкенда с точки зрения full-stack разработчика.",
      tj: "Муқоисаи ростини ду абарқудрати backend аз дидгоҳи full-stack developer."
    },
    date: "2025-01-10",
    readTime: "8 min read",
    tags: ["Backend", "Laravel", "Node.js"],
    image: "https://images.pexels.com/photos/11035380/pexels-photo-11035380.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "laravel-vs-nodejs"
  },
  {
    id: 3,
    title: {
      en: "Building Modern UIs with React and Tailwind",
      uz: "React va Tailwind bilan zamonaviy UI yaratish",
      ru: "Создание современных UI с React и Tailwind",
      tj: "Сохтани UI-и муосир бо React ва Tailwind"
    },
    excerpt: {
      en: "Best practices for creating beautiful, responsive interfaces using React and Tailwind CSS.",
      uz: "React va Tailwind CSS yordamida chiroyli va responsiv interfeyslar yaratish bo'yicha eng yaxshi amaliyotlar.",
      ru: "Лучшие практики создания красивых, адаптивных интерфейсов с React и Tailwind CSS.",
      tj: "Беҳтарин усулҳо барои сохтани интерфейсҳои зебо ва ҷавобгӯ бо React ва Tailwind CSS."
    },
    date: "2025-01-05",
    readTime: "6 min read",
    tags: ["React", "Tailwind", "Frontend"],
    image: "https://images.pexels.com/photos/11035471/pexels-photo-11035471.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "react-tailwind-modern-ui"
  }
];