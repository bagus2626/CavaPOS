<div class="lg:col-span-1 h-full">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm h-full flex flex-col overflow-hidden">
        <!-- HEADER - Sticky dengan shadow -->
        <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Order Selesai</h2>
                    <span class="text-sm font-normal text-gray-500">({{ $items->count() }})</span>
                </div>
            </div>
        </div>


        <!-- TABLE AREA - dengan styling smooth -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            @if ($items->isEmpty())
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada order yang selesai</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Table
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer Info
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($items as $i)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Order ID -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $i->booking_order_code }}
                                        </div>
                                    </td>


                                    <!-- Table -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-green-100 text-green-600 font-semibold text-sm">
                                                {{ $i->table->table_no ?? '-' }}
                                            </div>
                                        </div>
                                    </td>


                                    <!-- Customer Info -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-green-100 text-green-600 font-semibold text-xs">
                                                {{ strtoupper(substr($i->customer_name, 0, 2)) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $i->customer_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>


                                    <!-- Time -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $i->created_at?->format('H:i') }}
                                        </div>
                                    </td>


                                    <!-- Total Amount -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                        </div>
                                    </td>


                                    <!-- Status -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-600"></span>
                                            Completed
                                        </span>
                                    </td>


                                    <!-- Actions -->
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Detail Button (Eye Icon) -->
                                            <a href="{{ route('employee.cashier.order-detail', $i->id) }}"
                                                data-detail-btn
                                                data-order-id="{{ $i->id }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all"
                                                title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>


@include('pages.employee.cashier.dashboard.modals.cash')
@include('pages.employee.cashier.dashboard.modals.detail')
@include('pages.employee.cashier.dashboard.modals.served')