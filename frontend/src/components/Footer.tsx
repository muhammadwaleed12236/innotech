"use client";

import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { useState } from "react";
import { useSettings } from "@/hooks/useSettings";

export default function Footer() {
  const [email, setEmail] = useState("");
  const { data: settings } = useSettings();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (email.trim()) {
      alert("Thank you for subscribing to our newsletter.");
      setEmail("");
    }
  };

  return (
    <footer className="w-full bg-[#fafafa] border-t border-gray-100 text-[#111111] pt-16 pb-12 font-sans select-none">
      <div className="max-w-[1400px] mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        
        {/* About column */}
        <div className="space-y-4 lg:col-span-2 pr-6">
          {settings?.web_site_logo ? (
            <img 
              src={`http://127.0.0.1:8000/${settings.web_site_logo}`} 
              alt={settings?.web_site_name || "Logo"} 
              className="h-8 sm:h-10 object-contain"
            />
          ) : (
            <h4 className="font-serif text-xl tracking-[0.2em] font-bold uppercase">{settings?.web_site_name || "TRENDHUB"}</h4>
          )}
          
          <p className="text-[13px] text-gray-500 font-sans leading-relaxed max-w-[320px] break-words">
            {settings?.web_about_us || "TrendHub is a premium luxury clothing brand dedicated to minimal aesthetics, timeless elegance, and high-quality sustainable apparel. Designed for the modern tastemaker."}
          </p>
          <div className="flex gap-4 pt-2">
            {settings?.web_instagram_link && (
              <a href={settings.web_instagram_link} target="_blank" rel="noreferrer" className="p-2 border border-gray-200 hover:border-black rounded-full transition-colors text-gray-600 hover:text-black">
                {/* Instagram SVG */}
                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                  <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"></path>
                  <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                </svg>
              </a>
            )}
            {settings?.web_facebook_link && (
              <a href={settings.web_facebook_link} target="_blank" rel="noreferrer" className="p-2 border border-gray-200 hover:border-black rounded-full transition-colors text-gray-600 hover:text-black">
                {/* Facebook SVG */}
                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                  <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                </svg>
              </a>
            )}
            {settings?.web_tiktok_link && (
              <a href={settings.web_tiktok_link} target="_blank" rel="noreferrer" className="p-2 border border-gray-200 hover:border-black rounded-full transition-colors text-gray-600 hover:text-black">
                {/* TikTok SVG (using generic user icon as placeholder) */}
                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                  <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                </svg>
              </a>
            )}
            {settings?.web_whatsapp_number && (
              <a href={`https://wa.me/${settings.web_whatsapp_number.replace(/\D/g, '')}`} target="_blank" rel="noreferrer" className="p-2 border border-gray-200 hover:border-black rounded-full transition-colors text-gray-600 hover:text-black">
                {/* WhatsApp SVG placeholder */}
                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
              </a>
            )}
          </div>
        </div>

        {/* Explore Links */}
        <div className="space-y-4">
          <h5 className="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Explore</h5>
          <ul className="space-y-2.5 text-[13px]">
            <li>
              <Link href="/" className="text-gray-600 hover:text-black transition-colors">Home</Link>
            </li>
            <li>
              <Link href="/shop" className="text-gray-600 hover:text-black transition-colors">Shop</Link>
            </li>
            <li>
              <Link href="/store-locator" className="text-gray-600 hover:text-black transition-colors">Store Locator</Link>
            </li>
            <li>
              <Link href="/wishlist" className="text-gray-600 hover:text-black transition-colors">Wishlist</Link>
            </li>
          </ul>
        </div>

        {/* Newsletter */}
        <div className="space-y-4">
          <h5 className="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Newsletter</h5>
          <p className="text-[13px] text-gray-500 leading-relaxed">
            Subscribe to receive updates on collections, events, and exclusive offers.
          </p>
          <form onSubmit={handleSubmit} className="flex border-b border-black py-1">
            <input
              type="email"
              placeholder="ENTER YOUR EMAIL"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="bg-transparent text-[11px] uppercase tracking-wider focus:outline-none w-full text-black placeholder-gray-400"
              required
            />
            <button type="submit" className="text-black hover:opacity-50 transition-opacity">
              <ArrowRight size={16} />
            </button>
          </form>
        </div>
      </div>

      <div className="max-w-[1400px] mx-auto px-6 border-t border-gray-100 mt-16 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
        <span className="text-xs text-gray-400 font-sans">
          &copy; {new Date().getFullYear()} TrendHub Brand. All Rights Reserved.
        </span>
        <span className="text-xs text-gray-400 font-sans tracking-wider uppercase">
          Designed with pure sophistication.
        </span>
      </div>
    </footer>
  );
}
