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
      className={`bg-white rounded-xl shadow-lg ${hover ? 'hover:shadow-xl' : ''} transition-shadow duration-300 ${className}`}
      whileHover={hover ? { y: -5 } : undefined}
      transition={{ type: 'tween', duration: 0.2 }}
    >
      {children}
    </motion.div>
  );
};