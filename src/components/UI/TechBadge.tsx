import React from 'react';
import { useContent } from '../../context/ContentContext';

/** Badge for a technology a project or service is built with. */
interface TechBadgeProps {
  name: string;
  size?: 'sm' | 'md';
}

const FALLBACK_COLOR = '#8B95A5';

/**
 * The icon and colour come from the API's technology map, so a stack the
 * catalogue does not cover yet still renders — as a plain badge in the
 * neutral colour rather than a broken image.
 */
export const TechBadge: React.FC<TechBadgeProps> = ({ name, size = 'sm' }) => {
  const { technologies } = useContent();
  const meta = technologies[name];
  const color = meta?.color ?? FALLBACK_COLOR;

  const spacing =
    size === 'md' ? 'px-3 py-1.5 text-sm gap-2' : 'px-2.5 py-1 text-xs gap-1.5';
  const iconSize = size === 'md' ? 'w-4 h-4' : 'w-3.5 h-3.5';

  return (
    <span
      className={`inline-flex items-center rounded-full border font-semibold ${spacing}`}
      style={{
        color,
        // Hex with an alpha suffix: one brand colour drives the text, the
        // hairline border and the tint behind it.
        borderColor: `${color}59`,
        backgroundColor: `${color}1f`,
      }}
    >
      {meta?.icon && (
        <img
          src={meta.icon}
          alt=""
          loading="lazy"
          className={`${iconSize} shrink-0`}
        />
      )}
      {name}
    </span>
  );
};
