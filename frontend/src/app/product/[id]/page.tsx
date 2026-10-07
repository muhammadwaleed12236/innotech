"use client";

import { useState, use, useEffect, useRef } from "react";
import { useQuery } from "@tanstack/react-query";
import api from "@/lib/api";
import { Product } from "@/types";
import { useCartStore } from "@/store/cartStore";
import { useWishlistStore } from "@/store/wishlistStore";
import ProductCard from "@/components/ProductCard";
import Link from "next/link";
import { Heart, Plus, Minus, Info, Loader2, Star } from "lucide-react";
import { getProductFallbackImage } from "@/lib/imageHelper";

interface ProductPageProps {
  params: Promise<{ id: string }>;
}

const COLOR_HEX_MAP: Record<string, string> = {
  "white": "#ffffff",
  "black": "#111111",
  "navy blue": "#0f2042",
  "khaki": "#c3b091",
  "crimson red": "#990000",
  "tan leather": "#b68a55",
  "beige": "#f5f5dc",
  "oatmeal": "#eae6df",
  "black leather": "#252525",
  "royal blue": "#4169e1",
  "navy / grey": "#1d293f",
  "sun yellow": "#ffd700",
  "blush pink": "#ffb6c1",
  "off-white": "#fcf8f2",
  "wash indigo": "#4b6f96"
};

export default function ProductDetail({ params }: ProductPageProps) {
  // Resolve params using React.use() wrapper for Next.js App Router rules
  const resolvedParams = use(params);
  const productId = resolvedParams.id;

  const [activeImage, setActiveImage] = useState("");
  const [quantity, setQuantity] = useState(1);
  const [selectedSize, setSelectedSize] = useState("");
  const [selectedColor, setSelectedColor] = useState("");

  const mainImageRef = useRef<HTMLImageElement>(null);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (mainImageRef.current) {
      const { left, top, width, height } = e.currentTarget.getBoundingClientRect();
      const x = ((e.clientX - left) / width) * 100;
      const y = ((e.clientY - top) / height) * 100;
      mainImageRef.current.style.transformOrigin = `${x}% ${y}%`;
      mainImageRef.current.style.transform = "scale(1.8)";
    }
  };

  const handleMouseLeave = () => {
    if (mainImageRef.current) {
      mainImageRef.current.style.transformOrigin = "center center";
      mainImageRef.current.style.transform = "scale(1)";
    }
  };

  const addItem = useCartStore((state) => state.addItem);
  const toggleWishlist = useWishlistStore((state) => state.toggleWishlist);
  const isInWishlist = useWishlistStore((state) => state.isInWishlist(parseInt(productId)));

  const assetUrl = process.env.NEXT_PUBLIC_ASSET_URL || "http://127.0.0.1:8000";

  // 1. Fetch Product details
  const { data: product, isLoading } = useQuery<Product>({
    queryKey: ["product-detail", productId],
    queryFn: async () => {
      const res = await api.get(`/products/${productId}`);
      return res.data?.data;
    },
  });

  // 2. Fetch Related Products
  const { data: relatedProducts } = useQuery<Product[]>({
    queryKey: ["related-products", product?.category_id],
    enabled: !!product?.category_id,
    queryFn: async () => {
      const res = await api.get(`/products?category_id=${product?.category_id}`);
      return (res.data?.data?.data as Product[] || []).filter(p => p.id !== parseInt(productId));
    },
  });

  // Pre-calculate image list (guarded against loading state)
  const defaultMain = product && product.web_main_image
    ? `${assetUrl}/uploads/products/${product.web_main_image}`
    : product && product.image
    ? `${assetUrl}/uploads/products/${product.image}`
    : product
    ? getProductFallbackImage(product.id)
    : "";

  const allImages = [defaultMain];
  if (product && product.web_images) {
    product.web_images.forEach(img => {
      allImages.push(`${assetUrl}/uploads/products/${img.image_path}`);
    });
  }

  const currentImage = activeImage || defaultMain;

  // 3. Parse Sizes and Colors from product.color JSON
  let availableSizes: string[] = [];
  let availableColors: string[] = [];
  let variants: any[] = [];

  if (product && product.color) {
    try {
      const parsed = JSON.parse(product.color);
      if (Array.isArray(parsed) && parsed.length > 0) {
        if (typeof parsed[0] === "string") {
          availableColors = parsed;
        } else if (typeof parsed[0] === "object") {
          variants = parsed;
          const sizeSet = new Set<string>();
          const colorSet = new Set<string>();
          parsed.forEach((v: any) => {
            if (v.size && v.size !== "-") sizeSet.add(v.size);
            if (v.color && v.color !== "-") colorSet.add(v.color);
          });
          availableSizes = Array.from(sizeSet);
          availableColors = Array.from(colorSet);
        }
      }
    } catch (e) {
      console.error("Failed to parse product colors/variants", e);
    }
  }

  // Check if a specific size is out of stock for the selected color
  const isSizeOutOfStock = (size: string) => {
    if (variants.length === 0) return false;
    
    // If color is selected, check stock for this specific size-color combination
    if (selectedColor) {
      const variant = variants.find(v => v.size === size && v.color === selectedColor);
      return !variant || variant.stock <= 0;
    }
    
    // If no color is selected yet, check if all color variants of this size are out of stock
    const matchingVariants = variants.filter(v => v.size === size);
    if (matchingVariants.length === 0) return true;
    return matchingVariants.every(v => v.stock <= 0);
  };

  // Check if a specific color is out of stock for the selected size
  const isColorOutOfStock = (color: string) => {
    if (variants.length === 0) return false;
    
    // If size is selected, check stock for this specific size-color combination
    if (selectedSize) {
      const variant = variants.find(v => v.size === selectedSize && v.color === color);
      return !variant || variant.stock <= 0;
    }
    
    // If no size is selected yet, check if all size variants of this color are out of stock
    const matchingVariants = variants.filter(v => v.color === color);
    if (matchingVariants.length === 0) return true;
    return matchingVariants.every(v => v.stock <= 0);
  };

  // Find current active variant stock
  const getActiveVariantStock = () => {
    if (variants.length === 0) return product ? (product.total_stock ?? 0) : 0;
    if (selectedSize && selectedColor) {
      const v = variants.find(val => val.size === selectedSize && val.color === selectedColor);
      return v ? v.stock : 0;
    }
    return product ? (product.total_stock ?? 0) : 0;
  };

  const activeStock = getActiveVariantStock();

  // Reset active image when product changes
  useEffect(() => {
    if (product) {
      setActiveImage("");
    }
  }, [product]);

  // Auto-adjust selected size/color based on stock availability when selections change
  useEffect(() => {
    if (variants.length > 0 && selectedColor && selectedSize) {
      const currentVariant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
      if (!currentVariant || currentVariant.stock <= 0) {
        const availableInStockSize = availableSizes.find(sz => !isSizeOutOfStock(sz));
        if (availableInStockSize) {
          setSelectedSize(availableInStockSize);
        }
      }
    }
  }, [selectedColor, variants]);

  useEffect(() => {
    if (variants.length > 0 && selectedSize && selectedColor) {
      const currentVariant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
      if (!currentVariant || currentVariant.stock <= 0) {
        const availableInStockColor = availableColors.find(col => !isColorOutOfStock(col));
        if (availableInStockColor) {
          setSelectedColor(availableInStockColor);
        }
      }
    }
  }, [selectedSize, variants]);

  // Pre-select first options if available and nothing is selected
  // Ensure we select options that are actually IN STOCK if possible
  useEffect(() => {
    if (availableSizes.length > 0 && !selectedSize) {
      const inStockSize = availableSizes.find(sz => !isSizeOutOfStock(sz));
      setSelectedSize(inStockSize || availableSizes[0]);
    }
    if (availableColors.length > 0 && !selectedColor) {
      const inStockColor = availableColors.find(col => !isColorOutOfStock(col));
      setSelectedColor(inStockColor || availableColors[0]);
    }
  }, [availableSizes, availableColors, selectedSize, selectedColor]);

  // Calculate discount percentage
  const discountPercent = product && product.sale_price_per_piece && product.web_sale_price
    ? Math.round(((product.sale_price_per_piece - product.web_sale_price) / product.sale_price_per_piece) * 100)
    : 0;

  if (isLoading) {
    return (
      <div className="h-screen flex items-center justify-center">
        <Loader2 className="animate-spin text-gray-300" size={32} />
      </div>
    );
  }

  if (!product) {
    return (
      <div className="h-screen flex flex-col items-center justify-center text-center space-y-4">
        <h3 className="font-serif text-xl italic text-gray-500">Product not found</h3>
      </div>
    );
  }

  return (
    <div className="max-w-[1300px] mx-auto px-4 sm:px-6 pt-6 pb-20 sm:pt-8 sm:pb-28 select-none">
      
      {/* Breadcrumbs */}
      <div className="flex items-center gap-1.5 text-[10px] sm:text-xs text-gray-400 tracking-wider font-sans uppercase">
        <Link href="/" className="hover:text-black transition-colors">Home</Link>
        <span>&gt;</span>
        <span className="hover:text-black transition-colors">Categories</span>
        <span>&gt;</span>
        <span className="hover:text-black transition-colors">{product.category_relation?.name || "Apparel"}</span>
        <span>&gt;</span>
        <span className="text-gray-800 font-medium truncate max-w-[200px]">{product.item_name}</span>
      </div>

      {/* Main product showcase grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start mt-4 sm:mt-6">
        
        {/* Left Side: Product Gallery (lg:col-span-6) */}
        <div className="lg:col-span-6 flex flex-col-reverse md:flex-row gap-4">
          
          {/* Thumbnails - Left Side on Desktop, Bottom on Mobile */}
          {allImages.length > 1 && (
            <div className="flex md:flex-col gap-3 overflow-x-auto md:overflow-y-auto pb-2 md:pb-0 md:pr-1 max-h-[500px] shrink-0 scrollbar-thin">
              {allImages.map((imgUrl, index) => (
                <button
                  key={index}
                  onClick={() => setActiveImage(imgUrl)}
                  className={`w-16 h-20 sm:w-20 sm:h-26 relative overflow-hidden bg-gray-50 border rounded-xl transition-all duration-200 cursor-pointer shrink-0 ${
                    currentImage === imgUrl ? "border-black scale-95 shadow-sm" : "border-gray-200 hover:border-gray-400"
                  }`}
                >
                  <img src={imgUrl} alt="thumbnail" className="w-full h-full object-cover" />
                </button>
              ))}
            </div>
          )}

          {/* Main Large Image - Right Side on Desktop, Top on Mobile */}
          <div 
            onMouseMove={handleMouseMove}
            onMouseLeave={handleMouseLeave}
            className="relative flex-1 aspect-[3/4] overflow-hidden bg-gray-50 border border-gray-100 rounded-2xl shadow-sm group cursor-zoom-in"
          >
            {/* Promo Tag */}
            {product.promo_tag && (
              <div className="absolute top-4 left-4 z-10">
                <span className="bg-black text-white text-[10px] px-3 py-1 font-sans font-bold uppercase rounded-full shadow-sm tracking-wider">
                  {product.promo_tag}
                </span>
              </div>
            )}

            {/* Focus / Fullscreen icon */}
            <div className="absolute top-4 right-4 z-10">
              <div className="w-8 h-8 rounded-full bg-white/90 backdrop-blur-[1px] text-neutral-800 flex items-center justify-center cursor-pointer hover:bg-white hover:text-black transition-colors shadow-sm">
                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
              </div>
            </div>

            <img
              ref={mainImageRef}
              src={currentImage}
              alt={product.item_name}
              className="w-full h-full object-cover transition-transform duration-200 ease-out"
            />

            {/* Chevrons Navigation inside main image */}
            {allImages.length > 1 && (
              <>
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const currentIndex = allImages.indexOf(currentImage);
                    const prevIndex = (currentIndex - 1 + allImages.length) % allImages.length;
                    setActiveImage(allImages[prevIndex]);
                  }}
                  className="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-black flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 translate-x-[-10px] group-hover:translate-x-0 cursor-pointer"
                >
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const currentIndex = allImages.indexOf(currentImage);
                    const nextIndex = (currentIndex + 1) % allImages.length;
                    setActiveImage(allImages[nextIndex]);
                  }}
                  className="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-black flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 translate-x-[10px] group-hover:translate-x-0 cursor-pointer"
                >
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </>
            )}
          </div>
        </div>

        {/* Right Side: Product Details info (lg:col-span-6) */}
        <div className="lg:col-span-6 space-y-6 sm:space-y-8 font-sans">
          
          {/* Header Info */}
          <div className="space-y-2">
            <span className="text-[10px] uppercase tracking-[0.25em] text-gray-400 font-bold block">
              {product.category_relation?.name || "Apparel"}
            </span>
            <h1 className="font-serif text-2xl sm:text-3xl lg:text-4xl tracking-wide uppercase font-light text-black leading-tight">
              {product.item_name}
            </h1>
            

            
            <p className="text-[10px] text-gray-400 font-mono tracking-wider pt-1">SKU: {product.item_code}</p>
          </div>

          {/* Pricing Section */}
          <div className="border-t border-b border-gray-100 py-4 flex flex-wrap items-baseline gap-3 font-sans">
            <span className="text-2xl font-bold text-black">
              Rs. {product.final_price.toLocaleString()}
            </span>
            {discountPercent > 0 && (
              <>
                <span className="text-base text-gray-400 line-through font-light">
                  Rs. {product.sale_price_per_piece?.toLocaleString()}
                </span>
                <span className="text-[10px] font-bold text-white bg-[#7a0f12] px-2.5 py-1 rounded uppercase tracking-wider">
                  {discountPercent}% Discount
                </span>
              </>
            )}
          </div>

          {/* Short Description */}
          <p className="text-sm text-neutral-600 leading-relaxed font-sans font-light">
            {product.description || "Designed as part of our premium modern luxury apparel collection, this piece stands out with premium stitching and elegant cuts."}
          </p>

          {/* Variants Selectors Container */}
          <div className="space-y-6">
            {/* Sizes */}
            {availableSizes.length > 0 && (
              <div className="space-y-3">
                <div className="flex justify-between items-center text-xs">
                  <span className="font-bold uppercase tracking-wider text-black">Product Size</span>
                  <span className="text-gray-400 underline cursor-pointer hover:text-black">Size Guide</span>
                </div>
                <div className="flex gap-2.5 flex-wrap">
                  {availableSizes.map((sz) => {
                    const isOutOfStock = isSizeOutOfStock(sz);
                    return (
                      <button
                        key={sz}
                        disabled={isOutOfStock}
                        onClick={() => setSelectedSize(sz)}
                        className={`px-5 py-2.5 text-xs font-semibold tracking-wider transition-all duration-300 uppercase rounded-full border relative overflow-hidden ${
                          selectedSize === sz
                            ? "border-black bg-black text-white cursor-pointer"
                            : isOutOfStock
                            ? "border-gray-300 bg-gray-50 text-gray-400 line-through cursor-not-allowed"
                            : "border-gray-200 text-gray-500 hover:border-black bg-white cursor-pointer"
                        }`}
                      >
                        {sz}
                        {isOutOfStock && (
                          <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div className="w-[140%] h-[2px] bg-neutral-400 rotate-45 transform origin-center" />
                          </div>
                        )}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Colors */}
            {availableColors.length > 0 && (
              <div className="space-y-3">
                <span className="text-xs font-bold uppercase tracking-wider text-black block">
                  Product Color: <span className="font-normal text-gray-400 capitalize">{selectedColor}</span>
                </span>
                <div className="flex gap-2.5 flex-wrap">
                  {availableColors.map((col) => {
                    const isOutOfStock = isColorOutOfStock(col);
                    return (
                      <button
                        key={col}
                        disabled={isOutOfStock}
                        onClick={() => setSelectedColor(col)}
                        className={`px-5 py-2.5 text-xs font-semibold tracking-wider transition-all duration-300 uppercase rounded-full border relative overflow-hidden ${
                          selectedColor === col
                            ? "border-black bg-black text-white cursor-pointer"
                            : isOutOfStock
                            ? "border-gray-300 bg-gray-50 text-gray-400 line-through cursor-not-allowed"
                            : "border-gray-200 text-gray-500 hover:border-black bg-white cursor-pointer"
                        }`}
                      >
                        {col}
                        {isOutOfStock && (
                          <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div className="w-[140%] h-[2px] bg-neutral-400 rotate-45 transform origin-center" />
                          </div>
                        )}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}
            
            {availableSizes.length === 0 && availableColors.length === 0 && (
              <div className="text-sm text-gray-500 italic">No specific size or color variations available.</div>
            )}
          </div>

          {/* Out of Stock Alert Banner */}
          {activeStock <= 0 && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-xs uppercase font-bold tracking-widest text-center rounded-lg">
              This Variant is Out of Stock
            </div>
          )}

          {/* Action Row: Quantity + Add To Cart + Wishlist */}
          <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-center">
            <div className="flex gap-3 w-full sm:w-auto">
              {/* Quantity Selector - Pill Shape */}
              <div className={`flex flex-1 sm:flex-none justify-between items-center border border-gray-200 rounded-full py-1.5 ${
                activeStock <= 0 ? "opacity-40 pointer-events-none" : ""
              }`}>
                <button
                  disabled={activeStock <= 0}
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  className="px-4 py-2 text-gray-400 hover:text-black transition-colors cursor-pointer"
                >
                  <Minus size={12} />
                </button>
                <span className="px-2 text-sm text-black font-bold font-mono min-w-[20px] text-center">
                  {activeStock <= 0 ? 0 : quantity}
                </span>
                <button
                  disabled={activeStock <= 0 || quantity >= activeStock}
                  onClick={() => setQuantity(quantity + 1)}
                  className="px-4 py-2 text-gray-400 hover:text-black transition-colors cursor-pointer disabled:opacity-30"
                >
                  <Plus size={12} />
                </button>
              </div>

              {/* Wishlist Button - Circular (Mobile only) */}
              <button
                onClick={() => toggleWishlist(product)}
                className="flex sm:hidden border border-gray-200 p-4 rounded-full hover:border-black text-gray-400 hover:text-black transition-colors cursor-pointer items-center justify-center shadow-sm"
              >
                <Heart size={18} fill={isInWishlist ? "#ff3b30" : "none"} stroke={isInWishlist ? "#ff3b30" : "currentColor"} />
              </button>
            </div>

            {/* Add to Cart button - Pill Shape */}
            <button
              disabled={activeStock <= 0}
              onClick={() => {
                if (activeStock <= 0) return;
                addItem(product, quantity, selectedSize, selectedColor);
                window.dispatchEvent(new CustomEvent("open-cart"));
              }}
              className={`w-full sm:flex-1 text-center py-4 text-xs font-sans uppercase tracking-[0.2em] font-semibold transition-all duration-300 rounded-full cursor-pointer shadow-md hover:shadow-lg ${
                activeStock <= 0
                  ? "bg-gray-200 text-gray-400 cursor-not-allowed shadow-none"
                  : "bg-black text-white hover:bg-neutral-800"
              }`}
            >
              {activeStock <= 0 ? "Out of Stock" : "Add to Cart"}
            </button>

            {/* Wishlist Button - Circular (Desktop only) */}
            <button
              onClick={() => toggleWishlist(product)}
              className="hidden sm:flex border border-gray-200 p-4 rounded-full hover:border-black text-gray-400 hover:text-black transition-colors cursor-pointer items-center justify-center shadow-sm hover:shadow-md"
            >
              <Heart size={18} fill={isInWishlist ? "#ff3b30" : "none"} stroke={isInWishlist ? "#ff3b30" : "currentColor"} />
            </button>
          </div>

          {/* Accordion / Informative cards */}
          <div className="space-y-4 pt-6 border-t border-gray-100">
            <div className="space-y-1">
              <h4 className="text-xs font-bold uppercase tracking-wider text-black flex items-center gap-1.5">
                <Info size={14} /> Description & Care
              </h4>
              <p className="text-xs text-gray-500 leading-relaxed font-sans font-light">
                {product.description || "Designed as part of our premium modern luxury apparel collection, this piece stands out with premium stitching and elegant cuts. Hand wash or machine wash cold on delicate cycle."}
              </p>
            </div>
            
            <div className="space-y-1">
              <h4 className="text-xs font-bold uppercase tracking-wider text-black">Delivery & Shipping</h4>
              <p className="text-xs text-gray-500 leading-relaxed font-sans font-light">
                Complimentary shipping on orders over Rs. 10,000. Delivered in premium signature boxes. Typically ships within 2-4 business days.
              </p>
            </div>

            <div className="space-y-1">
              <h4 className="text-xs font-bold uppercase tracking-wider text-black">Return Policy</h4>
              <p className="text-xs text-gray-500 leading-relaxed font-sans font-light">
                We accept returns of unworn items within 14 days of receipt. Sale and promotional products are eligible for size exchanges only.
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Related Products Grid */}
      {relatedProducts && relatedProducts.length > 0 && (
        <div className="space-y-12 mt-16 sm:mt-24">
          <div className="border-b border-gray-100 pb-4 text-center sm:text-left">
            <h2 className="font-serif text-2xl tracking-widest uppercase font-light">Related Products</h2>
            <p className="text-[10px] text-gray-400 uppercase tracking-widest font-sans">Complete the look</p>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {relatedProducts.slice(0, 4).map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
