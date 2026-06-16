import React from 'react';

export const PageLoader: React.FC = () => (
  <div className="min-h-[calc(100vh-4rem)] flex items-center justify-center pt-16">
    <div className="relative w-20 h-20">
      {/* Spinning ring */}
      <div className="absolute inset-0 rounded-full border-4 border-gray-200 dark:border-gray-700" />
      <div className="absolute inset-0 rounded-full border-4 border-primary-600 border-t-transparent animate-spin" />
      {/* Logo centered inside */}
      <div className="absolute inset-0 flex items-center justify-center">
        <img
          src="/images/logos/iqbolshoh.svg"
          alt=""
          aria-hidden="true"
          className="w-10 h-10"
        />
      </div>
    </div>
  </div>
);
