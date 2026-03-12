import { Routes, Route, useLocation } from 'react-router-dom';
import { useEffect } from 'react';
import { HelmetProvider } from 'react-helmet-async';
import { useTranslation } from 'react-i18next';
import { Toaster } from 'react-hot-toast';

// Layout Components
import { Header } from './components/Layout/Header';
import { Footer } from './components/Layout/Footer';
import SEO from './components/SEO';

// Pages
import { Home } from './pages/Home';
import { About } from './pages/About';
import { Portfolio } from './pages/Portfolio';
import { Services } from './pages/Services';
import { Contact } from './pages/Contact';
import { Blog } from './pages/Blog';
import { BlogDetails } from './pages/BlogDetails';

/**
 * Scroll to top on route change
 */
function ScrollToTop() {
  const { pathname } = useLocation();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [pathname]);

  return null;
}

function App() {
  const { t } = useTranslation();
  const helmetContext = {};

  return (
    <HelmetProvider context={helmetContext}>
      <div className="min-h-screen bg-white flex flex-col">
        <Toaster position="top-right" reverseOrder={false} />

        <ScrollToTop />
        <Header />

        <main className="flex-grow">
          <Routes>
            {/* Home Page Route */}
            <Route path="/" element={
              <>
                <SEO
                  title={t('seo.home.title')}
                  description={t('seo.home.description')}
                  keywords={t('seo.home.keywords')}
                />
                <Home />
              </>
            } />

            {/* About Page Route */}
            <Route path="/about" element={
              <>
                <SEO
                  title={t('seo.about.title')}
                  description={t('seo.about.description')}
                  keywords={t('seo.about.keywords')}
                />
                <About />
              </>
            } />

            {/* Portfolio Page Route */}
            <Route path="/portfolio" element={
              <>
                <SEO
                  title={t('seo.portfolio.title')}
                  description={t('seo.portfolio.description')}
                  keywords={t('seo.portfolio.keywords')}
                />
                <Portfolio />
              </>
            } />

            {/* Services Page Route */}
            <Route path="/services" element={
              <>
                <SEO
                  title={t('seo.services.title')}
                  description={t('seo.services.description')}
                  keywords={t('seo.services.keywords')}
                />
                <Services />
              </>
            } />

            {/* Contact Page Route */}
            <Route path="/contact" element={
              <>
                <SEO
                  title={t('seo.contact.title')}
                  description={t('seo.contact.description')}
                  keywords={t('seo.contact.keywords')}
                />
                <Contact />
              </>
            } />

            {/* Blog Main Page Route */}
            <Route path="/blog" element={
              <>
                <SEO
                  title={t('seo.blog.title')}
                  description={t('seo.blog.description')}
                  keywords={t('seo.blog.keywords')}
                />
                <Blog />
              </>
            } />

            {/* Blog Details Page Route - SEO will be dynamic inside the component */}
            <Route path="/blog/:id" element={<BlogDetails />} />

            {/* 404 Fallback Route */}
            <Route path="*" element={
              <>
                <SEO
                  title={t('seo.notFound.title')}
                  noIndex={true}
                />
                <div className="flex items-center justify-center min-h-[60vh]">
                  <h1 className="text-3xl font-bold">404 - {t('seo.notFound.title')}</h1>
                </div>
              </>
            } />
          </Routes>
        </main>

        <Footer />
      </div>
    </HelmetProvider>
  );
}

export default App;