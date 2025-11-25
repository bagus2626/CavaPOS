@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.payouts.withdrawal'))
@section('page_title', __('messages.owner.xen_platform.payouts.withdrawal'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('messages.owner.xen_platform.payouts.withdrawal_list') }}</h4>
                            <div class="col ml-auto">
                                <div class="dropdown float-right">
                                    <button type="button" class="btn btn-primary"
                                            onclick="showModal('create-split-rules','add')">
                                        <i class="fas fa-paper-plane mr-1"></i> {{ __('messages.owner.xen_platform.payouts.create_withdrawal') }}
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
                                                @include('pages.owner.xen_platform.payout.filter')
                                            </div>

                                            <div class="card-content">
                                                <div class="card-body card-dashboard">
                                                    <div class="table-responsive mt-1" id="show-data-disbursement">
                                                        <div class="text-center">
                                                            <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                                <i class="bx bx-loader bx-spin bx-lg"></i>
                                                                <div class="fw-medium mt-1">{{ __('messages.owner.xen_platform.payouts.loading_data') }}</div>
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
    </section>
@endsection
@section('modal')
    @include('pages.owner.xen_platform.payout.modal')
@endsection

@push('scripts')
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
                       <i class="fas fa-spinner fa-spin fa-lg"></i>
                          <div class="text-bold-500 mt-3">Loading data...</div>
                    </div>
                </div>
            `);

            let data_search = { ...currentFilters };
            data_search.page = page;

            $.ajax({
                url: `{{ route("owner.user-owner.xen_platform.payout.get-data") }}`,
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

        $(document).on('click', '.disbursement-clickable-row', function (e) {
            const $row = $(this);

            if ($(e.target).closest('.dropdown, .dropdown-toggle, a').length > 0) {
                return;
            }

            const businessId = $row.data('business-id');
            const payoutId = $row.data('payout-id');

            if (payoutId && businessId) {
                $row.addClass('loading');

                const colCount = $row.find('td').length;

                $row.html(`
                    <td colspan="${colCount}" class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 overlay">
                             <div class="fas fa-spinner fa-spin fas-lg" role="status"></div>
                            <span class="fw-medium ml-1">Memuat detail Invoice...</span>
                        </div>
                    </td>
                `);

                setTimeout(() => {
                    window.location.href = `/owner/user-owner/xen_platform/payout/detail/${payoutId}`;
                }, 250);
            } else {
                alert('Missing business_id or payout_id for row click.')
            }
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