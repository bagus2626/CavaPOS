// class KitchenDashboard {
//     constructor() {
//         this.queueOrders = [];
//         this.activeOrders = []; // Tetap ada tapi kosong
//         this.baseUrl = '/employee/kitchen';
//         this.init();
//     }

//     init() {
//         this.loadOrderQueue();
//         this.loadActiveOrders(); // Tetap load tapi kosong
//         this.setupAutoRefresh();
//     }

//     // ORDER QUEUE METHODS - FOKUS UTAMA
//     async loadOrderQueue() {
//         try {
//             console.log('Loading order queue...');
//             const response = await fetch(`${this.baseUrl}/orders/queue`, {
//                 method: 'GET',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 }
//             });

//             const result = await response.json();
//             console.log('Queue API Response:', result);

//             if (result.success) {
//                 this.queueOrders = result.data.queue_orders;
//                 this.renderOrderQueue();
//                 this.updateCounters();
//             } else {
//                 console.error('Failed to load order queue:', result.message);
//                 this.showNotification('Failed to load order queue: ' + result.message, 'error');
//             }
//         } catch (error) {
//             console.error('Error loading order queue:', error);
//             this.showNotification('Error loading order queue: ' + error.message, 'error');
//         }
//     }

//     renderOrderQueue() {
//         const container = document.getElementById('orderQueue');
//         if (!container) return;

//         if (this.queueOrders.length === 0) {
//             container.innerHTML = `
//                 <div class="text-center text-muted-foreground py-8">
//                     <div class="text-lg mb-2">📭</div>
//                     <div>No orders in queue</div>
//                     <div class="text-sm mt-1">All orders have been processed</div>
//                 </div>
//             `;
//             return;
//         }

//         container.innerHTML = this.queueOrders.map(order => this.createQueueItem(order)).join('');
//     }

//     createQueueItem(order) {
//         return `
//             <div class="group rounded-lg bg-white p-4 transition-all hover:shadow-lg mb-4 border-2 border-red-200 hover:border-red-400">
//                 <div class="flex items-start gap-3 mb-3">
//                     <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#b91c1c] to-[#dc2626] text-sm font-bold text-white shrink-0 shadow-md">
//                         ${order.queue_number}
//                     </div>
//                     <div class="flex-1 min-w-0">
//                         <div class="flex items-center gap-2 mb-2 flex-wrap">
//                             <h4 class="font-bold text-lg text-foreground truncate">${order.customer_name}</h4>
//                             <span class="bg-gradient-to-r from-[#b91c1c] to-[#dc2626] text-white px-3 py-1 rounded-md text-xs font-bold uppercase shadow-md">
//                                 Waiting
//                             </span>
//                         </div>
//                         <div class="flex items-center gap-2 text-sm text-muted-foreground font-medium mb-2 flex-wrap">
//                             <span>⏰ ${order.order_time}</span>
//                             <span class="mx-1">•</span>
//                             <span class="bg-red-100 border border-red-300 text-red-800 px-2 py-1 rounded-md font-semibold">
//                                 📦 ${order.total_items} items
//                             </span>
//                             <span class="mx-1">•</span>
//                             <span class="font-bold">Rp ${order.total_order_value}</span>
//                         </div>
//                         <div class="bg-red-50 rounded-lg p-3 border border-red-200 max-h-20 overflow-y-auto">
//                             <p class="text-sm text-muted-foreground leading-relaxed">
//                                 ${order.product_names}
//                             </p>
//                         </div>
//                     </div>
//                 </div>
                
//                 <div class="grid grid-cols-2 gap-2 mt-3">
//                     <button class="px-4 py-2 text-sm font-semibold border-2 border-red-300 text-foreground hover:bg-red-50 bg-white rounded-lg transition-all flex items-center justify-center gap-2"
//                             onclick="kitchenDashboard.showOrderDetail('${order.id}')">
//                         <span>📋</span> <span>Details</span>
//                     </button>
//                     <button class="px-4 py-2 text-sm font-bold bg-gradient-to-r from-[#b91c1c] to-[#dc2626] hover:from-[#991b1b] hover:to-[#b91c1c] text-white rounded-lg transition-all flex items-center justify-center gap-2 shadow-md"
//                             onclick="kitchenDashboard.pickUpOrder('${order.id}')">
//                         <span>➕</span> <span>Pick Up</span>
//                     </button>
//                 </div>
//             </div>
//         `;
//     }

//     // ACTIVE ORDERS METHODS - KOSONG
//     async loadActiveOrders() {
//         try {
//             const response = await fetch(`${this.baseUrl}/orders/active`, {
//                 method: 'GET',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 }
//             });

//             const result = await response.json();
//             console.log('Active Orders API Response:', result);

//             if (result.success) {
//                 this.activeOrders = result.data.active_orders;
//                 this.renderActiveOrders();
//                 this.updateCounters();
//             } else {
//                 console.error('Failed to load active orders:', result.message);
//             }
//         } catch (error) {
//             console.error('Error loading active orders:', error);
//         }
//     }

//     renderActiveOrders() {
//         const container = document.getElementById('activeOrders');
//         if (!container) return;

//         // SELALU KOSONG - Hanya render ketika ada data dari pick up
//         if (this.activeOrders.length === 0) {
//             container.innerHTML = `
//                 <div class="col-span-full text-center py-12">
//                     <div class="text-4xl mb-4">👨‍🍳</div>
//                     <div class="text-muted-foreground text-lg">No orders cooking</div>
//                     <div class="text-sm text-muted-foreground mt-2">Pick up orders from the queue to start cooking</div>
//                 </div>
//             `;
//             return;
//         }

//         // Hanya render jika ada data dari pick up
//         container.innerHTML = this.activeOrders.map(order => this.createActiveOrderCard(order)).join('');
//     }

//     createActiveOrderCard(order) {
//         return `
//             <div class="group rounded-lg bg-white p-4 transition-all hover:shadow-lg border-2 border-yellow-200 hover:border-yellow-400 h-auto flex flex-col">
//                 <div class="flex items-start gap-3 mb-3">
//                     <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#d97706] to-[#f59e0b] text-sm font-bold text-white shrink-0 shadow-md">
//                         ${order.table_id || 'T'}
//                     </div>
//                     <div class="flex-1 min-w-0">
//                         <div class="flex items-center gap-2 mb-2 flex-wrap">
//                             <h4 class="font-bold text-lg text-foreground truncate">${order.customer_name}</h4>
//                             <span class="bg-gradient-to-r from-[#d97706] to-[#f59e0b] text-white px-3 py-1 rounded-md text-xs font-bold uppercase shadow-md">
//                                 Cooking
//                             </span>
//                         </div>
//                         <div class="flex items-center gap-2 text-sm text-muted-foreground font-medium mb-2 flex-wrap">
//                             <span>⏰ ${order.order_time}</span>
//                             <span class="mx-1">•</span>
//                             <span class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-2 py-1 rounded-md font-semibold">
//                                 📦 ${order.total_items}
//                             </span>
//                             <span class="mx-1">•</span>
//                             <span class="font-bold">Rp ${order.total_order_value}</span>
//                         </div>
//                         <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200 max-h-20 overflow-y-auto">
//                             <p class="text-sm text-muted-foreground leading-relaxed">
//                                 ${order.product_names}
//                             </p>
//                         </div>
//                     </div>
//                 </div>
                
//                 <div class="grid grid-cols-2 gap-2 mt-3">
//                     <button class="px-4 py-2 text-sm font-semibold border-2 border-yellow-300 text-foreground hover:bg-yellow-50 bg-white rounded-lg transition-all flex items-center justify-center gap-2"
//                             onclick="kitchenDashboard.showOrderDetail('${order.id}')">
//                         <span>📋</span> <span>Details</span>
//                     </button>
//                     <button class="px-4 py-2 text-sm font-bold bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all flex items-center justify-center gap-2 shadow-md"
//                             onclick="kitchenDashboard.markAsServed('${order.id}')">
//                         <span>✅</span> <span>Serve</span>
//                     </button>
//                 </div>
//             </div>
//         `;
//     }

//     // ACTION METHODS - PICK UP ORDER
//     async pickUpOrder(orderId) {
//         try {
//             const response = await fetch(`${this.baseUrl}/orders/${orderId}/pickup`, {
//                 method: 'PUT',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 }
//             });

//             const result = await response.json();

//             if (result.success) {
//                 this.showNotification('Order moved to active orders! 🎯 Now Cooking', 'success');
//                 // Reload both sections untuk update data terbaru
//                 setTimeout(() => {
//                     this.loadOrderQueue(); // Queue berkurang
//                     this.loadActiveOrders(); // Active orders bertambah
//                 }, 500);
//             } else {
//                 this.showNotification(result.message, 'error');
//             }
//         } catch (error) {
//             console.error('Error picking up order:', error);
//             this.showNotification('Error picking up order', 'error');
//         }
//     }

//     async markAsServed(orderId) {
//         try {
//             const response = await fetch(`${this.baseUrl}/orders/${orderId}/serve`, {
//                 method: 'PUT',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 }
//             });

//             const result = await response.json();

//             if (result.success) {
//                 this.showNotification('Order marked as served! ✅', 'success');
//                 // Reload active orders (order akan hilang dari active orders)
//                 setTimeout(() => {
//                     this.loadActiveOrders();
//                 }, 500);
//             } else {
//                 this.showNotification(result.message, 'error');
//             }
//         } catch (error) {
//             console.error('Error marking order as served:', error);
//             this.showNotification('Error marking order as served', 'error');
//         }
//     }

//     // UPDATE COUNTERS
//     updateCounters() {
//         const waitingCount = this.queueOrders.length;
//         const cookingCount = this.activeOrders.length;
        
//         // Update counters
//         document.getElementById('waitingCount').textContent = waitingCount;
//         document.getElementById('cookingCount').textContent = cookingCount;
//         document.getElementById('completedCount').textContent = '0';
        
//         // Update text descriptions
//         document.getElementById('activeOrdersCount').textContent = `${cookingCount} orders cooking`;
//         document.getElementById('queueOrdersCount').textContent = `${waitingCount} orders waiting`;
//         document.getElementById('servedOrdersCount').textContent = `0 orders served`;
//     }

//     showOrderDetail(orderId) {
//         // Cari order di queue atau active orders
//         let order = this.queueOrders.find(o => o.id == orderId);
//         if (!order) {
//             order = this.activeOrders.find(o => o.id == orderId);
//         }
        
//         if (!order) return;

//         alert(`ORDER DETAIL\n\nCustomer: ${order.customer_name}\nOrder: ${order.booking_order_code}\nTable: ${order.table_id || 'N/A'}\nStatus: ${order.status_badge}\nTime: ${order.order_time}\n\nItems: ${order.product_names}\n\nTotal: Rp ${order.total_order_value}`);
//     }

//     showNotification(message, type = 'info') {
//         const notification = document.createElement('div');
//         notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 ${
//             type === 'success' ? 'bg-green-500' : 'bg-red-500'
//         }`;
//         notification.textContent = message;

//         document.getElementById('notificationContainer').appendChild(notification);

//         setTimeout(() => {
//             notification.remove();
//         }, 3000);
//     }

//     setupAutoRefresh() {
//         // Auto refresh every 15 seconds
//         setInterval(() => {
//             this.loadOrderQueue();
//             this.loadActiveOrders();
//         }, 15000);
//     }
// }

// // Initialize dashboard
// document.addEventListener('DOMContentLoaded', function() {
//     window.kitchenDashboard = new KitchenDashboard();
// });