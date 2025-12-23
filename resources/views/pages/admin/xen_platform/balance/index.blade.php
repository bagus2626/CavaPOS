@extends('pages.admin.layouts.app')
@section('content-header')
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Xendit</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Balance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Balance Running</h4>
                </div>

                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-top border-4 border">
                                    <div class="card-header border-bottom">
                                        @include('pages.admin.xen_platform.balance.filter')
                                    </div>

                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive mt-1" id="show-data-balance">
                                                <div class="text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                        <i class="bx bx-loader bx-spin bx-lg"></i>
                                                        <div class="fw-medium mt-1">Loading data...</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        let currentBalancePage = 1;
        const pageBalanceLimit = $('#filter-limit').val() ?? 10;
        let activeBalanceFilters = {
            types: [],
            channel_categories: [],
        };

        $(document).ready(function () {
            $('#daterange-balance').daterangepicker({
                autoUpdateInput: false,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                },
                locale: {cancelLabel: 'Clear', format: 'YYYY/MM/DD'}
            }).on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            });

            $('#popup-balance-filter-options').on('change', '.balance-filter-checkbox, .filter-checkbox', function () {
                const group = $(this).data('filter-group');
                const value = $(this).val();

                if (this.checked) {
                    if (!activeBalanceFilters[group].includes(value)) {
                        activeBalanceFilters[group].push(value);
                    }
                } else {
                    activeBalanceFilters[group] = activeBalanceFilters[group].filter(item => item !== value);
                }
                updateBalanceFilterCount();
            });

            $('#popup-balance-filter-options').on('click', function (e) {
                e.stopPropagation();
            });

            $('#clear-all-balance-filters').on('click', function () {
                $('.balance-filter-checkbox').prop('checked', false).trigger('change');
            });

            $('#apply-balance-filter-btn').on('click', function (e) {
                e.preventDefault();
                currentBalancePage = 1;
                $('#dropdownFilterBalance').dropdown('hide');
                displayDataBalances(getBalanceFilterData());
            });

            $('#reset-balance-filter-btn').on('click', function (e) {
                e.preventDefault();
                currentBalancePage = 1;
                resetAllBalanceFilters();
            });

            $('.search-balance-type-select').on('click', function (e) {
                e.preventDefault();
                const key = $(this).data('search-key');
                const label = $(this).text();
                $('#current-balance-search-key').val(key);
                $('#search-balance-type-toggle').text(label);
                $('#global-balance-search-input').attr('placeholder', `Search ${label}...`);
            });

            $(document).on('click', '#xendit-balance-pagination .page-link', function (e) {
                e.preventDefault();

                const $this = $(this);
                const afterId = $this.data('after');
                const beforeId = $this.data('before');
                const direction = $this.data('direction');

                if ($this.closest('.page-item').hasClass('disabled')) return;

                const paginationParams = {};
                if (direction === 'after' && afterId) {
                    paginationParams.after_id = afterId;
                    currentBalancePage++;
                } else if (direction === 'before' && beforeId) {
                    paginationParams.before_id = beforeId;
                    currentBalancePage = Math.max(1, currentBalancePage - 1);
                }
                displayDataBalances(getBalanceFilterData(), paginationParams);
            });
            displayDataBalances(getBalanceFilterData());
        });

        function resetAllBalanceFilters() {
            $('.balance-filter-checkbox').prop('checked', false).trigger('change');
            $('#daterange-balance').val('');
            $('#global-balance-search-input').val('');
            $('#current-balance-search-key').val('reference_id');
            $('#search-balance-type-toggle').text('Reference');

            displayDataBalances(getBalanceFilterData());
        }

        function updateBalanceFilterCount() {
            let count = 0;
            for (const group in activeBalanceFilters) {
                count += activeBalanceFilters[group].length;
            }
            $('#balance-filter-count').text(count);
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        const getUrl = () => {
            let url = window.location.href;
            let arr = url.split("/");
            let data = arr[5];
            return data;
        };

        function getBalanceFilterData() {
            const dateRangeVal = $('#daterange-balance').val();
            const dateKey = 'created';
            const searchKey = $('#current-balance-search-key').val();
            const searchValue = $('#global-balance-search-input').val().trim();

            let dateGte = '';
            let dateLte = '';

            if (dateRangeVal) {
                const dates = dateRangeVal.split(' - ');
                dateGte = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
                dateLte = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
            }

            const filterPayload = {};
            for (const group in activeBalanceFilters) {
                if (activeBalanceFilters[group].length > 0) {
                    filterPayload[group] = activeBalanceFilters[group].join(',');
                }
            }

            if (searchValue) {
                filterPayload[searchKey] = searchValue;
            }

            if (dateGte && dateLte) {
                filterPayload[`${dateKey}_gte`] = dateGte;
                filterPayload[`${dateKey}_lte`] = dateLte;
            }

            filterPayload.limit = $('#balance-filter-limit').val();

            return filterPayload;
        }

        function displayDataBalances(filter_data = {}, pagination_params = {}) {
            showPageLoader("Mohon tunggu...");
            $(`#show-data-balance`).html(`
                <div class="text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                        <i class="bx bx-loader bx-spin bx-lg"></i>
                        <div class="fw-medium mt-1">Loading data...</div>
                    </div>
                </div>
            `);

            const requestData = {
                ...filter_data,
                ...pagination_params,
                page: currentBalancePage
            };

            $.ajax({
                url: `{{ route("admin.xen_platform.balance.data") }}`,
                method: "POST",
                data: requestData,
                success: async function (response) {
                    $(`#show-data-balance`).html(response.balanceTable);

                    const $tableBody = $('#show-data-balance').find('tbody');
                    const $rows = $tableBody.find('tr');

                    const isEmptyTable = $rows.length === 1 &&
                        $rows.first().find('td[colspan]').length > 0;

                    if (!isEmptyTable && $rows.length > 0) {
                        const startNumber = (currentBalancePage - 1) * pageBalanceLimit + 1;
                        $rows.each(function (index) {
                            $(this).prepend(`<td>${startNumber + index}</td>`);
                        });
                    }
                    hidePageLoader();
                },
                error: function (err) {
                    console.error("Error fetching balance data:", err);
                    $(`#show-data-balance`).html('<div class="text-center text-danger py-2">Gagal memuat data Balance. Silakan coba lagi.</div>');
                },
            });
        }
    </script>
@endpush