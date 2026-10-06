import React, { useEffect, useMemo, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Award, Calendar, ExternalLink, X } from "lucide-react";
import { useTranslation } from "react-i18next";
import { Card } from "../components/UI/Card";
import { useContent } from "../context/ContentContext";
import type { Certificate, Translated } from "../context/ContentContext";

/** "2025-06-14" → "14.06.2025"; the same in every language the site speaks. */
const formatDate = (value: string | null) => {
  if (!value) return null;
  const [year, month, day] = value.slice(0, 10).split("-");
  return year && month && day ? `${day}.${month}.${year}` : value;
};

export const Certificates: React.FC = () => {
  const { certificates } = useContent();
  const { t, i18n } = useTranslation();
  const [activeType, setActiveType] = useState("all");
  const [opened, setOpened] = useState<Certificate | null>(null);

  const tr = (text: Translated | null | undefined) =>
    text ? text[i18n.language] || text.uz || text.en || "" : "";

  // Only the types that actually have entries get a tab.
  const types = useMemo(
    () => Array.from(new Set(certificates.map((item) => item.type))),
    [certificates],
  );

  const visible =
    activeType === "all"
      ? certificates
      : certificates.filter((item) => item.type === activeType);

  useEffect(() => {
    if (!opened) return;
    const onKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") setOpened(null);
    };
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    return () => {
      document.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
    };
  }, [opened]);

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
              {t("certificates.title")}
            </h1>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
              {t("certificates.description")}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Filter Tabs */}
      {types.length > 1 && (
        <section className="py-8 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div className="flex flex-col items-center">
              <div
                className="inline-flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl overflow-x-auto max-w-full no-scrollbar"
                role="tablist"
                aria-label="Filter certificates by type"
              >
                {["all", ...types].map((type) => (
                  <button
                    key={type}
                    role="tab"
                    aria-selected={activeType === type}
                    onClick={() => setActiveType(type)}
                    className={`relative whitespace-nowrap px-6 py-2.5 text-sm font-semibold transition-colors duration-300 rounded-lg cursor-pointer focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none ${
                      activeType === type
                        ? "text-primary-600 dark:text-primary-400"
                        : "text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                    }`}
                  >
                    {activeType === type && (
                      <motion.span
                        layoutId="activeCertificateTab"
                        className="absolute inset-0 bg-white dark:bg-gray-700 rounded-lg shadow-sm"
                        transition={{ type: "spring", stiffness: 500, damping: 30 }}
                      />
                    )}
                    <span className="relative z-10">
                      {type === "all" ? t("certificates.all") : t(`certificates.types.${type}`)}
                    </span>
                  </button>
                ))}
              </div>
            </div>
          </div>
        </section>
      )}

      {/* Grid */}
      <section className="py-20 bg-gray-50 dark:bg-gray-800" aria-live="polite">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          {visible.length === 0 ? (
            <div className="flex flex-col items-center text-center py-16">
              <Award className="h-12 w-12 text-gray-300 dark:text-gray-600" aria-hidden="true" />
              <p className="mt-4 text-gray-500 dark:text-gray-400">{t("certificates.empty")}</p>
            </div>
          ) : (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
              {visible.map((item) => (
                <motion.div
                  key={item.id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.4, ease: "easeOut" }}
                >
                  <Card className="overflow-hidden h-full flex flex-col group hover:shadow-xl transition-shadow duration-300 border border-gray-100 dark:border-gray-700">
                    {item.image ? (
                      <button
                        type="button"
                        onClick={() => setOpened(item)}
                        className="block w-full aspect-[4/3] bg-gray-100 dark:bg-gray-900 overflow-hidden cursor-zoom-in focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none"
                        aria-label={tr(item.title)}
                      >
                        <img
                          src={item.image}
                          alt={tr(item.title)}
                          loading="lazy"
                          className="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105"
                        />
                      </button>
                    ) : (
                      <div className="w-full aspect-[4/3] flex items-center justify-center bg-gradient-to-br from-primary-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                        <Award className="h-16 w-16 text-primary-500/60" aria-hidden="true" />
                      </div>
                    )}

                    <div className="p-6 flex flex-col flex-grow">
                      <span className="self-start mb-3 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-3 py-1 rounded-full text-xs font-semibold">
                        {t(`certificates.types.${item.type}`)}
                      </span>
                      <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                        {tr(item.title)}
                      </h3>
                      {(item.issuer || item.issued_at) && (
                        <p className="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                          {item.issuer && <span>{item.issuer}</span>}
                          {item.issued_at && (
                            <span className="inline-flex items-center gap-1">
                              <Calendar className="h-3.5 w-3.5" aria-hidden="true" />
                              {formatDate(item.issued_at)}
                            </span>
                          )}
                        </p>
                      )}
                      {tr(item.description) && (
                        <p className="mt-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                          {tr(item.description)}
                        </p>
                      )}
                      {item.credential_url && (
                        <a
                          href={item.credential_url}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="mt-auto pt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline"
                        >
                          {t("certificates.view")}
                          <ExternalLink className="h-4 w-4" aria-hidden="true" />
                        </a>
                      )}
                    </div>
                  </Card>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </section>

      {/* Full-size image */}
      <AnimatePresence>
        {opened?.image && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-4"
            onClick={() => setOpened(null)}
            role="dialog"
            aria-modal="true"
            aria-label={tr(opened.title)}
          >
            <button
              type="button"
              onClick={() => setOpened(null)}
              className="absolute top-4 right-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20 cursor-pointer"
              aria-label={t("certificates.close")}
            >
              <X className="h-6 w-6" aria-hidden="true" />
            </button>
            <img
              src={opened.image}
              alt={tr(opened.title)}
              className="max-h-[90vh] max-w-full rounded-lg shadow-2xl object-contain"
              onClick={(event) => event.stopPropagation()}
            />
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};
