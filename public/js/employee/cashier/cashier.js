console.log("cashier.js loaded");
// cashier.js (eksternal - tidak ada Blade tag di sini)
(function () {
  function setBadge(id, n) {
    const el = document.getElementById(id);
    if (!el) return;
    if (n <= 0) {
      el.classList.add("hidden");
      el.textContent = "0";
    } else {
      el.classList.remove("hidden");
      el.textContent = String(n);
    }
  }

  function prependItem(listId, data) {
    const list = document.getElementById(listId);
    if (!list) return;

    const placeholder = list.querySelector("li.text-gray-500");
    if (placeholder) placeholder.remove();

    const code = data.code ?? "#" + (data.id ?? "");
    const customer = data.customer ?? "—";
    const total = Number(data.total ?? 0).toLocaleString("id-ID");
    const status = data.order_status ?? "-";
    const created = data.created_at ?? "";

    // tentukan tab tujuan berdasarkan status
    const statusToTab = {
      UNPAID: "pembayaran",
      PROCESSED: "proses",
      PAID: "proses", // optional kalau mau
    };

    const targetTab = statusToTab[status] || "pembelian";

    const li = document.createElement("li");
    li.className = "p-3 hover:bg-soft-choco/10";
    li.innerHTML = `
            <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-gray-900">${code}</p>
                <p class="text-xs text-gray-600">${customer} \u00B7 Rp ${total} \u00B7 ${status}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">${created}</p>
            </div>
            <a href="#"
                class="js-goto-order shrink-0 text-xs px-2 py-1 rounded border border-choco/20 text-choco hover:bg-soft-choco/10"
                data-order-id="${data.id ?? ""}"
                data-target-tab="${targetTab}">Lihat</a>
            </div>`;
    list.prepend(li);
  }

  function formatRupiah(num) {
    return new Intl.NumberFormat("id-ID").format(num || 0);
  }

  function refreshMetrics() {
    if (!window.CASHIER_METRICS_URL) {
      console.warn("CASHIER_METRICS_URL belum diset");
      return;
    }

    const qs = new URLSearchParams(window.location.search);

    fetch(window.CASHIER_METRICS_URL + "?" + qs.toString(), {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((r) => r.json())
      .then((data) => {
        const elTotal   = document.getElementById("metric-total-order");
        const elUnpaid  = document.getElementById("metric-unpaid-cash");
        const elPaid    = document.getElementById("metric-paid-cash");
        const elQris    = document.getElementById("metric-qris-paid");
        const elProcess = document.getElementById("metric-on-process");
        const elRev     = document.getElementById("metric-revenue");

        if (elTotal)   elTotal.textContent   = formatRupiah(data.total_order);
        if (elUnpaid)  elUnpaid.textContent  = formatRupiah(data.unpaid_cash);
        if (elPaid)    elPaid.textContent    = formatRupiah(data.paid_cash);
        if (elQris)    elQris.textContent    = formatRupiah(data.qris_paid);
        if (elProcess) elProcess.textContent = formatRupiah(data.on_process);
        if (elRev)     elRev.textContent     = "Rp " + formatRupiah(data.revenue);

        // 🔹 UPDATE BADGE TAB
        const bdPembayaran = document.getElementById("tab-badge-pembayaran");
        const bdProses     = document.getElementById("tab-badge-proses");
        const bdSelesai    = document.getElementById("tab-badge-selesai");

        if (bdPembayaran && typeof data.badge_pembayaran !== "undefined") {
          bdPembayaran.textContent = formatRupiah(data.badge_pembayaran);
        }
        if (bdProses && typeof data.badge_proses !== "undefined") {
          bdProses.textContent = formatRupiah(data.badge_proses);
        }
        if (bdSelesai && typeof data.badge_selesai !== "undefined") {
          bdSelesai.textContent = formatRupiah(data.badge_selesai);
        }

        // 🔹 UPDATE ANGKA "XX order" di panel kiri
        const pendingLabel = document.getElementById("pending-cash-count");
        if (pendingLabel && typeof data.pending_cash_count !== "undefined") {
          pendingLabel.textContent = `${formatRupiah(data.pending_cash_count)} order`;
        }
      })
      .catch((err) => {
        console.error("Gagal refresh metrics:", err);
      });
  }


  function startListener() {
    if (!window.Echo) {
      console.error("Echo belum siap");
      return;
    }

    const pid = window.CASHIER_PARTNER_ID;
    if (!pid) {
      console.warn("CASHIER_PARTNER_ID kosong");
      return;
    }

    const channelName = `partner.${pid}.orders`;
    console.log("Subscribe:", channelName);

    const bell = new Audio("/sounds/buzzer-or-wrong-answer-20582.mp3");
    const enableBtn = document.getElementById("enable-sound");
    let count = 0;

    const channel = window.Echo.private(channelName)
      .subscribed(() => {
        console.log("✅ Subscribed ke", channelName);
        // catatan: di Pusher Console, nama channel terlihat sebagai: private-partner.<pid>.orders
      })
      .error((err) => {
        console.error("❌ Channel error:", err);
      });

    channel.listen(".OrderCreated", (e) => {
      console.log("Payload OrderCreated diterima:", e);

      // bunyikan
      bell.play().catch(() => enableBtn?.classList.remove("hidden"));

      // update panel notifikasi
      prependItem("notif-list-desktop", e || {});
      prependItem("notif-list-mobile", e || {});

      // badge
      count += 1;
      setBadge("notif-badge-desktop", count);
      setBadge("notif-badge-mobile", count);

      refreshMetrics();
    });

    // user gesture untuk mengizinkan audio (opsional)
    enableBtn?.addEventListener("click", () => {
      bell
        .play()
        .then(() => enableBtn.classList.add("hidden"))
        .catch(console.warn);
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (window.Echo) startListener();
    else window.addEventListener("echo:ready", startListener, { once: true });
  });
})();

function goToOrder(targetTab, orderId) {
  if (!window.CASHIER) return;
  // aktifkan tab
  window.CASHIER.setActiveTab(targetTab);

  // muat konten tab, lalu scroll ke item
  window.CASHIER.loadTab(targetTab, () => {
    const el = document.getElementById(`order-item-${orderId}`);
    if (el) {
      el.classList.add(
        "ring-2",
        "ring-amber-400",
        "ring-offset-2",
        "rounded-xl"
      );
      el.scrollIntoView({ behavior: "smooth", block: "center" });
      setTimeout(
        () => el.classList.remove("ring-2", "ring-amber-400", "ring-offset-2"),
        2000
      );
    } else {
      // fallback ringan
      console.warn(
        "Order tidak ditemukan di tab:",
        targetTab,
        "orderId:",
        orderId
      );
    }
  });
}

function bindGotoClicks(containerId) {
  const ul = document.getElementById(containerId);
  if (!ul) return;
  ul.addEventListener("click", (e) => {
    const a = e.target.closest(".js-goto-order");
    if (!a) return;
    e.preventDefault();
    const orderId = a.getAttribute("data-order-id");
    const target = a.getAttribute("data-target-tab") || "pembayaran";
    if (!orderId) return;
    goToOrder(target, orderId);
  });
}

// panggil sekali saat DOM ready:
document.addEventListener("DOMContentLoaded", () => {
  bindGotoClicks("notif-list-desktop");
  bindGotoClicks("notif-list-mobile");
});
