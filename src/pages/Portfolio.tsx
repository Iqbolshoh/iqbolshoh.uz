import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ExternalLink, Github, Filter } from "lucide-react";
import { useTranslation } from "react-i18next";
import { Card } from "../components/UI/Card";
import { Button } from "../components/UI/Button";
import { projects } from "../data/content";

export const Portfolio: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [activeFilter, setActiveFilter] = useState("all");
  const [currentPage, setCurrentPage] = useState(1);
  const projectsPerPage = 6;

  const categories = [
    { key: "all", label: t("portfolio.categories.all") },
    { key: "frontend", label: t("portfolio.categories.frontend") },
    { key: "backend", label: t("portfolio.categories.backend") },
    { key: "full-stack", label: t("portfolio.categories.fullstack") },
  ];

  const handleFilterChange = (key: string) => {
    setActiveFilter(key);
    setCurrentPage(1);
  };

  const filteredProjects =
    activeFilter === "all"
      ? projects
      : projects.filter((project) =>
          project.category?.toLowerCase().includes(activeFilter.toLowerCase()),
        );

  const totalPages = Math.ceil(filteredProjects.length / projectsPerPage);
  const paginatedProjects = filteredProjects.slice(
    (currentPage - 1) * projectsPerPage,
    currentPage * projectsPerPage,
  );

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="min-h-[45vh] flex items-center py-16 bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center w-full">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl lg:text-5xl tracking-tight">
              {t("portfolio.title")}
            </h1>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
              {t("portfolio.description")}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Filter Tabs */}
      <section className="py-8 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col items-center">
            <div
              className="inline-flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl overflow-x-auto max-w-full no-scrollbar"
              role="tablist"
              aria-label="Filter projects by category"
            >
              {categories.map((category) => (
                <button
                  key={category.key}
                  role="tab"
                  aria-selected={activeFilter === category.key}
                  onClick={() => handleFilterChange(category.key)}
                  className={`relative whitespace-nowrap px-6 py-2.5 text-sm font-semibold transition-colors duration-300 rounded-lg cursor-pointer focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none ${
                    activeFilter === category.key
                      ? "text-primary-600 dark:text-primary-400"
                      : "text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                  }`}
                >
                  {activeFilter === category.key && (
                    <motion.span
                      layoutId="activeTab"
                      className="absolute inset-0 bg-white dark:bg-gray-700 rounded-lg shadow-sm"
                      transition={{
                        type: "spring",
                        stiffness: 500,
                        damping: 30,
                      }}
                    />
                  )}
                  <span className="relative z-10">{category.label}</span>
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Projects Grid */}
      <section
        className="py-20 bg-gray-50 dark:bg-gray-800"
        aria-live="polite"
        aria-label="Projects list"
      >
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeFilter + currentPage}
              initial="hidden"
              animate="show"
              exit="hidden"
              variants={{
                hidden: { opacity: 0 },
                show: {
                  opacity: 1,
                  transition: {
                    staggerChildren: 0.1,
                  },
                },
              }}
              className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {paginatedProjects.map((project) => (
                <motion.div
                  key={project.id}
                  variants={{
                    hidden: { opacity: 0, y: 20 },
                    show: { opacity: 1, y: 0 },
                  }}
                  transition={{ duration: 0.5, ease: "easeOut" }}
                >
                  <Card className="overflow-hidden h-full flex flex-col group hover:shadow-xl transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                    <div className="relative overflow-hidden">
                      <img
                        src={project.image}
                        alt={
                          project.name[
                            i18n.language as keyof typeof project.name
                          ]
                        }
                        className="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-105"
                      />
                      {project.featured && (
                        <div className="absolute top-4 left-4">
                          <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-md">
                            {t("portfolio.featured")}
                          </span>
                        </div>
                      )}
                      {(project.liveDemo || project.github) && (
                        <div
                          className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center space-x-4 opacity-0 group-hover:opacity-100"
                          aria-hidden="true"
                        >
                          {project.liveDemo && (
                            <Button
                              size="sm"
                              href={project.liveDemo}
                              icon={
                                <ExternalLink
                                  className="h-4 w-4"
                                  aria-hidden="true"
                                />
                              }
                              className="transform hover:scale-105"
                              tabIndex={-1}
                            >
                              {t("portfolio.liveDemo")}
                            </Button>
                          )}
                          {project.github && (
                            <Button
                              variant="outline"
                              size="sm"
                              href={project.github}
                              icon={
                                <Github
                                  className="h-4 w-4"
                                  aria-hidden="true"
                                />
                              }
                              className="bg-white hover:bg-gray-100 text-gray-900 border-none transform hover:scale-105"
                              tabIndex={-1}
                            >
                              {t("portfolio.github")}
                            </Button>
                          )}
                        </div>
                      )}
                    </div>

                    <div className="p-6 flex flex-col flex-grow">
                      <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-300">
                        {
                          project.name[
                            i18n.language as keyof typeof project.name
                          ]
                        }
                      </h3>
                      <p className="text-gray-600 dark:text-gray-400 mb-6 flex-grow leading-relaxed">
                        {
                          project.description[
                            i18n.language as keyof typeof project.description
                          ]
                        }
                      </p>

                      <div
                        className="flex flex-wrap gap-2 mb-6"
                        aria-label="Technologies"
                      >
                        {project.tech.map((tech) => (
                          <span
                            key={tech}
                            className="px-3 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 text-xs font-semibold rounded-full border border-primary-100 dark:border-primary-800"
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
                              {t("portfolio.viewProject")}
                            </Button>
                          )}
                          {project.github && (
                            <Button
                              variant="outline"
                              size="sm"
                              href={project.github}
                              className={!project.liveDemo ? "w-full" : ""}
                              aria-label={`${project.name[i18n.language as keyof typeof project.name]} on GitHub`}
                            >
                              <Github className="h-4 w-4" aria-hidden="true" />
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

          {/* Pagination */}
          {totalPages > 1 && (
            <nav
              className="flex justify-center items-center gap-3 mt-12 flex-wrap"
              aria-label="Pagination"
            >
              {Array.from({ length: totalPages }, (_, i) => i + 1).map(
                (page) => (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    aria-label={`Page ${page}`}
                    aria-current={page === currentPage ? "page" : undefined}
                    className={`w-10 h-10 flex items-center justify-center font-medium text-sm rounded-full border transition-all duration-200 cursor-pointer focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none ${
                      page === currentPage
                        ? "bg-primary-600 text-white border-primary-600 shadow-md transform scale-105"
                        : "bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300"
                    }`}
                  >
                    {page}
                  </button>
                ),
              )}
            </nav>
          )}

          {filteredProjects.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-16 bg-white dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-600 mt-8"
              role="status"
            >
              <Filter
                className="h-16 w-16 text-gray-300 dark:text-gray-500 mx-auto mb-4"
                aria-hidden="true"
              />
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {t("portfolio.noProjects")}
              </p>
              <p className="text-gray-500 dark:text-gray-400 mt-2 text-lg">
                {t("portfolio.tryDifferent")}
              </p>
            </motion.div>
          )}
        </div>
      </section>
    </div>
  );
};
