"use client";

import { useCartStore } from "@/store/cartStore";
import { X, Plus, Minus, ShoppingBag } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";
import Image from "next/image";
import { getProductFallbackImage } from "@/lib/imageHelper";

interface CartSidebarProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function CartSidebar({ isOpen, onClose }: CartSidebarProps) {
  const { items, updateQuantity, removeItem, getTotalPrice } = useCartStore();
  const assetUrl = process.env.NEXT_PUBLIC_ASSET_URL || "http://127.0.0.1:8000";

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          {/* Overlay */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 0.4 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black z-50 cursor-pointer"
          />

          {/* Cart Drawer */}
          <motion.div
            initial={{ x: "100%" }}
            animate={{ x: 0 }}
            exit={{ x: "100%" }}
            transition={{ type: "tween", duration: 0.35, ease: "easeInOut" }}
            className="fixed right-0 top-0 bottom-0 w-full max-w-[450px] bg-white z-50 flex flex-col shadow-2xl"
          >
            {/* Header */}
            <div className="p-6 border-b border-gray-100 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <ShoppingBag size={20} className="text-black" />
                <h3 className="font-serif text-lg font-medium uppercase tracking-wider text-black">Shopping Bag</h3>
                <span className="text-xs text-gray-500 font-sans">({items.length} items)</span>
              </div>
              <button
                onClick={onClose}
                className="p-1 hover:bg-gray-50 rounded-full transition-colors text-gray-500 hover:text-black"
              >
                <X size={20} />
              </button>
            </div>

            {/* Item List */}
            <div className="flex-1 overflow-y-auto p-6 space-y-6">
              {items.length === 0 ? (
                <div className="h-full flex flex-col items-center justify-center text-center space-y-4">
                  <ShoppingBag size={48} className="text-gray-300 stroke-[1]" />
                  <p className="font-serif text-lg text-gray-500 italic">Your cart is empty</p>
                  <button
                    onClick={onClose}
                    className="bg-black text-white px-6 py-2.5 text-xs font-sans uppercase tracking-widest hover:bg-neutral-800 transition-colors"
                  >
                    Start Shopping
                  </button>
                </div>
              ) : (
                items.map((item, idx) => {
                  const product = item.product;
                  const imageSrc = product.web_main_image
                    ? `${assetUrl}/uploads/products/${product.web_main_image}`
                    : product.image
                    ? `${assetUrl}/uploads/products/${product.image}`
                    : getProductFallbackImage(product.id);

                  return (
                    <div key={idx} className="flex gap-4 border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                      <div className="w-20 h-24 bg-gray-50 relative overflow-hidden flex-shrink-0">
                        <img
                          src={imageSrc}
                          alt={product.item_name}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      <div className="flex-1 flex flex-col justify-between py-1">
                        <div>
                          <div className="flex justify-between gap-2">
                            <h4 className="text-[13px] font-medium tracking-wide text-black line-clamp-2">
                              {product.item_name}
                            </h4>
                            <span className="text-[13px] font-semibold text-black">
                              Rs. {product.final_price.toLocaleString()}
                            </span>
                          </div>
                          <p className="text-[11px] text-gray-400 font-mono mt-1">SKU: {product.item_code}</p>
                          {(item.selectedSize || item.selectedColor) && (
                            <div className="flex gap-3 mt-1.5 text-[11px] text-gray-500 font-sans">
                              {item.selectedSize && <span>Size: {item.selectedSize}</span>}
                              {item.selectedColor && <span>Color: {item.selectedColor}</span>}
                            </div>
                          )}
                        </div>

                        <div className="flex items-center justify-between mt-3">
                          {/* Quantity Selector */}
                          <div className="flex items-center border border-gray-200">
                            <button
                              onClick={() =>
                                updateQuantity(
                                  product.id,
                                  item.quantity - 1,
                                  item.selectedSize,
                                  item.selectedColor
                                )
                              }
                              className="px-2 py-1 text-gray-400 hover:text-black transition-colors"
                            >
                              <Minus size={12} />
                            </button>
                            <span className="px-3 text-xs text-black font-semibold">{item.quantity}</span>
                            <button
                              onClick={() =>
                                updateQuantity(
                                  product.id,
                                  item.quantity + 1,
                                  item.selectedSize,
                                  item.selectedColor
                                )
                              }
                              className="px-2 py-1 text-gray-400 hover:text-black transition-colors"
                            >
                              <Plus size={12} />
                            </button>
                          </div>

                          {/* Remove button */}
                          <button
                            onClick={() =>
                              removeItem(product.id, item.selectedSize, item.selectedColor)
                            }
                            className="text-[11px] font-sans text-gray-400 hover:text-black uppercase tracking-wider transition-colors"
                          >
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  );
                })
              )}
            </div>

            {/* Footer Summary */}
            {items.length > 0 && (
              <div className="p-6 border-t border-gray-100 space-y-4 bg-gray-50">
                <div className="flex justify-between items-center text-sm">
                  <span className="text-gray-500 font-sans uppercase tracking-wider text-xs">Estimated Total</span>
                  <span className="font-semibold text-lg text-black">Rs. {getTotalPrice().toLocaleString()}</span>
                </div>
                <p className="text-[11px] text-gray-400 text-center font-sans">
                  Shipping, taxes, and discounts calculated at checkout.
                </p>
                <div className="space-y-2">
                  <Link
                    href="/checkout"
                    onClick={onClose}
                    className="w-full block bg-black text-white text-center py-3.5 text-xs font-sans uppercase tracking-widest hover:bg-neutral-800 transition-colors font-semibold"
                  >
                    Proceed To Checkout
                  </Link>
                  <button
                    onClick={onClose}
                    className="w-full bg-transparent border border-black text-black py-3.5 text-xs font-sans uppercase tracking-widest hover:bg-black hover:text-white transition-all duration-300 font-semibold"
                  >
                    Continue Shopping
                  </button>
                </div>
              </div>
            )}
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
}
