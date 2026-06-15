import React from 'react';
import { Helmet } from 'react-helmet-async';
import { useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

interface ArticleData {
  title: string;
  description: string;
  datePublished: string;
  image: string;
  tags: string[];
}

interface SEOProps {
  title: string;
  description?: string;
  keywords?: string;
  image?: string;
  type?: 'website' | 'article' | 'profile';
  noIndex?: boolean;
  structuredData?: 'home' | 'person' | 'article' | 'webpage';
  articleData?: ArticleData;
}

const SITE_URL = 'https://iqbolshoh.uz';
const SITE_NAME = 'Iqbolshoh.uz';
const TWITTER_HANDLE = '@Iqbolshoh_777';
const DEFAULT_OG_IMAGE = `${SITE_URL}/images/og-main.png`;
const OG_IMAGE_WIDTH = '1200';
const OG_IMAGE_HEIGHT = '630';

const PERSON_SCHEMA = {
  '@type': 'Person',
  '@id': `${SITE_URL}/#person`,
  name: 'Iqbolshoh Ilhomjonov',
  url: SITE_URL,
  image: `${SITE_URL}/images/logos/iqbolshoh-1.jpg`,
  jobTitle: 'Full-Stack Developer',
  description: 'Full-Stack Developer specializing in Laravel, React, PHP and scalable web applications.',
  email: 'iilhomjonov777@gmail.com',
  address: {
    '@type': 'PostalAddress',
    addressLocality: 'Samarkand',
    addressCountry: 'UZ',
  },
  sameAs: [
    'https://github.com/Iqbolshoh',
    'https://linkedin.com/in/iqbolshoh',
    'https://t.me/iqbolshoh_777',
    'https://instagram.com/Iqbolshoh_777',
  ],
};

const WEBSITE_SCHEMA = {
  '@type': 'WebSite',
  '@id': `${SITE_URL}/#website`,
  url: SITE_URL,
  name: SITE_NAME,
  description: 'Portfolio of Iqbolshoh Ilhomjonov — Full-Stack Developer',
  inLanguage: ['en', 'uz', 'ru', 'tg'],
  author: { '@id': `${SITE_URL}/#person` },
  potentialAction: {
    '@type': 'SearchAction',
    target: { '@type': 'EntryPoint', urlTemplate: `${SITE_URL}/portfolio?q={search_term_string}` },
    'query-input': 'required name=search_term_string',
  },
};

function buildWebpageSchema(url: string, title: string, description: string) {
  return {
    '@type': 'WebPage',
    '@id': `${url}#webpage`,
    url,
    name: title,
    description,
    isPartOf: { '@id': `${SITE_URL}/#website` },
    author: { '@id': `${SITE_URL}/#person` },
    inLanguage: 'en',
  };
}

function buildArticleSchema(url: string, data: ArticleData) {
  return {
    '@type': 'BlogPosting',
    '@id': `${url}#article`,
    url,
    headline: data.title,
    description: data.description,
    image: data.image.startsWith('http') ? data.image : `${SITE_URL}${data.image}`,
    datePublished: data.datePublished,
    dateModified: data.datePublished,
    author: { '@id': `${SITE_URL}/#person` },
    publisher: { '@id': `${SITE_URL}/#person` },
    keywords: data.tags.join(', '),
    inLanguage: 'en',
    isPartOf: { '@id': `${SITE_URL}/#website` },
  };
}

const SEO: React.FC<SEOProps> = ({
  title,
  description,
  keywords,
  image,
  type = 'website',
  noIndex = false,
  structuredData,
  articleData,
}) => {
  const { i18n, t } = useTranslation();
  const location = useLocation();

  const currentLang = i18n.language || 'en';
  const currentPath = location.pathname;
  const canonicalUrl = `${SITE_URL}${currentPath}`;

  const metaImage = image
    ? image.startsWith('http') ? image : `${SITE_URL}${image}`
    : DEFAULT_OG_IMAGE;

  const fullTitle = title.includes(SITE_NAME) ? title : `${title} | ${SITE_NAME}`;
  const metaDesc = description || t('seo.home.description', 'Portfolio of Iqbolshoh Ilhomjonov, a Full-Stack Developer specializing in Laravel, React, and scalable web applications.');

  const languages = ['en', 'uz', 'ru', 'tj'];

  // Build JSON-LD graph
  const graphNodes: object[] = [PERSON_SCHEMA, WEBSITE_SCHEMA];

  if (structuredData === 'home' || structuredData === 'webpage') {
    graphNodes.push(buildWebpageSchema(canonicalUrl, fullTitle, metaDesc));
  }
  if (structuredData === 'person') {
    graphNodes.push({ ...PERSON_SCHEMA, mainEntityOfPage: canonicalUrl });
  }
  if (structuredData === 'article' && articleData) {
    graphNodes.push(buildArticleSchema(canonicalUrl, articleData));
    graphNodes.push(buildWebpageSchema(canonicalUrl, fullTitle, metaDesc));
  }

  const jsonLd = {
    '@context': 'https://schema.org',
    '@graph': graphNodes,
  };

  return (
    <Helmet>
      {/* Language */}
      <html lang={currentLang} />

      {/* Primary meta */}
      <title>{fullTitle}</title>
      <meta name="description" content={metaDesc} />
      {keywords && <meta name="keywords" content={keywords} />}
      <meta name="author" content="Iqbolshoh Ilhomjonov" />
      <meta name="theme-color" content="#ef4444" />
      <meta name="color-scheme" content="light dark" />

      {/* Robots */}
      {noIndex ? (
        <meta name="robots" content="noindex, nofollow" />
      ) : (
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
      )}

      {/* Canonical */}
      <link rel="canonical" href={canonicalUrl} />

      {/* Hreflang */}
      {languages.map((lang) => (
        <link key={lang} rel="alternate" hrefLang={lang} href={canonicalUrl} />
      ))}
      <link rel="alternate" hrefLang="x-default" href={canonicalUrl} />

      {/* Open Graph */}
      <meta property="og:site_name" content={SITE_NAME} />
      <meta property="og:title" content={fullTitle} />
      <meta property="og:description" content={metaDesc} />
      <meta property="og:type" content={type} />
      <meta property="og:url" content={canonicalUrl} />
      <meta property="og:image" content={metaImage} />
      <meta property="og:image:width" content={OG_IMAGE_WIDTH} />
      <meta property="og:image:height" content={OG_IMAGE_HEIGHT} />
      <meta property="og:image:alt" content={fullTitle} />
      <meta property="og:locale" content={currentLang} />
      {type === 'article' && articleData && (
        <>
          <meta property="article:published_time" content={articleData.datePublished} />
          <meta property="article:author" content={`${SITE_URL}/about`} />
          {articleData.tags.map((tag) => (
            <meta key={tag} property="article:tag" content={tag} />
          ))}
        </>
      )}

      {/* Twitter Card */}
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:site" content={TWITTER_HANDLE} />
      <meta name="twitter:creator" content={TWITTER_HANDLE} />
      <meta name="twitter:title" content={fullTitle} />
      <meta name="twitter:description" content={metaDesc} />
      <meta name="twitter:image" content={metaImage} />
      <meta name="twitter:image:alt" content={fullTitle} />

      {/* JSON-LD Structured Data */}
      <script type="application/ld+json">
        {JSON.stringify(jsonLd)}
      </script>
    </Helmet>
  );
};

// eslint-disable-next-line react-refresh/only-export-components
export default SEO;
