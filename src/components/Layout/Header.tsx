import React, { useState, useEffect, useRef } from 'react';
import { createPortal } from 'react-dom';
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

  // Close on route change
  useEffect(() => {
    setMobileMenuOpen(false);
  }, [location.pathname]);

  // Body scroll lock — prevents background scroll when menu is open
  useEffect(() => {
    if (mobileMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [mobileMenuOpen]);

  // Focus management: open → close button; close → hamburger button
  const didMountRef = React.useRef(false);
  useEffect(() => {
    if (!didMountRef.current) { didMountRef.current = true; return; }
    if (mobileMenuOpen) {
      setTimeout(() => closeButtonRef.current?.focus(), 100);
    } else {
      setTimeout(() => openButtonRef.current?.focus(), 50);
    }
  }, [mobileMenuOpen]);

  // Focus trap and Escape key
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
        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [mobileMenuOpen]);

  const itemVariants = {
    initial: { opacity: 0, x: 24 },
    animate: { opacity: 1, x: 0 },
    exit:    { opacity: 0, x: 12 },
  };

  return (
    <>
      <header className="fixed w-full top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <nav
          className="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8"
          aria-label="Main navigation"
        >
          {/* Logo + mobile controls row */}
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

            {/* Mobile-only controls */}
            <div className="flex items-center space-x-1 lg:hidden">
              <LanguageSwitcher />
              <button
                onClick={toggleTheme}
                className="p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500"
                aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
              >
                {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
              </button>
              <button
                ref={openButtonRef}
                onClick={() => setMobileMenuOpen(true)}
                className="p-2 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500"
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
                className="p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500"
                aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
              >
                {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
              </button>
              <LanguageSwitcher />
              <Link
                to="/portfolio"
                className="rounded-md bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-base font-medium text-white shadow-md hover:shadow-lg transition-all focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
              >
                {t('nav.portfolio')}
              </Link>
            </div>
          </div>
        </nav>
      </header>

      {/* Mobile menu rendered via portal — avoids header stacking context issues */}
      {createPortal(
        <AnimatePresence>
          {mobileMenuOpen && (
            <>
              {/* Backdrop */}
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.25 }}
                className="fixed inset-0 z-[9998] bg-black/60 backdrop-blur-sm"
                onClick={() => setMobileMenuOpen(false)}
                aria-hidden="true"
              />

              {/* Menu panel — slides in from right, full screen on mobile */}
              <motion.div
                id="mobile-menu"
                role="dialog"
                aria-modal="true"
                aria-label="Navigation menu"
                initial={{ x: '100%' }}
                animate={{ x: 0 }}
                exit={{ x: '100%' }}
                transition={{ type: 'spring', stiffness: 280, damping: 28 }}
                className="fixed inset-y-0 right-0 z-[9999] w-full sm:max-w-sm bg-white dark:bg-gray-900 flex flex-col shadow-2xl"
              >
                {/* Panel header */}
                <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
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
                    className="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    aria-label="Close navigation menu"
                  >
                    <X className="h-7 w-7 text-gray-700 dark:text-gray-300" aria-hidden="true" />
                  </button>
                </div>

                {/* Nav links — scrollable if needed */}
                <nav
                  className="flex-1 flex flex-col px-5 py-6 gap-1.5 overflow-y-auto overscroll-contain"
                  aria-label="Mobile navigation"
                >
                  {navigation.map((item, index) => (
                    <motion.div
                      key={item.name}
                      variants={itemVariants}
                      initial="initial"
                      animate="animate"
                      exit="exit"
                      transition={{ delay: index * 0.055, duration: 0.22 }}
                    >
                      <Link
                        to={item.href}
                        onClick={() => setMobileMenuOpen(false)}
                        aria-current={location.pathname === item.href ? 'page' : undefined}
                        className={`flex items-center px-4 py-3.5 rounded-xl text-lg font-medium transition-all duration-200 focus-visible:ring-2 focus-visible:ring-primary-500 ${
                          location.pathname === item.href
                            ? 'bg-primary-50 dark:bg-primary-900/25 text-primary-700 dark:text-primary-400 shadow-sm'
                            : 'text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800/70'
                        }`}
                      >
                        {location.pathname === item.href && (
                          <span className="mr-3 w-1.5 h-5 rounded-full bg-primary-500 shrink-0" aria-hidden="true" />
                        )}
                        {item.name}
                      </Link>
                    </motion.div>
                  ))}
                </nav>

                {/* CTA — pinned to bottom */}
                <div className="px-5 py-5 shrink-0 border-t border-gray-100 dark:border-gray-800">
                  <motion.div
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.38, duration: 0.25 }}
                  >
                    <Link
                      to="/portfolio"
                      onClick={() => setMobileMenuOpen(false)}
                      className="flex items-center justify-center w-full rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-3.5 text-lg font-semibold text-white shadow-md hover:shadow-lg active:scale-[0.98] transition-all focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    >
                      {t('nav.portfolio')}
                    </Link>
                  </motion.div>
                </div>
              </motion.div>
            </>
          )}
        </AnimatePresence>,
        document.body
      )}
    </>
  );
};
