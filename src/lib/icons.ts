import {
  Award,
  BookOpen,
  BookOpenCheck,
  Bot,
  Brush,
  Code,
  Code2,
  Compass,
  Database,
  FileCode,
  FileText,
  GraduationCap,
  HeartHandshake,
  Layout,
  Lightbulb,
  MessageCircle,
  Monitor,
  Network,
  Package,
  Palette,
  Rocket,
  Server,
  ShieldCheck,
  Star,
  Terminal,
  Users,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

/**
 * API ikonkani matn sifatida qaytaradi ("Award"), komponentlar esa uni
 * React komponenti sifatida ishlatadi (<stat.icon />). Shu jadval ularni bog'laydi.
 *
 * Yangi ikonka qo'shsangiz — seed'dagi nom bilan bir xil qilib shu yerga ham qo'shing.
 * Ro'yxatda yo'q nom kelsa Code ishlatiladi (sayt buzilmasligi uchun).
 */
const ICONS: Record<string, LucideIcon> = {
  Award,
  BookOpen,
  BookOpenCheck,
  Bot,
  Brush,
  Code,
  Code2,
  Compass,
  Database,
  FileCode,
  FileText,
  GraduationCap,
  HeartHandshake,
  Layout,
  Lightbulb,
  MessageCircle,
  Monitor,
  Network,
  Package,
  Palette,
  Rocket,
  Server,
  ShieldCheck,
  Star,
  Terminal,
  Users,
};

export const resolveIcon = (name: unknown): LucideIcon =>
  (typeof name === 'string' && ICONS[name]) || Code;

/** Massivdagi har bir elementning `icon` matnini komponentga almashtiradi. */
export function withIcons<T extends { icon?: unknown }>(
  items: T[] | undefined,
): (Omit<T, 'icon'> & { icon: LucideIcon })[] {
  return (items ?? []).map((item) => ({
    ...item,
    icon: resolveIcon(item.icon),
  }));
}
