"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/authStore";
import api from "@/lib/api";
import { LogOut, Package, User as UserIcon } from "lucide-react";
import Link from "next/link";

interface OrderItem {
  id: number;
  product_name: string;
  price: number;
  quantity: number;
  size: string | null;
  color: string | null;
  total: number;
}

interface Order {
  id: number;
  order_number: string;
  total: number;
  order_status: string;
  payment_status: string;
  payment_method: string;
  created_at: string;
  items: OrderItem[];
}

export default function DashboardPage() {
  const { user, token, logout, checkAuth } = useAuthStore();
  const router = useRouter();
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("orders");

  useEffect(() => {
    if (!token) {
      router.push("/login");
      return;
    }

    const fetchOrders = async () => {
      try {
        const res = await api.get("/customer/orders");
        if (res.data.status === "success") {
          setOrders(res.data.orders);
        }
      } catch (err) {
        console.error("Failed to fetch orders", err);
      } finally {
        setLoading(false);
      }
    };

    fetchOrders();
  }, [token, router]);

  const handleLogout = async () => {
    try {
      await api.post("/logout");
    } catch (e) {}
    logout();
    router.push("/login");
  };

  if (!user || loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin w-8 h-8 border-2 border-black border-t-transparent rounded-full" />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-white">
      <div className="max-w-[1200px] mx-auto px-4 sm:px-6 py-16">
        <div className="mb-10">
          <h1 className="text-3xl font-serif tracking-widest uppercase">
            My Dashboard
          </h1>
          <p className="text-gray-500 font-serif italic mt-2">
            Welcome back, {user.name}
          </p>
        </div>

        <div className="flex flex-col md:flex-row gap-8">
          {/* Sidebar */}
          <div className="w-full md:w-64 space-y-2">
            <button
              onClick={() => setActiveTab("orders")}
              className={`w-full text-left px-4 py-3 flex items-center gap-3 text-sm font-semibold uppercase tracking-wider transition-colors ${
                activeTab === "orders"
                  ? "bg-black text-white"
                  : "bg-gray-50 text-gray-600 hover:bg-gray-100"
              }`}
            >
              <Package size={18} /> My Orders
            </button>
            <button
              onClick={() => setActiveTab("profile")}
              className={`w-full text-left px-4 py-3 flex items-center gap-3 text-sm font-semibold uppercase tracking-wider transition-colors ${
                activeTab === "profile"
                  ? "bg-black text-white"
                  : "bg-gray-50 text-gray-600 hover:bg-gray-100"
              }`}
            >
              <UserIcon size={18} /> Profile
            </button>
            <button
              onClick={handleLogout}
              className="w-full text-left px-4 py-3 flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-red-600 hover:bg-red-50 transition-colors"
            >
              <LogOut size={18} /> Logout
            </button>
          </div>

          {/* Main Content */}
          <div className="flex-1">
            {activeTab === "orders" && (
              <div className="space-y-6">
                <h2 className="text-xl font-bold uppercase tracking-wider mb-6 border-b pb-4">
                  Order History
                </h2>
                {orders.length === 0 ? (
                  <div className="text-center py-12 bg-gray-50 border border-gray-100">
                    <p className="text-gray-500 mb-4">You haven't placed any orders yet.</p>
                    <Link
                      href="/"
                      className="inline-block bg-black text-white px-8 py-3 text-xs font-bold uppercase tracking-widest hover:bg-neutral-800 transition-colors"
                    >
                      Start Shopping
                    </Link>
                  </div>
                ) : (
                  <div className="space-y-6">
                    {orders.map((order) => (
                      <div
                        key={order.id}
                        className="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow transition-shadow duration-200"
                      >
                        <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                          <div>
                            <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                              Order Number
                            </p>
                            <p className="font-mono font-bold text-sm">
                              {order.order_number}
                            </p>
                          </div>
                          <div>
                            <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                              Date Placed
                            </p>
                            <p className="text-sm">
                              {new Date(order.created_at).toLocaleDateString()}
                            </p>
                          </div>
                          <div>
                            <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                              Total
                            </p>
                            <p className="text-sm font-bold">Rs. {order.total}</p>
                          </div>
                          <div>
                            <span
                              className={`inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider ${
                                order.order_status === "pending"
                                  ? "bg-yellow-100 text-yellow-800"
                                  : order.order_status === "completed"
                                  ? "bg-green-100 text-green-800"
                                  : "bg-gray-200 text-gray-800"
                              }`}
                            >
                              {order.order_status}
                            </span>
                          </div>
                        </div>
                        <div className="px-4 py-4 sm:px-6 sm:py-4 space-y-4">
                          {order.items?.map((item) => (
                            <div key={item.id} className="flex justify-between items-center gap-4">
                              <div className="flex-1 min-w-0">
                                <p className="font-semibold text-sm">
                                  {item.product_name}
                                </p>
                                <p className="text-xs text-gray-500 mt-0.5">
                                  Qty: {item.quantity} 
                                  {item.size && ` | Size: ${item.size}`}
                                  {item.color && ` | Color: ${item.color}`}
                                </p>
                              </div>
                              <div className="flex-shrink-0 text-right pl-2">
                                <p className="font-bold text-sm whitespace-nowrap">Rs. {item.total}</p>
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {activeTab === "profile" && (
              <div className="space-y-6">
                <h2 className="text-xl font-bold uppercase tracking-wider mb-6 border-b pb-4">
                  My Details
                </h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                      Name
                    </label>
                    <p className="mt-1">{user.name}</p>
                  </div>
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                      Email
                    </label>
                    <p className="mt-1">{user.email}</p>
                  </div>
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                      Phone
                    </label>
                    <p className="mt-1">{user.phone}</p>
                  </div>
                  <div>
                    <label className="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                      City
                    </label>
                    <p className="mt-1">{user.city}</p>
                  </div>
                  <div className="sm:col-span-2">
                    <label className="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                      Address
                    </label>
                    <p className="mt-1">{user.address}</p>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
