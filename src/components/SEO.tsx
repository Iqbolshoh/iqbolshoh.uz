import React from 'react';
import { Helmet } from 'react-helmet-async';
import { useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

interface SEOProps {
    title: string;
    description?: string;
    keywords?: string;
    image?: string;
    type?: 'website' | 'article' | 'profile';
    noIndex?: boolean;
}

const SEO: React.FC<SEOProps> = ({
    title,
    description,
    keywords,
    image,
    type = 'website',
    noIndex = false
}) => {
    const { i18n, t } = useTranslation();
    const location = useLocation();

    // Default language fallback is 'en' for your portfolio based on your App settings
    const currentLang = i18n.language || 'en';

    // Update to your portfolio domain
    const siteUrl = 'https://iqbolshoh.uz';

    const currentPath = location.pathname;
    const canonicalUrl = `${siteUrl}${currentPath}`;

    // Update default OG image path (make sure you have an image at public/images/og-main.png)
    const metaImage = image
        ? (image.startsWith('http') ? image : `${siteUrl}${image}`)
        : `${siteUrl}/images/og-main.png`;

    const languages = ['en', 'uz', 'ru', 'tj'];

    const siteName = 'Iqbolshoh.uz';

    // Prevent duplicating the site name if it's already in the title
    const fullTitle = title.includes(siteName) ? title : `${title} | ${siteName}`;

    // Fallback description uses translation or a hardcoded fallback
    const metaDesc = description || t('seo.home.description', "Portfolio of Iqbolshoh Ilhomjonov, a Full-Stack Developer specializing in Laravel, React, and scalable SaaS platforms.");

    return (
        <Helmet>
            {/* 1. Main tags */}
            <html lang={currentLang} />
            <title>{fullTitle}</title>
            <meta name="description" content={metaDesc} />
            {keywords && <meta name="keywords" content={keywords} />}
            <meta name="author" content="Iqbolshoh Ilhomjonov" />

            {/* Robots */}
            {noIndex ? (
                <meta name="robots" content="noindex, nofollow" />
            ) : (
                <meta name="robots" content="index, follow, max-image-preview:large" />
            )}

            {/* 2. Canonical URL */}
            <link rel="canonical" href={canonicalUrl} />

            {/* 3. Hreflang Tags (Helps Google serve the right language version) */}
            {languages.map((lang) => (
                <link
                    key={lang}
                    rel="alternate"
                    hrefLang={lang}
                    // Adjust this depending on how you handle language routing (e.g., query param vs subdirectories)
                    // If you use subdirectories (like /uz/about), update the href accordingly.
                    // Currently assuming it relies on localStorage/state, so the URL stays the same.
                    href={`${siteUrl}${currentPath}`}
                />
            ))}
            <link rel="alternate" hrefLang="x-default" href={`${siteUrl}${currentPath}`} />

            {/* 4. Open Graph (Facebook, Telegram, LinkedIn) */}
            <meta property="og:site_name" content={siteName} />
            <meta property="og:title" content={fullTitle} />
            <meta property="og:description" content={metaDesc} />
            <meta property="og:type" content={type} />
            <meta property="og:url" content={canonicalUrl} />
            <meta property="og:image" content={metaImage} />
            <meta property="og:locale" content={currentLang} />

            {/* 5. Twitter Card */}
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={fullTitle} />
            <meta name="twitter:description" content={metaDesc} />
            <meta name="twitter:image" content={metaImage} />
        </Helmet>
    );
};

// eslint-disable-next-line react-refresh/only-export-components
export default SEO;