"use client";

import { useState, useEffect, useRef } from "react";
import { Search, Heart, User, ShoppingBag, Menu, X } from "lucide-react";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { usePathname } from "next/navigation";
import { useCartStore } from "@/store/cartStore";
import { useWishlistStore } from "@/store/wishlistStore";
import { useAuthStore } from "@/store/authStore";
import CartSidebar from "./CartSidebar";
import AnnouncementBar from "./AnnouncementBar";
import { useSettings } from "@/hooks/useSettings";
import { Product, Category } from "@/types";
import api from "@/lib/api";
import { getProductFallbackImage } from "@/lib/imageHelper";
import { useQuery } from "@tanstack/react-query";

export default function Header() {
  const pathname = usePathname();
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState<Product[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [isMounted, setIsMounted] = useState(false);
  const [isVisible, setIsVisible] = useState(true);
  const lastScrollY = useRef(0);

  const cartItemsCount = useCartStore((state) => state.getTotalItems());
  const wishlistItemsCount = useWishlistStore((state) => state.items.length);
  const user = useAuthStore((state) => state.user);
  const checkAuth = useAuthStore((state) => state.checkAuth);

  const { data: settings } = useSettings();

  const { data: categories } = useQuery({
    queryKey: ["header-categories"],
    queryFn: async () => {
      const res = await api.get("/categories");
      return res.data?.data as Category[] || [];
    },
  });

  useEffect(() => {
    setIsMounted(true);
    checkAuth();
  }, []);

  // Debounced live product search
  useEffect(() => {
    if (searchQuery.trim() === "") {
      setSearchResults([]);
      setSuggestions([]);
      return;
    }

    setIsSearching(true);
    const delayDebounce = setTimeout(async () => {
      try {
        const res = await api.get(`/products?search=${encodeURIComponent(searchQuery)}`);
        const fetchedProducts = res.data?.data?.data as Product[] || [];
        setSearchResults(fetchedProducts);

        // Predefined keywords to filter suggestions
        const allPossibleSuggestions = [
          "Formal Shirts",
          "Polo Shirts",
          "Denim Jackets",
          "Trousers",
          "Knit Wool Sweater",
          "Oxford Shoes",
          "Utility Jacket",
          "Evening Gown"
        ];
        const filtered = allPossibleSuggestions.filter(item => 
          item.toLowerCase().includes(searchQuery.toLowerCase())
        );
        setSuggestions(filtered);
      } catch (error) {
        console.error("Live search error:", error);
      } finally {
        setIsSearching(false);
      }
    }, 300);

    return () => clearTimeout(delayDebounce);
  }, [searchQuery]);

  // Scroll handler for transparent/white header logic + auto-hide and open-cart event
  useEffect(() => {
    const handleScroll = () => {
      const currentScrollY = window.scrollY;

      // Handle transparent vs solid background logic
      if (currentScrollY > 50) {
        setIsScrolled(true);
      } else {
        setIsScrolled(false);
      }

      // Handle show/hide header on scroll logic
      if (currentScrollY > lastScrollY.current && currentScrollY > 100) {
        // Scrolling down and past threshold -> Hide header
        setIsVisible(false);
      } else {
        // Scrolling up or near top -> Show header
        setIsVisible(true);
      }

      lastScrollY.current = currentScrollY;
    };

    const handleOpenCart = () => {
      setIsCartOpen(true);
    };

    window.addEventListener("scroll", handleScroll);
    window.addEventListener("open-cart", handleOpenCart);

    return () => {
      window.removeEventListener("scroll", handleScroll);
      window.removeEventListener("open-cart", handleOpenCart);
    };
  }, []);

  const isHome = pathname === "/";

  return (
    <>
      <header className={`w-full z-40 ${isHome && !isScrolled ? "absolute top-0 left-0 bg-transparent" : "relative"}`}>
        {/* <AnnouncementBar /> */}

        {/* Main Navbar */}
        <nav
          className={`w-full transition-all duration-300 transform ${
            isVisible ? "translate-y-0" : "-translate-y-full"
          } ${
            isHome
              ? isScrolled
                ? "fixed top-0 bg-white/95 backdrop-blur-md shadow-sm text-black border-b border-gray-100"
                : "absolute bg-transparent text-white border-transparent"
              : "sticky top-0 bg-white/95 backdrop-blur-md shadow-sm text-black border-b border-gray-100"
          }`}
        >
          <div className="w-full mx-auto px-4 sm:px-8 h-12 sm:h-14 flex items-center justify-between">
            {/* Left: Hamburger menu + Menu text */}
            <div className="flex items-center">
              <button
                onClick={() => setIsMobileMenuOpen(true)}
                className="flex items-center gap-1.5 cursor-pointer focus:outline-none transition-opacity hover:opacity-80"
                aria-label="Open menu"
              >
                <Menu size={18} strokeWidth={1.5} />
                <span className="text-[10px] uppercase tracking-[0.2em] font-sans font-semibold hidden sm:inline">Menu</span>
              </button>
            </div>

            {/* Center: Brand Logo */}
            <div className="absolute left-1/2 -translate-x-1/2 z-10 max-w-[50%] sm:max-w-[60%] flex items-center justify-center">
              <Link
                href="/"
                className="font-sans text-[11px] min-[375px]:text-sm sm:text-2xl tracking-[0.2em] sm:tracking-[0.35em] font-medium text-inherit uppercase flex items-center justify-center hover:opacity-85 transition-all duration-300"
              >
                {settings?.web_site_logo ? (
                  <img
                    src={`http://127.0.0.1:8000/${settings.web_site_logo}`}
                    alt={settings?.web_site_name || "TrendHub"}
                    className={`h-7 sm:h-12 w-auto max-w-[130px] min-[375px]:max-w-[160px] sm:max-w-[240px] object-contain transition-all duration-300 ${
                      isHome && !isScrolled ? "invert" : ""
                    }`}
                  />
                ) : (
                  settings?.web_site_name || "TrendHub"
                )}
              </Link>
            </div>

            {/* Right: Actions */}
            <div className="flex items-center gap-1 sm:gap-2">
              {/* Search Icon */}
              <button
                onClick={() => setIsSearchOpen(true)}
                className="p-1.5 hover:opacity-80 transition-opacity cursor-pointer"
                aria-label="Search products"
              >
                <Search size={18} strokeWidth={1.5} />
              </button>

              {/* User Account Icon */}
              <Link
                href={user ? "/dashboard" : "/login"}
                className="p-1.5 hover:opacity-80 transition-opacity hidden sm:inline-block"
                aria-label="User account"
              >
                <User size={18} strokeWidth={1.5} />
              </Link>

              {/* Wishlist Icon */}
              <Link
                href="/wishlist"
                className="p-1.5 hover:opacity-80 transition-opacity relative hidden sm:inline-block"
                aria-label="Wishlist"
              >
                <Heart size={18} strokeWidth={1.5} />
                {isMounted && wishlistItemsCount > 0 && (
                  <span className={`absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[8px] font-sans font-bold shadow-sm transition-all duration-300 ${
                    isHome && !isScrolled
                      ? "bg-white text-black"
                      : "bg-black text-white"
                  }`}>
                    {wishlistItemsCount}
                  </span>
                )}
              </Link>

              {/* Cart Icon */}
              <button
                onClick={() => setIsCartOpen(true)}
                className="p-1.5 hover:opacity-80 transition-opacity relative cursor-pointer"
                aria-label="Open cart"
              >
                <ShoppingBag size={18} strokeWidth={1.5} />
                {isMounted && cartItemsCount > 0 && (
                  <span className={`absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[8px] font-sans font-bold shadow-sm transition-all duration-300 ${
                    isHome && !isScrolled
                      ? "bg-white text-black"
                      : "bg-black text-white"
                  }`}>
                    {cartItemsCount}
                  </span>
                )}
              </button>
            </div>
          </div>
        </nav>

        {/* Animated Drawer Menu */}
        <AnimatePresence>
          {isMobileMenuOpen && (
            <>
              {/* Overlay */}
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 0.4 }}
                exit={{ opacity: 0 }}
                onClick={() => setIsMobileMenuOpen(false)}
                className="fixed inset-0 bg-black z-50 cursor-pointer"
              />

              {/* Drawer Content */}
              <motion.div
                initial={{ x: "-100%" }}
                animate={{ x: 0 }}
                exit={{ x: "-100%" }}
                transition={{ type: "tween", duration: 0.35, ease: "easeInOut" }}
                className="fixed left-0 top-0 bottom-0 w-full max-w-[360px] sm:max-w-[400px] bg-white z-50 flex flex-col shadow-2xl text-black border-r border-gray-100"
              >
                {/* Header */}
                <div className="p-6 border-b border-gray-100 flex items-center justify-between">
                  <span className="font-sans text-sm font-bold uppercase tracking-[0.2em] text-black">Menu</span>
                  <button
                    onClick={() => setIsMobileMenuOpen(false)}
                    className="p-1.5 hover:bg-gray-50 rounded-full transition-colors text-gray-500 hover:text-black cursor-pointer"
                  >
                    <X size={20} />
                  </button>
                </div>

                {/* Links */}
                <div className="flex-1 overflow-y-auto px-8 py-10 space-y-8">
                  {/* Main Sections */}
                  {categories && categories.length > 0 && (
                    <div className="flex flex-col">
                      <span className="font-sans text-xs font-bold uppercase tracking-[0.2em] text-black mb-4 block">
                        Shop by Category
                      </span>
                      <div className="flex flex-col space-y-4">
                        {categories.map((cat) => (
                          <Link
                            key={cat.id}
                            href={`/shop?category_id=${cat.id}`}
                            onClick={() => setIsMobileMenuOpen(false)}
                            className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block hover:translate-x-1.5 transition-transform duration-200"
                          >
                            {cat.name}
                          </Link>
                        ))}
                      </div>
                      <div className="border-t border-gray-100 my-4" />
                    </div>
                  )}

                  {/* Collections */}
                  <div className="flex flex-col space-y-4">
                    <Link
                      href="/shop"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Shop All
                    </Link>
                    <Link
                      href="/shop?promo_tag=New Arrival"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      New Arrivals
                    </Link>
                    <Link
                      href="/shop?promo_tag=Trending"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Trending
                    </Link>
                    <Link
                      href="/shop?promo_tag=Flash Sale"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Sale
                    </Link>
                  </div>

                  <div className="border-t border-gray-100 my-4" />

                  {/* User Actions */}
                  <div className="flex flex-col space-y-4">
                    <Link
                      href="/wishlist"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors flex items-center justify-between"
                    >
                      <span>Wishlist</span>
                      {isMounted && wishlistItemsCount > 0 && (
                        <span className="bg-black text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                          {wishlistItemsCount}
                        </span>
                      )}
                    </Link>
                    <Link
                      href={user ? "/dashboard" : "/login"}
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      {user ? "My Account" : "Login / Register"}
                    </Link>
                  </div>
                </div>
              </motion.div>
            </>
          )}
        </AnimatePresence>

        {/* Search Overlay */}
        {isSearchOpen && (
          <div className="fixed inset-0 bg-white z-50 flex flex-col p-6 overflow-y-auto">
            <div className="max-w-[1200px] mx-auto w-full mt-10 flex flex-col flex-1 pb-10">
              {/* Search Header */}
              <div className="flex justify-between items-center border-b border-black pb-4">
                <div className="flex items-center gap-3 flex-1">
                  <Search size={24} className="text-black" />
                  <input
                    type="text"
                    placeholder="SEARCH FOR LUXURY APPAREL..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    onKeyDown={(e) => {
                      if (e.key === "Enter" && searchQuery.trim() !== "") {
                        setIsSearchOpen(false);
                        window.location.href = `/shop?search=${encodeURIComponent(searchQuery)}`;
                      }
                    }}
                    className="w-full bg-transparent text-xl font-serif tracking-widest focus:outline-none placeholder-gray-300 text-black uppercase"
                    autoFocus
                  />
                </div>
                <button onClick={() => {
                  setIsSearchOpen(false);
                  setSearchQuery("");
                }} className="p-2 border border-gray-100 hover:border-black rounded-full transition-colors">
                  <X size={24} />
                </button>
              </div>

              {/* Dynamic Content Area */}
              {searchQuery.trim() === "" ? (
                /* Popular Searches (Empty state) */
                <div className="mt-10">
                  <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-4">Popular Searches</h5>
                  <div className="flex flex-wrap gap-3">
                    {["Formal Shirts", "Polo Shirts", "Denim Jackets", "Trousers"].map((term) => (
                      <Link
                        key={term}
                        href={`/shop?search=${encodeURIComponent(term)}`}
                        onClick={() => setIsSearchOpen(false)}
                        className="border border-gray-200 hover:border-black px-5 py-2.5 text-xs font-sans uppercase tracking-wider transition-colors text-black rounded-full"
                      >
                        {term}
                      </Link>
                    ))}
                  </div>
                </div>
              ) : (
                /* Active Search State */
                <div className="flex flex-col flex-1 mt-8 space-y-8">
                  {/* Suggestions Row */}
                  {suggestions.length > 0 && (
                    <div className="border-b border-gray-100 pb-6">
                      <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-4">Suggestions</h5>
                      <div className="flex flex-wrap gap-3">
                        {suggestions.map((sug) => {
                          // Highlight query inside suggestion
                          const parts = sug.split(new RegExp(`(${searchQuery})`, 'gi'));
                          return (
                            <Link
                              key={sug}
                              href={`/shop?search=${encodeURIComponent(sug)}`}
                              onClick={() => setIsSearchOpen(false)}
                              className="bg-gray-50 border border-gray-100 hover:border-black rounded-full px-4 py-2 text-xs font-sans uppercase tracking-wider flex items-center gap-2 transition-colors text-black"
                            >
                              <Search size={12} className="text-gray-400" />
                              <span>
                                {parts.map((part, i) => 
                                  part.toLowerCase() === searchQuery.toLowerCase() ? (
                                    <mark key={i} className="bg-yellow-100 text-black px-0.5 rounded font-bold">{part}</mark>
                                  ) : (
                                    <span key={i}>{part}</span>
                                  )
                                )}
                              </span>
                            </Link>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {/* Product Results */}
                  <div className="flex-1">
                    <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-6">
                      Product Results {isSearching ? "" : `(${searchResults.length})`}
                    </h5>

                    {isSearching ? (
                      <div className="flex flex-col items-center justify-center py-20 space-y-4">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-black"></div>
                        <span className="text-xs text-gray-400 font-sans tracking-widest uppercase">Searching products...</span>
                      </div>
                    ) : searchResults.length > 0 ? (
                      <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-6">
                        {searchResults.map((product) => {
                          const pMainImage = product.web_main_image
                            ? `http://127.0.0.1:8000/uploads/products/${product.web_main_image}`
                            : product.image
                            ? `http://127.0.0.1:8000/uploads/products/${product.image}`
                            : getProductFallbackImage(product.id);
                            
                          const price = product.web_sale_price || product.sale_price_per_piece;

                          return (
                            <Link
                              key={product.id}
                              href={`/product/${product.id}`}
                              onClick={() => {
                                setIsSearchOpen(false);
                                setSearchQuery("");
                              }}
                              className="group flex flex-col space-y-3 block"
                            >
                              <div className="aspect-[3/4] w-full bg-gray-50 overflow-hidden relative border border-gray-100 rounded-md">
                                <img
                                  src={pMainImage}
                                  alt={product.item_name}
                                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                              </div>
                              <div className="space-y-1">
                                <h4 className="text-[11px] font-sans font-bold uppercase tracking-wider text-black line-clamp-2 group-hover:underline leading-relaxed">
                                  {product.item_name}
                                </h4>
                                <p className="text-[10px] text-gray-500 font-sans font-bold">
                                  Rs. {Number(price).toLocaleString()}
                                </p>
                              </div>
                            </Link>
                          );
                        })}
                      </div>
                    ) : (
                      <div className="flex flex-col items-center justify-center py-20 text-center space-y-2">
                        <span className="text-sm text-gray-400 font-serif italic">No luxury apparel found matching "{searchQuery}"</span>
                        <span className="text-xs text-gray-300 font-sans tracking-wider uppercase">Try checking spelling or try other keywords</span>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>
        )}
      </header>

      {/* Cart Sidebar Drawer */}
      <CartSidebar isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} />
    </>
  );
}
