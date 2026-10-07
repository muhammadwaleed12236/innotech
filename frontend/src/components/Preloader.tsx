"use client";

import { useEffect, useState } from "react";

export default function Preloader() {
  const [showPreloader, setShowPreloader] = useState(true);
  const [fadeOut, setFadeOut] = useState(false);

  useEffect(() => {
    // Fast dismissal so users experience zero artificial waiting delay
    const timer = setTimeout(() => {
      setFadeOut(true);
      const hideTimer = setTimeout(() => {
        setShowPreloader(false);
      }, 250);
      return () => clearTimeout(hideTimer);
    }, 200);

    return () => clearTimeout(timer);
  }, []);

  if (!showPreloader) return null;

  return (
    <div
      className={`fixed inset-0 bg-white z-[9999] flex flex-col items-center justify-center select-none pointer-events-none transition-opacity duration-250 ease-out ${
        fadeOut ? "opacity-0" : "opacity-100"
      }`}
    >
      <div className="flex flex-col items-center space-y-4">
        <div className="animate-pulse flex items-center justify-center min-h-[64px]">
          <img
            src="/logo.png"
            alt="TrendHub Logo"
            className="h-16 sm:h-20 object-contain"
            onError={(e) => {
              (e.target as HTMLElement).style.display = "none";
            }}
          />
        </div>
      </div>
    </div>
  );
}
