@extends('pages.admin.layouts.app')
@section('content-header')
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">XenPlatform</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active">All Account Partners
                        </li>
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
                    <h4 class="card-title">Partner Data</h4>
                </div>

                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="card border-top border-4 border">
                                    <div class="card-header border-bottom">
                                        @include('pages.admin.xen_platform.partner-account.filter')
                                    </div>

                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive mt-1" id="show-data-partner-account">
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
@section('modal')
    @include('pages.admin.xen_platform.partner-account.modal')
@endsection

@push('page-scripts')
    <script>
        let currentAccountPage = 1;
        const pagetAccountLimit = $('#filter-limit').val() ?? 10;

        $(document).ready(function () {
            displayData();

            $('#daterange-account-created').daterangepicker({
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

            $('#apply-filter-btn').on('click', function(e) {
                e.preventDefault();
                currentAccountPage = 1;
                displayData(getFilterData());
            });

            $(document).on('click', '#xendit-account-pagination .page-link', function(e) {
                e.preventDefault();

                const $this = $(this);
                const afterId = $this.data('after');
                const beforeId = $this.data('before');

                if ($this.closest('.page-item').hasClass('disabled')) return;

                const paginationParams = {};
                if (afterId) {
                    paginationParams.after_id = afterId;
                    currentAccountPage++;
                } else if (beforeId) {
                    paginationParams.before_id = beforeId;
                    currentAccountPage = Math.max(1, currentAccountPage - 1);
                }

                displayData(getFilterData(), paginationParams);
            });
        });

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

        const data = getUrl();

        const numberWithCommas = x => {
            if (x === null) return '-';
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        };

        function getFilterData() {
            const statusValues = $('#filter-status').val() || [];
            const typeValues = $('#filter-type').val() || [];
            const dateRangeVal = $('#daterange-account-created').val();

            let createdGte = '';
            let createdLte = '';

            if (dateRangeVal) {
                const dates = dateRangeVal.split(' - ');
                createdGte = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
                createdLte = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
            }

            return {
                email: $('#filter-email').val().trim(),
                status: statusValues.join(','),
                business_name: $('#filter-business-name').val().trim(),
                type:typeValues.join(','),
                created_gte: createdGte,
                created_lte: createdLte,
                limit: $('#filter-limit').val()
            };
        }

        function resetFilters() {
            $('#filter-email').val('');
            $('#filter-status').val([]);
            $('#filter-type').val([]);
            $('#filter-business-name').val('');
            $('#daterange-account-created').val('');
            $('#filter-limit').val('10');

            $('#filter-status').val(null).trigger('change');
            $('#filter-type').val(null).trigger('change');

            displayData();
        }

        function displayData(filter_data = {}, pagination_params = {}) {
            if (typeof showPageLoader === 'function') {
                showPageLoader("Mohon tunggu...");
            }

            $(`#show-data-${data}`).html(`
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
                page: currentAccountPage,
            };

            $.ajax({
                url: `/admin/xen_platform/${data}/create`,
                method: "GET",
                data: requestData,
                success: async function (response) {
                    await $(`#show-data-${data}`).html(response);

                    if (typeof hidePageLoader === 'function') {
                        hidePageLoader();
                    }
                },
                error: function (err) {
                    console.error("Error fetching data:", err);
                    $(`#show-data-${data}`).html('<div class="text-center text-danger py-2">Gagal memuat data. Silakan cek konsol untuk detail error.</div>');

                    if (typeof hidePageLoader === 'function') {
                        hidePageLoader();
                    }
                },
            });
        }
    </script>
@endpush
