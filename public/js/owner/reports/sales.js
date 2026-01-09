/**
 * Sales Report Charts
 * File: public/js/owner/reports/sales.js
 */

// ==================== KONFIGURASI WARNA ====================
const colorScheme = {
  primary: "rgb(174, 21, 4)",
  primaryLight: "rgba(174, 21, 4, 0.1)",
  success: "#10b981",
  danger: "#ef4444",
  grid: "rgba(0, 0, 0, 0.05)",
  text: "#4B5563",
  tooltipBg: "rgba(0, 0, 0, 0.8)",
  categories: [
    "#ae1504",
    "#d91e0f",
    "#ff4d3d",
    "#ff7a6b",
    "#ffa199",
    "#ef4444",
    "#f87171",
    "#fca5a5",
    "#fecaca",
    "#fee2e2",
  ],
  payment: {
    cash: "#10b981",
    qris: "#3b82f6",
    others: "#6b7280",
  },
};

// ==================== FUNGSI HELPER ====================

function sanitizeNumber(value) {
  const num = parseFloat(value);
  return !isNaN(num) && isFinite(num) ? num : 0;
}

function formatCompactNumber(value) {
  if (value >= 1000000) {
    return "Rp " + (value / 1000000).toFixed(1) + "jt";
  } else if (value >= 1000) {
    return "Rp " + (value / 1000).toFixed(0) + "rb";
  }
  return "Rp " + value.toLocaleString("id-ID");
}

function calculatePercentage(value, total) {
  const sanitizedValue = sanitizeNumber(value);
  const sanitizedTotal = sanitizeNumber(total);

  if (sanitizedTotal === 0) {
    return "0.0";
  }

  return ((sanitizedValue / sanitizedTotal) * 100).toFixed(1);
}

// ==================== LINE CHART: REVENUE TREND ====================

function initRevenueTrendChart() {
  const canvas = document.getElementById("revenueTrendChart");
  if (!canvas) {
    console.warn("Canvas revenueTrendChart tidak ditemukan");
    return;
  }

  if (
    typeof revenueChartData === "undefined" ||
    !revenueChartData.labels ||
    revenueChartData.labels.length === 0
  ) {
    canvas.parentElement.innerHTML =
      '<div class="d-flex align-items-center justify-content-center h-100">' +
      '<p class="text-center" style="color: #999;">Tidak ada data pendapatan untuk periode ini.</p>' +
      "</div>";
    return;
  }

  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "line",
    data: {
      labels: revenueChartData.labels,
      datasets: [
        {
          label: "Penjualan (Rp)",
          data: revenueChartData.data.map((v) => sanitizeNumber(v)),
          borderColor: colorScheme.primary,
          backgroundColor: colorScheme.primaryLight,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: colorScheme.primary,
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: colorScheme.tooltipBg,
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: "bold",
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function (context) {
              return "Rp " + context.parsed.y.toLocaleString("id-ID");
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colorScheme.grid,
          },
          ticks: {
            font: {
              size: 11,
            },
            callback: function (value) {
              return formatCompactNumber(value);
            },
          },
        },
        x: {
          grid: {
            display: false,
          },
          ticks: {
            font: {
              size: 11,
            },
          },
        },
      },
    },
  });
}

// ==================== DOUGHNUT CHART: CATEGORY (SOLD QUANTITY) ====================

function initCategoryChart() {
  const canvas = document.getElementById("categoryChart");
  if (!canvas) {
    console.warn("Canvas categoryChart tidak ditemukan");
    return;
  }

  if (
    typeof categoryChartData === "undefined" ||
    !categoryChartData.labels ||
    categoryChartData.labels.length === 0
  ) {
    canvas.parentElement.innerHTML =
      '<div class="d-flex align-items-center justify-content-center h-100">' +
      '<p class="text-center" style="color: #999;">Tidak ada data kategori untuk periode ini.</p>' +
      "</div>";
    return;
  }

  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: categoryChartData.labels,
      datasets: [
        {
          data: categoryChartData.data.map((v) => sanitizeNumber(v)),
          backgroundColor: colorScheme.categories,
          borderWidth: 3,
          borderColor: "#fff",
          hoverOffset: 10,
          hoverBorderWidth: 3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "60%",
      plugins: {
        legend: {
          position: "bottom",
          align: "start",
          labels: {
            padding: 15,
            boxWidth: 12,
            usePointStyle: true,
            pointStyle: "circle",
            font: {
              size: 11,
              family: "'Inter', sans-serif",
            },
            color: colorScheme.text,
            generateLabels: function (chart) {
              const data = chart.data;
              if (data.labels.length && data.datasets.length) {
                return data.labels.map((label, i) => {
                  const value = sanitizeNumber(data.datasets[0].data[i]);
                  const total = data.datasets[0].data
                    .map((v) => sanitizeNumber(v))
                    .reduce((a, b) => a + b, 0);

                  const percentage = calculatePercentage(value, total);

                  return {
                    text: `${label} (${percentage}%)`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    hidden: false,
                    index: i,
                  };
                });
              }
              return [];
            },
          },
        },
        tooltip: {
          backgroundColor: colorScheme.tooltipBg,
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: "bold",
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = sanitizeNumber(context.parsed);
              const total = context.dataset.data
                .map((v) => sanitizeNumber(v))
                .reduce((a, b) => a + b, 0);

              const percentage = calculatePercentage(value, total);

              return `${label}: ${value} item (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}

// ==================== BAR CHART: TOP 5 PRODUCTS ====================

function initTopProductsChart() {
  const canvas = document.getElementById("topProductsChart");
  if (!canvas) {
    console.warn("Canvas topProductsChart tidak ditemukan");
    return;
  }

  if (
    typeof topProductsChartData === "undefined" ||
    !topProductsChartData.labels ||
    topProductsChartData.labels.length === 0
  ) {
    canvas.parentElement.innerHTML =
      '<div class="d-flex align-items-center justify-content-center h-100">' +
      '<p class="text-center" style="color: #999;">Tidak ada data produk untuk periode ini.</p>' +
      "</div>";
    return;
  }

  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: topProductsChartData.labels,
      datasets: [
        {
          label: "Jumlah Terjual",
          data: topProductsChartData.data.map((v) => sanitizeNumber(v)),
          backgroundColor: colorScheme.primary,
          borderRadius: 4, // Sedikit diperkecil agar rapi di horizontal
          borderSkipped: false,
          barPercentage: 0.7, // Mengatur ketebalan batang
          categoryPercentage: 0.8,
        },
      ],
    },
    options: {
      indexAxis: "y", // <--- INI KUNCI AGAR MENJADI HORIZONTAL
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: colorScheme.tooltipBg,
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: "bold",
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function (context) {
              // Perhatikan: kita mengambil context.parsed.x (bukan y) karena horizontal
              return "Terjual: " + context.parsed.x + " item";
            },
          },
        },
      },
      scales: {
        x: {
          // Sumbu X sekarang menjadi penunjuk Nilai (Value)
          beginAtZero: true,
          grid: {
            color: colorScheme.grid,
            borderDash: [2, 2], // Opsional: membuat grid putus-putus agar lebih ringan
          },
          ticks: {
            font: {
              size: 11,
            },
          },
        },
        y: {
          // Sumbu Y sekarang menjadi penunjuk Label Produk
          grid: {
            display: false, // Hilangkan grid pada sumbu kategori agar bersih
          },
          ticks: {
            font: {
              size: 11,
              weight: "500", // Sedikit lebih tebal agar nama produk terbaca jelas
            },
            autoSkip: false, // Pastikan semua nama produk muncul
          },
        },
      },
    },
  });
}

// ==================== PIE CHART: PAYMENT METHOD ====================

function initPaymentMethodChart() {
  const canvas = document.getElementById("paymentMethodChart");
  if (!canvas) {
    console.warn("Canvas paymentMethodChart tidak ditemukan");
    return;
  }

  if (
    typeof paymentMethodChartData === "undefined" ||
    !paymentMethodChartData.labels ||
    paymentMethodChartData.labels.length === 0
  ) {
    canvas.parentElement.innerHTML =
      '<div class="d-flex align-items-center justify-content-center h-100">' +
      '<p class="text-center" style="color: #999;">Tidak ada data metode pembayaran untuk periode ini.</p>' +
      "</div>";
    return;
  }

  const ctx = canvas.getContext("2d");

  // Generate colors based on payment method
  const colors = paymentMethodChartData.labels.map((label) => {
    if (label === "Cash") return colorScheme.payment.cash;
    if (label === "QRIS") return colorScheme.payment.qris;
    return colorScheme.payment.others;
  });

  new Chart(ctx, {
    type: "pie",
    data: {
      labels: paymentMethodChartData.labels,
      datasets: [
        {
          data: paymentMethodChartData.data.map((v) => sanitizeNumber(v)),
          backgroundColor: colors,
          borderWidth: 3,
          borderColor: "#fff",
          hoverOffset: 10,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            padding: 15,
            boxWidth: 12,
            usePointStyle: true,
            pointStyle: "circle",
            font: {
              size: 11,
              family: "'Inter', sans-serif",
            },
            color: colorScheme.text,
            generateLabels: function (chart) {
              const data = chart.data;
              if (data.labels.length && data.datasets.length) {
                return data.labels.map((label, i) => {
                  const value = sanitizeNumber(data.datasets[0].data[i]);
                  const total = data.datasets[0].data
                    .map((v) => sanitizeNumber(v))
                    .reduce((a, b) => a + b, 0);

                  const percentage = calculatePercentage(value, total);

                  return {
                    text: `${label} (${percentage}%)`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    hidden: false,
                    index: i,
                  };
                });
              }
              return [];
            },
          },
        },
        tooltip: {
          backgroundColor: colorScheme.tooltipBg,
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: "bold",
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = sanitizeNumber(context.parsed);
              const total = context.dataset.data
                .map((v) => sanitizeNumber(v))
                .reduce((a, b) => a + b, 0);

              const percentage = calculatePercentage(value, total);

              return `${label}: ${value} transaksi (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}

// ==================== BAR CHART: PAYMENT REVENUE ====================

function initPaymentRevenueChart() {
  const canvas = document.getElementById("paymentRevenueChart");
  if (!canvas) {
    console.warn("Canvas paymentRevenueChart tidak ditemukan");
    return;
  }

  if (
    typeof paymentRevenueChartData === "undefined" ||
    !paymentRevenueChartData.labels ||
    paymentRevenueChartData.labels.length === 0
  ) {
    canvas.parentElement.innerHTML =
      '<div class="d-flex align-items-center justify-content-center h-100">' +
      '<p class="text-center" style="color: #999;">Tidak ada data pendapatan pembayaran untuk periode ini.</p>' +
      "</div>";
    return;
  }

  const ctx = canvas.getContext("2d");

  // Generate colors based on payment method
  const colors = paymentRevenueChartData.labels.map((label) => {
    if (label === "Cash") return colorScheme.payment.cash;
    if (label === "QRIS") return colorScheme.payment.qris;
    return colorScheme.payment.others;
  });

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: paymentRevenueChartData.labels,
      datasets: [
        {
          label: "Pendapatan (Rp)",
          data: paymentRevenueChartData.data.map((v) => sanitizeNumber(v)),
          backgroundColor: colors,
          borderRadius: 6,
          borderSkipped: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          backgroundColor: colorScheme.tooltipBg,
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: "bold",
          },
          bodyFont: {
            size: 13,
          },
          callbacks: {
            label: function (context) {
              return "Rp " + context.parsed.y.toLocaleString("id-ID");
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colorScheme.grid,
          },
          ticks: {
            font: {
              size: 11,
            },
            callback: function (value) {
              return formatCompactNumber(value);
            },
          },
        },
        x: {
          grid: {
            display: false,
          },
          ticks: {
            font: {
              size: 11,
            },
          },
        },
      },
    },
  });
}

// ==================== INISIALISASI ====================

document.addEventListener("DOMContentLoaded", function () {
  try {
    initRevenueTrendChart();
  } catch (error) {
    console.error("Error pada Revenue Trend Chart:", error);
  }

  try {
    initCategoryChart();
  } catch (error) {
    console.error("Error pada Category Chart:", error);
  }

  try {
    initTopProductsChart();
  } catch (error) {
    console.error("Error pada Top Products Chart:", error);
  }

  try {
    initPaymentMethodChart();
  } catch (error) {
    console.error("Error pada Payment Method Chart:", error);
  }

  try {
    initPaymentRevenueChart();
  } catch (error) {
    console.error("Error pada Payment Revenue Chart:", error);
  }
});
