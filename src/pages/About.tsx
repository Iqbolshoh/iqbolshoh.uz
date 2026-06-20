import React from "react";
import { motion } from "framer-motion";
import { useTranslation } from "react-i18next";
import { GraduationCap, MapPin, Calendar, Focus } from "lucide-react";
import { Card } from "../components/UI/Card";
import {
  personalInfo,
  techStack,
  journey,
  highlights,
  beyond,
} from "../data/content";

export const About: React.FC = () => {
  const { t, i18n } = useTranslation();

  const getLocalizedContent = (
    content: Record<string, string>,
    language: string,
  ) => {
    return content[language] || content.en || "";
  };

  const containerVariants = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0 },
  };

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="min-h-[70vh] flex items-center py-20 bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 w-full">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <motion.div
              initial="hidden"
              animate="show"
              variants={containerVariants}
            >
              <motion.h1
                variants={itemVariants}
                className="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl lg:text-5xl"
              >
                {t("about.title")}
              </motion.h1>
              <motion.p
                variants={itemVariants}
                className="mt-4 text-lg text-gray-600 dark:text-gray-300 leading-relaxed"
              >
                {t("about.description")}
              </motion.p>
              <motion.p
                variants={itemVariants}
                className="mt-3 text-base text-gray-600 dark:text-gray-400"
              >
                {t("about.subDescription")}
              </motion.p>

              <motion.div
                variants={itemVariants}
                className="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4"
              >
                <div className="flex items-center space-x-2 text-gray-600 dark:text-gray-400 text-sm">
                  <MapPin
                    className="h-4 w-4 text-primary-600 flex-shrink-0"
                    aria-hidden="true"
                  />
                  <span>
                    {
                      personalInfo.location[
                        i18n.language as keyof typeof personalInfo.location
                      ]
                    }
                  </span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600 dark:text-gray-400 text-sm">
                  <GraduationCap
                    className="h-4 w-4 text-primary-600 flex-shrink-0"
                    aria-hidden="true"
                  />
                  <span>{t("about.locationInfo")}</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600 dark:text-gray-400 text-sm">
                  <Calendar
                    className="h-4 w-4 text-primary-600 flex-shrink-0"
                    aria-hidden="true"
                  />
                  <span>{t("about.experienceInfo")}</span>
                </div>
              </motion.div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.6, delay: 0.3 }}
              className="mt-10 lg:mt-0"
            >
              <div className="relative pb-6 pr-4 sm:pr-6">
                <img
                  src="/images/logos/iqbolshoh-2.png"
                  alt={
                    personalInfo.name[
                      i18n.language as keyof typeof personalInfo.name
                    ]
                  }
                  className="rounded-2xl shadow-xl w-full max-w-md mx-auto"
                />
                <div className="absolute bottom-0 right-0 bg-white dark:bg-gray-800 rounded-xl p-3 shadow-lg border border-gray-100 dark:border-gray-700">
                  <div className="flex items-center space-x-2">
                    <Focus
                      className="h-4 w-4 text-primary-500"
                      aria-hidden="true"
                    />
                    <span className="text-xs font-medium text-gray-900 dark:text-white">
                      {t("about.codingPurpose")}
                    </span>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Highlights */}
      <section className="py-16 bg-white dark:bg-gray-900">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial="hidden"
            whileInView="show"
            viewport={{ once: true }}
            variants={containerVariants}
            className="grid md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            {highlights.map((item, index) => {
              const Icon = item.icon;
              const lang = i18n.language as keyof typeof item.text;

              return (
                <motion.div key={index} variants={itemVariants}>
                  <Card className="p-5 text-center hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300 hover:shadow-glow-red group border-0">
                    <Icon
                      className="h-8 w-8 text-primary-600 mx-auto mb-2 group-hover:scale-110 transition-transform duration-300"
                      aria-hidden="true"
                    />
                    <p className="text-sm font-medium text-gray-900 dark:text-white">
                      {item.text[lang]}
                    </p>
                  </Card>
                </motion.div>
              );
            })}
          </motion.div>
        </div>
      </section>

      {/* Journey Timeline */}
      <section className="py-20 bg-gray-50 dark:bg-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
              {t("about.journey")}
            </h2>
            <p className="mt-4 text-base text-gray-600 dark:text-gray-400">
              {t("about.journeyDescription")}
            </p>
          </motion.div>

          <div className="relative" aria-label="Career timeline">
            <div
              className="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-primary-200 dark:bg-primary-900"
              aria-hidden="true"
            ></div>
            <motion.div
              initial="hidden"
              whileInView="show"
              viewport={{ once: true }}
              variants={containerVariants}
              className="space-y-10"
            >
              {journey.map((item, index) => (
                <motion.div
                  key={index}
                  variants={{
                    hidden: { opacity: 0, x: index % 2 === 0 ? -20 : 20 },
                    show: { opacity: 1, x: 0 },
                  }}
                  className={`relative flex items-center ${index % 2 === 0 ? "flex-row" : "flex-row-reverse"}`}
                >
                  <div className="flex-1">
                    <Card
                      className={`p-5 ${index % 2 === 0 ? "mr-6" : "ml-6"}`}
                    >
                      <div className="flex items-center space-x-2 mb-2">
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                          {item.year}
                        </span>
                      </div>
                      <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        {getLocalizedContent(item.title, i18n.language)}
                      </h3>
                      <p className="text-sm text-gray-600 dark:text-gray-400">
                        {getLocalizedContent(item.description, i18n.language)}
                      </p>
                    </Card>
                  </div>
                </motion.div>
              ))}
            </motion.div>
          </div>
        </div>
      </section>

      {/* Skills & Interests */}
      <section className="py-20 bg-white dark:bg-gray-900">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16">
            {/* Technical Skills */}
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
            >
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                {t("about.technicalSkills")}
              </h2>
              <div className="space-y-4">
                {techStack.map((tech, index) => {
                  const Icon = tech.icon;
                  return (
                    <div
                      key={tech.name}
                      className="flex items-center space-x-4"
                    >
                      <Icon
                        className="h-6 w-6 text-primary-600 flex-shrink-0"
                        aria-hidden="true"
                      />
                      <div className="flex-1">
                        <div className="flex justify-between mb-1 text-sm">
                          <span className="font-medium text-gray-900 dark:text-white">
                            {tech.name}
                          </span>
                          <span className="text-xs text-gray-600 dark:text-gray-400">
                            {tech.level}%
                          </span>
                        </div>
                        <div
                          className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5"
                          role="progressbar"
                          aria-valuenow={tech.level}
                          aria-valuemin={0}
                          aria-valuemax={100}
                          aria-label={`${tech.name} proficiency`}
                        >
                          <motion.div
                            initial={{ width: 0 }}
                            whileInView={{ width: `${tech.level}%` }}
                            transition={{ duration: 1, delay: index * 0.1 }}
                            viewport={{ once: true }}
                            className="bg-primary-600 h-1.5 rounded-full"
                          ></motion.div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </motion.div>

            {/* Beyond Coding */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="mt-10 lg:mt-0"
            >
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                {t("about.beyondCoding")}
              </h2>
              <div className="space-y-6">
                {beyond.map((item, index) => {
                  const lang = i18n.language as keyof typeof item.title;
                  const Icon = item.icon;

                  return (
                    <div key={index} className="flex items-start space-x-5">
                      <Icon
                        className="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5"
                        aria-hidden="true"
                      />
                      <div>
                        <h3 className="font-semibold text-gray-900 dark:text-white text-base">
                          {item.title[lang]}
                        </h3>
                        <p className="text-gray-600 dark:text-gray-400 text-sm">
                          {item.description[lang]}
                        </p>
                      </div>
                    </div>
                  );
                })}
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};
