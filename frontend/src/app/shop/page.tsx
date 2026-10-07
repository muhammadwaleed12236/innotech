"use client";

import { useState, useEffect, Suspense } from "react";
import { useQuery } from "@tanstack/react-query";
import { useSearchParams, useRouter } from "next/navigation";
import api from "@/lib/api";
import { Product, Category } from "@/types";
import ProductCard from "@/components/ProductCard";
import { SlidersHorizontal, ChevronDown, Loader2 } from "lucide-react";

function ShopCatalog() {
  const searchParams = useSearchParams();
  const router = useRouter();

  // Filter States
  const [selectedCategory, setSelectedCategory] = useState<string>(searchParams.get("category_id") || "");
  const [selectedTag, setSelectedTag] = useState<string>(searchParams.get("promo_tag") || "");
  const [searchQuery, setSearchQuery] = useState<string>(searchParams.get("search") || "");
  const [selectedSize, setSelectedSize] = useState<string>(searchParams.get("size") || "");
  const [selectedColor, setSelectedColor] = useState<string>(searchParams.get("color") || "");
  const [sortBy, setSortBy] = useState<string>("newest");
  const [isFilterOpen, setIsFilterOpen] = useState(false);

  // Sync state with URL search params changes
  useEffect(() => {
    setSelectedCategory(searchParams.get("category_id") || "");
    setSelectedTag(searchParams.get("promo_tag") || "");
    setSearchQuery(searchParams.get("search") || "");
    setSelectedSize(searchParams.get("size") || "");
    setSelectedColor(searchParams.get("color") || "");
  }, [searchParams]);

  // Fetch Categories
  const { data: categories } = useQuery({
    queryKey: ["shop-categories"],
    queryFn: async () => {
      const res = await api.get("/categories");
      return res.data?.data as Category[] || [];
    },
  });

  // Fetch Products based on filters
  const { data: productsData, isLoading: loadingProducts } = useQuery({
    queryKey: ["shop-products", selectedCategory, selectedTag, searchQuery, selectedSize, selectedColor],
    queryFn: async () => {
      const params = new URLSearchParams();
      if (selectedCategory) params.append("category_id", selectedCategory);
      if (selectedTag) params.append("promo_tag", selectedTag);
      if (searchQuery) params.append("search", searchQuery);
      if (selectedSize) params.append("size", selectedSize);
      if (selectedColor) params.append("color", selectedColor);

      const res = await api.get(`/products?${params.toString()}`);
      return res.data?.data?.data as Product[] || [];
    },
  });

  // Client Side Sorting
  const sortedProducts = (() => {
    if (!productsData) return [];
    let items = [...productsData];
    if (sortBy === "price-low") {
      items.sort((a, b) => a.final_price - b.final_price);
    } else if (sortBy === "price-high") {
      items.sort((a, b) => b.final_price - a.final_price);
    } else {
      // Default / Newest (sort by id desc)
      items.sort((a, b) => b.id - a.id);
    }
    return items;
  })();

  const clearFilters = () => {
    setSelectedCategory("");
    setSelectedTag("");
    setSearchQuery("");
    setSelectedSize("");
    setSelectedColor("");
    router.push("/shop");
  };

  return (
    <div className="max-w-[1400px] mx-auto px-6 pt-8 pb-20 sm:pt-12 sm:pb-28 space-y-8 select-none">
      
      {/* Title / Banner */}
      <div className="space-y-2 border-b border-gray-100 pb-6">
        <h1 className="font-serif text-3xl sm:text-4xl uppercase tracking-widest font-light">COLLECTIONS</h1>
        <p className="text-xs text-gray-400 uppercase tracking-widest font-sans">
          {searchQuery ? `Search Results for "${searchQuery}"` : "Explore curated modern luxury wardrobe staples"}
        </p>
      </div>

      {/* Filter and Sort bar */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 py-4 border-b border-gray-100 font-sans">
        <div className="flex items-center gap-4">
          <button
            onClick={() => setIsFilterOpen(!isFilterOpen)}
            className="flex items-center gap-2 border border-black px-5 py-2.5 text-[11px] uppercase tracking-widest font-semibold hover:bg-black hover:text-white transition-all duration-300 cursor-pointer"
          >
            <SlidersHorizontal size={14} />
            Filters {isFilterOpen ? "Close" : "Open"}
          </button>
          {(selectedCategory || selectedTag || searchQuery || selectedSize || selectedColor) && (
            <button
              onClick={clearFilters}
              className="text-[11px] uppercase tracking-widest font-bold text-gray-400 hover:text-black transition-colors"
            >
              Clear All ({[selectedCategory, selectedTag, searchQuery, selectedSize, selectedColor].filter(Boolean).length})
            </button>
          )}
        </div>

        <div className="flex items-center gap-2 self-end sm:self-auto">
          <span className="text-[11px] uppercase tracking-wider text-gray-400">Sort By:</span>
          <div className="relative">
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="appearance-none bg-transparent border-b border-black pr-8 pl-1 py-1 text-xs uppercase tracking-wider focus:outline-none cursor-pointer text-black font-semibold"
            >
              <option value="newest">Newest</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
            </select>
            <ChevronDown size={12} className="absolute right-0.5 top-1/2 -translate-y-1/2 pointer-events-none text-black" />
          </div>
        </div>
      </div>

      {/* Main Grid area with filters sidebar */}
      <div className="flex flex-col lg:flex-row gap-10">
        
        {/* Sidebar Filters */}
        {isFilterOpen && (
          <aside className="w-full lg:w-64 flex-shrink-0 space-y-8 animate-fadeIn font-sans border-b lg:border-b-0 lg:border-r border-gray-100 pb-6 lg:pb-0 pr-0 lg:pr-8">
            {/* Category Filter */}
            <div className="space-y-3">
              <h4 className="text-[11px] font-bold uppercase tracking-widest text-black">Categories</h4>
              <div className="flex flex-col gap-2.5 text-xs text-gray-500">
                <button
                  onClick={() => setSelectedCategory("")}
                  className={`text-left hover:text-black transition-colors uppercase tracking-wider ${
                    selectedCategory === "" ? "text-black font-bold" : ""
                  }`}
                >
                  All Categories
                </button>
                {categories?.map((cat) => (
                  <button
                    key={cat.id}
                    onClick={() => setSelectedCategory(cat.id.toString())}
                    className={`text-left hover:text-black transition-colors uppercase tracking-wider ${
                      selectedCategory === cat.id.toString() ? "text-black font-bold" : ""
                    }`}
                  >
                    {cat.name}
                  </button>
                ))}
              </div>
            </div>

            {/* Tag Filter */}
            <div className="space-y-3">
              <h4 className="text-[11px] font-bold uppercase tracking-widest text-black">Collections</h4>
              <div className="flex flex-col gap-2.5 text-xs text-gray-500">
                {["Featured", "New Arrival", "Trending", "Best Seller", "Flash Sale"].map((tag) => (
                  <button
                    key={tag}
                    onClick={() => setSelectedTag(tag === selectedTag ? "" : tag)}
                    className={`text-left hover:text-black transition-colors uppercase tracking-wider ${
                      selectedTag === tag ? "text-black font-bold" : ""
                    }`}
                  >
                    {tag}
                  </button>
                ))}
              </div>
            </div>

            {/* Size Filter */}
            <div className="space-y-3 border-t border-gray-100 pt-6">
              <h4 className="text-[11px] font-bold uppercase tracking-widest text-black">Sizes</h4>
              <div className="flex flex-wrap gap-2">
                {["S", "M", "L", "XL", "XXL"].map((size) => (
                  <button
                    key={size}
                    onClick={() => setSelectedSize(selectedSize === size ? "" : size)}
                    className={`w-9 h-9 flex items-center justify-center border text-[11px] font-bold uppercase transition-all duration-200 cursor-pointer ${
                      selectedSize === size
                        ? "border-black bg-black text-white"
                        : "border-gray-200 text-gray-500 hover:border-black hover:text-black"
                    }`}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>

            {/* Color Filter */}
            <div className="space-y-3 border-t border-gray-100 pt-6">
              <h4 className="text-[11px] font-bold uppercase tracking-widest text-black">Colors</h4>
              <div className="grid grid-cols-4 gap-x-2 gap-y-3.5">
                {[
                  { name: "Black", code: "#000000" },
                  { name: "White", code: "#ffffff", border: true },
                  { name: "Grey", code: "#808080" },
                  { name: "Blue", code: "#1e40af" },
                  { name: "Red", code: "#dc2626" },
                  { name: "Green", code: "#16a34a" },
                  { name: "Brown", code: "#78350f" },
                  { name: "Pink", code: "#db2777" },
                  { name: "Yellow", code: "#eab308" },
                  { name: "Beige", code: "#f5f5dc", border: true }
                ].map((color) => (
                  <button
                    key={color.name}
                    onClick={() => setSelectedColor(selectedColor === color.name ? "" : color.name)}
                    className="group flex flex-col items-center gap-1 cursor-pointer focus:outline-none"
                    title={color.name}
                  >
                    <div
                      className={`w-7 h-7 rounded-full transition-transform duration-200 relative flex items-center justify-center ${
                        color.border ? "border border-gray-300" : ""
                      } ${
                        selectedColor === color.name
                          ? "scale-110 ring-2 ring-offset-2 ring-black"
                          : "group-hover:scale-105"
                      }`}
                      style={{ backgroundColor: color.code }}
                    >
                      {selectedColor === color.name && (
                        <span
                          className={`text-[10px] font-bold ${
                            color.name === "White" || color.name === "Beige" || color.name === "Yellow"
                              ? "text-black"
                              : "text-white"
                          }`}
                        >
                          ✓
                        </span>
                      )}
                    </div>
                    <span className={`text-[9px] uppercase tracking-wider text-gray-400 group-hover:text-black transition-colors ${
                      selectedColor === color.name ? "text-black font-semibold" : ""
                    }`}>
                      {color.name}
                    </span>
                  </button>
                ))}
              </div>
            </div>
          </aside>
        )}

        {/* Product Grid Area */}
        <div className="flex-1">
          {loadingProducts ? (
            <div className="h-96 flex items-center justify-center">
              <Loader2 className="animate-spin text-gray-300" size={32} />
            </div>
          ) : sortedProducts.length === 0 ? (
            <div className="h-96 flex flex-col items-center justify-center text-center space-y-4">
              <h3 className="font-serif text-lg text-gray-500 italic">No products found</h3>
              <p className="text-xs text-gray-400 font-sans">Try expanding your filters or search criteria.</p>
              <button
                onClick={clearFilters}
                className="bg-black text-white px-6 py-2.5 text-xs uppercase tracking-widest font-sans hover:bg-neutral-800 transition-colors font-bold"
              >
                Reset Shop
              </button>
            </div>
          ) : (
            <div className="grid grid-cols-2 md:grid-cols-4 gap-5">
              {sortedProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default function Shop() {
  return (
    <Suspense fallback={
      <div className="h-screen flex items-center justify-center">
        <Loader2 className="animate-spin text-gray-300" size={32} />
      </div>
    }>
      <ShopCatalog />
    </Suspense>
  );
}
