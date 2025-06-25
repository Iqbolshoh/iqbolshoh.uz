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
    name: "Templates.uz",
    description: "A SaaS platform to build professional websites using drag & drop tools with Laravel backend and React frontend.",
    image: "https://images.pexels.com/photos/196644/pexels-photo-196644.jpeg?auto=compress&cs=tinysrgb&w=800",
    tech: ["Laravel", "React", "Vite", "MySQL"],
    liveDemo: "https://templates.uz",
    github: "https://github.com/iqbolshoh/templates-uz",
    featured: true
  },
  {
    id: 2,
    name: "Personal Portfolio",
    description: "Modern portfolio website showcasing my skills, projects, and professional journey in web development.",
    image: "https://images.pexels.com/photos/270404/pexels-photo-270404.jpeg?auto=compress&cs=tinysrgb&w=800",
    tech: ["React", "TypeScript", "Tailwind"],
    liveDemo: "https://iqbolshoh.uz",
    github: "https://github.com/iqbolshoh/portfolio",
    featured: true
  },
  {
    id: 3,
    name: "E-Commerce Platform",
    description: "Full-featured online store with admin panel, payment integration, and inventory management.",
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
    title: "Web Development",
    icon: Monitor,
    description: "Building responsive websites, admin panels, and full-stack systems using Laravel and React.",
    price: "Starting from $200",
    features: ["Responsive Design", "Admin Panels", "Database Integration", "API Development"]
  },
  {
    id: 2,
    title: "Frontend UI Design",
    icon: Palette,
    description: "Pixel-perfect, modern UI designs with Tailwind CSS and responsive layouts.",
    price: "Starting from $100",
    features: ["Figma to Code", "Responsive Design", "Interactive UI", "Performance Optimized"]
  },
  {
    id: 3,
    title: "Backend & APIs",
    icon: Lock,
    description: "Secure, scalable APIs with Laravel, JWT authentication, and MySQL databases.",
    price: "Starting from $150",
    features: ["RESTful APIs", "JWT Authentication", "Database Design", "Security Implementation"]
  },
  {
    id: 4,
    title: "WordPress CMS",
    icon: Package,
    description: "Custom WordPress websites and themes for small businesses and blogs.",
    price: "Starting from $120",
    features: ["Custom Themes", "Plugin Development", "SEO Optimization", "Content Management"]
  },
  {
    id: 5,
    title: "Mentorship",
    icon: BookOpen,
    description: "1-on-1 mentoring sessions for beginner developers in web development.",
    price: "$15/hour",
    features: ["Code Reviews", "Career Guidance", "Technical Skills", "Project Support"]
  }
];

export const blogPosts = [
  {
    id: 1,
    title: "Top 10 Phrasal Verbs for Developers",
    excerpt: "Learn how to use phrasal verbs like 'figure out', 'break down', and 'carry on' in tech conversations.",
    date: "2025-01-15",
    readTime: "5 min read",
    tags: ["English", "Communication"],
    image: "https://images.pexels.com/photos/1181671/pexels-photo-1181671.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "phrasal-verbs-developers"
  },
  {
    id: 2,
    title: "Laravel vs Node.js – Which Backend to Choose?",
    excerpt: "An honest comparison between two backend giants from a full-stack developer's perspective.",
    date: "2025-01-10",
    readTime: "8 min read",
    tags: ["Backend", "Laravel", "Node.js"],
    image: "https://images.pexels.com/photos/11035380/pexels-photo-11035380.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "laravel-vs-nodejs"
  },
  {
    id: 3,
    title: "Building Modern UIs with React and Tailwind",
    excerpt: "Best practices for creating beautiful, responsive interfaces using React and Tailwind CSS.",
    date: "2025-01-05",
    readTime: "6 min read",
    tags: ["React", "Tailwind", "Frontend"],
    image: "https://images.pexels.com/photos/11035471/pexels-photo-11035471.jpeg?auto=compress&cs=tinysrgb&w=800",
    slug: "react-tailwind-modern-ui"
  }
];