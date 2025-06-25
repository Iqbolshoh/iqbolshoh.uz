import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { ArrowRight, Download, Star, Users, Coffee, Award } from 'lucide-react';
import { Button } from '../components/UI/Button';
import { Card } from '../components/UI/Card';
import { personalInfo, techStack, projects } from '../data/content';

export const Home: React.FC = () => {
  const { t } = useTranslation();

  const stats = [
    { label: t('home.yearsExperience'), value: '3+', icon: Star },
    { label: t('home.projectsCompleted'), value: '50+', icon: Award },
    { label: t('home.happyClients'), value: '25+', icon: Users },
    { label: t('home.cupsOfCoffee'), value: '1000+', icon: Coffee },
  ];

  return (
    <div className="pt-16 relative">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 py-16 sm:py-24 lg:py-32 xl:py-40">
        <div className="absolute inset-0 bg-light-pattern opacity-30"></div>
        <div className="absolute inset-0 bg-gradient-to-r from-primary-500/5 to-accent-500/5"></div>

        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">

            {/* TEXT SECTION */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="lg:col-span-7 text-center lg:text-left"
            >
              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                {t('home.greeting')}{' '}
                <span className="bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">
                  {t('home.name')}
                </span>
              </h1>

              <p className="mt-6 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0">
                {t('home.description')}
              </p>

              <div className="mt-8 flex flex-col sm:flex-row sm:justify-center lg:justify-start gap-4">
                <Link to="/portfolio">
                  <Button
                    size="lg"
                    icon={<ArrowRight className="h-5 w-5" />}
                    className="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-glow-red w-full sm:w-auto"
                  >
                    {t('home.viewPortfolio')}
                  </Button>
                </Link>
                <Button
                  variant="outline"
                  size="lg"
                  icon={<Download className="h-5 w-5" />}
                  href="/uploads/iqbolshoh-cv.pdf"
                  download
                  className="border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white w-full sm:w-auto"
                >
                  {t('home.downloadCV')}
                </Button>
              </div>
            </motion.div>

            {/* IMAGE SECTION */}
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.6, delay: 0.3 }}
              className="lg:col-span-5 flex justify-center"
            >
              <div className="relative w-[240px] sm:w-[280px] md:w-[320px] lg:w-[360px] xl:w-[420px]">
                <div className="aspect-square rounded-full bg-gradient-to-tr from-primary-500 to-primary-400 p-1 shadow-glow-red-lg">
                  <img
                    src={personalInfo.avatar}
                    alt={personalInfo.name}
                    className="h-full w-full rounded-full object-cover"
                  />
                </div>

                {/* Floating tech icons */}
                <div className="absolute -bottom-4 -right-4 bg-white rounded-full p-4 shadow-lg border-2 border-primary-200">
                  <div className="flex space-x-1">
                    {techStack.slice(0, 3).map((tech, index) => {
                      const Icon = tech.icon;
                      return (
                        <div
                          key={tech.name}
                          className="text-2xl animate-float"
                          style={{ animationDelay: `${index * 0.5}s` }}
                        >
                          <Icon className="h-6 w-6 text-primary-600" />
                        </div>
                      );
                    })}
                  </div>
                </div>

                {/* Floating logos */}
                <div className="absolute -top-6 -left-6 animate-bounce-slow">
                  <img src="/images/logos/iqbolshoh_dev.svg" alt="Logo" className="h-8 w-8 opacity-20" />
                </div>
                <div
                  className="absolute -bottom-8 -left-8 animate-bounce-slow"
                  style={{ animationDelay: '1s' }}
                >
                  <img src="/images/logos/iqbolshoh_dev.svg" alt="Logo" className="h-6 w-6 opacity-15" />
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-16 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">{t('home.techStack')}</h2>
            <p className="mt-4 text-lg text-gray-600">{t('home.technologiesILove')}</p>
          </motion.div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            {techStack.map((tech, index) => {
              const Icon = tech.icon; // Get the icon component
              return (
                <motion.div
                  key={tech.name}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <Card className="p-6 text-center hover:bg-primary-50 transition-all duration-300 hover:shadow-glow-red group border-0">
                    <Icon className="h-10 w-10 text-primary-600 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300" />
                    <h3 className="font-semibold text-gray-900">{tech.name}</h3>
                  </Card>
                </motion.div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Stats */}
      <section className="py-16 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">{t('home.journeyTitle')}</h2>
            <p className="mt-4 text-lg text-gray-600">{t('home.journeyDescription')}</p>
          </motion.div>
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.label}
                initial={{ opacity: 0, scale: 0.8 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6 text-center hover:shadow-glow-red transition-all duration-300 group border-0">
                  <stat.icon className="h-8 w-8 text-primary-600 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300" />
                  <div className="text-3xl font-bold text-gray-900 mb-2 bg-gradient-to-r from-primary-700 to-primary-600 bg-clip-text text-transparent">{stat.value}</div>
                  <div className="text-sm text-gray-600">{stat.label}</div>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Projects */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">{t('home.featuredProjects')}</h2>
            <p className="mt-4 text-lg text-gray-600">{t('home.someRecentWork')}</p>
          </motion.div>

          <div className="grid md:grid-cols-2 gap-8 mb-12">
            {projects.filter(p => p.featured).map((project, index) => (
              <motion.div
                key={project.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.2 }}
                viewport={{ once: true }}
              >
                <Card className="overflow-hidden hover:shadow-glow-red transition-all duration-300 group border-0">
                  <div className="relative overflow-hidden">
                    <img
                      src={project.image}
                      alt={project.name}
                      className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-900 mb-2">{project.name}</h3>
                    <p className="text-gray-600 mb-4">{project.description}</p>
                    <div className="flex flex-wrap gap-2 mb-4">
                      {project.tech.map((tech) => (
                        <span
                          key={tech}
                          className="px-3 py-1 bg-primary-100 text-primary-800 text-sm rounded-full"
                        >
                          {tech}
                        </span>
                      ))}
                    </div>
                    <div className="flex space-x-4">
                      <Button size="sm" href={project.liveDemo} className="bg-gradient-to-r from-primary-600 to-primary-700">
                        {t('portfolio.liveDemo')}
                      </Button>
                      <Button variant="outline" size="sm" href={project.github} className="border-primary-600 text-primary-600 hover:bg-primary-600">
                        {t('portfolio.github')}
                      </Button>
                    </div>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>

          <div className="text-center">
            <Link to="/portfolio">
              <Button
                variant="outline"
                size="lg"
                className="border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white transition"
              >
                {t('portfolio.viewAllProjects')}
              </Button>
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};