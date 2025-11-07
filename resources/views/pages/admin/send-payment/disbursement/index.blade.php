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
                    <div class="card-body">

                        <hr/>

                        <div class="table-responsive" id="show-data-disbursement">
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

            function getFilterData() {
                const search = $('#filter-search').val().trim();
                const status = $('#filter-status').val();
                const dateFrom = $('#filter-date-from').val();
                const dateTo = $('#filter-date-to').val();

                let filters = {};
                if (search) filters.search = search;
                if (status) filters.status = status;
                if (dateFrom) filters.date_from = dateFrom;
                if (dateTo) filters.date_to = dateTo;

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