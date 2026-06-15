import React from 'react';
import { motion } from 'framer-motion';

interface CardProps {
  children: React.ReactNode;
  className?: string;
  hover?: boolean;
}

export const Card: React.FC<CardProps> = ({
  children,
  className = '',
  hover = true
}) => {
  return (
    <motion.div
      className={`bg-white dark:bg-gray-800 rounded-xl shadow-lg ${hover ? 'hover:shadow-xl cursor-pointer' : ''} transition-all duration-300 ${className}`}
      whileHover={hover ? { y: -5 } : undefined}
      transition={{ type: 'tween', duration: 0.2 }}
    >
      {children}
    </motion.div>
  );
};
