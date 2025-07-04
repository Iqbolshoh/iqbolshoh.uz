import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ExternalLink, Github, Filter } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Card } from '../components/UI/Card';
import { Button } from '../components/UI/Button';
import { projects } from '../data/content';

export const Portfolio: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [activeFilter, setActiveFilter] = useState('all');

  const categories = [
    { key: 'all', label: t('portfolio.categories.all') },
    { key: 'frontend', label: t('portfolio.categories.frontend') },
    { key: 'backend', label: t('portfolio.categories.backend') },
    { key: 'full-stack', label: t('portfolio.categories.fullstack') },
  ];

  const filteredProjects =
    activeFilter === 'all'
      ? projects
      : projects.filter((project) =>
        project.category?.toLowerCase().includes(activeFilter.toLowerCase())
      );

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
              {t('portfolio.title')}
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
              {t('portfolio.description')}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Filter Tabs */}
      <section className="py-8 bg-white border-b">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="overflow-x-auto">
            <div className="flex items-center justify-center space-x-1 bg-gray-100 rounded-lg p-1 w-full sm:w-fit mx-auto">
              {categories.map((category) => (
                <button
                  key={category.key}
                  onClick={() => setActiveFilter(category.key)}
                  className={`whitespace-nowrap px-3 py-2 text-sm font-medium transition-all ${activeFilter === category.key
                    ? 'bg-white text-primary-600 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'
                    }`}
                >
                  {category.label}
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Projects Grid */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeFilter}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              transition={{ duration: 0.3 }}
              className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {filteredProjects.map((project, index) => (
                <motion.div
                  key={project.id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                >
                  <Card className="overflow-hidden h-full">
                    <div className="relative group">
                      <img
                        src={project.image}
                        alt={project.name[i18n.language as keyof typeof project.name]}
                        className="w-full h-48 object-cover transition-transform group-hover:scale-105"
                      />
                      {project.featured && (
                        <div className="absolute top-4 left-4">
                          <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {t('portfolio.featured')}
                          </span>
                        </div>
                      )}
                      <div className="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-4">
                        <Button
                          size="sm"
                          href={project.liveDemo}
                          icon={<ExternalLink className="h-4 w-4" />}
                        >
                          {t('portfolio.liveDemo')}
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          href={project.github}
                          icon={<Github className="h-4 w-4" />}
                        >
                          {t('portfolio.github')}
                        </Button>
                      </div>
                    </div>

                    <div className="p-6 flex flex-col flex-grow">
                      <h3 className="text-xl font-bold text-gray-900 mb-2">
                        {project.name[i18n.language as keyof typeof project.name]}
                      </h3>
                      <p className="text-gray-600 mb-4 flex-grow">
                        {project.description[i18n.language as keyof typeof project.description]}
                      </p>

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

                      <div className="flex space-x-3">
                        <Button size="sm" href={project.liveDemo} className="flex-1">
                          {t('portfolio.viewProject')}
                        </Button>
                        <Button variant="outline" size="sm" href={project.github}>
                          <Github className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </Card>
                </motion.div>
              ))}
            </motion.div>
          </AnimatePresence>

          {filteredProjects.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-12"
            >
              <Filter className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-xl text-gray-600">{t('portfolio.noProjects')}</p>
              <p className="text-gray-500 mt-2">{t('portfolio.tryDifferent')}</p>
            </motion.div>
          )}
        </div>
      </section>
    </div>
  );
};