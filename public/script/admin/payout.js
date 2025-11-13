window.initPayoutTab = function (accountId) {
    $(document).ready(function () {
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

    });

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let currentFilters = {};

    function displayData(page = 2, callback = null) {
        showPageLoader("Mohon tunggu...");
        $(`#show-data-payout`).html(`
                <div class="text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                        <i class="bx bx-loader bx-spin bx-lg"></i>
                        <div class="fw-medium mt-1">Loading data...</div>
                    </div>
                </div>
            `);

        let data_search = {...currentFilters};
        data_search.page = page;

        $.ajax({
            url: `/admin/xen_platform/partner-account/${accountId}/filter/payout`,
            method: "GET",
            data: data_search,
            success: async function (response) {
                $('#transactions-summary-div').html(response.summary);
                $(`#show-data-payout`).html(response.payoutTable);
                hidePageLoader();
            },
            error: function (err) {
                console.log(err);
                hidePageLoader();
            },
        });
    };

    $(document).ready(function () {
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

        $('#disbursement-filter-form').on('submit', function (e) {
            e.preventDefault();
            currentFilters = getFilterData();
            displayData(1);
        });

        $('#reset-filter-btn').on('click', function (e) {
            $('#disbursement-filter-form')[0].reset();
            currentFilters = {};
            displayData(1);
        });

        $(document).on('click', '#show-data-payout .pagination a', function (e) {
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

        if (businessId && payoutId) {
            $row.addClass('loading');

            const colCount = $row.find('td').length;

            $row.html(`
                    <td colspan="${colCount}" class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 overlay">
                            <div class="spinner-border" role="status" style="width:1.5rem; height:1.5rem;"></div>
                            <span class="fw-medium ml-1">Memuat detail payout...</span>
                        </div>
                    </td>
                `);

            setTimeout(() => {
                window.location.href = `/admin/send-payment/payout/${businessId}/detail/${payoutId}`;
            }, 250);
        } else {
            alert('Missing business_id or payout_id for row click.')
        }
    });
}