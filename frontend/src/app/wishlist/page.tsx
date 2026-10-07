"use client";

import { useWishlistStore } from "@/store/wishlistStore";
import { useCartStore } from "@/store/cartStore";
import ProductCard from "@/components/ProductCard";
import Link from "next/link";
import { Heart } from "lucide-react";

export default function Wishlist() {
  const { items, clearWishlist } = useWishlistStore();

  return (
    <div className="max-w-[1400px] mx-auto px-6 pt-8 pb-20 sm:pt-12 sm:pb-28 space-y-8 select-none">
      {/* Title */}
      <div className="space-y-2 border-b border-gray-100 pb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="font-serif text-3xl sm:text-4xl uppercase tracking-widest font-light">WISHLIST</h1>
          <p className="text-xs text-gray-400 uppercase tracking-widest font-sans">
            Your saved luxury products
          </p>
        </div>
        {items.length > 0 && (
          <button
            onClick={clearWishlist}
            className="text-xs font-sans uppercase tracking-widest font-bold text-gray-400 hover:text-black transition-colors"
          >
            Clear All
          </button>
        )}
      </div>

      {/* Grid */}
      {items.length === 0 ? (
        <div className="h-96 flex flex-col items-center justify-center text-center space-y-4">
          <Heart size={48} className="text-gray-300 stroke-[1]" />
          <p className="font-serif text-lg text-gray-500 italic">Your wishlist is empty</p>
          <div className="pt-2">
            <Link
              href="/shop"
              className="bg-black text-white px-8 py-3 text-xs uppercase tracking-widest font-sans hover:bg-neutral-800 transition-colors font-bold"
            >
              Start Exploring
            </Link>
          </div>
        </div>
      ) : (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {items.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      )}
    </div>
  );
}
