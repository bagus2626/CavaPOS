(function () {
  // ========= Helpers =========
  const qs = (sel, root = document) => (root ? root.querySelector(sel) : null);
  const qsa = (sel, root = document) =>
    root ? [...root.querySelectorAll(sel)] : [];
  const rupiah = (x) => "Rp " + Number(x || 0).toLocaleString("id-ID");

  // ========= DETAIL MODAL =========
  (function setupDetailModal() {
    const detailModal = qs("#detailModal");
    const detailContent = qs("#detailContent");
    if (!detailModal || !detailContent) return;

    // Delegasi klik tombol detail
    document.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-detail-btn]");
      if (!btn) return;

      e.preventDefault();

      // Ambil URL detail (prioritas: data-detail-url, fallback: href, atau pakai id)
      let url = btn.getAttribute("data-detail-url") || btn.getAttribute("href");

      if (!url) {
        const id = btn.getAttribute("data-order-id");
        if (id)
          url = `/employee/cashier/order-detail/${encodeURIComponent(id)}`;
      }
      if (!url) return;

      // Tampilkan modal + loading
      detailContent.innerHTML = '<p class="text-gray-400">Memuat…</p>';
      detailModal.classList.remove("hidden");

      fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then((res) => {
          if (!res.ok) throw new Error("Network error");
          return res.json();
        })
        .then((order) => {
          const paymentNote = (order?.payment?.note || "").toString().trim();

          let html = `
            <div class="space-y-1">
              <p><span class="text-gray-500">Kode:</span> <span class="font-semibold">${order.booking_order_code}</span></p>
              <p><span class="text-gray-500">Nama:</span> ${order.customer_name ?? "-"}</p>
              <p><span class="text-gray-500">Meja:</span> ${order.table?.table_no ?? "-"}</p>
              <p><span class="text-gray-500">Total:</span> <span class="font-semibold">${rupiah(order.total_order_value)}</span></p>
              <p>
                <span class="text-gray-500">Status:</span>
                <span class="${
                  order.order_status === "UNPAID"
                    ? "text-rose-600 font-semibold"
                    : order.order_status === "PROCESSED"
                    ? "text-blue-600 font-semibold"
                    : order.order_status === "SERVED"
                    ? "text-emerald-700 font-semibold"
                    : "text-gray-700"
                }">${order.order_status}</span>
              </p>
            </div>
          `;

          // ✅ TAMBAHAN: tampilkan payment note jika ada
          if (paymentNote) {
            html += `
              <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                <p class="text-xs font-semibold text-amber-800 mb-1">Catatan Pembayaran</p>
                <p class="text-sm text-amber-900 whitespace-pre-line">${paymentNote}</p>
              </div>
            `;
          }

          html += `
            <hr class="my-3">
            <h4 class="font-semibold mb-2">Items</h4>
            <ul class="space-y-3">
          `;

          (order.order_details || []).forEach((it) => {
            html += `
              <li class="border-b pb-2">
                <div class="font-medium text-choco">
                  ${it.partner_product && it.product_name ? it.product_name : "Produk"} × ${it.quantity}
                  = ${rupiah((it.base_price - (it.promo_amount ?? 0)) * it.quantity)}
                  ${it.customer_note ? `<span class="text-xs text-gray-500 italic">(${it.customer_note})</span>` : ""}
                </div>
            `;
            if (it.order_detail_options && it.order_detail_options.length) {
              html += `<ul class="list-none ml-4 mt-1 space-y-1 text-sm text-gray-600">`;
              it.order_detail_options.forEach((opt) => {
                html += `<li>- ${
                  opt.option && opt.option.parent && opt.option.parent.name ? opt.option.parent.name : "Opsi"
                }: ${opt.option?.name ?? "-"} × ${it.quantity} = ${rupiah(opt.price * it.quantity)} </li>`;
              });
              html += `</ul>`;
            }
            html += `</li>`;
          });

          html += `</ul>`;
          detailContent.innerHTML = html;
        })
        .catch((err) => {
          console.error(err);
          detailContent.innerHTML =
            '<p class="text-rose-600">Gagal memuat data.</p>';
        });
    });

    // Tutup modal
    qsa("[data-detail-close]", detailModal).forEach((b) =>
      b.addEventListener("click", () => detailModal.classList.add("hidden"))
    );
    detailModal.addEventListener("click", (e) => {
      if (e.target === detailModal) detailModal.classList.add("hidden");
    });
  })();

  // ========= CASH MODAL =========
  (function setupCashModal() {
    const cashModal = qs("#cashModal");
    if (!cashModal) return;

    const form = qs("#cashForm");
    const printReceiptBtn = qs("[data-print-receipt]", cashModal);
    const closeBtns = qsa("[data-cash-close]", cashModal);
    const detailItem = qs("#detailItemCash");

    const orderIdEl = qs("#cashOrderId");
    const orderCodeEl = qs("#cashOrderCode");
    const orderNameEl = qs("#cashOrderName");
    const orderTotalEl = qs("#cashOrderTotal");
    const orderTotalRawEl = qs("#cashOrderTotalRaw");
    const paidInput = qs("#paidAmount");
    const changeDisplay = qs("#changeDisplay");
    const changeAmount = qs("#changeAmount");
    const errorBox = qs("#cashError");

    const paymentInfoBox = qs("#paymentInfoBox");
    const payInfoType = qs("#payInfoType");
    const payInfoProvider = qs("#payInfoProvider");
    const payInfoAccName = qs("#payInfoAccName");
    const payInfoAccNoWrap = qs("#payInfoAccNoWrap");
    const payInfoAccNo = qs("#payInfoAccNo");
    const payInfoProofWrap = qs("#payInfoProofWrap");
    const payInfoProofLink = qs("#payInfoProofLink");
    const payInfoProofImg = qs("#payInfoProofImg");
    const payInfoProofPreview = qs("#payInfoProofPreview");
    const payInfoProofPdfHint = qs("#payInfoProofPdfHint");



    // Ambil CSRF dari meta
    const CSRF =
      document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

    document.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-cash-btn]");
      if (!btn) return;

      const id     = btn.getAttribute("data-order-id");
      const code   = btn.getAttribute("data-order-code");
      const name   = btn.getAttribute("data-order-name");
      const total  = Number(btn.getAttribute("data-order-total") || 0);
      const action = btn.getAttribute("data-cash-url");
      const url    = btn.getAttribute("data-cash-get-url");

      if (url && detailItem) {
        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then((res) => {
            if (!res.ok) throw new Error("Network error");
            return res.json();
          })
          .then((order) => {
            let html = `
              <p><strong>Order Status:</strong>
                <span class="${
                  order.order_status === "UNPAID"
                    ? "text-red-600 font-semibold"
                    : order.order_status === "PROCESSED"
                    ? "text-blue-600 font-semibold"
                    : order.order_status === "SERVED"
                    ? "text-green-600 font-semibold"
                    : "text-gray-600"
                }">${order.order_status}</span>
              </p>
              <hr class="my-2">
              <h3 class="font-semibold mb-2">Items:</h3>
              <ul class="list-none space-y-3">
            `;

            (order.order_details || []).forEach((it) => {
              html += `
                <li class="border-b pb-2">
                  <div class="font-medium text-choco">
                    ${
                      it.partner_product && it.partner_product.name
                        ? it.partner_product.name
                        : "Produk"
                    } × ${it.quantity}
                    = ${rupiah((it.base_price - (it.promo_amount ?? 0)) * it.quantity)}
                    ${
                      it.customer_note
                        ? `<span class="text-sm text-gray-500 italic">(${it.customer_note})</span>`
                        : ""
                    }
                  </div>
              `;
              if (it.order_detail_options && it.order_detail_options.length) {
                html += `<ul class="list-none ml-4 mt-1 space-y-1 text-sm text-gray-600">`;
                it.order_detail_options.forEach((opt) => {
                  html += `<li>- ${
                    opt.option && opt.option.parent && opt.option.parent.name
                      ? opt.option.parent.name
                      : "Opsi"
                  }: ${opt.option?.name ?? "-"} × ${it.quantity} = ${rupiah(opt.price * it.quantity)}</li>`;
                });
                html += `</ul>`;
              }
              html += `</li>`;
            });

            html += `</ul>`;
            detailItem.innerHTML = html;
            // === Render payment info khusus PAYMENT REQUEST ===
            const pr = order.payment_request;

            if (
              paymentInfoBox &&
              order.order_status === "PAYMENT REQUEST" &&
              pr
            ) {
              paymentInfoBox.classList.remove("hidden");

              if (payInfoType) payInfoType.textContent = pr.payment_type_label || "-";
              if (payInfoProvider) payInfoProvider.textContent = pr.manual_provider_name || "-";
              if (payInfoAccName) payInfoAccName.textContent = pr.manual_provider_account_name || "-";

              // No akun: tampilkan hanya kalau ada
              const accNo = pr.manual_provider_account_no || "";
              if (payInfoAccNoWrap && payInfoAccNo) {
                if (accNo) {
                  payInfoAccNoWrap.classList.remove("hidden");
                  payInfoAccNo.textContent = accNo;
                } else {
                  payInfoAccNoWrap.classList.add("hidden");
                }
              }

              // Bukti bayar: url bisa full atau relative
              const proof = pr.manual_payment_image || "";
              if (payInfoProofWrap && payInfoProofLink) {
                if (proof) {
                  payInfoProofWrap.classList.remove("hidden");

                  const proofUrl = proof.startsWith("http")
                    ? proof
                    : `/storage/${proof.replace(/^\/?storage\/?/, "")}`;

                  payInfoProofLink.href = proofUrl;

                  const isPdf = proofUrl.toLowerCase().endsWith(".pdf");

                  // Reset dulu (biar tidak kebawa dari order sebelumnya)
                  if (payInfoProofPreview) payInfoProofPreview.classList.add("hidden");
                  if (payInfoProofPdfHint) payInfoProofPdfHint.classList.add("hidden");
                  if (payInfoProofImg) {
                    payInfoProofImg.classList.remove("hidden");
                    payInfoProofImg.removeAttribute("src");
                  }

                  if (isPdf) {
                    // PDF: tidak preview, hanya hint
                    if (payInfoProofPreview) payInfoProofPreview.classList.add("hidden");
                    if (payInfoProofPdfHint) payInfoProofPdfHint.classList.remove("hidden");
                    if (payInfoProofImg) payInfoProofImg.classList.add("hidden");
                  } else {
                    // Image: tampilkan preview besar
                    if (payInfoProofImg) payInfoProofImg.src = proofUrl;
                    if (payInfoProofPreview) payInfoProofPreview.classList.remove("hidden");
                  }
                } else {
                  payInfoProofWrap.classList.add("hidden");
                }
              }
              setPaidToTotal();

            } else {
              // kalau tidak memenuhi syarat, sembunyikan panel
              if (paymentInfoBox) paymentInfoBox.classList.add("hidden");
            }

          })
          .catch(() => {
            detailItem.innerHTML = '<p class="text-red-500">Gagal memuat data.</p>';
          });
      }

      if (orderIdEl) orderIdEl.value = id || "";
      if (orderCodeEl) orderCodeEl.textContent = code || "";
      if (orderNameEl) orderNameEl.textContent = name || "";
      if (orderTotalEl) orderTotalEl.textContent = rupiah(total);
      if (orderTotalRawEl) orderTotalRawEl.value = total;

      if (paidInput) paidInput.value = "";
      if (changeDisplay) changeDisplay.value = "Rp 0";
      if (changeAmount) changeAmount.value = 0;
      if (errorBox) {
        errorBox.classList.add("hidden");
        errorBox.textContent = "";
      }

      if (form && action && id) {
        form.setAttribute("action", action.replace("__ID__", encodeURIComponent(id)));
      }

      cashModal.classList.remove("hidden");
      if (paidInput) paidInput.focus();
    });


    // Tutup modal
    closeBtns.forEach((b) =>
      b.addEventListener("click", () => cashModal.classList.add("hidden"))
    );
    cashModal.addEventListener("click", (e) => {
      if (e.target === cashModal) cashModal.classList.add("hidden");
    });

    // Hitung kembalian realtime
    function recalcChange() {
      if (!orderTotalRawEl || !paidInput || !changeDisplay || !changeAmount)
        return;
      const total = Number(orderTotalRawEl.value || 0);
      const paid = Number(paidInput.value || 0);
      const change = Math.max(0, paid - total);
      changeDisplay.value = rupiah(change);
      changeAmount.value = change;
    }
    function setPaidToTotal() {
      if (!orderTotalRawEl || !paidInput) return;

      const total = Number(orderTotalRawEl.value || 0);

      // isi otomatis
      paidInput.value = String(total);

      // hitung kembalian (trigger recalcChange)
      paidInput.dispatchEvent(new Event("input", { bubbles: true }));
    }

    if (paidInput) paidInput.addEventListener("input", recalcChange);

    // Print langsung dari modal
    if (printReceiptBtn) {
      printReceiptBtn.addEventListener("click", function () {
        const id = orderIdEl?.value;
        if (!id) return console.warn("Order ID kosong, tidak bisa cetak nota");
        const url = `/employee/cashier/print-receipt/${encodeURIComponent(id)}`;
        window.open(url, "_blank", "noopener,noreferrer");
      });
    }

    // === HANYA BAGIAN INI YANG DIUBAH → submit via AJAX ===
    if (form) {
      form.addEventListener("submit", async function (e) {
        e.preventDefault(); // jangan submit normal

        if (!orderTotalRawEl || !paidInput || !errorBox) return;

        const total = Number(orderTotalRawEl.value || 0);
        const paid = Number(paidInput.value || 0);
        if (paid < total) {
          errorBox.textContent =
            "Nominal uang diterima kurang dari total tagihan.";
          errorBox.classList.remove("hidden");
          paidInput.focus();
          return;
        }

        errorBox.classList.add("hidden");
        errorBox.textContent = "";

        const id = orderIdEl?.value;
        const postUrl = form.getAttribute("action");
        if (!postUrl || !id) {
          console.warn("postUrl / order id tidak tersedia.");
          return;
        }

        // disable tombol submit sementara
        const submitBtn = form.querySelector("[type=submit]");
        const originalText = submitBtn ? submitBtn.innerHTML : "";
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = "Memproses...";
        }

        try {
          const fd = new FormData(form);
          const res = await fetch(postUrl, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": CSRF,
              "X-Requested-With": "XMLHttpRequest",
              Accept: "application/json",
            },
            body: fd,
          });

          if (!res.ok) {
            if (res.status === 422) {
              const data = await res.json().catch(() => ({}));
              const msg = data?.message || "Validasi gagal.";
              errorBox.textContent = msg;
              errorBox.classList.remove("hidden");
              return;
            } else {
              const text = await res.text();
              throw new Error(
                `Gagal memproses pembayaran. (${res.status}) ${text}`
              );
            }
          }

          // Sukses → buka struk, tutup modal, reset
          const printUrl = `/employee/cashier/print-receipt/${encodeURIComponent(
            id
          )}`;
          window.open(printUrl, "_blank", "noopener,noreferrer");
          cashModal.classList.add("hidden");
          form.reset();

          if (window.Swal) {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: "Pembayaran berhasil diproses!",
              showConfirmButton: true, // pastikan tombol OK tampil
              allowOutsideClick: false, // tidak bisa ditutup dengan klik luar
              allowEscapeKey: false, // tidak bisa ditutup dengan ESC
            }).then((result) => {
              if (result.isConfirmed) {
                // Optional: refresh daftar
                location.reload();
              }
            });
          }
        } catch (err) {
          console.error(err);
          errorBox.textContent =
            err.message || "Terjadi kesalahan saat memproses pembayaran.";
          errorBox.classList.remove("hidden");
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        }
      });
    }
  })();

  // ========= SERVED MODAL =========
  (function setupServedModal() {
    // 1) Delegasi klik: selalu aktif walau modal baru muncul setelah AJAX
    document.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-process-btn]");
      if (!btn) return;

      // 2) Ambil element modal saat tombol diklik (lazy lookup)
      const servedModal    = document.querySelector("#servedModal");
      const detailItem     = servedModal?.querySelector("#detailItem");
      const form           = servedModal?.querySelector("#servedForm");
      const orderIdEl      = servedModal?.querySelector("#servedOrderId");
      const orderCodeEl    = servedModal?.querySelector("#servedOrderCode");
      const orderNameEl    = servedModal?.querySelector("#servedOrderName");
      const orderTableEl   = servedModal?.querySelector("#servedOrderTable");
      const orderTotalEl   = servedModal?.querySelector("#servedOrderTotal");
      const orderTotalRawEl= servedModal?.querySelector("#servedOrderTotalRaw");
      const errorBox       = servedModal?.querySelector("#servedError");

      if (!servedModal || !detailItem || !form) {
        console.warn("servedModal elements not found (maybe belum ter-render).");
        return;
      }

      const id    = btn.getAttribute("data-order-id");
      const code  = btn.getAttribute("data-order-code");
      const name  = btn.getAttribute("data-order-name");
      const total = Number(btn.getAttribute("data-order-total") || 0);
      const base  = btn.getAttribute("data-order-url");
      const table = btn.getAttribute("data-order-table");
      const url   = btn.getAttribute("data-order-get-url");

      // reset error
      if (errorBox) { errorBox.classList.add("hidden"); errorBox.textContent = ""; }

      // isi header modal
      if (orderIdEl) orderIdEl.value = id || "";
      if (orderCodeEl) orderCodeEl.textContent = code || "";
      if (orderNameEl) orderNameEl.textContent = name || "";
      if (orderTableEl) orderTableEl.textContent = table || "-";
      if (orderTotalEl) orderTotalEl.textContent = rupiah(total);
      if (orderTotalRawEl) orderTotalRawEl.value = total;

      if (base && form && id) form.setAttribute("action", base.replace("__ID__", encodeURIComponent(id)));

      // load detail items
      if (url) {
        detailItem.innerHTML = `<p class="text-gray-400">Memuat…</p>`;
        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then((res) => res.ok ? res.json() : Promise.reject("Network error"))
          .then((order) => {
            let html = `<h3 class="font-semibold mb-2">Items:</h3><ul class="list-none space-y-2">`;
            (order.order_details || []).forEach((it) => {
              html += `<li class="border-b pb-2">
                <div class="font-medium text-choco">
                  ${(it.partner_product?.name || "Produk")} × ${it.quantity}
                  = ${rupiah((it.base_price - (it.promo_amount ?? 0)) * it.quantity)}
                  ${it.customer_note ? `<span class="text-sm text-gray-500 italic">(${it.customer_note})</span>` : ""}
                </div>
              </li>`;
            });
            html += `</ul>`;
            detailItem.innerHTML = html;
          })
          .catch(() => {
            detailItem.innerHTML = `<p class="text-red-500">Gagal memuat data.</p>`;
          });
      }

      // tampilkan modal
      servedModal.classList.remove("hidden");
    });

    // 3) Close modal juga pakai delegasi (biar aman kalau modal muncul via AJAX)
    document.addEventListener("click", (e) => {
      const btnClose = e.target.closest("[data-served-close]");
      if (!btnClose) return;
      const servedModal = document.querySelector("#servedModal");
      servedModal?.classList.add("hidden");
    });

    document.addEventListener("click", (e) => {
      const servedModal = document.querySelector("#servedModal");
      if (!servedModal) return;
      if (e.target === servedModal) servedModal.classList.add("hidden");
    });
  })();


  (function bindDynamicButtons() {
    document.addEventListener("click", async (e) => {
      // ===============================
      // PROSES (PAID -> PROCESSED)
      // ===============================
      const btnProcess = e.target.closest("[data-turn-to-process-btn]");
      if (btnProcess) {
        const orderId   = btnProcess.getAttribute("data-order-id");
        const orderName = btnProcess.getAttribute("data-order-name");
        const baseUrl   = btnProcess.getAttribute("data-order-url");
        const processUrl = baseUrl?.replace("__ID__", orderId);

        if (!orderId || !processUrl) return;

        const { isConfirmed, value: payload } = await Swal.fire({
          icon: "question",
          title: "Proses order ini?",
          text: `Anda akan memproses Order (${orderName}). Lanjutkan?`,
          showCancelButton: true,
          confirmButtonText: "Ya, proses",
          cancelButtonText: "Batal",
          reverseButtons: true,
          showLoaderOnConfirm: true,
          allowOutsideClick: () => !Swal.isLoading(),
          preConfirm: async () => {
            try {
              const response = await fetch(processUrl, {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                  Accept: "application/json",
                  "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
                },
                body: JSON.stringify({ order_id: orderId }),
              });

              const raw = await response.text();
              let json = null;
              try { json = JSON.parse(raw); } catch (_) {}

              if (!response.ok) {
                const msg = (json && (json.message || json.error)) || "Gagal memproses order.";
                Swal.showValidationMessage(msg);
                return false;
              }

              return json || { status: "ok", message: "Order berhasil diproses." };
            } catch (err) {
              Swal.showValidationMessage(err?.message || "Terjadi kesalahan jaringan.");
              return false;
            }
          },
        });

        if (!isConfirmed) return;

        await Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: payload?.message || "Order berhasil diproses.",
          confirmButtonText: "OK",
        });

        if (payload?.redirect_url) window.location.href = payload.redirect_url;
        else location.reload();

        return; // stop
      }

      // ===============================
      // BATAL PROSES (PROCESSED -> PAID)
      // ===============================
      const btnCancel = e.target.closest("[data-turn-to-paid-btn]");
      if (btnCancel) {
        const orderId   = btnCancel.getAttribute("data-order-id");
        const orderName = btnCancel.getAttribute("data-order-name");
        const baseUrl   = btnCancel.getAttribute("data-order-url");
        const processUrl = baseUrl?.replace("__ID__", orderId);

        if (!orderId || !processUrl) return;

        const { isConfirmed, value: payload } = await Swal.fire({
          icon: "question",
          title: "Batalkan proses order ini?",
          text: `Anda akan membatalkan memproses order (${orderName}). Lanjutkan?`,
          showCancelButton: true,
          confirmButtonText: "Ya, batalkan",
          cancelButtonText: "Kembali",
          reverseButtons: true,
          showLoaderOnConfirm: true,
          allowOutsideClick: () => !Swal.isLoading(),
          preConfirm: async () => {
            try {
              const response = await fetch(processUrl, {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                  Accept: "application/json",
                  "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
                },
                body: JSON.stringify({ order_id: orderId }),
              });

              const raw = await response.text();
              let json = null;
              try { json = JSON.parse(raw); } catch (_) {}

              if (!response.ok) {
                const msg = (json && (json.message || json.error)) || "Gagal membatalkan proses.";
                Swal.showValidationMessage(msg);
                return false;
              }

              return json || { status: "ok", message: "Proses berhasil dibatalkan." };
            } catch (err) {
              Swal.showValidationMessage(err?.message || "Terjadi kesalahan jaringan.");
              return false;
            }
          },
        });

        if (!isConfirmed) return;

        await Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: payload?.message || "Proses berhasil dibatalkan.",
          confirmButtonText: "OK",
        });

        if (payload?.redirect_url) window.location.href = payload.redirect_url;
        else location.reload();
      }
    });
  })();

})();
