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

  return (
    <header className="fixed w-full top-0 z-50 bg-white backdrop-blur-md border-b border-gray-200">
      <nav className="mx-auto flex max-w-7xl items-center justify-between p-4 md:p-6 lg:px-8 bg-white lg:bg-transparent">
        <div className="flex flex-1 items-center justify-between">
          <Link to="/" className="flex items-center space-x-2">
            <img src="/iqbolshoh.svg" alt="Iqbolshoh Logo" className="h-9 w-9" />
            <span className="text-lg font-bold text-gray-900">
              Iqbolshoh<span className="text-primary-600"> dev</span>
            </span>
          </Link>
          <div className="flex items-center space-x-3 lg:hidden">
            <LanguageSwitcher />
            <button onClick={() => setMobileMenuOpen(true)} className="p-2 text-gray-700">
              <Menu className="h-6 w-6" />
            </button>
          </div>
        </div>

        <div className="hidden lg:flex lg:items-center lg:space-x-6">
          {navigation.map((item) => (
            <Link
              key={item.name}
              to={item.href}
              className={`text-sm font-semibold transition-colors ${location.pathname === item.href
                  ? 'text-primary-600'
                  : 'text-gray-700 hover:text-primary-600'
                }`}
            >
              {item.name}
            </Link>
          ))}
          <div className="flex items-center space-x-4 ml-4">
            <LanguageSwitcher />
            <Link
              to="/portfolio"
              className="rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white shadow-md hover:shadow-glow-red transition-all"
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
              className="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
              onClick={() => setMobileMenuOpen(false)}
            />
            <motion.div
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'spring', stiffness: 300, damping: 30 }}
              className="fixed top-0 right-0 z-50 h-full w-64 sm:w-80 bg-white p-6 shadow-lg"
            >
              <div className="flex items-center justify-between mb-6">
                <Link to="/" onClick={() => setMobileMenuOpen(false)} className="flex items-center space-x-2">
                  <img src="/iqbolshoh.svg" alt="Logo" className="h-8 w-8" />
                  <span className="text-lg font-bold text-gray-900">
                    Iqbolshoh<span className="text-primary-600"> dev</span>
                  </span>
                </Link>
                <button onClick={() => setMobileMenuOpen(false)} className="p-2 text-gray-700">
                  <X className="h-6 w-6" />
                </button>
              </div>
              <div className="space-y-2">
                {navigation.map((item) => (
                  <Link
                    key={item.name}
                    to={item.href}
                    onClick={() => setMobileMenuOpen(false)}
                    className={`block px-4 py-2 rounded-md text-sm font-semibold transition-colors ${location.pathname === item.href
                        ? 'bg-primary-100 text-primary-700'
                        : 'text-gray-700 hover:bg-gray-100'
                      }`}
                  >
                    {item.name}
                  </Link>
                ))}
              </div>
              <div className="mt-6 space-y-3">
                <LanguageSwitcher />
                <Link
                  to="/portfolio"
                  onClick={() => setMobileMenuOpen(false)}
                  className="block w-full text-center rounded-md bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white"
                >
                  {t('nav.portfolio')}
                </Link>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </header>
  );
};