"use client";

import { useSettings } from "@/hooks/useSettings";

export default function AnnouncementBar() {
  const { data: settings } = useSettings();
  const text = settings?.web_home_banner_text || "Free worldwide shipping on orders over Rs. 10,000 | Use Code: TRENDNEW";

  return (
    <div className="w-full bg-white text-black border-b border-gray-100 text-[11px] uppercase tracking-[0.2em] py-2.5 text-center font-sans select-none">
      <span dangerouslySetInnerHTML={{ __html: text }} />
    </div>
  );
}
