import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { motion } from 'framer-motion';
import { ArrowRight, Download, Star, Users, Coffee, Award } from 'lucide-react';
import { Button } from '../components/UI/Button';
import { Card } from '../components/UI/Card';
import { FloatingIcons } from '../components/FloatingIcons';
import { personalInfo, techStack, projects } from '../data/content';

export const Home: React.FC = () => {
  const { t } = useTranslation();
  
  const stats = [
    { label: t('common.yearsExperience'), value: '3+', icon: Star },
    { label: t('common.projectsCompleted'), value: '50+', icon: Award },
    { label: t('common.happyClients'), value: '25+', icon: Users },
    { label: t('common.cupsOfCoffee'), value: '1000+', icon: Coffee },
  ];

  return (
    <div className="pt-16 relative">
      <FloatingIcons />
      
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-dark-bg-primary dark:via-dark-bg-secondary dark:to-dark-bg-tertiary py-20 sm:py-32">
        <div className="absolute inset-0 bg-light-pattern dark:bg-dark-pattern opacity-30 dark:opacity-10"></div>
        <div className="absolute inset-0 bg-gradient-to-r from-primary-500/5 to-accent-500/5 dark:from-primary-500/10 dark:to-accent-500/10"></div>
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <div className="mx-auto max-w-2xl lg:mx-0 lg:grid lg:max-w-none lg:grid-cols-2 lg:gap-x-16 lg:gap-y-6 xl:grid-cols-12 xl:gap-x-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="col-span-2 max-w-xl lg:col-span-6 xl:col-span-7"
            >
              <div className="flex items-center space-x-2 mb-4">
                <div className="h-2 w-2 bg-accent-500 rounded-full animate-pulse"></div>
                <span className="text-accent-600 dark:text-accent-400 font-medium">{t('hero.availableForHire')}</span>
              </div>
              
              <h1 className="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl lg:text-5xl xl:text-6xl">
                {t('hero.greeting')}{' '}
                <span className="bg-gradient-to-r from-primary-600 to-accent-600 dark:from-primary-400 dark:to-accent-400 bg-clip-text text-transparent">
                  {t('hero.name')}
                </span>{' '}
                <span className="inline-block animate-float">👋</span>
              </h1>
              
              <p className="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                {t('hero.description')}
              </p>
              
              <div className="mt-8 flex flex-wrap gap-4">
                <Button size="lg" icon={<ArrowRight className="h-5 w-5" />} className="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-glow-red">
                  <Link to="/portfolio">{t('hero.viewPortfolio')}</Link>
                </Button>
                <Button 
                  variant="outline" 
                  size="lg" 
                  icon={<Download className="h-5 w-5" />}
                  href="/resume.pdf"
                  className="border-primary-600 dark:border-primary-400 text-primary-600 dark:text-primary-400 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-500"
                >
                  {t('hero.downloadCV')}
                </Button>
              </div>

              <div className="mt-12">
                <p className="text-sm font-semibold text-gray-900 dark:text-gray-200 mb-4">{t('hero.trustedBy')}</p>
                <div className="flex items-center space-x-8 grayscale opacity-70">
                  <div className="h-8 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                  <div className="h-8 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                  <div className="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
              </div>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="col-span-2 lg:col-span-6 xl:col-span-5"
            >
              <div className="relative">
                <div className="aspect-square rounded-full bg-gradient-to-tr from-primary-400 to-accent-400 dark:from-primary-500 dark:to-accent-500 p-1 shadow-glow-red-lg">
                  <img
                    src={personalInfo.avatar}
                    alt={personalInfo.name}
                    className="h-full w-full rounded-full object-cover"
                  />
                </div>
                <div className="absolute -bottom-4 -right-4 bg-white dark:bg-dark-bg-secondary rounded-full p-4 shadow-lg dark:shadow-dark-lg border-2 border-primary-200 dark:border-primary-800">
                  <div className="flex space-x-1">
                    {techStack.slice(0, 3).map((tech, index) => (
                      <div key={tech.name} className="text-2xl animate-float" style={{ animationDelay: `${index * 0.5}s` }}>
                        {tech.icon}
                      </div>
                    ))}
                  </div>
                </div>
                
                {/* Floating logo elements */}
                <div className="absolute -top-6 -left-6 animate-bounce-slow">
                  <img src="/iqbolshoh.svg" alt="Logo" className="h-8 w-8 opacity-20 dark:brightness-110" />
                </div>
                <div className="absolute -bottom-8 -left-8 animate-bounce-slow" style={{ animationDelay: '1s' }}>
                  <img src="/iqbolshoh.svg" alt="Logo" className="h-6 w-6 opacity-15 dark:brightness-110" />
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-16 bg-white dark:bg-dark-bg-secondary">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-4xl">{t('common.techStack')}</h2>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">{t('common.technologiesILove')}</p>
          </motion.div>
          
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            {techStack.map((tech, index) => (
              <motion.div
                key={tech.name}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6 text-center hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300 hover:shadow-glow-red group border-0 dark:bg-dark-bg-tertiary dark:shadow-dark-lg">
                  <div className="text-4xl mb-3 group-hover:scale-110 transition-transform duration-300">{tech.icon}</div>
                  <h3 className="font-semibold text-gray-900 dark:text-gray-200">{tech.name}</h3>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Stats */}
      <section className="py-16 bg-gray-50 dark:bg-dark-bg-primary">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.label}
                initial={{ opacity: 0, scale: 0.8 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6 text-center hover:shadow-glow-red transition-all duration-300 group border-0 dark:bg-dark-bg-secondary dark:shadow-dark-lg">
                  <stat.icon className="h-8 w-8 text-primary-600 dark:text-primary-400 mx-auto mb-3 group-hover:scale-110 transition-transform duration-300" />
                  <div className="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2 bg-gradient-to-r from-primary-600 to-accent-600 dark:from-primary-400 dark:to-accent-400 bg-clip-text text-transparent">{stat.value}</div>
                  <div className="text-sm text-gray-600 dark:text-gray-300">{stat.label}</div>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Projects */}
      <section className="py-20 bg-white dark:bg-dark-bg-secondary">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-4xl">{t('common.featuredProjects')}</h2>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">{t('common.someRecentWork')}</p>
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
                <Card className="overflow-hidden hover:shadow-glow-red transition-all duration-300 group border-0 dark:bg-dark-bg-tertiary dark:shadow-dark-lg">
                  <div className="relative overflow-hidden">
                    <img
                      src={project.image}
                      alt={project.name}
                      className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{project.name}</h3>
                    <p className="text-gray-600 dark:text-gray-300 mb-4">{project.description}</p>
                    <div className="flex flex-wrap gap-2 mb-4">
                      {project.tech.map((tech) => (
                        <span
                          key={tech}
                          className="px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm rounded-full"
                        >
                          {tech}
                        </span>
                      ))}
                    </div>
                    <div className="flex space-x-4">
                      <Button size="sm" href={project.liveDemo} className="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-500 dark:to-primary-600">
                        {t('common.liveDemo')}
                      </Button>
                      <Button variant="outline" size="sm" href={project.github} className="border-primary-600 dark:border-primary-400 text-primary-600 dark:text-primary-400 hover:bg-primary-600 dark:hover:bg-primary-500">
                        {t('common.github')}
                      </Button>
                    </div>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
          
          <div className="text-center">
            <Button variant="outline" size="lg" className="border-primary-600 dark:border-primary-400 text-primary-600 dark:text-primary-400 hover:bg-primary-600 dark:hover:bg-primary-500">
              <Link to="/portfolio">{t('common.viewAllProjects')}</Link>
            </Button>
          </div>
        </div>
      </section>
    </div>
  );
};