window.initInvoiceTab = function(accountId) {
    let currentInvoicePage = 1;
    const pageInvoiceLimit = $('#invoice-filter-limit').val() ?? 10;
    let activeInvoiceFilters = {
        statuses: [],
        client_types: [],
        payment_channels: []
    };

    $(document).ready(function () {
        $('#daterange-invoice').daterangepicker({
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
            },
            locale: { cancelLabel: 'Clear', format: 'YYYY/MM/DD' }
        }).on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });

        $('.date-type-select').on('click', function(e) {
            e.preventDefault();
            const key = $(this).data('date-key');
            const label = $(this).text();
            $('#current-date-key').val(key);
            $('#date-type-toggle').text(label);
        });

        $('.search-invoice-type-select').on('click', function(e) {
            e.preventDefault();
            const key = $(this).data('search-key');
            const label = $(this).text();
            $('#current-invoice-search-key').val(key);
            $('#search-invoice-type-toggle').text(label);
            $('#global-invoice-search-input').attr('placeholder', `Search ${label}...`);
        });

        $('#popup-invoice-filter-options').on('change', '.invoice-filter-checkbox', function() {
            const group = $(this).data('filter-group');
            const value = $(this).val();

            if (this.checked) {
                if (!activeInvoiceFilters[group].includes(value)) {
                    activeInvoiceFilters[group].push(value);
                }
            } else {
                activeInvoiceFilters[group] = activeInvoiceFilters[group].filter(item => item !== value);
            }
            updateInvoiceFilterCount();
        });

        $(document).on('click', '#xendit-invoice-pagination .page-link', function (e) {
            e.preventDefault();

            const $this = $(this);
            if ($this.closest('.page-item').hasClass('disabled')) return;

            const cursorValue = $this.data('cursor');
            const direction = $this.data('direction');

            const paginationParams = {};
            if (direction === 'before' && cursorValue) {
                paginationParams.before_id = cursorValue;
                currentInvoicePage = Math.max(1, currentInvoicePage - 1);
            }
            else if (direction === 'after' && cursorValue) {
                paginationParams.after_id = cursorValue;
                currentInvoicePage++;
            }

            displayInvoiceData(getInvoiceFilterData(), paginationParams, 'row');
        });

        $('#clear-all-invoice-filters').on('click', function() {
            $('.invoice-filter-checkbox').prop('checked', false).trigger('change');
        });

        $('#apply-invoice-filter-btn').on('click', function(e) {
            e.preventDefault();
            currentInvoicePage = 1;
            $('#dropdownFilterInvoice').dropdown('hide');
            displayInvoiceData(getInvoiceFilterData(), {}, 'table');
        });

        $('#reset-invoice-filter-btn').on('click', function(e) {
            e.preventDefault();
            currentInvoicePage = 1;
            resetAllInvoiceFilters();
        });

        displayInvoiceData(getInvoiceFilterData());
    });

    function updateInvoiceFilterCount() {
        let count = 0;
        for (const group in activeInvoiceFilters) {
            count += activeInvoiceFilters[group].length;
        }
        $('#invoice-filter-count').text(count);
    }

    function resetAllInvoiceFilters() {
        $('.invoice-filter-checkbox').prop('checked', false).trigger('change');

        $('#daterange-invoice').val('');
        $('#current-date-key').val('created');
        $('#date-type-toggle').text('Created Date');
        $('#global-invoice-search-input').val('');
        $('#current-invoice-search-key').val('external_id');
        $('#search-invoice-type-toggle').text('External ID');

        displayInvoiceData(getInvoiceFilterData(), {}, 'table');
    }

    function getInvoiceFilterData() {
        const filterPayload = {};
        const dateRangeVal = $('#daterange-invoice').val();
        const dateKey = $('#current-date-key').val();
        const searchKey = $('#current-invoice-search-key').val();
        const searchValue = $('#global-invoice-search-input').val().trim();

        for (const group in activeInvoiceFilters) {
            if (activeInvoiceFilters[group].length > 0) {
                filterPayload[group] = activeInvoiceFilters[group].join(',');
            }
        }

        if (dateRangeVal) {
            const dates = dateRangeVal.split(' - ');
            const startDate = moment(dates[0], 'YYYY/MM/DD').toISOString();
            const endDate = moment(dates[1], 'YYYY/MM/DD').endOf('day').toISOString();

            filterPayload[`${dateKey}_after`] = startDate;
            filterPayload[`${dateKey}_before`] = endDate;
        }

        if (searchValue) {
            filterPayload[searchKey] = searchValue;
        }

        filterPayload.limit = $('#invoice-filter-limit').val();

        return filterPayload;
    }

    function displayInvoiceData(filter_data = {}, pagination_params = {}, render_type = 'table') {
        if (render_type === 'table') {
            $(`#show-data-invoices`).html(`
                    <div class="text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                            <i class="bx bx-loader bx-spin bx-lg"></i>
                            <div class="fw-medium mt-1">Loading data...</div>
                        </div>
                    </div>
                `);
        }

        const requestData = {
            ...filter_data,
            ...pagination_params,
            page: currentInvoicePage,
        };

        $.ajax({
            url: `/admin/xen_platform/partner-account/${accountId}/filter/invoices`,
            method: "GET",
            data: requestData,
            success: function (response) {

                if (render_type === 'table') {
                    $('#transactions-summary-div').html(response.summary);
                    $(`#show-data-invoices`).html(response.invoiceTable);

                    addInvoiceRowNumbers();

                } else if (render_type === 'row') {
                    const invoices = response.invoiceData.invoices;
                    const meta = response.invoiceData.meta;
                    const newRowsCount = renderInvoiceRows(invoices);

                    if (meta) {
                        renderInvoicePagination(meta, invoices.length);
                    }

                    addInvoiceRowNumbers(true, newRowsCount);
                }
            },
            error: function (err) {
                console.error("Error fetching invoice data:", err);
                $(`#show-data-invoices`).html('<div class="text-center text-danger py-2">Gagal memuat data. Silakan coba lagi.</div>');
            },
        });
    }

    function addInvoiceRowNumbers(appendMode = false, newRowsCount = 0) {
        const $tableBody = $('#xendit-invoice-table tbody');
        const $rows = $tableBody.find('tr').not(':has(td[colspan])');

        if ($rows.length === 0) return;

        $rows.find('td:first-child').remove();

        let startNumber = 1;
        if (appendMode) {
            const lastNumber = parseInt($('#xendit-invoice-table tbody tr:last td:first').text());
            startNumber = isNaN(lastNumber) ? 1 : lastNumber - newRowsCount + 1;
        }

        $rows.each(function (index) {
            $(this).prepend(`<td>${index + 1}</td>`);
        });
    }

    function renderInvoiceRows(invoices) {
        if (!Array.isArray(invoices) || invoices.length === 0) {
            console.warn("Tidak ada data invoice baru untuk ditambahkan.");
            return 0;
        }

        const tableBody = $('#xendit-invoice-table tbody');

        const emptyRow = tableBody.find('td[colspan]');
        if (emptyRow.length > 0) {
            emptyRow.closest('tr').remove();
        }

        let rowsHtml = '';

        invoices.forEach((item) => {
            let badgeClass = 'badge-light-dark';
            switch (item.status) {
                case 'PENDING':
                    badgeClass = 'badge-light-warning';
                    break;
                case 'PAID':
                    badgeClass = 'badge-light-info';
                    break;
                case 'SETTLED':
                    badgeClass = 'badge-light-success';
                    break;
                case 'EXPIRED':
                    badgeClass = 'badge-light-secondary';
                    break;
            }

            const createdDate = new Date(item.created);
            const tanggal = createdDate.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            const waktu = createdDate.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });

            const formattedAmount = new Intl.NumberFormat('id-ID').format(item.amount ?? 0);

            rowsHtml += `
        <tr data-id="${item.id}">
            <td class="text-bold-500">${item.id ?? '-'}</td>
            <td>
                <span class="font-weight-bold">${tanggal}</span><br>
                <small class="text-muted">${waktu}</small>
            </td>
            <td>${item.external_id ?? '-'}</td>
            <td>${item.customer?.email ?? '-'}</td>
            <td>${item.description ?? '-'}</td>
            <td>${item.currency ?? 'IDR'} ${formattedAmount}</td>
            <td><span class="badge ${badgeClass} badge-pill">${item.status ?? 'UNKNOWN'}</span></td>
            <td class="text-center">
                <div class="dropdown">
                    <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#"><i class="bx bx-copy-alt mr-1"></i> Copy Transaction ID</a>
                        <a class="dropdown-item" href="#"><i class="bx bx-copy-alt mr-1"></i> Copy Reference</a>
                        <a class="dropdown-item" href="#"><i class="bx bx-send mr-1"></i> Resend Webhook</a>
                        <a class="dropdown-item" href="#"><i class="bx bx-copy-alt mr-1"></i> Copy Invoice ID</a>
                    </div>
                </div>
            </td>
        </tr>`;
        });

        tableBody.append(rowsHtml);
        return invoices.length;
    }

    function renderInvoicePagination(meta, totalRows = 0) {
        const paginationContainer = $('#xendit-invoice-pagination');
        paginationContainer.empty();

        const afterId = meta.after_id ?? null;
        const limit = meta.limit ?? 10;

        const isDisabled = !afterId || totalRows < limit;

        const paginationHtml = `
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center border-top pt-1">
            <nav aria-label="Navigasi halaman transaksi">
                <ul class="pagination mb-0 pagination">
                    <li class="page-item ${isDisabled ? 'disabled' : ''} mr-1">
                        <a class="page-link fw-medium px-2 rounded-pill"
                           href="#"
                           data-cursor="${afterId ?? ''}"
                           data-direction="after">
                           More
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    `;

        paginationContainer.html(paginationHtml);
    }
}
