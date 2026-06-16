import { Routes, Route, useLocation, Navigate, Outlet, useParams, useNavigate } from 'react-router-dom';
import { useEffect, lazy, Suspense } from 'react';
import { HelmetProvider } from 'react-helmet-async';
import { useTranslation } from 'react-i18next';
import { Toaster } from 'react-hot-toast';

// Layout Components
import { Header } from './components/Layout/Header';
import { Footer } from './components/Layout/Footer';
import SEO from './components/SEO';
import { PageLoader } from './components/UI/PageLoader';
import { SUPPORTED_LANGS } from './hooks/usePath';

// Pages (lazy loaded for code splitting)
const Home        = lazy(() => import('./pages/Home').then(m => ({ default: m.Home })));
const About       = lazy(() => import('./pages/About').then(m => ({ default: m.About })));
const Portfolio   = lazy(() => import('./pages/Portfolio').then(m => ({ default: m.Portfolio })));
const Services    = lazy(() => import('./pages/Services').then(m => ({ default: m.Services })));
const Contact     = lazy(() => import('./pages/Contact').then(m => ({ default: m.Contact })));
const Blog        = lazy(() => import('./pages/Blog').then(m => ({ default: m.Blog })));
const BlogDetails = lazy(() => import('./pages/BlogDetails').then(m => ({ default: m.BlogDetails })));
const NotFound    = lazy(() => import('./pages/NotFound').then(m => ({ default: m.NotFound })));

function ScrollToTop() {
  const { pathname } = useLocation();
  useEffect(() => { window.scrollTo(0, 0); }, [pathname]);
  return null;
}

/** Redirects bare "/" to the user's saved or default language. */
function RootRedirect() {
  const { i18n } = useTranslation();
  const raw = i18n.language?.split('-')[0] ?? '';
  const lang = (SUPPORTED_LANGS as readonly string[]).includes(raw) ? raw : 'uz';
  return <Navigate to={`/${lang}`} replace />;
}

/** Guards the /:lang segment — redirects to /uz if the value isn't a supported language. */
function LangLayout() {
  const { lang } = useParams<{ lang: string }>();
  const { i18n } = useTranslation();
  const navigate = useNavigate();

  useEffect(() => {
    if (!lang || !(SUPPORTED_LANGS as readonly string[]).includes(lang)) {
      navigate('/uz', { replace: true });
      return;
    }
    if (i18n.language !== lang) {
      i18n.changeLanguage(lang);
    }
  }, [lang, i18n, navigate]);

  if (!lang || !(SUPPORTED_LANGS as readonly string[]).includes(lang)) {
    return null;
  }

  return <Outlet />;
}

function App() {
  const { t } = useTranslation();
  const helmetContext = {};

  return (
    <HelmetProvider context={helmetContext}>
      <a href="#main-content" className="skip-link">
        Skip to main content
      </a>

      <div className="min-h-screen bg-white dark:bg-gray-950 flex flex-col transition-colors duration-200">
        <Toaster position="top-right" reverseOrder={false} />
        <ScrollToTop />
        <Header />

        <main id="main-content" className="flex-grow" tabIndex={-1}>
          <Suspense fallback={<PageLoader />}>
            <Routes>
              {/* Bare "/" → redirect to saved language */}
              <Route index element={<RootRedirect />} />

              {/* Language-prefixed routes */}
              <Route path=":lang" element={<LangLayout />}>
                <Route index element={<>
                  <SEO title={t('seo.home.title')} description={t('seo.home.description')} keywords={t('seo.home.keywords')} type="website" structuredData="home" />
                  <Home />
                </>} />

                <Route path="about" element={<>
                  <SEO title={t('seo.about.title')} description={t('seo.about.description')} keywords={t('seo.about.keywords')} type="profile" structuredData="person" />
                  <About />
                </>} />

                <Route path="portfolio" element={<>
                  <SEO title={t('seo.portfolio.title')} description={t('seo.portfolio.description')} keywords={t('seo.portfolio.keywords')} />
                  <Portfolio />
                </>} />

                <Route path="services" element={<>
                  <SEO title={t('seo.services.title')} description={t('seo.services.description')} keywords={t('seo.services.keywords')} />
                  <Services />
                </>} />

                <Route path="contact" element={<>
                  <SEO title={t('seo.contact.title')} description={t('seo.contact.description')} keywords={t('seo.contact.keywords')} />
                  <Contact />
                </>} />

                <Route path="blog" element={<>
                  <SEO title={t('seo.blog.title')} description={t('seo.blog.description')} keywords={t('seo.blog.keywords')} />
                  <Blog />
                </>} />

                <Route path="blog/:id" element={<BlogDetails />} />
              </Route>

              {/* 404 */}
              <Route path="*" element={<>
                <SEO title={t('seo.notFound.title')} noIndex={true} />
                <NotFound />
              </>} />
            </Routes>
          </Suspense>
        </main>

        <Footer />
      </div>
    </HelmetProvider>
  );
}

export default App;
