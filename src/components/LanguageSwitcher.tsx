import React, { useState, useRef, useEffect, useCallback } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigate, useLocation } from 'react-router-dom';
import { ChevronDown } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useCurrentLang } from '../hooks/usePath';

const languages = [
  { code: 'en', name: 'English', flagPath: '/images/flags/gb.svg' },
  { code: 'ru', name: 'Русский', flagPath: '/images/flags/ru.svg' },
  { code: 'uz', name: "O'zbekcha", flagPath: '/images/flags/uz.svg' },
  { code: 'tj', name: 'Тоҷикӣ', flagPath: '/images/flags/tj.svg' },
];

export const LanguageSwitcher: React.FC = () => {
  const { i18n } = useTranslation();
  const navigate = useNavigate();
  const location = useLocation();
  const currentLang = useCurrentLang();
  const [isOpen, setIsOpen] = useState(false);
  const [focusedIndex, setFocusedIndex] = useState<number>(-1);
  const switcherRef = useRef<HTMLDivElement>(null);
  const buttonRef = useRef<HTMLButtonElement>(null);
  const optionRefs = useRef<(HTMLButtonElement | null)[]>([]);

  const currentLanguage = languages.find(l => l.code === i18n.language) || languages[0];

  const handleLanguageChange = (langCode: string) => {
    // Keep current sub-path, swap language prefix
    const subPath = location.pathname.replace(`/${currentLang}`, '') || '';
    navigate(`/${langCode}${subPath}`, { replace: true });
    i18n.changeLanguage(langCode);
    setIsOpen(false);
    setFocusedIndex(-1);
    buttonRef.current?.focus();
  };

  const closeDropdown = useCallback(() => {
    setIsOpen(false);
    setFocusedIndex(-1);
    buttonRef.current?.focus();
  }, []);

  // Close on outside click
  useEffect(() => {
    if (!isOpen) return;
    const handleClickOutside = (e: MouseEvent) => {
      if (switcherRef.current && !switcherRef.current.contains(e.target as Node)) {
        closeDropdown();
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [isOpen, closeDropdown]);

  // Focus option when focusedIndex changes
  useEffect(() => {
    if (isOpen && focusedIndex >= 0) {
      optionRefs.current[focusedIndex]?.focus();
    }
  }, [isOpen, focusedIndex]);

  const handleButtonKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
      e.preventDefault();
      setIsOpen(true);
      setFocusedIndex(0);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setIsOpen(true);
      setFocusedIndex(languages.length - 1);
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  };

  const handleOptionKeyDown = (e: React.KeyboardEvent, index: number) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      setFocusedIndex((index + 1) % languages.length);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setFocusedIndex((index - 1 + languages.length) % languages.length);
    } else if (e.key === 'Escape' || e.key === 'Tab') {
      closeDropdown();
    } else if (e.key === 'Home') {
      e.preventDefault();
      setFocusedIndex(0);
    } else if (e.key === 'End') {
      e.preventDefault();
      setFocusedIndex(languages.length - 1);
    }
  };

  return (
    <div className="relative z-30" ref={switcherRef}>
      <motion.button
        ref={buttonRef}
        onClick={() => setIsOpen(!isOpen)}
        onKeyDown={handleButtonKeyDown}
        className="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
        whileHover={{ scale: 1.02 }}
        whileTap={{ scale: 0.98 }}
        aria-haspopup="listbox"
        aria-expanded={isOpen}
        aria-label={`Language: ${currentLanguage.name}. Click to change`}
      >
        <img src={currentLanguage.flagPath} alt="" aria-hidden="true" className="w-5 h-5 rounded-md" />
        <span className="hidden sm:block text-sm font-medium text-gray-900 dark:text-gray-100">
          {currentLanguage.name}
        </span>
        <motion.div
          animate={{ rotate: isOpen ? 180 : 0 }}
          transition={{ duration: 0.2 }}
          aria-hidden="true"
        >
          <ChevronDown className="h-4 w-4 text-gray-700 dark:text-gray-300" />
        </motion.div>
      </motion.button>

      <AnimatePresence>
        {isOpen && (
          <motion.ul
            role="listbox"
            aria-label="Select language"
            initial={{ opacity: 0, y: -10, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -10, scale: 0.95 }}
            transition={{ duration: 0.15, ease: 'easeOut' }}
            className="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-20 overflow-hidden list-none p-1"
          >
            {languages.map((language, index) => {
              const isSelected = i18n.language === language.code;
              return (
                <li key={language.code} role="option" aria-selected={isSelected}>
                  <motion.button
                    ref={el => { optionRefs.current[index] = el; }}
                    onClick={() => handleLanguageChange(language.code)}
                    onKeyDown={(e) => handleOptionKeyDown(e, index)}
                    className={`w-full flex items-center space-x-3 px-4 py-3 text-left rounded-lg transition-all duration-200 cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none
                      ${isSelected
                        ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                        : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100'
                      }`}
                    initial={{ opacity: 0, x: -10 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: index * 0.05 }}
                    whileHover={{ x: 4 }}
                    tabIndex={0}
                  >
                    <img src={language.flagPath} alt="" aria-hidden="true" className="w-5 h-5 rounded-md" />
                    <span className="font-medium">{language.name}</span>
                    {isSelected && (
                      <motion.div
                        className="ml-auto w-2 h-2 bg-primary-600 rounded-full"
                        initial={{ scale: 0 }}
                        animate={{ scale: 1 }}
                        transition={{ delay: 0.1 }}
                        aria-hidden="true"
                      />
                    )}
                  </motion.button>
                </li>
              );
            })}
          </motion.ul>
        )}
      </AnimatePresence>
    </div>
  );
};
