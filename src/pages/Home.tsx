import React from "react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { motion } from "framer-motion";
import { ArrowRight, Download } from "lucide-react";
import { Button } from "../components/UI/Button";
import { Card } from "../components/UI/Card";
import { personalInfo, techStack, stats, projects } from "../data/content";
import { usePath } from "../hooks/usePath";

export const Home: React.FC = () => {
  const { t, i18n } = useTranslation();
  const toPath = usePath();

  return (
    <div className="pt-16 relative">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 min-h-[calc(100vh-4rem)] flex items-center py-16">
        <div className="absolute inset-0 bg-gradient-to-r from-primary-500/5 to-transparent dark:from-primary-500/10"></div>

        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            {/* TEXT SECTION */}
            <motion.div
              initial="hidden"
              animate="show"
              variants={{
                hidden: { opacity: 0 },
                show: {
                  opacity: 1,
                  transition: { staggerChildren: 0.1 },
                },
              }}
              className="lg:col-span-7 text-center lg:text-left"
            >
              <motion.h1
                variants={{
                  hidden: { opacity: 0, y: 20 },
                  show: { opacity: 1, y: 0 },
                }}
                className="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight"
              >
                {t("home.greeting")}{" "}
                <span className="bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">
                  {t("home.name")}
                </span>
              </motion.h1>

              <motion.p
                variants={{
                  hidden: { opacity: 0, y: 20 },
                  show: { opacity: 1, y: 0 },
                }}
                className="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto lg:mx-0"
              >
                {t("home.description")}
              </motion.p>

              <motion.div
                variants={{
                  hidden: { opacity: 0, y: 20 },
                  show: { opacity: 1, y: 0 },
                }}
                className="mt-8 flex flex-col sm:flex-row sm:justify-center lg:justify-start gap-4"
              >
                <Link to={toPath("/portfolio")}>
                  <Button
                    size="lg"
                    icon={<ArrowRight className="h-5 w-5" aria-hidden="true" />}
                    className="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-glow-red w-full sm:w-auto"
                  >
                    {t("home.viewPortfolio")}
                  </Button>
                </Link>
                <Button
                  variant="outline"
                  size="lg"
                  icon={<Download className="h-5 w-5" aria-hidden="true" />}
                  href="/uploads/iqbolshoh-cv.pdf"
                  download
                  className="border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white w-full sm:w-auto"
                  aria-label="Download CV (PDF)"
                >
                  {t("home.downloadCV")}
                </Button>
              </motion.div>
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
                    src="/images/logos/iqbolshoh-1.png"
                    alt={
                      personalInfo.name[
                        i18n.language as keyof typeof personalInfo.name
                      ]
                    }
                    className="h-full w-full rounded-full object-cover"
                  />
                </div>

                {/* Floating tech icons */}
                <div
                  className="absolute -bottom-4 -right-4 bg-white dark:bg-gray-800 rounded-full p-4 shadow-lg border-2 border-primary-200 dark:border-primary-800"
                  aria-hidden="true"
                >
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
                <div
                  className="absolute -top-6 -left-6 animate-bounce-slow"
                  aria-hidden="true"
                >
                  <img
                    src="/images/logos/iqbolshoh_dev.svg"
                    alt=""
                    className="h-8 w-8 opacity-20 dark:opacity-30"
                  />
                </div>
                <div
                  className="absolute -bottom-8 -left-8 animate-bounce-slow"
                  style={{ animationDelay: "1s" }}
                  aria-hidden="true"
                >
                  <img
                    src="/images/logos/iqbolshoh_dev.svg"
                    alt=""
                    className="h-6 w-6 opacity-15 dark:opacity-20"
                  />
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-20 bg-white dark:bg-gray-900">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-10"
          >
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
              {t("home.techStack")}
            </h2>
            <p className="mt-4 text-base text-gray-600 dark:text-gray-400">
              {t("home.technologiesILove")}
            </p>
          </motion.div>

          <motion.div
            initial="hidden"
            whileInView="show"
            viewport={{ once: true }}
            variants={{
              hidden: { opacity: 0 },
              show: {
                opacity: 1,
                transition: { staggerChildren: 0.05 },
              },
            }}
            className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6"
            role="list"
            aria-label="Technology stack"
          >
            {techStack.map((tech) => {
              const Icon = tech.icon;
              return (
                <motion.div
                  key={tech.name}
                  role="listitem"
                  variants={{
                    hidden: { opacity: 0, y: 10 },
                    show: { opacity: 1, y: 0 },
                  }}
                  transition={{ duration: 0.4 }}
                >
                  <Card className="p-4 text-center hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300 hover:shadow-glow-red group border-0">
                    <Icon
                      className="h-8 w-8 text-primary-600 mx-auto mb-2 group-hover:scale-110 transition-transform duration-300"
                      aria-hidden="true"
                    />
                    <h3 className="text-sm font-semibold text-gray-900 dark:text-white">
                      {tech.name}
                    </h3>
                  </Card>
                </motion.div>
              );
            })}
          </motion.div>
        </div>
      </section>

      {/* Stats */}
      <section className="py-20 bg-gray-50 dark:bg-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-10"
          >
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
              {t("home.journeyTitle")}
            </h2>
            <p className="mt-4 text-base text-gray-600 dark:text-gray-400">
              {t("home.journeyDescription")}
            </p>
          </motion.div>

          <motion.div
            initial="hidden"
            whileInView="show"
            viewport={{ once: true }}
            variants={{
              hidden: { opacity: 0 },
              show: {
                opacity: 1,
                transition: { staggerChildren: 0.1 },
              },
            }}
            className="grid grid-cols-2 lg:grid-cols-4 gap-8"
          >
            {stats.map((stat, index) => (
              <motion.div
                key={index}
                variants={{
                  hidden: { opacity: 0, scale: 0.9 },
                  show: { opacity: 1, scale: 1 },
                }}
                transition={{ duration: 0.5 }}
              >
                <Card className="p-4 text-center hover:shadow-glow-red transition-all duration-300 group border-0 cursor-default">
                  <stat.icon
                    className="h-6 w-6 text-primary-600 mx-auto mb-2 group-hover:scale-110 transition-transform duration-300"
                    aria-hidden="true"
                  />
                  <div className="text-2xl font-bold text-gray-900 dark:text-white mb-1 bg-gradient-to-r from-primary-700 to-primary-600 bg-clip-text text-transparent">
                    {stat.value}
                  </div>
                  <div className="text-xs text-gray-600 dark:text-gray-400">
                    {stat.label[i18n.language as keyof typeof stat.label]}
                  </div>
                </Card>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* Featured Projects */}
      <section className="py-24 bg-white dark:bg-gray-900">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
              {t("home.featuredProjects")}
            </h2>
            <p className="mt-4 text-base text-gray-600 dark:text-gray-400">
              {t("home.someRecentWork")}
            </p>
          </motion.div>

          <div className="grid md:grid-cols-2 gap-8 mb-12">
            {projects
              .filter((p) => p.featured)
              .map((project, index) => (
                <motion.div
                  key={project.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.2 }}
                  viewport={{ once: true }}
                >
                  <Card className="overflow-hidden hover:shadow-glow-red transition-all duration-300 group border-0">
                    <div className="relative overflow-hidden aspect-video">
                      <img
                        src={project.image}
                        alt={
                          project.name[
                            i18n.language as keyof typeof project.name
                          ]
                        }
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                      <div className="absolute top-4 left-4">
                        <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                          {t("portfolio.featured")}
                        </span>
                      </div>
                      <div
                        className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        aria-hidden="true"
                      ></div>
                    </div>

                    <div className="p-6">
                      <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {
                          project.name[
                            i18n.language as keyof typeof project.name
                          ]
                        }
                      </h3>
                      <p className="text-gray-600 dark:text-gray-400 mb-4">
                        {
                          project.description[
                            i18n.language as keyof typeof project.description
                          ]
                        }
                      </p>
                      <div
                        className="flex flex-wrap gap-2 mb-4"
                        aria-label="Technologies used"
                      >
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
                        {project.liveDemo && (
                          <Button
                            size="sm"
                            href={project.liveDemo}
                            className="bg-gradient-to-r from-primary-600 to-primary-700"
                          >
                            {t("portfolio.liveDemo")}
                          </Button>
                        )}
                        {project.github && (
                          <Button
                            variant="outline"
                            size="sm"
                            href={project.github}
                            className="border-primary-600 text-primary-600 hover:bg-primary-600"
                          >
                            {t("portfolio.github")}
                          </Button>
                        )}
                      </div>
                    </div>
                  </Card>
                </motion.div>
              ))}
          </div>

          <div className="text-center">
            <Link to={toPath("/portfolio")}>
              <Button
                variant="outline"
                size="lg"
                className="border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white transition"
              >
                {t("portfolio.viewAllProjects")}
              </Button>
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};
