import React, { useState, useEffect, useRef } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Menu, X, Sun, Moon } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { LanguageSwitcher } from '../LanguageSwitcher';
import { useTheme } from '../../context/ThemeContext';

export const Header: React.FC = () => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const location = useLocation();
  const { t } = useTranslation();
  const { theme, toggleTheme } = useTheme();
  const closeButtonRef = useRef<HTMLButtonElement>(null);
  const openButtonRef = useRef<HTMLButtonElement>(null);

  const navigation = [
    { name: t('nav.home'), href: '/' },
    { name: t('nav.about'), href: '/about' },
    { name: t('nav.portfolio'), href: '/portfolio' },
    { name: t('nav.services'), href: '/services' },
    { name: t('nav.blog'), href: '/blog' },
    { name: t('nav.contact'), href: '/contact' },
  ];

  // Close mobile menu on route change
  useEffect(() => {
    setMobileMenuOpen(false);
  }, [location.pathname]);

  // Focus close button when menu opens; restore focus to open button when it closes
  useEffect(() => {
    if (mobileMenuOpen) {
      setTimeout(() => closeButtonRef.current?.focus(), 100);
    } else {
      openButtonRef.current?.focus();
    }
  }, [mobileMenuOpen]);

  // Trap focus inside mobile menu and handle Escape
  useEffect(() => {
    if (!mobileMenuOpen) return;

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setMobileMenuOpen(false);
        return;
      }

      if (e.key !== 'Tab') return;

      const menuEl = document.getElementById('mobile-menu');
      if (!menuEl) return;

      const focusable = menuEl.querySelectorAll<HTMLElement>(
        'a, button, [tabindex]:not([tabindex="-1"])'
      );
      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [mobileMenuOpen]);

  const menuItemVariants = {
    initial: { opacity: 0, y: 20 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -20 },
  };

  return (
    <header className="fixed w-full top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
      <nav
        className="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8"
        aria-label="Main navigation"
      >
        <div className="flex flex-1 items-center justify-between">
          <Link
            to="/"
            className="flex items-center space-x-2 focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg"
            aria-label="Iqbolshoh dev — Home"
          >
            <img src="/images/logos/iqbolshoh.svg" alt="" aria-hidden="true" className="h-10 w-10" />
            <span className="text-xl font-bold text-gray-900 dark:text-white">
              Iqbolshoh<span className="text-primary-600"> dev</span>
            </span>
          </Link>

          <div className="flex items-center space-x-2 lg:hidden">
            <LanguageSwitcher />
            <button
              onClick={toggleTheme}
              className="p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500"
              aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
            >
              {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
            </button>
            <button
              ref={openButtonRef}
              onClick={() => setMobileMenuOpen(true)}
              className="p-2 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500"
              aria-label="Open navigation menu"
              aria-expanded={mobileMenuOpen}
              aria-controls="mobile-menu"
            >
              <Menu className="h-7 w-7" aria-hidden="true" />
            </button>
          </div>
        </div>

        {/* Desktop navigation */}
        <div className="hidden lg:flex lg:items-center lg:space-x-6" role="menubar">
          {navigation.map((item) => (
            <Link
              key={item.name}
              to={item.href}
              role="menuitem"
              aria-current={location.pathname === item.href ? 'page' : undefined}
              className={`relative px-2 py-3 text-base font-medium transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-md ${
                location.pathname === item.href
                  ? 'text-primary-600'
                  : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400'
              }`}
            >
              {item.name}
              {location.pathname === item.href && (
                <motion.span
                  layoutId="activeNav"
                  className="absolute left-0 bottom-0 h-0.5 w-full bg-primary-600"
                  transition={{ duration: 0.3 }}
                />
              )}
            </Link>
          ))}
          <div className="flex items-center space-x-4 ml-6">
            <button
              onClick={toggleTheme}
              className="p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500"
              aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
            >
              {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
            </button>
            <LanguageSwitcher />
            <Link
              to="/portfolio"
              className="rounded-md bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-base font-medium text-white shadow-md hover:shadow-glow-red transition-all focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
            >
              {t('nav.portfolio')}
            </Link>
          </div>
        </div>
      </nav>

      {/* Mobile navigation */}
      <AnimatePresence>
        {mobileMenuOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.2 }}
              className="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm"
              onClick={() => setMobileMenuOpen(false)}
              aria-hidden="true"
            />

            <motion.div
              id="mobile-menu"
              role="dialog"
              aria-modal="true"
              aria-label="Navigation menu"
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'spring', stiffness: 400, damping: 40 }}
              className="fixed right-0 top-0 z-50 w-full h-full bg-white dark:bg-gray-900"
            >
              <div className="h-full flex flex-col">
                <div className="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-800">
                  <Link
                    to="/"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center space-x-2 focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg"
                    aria-label="Iqbolshoh dev — Home"
                  >
                    <img src="/images/logos/iqbolshoh.svg" alt="" aria-hidden="true" className="h-9 w-9" />
                    <span className="text-xl font-bold text-gray-900 dark:text-white">
                      Iqbolshoh<span className="text-primary-600"> dev</span>
                    </span>
                  </Link>
                  <button
                    ref={closeButtonRef}
                    onClick={() => setMobileMenuOpen(false)}
                    className="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500"
                    aria-label="Close navigation menu"
                  >
                    <X className="h-8 w-8 text-gray-700 dark:text-gray-300" aria-hidden="true" />
                  </button>
                </div>

                <nav
                  className="w-full flex-1 flex flex-col space-y-4 p-6 overflow-y-auto"
                  aria-label="Mobile navigation"
                >
                  {navigation.map((item, index) => (
                    <motion.div
                      key={item.name}
                      variants={menuItemVariants}
                      initial="initial"
                      animate="animate"
                      exit="exit"
                      transition={{ delay: index * 0.08 }}
                    >
                      <Link
                        to={item.href}
                        onClick={() => setMobileMenuOpen(false)}
                        aria-current={location.pathname === item.href ? 'page' : undefined}
                        className={`block px-5 py-3.5 rounded-lg text-xl font-medium transition-all duration-200 focus-visible:ring-2 focus-visible:ring-primary-500 ${
                          location.pathname === item.href
                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 shadow-sm'
                            : 'text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-sm'
                        }`}
                      >
                        {item.name}
                      </Link>
                    </motion.div>
                  ))}

                  <div className="mt-auto pt-8 space-y-4">
                    <motion.div
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: 0.5 }}
                    >
                      <Link
                        to="/portfolio"
                        onClick={() => setMobileMenuOpen(false)}
                        className="block w-full text-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-3.5 text-xl font-medium text-white shadow-md hover:shadow-lg transition-all focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                      >
                        {t('nav.portfolio')}
                      </Link>
                    </motion.div>
                  </div>
                </nav>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </header>
  );
};
