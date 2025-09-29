
const staticDataForJs = {
  // Data untuk chart garis dan tabel "Recent Transactions"
  revenue: {
    labels: [
      "15 Sep",
      "16 Sep",
      "17 Sep",
      "18 Sep",
      "19 Sep",
      "20 Sep",
      "21 Sep",
      "22 Sep",
    ],
    data: [390000, 480000, 550000, 510000, 430000, 460000, 520000, 480000],
  },
  // Data dasar untuk proporsi chart kategori
  category: {
    labels: ["Makanan", "Minuman", "Dessert", "Snack"],
    data: [850, 620, 280, 125], // Rasio
    colors: ["#667eea", "#764ba2", "#f093fb", "#f5576c"],
  },
  // Data dasar untuk proporsi tabel "Top Products"
  topProducts: [
    { name: "Nasi Goreng Spesial", weight: 0.25 },
    { name: "Es Kopi Susu", weight: 0.18 },
    { name: "Ayam Geprek", weight: 0.12 },
    { name: "Milkshake Oreo", weight: 0.1 },
    { name: "Mie Goreng Seafood", weight: 0.09 },
    { name: "Jus Alpukat", weight: 0.08 },
    { name: "Pisang Goreng", weight: 0.07 },
  ],
};

// Variabel global untuk instance chart
let revenueTrendChart;
let categoryChart;

// Opsi default untuk semua chart
const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: { duration: 600, easing: "easeInOutQuart" },
  plugins: {
    legend: {
      position: "bottom",
      labels: { padding: 15, usePointStyle: true },
    },
  },
};

function initializeJsComponents() {
  const revenueData = staticDataForJs.revenue;
  const totalRevenue = revenueData.data.reduce((sum, value) => sum + value, 0);

  // 1. Inisialisasi kedua chart
  initRevenueTrendChart(revenueData);
  initCategoryChart();

  // 2. Perbarui chart kategori dan kedua tabel berdasarkan total pendapatan dari data statis JS
  updateCategoryChartByTotal(totalRevenue);
  updateTopProducts(totalRevenue);
  updateRecentTransactions(revenueData);

  // 3. Set teks indikator (opsional, bisa juga statis di Blade)
  document.getElementById("chart-period-indicator").textContent =
    "Tampilan Data Statis";
}

function initRevenueTrendChart(data) {
  const ctx = document.getElementById("revenueTrendChart")?.getContext("2d");
  if (!ctx) return;
  revenueTrendChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: data.labels,
      datasets: [
        {
          label: "Pendapatan",
          data: data.data,
          borderColor: "#667eea",
          backgroundColor: "rgba(102, 126, 234, 0.08)",
          borderWidth: 2,
          fill: true,
          tension: 0.3,
        },
      ],
    },
    options: {
      ...defaultOptions,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => "Rp " + (value / 1000).toFixed(0) + "K",
          },
        },
        x: { grid: { display: false } },
      },
    },
  });
}

function initCategoryChart() {
  const ctx = document.getElementById("categoryChart")?.getContext("2d");
  if (!ctx) return;
  categoryChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: staticDataForJs.category.labels,
      datasets: [
        {
          data: staticDataForJs.category.data,
          backgroundColor: staticDataForJs.category.colors,
          borderWidth: 0,
          hoverOffset: 6,
        },
      ],
    },
    options: {
      ...defaultOptions,
      cutout: "55%",
      plugins: {
        legend: {
          ...defaultOptions.plugins.legend,
          labels: {
            ...defaultOptions.plugins.legend.labels,
            generateLabels: (chart) => {
              const data = chart.data;
              const total =
                data.datasets[0].data.reduce((a, b) => a + b, 0) || 1;
              return data.labels.map((label, i) => ({
                text: `${label} (${(
                  (data.datasets[0].data[i] / total) *
                  100
                ).toFixed(1)}%)`,
                fillStyle: data.datasets[0].backgroundColor[i],
              }));
            },
          },
        },
      },
    },
  });
}

function updateCategoryChartByTotal(totalRevenue) {
  if (!categoryChart) return;
  const baseData = staticDataForJs.category.data;
  const sumBase = baseData.reduce((a, b) => a + b, 0) || 1;
  const scaledData = baseData.map((v) =>
    Math.round((v / sumBase) * totalRevenue)
  );
  categoryChart.data.datasets[0].data = scaledData;
  categoryChart.update();
}

function updateTopProducts(totalRevenue) {
  const container = document.getElementById("top-products-list");
  if (!container) return;
  const computed = staticDataForJs.topProducts
    .map((p) => ({ name: p.name, amount: Math.round(p.weight * totalRevenue) }))
    .sort((a, b) => b.amount - a.amount);
  container.innerHTML = computed
    .map(
      (item) =>
        `<div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl"><span class="font-medium text-gray-800">${
          item.name
        }</span><span class="font-bold text-indigo-600">${formatCurrency(
          item.amount
        )}</span></div>`
    )
    .join("");
}

function updateRecentTransactions(data) {
  const container = document.getElementById("recent-transactions-list");
  if (!container) return;
  const items = data.labels
    .map((label, i) => ({
      orderNo: 1234 - (data.labels.length - 1 - i),
      label: label,
      amount: data.data[i],
    }))
    .reverse()
    .slice(0, 8);
  container.innerHTML = items
    .map(
      (item) =>
        `<div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl"><div><p class="font-medium text-gray-800">Order #${
          item.orderNo
        }</p><p class="text-sm text-gray-500">${
          item.label
        }</p></div><span class="font-bold text-green-600">${formatCurrency(
          item.amount
        )}</span></div>`
    )
    .join("");
}



function applyGlobalFilters() {
  // Sengaja dikosongkan agar filter tidak berfungsi.
}

function changeFilterPeriod(period) {
  document.querySelectorAll('[id^="filter-btn-"]').forEach((btn) => {
    btn.classList.remove("bg-indigo-600", "text-white");
    btn.classList.add("text-gray-600");
  });
  document
    .getElementById(`filter-btn-${period}`)
    .classList.add("bg-indigo-600", "text-white");
  document
    .getElementById(`filter-btn-${period}`)
    .classList.remove("text-gray-600");

  document.querySelectorAll(".period-filter").forEach((filter) => {
    filter.classList.add("hidden");
  });
  document.getElementById(`global-${period}-filter`).classList.remove("hidden");
}

function formatCurrency(amount) {
  return "Rp " + Number(amount).toLocaleString("id-ID");
}

document.addEventListener("DOMContentLoaded", initializeJsComponents);
