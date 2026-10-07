import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import api from '@/lib/api';

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  setAuth: (user: User, token: string) => void;
  logout: () => void;
  checkAuth: () => Promise<void>;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      setAuth: (user, token) => {
        set({ user, token });
        if (typeof window !== 'undefined') {
          localStorage.setItem('auth_token', token);
        }
      },
      logout: () => {
        set({ user: null, token: null });
        if (typeof window !== 'undefined') {
          localStorage.removeItem('auth_token');
        }
      },
      checkAuth: async () => {
        const token = get().token;
        if (!token) return;
        
        try {
          const res = await api.get('/user', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });
          if (res.data.status === 'success') {
            set({ user: res.data.user });
          }
        } catch (error) {
          console.error("Auth check failed", error);
          get().logout();
        }
      }
    }),
    {
      name: 'auth-storage',
    }
  )
);
