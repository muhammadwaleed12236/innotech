"use client";

import { useState, useEffect } from "react";
import { Product } from "@/types";
import { Heart, ShoppingBag, Eye, X, ChevronLeft, ChevronRight, ArrowRight, Plus, Minus } from "lucide-react";
import { useWishlistStore } from "@/store/wishlistStore";
import { useCartStore } from "@/store/cartStore";
import Link from "next/link";
import { getProductFallbackImage } from "@/lib/imageHelper";
import { useRouter } from "next/navigation";

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const [isHovered, setIsHovered] = useState(false);
  const toggleWishlist = useWishlistStore((state) => state.toggleWishlist);
  const isInWishlist = useWishlistStore((state) => state.isInWishlist(product.id));
  const addItem = useCartStore((state) => state.addItem);
  const router = useRouter();

  // Quick View Modal states
  const [isQuickViewOpen, setIsQuickViewOpen] = useState(false);
  const [selectedSize, setSelectedSize] = useState("");
  const [selectedColor, setSelectedColor] = useState("");
  const [quantity, setQuantity] = useState(1);
  const [modalActiveImg, setModalActiveImg] = useState("");

  const assetUrl = process.env.NEXT_PUBLIC_ASSET_URL || "http://127.0.0.1:8000";

  // Compute Primary and Secondary Image urls
  const mainImage = product.web_main_image
    ? `${assetUrl}/uploads/products/${product.web_main_image}`
    : product.image
    ? `${assetUrl}/uploads/products/${product.image}`
    : getProductFallbackImage(product.id);

  // Use first gallery image as secondary/hover image if available
  let hoverImage = mainImage;
  if (product.web_images && product.web_images.length > 0) {
    hoverImage = `${assetUrl}/uploads/products/${product.web_images[0].image_path}`;
  }

  // Pre-calculate image list for Quick View Modal
  const allImages = [mainImage];
  if (product.web_images) {
    product.web_images.forEach(img => {
      allImages.push(`${assetUrl}/uploads/products/${img.image_path}`);
    });
  }

  const currentModalImg = modalActiveImg || mainImage;

  // Calculate discount percentage
  let discountPercent = 0;
  if (product.web_sale_price && product.sale_price_per_piece) {
    const diff = product.sale_price_per_piece - product.web_sale_price;
    discountPercent = Math.round((diff / product.sale_price_per_piece) * 100);
  }

  // Parse Sizes and Colors from product.color JSON
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
    if (selectedColor) {
      const variant = variants.find(v => v.size === size && v.color === selectedColor);
      return !variant || variant.stock <= 0;
    }
    const matchingVariants = variants.filter(v => v.size === size);
    if (matchingVariants.length === 0) return true;
    return matchingVariants.every(v => v.stock <= 0);
  };

  // Check if a specific color is out of stock for the selected size
  const isColorOutOfStock = (color: string) => {
    if (variants.length === 0) return false;
    if (selectedSize) {
      const variant = variants.find(v => v.size === selectedSize && v.color === color);
      return !variant || variant.stock <= 0;
    }
    const matchingVariants = variants.filter(v => v.color === color);
    if (matchingVariants.length === 0) return true;
    return matchingVariants.every(v => v.stock <= 0);
  };

  const getActiveVariantStock = () => {
    if (variants.length === 0) return product.total_stock ?? 0;
    if (selectedSize && selectedColor) {
      const v = variants.find(val => val.size === selectedSize && val.color === selectedColor);
      return v ? v.stock : 0;
    }
    return product.total_stock ?? 0;
  };

  const activeStock = getActiveVariantStock();

  // Prevent background scrolling when Quick View is open
  useEffect(() => {
    if (isQuickViewOpen) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
    };
  }, [isQuickViewOpen]);

  // Auto-adjust size when color changes
  useEffect(() => {
    if (isQuickViewOpen && variants.length > 0 && selectedColor && selectedSize) {
      const currentVariant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
      if (!currentVariant || currentVariant.stock <= 0) {
        const availableInStockSize = availableSizes.find(sz => !isSizeOutOfStock(sz));
        if (availableInStockSize) {
          setSelectedSize(availableInStockSize);
        }
      }
    }
  }, [selectedColor, isQuickViewOpen]);

  // Auto-adjust color when size changes
  useEffect(() => {
    if (isQuickViewOpen && variants.length > 0 && selectedSize && selectedColor) {
      const currentVariant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
      if (!currentVariant || currentVariant.stock <= 0) {
        const availableInStockColor = availableColors.find(col => !isColorOutOfStock(col));
        if (availableInStockColor) {
          setSelectedColor(availableInStockColor);
        }
      }
    }
  }, [selectedSize, isQuickViewOpen]);

  // Initialize size and color selections when modal opens
  useEffect(() => {
    if (isQuickViewOpen) {
      if (availableSizes.length > 0 && !selectedSize) {
        const inStockSize = availableSizes.find(sz => !isSizeOutOfStock(sz));
        setSelectedSize(inStockSize || availableSizes[0]);
      }
      if (availableColors.length > 0 && !selectedColor) {
        const inStockColor = availableColors.find(col => !isColorOutOfStock(col));
        setSelectedColor(inStockColor || availableColors[0]);
      }
    }
  }, [isQuickViewOpen, availableSizes, availableColors]);

  const handleClose = () => {
    setIsQuickViewOpen(false);
    setSelectedSize("");
    setSelectedColor("");
    setQuantity(1);
    setModalActiveImg("");
  };

  const prevImage = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    const currentIndex = allImages.indexOf(currentModalImg);
    const prevIndex = (currentIndex - 1 + allImages.length) % allImages.length;
    setModalActiveImg(allImages[prevIndex]);
  };

  const nextImage = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    const currentIndex = allImages.indexOf(currentModalImg);
    const nextIndex = (currentIndex + 1) % allImages.length;
    setModalActiveImg(allImages[nextIndex]);
  };

  // Helper to determine fabric / variant details overlay
  const getOverlayText = () => {
    if (product.color) {
      try {
        const parsed = JSON.parse(product.color);
        const item = parsed[0];
        if (item) {
          if (typeof item === "object") {
            return item.color || item.name || "Texture";
          }
          return String(item);
        }
      } catch (e) {
        if (typeof product.color === "string") {
          return product.color;
        }
      }
    }

    const nameLower = product.item_name.toLowerCase();
    if (nameLower.includes("polo")) return "Micro Zig Zag";
    if (nameLower.includes("shirt")) return "Seer Sucker Lycra";
    if (nameLower.includes("jacket")) return "Drop Needle Jersey";
    if (nameLower.includes("trouser")) return "Stretch Fit";
    if (nameLower.includes("cargo")) return "Neoprene";
    
    return "Texture";
  };

  return (
    <div
      className="group w-full flex flex-col relative overflow-hidden bg-white select-none"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      {/* Image/Product Container */}
      <div className="relative aspect-[3/4] overflow-hidden bg-gray-50 block">
        {/* Quarter Circle Sale Tag */}
        {discountPercent > 0 && (
          <div className="absolute top-0 left-0 w-9 h-9 bg-[#7a0f12] text-white rounded-br-full z-10 pointer-events-none shadow-sm">
            <span className="absolute top-[11px] left-[5px] text-[9px] font-sans font-bold tracking-tighter leading-none block">
              -{discountPercent}%
            </span>
          </div>
        )}

        {/* Out of Stock Badge */}
        {product.total_stock !== undefined && product.total_stock <= 0 && (
          <div className="absolute top-2.5 right-2.5 z-10 pointer-events-none">
            <span className="bg-[#222222]/95 backdrop-blur-[1px] text-white text-[9px] uppercase tracking-wider px-2 py-1 font-sans font-bold shadow-sm">
              Out of Stock
            </span>
          </div>
        )}

        <Link href={`/product/${product.id}`} className="w-full h-full block">
          <img
            src={isHovered ? hoverImage : mainImage}
            alt={product.item_name}
            loading="lazy"
            decoding="async"
            className={`w-full h-full object-cover transition-all duration-700 ease-out ${
              product.total_stock !== undefined && product.total_stock <= 0 ? "opacity-60 grayscale-[40%]" : ""
            }`}
          />
        </Link>

        {/* Hover Actions Bar */}
        <div className="absolute bottom-3 left-3 right-3 z-10 flex bg-white shadow-md border border-neutral-100 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 ease-out">
          {product.total_stock !== undefined && product.total_stock <= 0 ? (
            <div className="flex-1 bg-neutral-50 text-neutral-400 text-[10px] sm:text-xs font-sans font-bold tracking-wider uppercase flex items-center justify-center py-1.5 px-2 select-none">
              Out of Stock
            </div>
          ) : (
            <button
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();

                let defaultSize = undefined;
                let defaultColor = undefined;

                if (product.color) {
                  try {
                    const variants = JSON.parse(product.color);
                    const baseVariant = variants.find((v: any) => v.is_base_variant === 1) || variants[0];
                    if (baseVariant) {
                      defaultSize = baseVariant.size !== "-" ? baseVariant.size : undefined;
                      defaultColor = baseVariant.color !== "-" ? baseVariant.color : undefined;
                    }
                  } catch (err) {
                    // Fallback
                  }
                }

                addItem(product, 1, defaultSize, defaultColor);
                window.dispatchEvent(new CustomEvent("open-cart"));
              }}
              className="flex-1 bg-white hover:bg-neutral-900 hover:text-white text-neutral-900 text-[10px] sm:text-xs font-sans font-bold tracking-wider uppercase transition-colors duration-300 py-1.5 px-2 flex items-center justify-center cursor-pointer"
            >
              Add to Cart
            </button>
          )}

          <div className="w-[1px] bg-neutral-200" />

          <button
            onClick={(e) => {
              e.preventDefault();
              e.stopPropagation();
              setIsQuickViewOpen(true);
            }}
            className="w-9 hover:bg-neutral-900 hover:text-white text-neutral-900 flex items-center justify-center transition-colors duration-300 cursor-pointer"
          >
            <Eye className="w-4 h-4" />
          </button>
        </div>

        {/* Fabric/Variant Text Overlay on Bottom-Left */}
        <div className="absolute bottom-0 left-0 bg-[#dcdcdc]/70 text-neutral-800 text-[10px] font-sans font-medium py-1 px-3.5 tracking-wide select-none pointer-events-none transition-opacity duration-300 group-hover:opacity-0">
          {getOverlayText()}
        </div>
      </div>

      {/* Product Info */}
      <div className="pt-4 text-center space-y-1.5 px-2">
        <Link
          href={`/product/${product.id}`}
          className="text-xs sm:text-[13px] font-sans uppercase font-semibold text-neutral-800 hover:text-black transition-colors tracking-wide min-h-[32px] sm:min-h-[40px] flex items-center justify-center line-clamp-2"
        >
          {product.item_name}
        </Link>

        {/* Pricing */}
        <div className="flex justify-center items-baseline gap-2 text-[10px] sm:text-xs">
          {discountPercent > 0 && (
            <span className="text-gray-400 line-through font-sans">
              PKR {Math.round(product.sale_price_per_piece || 0).toLocaleString()}
            </span>
          )}
          <span className="text-black font-bold font-sans">
            PKR {Math.round(product.final_price).toLocaleString()}
          </span>
        </div>
      </div>

      {/* Quick View Modal */}
      {isQuickViewOpen && (
        <div 
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
          onClick={handleClose}
        >
          <div 
            className="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-6 p-5 md:p-6 animate-in fade-in zoom-in-95 duration-200 text-left max-h-[95vh] md:max-h-[85vh] overflow-y-auto md:overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Close Button */}
            <button 
              onClick={handleClose}
              className="absolute top-4 right-4 text-gray-400 hover:text-black transition-colors z-20 cursor-pointer p-1.5 rounded-full hover:bg-neutral-100"
            >
              <X size={20} />
            </button>

            {/* Left Column: Gallery */}
            <div className="flex flex-col gap-3">
              <div className="relative w-full aspect-[3/4] md:max-w-[292px] mx-auto overflow-hidden bg-gray-50 border border-gray-100 rounded-xl group select-none animate-fade-in">
                <img 
                  src={currentModalImg} 
                  alt={product.item_name} 
                  className="w-full h-full object-cover" 
                />
                {allImages.length > 1 && (
                  <>
                    <button 
                      onClick={prevImage}
                      className="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-black flex items-center justify-center shadow-md cursor-pointer transition-colors"
                    >
                      <ChevronLeft size={16} />
                    </button>
                    <button 
                      onClick={nextImage}
                      className="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-black flex items-center justify-center shadow-md cursor-pointer transition-colors"
                    >
                      <ChevronRight size={16} />
                    </button>
                  </>
                )}
              </div>

              {/* Thumbnails strip */}
              {allImages.length > 1 && (
                <div className="flex gap-2 overflow-x-auto py-1 scrollbar-none">
                  {allImages.map((imgUrl, idx) => (
                    <button
                      key={idx}
                      onClick={() => setModalActiveImg(imgUrl)}
                      className={`w-10 h-14 border rounded-lg overflow-hidden shrink-0 transition-all cursor-pointer ${
                        currentModalImg === imgUrl ? "border-black scale-95 shadow-sm" : "border-gray-200 hover:border-gray-400"
                      }`}
                    >
                      <img src={imgUrl} alt="thumbnail" className="w-full h-full object-cover" />
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Right Column: Info */}
            <div className="flex flex-col justify-between font-sans h-full">
              <div className="space-y-3">
                {/* Header info */}
                <div>
                  <span className="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold block mb-1">
                    {product.category_relation?.name || "Apparel"}
                  </span>
                  <h2 className="font-serif text-lg sm:text-xl uppercase tracking-wide font-light text-black leading-tight">
                    {product.item_name}
                  </h2>
                  <p className="text-[9px] text-gray-400 font-mono tracking-wider pt-0.5">SKU: {product.item_code}</p>
                </div>

                {/* Pricing */}
                <div className="border-t border-b border-gray-100 py-3 flex items-baseline gap-2.5">
                  <span className="text-lg font-bold text-black">
                    PKR {Math.round(product.final_price).toLocaleString()}
                  </span>
                  {discountPercent > 0 && (
                    <>
                      <span className="text-xs text-gray-400 line-through font-light">
                        PKR {Math.round(product.sale_price_per_piece || 0).toLocaleString()}
                      </span>
                      <span className="text-[9px] font-bold text-white bg-[#7a0f12] px-2 py-0.5 rounded uppercase tracking-wider">
                        {discountPercent}% OFF
                      </span>
                    </>
                  )}
                </div>

                {/* Description */}
                <p className="text-xs text-neutral-500 leading-relaxed font-light line-clamp-2">
                  {product.description || "Designed as part of our premium modern luxury apparel collection, this piece stands out with premium stitching and elegant cuts."}
                </p>

                {/* Sizes */}
                {availableSizes.length > 0 && (
                  <div className="space-y-1.5">
                    <span className="text-[11px] font-bold uppercase tracking-wider text-black block">Product Size: {selectedSize}</span>
                    <div className="flex gap-2 flex-wrap">
                      {availableSizes.map((sz) => {
                        const isOutOfStock = isSizeOutOfStock(sz);
                        return (
                          <button
                            key={sz}
                            disabled={isOutOfStock}
                            onClick={() => setSelectedSize(sz)}
                            className={`px-4 py-2 text-xs font-semibold tracking-wider transition-all duration-300 uppercase rounded-full border relative overflow-hidden ${
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
                  <div className="space-y-1.5">
                    <span className="text-[11px] font-bold uppercase tracking-wider text-black block">
                      Product Color: <span className="font-normal text-gray-400 capitalize">{selectedColor}</span>
                    </span>
                    <div className="flex gap-2 flex-wrap">
                      {availableColors.map((col) => {
                        const isOutOfStock = isColorOutOfStock(col);
                        return (
                          <button
                            key={col}
                            disabled={isOutOfStock}
                            onClick={() => setSelectedColor(col)}
                            className={`px-4 py-2 text-xs font-semibold tracking-wider transition-all duration-300 uppercase rounded-full border relative overflow-hidden ${
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

                {/* Quantity */}
                <div className="space-y-1.5">
                  <span className="text-[11px] font-bold uppercase tracking-wider text-black block">Quantity</span>
                  <div className={`flex items-center border border-gray-200 rounded-full py-0.5 w-max ${
                    activeStock <= 0 ? "opacity-40 pointer-events-none" : ""
                  }`}>
                    <button
                      disabled={activeStock <= 0}
                      onClick={() => setQuantity(Math.max(1, quantity - 1))}
                      className="px-3 py-1 text-gray-400 hover:text-black transition-colors cursor-pointer"
                    >
                      <Minus size={10} />
                    </button>
                    <span className="px-2 text-xs text-black font-bold font-mono min-w-[20px] text-center">
                      {activeStock <= 0 ? 0 : quantity}
                    </span>
                    <button
                      disabled={activeStock <= 0 || quantity >= activeStock}
                      onClick={() => setQuantity(quantity + 1)}
                      className="px-3 py-1 text-gray-400 hover:text-black transition-colors cursor-pointer disabled:opacity-30"
                    >
                      <Plus size={10} />
                    </button>
                  </div>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="pt-4 space-y-2">
                <button
                  disabled={activeStock <= 0}
                  onClick={() => {
                    if (activeStock <= 0) return;
                    addItem(product, quantity, selectedSize, selectedColor);
                    handleClose();
                    window.dispatchEvent(new CustomEvent("open-cart"));
                  }}
                  className="w-full bg-white hover:bg-neutral-900 hover:text-white text-neutral-900 text-xs font-sans font-bold tracking-wider uppercase transition-colors duration-300 py-3 border border-black rounded-lg cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  Add to Cart
                </button>
                <button
                  disabled={activeStock <= 0}
                  onClick={() => {
                    if (activeStock <= 0) return;
                    addItem(product, quantity, selectedSize, selectedColor);
                    handleClose();
                    router.push("/checkout");
                  }}
                  className="w-full bg-black hover:bg-neutral-800 text-white text-xs font-sans font-bold tracking-wider uppercase transition-colors duration-300 py-3 rounded-lg cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  Buy It Now
                </button>
                <Link
                  href={`/product/${product.id}`}
                  onClick={handleClose}
                  className="w-full text-center text-[10px] tracking-widest font-bold uppercase underline flex items-center justify-center gap-1.5 hover:text-neutral-600 transition-colors pt-1"
                >
                  View Full Details <ArrowRight size={12} />
                </Link>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
