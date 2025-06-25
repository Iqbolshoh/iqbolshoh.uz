import {
  Code,          // React
  FileCode,      // TypeScript
  Palette,       // Tailwind CSS
  FileText,      // PHP
  Server,        // Laravel
  Database,      // MySQL
  Monitor,       // HTML
  BookOpen,      // CSS
  Package,       // Bootstrap
  Brush,          // Sass
  Terminal,      // JavaScript
  Network,         // API (RESTful, external)
  Lock,
  Gauge,
} from 'lucide-react';

export const personalInfo = {
  name: "Iqbolshoh Ilhomjonov",
  title: "Full-Stack Developer",
  location: "Samarkand, Uzbekistan",
  email: "iilhomjonov777@gmail.com",
  phone: "+998 (99) 779 93 33",
  bio: "A passionate Full-Stack Developer with over 3 years of experience in Laravel, React, and Node.js. 3rd-year Software Engineering student at Samarkand State University.",
  avatar: "/images/logos/iqbolshoh.jpg",
  social: {
    github: "https://github.com/iqbolshoh",
    linkedin: "https://linkedin.com/in/iqbolshoh",
    telegram: "https://t.me/iqbolshoh_777",
    instagram: "https://instagram.com/iqbolshoh_777"
  }
};

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
    featured: true
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
    featured: true
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
    featured: false
  }
];

export const services = [
  {
    id: 1,
    title: {
      en: "Web Development",
      uz: "Veb Dasturlash",
      ru: "Веб-разработка",
      tj: "Веб-таҳия"
    },
    icon: Monitor,
    description: {
      en: "Building responsive websites, admin panels, and full-stack systems using Laravel and React.",
      uz: "Laravel va React yordamida responsiv saytlar, admin panellar va to‘liq tizimlar yaratish.",
      ru: "Создание адаптивных сайтов, админ-панелей и full-stack систем с использованием Laravel и React.",
      tj: "Сохтани сайтҳои ҷавобгӯ, панелҳои маъмурӣ ва системаҳое, ки бо Laravel ва React таҳия шудаанд."
    },
    price: "$200",
    features: {
      en: ["Responsive Design", "Admin Panels", "Database Integration", "API Development"],
      uz: ["Responsiv Dizayn", "Admin Panellar", "Ma’lumotlar Bazasi", "API Dasturlash"],
      ru: ["Адаптивный дизайн", "Админ-панели", "Интеграция базы данных", "Разработка API"],
      tj: ["Тарҳи ҷавобгӯ", "Панелҳои маъмурӣ", "Пойгоҳи додаҳо", "Таҳияи API"]
    }
  },
  {
    id: 2,
    title: {
      en: "Frontend UI Design",
      uz: "Frontend UI Dizayn",
      ru: "Дизайн интерфейса",
      tj: "Тарроҳии UI"
    },
    icon: Palette,
    description: {
      en: "Pixel-perfect, modern UI designs with Tailwind CSS and responsive layouts.",
      uz: "Tailwind CSS asosida zamonaviy, pixel-perfect dizaynlar.",
      ru: "Современные дизайны UI с Tailwind CSS и адаптивной вёрсткой.",
      tj: "Тарроҳии муосири UI бо Tailwind CSS ва тарҳбандии ҷавобгӯ."
    },
    price: "$100",
    features: {
      en: ["Figma to Code", "Responsive Design", "Interactive UI", "Performance Optimized"],
      uz: ["Figma dan kodga", "Responsiv dizayn", "Interaktiv interfeys", "Yaxshilangan ishlash"],
      ru: ["Из Figma в код", "Адаптивный дизайн", "Интерактивный интерфейс", "Оптимизация производительности"],
      tj: ["Аз Figma ба код", "Тарҳи ҷавобгӯ", "Интерфейси интерактивӣ", "Самаранокии беҳтаршуда"]
    }
  },
  {
    id: 3,
    title: {
      en: "Backend & APIs",
      uz: "Backend va APIlar",
      ru: "Бэкенд и API",
      tj: "Бекенд ва APIҳо"
    },
    icon: Lock,
    description: {
      en: "Secure, scalable APIs with Laravel, JWT authentication, and MySQL databases.",
      uz: "Laravel, JWT autentifikatsiyasi va MySQL yordamida xavfsiz APIlar.",
      ru: "Безопасные, масштабируемые API с Laravel, JWT и MySQL.",
      tj: "API-ҳои боэътимод ва васеъшаванда бо истифодаи Laravel, JWT ва MySQL."
    },
    price: "$150",
    features: {
      en: ["RESTful APIs", "JWT Authentication", "Database Design", "Security Implementation"],
      uz: ["RESTful APIlar", "JWT autentifikatsiyasi", "Bazani loyihalash", "Xavfsizlikni joriy etish"],
      ru: ["RESTful API", "JWT аутентификация", "Проектирование БД", "Реализация безопасности"],
      tj: ["API-ҳои RESTful", "Аутентификатсияи JWT", "Тарҳрезии пойгоҳи додаҳо", "Татбиқи амният"]
    }
  },
  {
    id: 4,
    title: {
      en: "WordPress CMS",
      uz: "WordPress CMS",
      ru: "WordPress CMS",
      tj: "WordPress CMS"
    },
    icon: Package,
    description: {
      en: "Custom WordPress websites and themes for small businesses and blogs.",
      uz: "Kichik biznes va bloglar uchun WordPress sayt va mavzular.",
      ru: "Индивидуальные WordPress сайты и темы для малого бизнеса и блогов.",
      tj: "Сайтҳо ва мавзуҳои фармоишии WordPress барои тиҷорати хурд ва блогҳо."
    },
    price: "$120",
    features: {
      en: ["Custom Themes", "Plugin Development", "SEO Optimization", "Content Management"],
      uz: ["Maxsus mavzular", "Plaginlar yaratish", "SEO optimallashtirish", "Kontent boshqaruvi"],
      ru: ["Индивидуальные темы", "Разработка плагинов", "Оптимизация SEO", "Управление контентом"],
      tj: ["Мавзӯъҳои фармоишӣ", "Таҳияи плугинҳо", "Оптимизатсияи SEO", "Идоракунии муҳтаво"]
    }
  },
  {
    id: 5,
    title: {
      en: "Mentorship",
      uz: "Mentorlik",
      ru: "Наставничество",
      tj: "Менторӣ"
    },
    icon: BookOpen,
    description: {
      en: "1-on-1 mentoring sessions for beginner developers in web development.",
      uz: "Yangi boshlovchilar uchun veb dasturlash bo‘yicha 1-on-1 mentorlik.",
      ru: "Индивидуальные сессии наставничества для начинающих веб-разработчиков.",
      tj: "Машваратҳои як ба як барои барномасозони навкор дар соҳаи веб."
    },
    price: "$15/hour",
    features: {
      en: ["Code Reviews", "Career Guidance", "Technical Skills", "Project Support"],
      uz: ["Kod tekshiruvlari", "Kasbiy yo‘naltirish", "Texnik ko‘nikmalar", "Loyihaga yordam"],
      ru: ["Код-ревью", "Профориентация", "Технические навыки", "Поддержка проектов"],
      tj: ["Таҳлили код", "Роҳнамоии касбӣ", "Маҳорати техникӣ", "Дастгирии лоиҳа"]
    }
  },
  {
    id: 6,
    title: {
      en: "SEO & Performance Optimization",
      uz: "SEO va Ishlashni Optimallashtirish",
      ru: "SEO и Оптимизация Производительности",
      tj: "SEO ва Беҳтарсозии Самаранокӣ"
    },
    icon: Gauge,
    description: {
      en: "Improve your website’s visibility on search engines and ensure it loads fast across all devices.",
      uz: "Saytingizni qidiruv tizimlarida yuqoriga olib chiqish va barcha qurilmalarda tez yuklanishini ta’minlash.",
      ru: "Улучшение видимости сайта в поисковиках и обеспечение высокой скорости загрузки на всех устройствах.",
      tj: "Баланд бардоштани диданпазирии сайт дар ҷустуҷӯгарҳо ва таъмин кардани суръати баланди боркунӣ дар ҳамаи дастгоҳҳо."
    },
    price: "$100",
    features: {
      en: ["Technical SEO Audit", "Image & Code Optimization", "PageSpeed Improvements", "Mobile Optimization"],
      uz: ["Texnik SEO auditi", "Rasm va kodni optimallashtirish", "Sahifa tezligi", "Mobil optimizatsiya"],
      ru: ["Технический SEO аудит", "Оптимизация изображений и кода", "Улучшение скорости страницы", "Мобильная оптимизация"],
      tj: ["SEO аудити", "Оптимизатсияи расм ва код", "Беҳтарсозии суръати саҳифа", "Оптимизатсияи мобилӣ"]
    }
  }
];

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

export const blogPosts = [
  {
    id: 1,
    title: {
      en: "Top 10 Phrasal Verbs for Developers",
      uz: "Dasturchilar uchun 10 ta eng yaxshi frazeologik fe’l",
      ru: "Топ-10 фразовых глаголов для разработчиков",
      tj: "10 феълҳои иборати беҳтарин барои барномасозон"
    },
    excerpt: {
      en: "Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
      uz: "Texnologik suhbatlarda 'figure out', 'break down' va 'carry on' kabi fe’llardan qanday foydalanishni o‘rganing.",
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
      uz: "React va Tailwind CSS yordamida chiroyli va responsiv interfeyslar yaratish bo‘yicha eng yaxshi amaliyotlar.",
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