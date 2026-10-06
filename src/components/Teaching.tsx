import React from "react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { motion } from "framer-motion";
import { ArrowRight, Code2, GraduationCap, Layout, MapPin, Server, Users } from "lucide-react";
import { Card } from "./UI/Card";
import { usePath } from "../hooks/usePath";

/** The courses taught at IT Markaz; their text lives under `teaching.courses.*`. */
const COURSES = [
  { key: "cpp", icon: Code2 },
  { key: "frontend", icon: Layout },
  { key: "backend", icon: Server },
] as const;

/** Teaching at IT Markaz, Samarkand — shown on the Home and About pages. */
export const Teaching: React.FC = () => {
  const { t } = useTranslation();
  const toPath = usePath();

  const facts = [
    { icon: Users, value: t("teaching.facts.studentsValue"), label: t("teaching.facts.students") },
    { icon: GraduationCap, value: t("teaching.facts.coursesValue"), label: t("teaching.facts.courses") },
    { icon: MapPin, value: t("teaching.facts.placeValue"), label: t("teaching.facts.place") },
  ];

  return (
    <section className="py-20 bg-gray-50 dark:bg-gray-800" aria-labelledby="teaching-title">
      <div className="mx-auto max-w-7xl px-6 lg:px-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
          className="text-center max-w-3xl mx-auto mb-12"
        >
          <span className="inline-flex items-center gap-2 rounded-full bg-primary-50 dark:bg-primary-900/30 px-4 py-1.5 text-sm font-semibold text-primary-700 dark:text-primary-300">
            <GraduationCap className="h-4 w-4" aria-hidden="true" />
            {t("teaching.badge")}
          </span>
          <h2 id="teaching-title" className="mt-4 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
            {t("teaching.title")}
          </h2>
          <p className="mt-4 text-base text-gray-600 dark:text-gray-400 leading-relaxed">
            {t("teaching.description")}
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
          {facts.map((fact) => (
            <Card key={fact.label} className="p-5 text-center border-0 cursor-default">
              <fact.icon className="h-6 w-6 text-primary-600 mx-auto mb-2" aria-hidden="true" />
              <div className="text-2xl font-bold bg-gradient-to-r from-primary-700 to-primary-600 bg-clip-text text-transparent">
                {fact.value}
              </div>
              <div className="mt-1 text-sm text-gray-600 dark:text-gray-400">{fact.label}</div>
            </Card>
          ))}
        </div>

        <div className="grid md:grid-cols-3 gap-6">
          {COURSES.map(({ key, icon: Icon }, index) => (
            <motion.div
              key={key}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              viewport={{ once: true }}
            >
              <Card className="p-6 h-full border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-4">
                  <Icon className="h-6 w-6" aria-hidden="true" />
                </div>
                <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                  {t(`teaching.courses.${key}.title`)}
                </h3>
                <p className="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                  {t(`teaching.courses.${key}.description`)}
                </p>
                <p className="mt-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                  {t(`teaching.courses.${key}.topics`)}
                </p>
              </Card>
            </motion.div>
          ))}
        </div>

        <div className="mt-10 text-center">
          <Link
            to={toPath("/contact")}
            className="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-3 font-medium text-white shadow-md hover:shadow-lg transition-all focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
          >
            {t("teaching.cta")}
            <ArrowRight className="h-4 w-4" aria-hidden="true" />
          </Link>
        </div>
      </div>
    </section>
  );
};
