import React from 'react';
import { motion } from 'framer-motion';

// Extending standard button attributes but omitting conflicting animation props
export interface ButtonProps extends Omit<React.ButtonHTMLAttributes<HTMLButtonElement>, 'onAnimationStart' | 'onDragStart' | 'onDragEnd' | 'onDrag'> {
  variant?: 'primary' | 'secondary' | 'outline';
  size?: 'sm' | 'md' | 'lg';
  href?: string;
  icon?: React.ReactNode;
  download?: boolean | string;
  target?: string;
  rel?: string;
}

export const Button: React.FC<ButtonProps> = ({
  children,
  variant = 'primary',
  size = 'md',
  type = 'button',
  href,
  className = '',
  icon,
  download,
  disabled, // Destructure disabled so we can use it for custom styling and logic
  ...props  // Catches all other standard props (onClick, id, etc.)
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

  // Add styles for disabled state
  const disabledClasses = disabled ? 'opacity-60 cursor-not-allowed shadow-none hover:shadow-none pointer-events-none' : '';

  const classes = `${baseClasses} ${variants[variant]} ${sizes[size]} ${disabledClasses} ${className}`;

  // Render as an Anchor link if href is provided
  if (href) {
    return (
      <motion.a
        href={href}
        className={classes}
        // Disable hover/tap animations if disabled
        whileHover={!disabled ? { scale: 1.02 } : {}}
        whileTap={!disabled ? { scale: 0.98 } : {}}
        target={href.startsWith('http') ? '_blank' : undefined}
        rel={href.startsWith('http') ? 'noopener noreferrer' : undefined}
        download={download}
        // Bypass strict typing for anchor/button mixed props
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        {...(props as any)}
      >
        {icon && <span className="mr-2 flex items-center justify-center">{icon}</span>}
        {children}
      </motion.a>
    );
  }

  // Render as a standard Button
  return (
    <motion.button
      type={type}
      className={classes}
      disabled={disabled}
      whileHover={!disabled ? { scale: 1.02 } : {}}
      whileTap={!disabled ? { scale: 0.98 } : {}}
      // Bypass strict typing for motion specific event overlaps
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      {...(props as any)}
    >
      {icon && <span className="mr-2 flex items-center justify-center">{icon}</span>}
      {children}
    </motion.button>
  );
};