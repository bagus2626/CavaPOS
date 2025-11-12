@extends('pages.admin.layouts.app')
@section('content-header')
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Send Payments</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Disbursements</li>
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
                    <h4 class="card-title">List Disbursement</h4>
                    <div class="col ml-auto">
                        <div class="dropdown float-right">
                            <button type="button" class="btn btn-primary"
                                    onclick="showModal('create-split-rules','add')"><i
                                        class="bx bx-plus"></i> Create Disbursement
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="card border-top border-4 border">
                                    <div class="card-header border-bottom">
                                        @include('pages.admin.send-payment.disbursement.filter')
                                    </div>

                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive mt-1" id="show-data-disbursement">
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
    @include('pages.admin.send-payment.disbursement.modal')
@endsection
@push('page-scripts')
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        let currentFilters = {};

        function displayData(page = 1, callback = null) {
            showPageLoader("Mohon tunggu...");
            $(`#show-data-disbursement`).html(`
                <div class="text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                        <i class="bx bx-loader bx-spin bx-lg"></i>
                        <div class="fw-medium mt-1">Loading data...</div>
                    </div>
                </div>
            `);

            let data_search = { ...currentFilters };
            data_search.page = page;

            $.ajax({
                url: `{{ route("admin.send-payment.payout.get-data") }}`,
                method: "post",
                data: data_search,
                success: async function (response) {
                    await $(`#show-data-disbursement`).html(response);
                    hidePageLoader();
                },
                error: function (err) {
                    console.log(err);
                    hidePageLoader();
                },
            });
        };

        $(document).ready(function () {
            displayData();

            $('#daterange-payout').daterangepicker({
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
                const search = $('#filter-search').val().trim();
                const status = $('#filter-status').val();
                const date_range = $('#daterange-payout').val();

                let dateStart = '';
                let dateEnd = '';

                if (date_range) {
                    const dates = date_range.split(' - ');
                    dateStart = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
                    dateEnd = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
                }


                let filters = {};
                if (search) filters.search = search;
                if (status) filters.status = status;
                if (dateStart) filters.date_from = dateStart;
                if (dateEnd) filters.date_to = dateEnd;

                return filters;
            }

            $('#disbursement-filter-form').on('submit', function(e) {
                e.preventDefault();
                currentFilters = getFilterData();
                displayData(1);
            });

            $('#reset-filter-btn').on('click', function(e) {
                $('#disbursement-filter-form')[0].reset();
                currentFilters = {};
                displayData(1);
            });

            $(document).on('click', '#show-data-disbursement .pagination a', function(e) {
                e.preventDefault();

                const url = $(this).attr('href');
                if (!url) return;

                const page = new URLSearchParams(url.split('?')[1]).get('page');

                if (page) {
                    displayData(parseInt(page));
                }
            });
        });

        function showModal(type, id) {
            switch (type) {
                case "create-split-rules":
                    $(`#createDisbursementModal`).modal("show");
                    break;
            }
        }
    </script>
@endpush