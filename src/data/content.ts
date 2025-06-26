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
  name: "Iqbolshoh Ilhomjonov",
  // title: "Full-Stack Developer",
  location: "Samarkand, Uzbekistan",
  // bio: "A passionate Full-Stack Developer with over 3 years of experience in Laravel, React, and Node.js. 4rd-year Software Engineering student at Samarkand State University.",
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
      ru: "Университетни начал",
      tj: "Донишгоҳро оғоз кардам"
    },
    description: {
      en: "Started studying Software Engineering at Samarkand State University",
      uz: "Samarkand State Universityda Dasturiy injiniring yo‘nalishida o‘qishni boshladim",
      ru: "Начал обучение по направлению Программная инженерия в Samarkand State University",
      tj: "Омӯзиши муҳандисии нармафзорро дар Samarkand State University оғоз кардам"
    }
  },
  {
    year: "2022",
    title: {
      en: "Learned Frontend Foundations",
      uz: "Frontend asoslarini o‘rgandim",
      ru: "Изучил основы Frontend",
      tj: "Асосҳои frontend-ро омӯхтам"
    },
    description: {
      en: "Learned the fundamentals of HTML, CSS, JavaScript, and React — my first step into frontend development",
      uz: "HTML, CSS, JavaScript va React asoslarini o‘rgandim — frontend rivojlanishiga ilk qadammim",
      ru: "Изучил основы HTML, CSS, JavaScript и React — мой первый шаг в frontend-разработку",
      tj: "Асосҳои HTML, CSS, JavaScript ва React-ро омӯхтам — қадами аввалини ман ба рушди frontend буд"
    }
  },
  {
    year: "2023",
    title: {
      en: "Mastered Backend Development",
      uz: "Backend rivojini chuqur o‘rgandim",
      ru: "Углубился в backend-разработку",
      tj: "Рушди backend-ро амиқ омӯхтам"
    },
    description: {
      en: "Specialized in backend with Laravel and REST APIs — discovered my passion for backend development",
      uz: "Laravel va REST APIlar bilan backendga ixtisoslashdim — backendga bo‘lgan ishtiyoqimni kashf etdim",
      ru: "Специализировался в backend с использованием Laravel и REST API — открыл свою страсть к backend-разработке",
      tj: "Бо истифодаи Laravel ва REST API дар backend ихтисос ёфтам — иштиёқам ба backend-ро дарёфтам"
    }
  },
  {
    year: "2023",
    title: {
      en: "First Full Freelance Project",
      uz: "Birinchi to‘liq freelance loyiham",
      ru: "Первый полноценный фриланс-проект",
      tj: "Аввалин лоиҳаи пурраи фриланс"
    },
    description: {
      en: "Built a full-stack client project from scratch using Laravel and React",
      uz: "Laravel va React yordamida noldan to‘liq mijoz loyihasini yaratdim",
      ru: "С нуля создал полноценный клиентский full-stack проект с использованием Laravel и React",
      tj: "Лоиҳаи пурраи муштариро бо истифодаи Laravel ва React аз сифр сохтам"
    }
  },
  {
    year: "2024",
    title: {
      en: "Contributed to Open Source",
      uz: "Open-sourcega hissa qo‘shdim",
      ru: "Сделал вклад в Open Source",
      tj: "Ба Open Source саҳм гузоштам"
    },
    description: {
      en: "Published open repositories and joined global development communities",
      uz: "Ochiq repositorylar e’lon qilib, global dasturchilar hamjamiyatiga qo‘shildim",
      ru: "Опубликовал открытые репозитории и присоединился к международным сообществам разработчиков",
      tj: "Repository-ҳои кушодаро нашр карда, ба ҷомеаҳои байналмилалии барномасозон ҳамроҳ шудам"
    }
  },
  {
    year: "2024",
    title: {
      en: "Launched Personal Portfolio Website",
      uz: "Shaxsiy portfolio saytini ishga tushirdim",
      ru: "Запустил личный сайт-портфолио",
      tj: "Сомонаи шахсии портфолиоро оғоз кардам"
    },
    description: {
      en: "Launched iqbolshoh.uz to present my projects, applications, and technical skills",
      uz: "Iqbolshoh.uz saytini ishga tushirdim — unda loyihalarim, ilovalarim va texnik ko‘nikmalarim mavjud",
      ru: "Запустил сайт iqbolshoh.uz — на нём представлены мои проекты, приложения и технические навыки",
      tj: "Сомонаи iqbolshoh.uz-ро оғоз кардам — дар он лоиҳаҳо, барномаҳо ва малакаҳои техникии ман ҷой дода шудаанд"
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
      en: "Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
      uz: "Texnologik suhbatlarda 'figure out', 'break down' va 'carry on' kabi fe'llardan qanday foydalanishni o'rganing.",
      ru: "Узнайте, как использовать фразовые глаголы как 'figure out', 'break down', 'carry on' в IT-разговорах.",
      tj: "Ёд гиред, ки чӣ гуна феълҳои иборавиеро монанди 'figure out', 'break down', 'carry on' дар муколамаҳои техникӣ истифода баред."
    },
    featured: false,
    date: "2025-01-15",
    readTime: "5 min read",
    tags: ["English", "Communication"],
    image: "https://images.pexels.com/photos/1181671/pexels-photo-1181671.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "phrasal-verbs-developers"
  },
  {
    id: 2,
    title: {
      en: "Laravel vs Node.js – Which Backend to Choose?Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
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
    featured: false,
    date: "2025-01-10",
    readTime: "8 min read",
    tags: ["Backend", "Laravel", "Node.js"],
    image: "https://images.pexels.com/photos/11035380/pexels-photo-11035380.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "laravel-vs-nodejs"
  },
  {
    id: 3,
    title: {
      en: "Building Modern UIs with React and TailwindLearn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
      uz: "React va Tailwind bilan zamonaviy UI yaratish",
      ru: "Создание современных UI с React и Tailwind",
      tj: "Сохтани UI-и муосир бо React ва Tailwind"
    },
    excerpt: {
      en: "Best practices for creating beautiful, responsive interfaces using React and Tailwind CSS.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
      uz: "React va Tailwind CSS yordamida chiroyli va responsiv interfeyslar yaratish bo'yicha eng yaxshi amaliyotlar.",
      ru: "Лучшие практики создания красивых, адаптивных интерфейсов с React и Tailwind CSS.",
      tj: "Беҳтарин усулҳо барои сохтани интерфейсҳои зебо ва ҷавобгӯ бо React ва Tailwind CSS."
    },
    featured: true,
    date: "2025-01-05",
    readTime: "6 min read",
    tags: ["React", "Tailwind", "Frontend"],
    image: "https://images.pexels.com/photos/11035471/pexels-photo-11035471.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "react-tailwind-modern-ui"
  },
];