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
                        <li class="breadcrumb-item active">Split Payments
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
                    <div class="toolbar row">
                        <div class="col-md-12 d-flex">
                            <h4 class="card-title">Split Payments</h4>
                            <div class="col ml-auto">
                                <div class="dropdown float-right">
                                    <button type="button" class="btn btn-primary"
                                            onclick="showModal('create-split-rules','add')"><i
                                                class="bx bx-plus"></i> Create Split Rules
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" onclick="handleTab('split-payments')" id="split-payments-tab"
                                   data-toggle="tab" href="#split-payments" aria-controls="split-payments" role="tab"
                                   aria-selected="true">
                                    <span class="align-middle">Splits</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" onclick="handleTab('split-rules')" id="split-rules-tab" data-toggle="tab"
                                   href="#split-rules" aria-controls="split-rules'" role="tab" aria-selected="false">
                                    <span class="align-middle">Split Rules</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="split-payments" aria-labelledby="split-payments-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card border-top border-4 border">
                                            <div class="card-header border-bottom">
                                                @include('pages.admin.xen_platform.split-payments.payments.filter')
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body card-dashboard">
                                                    <div class="table-responsive mt-2" id="show-data-split-payments">
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
                            <div class="tab-pane" id="split-rules" aria-labelledby="split-rules-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card border-top border-4 border">
                                            <div class="card-header border-bottom">
                                                @include('pages.admin.xen_platform.split-payments.rules.filter')
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body card-dashboard">
                                                    <div class="table-responsive mt-2" id="show-data-split-rules">
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
        </div>
    </div>
@endsection
@section('modal')
    @include('pages.admin.xen_platform.split-payments.modal')
@endsection

@push('page-scripts')
    <script>
        let currentFilters = {};
        let tabActive = 'split-payments';

        $(document).ready(function () {
            displayData();

            $('#daterange-payments, #daterange-rules').daterangepicker({
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

            function getFilterData() {
                let filters = {};

                if (tabActive === 'split-payments') {
                    const reference_id = $('#search_reference_id').val().trim();
                    const transaction_status = $('#transaction_status').val();
                    const min_split = $('#filter-min-split').val();
                    const max_split = $('#filter-max-split').val();
                    const created_gte = $('#filter-created-gte').val();
                    const created_lte = $('#filter-created-lte').val();
                    const limit = $('#filter-limit').val();
                    const filter_business_name = $('#filter-business-name').val().trim();
                    const date_range = $('#daterange-payments').val();

                    let dateStart = '';
                    let dateEnd = '';

                    if (date_range) {
                        const dates = date_range.split(' - ');
                        dateStart = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
                        dateEnd = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
                    }

                    if (reference_id) filters.reference_id = reference_id;
                    if (transaction_status) filters.transaction_status = transaction_status;
                    if (min_split) filters.min_split = min_split;
                    if (max_split) filters.max_split = max_split;
                    if (created_gte) filters.created_gte = created_gte;
                    if (created_lte) filters.created_lte = created_lte;
                    if (limit) filters.limit = limit;
                    if (filter_business_name) filters.business_name = filter_business_name;
                    if (dateStart) filters.date_start = dateStart;
                    if (dateEnd) filters.date_end = dateEnd;

                }else if (tabActive === 'split-rules') {
                    const rules_id_or_name = $('#search_rules_id_or_name').val();
                    const business_name = $('#filter_business_name_rules').val();
                    const date_range = $('#daterange-rules').val();

                    let dateStart = '';
                    let dateEnd = '';

                    if (date_range) {
                        const dates = date_range.split(' - ');
                        dateStart = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
                        dateEnd = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
                    }

                    if (rules_id_or_name) filters.rules_id_or_name = rules_id_or_name;
                    if (business_name) filters.business_name = business_name;
                    if (dateStart) filters.date_start = dateStart;
                    if (dateEnd) filters.date_end = dateEnd;
                }

                return filters;
            }

            $('#split-payments-filter-form').on('submit', function(e) {
                e.preventDefault();
                currentFilters = getFilterData();
                displayData(1, tabActive);
            });

            $('#split-rules-filter-form').on('submit', function(e) {
                e.preventDefault();
                currentFilters = getFilterData();
                displayData(1, tabActive);
            });

            $('#reset-filter-btn').on('click', function(e) {
                $('#split-payments-filter-form')[0].reset();
                currentFilters = {};
                displayData(1, tabActive);
            });

            $('#reset-rules-filter-btn').on('click', function(e) {
                $('#split-rules-filter-form')[0].reset();
                currentFilters = {};
                displayData(1, tabActive);
            });

            $(document).on('click', '#show-data-split-payments .pagination a', function(e) {
                e.preventDefault();

                const url = $(this).attr('href');
                if (!url) return;

                const page = new URLSearchParams(url.split('?')[1]).get('page');

                if (page) {
                    displayData(parseInt(page), tabActive);
                }
            });

            $(document).on('click', '#show-data-split-rules .pagination a', function(e) {
                e.preventDefault();

                const url = $(this).attr('href');
                if (!url) return;

                const page = new URLSearchParams(url.split('?')[1]).get('page');

                if (page) {
                    displayData(parseInt(page), tabActive);
                }
            });
        });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });


        function displayData(page = 1, type = "split-payments", pagination_params = {}) {
            showPageLoader("Mohon tunggu...");
            tabActive = type;

            let data_search = { ...currentFilters };
            data_search.page = page;

            $.ajax({
                url: `/admin/xen_platform/split-payments/${type}`,
                method: "get",
                data: data_search,
                success: async function (response) {
                    await $(`#show-data-${type}`).html(response);

                    if (typeof hidePageLoader === 'function') {
                        hidePageLoader();
                    }
                },
                error: function (err) {
                    console.error("Error fetching data:", err);
                    $(`#show-data-${type}`).html('<div class="text-center text-danger py-2">Gagal memuat data. Silakan cek konsol untuk detail error.</div>');

                    if (typeof hidePageLoader === 'function') {
                        hidePageLoader();
                    }
                },
            });
        };

        const handleTab = (type, callback) => {
            displayData(1, type, {});
        };

        function showModal(type, id) {
            switch (type) {
                case "create-split-rules":
                    showPageLoader("Mohon tunggu...");
                    $(`#createAccountModal`).modal("show");

                    $.ajax({
                        url: `/admin/xendit/sub-account/profile/${id}`,
                        method: "get",
                        dataType: "json",
                        success: async function (response) {
                            await fetchData(type, response.data);

                            setTimeout(() => {
                                hidePageLoader();
                            }, 500);
                        },
                        error: function (err) {
                            console.log(err);
                            setTimeout(() => {
                                hidePageLoader();
                            }, 500);
                        },
                    });
                    break;
            }
        }

        function fetchData(type, response) {
            switch (type) {
                case "profile":
                    $("#business-name").val(response.public_profile.business_name);
                    $("#email").val(response.email);
                    $("#account-id").val(response.id);
                    $("#date-created").val(response.created);
                    $("#account-type").val(response.type);
                    $("#account-status").val(response.status);
                    break;
            }
        }
    </script>
@endpush