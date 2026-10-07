export interface Category {
  id: number;
  name: string;
  web_image_url?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface WebImage {
  id: number;
  product_id: number;
  image_path: string;
}

export interface Product {
  id: number;
  item_name: string;
  item_code: string; // SKU
  category_id?: number;
  category_relation?: Category;
  sale_price_per_piece?: number;
  web_sale_price?: number | null;
  final_price: number;
  is_web_visible: number;
  show_on_homepage: number;
  auto_hide_out_of_stock: number;
  promo_tag?: string | null;
  meta_title?: string | null;
  meta_description?: string | null;
  image?: string | null; // Default POS Image
  web_main_image?: string | null; // Primary Web Image
  web_images?: WebImage[];
  description?: string | null;
  total_stock?: number;
  color?: string | null;
}

export interface CartItem {
  product: Product;
  quantity: number;
  selectedSize?: string;
  selectedColor?: string;
}
