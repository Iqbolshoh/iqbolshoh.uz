import React from 'react';
import { motion } from 'framer-motion';
import { Calendar, Clock, Tag, ArrowLeft } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Card } from '../components/UI/Card';
import { Button } from '../components/UI/Button';
import { blogPosts } from '../data/content';
import { useParams, useNavigate } from 'react-router-dom';

interface BlogDetailsProps {
  id?: string;
}

export const BlogDetails: React.FC<BlogDetailsProps> = () => {
  const { t, i18n } = useTranslation();
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const post = blogPosts.find((post) => post.id.toString() === id);

  const getLocalizedContent = (content: Record<string, string>, language: string) => {
    return content[language] || content.en || '';
  };

  if (!post) {
    return (
      <div className="pt-16">
        <section className="py-20 bg-white">
          <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h2 className="text-3xl font-bold text-gray-900">{t('blog.notFound')}</h2>
            <p className="mt-4 text-lg text-gray-600">{t('blog.postNotFound')}</p>
            <Button
              onClick={() => navigate('/blog')}
              icon={<ArrowLeft className="h-4 w-4" />}
              className="mt-6"
            >
              {t('blog.backToBlog')}
            </Button>
          </div>
        </section>
      </div>
    );
  }

  return (
    <div className="pt-16">
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <Button
              variant="outline"
              size="sm"
              onClick={() => navigate('/blog')}
              icon={<ArrowLeft className="h-4 w-4" />}
              className="mb-6"
            >
              {t('blog.backToBlog')}
            </Button>
            <Card className="overflow-hidden">
              <img
                src={post.image}
                alt={getLocalizedContent(post.title, i18n.language)}
                className="w-full object-cover"
              />
              <div className="p-8 lg:p-12">
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
                <h1 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                  {getLocalizedContent(post.title, i18n.language)}
                </h1>
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
                <p className="text-gray-600 leading-relaxed">
                  {getLocalizedContent(post.excerpt, i18n.language)}
                </p>
                {/* Add full content here if available */}
              </div>
            </Card>
          </motion.div>
        </div>
      </section>
    </div>
  );
};