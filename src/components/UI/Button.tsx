import React from 'react';
import { motion, HTMLMotionProps } from 'framer-motion';

// Extend HTMLMotionProps instead of standard HTML attributes to prevent type conflicts
export interface ButtonProps extends Omit<HTMLMotionProps<"button">, "ref"> {
  variant?: 'primary' | 'secondary' | 'outline';
  size?: 'sm' | 'md' | 'lg';
  href?: string;
  icon?: React.ReactNode;
  download?: boolean | string;
  target?: string;
  rel?: string;
}

export const Button: React.FC<ButtonProps> = ({
  variant = 'primary',
  size = 'md',
  type = 'button',
  href,
  className = '',
  icon,
  download,
  disabled,
  target,
  rel,
  ...props
}) => {
  const baseClasses =
    'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

  const variants = {
    primary: 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-lg hover:shadow-xl',
    secondary: 'bg-accent-600 text-white hover:bg-accent-700 focus:ring-accent-500 shadow-lg hover:shadow-xl',
    outline: 'border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white focus:ring-primary-500',
  };

  const sizes = {
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3 text-base',
    lg: 'px-8 py-4 text-lg',
  };

  const disabledClasses = disabled ? 'opacity-60 cursor-not-allowed shadow-none hover:shadow-none pointer-events-none' : '';

  const classes = `${baseClasses} ${variants[variant]} ${sizes[size]} ${disabledClasses} ${className}`;

  if (href) {
    // If it's a link, render an anchor tag. (Note: we use standard 'a' here, not motion.a, to keep types simple and avoid conflicts)
    return (
      <a
        href={href}
        className={`${classes} ${disabled ? '' : 'hover:scale-[1.02] active:scale-[0.98] transform transition-transform'}`}
        target={target || (href.startsWith('http') ? '_blank' : undefined)}
        rel={rel || (href.startsWith('http') ? 'noopener noreferrer' : undefined)}
        download={download}
        style={{ pointerEvents: disabled ? 'none' : 'auto' }}
      >
        {icon && <span className="mr-2 flex items-center justify-center">{icon}</span>}
      </a>
    );
  }

  return (
    <motion.button
      type={type}
      className={classes}
      disabled={disabled}
      whileHover={!disabled ? { scale: 1.02 } : undefined}
      whileTap={!disabled ? { scale: 0.98 } : undefined}
      {...props}
    >
      {icon && <span className="mr-2 flex items-center justify-center">{icon}</span>}
    </motion.button>
  );
};