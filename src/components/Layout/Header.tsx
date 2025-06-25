import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { LanguageSwitcher } from '../LanguageSwitcher';

export const Header: React.FC = () => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const location = useLocation();
  const { t } = useTranslation();

  const navigation = [
    { name: t('nav.home'), href: '/' },
    { name: t('nav.about'), href: '/about' },
    { name: t('nav.portfolio'), href: '/portfolio' },
    { name: t('nav.services'), href: '/services' },
    { name: t('nav.blog'), href: '/blog' },
    { name: t('nav.contact'), href: '/contact' },
  ];

  // Animation variants for mobile menu items
  const menuItemVariants = {
    initial: { opacity: 0, y: 20 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -20 },
  };

  return (
    <header className="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
      <nav className="mx-auto flex max-w-7xl items-center justify-between p-4 md:p-6 lg:px-8">
        <div className="flex flex-1 items-center justify-between">
          <Link to="/" className="flex items-center space-x-2">
            <img src="/images/logos/iqbolshoh.svg" alt="Iqbolshoh Logo" className="h-9 w-9" />
            <span className="text-lg font-bold text-gray-900">
              Iqbolshoh<span className="text-primary-600"> dev</span>
            </span>
          </Link>
          <div className="flex items-center space-x-3 lg:hidden">
            <LanguageSwitcher />
            <button
              onClick={() => setMobileMenuOpen(true)}
              className="p-2 text-gray-700 rounded-full hover:bg-gray-100 transition-colors"
              aria-label="Open menu"
            >
              <Menu className="h-6 w-6" />
            </button>
          </div>
        </div>

        <div className="hidden lg:flex lg:items-center lg:space-x-8">
          {navigation.map((item) => (
            <Link
              key={item.name}
              to={item.href}
              className={`relative px-1 py-2 text-sm font-medium transition-colors ${location.pathname === item.href
                ? 'text-primary-600'
                : 'text-gray-700 hover:text-primary-600'
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
          <div className="flex items-center space-x-4 ml-4">
            <LanguageSwitcher />
            <Link
              to="/portfolio"
              className="rounded-md bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-medium text-white shadow-md hover:shadow-glow-red transition-all"
            >
              {t('nav.portfolio')}
            </Link>
          </div>
        </div>
      </nav>

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
            />

            <motion.div
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'spring', stiffness: 400, damping: 40 }}
              className="fixed right-0 top-0 z-50 w-full h-full bg-white"
            >
              <div className="h-full flex flex-col">
                <div className="flex items-center justify-between mb-1">
                  <Link
                    to="/"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center space-x-2"
                  >
                    <img src="/images/logos/iqbolshoh.svg" alt="Logo" className="h-8 w-8" />
                    <span className="text-lg font-bold text-gray-900">
                      Iqbolshoh<span className="text-primary-600"> dev</span>
                    </span>
                  </Link>
                  <button
                    onClick={() => setMobileMenuOpen(false)}
                    className="p-2 rounded-full hover:bg-gray-100 transition-colors"
                    aria-label="Close menu"
                  >
                    <X className="h-7 w-7 text-gray-700" />
                  </button>
                </div>

                <nav className="w-full flex-1 flex flex-col space-y-4 bg-white p-4 rounded-xl">
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
                        className={`block px-4 py-3 rounded-lg text-lg font-medium transition-all duration-200 ${location.pathname === item.href
                          ? 'bg-primary-50 text-primary-700 shadow-sm'
                          : 'text-gray-900 hover:bg-gray-50 hover:shadow-sm'
                          }`}
                      >
                        {item.name}
                      </Link>
                    </motion.div>
                  ))}
                  <div className="mt-auto pt-6 space-y-4">
                    <motion.div
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: 0.5 }}
                    >
                      <Link
                        to="/portfolio"
                        onClick={() => setMobileMenuOpen(false)}
                        className="block w-full text-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 text-lg font-medium text-white shadow-md hover:shadow-lg transition-all"
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