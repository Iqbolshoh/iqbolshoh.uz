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
import { NotFound } from './pages/NotFound';

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
      {/* Skip-to-content link for keyboard users */}
      <a href="#main-content" className="skip-link">
        Skip to main content
      </a>

      <div className="min-h-screen bg-white dark:bg-gray-950 flex flex-col transition-colors duration-200">
        <Toaster position="top-right" reverseOrder={false} />
        <ScrollToTop />
        <Header />

        <main id="main-content" className="flex-grow" tabIndex={-1}>
          <Routes>
            <Route path="/" element={
              <>
                <SEO
                  title={t('seo.home.title')}
                  description={t('seo.home.description')}
                  keywords={t('seo.home.keywords')}
                  type="website"
                  structuredData="home"
                />
                <Home />
              </>
            } />

            <Route path="/about" element={
              <>
                <SEO
                  title={t('seo.about.title')}
                  description={t('seo.about.description')}
                  keywords={t('seo.about.keywords')}
                  type="profile"
                  structuredData="person"
                />
                <About />
              </>
            } />

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

            <Route path="/blog/:id" element={<BlogDetails />} />

            <Route path="*" element={
              <>
                <SEO
                  title={t('seo.notFound.title')}
                  noIndex={true}
                />
                <NotFound />
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
