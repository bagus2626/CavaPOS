// kitchen.js (eksternal - tidak ada Blade tag di sini)
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
            PAID: "selesai", // optional kalau mau
        };

        const targetTab = statusToTab[status] || "pembelian";

        const li = document.createElement("li");
        li.className = "p-3 hover:bg-soft-choco/10";
        li.innerHTML = `
            <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-gray-900">${code}</p>
                <p class="text-xs text-gray-600">${customer} · Rp ${total} · ${status}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">${created}</p>
            </div>
            <a href="#"
                class="js-goto-order shrink-0 text-xs px-2 py-1 rounded border border-choco/20 text-choco hover:bg-soft-choco/10"
                data-order-id="${data.id ?? ""}"
                data-target-tab="${targetTab}">Lihat</a>
            </div>`;
        list.prepend(li);
    }

    function startListener() {
        if (!window.Echo) {
            console.error("Echo belum siap");
            return;
        }

        const pid = window.KITCHEN_PARTNER_ID;
        if (!pid) {
            console.warn("KITCHEN_PARTNER_ID kosong");
            return;
        }

        const channel = `partner.${pid}.orders`;
        console.log("Subscribe:", channel);

        const bell = new Audio("/sounds/buzzer-or-wrong-answer-20582.mp3");
        const enableBtn = document.getElementById("enable-sound");
        let count = 0;

        window.Echo.private(channel).listen(".OrderCreated", (e) => {
            console.log("Payload OrderCreated diterima:", e);
            // bunyikan
            bell.play().catch(() => enableBtn?.classList.remove("hidden"));

            // update panel notifikasi (desktop & mobile)
            prependItem("notif-list-desktop", e || {});
            prependItem("notif-list-mobile", e || {});

            // badge
            count += 1;
            setBadge("notif-badge-desktop", count);
            setBadge("notif-badge-mobile", count);
        });
    }

    // Tunggu DOM siap, lalu mulai ketika Echo siap
    document.addEventListener("DOMContentLoaded", () => {
        if (window.Echo) startListener();
        else
            window.addEventListener("echo:ready", startListener, {
                once: true,
            });
    });
})();

function goToOrder(targetTab, orderId) {
    if (!window.KITCHEN) return;
    // aktifkan tab
    window.KITCHEN.setActiveTab(targetTab);

    // muat konten tab, lalu scroll ke item
    window.KITCHEN.loadTab(targetTab, () => {
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
                () =>
                    el.classList.remove(
                        "ring-2",
                        "ring-amber-400",
                        "ring-offset-2"
                    ),
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
