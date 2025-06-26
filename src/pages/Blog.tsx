import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Calendar, Clock, ArrowRight, Tag } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Card } from '../components/UI/Card';
import { Button } from '../components/UI/Button';
import { blogPosts } from '../data/content';
import { Link } from 'react-router-dom';

export const Blog: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [currentPage, setCurrentPage] = useState(1);
  const postsPerPage = 3;

  const getLocalizedContent = (content: Record<string, string>, language: string) => {
    return content[language] || content.en || '';
  };

  const totalPages = Math.ceil(blogPosts.length / postsPerPage);
  const paginatedPosts = [...blogPosts]
    .reverse()
    .slice((currentPage - 1) * postsPerPage, currentPage * postsPerPage);

  return (
    <div className="pt-16">
      {/* Title section */}
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
              {t('blog.title')}
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
              {t('blog.description')}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Posts */}
      {paginatedPosts.length > 0 && (
        <section className="py-20 bg-white">
          <div className="mx-auto max-w-7xl px-6 lg:px-8 space-y-16">
            {paginatedPosts.map((post, index) => (
              <motion.div
                key={post.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.05 }}
                viewport={{ once: true }}
              >
                <Card className="overflow-hidden lg:grid lg:grid-cols-2 lg:gap-0">
                  {/* Image */}
                  <div className="relative">
                    <img
                      src={post.image}
                      alt={getLocalizedContent(post.title, i18n.language)}
                      className="w-full h-64 lg:h-full object-cover"
                    />
                    {post.featured && (
                      <div className="absolute top-4 left-4">
                        <span className="bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                          {t('blog.featured')}
                        </span>
                      </div>
                    )}
                  </div>

                  {/* Content */}
                  <div className="p-8 lg:p-12 flex flex-col justify-center">
                    <div className="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                      <div className="flex items-center space-x-1">
                        <Calendar className="h-4 w-4" />
                        <span>{new Date(post.date).toLocaleDateString()}</span>
                      </div>
                      <div className="flex items-center space-x-1">
                        <Clock className="h-4 w-4" />
                        <span>{post.readTime}</span>
                      </div>
                    </div>
                    <h3 className="text-2xl font-bold text-gray-900 mb-4 line-clamp-2">
                      {getLocalizedContent(post.title, i18n.language)}
                    </h3>
                    <p className="text-gray-600 mb-6 line-clamp-2">
                      {getLocalizedContent(post.excerpt, i18n.language)}
                    </p>
                    <div className="flex flex-wrap gap-2 mb-6">
                      {post.tags.map((tag) => (
                        <span
                          key={tag}
                          className="inline-flex items-center px-3 py-1 bg-primary-100 text-primary-800 text-sm rounded-full"
                        >
                          <Tag className="h-3 w-3 mr-1" />
                          {tag}
                        </span>
                      ))}
                    </div>
                    <Link to={`/blog/${post.id}`}>
                      <Button icon={<ArrowRight className="h-4 w-4" />} className="self-start">
                        {t('blog.readMore')}
                      </Button>
                    </Link>
                  </div>
                </Card>
              </motion.div>
            ))}

            {/* Pagination buttons */}
            {totalPages > 1 && (
              <div className="flex justify-center items-center gap-2 mt-10 flex-wrap">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    className={`px-4 py-2 text-sm rounded-full border transition ${page === currentPage
                      ? 'bg-primary-500 text-white border-primary-500'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                      }`}
                  >
                    {page}
                  </button>
                ))}
              </div>
            )}
          </div>
        </section>
      )}
    </div>
  );
};
