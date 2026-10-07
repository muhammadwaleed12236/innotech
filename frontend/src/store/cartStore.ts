import { create } from "zustand";
import { persist } from "zustand/middleware";
import { CartItem, Product } from "@/types";

interface CartState {
  items: CartItem[];
  addItem: (product: Product, quantity?: number, size?: string, color?: string) => void;
  removeItem: (productId: number, size?: string, color?: string) => void;
  updateQuantity: (productId: number, quantity: number, size?: string, color?: string) => void;
  clearCart: () => void;
  getTotalItems: () => number;
  getTotalPrice: () => number; // Subtotal
  
  // Coupon Support
  appliedCoupon: { code: string; type: string; value: number; discountAmount: number } | null;
  applyCoupon: (couponData: { code: string; type: string; value: number }) => void;
  removeCoupon: () => void;
  getDiscountAmount: () => number;
  getFinalTotal: () => number;
}

export const useCartStore = create<CartState>()(
  persist(
    (set, get) => ({
      items: [],
      appliedCoupon: null,

      addItem: (product, quantity = 1, size, color) => {
        set((state) => {
          const existingItemIndex = state.items.findIndex(
            (item) =>
              item.product.id === product.id &&
              item.selectedSize === size &&
              item.selectedColor === color
          );

          const maxStock = product.total_stock !== undefined ? product.total_stock : Infinity;

          if (existingItemIndex > -1) {
            const updatedItems = [...state.items];
            const newQty = Math.min(updatedItems[existingItemIndex].quantity + quantity, maxStock);
            updatedItems[existingItemIndex].quantity = newQty;
            return { items: updatedItems };
          }

          const safeQty = Math.min(quantity, maxStock);
          return {
            items: [...state.items, { product, quantity: safeQty, selectedSize: size, selectedColor: color }],
          };
        });
      },

      removeItem: (productId, size, color) => {
        set((state) => ({
          items: state.items.filter(
            (item) =>
              !(
                item.product.id === productId &&
                item.selectedSize === size &&
                item.selectedColor === color
              )
          ),
        }));
      },

      updateQuantity: (productId, quantity, size, color) => {
        if (quantity <= 0) {
          get().removeItem(productId, size, color);
          return;
        }

        set((state) => ({
          items: state.items.map((item) => {
            if (
              item.product.id === productId &&
              item.selectedSize === size &&
              item.selectedColor === color
            ) {
              const maxStock = item.product.total_stock !== undefined ? item.product.total_stock : Infinity;
              const safeQty = Math.min(quantity, maxStock);
              return { ...item, quantity: safeQty };
            }
            return item;
          }),
        }));
      },

      clearCart: () => set({ items: [] }),

      getTotalItems: () => {
        return get().items.reduce((total, item) => total + item.quantity, 0);
      },

      getTotalPrice: () => {
        return get().items.reduce(
          (total, item) => total + item.product.final_price * item.quantity,
          0
        );
      },

      applyCoupon: (couponData) => {
        set(() => {
          return { appliedCoupon: { ...couponData, discountAmount: 0 } };
        });
      },

      removeCoupon: () => {
        set({ appliedCoupon: null });
      },

      getDiscountAmount: () => {
        const state = get();
        if (!state.appliedCoupon) return 0;
        
        const subtotal = state.getTotalPrice();
        if (state.appliedCoupon.type === "percent") {
          return (subtotal * state.appliedCoupon.value) / 100;
        } else {
          return state.appliedCoupon.value;
        }
      },

      getFinalTotal: () => {
        const state = get();
        const subtotal = state.getTotalPrice();
        const discount = state.getDiscountAmount();
        return Math.max(0, subtotal - discount);
      },
    }),
    {
      name: "trendhub-cart", // LocalStorage Key
    }
  )
);
