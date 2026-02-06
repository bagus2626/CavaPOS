window.initInvoiceTab = function (accountId) {
  let currentInvoicePage = 1;
  const pageInvoiceLimit = $("#invoice-filter-limit").val() ?? 10;
  let activeInvoiceFilters = {
    statuses: [],
    client_types: [],
    payment_channels: [],
  };

  $(document).ready(function () {
    $("#daterange-invoice")
      .daterangepicker({
        autoUpdateInput: false,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        locale: { cancelLabel: "Clear", format: "YYYY/MM/DD" },
      })
      .on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
      })
      .on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
          picker.startDate.format("YYYY/MM/DD") +
            " - " +
            picker.endDate.format("YYYY/MM/DD")
        );
      });

    $(".date-type-select").on("click", function (e) {
      e.preventDefault();
      const key = $(this).data("date-key");
      const label = $(this).text();
      $("#current-date-key").val(key);
      $("#date-type-toggle").text(label);
    });

    $(".search-invoice-type-select").on("click", function (e) {
      e.preventDefault();
      const key = $(this).data("search-key");
      const label = $(this).text();
      $("#current-invoice-search-key").val(key);
      $("#search-invoice-type-toggle").text(label);
      $("#global-invoice-search-input").attr(
        "placeholder",
        `Search ${label}...`
      );
    });

    $("#popup-invoice-filter-options").on(
      "change",
      ".invoice-filter-checkbox",
      function () {
        const group = $(this).data("filter-group");
        const value = $(this).val();

        if (this.checked) {
          if (!activeInvoiceFilters[group].includes(value)) {
            activeInvoiceFilters[group].push(value);
          }
        } else {
          activeInvoiceFilters[group] = activeInvoiceFilters[group].filter(
            (item) => item !== value
          );
        }
        updateInvoiceFilterCount();
      }
    );

    $("#popup-invoice-filter-options").on("click", function (e) {
      e.stopPropagation();
    });

    $(document).on(
      "click",
      "#xendit-invoice-pagination .page-link",
      function (e) {
        e.preventDefault();

        const $this = $(this);
        if ($this.closest(".page-item").hasClass("disabled")) return;

        const cursorValue = $this.data("cursor");
        const direction = $this.data("direction");

        const paginationParams = {};
        if (direction === "before" && cursorValue) {
          paginationParams.before_id = cursorValue;
          currentInvoicePage = Math.max(1, currentInvoicePage - 1);
        } else if (direction === "after" && cursorValue) {
          paginationParams.after_id = cursorValue;
          currentInvoicePage++;
        }

        displayInvoiceData(getInvoiceFilterData(), paginationParams, "row");
      }
    );

    $("#clear-all-invoice-filters").on("click", function () {
      $(".invoice-filter-checkbox").prop("checked", false).trigger("change");
    });

    $("#apply-invoice-filter-btn").on("click", function (e) {
      e.preventDefault();
      currentInvoicePage = 1;
      $("#dropdownFilterInvoice").dropdown("hide");
      displayInvoiceData(getInvoiceFilterData(), {}, "table");
    });

    $("#reset-invoice-filter-btn").on("click", function (e) {
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
    $("#invoice-filter-count").text(count);
  }

  function resetAllInvoiceFilters() {
    $(".invoice-filter-checkbox").prop("checked", false).trigger("change");

    $("#daterange-invoice").val("");
    $("#current-date-key").val("created");
    $("#date-type-toggle").text("Created Date");
    $("#global-invoice-search-input").val("");
    $("#current-invoice-search-key").val("external_id");
    $("#search-invoice-type-toggle").text("External ID");

    displayInvoiceData(getInvoiceFilterData(), {}, "table");
  }

  function getInvoiceFilterData() {
    const filterPayload = {};
    const dateRangeVal = $("#daterange-invoice").val();
    const dateKey = $("#current-date-key").val();
    const searchKey = $("#current-invoice-search-key").val();
    const searchValue = $("#global-invoice-search-input").val().trim();

    for (const group in activeInvoiceFilters) {
      if (activeInvoiceFilters[group].length > 0) {
        filterPayload[group] = activeInvoiceFilters[group].join(",");
      }
    }

    if (dateRangeVal) {
      const dates = dateRangeVal.split(" - ");
      const startDate = moment(dates[0], "YYYY/MM/DD").toISOString();
      const endDate = moment(dates[1], "YYYY/MM/DD").endOf("day").toISOString();

      filterPayload[`${dateKey}_after`] = startDate;
      filterPayload[`${dateKey}_before`] = endDate;
    }

    if (searchValue) {
      filterPayload[searchKey] = searchValue;
    }

    filterPayload.limit = $("#invoice-filter-limit").val();

    return filterPayload;
  }

  function displayInvoiceData(
    filter_data = {},
    pagination_params = {},
    render_type = "table"
  ) {
    if (render_type === "table") {
      $(`#show-data-invoices`).html(`
                    <div class="text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                            <i class="fas fa-spinner fa-spin fa-lg"></i>
                            <div class="text-bold-500 mt-3">Loading data...</div>
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
      url: `/owner/user-owner/xen_platform/accounts/filter/invoices`,
      method: "GET",
      data: requestData,
      success: function (response) {
        if (render_type === "table") {
          $("#transactions-summary-div").html(response.summary);
          $(`#show-data-invoices`).html(response.invoiceTable);

          addInvoiceRowNumbers();
        } else if (render_type === "row") {
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
        $(`#show-data-invoices`).html(
          '<div class="text-center text-danger py-2">Gagal memuat data. Silakan coba lagi.</div>'
        );
      },
    });
  }

  function addInvoiceRowNumbers(appendMode = false, newRowsCount = 0) {
    const $tableBody = $("#xendit-invoice-table tbody");
    const $rows = $tableBody.find("tr").not(":has(td[colspan])");

    if ($rows.length === 0) return;

    $rows.find("td:first-child").remove();

    let startNumber = 1;
    if (appendMode) {
      const lastNumber = parseInt(
        $("#xendit-invoice-table tbody tr:last td:first").text()
      );
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

    const tableBody = $("#xendit-invoice-table tbody");
    const emptyRow = tableBody.find("td[colspan]");
    if (emptyRow.length > 0) {
      emptyRow.closest("tr").remove();
    }

    let rowsHtml = "";

    invoices.forEach((item) => {
      // Gunakan class yang sama dengan Blade template
      const badgeClasses = {
        PENDING: "badge-warning",
        PAID: "badge-info",
        SETTLED: "badge-success",
        EXPIRED: "badge-secondary",
        UNKNOWN: "badge-secondary",
      };
      const badgeClass = badgeClasses[item.status] || "badge-secondary";

      const createdDate = new Date(item.created);
      const tanggal = createdDate.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
      const waktu = createdDate.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
      });

      const formattedAmount = new Intl.NumberFormat("id-ID").format(
        item.amount ?? 0
      );

      rowsHtml += `
        <tr class="table-row invoice-clickable-row"
            data-invoice-id="${item.id ?? ""}"
            data-business-id="${item.user_id ?? ""}"
            style="cursor: pointer;">
            
            <td class="text-center"></td>
            
            <td>
                <div>
                    <span class="fw-600">${tanggal}</span><br>
                    <small class="text-muted">${waktu}</small>
                </div>
            </td>
            
            <td>
                <code class="text-monospace small">${
                  item.external_id ?? "-"
                }</code>
            </td>
            
            <td>
                <span class="text-secondary">${
                  item.customer?.email ?? "-"
                }</span>
            </td>
            
            <td>
                <span class="fw-500">${item.description ?? "-"}</span>
            </td>
            
            <td>
                <span class="fw-600">${
                  item.currency ?? "IDR"
                } ${formattedAmount}</span>
            </td>
            
            <td>
                <span class="badge-modern ${badgeClass}">${
        item.status ?? "UNKNOWN"
      }</span>
            </td>
            
            <td class="text-center">
                <div class="dropdown">
                    <span class="fas fa-ellipsis-v fa-lg font-medium-3 nav-hide-arrow cursor-pointer"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item copy-btn" data-copy-value="${
                          item.external_id ?? ""
                        }">
                            <i class="bx bx-copy-alt mr-1"></i> Copy Reference
                        </a>
                    </div>
                </div>
            </td>
        </tr>`;
    });

    tableBody.append(rowsHtml);
    return invoices.length;
  }

  function renderInvoicePagination(meta, totalRows = 0) {
    const paginationContainer = $("#xendit-invoice-pagination");
    paginationContainer.empty();

    const afterId = meta.after_id ?? null;
    const limit = meta.limit ?? 10;

    const isDisabled = !afterId || totalRows < limit;

    const paginationHtml = `
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center border-top pt-1">
            <nav aria-label="Navigasi halaman transaksi">
                <ul class="pagination mb-0 pagination">
                    <li class="page-item ${isDisabled ? "disabled" : ""} mt-2">
                        <a class="page-link fw-medium rounded-pill"
                           href="#"
                           data-cursor="${afterId ?? ""}"
                           data-direction="after">
                           <i class="fas fa-arrow-down mr-1"></i> Load More
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    `;
    paginationContainer.html(paginationHtml);
  }

  $(document).on("click", ".invoice-clickable-row", function (e) {
    const $row = $(this);

    if ($(e.target).closest(".dropdown, .dropdown-toggle, a").length > 0) {
      return;
    }

    const businessId = $row.data("business-id");
    const invoiceId = $row.data("invoice-id");

    if (invoiceId && businessId) {
      $row.addClass("loading");

      const colCount = $row.find("td").length;

      $row.html(`
                    <td colspan="${colCount}" class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 overlay">
                             <div class="fas fa-spinner fa-spin fas-lg" role="status"></div>
                            <span class="fw-medium ml-1">Memuat detail Invoice...</span>
                        </div>
                    </td>
                `);

      setTimeout(() => {
        window.location.href = `/owner/user-owner/xen_platform/accounts/invoice-detail/${invoiceId}`;
      }, 250);
    } else {
      alert("Missing business_id or payout_id for row click.");
    }
  });

  $(document).on("click", ".copy-btn", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const value = String($btn.data("copy-value") ?? "");
    const originalHtml = $btn.html();

    if (!value) {
      flashTemp($btn, "No value to copy", "#fff3cd", "#856404");
      return;
    }

    const onCopied = () => {
      $btn.html('<span class="text-success">Value copied to clipboard</span>');
      $btn.css({
        "background-color": "#e6f9ec",
        "border-radius": "6px",
      });

      setTimeout(() => {
        $btn.html(originalHtml);
        $btn.css({ "background-color": "", "border-radius": "" });
      }, 2500);
    };

    const onFail = (err) => {
      console.error("Copy failed:", err);
      flashTemp($btn, "Copy failed", "#f8d7da", "#721c24");
    };

    if (
      navigator.clipboard &&
      typeof navigator.clipboard.writeText === "function"
    ) {
      navigator.clipboard
        .writeText(value)
        .then(onCopied)
        .catch(() => {
          fallbackCopyTextToClipboard(value, (ok) =>
            ok ? onCopied() : onFail("fallback failed")
          );
        });
    } else {
      fallbackCopyTextToClipboard(value, (ok) =>
        ok ? onCopied() : onFail("no clipboard API")
      );
    }
  });

  function fallbackCopyTextToClipboard(text, cb) {
    try {
      const $txt = $("<textarea>");
      $txt.css({
        position: "fixed",
        top: 0,
        left: 0,
        width: "2em",
        height: "2em",
        padding: 0,
        border: "none",
        outline: "none",
        boxShadow: "none",
        background: "transparent",
      });
      $txt.val(text);
      $("body").append($txt);
      $txt[0].select();
      $txt[0].setSelectionRange(0, $txt[0].value.length);
      const success = document.execCommand("copy");
      $txt.remove();
      cb(Boolean(success));
    } catch (err) {
      cb(false);
    }
  }

  function flashTemp(
    $btn,
    message,
    bgColor = "#fff3cd",
    textColor = "#856404"
  ) {
    const original = $btn.html();
    $btn.html(`<span style="color:${textColor}">${message}</span>`);
    $btn.css({ "background-color": bgColor, "border-radius": "6px" });
    setTimeout(() => {
      $btn.html(original);
      $btn.css({ "background-color": "", "border-radius": "" });
    }, 2500);
  }
};
