import React, { useState } from "react";
import { motion } from "framer-motion";
import { Calendar, ArrowRight, Tag } from "lucide-react";
import { useTranslation } from "react-i18next";
import { Card } from "../components/UI/Card";
import { Button } from "../components/UI/Button";
import { blogPosts } from "../data/content";
import { Link } from "react-router-dom";
import { usePath } from "../hooks/usePath";

export const Blog: React.FC = () => {
  const { t, i18n } = useTranslation();
  const toPath = usePath();
  const [currentPage, setCurrentPage] = useState(1);
  const postsPerPage = 3;

  const getLocalizedContent = (
    content: Record<string, string>,
    language: string,
  ) => {
    return content[language] || content.en || "";
  };

  const totalPages = Math.ceil(blogPosts.length / postsPerPage);
  const paginatedPosts = [...blogPosts]
    .reverse()
    .slice((currentPage - 1) * postsPerPage, currentPage * postsPerPage);

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
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
              {t("blog.title")}
            </h1>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
              {t("blog.description")}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Posts */}
      {paginatedPosts.length > 0 && (
        <section className="py-20 bg-white dark:bg-gray-900">
          <div className="mx-auto max-w-7xl px-6 lg:px-8">
            <motion.div
              key={currentPage}
              initial="hidden"
              animate="show"
              variants={{
                hidden: { opacity: 0 },
                show: {
                  opacity: 1,
                  transition: {
                    staggerChildren: 0.1,
                  },
                },
              }}
              className="space-y-12"
            >
              {paginatedPosts.map((post) => (
                <motion.article
                  key={post.id}
                  variants={{
                    hidden: { opacity: 0, y: 20 },
                    show: { opacity: 1, y: 0 },
                  }}
                  transition={{ duration: 0.5, ease: "easeOut" }}
                  viewport={{ once: true }}
                  aria-labelledby={`post-title-${post.id}`}
                >
                  <Card className="overflow-hidden lg:grid lg:grid-cols-2 lg:gap-0 border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-shadow duration-300">
                    {/* Image */}
                    <div className="relative">
                      <img
                        src={post.image}
                        alt=""
                        aria-hidden="true"
                        className="w-full h-56 lg:h-full object-cover"
                      />
                      {post.featured && (
                        <div className="absolute top-4 left-4">
                          <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-md">
                            {t("blog.featured")}
                          </span>
                        </div>
                      )}
                    </div>

                    {/* Content */}
                    <div className="p-6 lg:p-10 flex flex-col justify-center">
                      <div className="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <div className="flex items-center space-x-1">
                          <Calendar className="h-4 w-4" aria-hidden="true" />
                          <time dateTime={post.date}>
                            {new Date(post.date).toLocaleDateString()}
                          </time>
                        </div>
                      </div>
                      <h2
                        id={`post-title-${post.id}`}
                        className="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2"
                      >
                        {getLocalizedContent(post.title, i18n.language)}
                      </h2>
                      <p className="text-gray-600 dark:text-gray-400 mb-4 text-sm line-clamp-2 leading-relaxed">
                        {getLocalizedContent(post.excerpt, i18n.language)}
                      </p>
                      <div
                        className="flex flex-wrap gap-2 mb-6"
                        aria-label="Tags"
                      >
                        {post.tags.map((tag) => (
                          <span
                            key={tag}
                            className="inline-flex items-center px-2.5 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-xs font-semibold rounded-full"
                          >
                            <Tag className="h-3 w-3 mr-1" aria-hidden="true" />
                            {tag}
                          </span>
                        ))}
                      </div>
                      <Link to={toPath(`/blog/${post.id}`)}>
                        <Button
                          size="sm"
                          icon={
                            <ArrowRight
                              className="h-4 w-4"
                              aria-hidden="true"
                            />
                          }
                          className="self-start shadow-md shadow-primary-500/10"
                        >
                          {t("blog.readMore")}
                          <span className="sr-only">
                            : {getLocalizedContent(post.title, i18n.language)}
                          </span>
                        </Button>
                      </Link>
                    </div>
                  </Card>
                </motion.article>
              ))}
            </motion.div>

            {/* Pagination */}
            {totalPages > 1 && (
              <nav
                className="flex justify-center items-center gap-2 mt-12 flex-wrap"
                aria-label="Blog pagination"
              >
                {Array.from({ length: totalPages }, (_, i) => i + 1).map(
                  (page) => (
                    <button
                      key={page}
                      onClick={() => {
                        setCurrentPage(page);
                        window.scrollTo({ top: 400, behavior: "smooth" });
                      }}
                      aria-label={`Page ${page}`}
                      aria-current={page === currentPage ? "page" : undefined}
                      className={`w-10 h-10 flex items-center justify-center font-semibold text-sm rounded-xl transition-all duration-300 cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none ${
                        page === currentPage
                          ? "bg-primary-600 text-white shadow-lg shadow-primary-500/30"
                          : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary-500 hover:text-primary-600"
                      }`}
                    >
                      {page}
                    </button>
                  ),
                )}
              </nav>
            )}
          </div>
        </section>
      )}
    </div>
  );
};
