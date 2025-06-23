import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { ChevronDown } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

const languages = [
  { code: 'uz', name: "O'zbekcha", flagPath: '/images/flags/uz.svg' },
  { code: 'en', name: 'English', flagPath: '/images/flags/gb.svg' },
  { code: 'ru', name: 'Русский', flagPath: '/images/flags/ru.svg' },
  { code: 'tj', name: 'Тоҷикӣ', flagPath: '/images/flags/tj.svg' },
];

export const LanguageSwitcher: React.FC = () => {
  const { i18n } = useTranslation();
  const [isOpen, setIsOpen] = useState(false);

  const currentLanguage = languages.find(lang => lang.code === i18n.language) || languages[1];

  const handleLanguageChange = (langCode: string) => {
    i18n.changeLanguage(langCode);
    setIsOpen(false);
  };

  return (
    <div className="relative z-30">
      <motion.button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center space-x-2 px-3 py-2 rounded-lg bg-accent-100 hover:bg-accent-200 transition-all duration-200"
        whileHover={{ scale: 1.02 }}
        whileTap={{ scale: 0.98 }}
      >
        <img src={currentLanguage.flagPath} alt={currentLanguage.name} className="w-5 h-5 rounded-full" />
        <span className="hidden sm:block text-sm font-medium text-logo-black">
          {currentLanguage.name}
        </span>
        <motion.div
          animate={{ rotate: isOpen ? 180 : 0 }}
          transition={{ duration: 0.2 }}
        >
          <ChevronDown className="h-4 w-4 text-logo-black" />
        </motion.div>
      </motion.button>

      <AnimatePresence>
        {isOpen && (
          <>
            {/* Mobile backdrop */}
            <div
              className="fixed inset-0 z-10 lg:hidden"
              onClick={() => setIsOpen(false)}
            />

            <motion.div
              initial={{ opacity: 0, y: -10, scale: 0.95 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              exit={{ opacity: 0, y: -10, scale: 0.95 }}
              transition={{ duration: 0.2, ease: 'easeOut' }}
              className="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-accent-200 z-20 overflow-hidden"
            >
              {languages.map((language, index) => (
                <motion.button
                  key={language.code}
                  onClick={() => handleLanguageChange(language.code)}
                  className={`w-full flex items-center space-x-3 px-4 py-3 text-left transition-all duration-200
                    ${i18n.language === language.code
                      ? 'bg-primary-100 text-primary-700'
                      : 'hover:bg-accent-50 text-logo-black'
                    }`}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: index * 0.05 }}
                  whileHover={{ x: 4 }}
                >
                  <img src={language.flagPath} alt={language.name} className="w-5 h-5 rounded-full" />
                  <span className="font-medium">{language.name}</span>
                  {i18n.language === language.code && (
                    <motion.div
                      className="ml-auto w-2 h-2 bg-primary-600 rounded-full"
                      initial={{ scale: 0 }}
                      animate={{ scale: 1 }}
                      transition={{ delay: 0.1 }}
                    />
                  )}
                </motion.button>
              ))}
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </div>
  );
};