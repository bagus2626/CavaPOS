window.initTransactionTab = function (accountId) {
    console.log('Test: initTransactionTab')
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
                       <i class="fas fa-spinner fa-spin fa-lg"></i>
                       <div class="text-bold-500 mt-3">Loading data...</div>
                    </div>
                </div>
            `);

        const requestData = {
            ...filter_data,
            ...pagination_params,
            page: currentPage
        };

        $.ajax({
            url: `/owner/user-owner/xen_platform/accounts/filter/activity`,
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

    $(document).on('click', '.copy-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const value = String($btn.data('copy-value') ?? '');
        const originalHtml = $btn.html();

        if (!value) {
            flashTemp($btn, 'No value to copy', '#fff3cd', '#856404');
            return;
        }

        const onCopied = () => {
            $btn.html('<span class="text-success">Value copied to clipboard</span>');
            $btn.css({
                'background-color': '#e6f9ec',
                'border-radius': '6px'
            });

            setTimeout(() => {
                $btn.html(originalHtml);
                $btn.css({'background-color': '', 'border-radius': ''});
            }, 2500);
        };

        const onFail = (err) => {
            console.error('Copy failed:', err);
            flashTemp($btn, 'Copy failed', '#f8d7da', '#721c24');
        };

        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(value)
                .then(onCopied)
                .catch(() => {
                    fallbackCopyTextToClipboard(value, (ok) => ok ? onCopied() : onFail('fallback failed'));
                });
        } else {
            fallbackCopyTextToClipboard(value, (ok) => ok ? onCopied() : onFail('no clipboard API'));
        }
    });

    function fallbackCopyTextToClipboard(text, cb) {
        try {
            const $txt = $('<textarea>');
            $txt.css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '2em',
                height: '2em',
                padding: 0,
                border: 'none',
                outline: 'none',
                boxShadow: 'none',
                background: 'transparent'
            });
            $txt.val(text);
            $('body').append($txt);
            $txt[0].select();
            $txt[0].setSelectionRange(0, $txt[0].value.length);
            const success = document.execCommand('copy');
            $txt.remove();
            cb(Boolean(success));
        } catch (err) {
            cb(false);
        }
    }

    function flashTemp($btn, message, bgColor = '#fff3cd', textColor = '#856404') {
        const original = $btn.html();
        $btn.html(`<span style="color:${textColor}">${message}</span>`);
        $btn.css({'background-color': bgColor, 'border-radius': '6px'});
        setTimeout(() => {
            $btn.html(original);
            $btn.css({'background-color': '', 'border-radius': ''});
        }, 2500);
    }

    $(document).on('click', '.transaction-clickable-row', function (e) {
        const $row = $(this);

        if ($(e.target).closest('.dropdown, .dropdown-toggle, a').length > 0) {
            return;
        }

        const businessId = $row.data('business-id');
        const transactionId = $row.data('transaction-id');

        if (transactionId && businessId) {
            $row.addClass('loading');

            const colCount = $row.find('td').length;

            $row.html(`
                    <td colspan="${colCount}" class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 overlay">
                            <div class="fas fa-spinner fa-spin fas-lg" role="status"></div>
                            <span class="fw-medium ml-1">Memuat detail transaksi...</span>
                        </div>
                    </td>
                `);

            setTimeout(() => {
                window.location.href = `/owner/user-owner/xen_platform/accounts/transaction-detail/${transactionId}`;
            }, 250);
        } else {
            alert('Missing business_id or payout_id for row click.')
        }
    });

}
