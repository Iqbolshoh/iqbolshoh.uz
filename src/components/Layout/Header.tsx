import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { LanguageSwitcher } from '../LanguageSwitcher';
import { ThemeToggle } from '../ThemeToggle';

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
    <header className="fixed w-full top-0 z-50 bg-white/95 dark:bg-dark-bg-primary/95 backdrop-blur-md border-b border-gray-200/50 dark:border-gray-800/50">
      <nav className="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
        <div className="flex lg:flex-1">
          <Link to="/" className="-m-1.5 p-1.5 flex items-center space-x-3 group">
            <div className="relative">
              <img 
                src="/iqbolshoh.svg" 
                alt="Iqbolshoh Logo" 
                className="h-10 w-10 transition-all duration-300 group-hover:scale-110 group-hover:drop-shadow-glow-red dark:brightness-110"
              />
              <div className="absolute inset-0 bg-gradient-to-r from-primary-500 to-accent-500 opacity-0 group-hover:opacity-20 rounded-full transition-opacity duration-300"></div>
            </div>
            <span className="font-bold text-xl text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
              Iqbolshoh
            </span>
          </Link>
        </div>
        
        <div className="flex lg:hidden items-center space-x-3">
          <ThemeToggle />
          <LanguageSwitcher />
          <button
            type="button"
            className="-m-2.5 inline-flex items-center justify-center rounded-lg p-2.5 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
            onClick={() => setMobileMenuOpen(true)}
          >
            <Menu className="h-6 w-6" />
          </button>
        </div>
        
        <div className="hidden lg:flex lg:gap-x-12">
          {navigation.map((item) => (
            <Link
              key={item.name}
              to={item.href}
              className={`text-sm font-semibold leading-6 transition-all duration-200 relative group ${
                location.pathname === item.href
                  ? 'text-primary-600 dark:text-primary-400'
                  : 'text-gray-900 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400'
              }`}
            >
              {item.name}
              <span className={`absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-600 dark:bg-primary-400 transition-all duration-200 group-hover:w-full ${
                location.pathname === item.href ? 'w-full' : ''
              }`}></span>
            </Link>
          ))}
        </div>
        
        <div className="hidden lg:flex lg:flex-1 lg:justify-end lg:items-center lg:space-x-4">
          <ThemeToggle />
          <LanguageSwitcher />
          <Link
            to="/contact"
            className="relative overflow-hidden rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-500 dark:to-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-glow-red transition-all duration-300 group"
          >
            <span className="relative z-10">{t('nav.hireMe')}</span>
            <div className="absolute inset-0 bg-gradient-to-r from-primary-700 to-primary-800 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </Link>
        </div>
      </nav>

      <AnimatePresence>
        {mobileMenuOpen && (
          <>
            {/* Backdrop */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="lg:hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
              onClick={() => setMobileMenuOpen(false)}
            />
            
            {/* Mobile Menu */}
            <motion.div
              initial={{ x: '100%' }}
              animate={{ x: 0 }}
              exit={{ x: '100%' }}
              transition={{ type: 'spring', damping: 25, stiffness: 200 }}
              className="lg:hidden fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-white dark:bg-dark-bg-secondary shadow-2xl dark:shadow-dark-xl"
            >
              <div className="flex h-full flex-col">
                {/* Header */}
                <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                  <Link to="/" className="flex items-center space-x-3" onClick={() => setMobileMenuOpen(false)}>
                    <img 
                      src="/iqbolshoh.svg" 
                      alt="Iqbolshoh Logo" 
                      className="h-8 w-8 dark:brightness-110"
                    />
                    <span className="font-bold text-xl text-gray-900 dark:text-gray-100">Iqbolshoh</span>
                  </Link>
                  <button
                    type="button"
                    className="rounded-lg p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    <X className="h-6 w-6" />
                  </button>
                </div>

                {/* Navigation */}
                <div className="flex-1 overflow-y-auto py-6">
                  <div className="space-y-2 px-6">
                    {navigation.map((item, index) => (
                      <motion.div
                        key={item.name}
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: index * 0.1 }}
                      >
                        <Link
                          to={item.href}
                          className={`block rounded-xl px-4 py-3 text-base font-semibold transition-all duration-200 ${
                            location.pathname === item.href
                              ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800'
                              : 'text-gray-900 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400'
                          }`}
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          {item.name}
                        </Link>
                      </motion.div>
                    ))}
                  </div>
                </div>

                {/* Footer */}
                <div className="border-t border-gray-200 dark:border-gray-700 p-6 space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Settings</span>
                    <div className="flex items-center space-x-3">
                      <ThemeToggle />
                      <LanguageSwitcher />
                    </div>
                  </div>
                  
                  <Link
                    to="/contact"
                    className="block w-full rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-500 dark:to-primary-600 px-4 py-3 text-center text-sm font-semibold text-white shadow-lg hover:shadow-glow-red transition-all duration-300"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    {t('nav.hireMe')}
                  </Link>
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </header>
  );
};