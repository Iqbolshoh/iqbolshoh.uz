import React from 'react';
import { Helmet } from 'react-helmet-async';
import { useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useCurrentLang, SUPPORTED_LANGS } from '../hooks/usePath';

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

const SITE_URL     = 'https://iqbolshoh.uz';
const SITE_NAME    = 'Iqbolshoh.uz';
const TWITTER      = '@Iqbolshoh_777';
const DEFAULT_OG   = `${SITE_URL}/images/og-main.png`;
const OG_W         = '1200';
const OG_H         = '630';

// Maps app language code → BCP 47 hreflang value (Google standard)
const HREFLANG: Record<string, string> = {
  en: 'en',
  uz: 'uz',
  ru: 'ru',
  tj: 'tg', // Tajik BCP 47 is "tg"
};

// Maps app language code → Open Graph locale value
const OG_LOCALE: Record<string, string> = {
  en: 'en_US',
  uz: 'uz_UZ',
  ru: 'ru_RU',
  tj: 'tg_TJ',
};

// Maps app language code → Schema.org inLanguage value
const SCHEMA_LANG: Record<string, string> = {
  en: 'en',
  uz: 'uz',
  ru: 'ru',
  tj: 'tg',
};

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
    target: { '@type': 'EntryPoint', urlTemplate: `${SITE_URL}/uz/portfolio?q={search_term_string}` },
    'query-input': 'required name=search_term_string',
  },
};

function buildWebpageSchema(url: string, title: string, description: string, lang: string) {
  return {
    '@type': 'WebPage',
    '@id': `${url}#webpage`,
    url,
    name: title,
    description,
    inLanguage: SCHEMA_LANG[lang] ?? lang,
    isPartOf: { '@id': `${SITE_URL}/#website` },
    author: { '@id': `${SITE_URL}/#person` },
  };
}

function buildArticleSchema(url: string, data: ArticleData, lang: string) {
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
    inLanguage: SCHEMA_LANG[lang] ?? lang,
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
  const { t } = useTranslation();
  const location = useLocation();
  const lang = useCurrentLang();

  // Full canonical URL including language prefix (e.g. https://iqbolshoh.uz/uz/portfolio)
  const canonicalUrl = `${SITE_URL}${location.pathname}`;

  // Sub-path without the language segment (e.g. /portfolio)
  const subPath = location.pathname.replace(`/${lang}`, '') || '';

  const metaImage = image
    ? image.startsWith('http') ? image : `${SITE_URL}${image}`
    : DEFAULT_OG;

  const fullTitle = title.includes(SITE_NAME) ? title : `${title} | ${SITE_NAME}`;
  const metaDesc = description
    || t('seo.home.description', 'Portfolio of Iqbolshoh Ilhomjonov, a Full-Stack Developer specializing in Laravel, React, and scalable web applications.');

  // Build JSON-LD graph
  const graphNodes: object[] = [PERSON_SCHEMA, WEBSITE_SCHEMA];

  if (structuredData === 'home' || structuredData === 'webpage') {
    graphNodes.push(buildWebpageSchema(canonicalUrl, fullTitle, metaDesc, lang));
  }
  if (structuredData === 'person') {
    graphNodes.push({ ...PERSON_SCHEMA, mainEntityOfPage: canonicalUrl });
    graphNodes.push(buildWebpageSchema(canonicalUrl, fullTitle, metaDesc, lang));
  }
  if (structuredData === 'article' && articleData) {
    graphNodes.push(buildArticleSchema(canonicalUrl, articleData, lang));
    graphNodes.push(buildWebpageSchema(canonicalUrl, fullTitle, metaDesc, lang));
  }

  const jsonLd = { '@context': 'https://schema.org', '@graph': graphNodes };

  // Alternate locales for og:locale:alternate
  const alternateLocales = SUPPORTED_LANGS
    .filter(l => l !== lang)
    .map(l => OG_LOCALE[l]);

  return (
    <Helmet>
      {/* Language attribute on <html> */}
      <html lang={HREFLANG[lang] ?? lang} />

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

      {/* Canonical — always points to the current language version */}
      <link rel="canonical" href={canonicalUrl} />

      {/*
        Hreflang — each supported language gets its own URL.
        x-default points to the Uzbek version (primary audience).
      */}
      {SUPPORTED_LANGS.map(l => (
        <link
          key={l}
          rel="alternate"
          hrefLang={HREFLANG[l]}
          href={`${SITE_URL}/${l}${subPath}`}
        />
      ))}
      <link rel="alternate" hrefLang="x-default" href={`${SITE_URL}/uz${subPath}`} />

      {/* Open Graph */}
      <meta property="og:site_name" content={SITE_NAME} />
      <meta property="og:title" content={fullTitle} />
      <meta property="og:description" content={metaDesc} />
      <meta property="og:type" content={type} />
      <meta property="og:url" content={canonicalUrl} />
      <meta property="og:image" content={metaImage} />
      <meta property="og:image:width" content={OG_W} />
      <meta property="og:image:height" content={OG_H} />
      <meta property="og:image:alt" content={fullTitle} />
      {/* og:locale in BCP 47 format (e.g. uz_UZ) */}
      <meta property="og:locale" content={OG_LOCALE[lang] ?? lang} />
      {/* Announce alternate language versions */}
      {alternateLocales.map(locale => (
        <meta key={locale} property="og:locale:alternate" content={locale} />
      ))}
      {type === 'article' && articleData && (
        <>
          <meta property="article:published_time" content={articleData.datePublished} />
          <meta property="article:author" content={`${SITE_URL}/${lang}/about`} />
          {articleData.tags.map(tag => (
            <meta key={tag} property="article:tag" content={tag} />
          ))}
        </>
      )}

      {/* Twitter Card */}
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:site" content={TWITTER} />
      <meta name="twitter:creator" content={TWITTER} />
      <meta name="twitter:title" content={fullTitle} />
      <meta name="twitter:description" content={metaDesc} />
      <meta name="twitter:image" content={metaImage} />
      <meta name="twitter:image:alt" content={fullTitle} />

      {/* JSON-LD Structured Data */}
      <script type="application/ld+json">{JSON.stringify(jsonLd)}</script>
    </Helmet>
  );
};

// eslint-disable-next-line react-refresh/only-export-components
export default SEO;
