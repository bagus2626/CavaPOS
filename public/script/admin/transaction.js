window.initTransactionTab = function (accountId) {
    let currentPage = 1;
    const pageLimit = $('#filter-limit').val() ?? 10;
    let activeFilters = {
        statuses: [],
        settlement_statuses: [],
        types: [],
        channel_categories: [],
        currency: []
    };

    $(document).ready(function () {
        $('#daterange-transaction').daterangepicker({
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

        $('#popup-filter-options').on('change', '.filter-checkbox', function () {
            const group = $(this).data('filter-group');
            const value = $(this).val();

            if (this.checked) {
                if (!activeFilters[group].includes(value)) {
                    activeFilters[group].push(value);
                }
            } else {
                activeFilters[group] = activeFilters[group].filter(item => item !== value);
            }
            updateFilterCount();
        });

        $('#popup-filter-options').on('click', function (e) {
            e.stopPropagation();
        });

        $('#clear-all-filters').on('click', function () {
            $('.filter-checkbox').prop('checked', false).trigger('change');
        });

        $('#apply-filter-btn').on('click', function (e) {
            e.preventDefault();
            currentPage = 1;
            $('#dropdownFilter').dropdown('hide');
            displayDataTransactions(getFilterData());
        });

        $('#reset-filter-btn').on('click', function (e) {
            e.preventDefault();
            currentPage = 1;
            resetAllFilters();
        });

        $('.search-type-select').on('click', function (e) {
            e.preventDefault();
            const key = $(this).data('search-key');
            const label = $(this).text();
            $('#current-search-key').val(key);
            $('#search-type-toggle').text(label);
            $('#global-search-input').attr('placeholder', `Search by ${label}...`);
        });

        $(document).on('click', '#xendit-pagination .page-link', function (e) {
            e.preventDefault();

            const $this = $(this);
            const afterId = $this.data('after');
            const beforeId = $this.data('before');

            if ($this.closest('.page-item').hasClass('disabled')) return;

            const paginationParams = {};
            if (afterId) {
                paginationParams.after_id = afterId;
                currentPage++;
            } else if (beforeId) {
                paginationParams.before_id = beforeId;
                currentPage = Math.max(1, currentPage - 1);
            }

            displayDataTransactions(getFilterData(), paginationParams);
        });

        displayDataTransactions(getFilterData());
    });

    function resetAllFilters() {
        $('.filter-checkbox').prop('checked', false).trigger('change');
        $('#daterange-transaction').val('');
        $('#global-search-input').val('');
        $('#current-search-key').val('reference_id');
        $('#search-type-toggle').text('Reference');

        displayDataTransactions(getFilterData());
    }

    function updateFilterCount() {
        let count = 0;
        for (const group in activeFilters) {
            count += activeFilters[group].length;
        }
        $('#filter-count').text(count);
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

    function getFilterData() {
        const dateRangeVal = $('#daterange-transaction').val();
        const searchKey = $('#current-search-key').val();
        const searchValue = $('#global-search-input').val().trim();

        let createdGte = '';
        let createdLte = '';

        if (dateRangeVal) {
            const dates = dateRangeVal.split(' - ');
            createdGte = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
            createdLte = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');
        }

        const filterPayload = {};
        for (const group in activeFilters) {
            if (activeFilters[group].length > 0) {
                filterPayload[group] = activeFilters[group].join(',');
            }
        }

        if (searchValue) {
            filterPayload[searchKey] = searchValue;
        }

        if (createdGte && createdLte) {
            filterPayload['created_gte'] = createdGte;
            filterPayload['created_lte'] = createdLte;
        }

        filterPayload.limit = $('#filter-limit').val();

        return filterPayload;
    }

    function displayDataTransactions(filter_data = {}, pagination_params = {}) {
        $(`#show-data-transaction`).html(`
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
            page: currentPage
        };

        $.ajax({
            url: `/admin/xen_platform/partner-account/${accountId}/filter/activity`,
            method: "GET",
            data: requestData,
            success: async function (response) {
                $('#transactions-summary-div').html(response.summary);
                $('#show-data-transaction').html(response.activityTable);

                const $tableBody = $('#show-data-transaction').find('tbody');
                const $rows = $tableBody.find('tr');

                const isEmptyTable = $rows.length === 1 &&
                    $rows.first().find('td[colspan]').length > 0;

                if (!isEmptyTable && $rows.length > 0) {

                    const startNumber = (currentPage - 1) * pageLimit + 1;
                    $rows.each(function (index) {
                        $(this).prepend(`<td>${startNumber + index}</td>`);
                    });
                }
            },
            error: function (err) {
                console.error("Error fetching data:", err);
                $(`#show-data-transaction`).html('<div class="text-center text-danger py-2">Gagal memuat data. Silakan coba lagi.</div>');
            },
        });
    }
}
