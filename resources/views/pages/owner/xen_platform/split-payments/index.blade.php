@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.split_payments.split_payments'))
@section('page_title', __('messages.owner.xen_platform.split_payments.split_payments'))

@section('content')
<div class="modern-container">
    <div class="container-modern">
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.xen_platform.split_payments.split_payments') }}</h1>
                <p class="page-subtitle">{{ __('messages.owner.xen_platform.split_payments.subtitle') }}</p>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-modern">
            <div class="alert-icon">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div class="alert-content">
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-modern">
            <div class="alert-icon">
                <span class="material-symbols-outlined">error</span>
            </div>
            <div class="alert-content">
                {{ session('error') }}
            </div>
        </div>
        @endif

        <div class="modern-card mb-4">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                @include('pages.owner.xen_platform.split-payments.filter')
            </div>
        </div>

        <div class="modern-card">
            <div class="data-table-wrapper" id="show-data-split-payments">
                <div class="text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                        <i class="bx bx-loader bx-spin bx-lg"></i>
                        <div class="fw-medium mt-1">{{ __('messages.owner.xen_platform.split_payments.loading_data') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentFilters = {};

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

            return filters;
        }

        $('#split-payments-filter-form').on('submit', function(e) {
            e.preventDefault();
            currentFilters = getFilterData();
            displayData(1);
        });

        $('#reset-filter-btn').on('click', function(e) {
            $('#split-payments-filter-form')[0].reset();
            currentFilters = {};
            displayData(1);
        });

        $(document).on('click', '#show-data-split-payments .pagination a', function(e) {
            e.preventDefault();

            const url = $(this).attr('href');
            if (!url) return;

            const page = new URLSearchParams(url.split('?')[1]).get('page');

            if (page) {
                displayData(parseInt(page));
            }
        });
    });

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    function displayData(page = 1, pagination_params = {}) {
        showPageLoader("Mohon tunggu...");

        $(`#show-data-split-payments`).html(`
            <div class="text-center">
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                   <i class="fas fa-spinner fa-spin fa-lg"></i>
                      <div class="text-bold-500 mt-3">Loading data...</div>
                </div>
            </div>
        `);

        let data_search = { ...currentFilters };
        data_search.page = page;

        $.ajax({
            url: `/owner/user-owner/xen_platform/split-payment/get-data`,
            method: "get",
            data: data_search,
            success: async function (response) {
                await $(`#show-data-split-payments`).html(response);

                if (typeof hidePageLoader === 'function') {
                    hidePageLoader();
                }
            },
            error: function (err) {
                console.error("Error fetching data:", err);
                $(`#show-data-split-payments`).html('<div class="text-center text-danger py-2">Gagal memuat data. Silakan cek konsol untuk detail error.</div>');

                if (typeof hidePageLoader === 'function') {
                    hidePageLoader();
                }
            },
        });
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