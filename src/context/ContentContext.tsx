import React, { createContext, useContext, useEffect, useState } from 'react';
import type { LucideIcon } from 'lucide-react';
import { getContent } from '../lib/api';
import { withIcons } from '../lib/icons';
import { PageLoader } from '../components/UI/PageLoader';

/** Multilingual text: {"en":…,"uz":…,"ru":…,"tj":…} */
export type Translated = Record<string, string>;

export interface SocialLink {
  label: string;
  link: string;
}

export interface PersonalInfo {
  name: Translated;
  location: Translated;
  social: Record<string, SocialLink>;
}

export interface Project {
  id: number;
  name: Translated;
  description: Translated;
  image?: string;
  tech: string[];
  liveDemo: string | null;
  github: string | null;
  featured: boolean;
  category: string;
}

export interface Service {
  id: number;
  category: string;
  icon: LucideIcon;
  price: string | null;
  title: Translated;
  description: Translated;
  tech: string[];
  features: Record<string, string[]>;
}

/** Badge styling for one technology name, served by the API. */
export interface TechMeta {
  icon: string | null;
  color: string;
}

export interface SiteContent {
  personalInfo: PersonalInfo;
  techStack: { name: string; icon: LucideIcon; level: number }[];
  stats: { icon: LucideIcon; value: string; label: Translated }[];
  projects: Project[];
  highlights: { icon: LucideIcon; text: Translated }[];
  journey: { year: string; title: Translated; description: Translated }[];
  beyond: { icon: LucideIcon; title: Translated; description: Translated }[];
  services: Service[];
  processSteps: { step: string; title: Translated; description: Translated }[];
  technologies: Record<string, TechMeta>;
}

const ContentContext = createContext<SiteContent | null>(null);

/** An empty image is `null` in JSON, but components expect `undefined`. */
const withImage = <T extends { image?: string | null }>(items: T[] | undefined) =>
  (items ?? []).map((item) => ({ ...item, image: item.image ?? undefined }));

/** Turns the icon names in the API response into components. */
function normalize(raw: any): SiteContent {
  return {
    personalInfo: raw.personalInfo,
    techStack: withIcons(raw.techStack),
    stats: withIcons(raw.stats),
    projects: withImage(raw.projects),
    highlights: withIcons(raw.highlights),
    journey: raw.journey ?? [],
    beyond: withIcons(raw.beyond),
    services: withIcons(raw.services),
    processSteps: raw.processSteps ?? [],
    technologies: raw.technologies ?? {},
  } as SiteContent;
}

export const ContentProvider: React.FC<{ children: React.ReactNode }> = ({
  children,
}) => {
  const [content, setContent] = useState<SiteContent | null>(null);
  const [failed, setFailed] = useState(false);

  useEffect(() => {
    let active = true;

    getContent()
      .then((raw) => {
        if (active) setContent(normalize(raw));
      })
      .catch(() => {
        if (active) setFailed(true);
      });

    return () => {
      active = false;
    };
  }, []);

  if (failed) {
    return (
      <div className="flex min-h-screen flex-col items-center justify-center gap-4 p-6 text-center">
        <h1 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
          Ma'lumotlarni yuklab bo'lmadi
        </h1>
        <p className="text-gray-600 dark:text-gray-400">
          Server bilan aloqa yo'q. Iltimos, sahifani yangilang.
        </p>
        <button
          onClick={() => window.location.reload()}
          className="rounded-lg bg-primary-600 px-5 py-2.5 font-medium text-white hover:bg-primary-700"
        >
          Yangilash
        </button>
      </div>
    );
  }

  if (!content) return <PageLoader />;

  return (
    <ContentContext.Provider value={content}>{children}</ContentContext.Provider>
  );
};

/**
 * The site content. The provider renders no children until the payload has
 * loaded, so the value here is always present.
 */
export function useContent(): SiteContent {
  const value = useContext(ContentContext);

  if (!value) {
    throw new Error('useContent must be used inside <ContentProvider>');
  }

  return value;
}
