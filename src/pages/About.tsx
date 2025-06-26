import React from 'react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { GraduationCap, MapPin, Calendar, Heart, Code2, Coffee, Award, Users, Star } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { personalInfo, techStack } from '../data/content';

export const About: React.FC = () => {
  const { t, i18n } = useTranslation();

  // Helper function to get localized content
  const getLocalizedContent = (content: Record<string, string>, language: string) => {
    return content[language] || content.en || '';
  };

  const highlights = [
    {
      icon: Award,
      text: {
        en: "3+ years of coding experience",
        uz: "3+ yillik dasturlash tajribasi",
        ru: "3+ года опыта программирования",
        tj: "3+ сол таҷрибаи барномасозӣ",
      },
    },
    {
      icon: Coffee,
      text: {
        en: "Mentor to junior developers",
        uz: "Yosh dasturchilarga mentorlik",
        ru: "Ментор для junior разработчиков",
        tj: "Менти барои барномасозони ҷавон",
      },
    },
    {
      icon: Users,
      text: {
        en: "Focused on ethical tech & innovation",
        uz: "Axloqiy texnologiya va innovatsiyalarga e'tibor",
        ru: "Фокус на этичные технологии и инновации",
        tj: "Диққат ба технологияҳои ахлоқӣ ва навовариҳо",
      },
    },
    {
      icon: Star,
      text: {
        en: "Building Templates.uz SaaS platform",
        uz: "Templates.uz SaaS platformasini qurish",
        ru: "Создание SaaS платформы Templates.uz",
        tj: "Сохтани платформаи Templates.uz",
      },
    },
    {
      icon: Coffee,
      text: {
        en: "Active in open-source community",
        uz: "Ochiq manba hamjamiyatida faol",
        ru: "Активен в open-source сообществе",
        tj: "Фаъол дар ҷомеаи open-source",
      },
    },
  ];

  const journey = [
    {
      year: "2022",
      title: {
        en: "Started University",
        uz: "Universitetni boshlash",
        ru: "Начал университет",
        tj: "Донишгоҳро оғоз кард"
      },
      description: {
        en: "Began Software Engineering at Samarkand State University",
        uz: "Samarqand Davlat Universitetida Dasturiy Injiniring yo'nalishini boshlash",
        ru: "Начал изучать программную инженерию в Самаркандском государственном университете",
        tj: "Оғози муҳандисии нармафзор дар Донишгоҳи давлатии Самарқанд"
      }
    },
    {
      year: "2022",
      title: {
        en: "First Web Project",
        uz: "Birinchi veb loyiha",
        ru: "Первый веб-проект",
        tj: "Лоиҳаи аввалини веб"
      },
      description: {
        en: "Built my first Laravel application and fell in love with backend development",
        uz: "Birinchi Laravel ilovasini yaratish va backend dasturlashga oshiq bo'lish",
        ru: "Создал своё первое приложение на Laravel и влюбился в backend разработку",
        tj: "Эҷоди барномаи аввалини Laravel ва ошиқ шудан ба backend барномасозӣ"
      }
    },
    {
      year: "2023",
      title: {
        en: "Frontend Focus",
        uz: "Frontendga e'tibor",
        ru: "Фокус на Frontend",
        tj: "Диққат ба Frontend"
      },
      description: {
        en: "Mastered React and started building full-stack applications",
        uz: "React-ni o'rganish va full-stack ilovalar qurishni boshlash",
        ru: "Освоил React и начал создавать full-stack приложения",
        tj: "Омӯзиши React ва оғози сохтани барномаҳои full-stack"
      }
    },
    {
      year: "2024",
      title: {
        en: "Templates.uz",
        uz: "Templates.uz",
        ru: "Templates.uz",
        tj: "Templates.uz"
      },
      description: {
        en: "Launched my SaaS platform for website building",
        uz: "Vebsayt yaratish uchun SaaS platformamni ishga tushirish",
        ru: "Запустил свою SaaS платформу для создания сайтов",
        tj: "Оғози платформаи SaaS-ии ман барои сохтани сомонаҳо"
      }
    },
    {
      year: "2025",
      title: {
        en: "Freelancing",
        uz: "Freelancerlik",
        ru: "Фриланс",
        tj: "Фриланс"
      },
      description: {
        en: "Started taking on client projects and mentoring developers",
        uz: "Mijozlar loyihalarini qabul qilish va dasturchilarga mentorlik qilishni boshlash",
        ru: "Начал брать проекты клиентов и менторить разработчиков",
        tj: "Оғози қабули лоиҳаҳои муштариён ва роҳнамоӣ ба барномасозон"
      }
    }
  ];

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
            >
              <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
                {t('about.title')}
              </h1>
              <p className="mt-6 text-xl text-gray-600 leading-relaxed">
                {t('about.description')}
              </p>
              <p className="mt-4 text-lg text-gray-600">
                {t('about.subDescription')}
              </p>

              <div className="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="flex items-center space-x-2 text-gray-600">
                  <MapPin className="h-5 w-5 text-primary-600" />
                  <span>{personalInfo.location}</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600">
                  <GraduationCap className="h-5 w-5 text-primary-600" />
                  <span>{t('about.locationInfo')}</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600">
                  <Calendar className="h-5 w-5 text-primary-600" />
                  <span>{t('about.experienceInfo')}</span>
                </div>
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="mt-12 lg:mt-0"
            >
              <div className="relative">
                <img
                  src={personalInfo.avatar}
                  alt={personalInfo.name}
                  className="rounded-2xl shadow-2xl"
                />
                <div className="absolute -bottom-6 -right-6 bg-white rounded-xl p-4 shadow-lg">
                  <div className="flex items-center space-x-2">
                    <Heart className="h-5 w-5 text-red-500" />
                    <span className="text-sm font-medium">{t('about.codingPassion')}</span>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Highlights */}
      <section className="py-16 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {highlights.map((item, index) => {
              const Icon = item.icon;
              const lang = i18n.language as keyof typeof item.text;

              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <Card className="p-6 text-center hover:bg-primary-50 transition-all duration-300 hover:shadow-glow-red group border-0">
                    <Icon className="h-10 w-10 text-primary-600 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300" />
                    <p className="font-medium text-gray-900">{item.text[lang]}</p>
                  </Card>
                </motion.div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Journey Timeline */}
      <section className="py-20 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">{t('about.journey')}</h2>
            <p className="mt-4 text-lg text-gray-600">{t('about.journeyDescription')}</p>
          </motion.div>

          <div className="relative">
            <div className="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-primary-200"></div>
            <div className="space-y-12">
              {journey.map((item, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, x: index % 2 === 0 ? -20 : 20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className={`relative flex items-center ${index % 2 === 0 ? 'flex-row' : 'flex-row-reverse'}`}
                >
                  <div className="flex-1">
                    <Card className={`p-6 ${index % 2 === 0 ? 'mr-8' : 'ml-8'}`}>
                      <div className="flex items-center space-x-2 mb-2">
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                          {item.year}
                        </span>
                      </div>
                      <h3 className="text-xl font-bold text-gray-900 mb-2">
                        {getLocalizedContent(item.title, i18n.language)}
                      </h3>
                      <p className="text-gray-600">
                        {getLocalizedContent(item.description, i18n.language)}
                      </p>
                    </Card>
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Skills & Interests */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
            >
              <h2 className="text-3xl font-bold text-gray-900 mb-8">{t('about.technicalSkills')}</h2>
              <div className="space-y-4">
                {techStack.map((tech, index) => {
                  const Icon = tech.icon;
                  return (
                    <div key={tech.name} className="flex items-center space-x-4">
                      <Icon className="h-6 w-6 text-primary-600" />
                      <div className="flex-1">
                        <div className="flex justify-between mb-1">
                          <span className="font-medium text-gray-900">{tech.name}</span>
                          <span className="text-sm text-gray-600">{tech.level}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <motion.div
                            initial={{ width: 0 }}
                            whileInView={{ width: `${tech.level}%` }}
                            transition={{ duration: 1, delay: index * 0.1 }}
                            viewport={{ once: true }}
                            className="bg-primary-600 h-2 rounded-full"
                          ></motion.div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="mt-10 lg:mt-0"
            >
              <h2 className="text-3xl font-bold text-gray-900 mb-8">{t('about.beyondCoding')}</h2>
              <div className="space-y-6">
                <div className="flex items-start space-x-4">
                  <Coffee className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">{t('about.mentoring')}</h3>
                    <p className="text-gray-600">{t('about.mentoringDesc')}</p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <Code2 className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">{t('about.openSource')}</h3>
                    <p className="text-gray-600">{t('about.openSourceDesc')}</p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <Heart className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">{t('about.ethicalTech')}</h3>
                    <p className="text-gray-600">{t('about.ethicalTechDesc')}</p>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};