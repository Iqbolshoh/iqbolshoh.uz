import React from "react";
import { motion } from "framer-motion";
import { Calendar, Tag, ArrowLeft } from "lucide-react";
import { useTranslation } from "react-i18next";
import { Card } from "../components/UI/Card";
import { Button } from "../components/UI/Button";
import { blogPosts } from "../data/content";
import { useParams, useNavigate } from "react-router-dom";
import SEO from "../components/SEO";

export const BlogDetails: React.FC = () => {
  const { t, i18n } = useTranslation();
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const post = blogPosts.find((p) => p.id.toString() === id);

  const getLocalizedContent = (
    content: Record<string, string>,
    language: string,
  ) => {
    return content[language] || content.en || "";
  };

  if (!post) {
    return (
      <div className="pt-16">
        <SEO title={t("blog.notFound")} noIndex />
        <section className="py-16 bg-white dark:bg-gray-900">
          <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
              {t("blog.notFound")}
            </h1>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400">
              {t("blog.postNotFound")}
            </p>
            <Button
              onClick={() => navigate("/blog")}
              icon={<ArrowLeft className="h-4 w-4" aria-hidden="true" />}
              className="mt-6"
            >
              {t("blog.backToBlog")}
            </Button>
          </div>
        </section>
      </div>
    );
  }

  const postTitle = getLocalizedContent(post.title, i18n.language);
  const postExcerpt = getLocalizedContent(post.excerpt, i18n.language);

  return (
    <div className="pt-16">
      <SEO
        title={postTitle}
        description={postExcerpt}
        keywords={post.tags.join(", ")}
        image={post.image}
        type="article"
        structuredData="article"
        articleData={{
          title: postTitle,
          description: postExcerpt,
          datePublished: post.date,
          image: post.image,
          tags: post.tags,
        }}
      />

      <section className="min-h-[50vh] py-24 bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <Button
              variant="outline"
              size="sm"
              onClick={() => navigate("/blog")}
              icon={<ArrowLeft className="h-4 w-4" aria-hidden="true" />}
              className="mb-6"
            >
              {t("blog.backToBlog")}
            </Button>

            <Card className="overflow-hidden">
              <img
                src={post.image}
                alt={postTitle}
                className="w-full object-cover max-h-[480px]"
              />
              <div className="p-8 lg:p-12">
                <div className="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                  <div className="flex items-center space-x-1">
                    <Calendar className="h-4 w-4" aria-hidden="true" />
                    <time dateTime={post.date}>
                      {new Date(post.date).toLocaleDateString()}
                    </time>
                  </div>
                </div>
                <h1 className="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                  {postTitle}
                </h1>
                <div className="flex flex-wrap gap-2 mb-6" aria-label="Tags">
                  {post.tags.map((tag) => (
                    <span
                      key={tag}
                      className="inline-flex items-center px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm rounded-full"
                    >
                      <Tag className="h-3 w-3 mr-1" aria-hidden="true" />
                      {tag}
                    </span>
                  ))}
                </div>
                <p className="text-gray-600 dark:text-gray-400 leading-relaxed text-lg">
                  {postExcerpt}
                </p>
              </div>
            </Card>
          </motion.div>
        </div>
      </section>
    </div>
  );
};
