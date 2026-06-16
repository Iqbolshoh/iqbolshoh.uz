import { useParams, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

export const SUPPORTED_LANGS = ['en', 'uz', 'ru', 'tj'] as const;
export type SupportedLang = (typeof SUPPORTED_LANGS)[number];

/** Returns the active language — works both inside and outside /:lang routes. */
export const useCurrentLang = (): SupportedLang => {
  const { lang } = useParams<{ lang?: string }>();
  const location = useLocation();
  const { i18n } = useTranslation();

  if (lang && (SUPPORTED_LANGS as readonly string[]).includes(lang)) {
    return lang as SupportedLang;
  }

  const fromPath = location.pathname.split('/')[1];
  if (fromPath && (SUPPORTED_LANGS as readonly string[]).includes(fromPath)) {
    return fromPath as SupportedLang;
  }

  const current = i18n.language?.split('-')[0];
  if (current && (SUPPORTED_LANGS as readonly string[]).includes(current)) {
    return current as SupportedLang;
  }

  return 'uz';
};

/**
 * Returns a function that converts a root-relative path (e.g. '/portfolio')
 * to a lang-prefixed path (e.g. '/uz/portfolio').
 */
export const usePath = () => {
  const lang = useCurrentLang();
  return (to: string) => (to === '/' ? `/${lang}` : `/${lang}${to}`);
};
