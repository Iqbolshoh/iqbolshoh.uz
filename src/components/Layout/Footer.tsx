import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Github, Linkedin, Instagram, Send, Mail, Phone, MapPin, Laptop } from 'lucide-react';
import { personalInfo } from '../../data/content';

export const Footer: React.FC = () => {
  const { t, i18n } = useTranslation();

  return (
    <footer className="bg-gray-900 dark:bg-gray-950 text-gray-100 border-t border-gray-800 dark:border-gray-800/60 transition-colors duration-200">
      <div className="max-w-7xl mx-auto px-6 py-14">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-10">
          <div className="md:col-span-4 space-y-6">
            <div className="flex flex-col gap-6">
              <div className="flex items-center gap-6">
                <img
                  src="/images/logos/iqbolshoh_dev.svg"
                  alt="Iqbolshoh Logo"
                  className="h-24 w-24 bg-white rounded-2xl border border-gray-700 shadow-md"
                />
                <div>
                  <h2 className="text-3xl font-extrabold text-white leading-tight">
                    Iqbolshoh
                  </h2>
                  <p className="text-base text-gray-400">{t('footer.description')}</p>
                </div>
              </div>
            </div>
            <div className="flex gap-4" role="list" aria-label="Social links">
              {Object.entries(personalInfo.social).map(([platform, data]) => (
                <a
                  key={platform}
                  href={(data as { link: string }).link}
                  target="_blank"
                  rel="noopener noreferrer"
                  role="listitem"
                  aria-label={platform.charAt(0).toUpperCase() + platform.slice(1)}
                  className="text-gray-400 hover:text-primary-400 hover:bg-gray-800 p-2 rounded-full transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900"
                >
                  {platform === 'github' && <Github className="h-5 w-5" aria-hidden="true" />}
                  {platform === 'linkedin' && <Linkedin className="h-5 w-5" aria-hidden="true" />}
                  {platform === 'telegram' && <Send className="h-5 w-5" aria-hidden="true" />}
                  {platform === 'instagram' && <Instagram className="h-5 w-5" aria-hidden="true" />}
                </a>
              ))}
            </div>
          </div>

          <div className="md:col-span-8 grid grid-cols-2 md:grid-cols-3 gap-10">
            <nav aria-label="Footer navigation">
              <h3 className="text-sm font-semibold uppercase tracking-wider text-white mb-4">
                {t('footer.navigation')}
              </h3>
              <ul className="space-y-3">
                {['home', 'about', 'portfolio', 'services'].map((item) => (
                  <li key={item}>
                    <Link
                      to={`/${item === 'home' ? '' : item}`}
                      className="text-sm text-gray-400 hover:text-primary-400 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900 rounded"
                    >
                      {t(`nav.${item}`)}
                    </Link>
                  </li>
                ))}
              </ul>
            </nav>

            <nav aria-label="Footer resources">
              <h3 className="text-sm font-semibold uppercase tracking-wider text-white mb-4">
                {t('footer.resources')}
              </h3>
              <ul className="space-y-3">
                {['blog', 'contact'].map((item) => (
                  <li key={item}>
                    <Link
                      to={`/${item}`}
                      className="text-sm text-gray-400 hover:text-primary-400 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900 rounded"
                    >
                      {t(`nav.${item}`)}
                    </Link>
                  </li>
                ))}
              </ul>
            </nav>

            <div>
              <h3 className="text-sm font-semibold uppercase tracking-wider text-white mb-4">
                {t('footer.contact')}
              </h3>
              <ul className="space-y-4 text-sm">
                <li className="flex items-center gap-3">
                  <div className="bg-gray-800 p-2 rounded-full" aria-hidden="true">
                    <Mail className="h-4 w-4 text-primary-400" />
                  </div>
                  <a
                    href={personalInfo.social.email.link}
                    className="hover:text-primary-400 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 rounded"
                    aria-label={`Email: ${personalInfo.social.email.label}`}
                  >
                    {personalInfo.social.email.label}
                  </a>
                </li>
                <li className="flex items-center gap-3">
                  <div className="bg-gray-800 p-2 rounded-full" aria-hidden="true">
                    <Phone className="h-4 w-4 text-primary-400" />
                  </div>
                  <a
                    href={personalInfo.social.phone.link}
                    className="hover:text-primary-400 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 rounded"
                    aria-label={`Phone: ${personalInfo.social.phone.label}`}
                  >
                    {personalInfo.social.phone.label}
                  </a>
                </li>
                <li className="flex items-center gap-3">
                  <div className="bg-gray-800 p-2 rounded-full" aria-hidden="true">
                    <MapPin className="h-4 w-4 text-primary-400" />
                  </div>
                  <span>
                    {personalInfo.location[i18n.language as keyof typeof personalInfo.location]}
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div className="mt-16 pt-6 border-t border-gray-800/50 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-3">
          <p>© {new Date().getFullYear()} {t('footer.copyright')}</p>
          <p className="flex items-center gap-1">
            <Laptop className="h-3 w-3 text-primary-400" aria-hidden="true" />
            {personalInfo.name[i18n.language as keyof typeof personalInfo.name]}
          </p>
        </div>
      </div>
    </footer>
  );
};
