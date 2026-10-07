"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { useSettings } from "@/hooks/useSettings";
import { Loader2, MapPin, Phone, ArrowLeft } from "lucide-react";

interface StoreLocation {
  name: string;
  address: string;
  phone?: string;
  map_link?: string;
}

export default function StoreLocatorPage() {
  const { data: settings, isLoading } = useSettings();
  const [locations, setLocations] = useState<StoreLocation[]>([]);
  const [activeMapUrl, setActiveMapUrl] = useState<string>("");

  useEffect(() => {
    if (settings) {
      // Load locations
      let parsedLocations: StoreLocation[] = [];
      try {
        parsedLocations = JSON.parse(settings.web_store_locator_locations || "[]");
      } catch (e) {
        parsedLocations = [];
      }
      setLocations(parsedLocations);

      // Load default map URL
      if (settings.web_store_locator_map_iframe) {
        setActiveMapUrl(settings.web_store_locator_map_iframe);
      } else if (parsedLocations.length > 0) {
        // Fallback: Use the first location's address
        const firstAddr = parsedLocations[0].address;
        setActiveMapUrl(`https://maps.google.com/maps?q=${encodeURIComponent(firstAddr)}&t=&z=15&ie=UTF8&iwloc=&output=embed`);
      } else {
        // Ultimate fallback
        setActiveMapUrl("https://maps.google.com/maps?q=Pakistan&t=&z=5&ie=UTF8&iwloc=&output=embed");
      }
    }
  }, [settings]);

  const handleViewOnMap = (loc: StoreLocation) => {
    if (loc.map_link && loc.map_link.includes("embed")) {
      setActiveMapUrl(loc.map_link);
    } else {
      // Generate standard embed URL from address
      setActiveMapUrl(`https://maps.google.com/maps?q=${encodeURIComponent(loc.address)}&t=&z=16&ie=UTF8&iwloc=&output=embed`);
    }
  };

  if (isLoading) {
    return (
      <div className="h-screen flex items-center justify-center bg-white">
        <Loader2 className="animate-spin text-gray-300" size={32} />
      </div>
    );
  }

  const bannerImg = settings?.web_store_locator_banner_image
    ? `${process.env.NEXT_PUBLIC_ASSET_URL || "http://127.0.0.1:8000"}/${settings.web_store_locator_banner_image}`
    : "https://images.unsplash.com/photo-1582037917273-10250df7a230?q=80&w=1600";

  return (
    <div className="min-h-screen bg-white pb-20">
      
      {/* 1. STORE LOCATOR HEADER BANNER */}
      <section className="relative w-full h-[220px] sm:h-[300px] overflow-hidden select-none bg-neutral-900">
        <img
          src={bannerImg}
          alt="Store Locator Header"
          className="w-full h-full object-cover opacity-60 filter brightness-[0.9] contrast-[0.95]"
        />
        <div className="absolute inset-0 flex flex-col items-center justify-center p-6 text-center space-y-2">
          <Link href="/" className="absolute top-6 left-6 text-white/80 hover:text-white flex items-center gap-1.5 text-[10px] uppercase tracking-widest font-sans font-bold transition-colors">
            <ArrowLeft size={12} /> Back to Home
          </Link>
          <h1 className="text-white text-3xl sm:text-5xl uppercase tracking-[0.2em] font-sans font-extrabold drop-shadow-sm">
            STORE LOCATOR
          </h1>
          <p className="text-white text-xs sm:text-sm uppercase tracking-widest font-sans font-light drop-shadow-sm max-w-[450px]">
            Your favourites, now just a visit away!
          </p>
        </div>
      </section>

      {/* 2. MAP & STORES GRID */}
      <div className="max-w-[1400px] mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
          
          {/* Left Column: Stores List */}
          <div className="lg:col-span-5 flex flex-col h-[320px] sm:h-[400px] lg:h-[650px] border border-gray-100 rounded-[2px] overflow-hidden shadow-sm">
            <div className="bg-neutral-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
              <h2 className="font-sans text-xs uppercase tracking-widest font-bold text-neutral-800">
                Locations
              </h2>
              <span className="text-[10px] bg-neutral-200 text-neutral-700 px-2 py-0.5 rounded-full font-bold font-sans">
                {locations.length} {locations.length === 1 ? "Store" : "Stores"}
              </span>
            </div>

            <div className="flex-1 overflow-y-auto divide-y divide-gray-100 scrollbar-thin">
              {locations.length === 0 ? (
                <div className="p-8 text-center text-gray-400 font-sans text-xs uppercase tracking-widest py-20">
                  No store locations added yet.
                </div>
              ) : (
                locations.map((loc, idx) => (
                  <div key={idx} className="p-6 hover:bg-neutral-50 transition-colors space-y-4">
                    <div className="space-y-2">
                      <h3 className="font-serif text-sm uppercase tracking-wider font-bold text-neutral-900 flex items-start gap-2">
                        <MapPin size={16} className="text-neutral-500 shrink-0 mt-0.5" />
                        {loc.name || "TrendHub Store"}
                      </h3>
                      <p className="text-[11px] text-gray-500 font-sans leading-relaxed uppercase tracking-wider pl-6">
                        {loc.address}
                      </p>
                      {loc.phone && (
                        <p className="text-[11px] text-gray-400 font-sans flex items-center gap-2 pl-6">
                          <Phone size={12} className="shrink-0" />
                          {loc.phone}
                        </p>
                      )}
                    </div>

                    <div className="pl-6">
                      <button
                        onClick={() => handleViewOnMap(loc)}
                        className="text-[9px] font-sans font-bold uppercase tracking-widest text-neutral-800 border-b border-neutral-800 hover:text-black hover:border-black transition-all cursor-pointer pb-0.5"
                      >
                        VIEW ON MAP
                      </button>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>

          {/* Right Column: Interactive Google Map */}
          <div className="lg:col-span-7 border border-gray-100 rounded-[2px] overflow-hidden shadow-sm bg-gray-50 relative h-[350px] sm:h-[450px] lg:h-auto lg:min-h-[650px]">
            {activeMapUrl ? (
              <iframe
                src={activeMapUrl}
                className="w-full h-full border-0 absolute inset-0"
                allowFullScreen
                loading="lazy"
                title="Store Locations Map"
              ></iframe>
            ) : (
              <div className="absolute inset-0 flex items-center justify-center text-center p-6">
                <Loader2 className="animate-spin text-gray-300" size={24} />
              </div>
            )}
          </div>

        </div>
      </div>

    </div>
  );
}
