/**
 * Sales Report Charts
 * File: public/js/sales.js
 */

// ==================== KONFIGURASI WARNA ====================
const colorScheme = {
  primary: {
    start: "rgba(139, 69, 19, 0.8)",
    mid: "rgba(160, 82, 45, 0.6)",
    end: "rgba(139, 69, 19, 0.1)",
    solid: "#8B4513",
  },
  // Palet warna tema cokelat dengan variasi yang kontras
  categories: [
    "#8B4513", // Saddle Brown (Cokelat tua)
    "#D2691E", // Chocolate (Cokelat medium)
    "#CD853F", // Peru (Cokelat keemasan)
    "#DEB887", // Burlywood (Cokelat muda)
    "#F4A460", // Sandy Brown (Cokelat pastel)
    "#BC8F8F", // Rosy Brown (Cokelat kemerahan)
    "#A0522D", // Sienna (Cokelat tanah)
    "#B8860B", // Dark Goldenrod (Emas tua)
    "#DAA520", // Goldenrod (Emas medium)
    "#F5DEB3", // Wheat (Gandum)
  ],
  grid: "rgba(0, 0, 0, 0.05)",
  text: "#4B5563",
};

// ==================== OPSI DEFAULT CHART ====================
const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: {
    duration: 1200,
    easing: "easeInOutCubic",
  },
  plugins: {
    legend: {
      position: "bottom",
      labels: {
        padding: 20,
        usePointStyle: true,
        font: {
          size: 12,
          family: "'Inter', sans-serif",
        },
        color: colorScheme.text,
      },
    },
    tooltip: {
      backgroundColor: "rgba(255, 255, 255, 0.95)",
      titleColor: colorScheme.text,
      bodyColor: colorScheme.text,
      borderColor: colorScheme.primary.solid,
      borderWidth: 1,
      padding: 12,
      displayColors: true,
      bodyFont: {
        size: 13,
      },
      titleFont: {
        size: 14,
        weight: "bold",
      },
      cornerRadius: 8,
      callbacks: {
        label: function (context) {
          let label = context.dataset.label || "";
          if (label) {
            label += ": ";
          }
          if (context.parsed.y !== null) {
            label +=
              "Rp " + new Intl.NumberFormat("id-ID").format(context.parsed.y);
          }
          return label;
        },
      },
    },
  },
};

// ==================== FUNGSI HELPER ====================

/**
 * Membuat gradient background untuk line chart
 */
function createGradient(ctx, area) {
  const gradient = ctx.createLinearGradient(0, area.bottom, 0, area.top);
  gradient.addColorStop(0, colorScheme.primary.end);
  gradient.addColorStop(0.5, colorScheme.primary.mid);
  gradient.addColorStop(1, colorScheme.primary.start);
  return gradient;
}

/**
 * Validasi dan sanitasi nilai numerik
 */
function sanitizeNumber(value) {
  const num = parseFloat(value);
  return !isNaN(num) && isFinite(num) ? num : 0;
}

/**
 * Hitung persentase dengan validasi
 */
function calculatePercentage(value, total) {
  const sanitizedValue = sanitizeNumber(value);
  const sanitizedTotal = sanitizeNumber(total);

  if (sanitizedTotal === 0) {
    return "0.0";
  }

  return ((sanitizedValue / sanitizedTotal) * 100).toFixed(1);
}

// ==================== LINE CHART: REVENUE TREND ====================

/**
 * Inisialisasi chart trend pendapatan
 */
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
      '<div class="flex items-center justify-center h-full">' +
      '<p class="text-gray-500 text-center">Tidak ada data pendapatan untuk periode ini.</p>' +
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
          label: "Pendapatan",
          data: revenueChartData.data.map((v) => sanitizeNumber(v)),
          borderColor: colorScheme.primary.solid,
          backgroundColor: function (context) {
            const chart = context.chart;
            const { ctx, chartArea } = chart;
            if (!chartArea) {
              return null;
            }
            return createGradient(ctx, chartArea);
          },
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 8,
          pointBackgroundColor: "#fff",
          pointBorderColor: colorScheme.primary.solid,
          pointBorderWidth: 3,
          pointHoverBackgroundColor: colorScheme.primary.solid,
          pointHoverBorderColor: "#fff",
          pointHoverBorderWidth: 3,
        },
      ],
    },
    options: {
      ...defaultOptions,
      interaction: {
        intersect: false,
        mode: "index",
      },
      plugins: {
        ...defaultOptions.plugins,
        legend: {
          display: false,
        },
        tooltip: {
          ...defaultOptions.plugins.tooltip,
          callbacks: {
            label: function (context) {
              const value = sanitizeNumber(context.parsed.y);
              return (
                "Pendapatan: Rp " + new Intl.NumberFormat("id-ID").format(value)
              );
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colorScheme.grid,
            drawBorder: false,
          },
          ticks: {
            color: colorScheme.text,
            font: {
              size: 11,
            },
            callback: function (value) {
              return (
                "Rp " +
                new Intl.NumberFormat("id-ID", {
                  notation: "compact",
                  compactDisplay: "short",
                }).format(value)
              );
            },
          },
        },
        x: {
          grid: {
            display: false,
            drawBorder: false,
          },
          ticks: {
            color: colorScheme.text,
            font: {
              size: 11,
            },
          },
        },
      },
    },
  });
}

// ==================== DOUGHNUT CHART: CATEGORY ====================

/**
 * Inisialisasi chart kategori
 */
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
      '<div class="flex items-center justify-center h-full">' +
      '<p class="text-gray-500 text-center">Tidak ada data kategori untuk periode ini.</p>' +
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
          hoverOffset: 15,
          hoverBorderWidth: 4,
          hoverBorderColor: "#fff",
        },
      ],
    },
    options: {
      ...defaultOptions,
      cutout: "50%", // [PERUBAHAN] Lubang dibuat lebih besar
      plugins: {
        ...defaultOptions.plugins,
        legend: {
          position: "bottom", // [PERUBAHAN] Legenda dipindah ke bawah
          align: "center", // [PERUBAHAN] Teks legenda dibuat rata kiri
          labels: {
            padding: 20, // Jarak dari chart
            boxWidth: 12, // Ukuran kotak warna
            usePointStyle: true,
            pointStyle: "circle",
            font: {
              size: 12, // Ukuran font
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
          ...defaultOptions.plugins.tooltip,
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = sanitizeNumber(context.parsed);
              const total = context.dataset.data
                .map((v) => sanitizeNumber(v))
                .reduce((a, b) => a + b, 0);

              const percentage = calculatePercentage(value, total);

              return `${label}: Rp ${new Intl.NumberFormat("id-ID").format(
                value
              )} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}

// ==================== INISIALISASI ====================

/**
 * Inisialisasi semua chart setelah DOM ready
 */
document.addEventListener("DOMContentLoaded", function () {
  console.log("Menginisialisasi charts...");

  try {
    initRevenueTrendChart();
    console.log("✓ Revenue Trend Chart berhasil diinisialisasi");
  } catch (error) {
    console.error("✗ Error pada Revenue Trend Chart:", error);
  }

  try {
    initCategoryChart();
    console.log("✓ Category Chart berhasil diinisialisasi");
  } catch (error) {
    console.error("✗ Error pada Category Chart:", error);
  }
});
