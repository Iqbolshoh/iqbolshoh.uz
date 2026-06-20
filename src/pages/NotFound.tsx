import React from "react";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { useTranslation } from "react-i18next";
import { Home, ArrowRight, Search } from "lucide-react";
import { Button } from "../components/UI/Button";
import { usePath } from "../hooks/usePath";

export const NotFound: React.FC = () => {
  const { t } = useTranslation();
  const toPath = usePath();

  return (
    <div className="min-h-[calc(100vh-4rem)] flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      {/* Decorative background blobs */}
      <div className="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div className="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-200/30 dark:bg-primary-900/20 rounded-full blur-3xl" />
        <div className="absolute bottom-1/4 right-1/4 w-80 h-80 bg-primary-300/20 dark:bg-primary-800/20 rounded-full blur-3xl" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gray-200/20 dark:bg-gray-700/10 rounded-full blur-3xl" />
      </div>

      <div className="relative z-10 text-center px-6 max-w-2xl mx-auto">
        {/* 404 number */}
        <motion.div
          initial={{ opacity: 0, scale: 0.5 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.6, type: "spring", stiffness: 200 }}
          className="relative mb-6"
        >
          <span className="text-[10rem] sm:text-[14rem] font-black leading-none select-none bg-gradient-to-br from-primary-600 via-primary-500 to-primary-400 bg-clip-text text-transparent">
            404
          </span>
          {/* Floating search icon */}
          <motion.div
            animate={{ y: [0, -14, 0], rotate: [-8, 8, -8] }}
            transition={{ duration: 3.5, repeat: Infinity, ease: "easeInOut" }}
            className="absolute -top-4 -right-4 sm:-top-6 sm:-right-6 bg-white dark:bg-gray-800 rounded-2xl p-3 shadow-xl border border-gray-100 dark:border-gray-700"
            aria-hidden="true"
          >
            <Search className="h-7 w-7 sm:h-9 sm:w-9 text-primary-500" />
          </motion.div>
        </motion.div>

        {/* Error code badge */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.15 }}
          className="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 rounded-full text-sm font-semibold mb-6"
        >
          <span className="w-2 h-2 rounded-full bg-primary-500 animate-pulse" />
          {t("seo.notFound.errorCode")}
        </motion.div>

        {/* Title */}
        <motion.h1
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.25 }}
          className="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4"
        >
          {t("seo.notFound.heading")}
        </motion.h1>

        {/* Description */}
        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.35 }}
          className="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-md mx-auto"
        >
          {t("seo.notFound.description")}
        </motion.p>

        {/* Action buttons */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.45 }}
          className="flex flex-col sm:flex-row items-center justify-center gap-4"
        >
          <Link to={toPath("/")}>
            <Button
              size="lg"
              icon={<Home className="h-5 w-5" aria-hidden="true" />}
              className="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-glow-red w-full sm:w-auto"
            >
              {t("seo.notFound.goHome")}
            </Button>
          </Link>
          <Link to={toPath("/portfolio")}>
            <Button
              variant="outline"
              size="lg"
              icon={<ArrowRight className="h-5 w-5" aria-hidden="true" />}
              className="border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white w-full sm:w-auto"
            >
              {t("seo.notFound.viewPortfolio")}
            </Button>
          </Link>
        </motion.div>

        {/* Decorative dots grid */}
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.8, delay: 0.6 }}
          className="mt-16 flex justify-center gap-3"
          aria-hidden="true"
        >
          {[...Array(7)].map((_, i) => (
            <motion.div
              key={i}
              animate={{ scale: [1, 1.5, 1], opacity: [0.3, 1, 0.3] }}
              transition={{
                duration: 1.5,
                repeat: Infinity,
                delay: i * 0.15,
                ease: "easeInOut",
              }}
              className="w-2 h-2 rounded-full bg-primary-400 dark:bg-primary-600"
            />
          ))}
        </motion.div>
      </div>
    </div>
  );
};
