import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Github, Linkedin, Send, Instagram, Heart, Mail, Phone, MapPin } from 'lucide-react';
import { personalInfo } from '../../data/content';

export const Footer: React.FC = () => {
  const { t, i18n } = useTranslation();

  return (
    <footer className="bg-gray-900 text-gray-100 border-t border-gray-700">
      <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Brand Column */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <img
                src="/images/logos/iqbolshoh.svg"
                alt="Iqbolshoh Logo"
                className="h-10 w-10 bg-white rounded-md"
              />
              <span className="font-bold text-xl text-white">Iqbolshoh</span>
            </div>
            <p className="text-gray-300">{t('footer.description')}</p>
            <div className="flex gap-4">
              {Object.entries(personalInfo.social).map(([platform, data]) => {
                type Platform = 'github' | 'linkedin' | 'telegram' | 'instagram';
                const icons: Record<Platform, JSX.Element> = {
                  github: <Github className="h-5 w-5" />,
                  linkedin: <Linkedin className="h-5 w-5" />,
                  telegram: <Send className="h-5 w-5" />,
                  instagram: <Instagram className="h-5 w-5" />
                };

                if (!(platform in icons)) return null;

                return (
                  <a
                    key={platform}
                    href={(data as { link: string }).link}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-gray-400 hover:text-primary-400 transition-colors"
                  >
                    {icons[platform as Platform]}
                  </a>
                );
              })}
            </div>
          </div>

          {/* Navigation Column */}
          <div className="space-y-4">
            <h3 className="text-sm font-semibold text-primary-400">{t('footer.navigation')}</h3>
            <ul className="space-y-3">
              {['home', 'about', 'portfolio', 'services'].map((item) => (
                <li key={item}>
                  <Link
                    to={`/${item === 'home' ? '' : item}`}
                    className="text-gray-300 hover:text-white transition-colors"
                  >
                    {t(`nav.${item}`)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Resources Column */}
          <div className="space-y-4">
            <h3 className="text-sm font-semibold text-primary-400">{t('footer.resources')}</h3>
            <ul className="space-y-3">
              {['blog', 'contact'].map((item) => (
                <li key={item}>
                  <Link
                    to={`/${item}`}
                    className="text-gray-300 hover:text-white transition-colors"
                  >
                    {t(`nav.${item}`)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Column */}
          <div className="space-y-4">
            <h3 className="text-sm font-semibold text-primary-400">{ }</h3>
            <ul className="space-y-3">
              <li className="flex items-center gap-2 text-gray-300">
                <Mail className="h-5 w-5" />
                <a href={personalInfo.social.email.link} className="hover:text-white transition-colors">
                  {personalInfo.social.email.label}
                </a>
              </li>
              <li className="flex items-center gap-2 text-gray-300">
                <Phone className="h-5 w-5" />
                <a href={personalInfo.social.phone.link} className="hover:text-white transition-colors">
                  {personalInfo.social.phone.label}
                </a>
              </li>
              <li className="flex items-center gap-2 text-gray-300">
                <MapPin className="h-5 w-5" />
                <span>{personalInfo.location[i18n.language as keyof typeof personalInfo.location]}</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Copyright Section */}
        <div className="mt-12 pt-8 border-t border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
          <p className="text-gray-400 text-sm">{t('footer.copyright')}</p>
          <p className="text-gray-400 text-sm flex items-center">
            {t('footer.madeWithLove')}
            <Heart className="h-4 w-4 text-primary-400 mx-1 animate-pulse" />
            {t('footer.fromSamarkand')}
          </p>
        </div>
      </div>
    </footer>
  );
};