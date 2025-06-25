import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Github, Linkedin, Send, Instagram, Heart } from 'lucide-react';
import { personalInfo } from '../../data/content';

export const Footer: React.FC = () => {
  const { t } = useTranslation();

  return (
    <footer className="bg-gray-900 text-gray-100 border-t border-gray-700 relative overflow-hidden">
      <div className="absolute inset-0 bg-logo-pattern opacity-5"></div>
      <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8 relative z-10">
        <div className="xl:grid xl:grid-cols-3 xl:gap-8">
          <div className="space-y-8">
            <div className="flex items-center space-x-3">
              <img
                src="/images/logos/iqbolshoh.svg"
                alt="Iqbolshoh Logo"
                className="h-10 w-10 bg-white rounded-md transition-all duration-300 group-hover:scale-110 group-hover:drop-shadow-glow-red"
              />
              <span className="font-bold text-xl text-white">Iqbolshoh</span>
            </div>
            <p className="text-gray-300 max-w-md">{t('footer.description')}</p>
            <div className="flex space-x-6">
              <a
                href={personalInfo.social.github}
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary-400 transition-all duration-300 hover:scale-110"
              >
                <Github className="h-6 w-6" />
              </a>
              <a
                href={personalInfo.social.linkedin}
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary-400 transition-all duration-300 hover:scale-110"
              >
                <Linkedin className="h-6 w-6" />
              </a>
              <a
                href={personalInfo.social.telegram}
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary-400 transition-all duration-300 hover:scale-110"
              >
                <Send className="h-6 w-6" />
              </a>
              <a
                href={personalInfo.social.instagram}
                target="_blank"
                rel="noopener noreferrer"
                className="text-gray-400 hover:text-primary-400 transition-all duration-300 hover:scale-110"
              >
                <Instagram className="h-6 w-6" />
              </a>
            </div>
          </div>

          <div className="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
            <div className="md:grid md:grid-cols-2 md:gap-8">
              <div>
                <h3 className="text-sm font-semibold leading-6 text-primary-400">{t('footer.navigation')}</h3>
                <ul className="mt-6 space-y-4">
                  <li><Link to="/" className="text-gray-300 hover:text-white transition-colors">{t('nav.home')}</Link></li>
                  <li><Link to="/about" className="text-gray-300 hover:text-white transition-colors">{t('nav.about')}</Link></li>
                  <li><Link to="/portfolio" className="text-gray-300 hover:text-white transition-colors">{t('nav.portfolio')}</Link></li>
                  <li><Link to="/services" className="text-gray-300 hover:text-white transition-colors">{t('nav.services')}</Link></li>
                </ul>
              </div>
              <div className="mt-10 md:mt-0">
                <h3 className="text-sm font-semibold leading-6 text-primary-400">{t('footer.resources')}</h3>
                <ul className="mt-6 space-y-4">
                  <li><Link to="/blog" className="text-gray-300 hover:text-white transition-colors">{t('nav.blog')}</Link></li>
                  <li><Link to="/contact" className="text-gray-300 hover:text-white transition-colors">{t('nav.contact')}</Link></li>
                  <li><a href="mailto:iqbolshoh@gmail.com" className="text-gray-300 hover:text-white transition-colors">{t('contact.email')}</a></li>
                  <li><a href="tel:+998997799333" className="text-gray-300 hover:text-white transition-colors">{t('contact.phone')}</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-16 border-t border-gray-700 pt-8 sm:mt-20 lg:mt-24">
          <div className="flex flex-col sm:flex-row justify-between items-center">
            <p className="text-gray-400">{t('footer.copyright')}</p>
            <p className="flex items-center text-gray-400 mt-4 sm:mt-0">
              {t('common.madeWithLove')} <Heart className="h-4 w-4 text-primary-400 mx-1 animate-pulse" /> {t('common.fromSamarkand')}
            </p>
          </div>
        </div>
      </div>
    </footer>
  );
};