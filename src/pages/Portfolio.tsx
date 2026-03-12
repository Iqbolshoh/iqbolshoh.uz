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
  const [currentPage, setCurrentPage] = useState(1); // Sahifalash uchun state
  const projectsPerPage = 6; // Bitta sahifada nechta chiqishi

  const categories = [
    { key: 'all', label: t('portfolio.categories.all') },
    { key: 'frontend', label: t('portfolio.categories.frontend') },
    { key: 'backend', label: t('portfolio.categories.backend') },
    { key: 'full-stack', label: t('portfolio.categories.fullstack') },
  ];

  // Kategoriyani o'zgartirganda 1-sahifaga qaytish
  const handleFilterChange = (key: string) => {
    setActiveFilter(key);
    setCurrentPage(1);
  };

  // Loyihalarni filtrlash
  const filteredProjects =
    activeFilter === 'all'
      ? projects
      : projects.filter((project) =>
        project.category?.toLowerCase().includes(activeFilter.toLowerCase())
      );

  // Paginatsiya hisob-kitoblari
  const totalPages = Math.ceil(filteredProjects.length / projectsPerPage);
  const paginatedProjects = filteredProjects.slice(
    (currentPage - 1) * projectsPerPage,
    currentPage * projectsPerPage
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
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl tracking-tight">
              {t('portfolio.title')}
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
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
                  onClick={() => handleFilterChange(category.key)}
                  className={`whitespace-nowrap px-4 py-2 text-sm font-medium transition-all duration-300 rounded-md cursor-pointer ${activeFilter === category.key
                      ? 'bg-white text-primary-600 shadow-sm transform scale-105'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200'
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
      <section className="py-20 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeFilter + currentPage} // Sahifa o'zgarganda animatsiya ishlashi uchun
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              transition={{ duration: 0.3 }}
              className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {paginatedProjects.map((project, index) => (
                <motion.div
                  key={project.id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                >
                  <Card className="overflow-hidden h-full flex flex-col group hover:shadow-xl transition-shadow duration-300 border border-gray-100 bg-white">
                    <div className="relative overflow-hidden">
                      <img
                        src={project.image}
                        alt={project.name[i18n.language as keyof typeof project.name]}
                        className="w-full h-56 object-cover transition-transform duration-700 group-hover:scale-105"
                      />
                      {project.featured && (
                        <div className="absolute top-4 left-4">
                          <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-md">
                            {t('portfolio.featured')}
                          </span>
                        </div>
                      )}
                      {(project.liveDemo || project.github) && (
                        <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center space-x-4 opacity-0 group-hover:opacity-100">
                          {project.liveDemo && (
                            <Button
                              size="sm"
                              href={project.liveDemo}
                              icon={<ExternalLink className="h-4 w-4" />}
                              className="transform hover:scale-105"
                            >
                              {t('portfolio.liveDemo')}
                            </Button>
                          )}

                          {project.github && (
                            <Button
                              variant="outline"
                              size="sm"
                              href={project.github}
                              icon={<Github className="h-4 w-4" />}
                              className="bg-white hover:bg-gray-100 text-gray-900 border-none transform hover:scale-105"
                            >
                              {t('portfolio.github')}
                            </Button>
                          )}
                        </div>
                      )}
                    </div>

                    <div className="p-6 flex flex-col flex-grow">
                      <h3 className="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors duration-300">
                        {project.name[i18n.language as keyof typeof project.name]}
                      </h3>
                      <p className="text-gray-600 mb-6 flex-grow leading-relaxed">
                        {project.description[i18n.language as keyof typeof project.description]}
                      </p>

                      <div className="flex flex-wrap gap-2 mb-6">
                        {project.tech.map((tech) => (
                          <span
                            key={tech}
                            className="px-3 py-1 bg-primary-50 text-primary-700 text-xs font-semibold rounded-full border border-primary-100"
                          >
                            {tech}
                          </span>
                        ))}
                      </div>

                      {(project.liveDemo || project.github) && (
                        <div className="flex space-x-3 mt-auto">
                          {project.liveDemo && (
                            <Button
                              size="sm"
                              href={project.liveDemo}
                              className={!project.github ? "w-full" : "flex-1"}
                            >
                              {t('portfolio.viewProject')}
                            </Button>
                          )}

                          {project.github && (
                            <Button
                              variant="outline"
                              size="sm"
                              href={project.github}
                              className={!project.liveDemo ? "w-full" : ""}
                            >
                              <Github className="h-4 w-4" />
                            </Button>
                          )}
                        </div>
                      )}
                    </div>
                  </Card>
                </motion.div>
              ))}
            </motion.div>
          </AnimatePresence>

          {/* Pagination buttons */}
          {totalPages > 1 && (
            <div className="flex justify-center items-center gap-3 mt-16 flex-wrap">
              {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                <button
                  key={page}
                  onClick={() => setCurrentPage(page)}
                  className={`w-10 h-10 flex items-center justify-center font-medium text-sm rounded-full border transition-all duration-200 cursor-pointer ${page === currentPage
                      ? 'bg-primary-600 text-white border-primary-600 shadow-md transform scale-105'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300'
                    }`}
                >
                  {page}
                </button>
              ))}
            </div>
          )}

          {filteredProjects.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100 mt-8"
            >
              <Filter className="h-16 w-16 text-gray-300 mx-auto mb-4" />
              <p className="text-2xl font-bold text-gray-900">{t('portfolio.noProjects')}</p>
              <p className="text-gray-500 mt-2 text-lg">{t('portfolio.tryDifferent')}</p>
            </motion.div>
          )}
        </div>
      </section>
    </div>
  );
};