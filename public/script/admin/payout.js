window.initPayoutTab = function (accountId) {
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
                console.log(response);
                // await $(`#show-data-disbursement`).html(response);
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
            const dateFrom = $('#filter-date-from').val();
            const dateTo = $('#filter-date-to').val();

            let filters = {};
            if (search) filters.search = search;
            if (status) filters.status = status;
            if (dateFrom) filters.date_from = dateFrom;
            if (dateTo) filters.date_to = dateTo;

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
                            <span class="fw-medium ml-1">Memuat detail transaksi...</span>
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