import { useQuery } from "@tanstack/react-query";
import api from "@/lib/api";

export interface WebsiteSettings {
  web_site_name?: string;
  web_contact_email?: string;
  web_contact_phone?: string;
  web_facebook_link?: string;
  web_instagram_link?: string;
  web_tiktok_link?: string;
  web_whatsapp_number?: string;
  web_shipping_policy?: string;
  web_return_policy?: string;
  web_about_us?: string;
  web_home_banner_text?: string;
  web_home_banner_image?: string;
  web_site_logo?: string;
  web_home_hero_video?: string;
  web_home_hero_image?: string;
  web_home_hero_media_type?: string;
  web_easypaisa_account_title?: string;
  web_easypaisa_mobile_number?: string;
  web_easypaisa_qr_code?: string;
  web_store_locator_banner_image?: string;
  web_store_locator_locations?: string;
  web_store_locator_map_iframe?: string;
}

export const useSettings = () => {
  return useQuery({
    queryKey: ["websiteSettings", "v2"],
    queryFn: async (): Promise<WebsiteSettings> => {
      try {
        const response = await api.get("/settings");
        // The API returns { status: 'success', data: [...] }
        const dataPayload = response.data?.data || response.data;
        
        let settings: any = {};
        if (Array.isArray(dataPayload)) {
          dataPayload.forEach((item: any) => {
            if (item.key) {
              // Remove 'web_' prefix if needed, but our interface expects 'web_site_logo' etc.
              settings[item.key as keyof WebsiteSettings] = item.value;
            }
          });
        } else if (typeof dataPayload === 'object' && dataPayload !== null) {
          settings = dataPayload;
        }
        return settings;
      } catch (error) {
        console.error("Failed to fetch website settings", error);
        return {};
      }
    },
    staleTime: 1000 * 60 * 60, // 1 hour
    gcTime: 1000 * 60 * 60 * 24, // 24 hours
  });
};
