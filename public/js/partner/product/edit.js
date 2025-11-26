// Quantity product
(function () {
  const qty = document.getElementById("quantity");
  const dec = document.getElementById("btn-qty-dec");
  const inc = document.getElementById("btn-qty-inc");
  const max = document.getElementById("btn-qty-max");
  const toInt = (v) => {
    const n = parseInt(v, 10);
    return isNaN(n) ? 0 : n;
  };
  dec?.addEventListener("click", () => {
    if (qty?.disabled) return;
    qty.value = Math.max(0, toInt(qty.value) - 1);
  });
  inc?.addEventListener("click", () => {
    if (qty?.disabled) return;
    qty.value = toInt(qty.value) + 1;
  });
  max?.addEventListener("click", () => {
    if (qty?.disabled) return;
    qty.value = 999999999;
  });
})();

// +/- options
(function () {
  const toInt = (v) => {
    const n = parseInt(v, 10);
    return isNaN(n) ? 0 : n;
  };
  document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-opt-dec")) {
      const target = e.target.closest(".btn-opt-dec").dataset.target;
      const input = document.querySelector(target);
      if (!input || input.disabled) return;
      input.value = Math.max(0, toInt(input.value) - 1);
    }
    if (e.target.closest(".btn-opt-inc")) {
      const target = e.target.closest(".btn-opt-inc").dataset.target;
      const input = document.querySelector(target);
      if (!input || input.disabled) return;
      input.value = toInt(input.value) + 1;
    }
  });
})();

// Toggle Always available
(function () {
  function toggleQty(wrapperEl, inputEl, checked) {
    if (!wrapperEl || !inputEl) return;
    if (checked) {
      if (!inputEl.dataset.prev) inputEl.dataset.prev = inputEl.value || "0";
      wrapperEl.classList.add("d-none");
      inputEl.disabled = true;
    } else {
      wrapperEl.classList.remove("d-none");
      inputEl.disabled = false;
      if (inputEl.dataset.prev) inputEl.value = inputEl.dataset.prev;
    }
  }

  // Product
  const aaProd = document.getElementById("aa_product");
  const prodWrap = document.getElementById("product_qty_group");
  const prodQty = document.getElementById("quantity");
  function syncProd() {
    if (prodWrap && prodQty) {
      toggleQty(prodWrap, prodQty, aaProd?.checked);
    }
  }
  aaProd?.addEventListener("change", syncProd);
  syncProd();

  // Options
  function syncOneOpt(tg) {
    const qtySel = tg.getAttribute("data-qty");
    const wrapSel = tg.getAttribute("data-wrap");
    const qty = document.querySelector(qtySel);
    const wrap = document.querySelector(wrapSel);
    toggleQty(wrap, qty, tg.checked);
  }
  document.querySelectorAll(".opt-aa").forEach((tg) => {
    tg.addEventListener("change", () => syncOneOpt(tg));
    syncOneOpt(tg);
  });
})();

// Product Stock Type Toggle
(function () {
  const stockTypeSelect = document.getElementById("product_stock_type");
  const aaGroup = document.getElementById("product_aa_group");
  const qtyGroup = document.getElementById("product_qty_group");
  const linkedGroup = document.getElementById("product_linked_group");
  const qtyInput = document.getElementById("quantity");
  const aaCheckbox = document.getElementById("aa_product");

  function handleProductStockType() {
    const stockType = stockTypeSelect?.value;

    if (stockType === "linked") {
      // Hide AA and Quantity, show linked info + button
      if (aaGroup) aaGroup.style.display = "none";
      if (qtyGroup) qtyGroup.style.display = "none";
      if (linkedGroup) linkedGroup.style.display = "block";
      
      // PENTING: Disable input dan set ke 0
      if (qtyInput) {
        qtyInput.disabled = true;
        qtyInput.value = 0;
      }
      
      // Uncheck always available
      if (aaCheckbox) {
        aaCheckbox.checked = false;
      }
    } else {
      // Show AA and Quantity, hide linked info
      if (aaGroup) aaGroup.style.display = "block";
      if (qtyGroup) qtyGroup.style.display = "block";
      if (linkedGroup) linkedGroup.style.display = "none";
      
      // PENTING: Enable input kembali (kecuali jika AA checked)
      if (qtyInput && aaCheckbox) {
        if (aaCheckbox.checked) {
          qtyInput.disabled = true;
          qtyInput.value = 0;
        } else {
          qtyInput.disabled = false;
        }
      } else if (qtyInput) {
        qtyInput.disabled = false;
      }
    }
  }

  stockTypeSelect?.addEventListener("change", handleProductStockType);
  handleProductStockType(); // Initial state
})();

// Stock Type Toggle Logic for Options
(function () {
  function handleStockTypeChange(selectEl) {
    const optId = selectEl.dataset.optId;
    const stockType = selectEl.value;

    const aaContainer = document.querySelector(`.opt-aa-container-${optId}`);
    const qtyWrapper = document.getElementById(`opt-qty-wrap-${optId}`);
    const linkedInfo = document.getElementById(`opt-linked-info-${optId}`);
    const aaCheckbox = document.getElementById(`opt-aa-${optId}`);

    if (stockType === "linked") {
      if (aaContainer) {
        aaContainer.classList.remove("d-inline-block");
        aaContainer.classList.add("d-none");
        aaContainer.style.display = "none";
      }
      if (qtyWrapper) {
        qtyWrapper.classList.add("d-none");
        qtyWrapper.style.display = "none";
      }
      if (linkedInfo) {
        linkedInfo.classList.remove("d-none");
        linkedInfo.style.display = "block";
      }
    } else {
      if (aaContainer) {
        aaContainer.classList.remove("d-none");
        aaContainer.classList.add("d-inline-block");
        aaContainer.style.display = "";
      }
      if (linkedInfo) {
        linkedInfo.classList.add("d-none");
        linkedInfo.style.display = "none";
      }
      
      if (aaCheckbox && qtyWrapper) {
        if (aaCheckbox.checked) {
          qtyWrapper.classList.add("d-none");
          qtyWrapper.style.display = "none";
        } else {
          qtyWrapper.classList.remove("d-none");
          qtyWrapper.style.display = "block";
        }
      }
    }
  }

  // Initialize all stock type selectors
  document.querySelectorAll(".opt-stock-type").forEach((select) => {
    handleStockTypeChange(select);
    select.addEventListener("change", function () {
      handleStockTypeChange(this);
    });
  });
})();

// Recipe Modal Management
(function () {
  let cachedIngredients = [];
  let currentItemType = null;
  let currentItemId = null;

  const SAVE_URL = "/partner/products/recipe/save";
  const LOAD_URL = "/partner/products/recipe/load";
  const ingredientsUrl = "/partner/products/recipe/ingredients";
  const modal = document.getElementById("recipeModal");
  const $modal = $("#recipeModal");

  // 1. FUNGSI MODAL MURNI BS4 / JQUERY
  function showModal() {
    if (typeof $ !== "undefined") {
      $modal.modal("show");
    } else {
      console.warn("jQuery tidak terdeteksi. Modal tidak dapat dibuka.");
    }
  }

  function hideModal() {
    if (typeof $ !== "undefined") {
      $modal.modal("hide");
    }
  }

  // 2. FUNGSI SELECT2 (Dipertahankan)
  function initializeSelect2OnNewItem() {
    document
      .querySelectorAll(".recipe-item .select2-init")
      .forEach((selectElement) => {
        // FIX: Cek JQuery di sini juga
        if (typeof $ === "function" && $.fn.select2) {
          const $select = $(selectElement);

          if (!$select.data("select2")) {
            $select.select2({
              dropdownParent: $modal, // Gunakan objek jQuery $modal
              theme: "bootstrap4",
              placeholder: $select.data("placeholder") || "Cari...",
              width: "100%",
              minimumInputLength: 0,
              allowClear: true,
            });

            $select.removeClass("select2-init");

            $select.on("select2:select", function (e) {
              e.target.dispatchEvent(new Event("change", { bubbles: true }));
            });
          }
        } else {
          // ERROR: Ini mengindikasikan Select2.js atau jQuery.js dimuat SETELAH edit.js
          console.warn(
            "Select2 atau jQuery belum selesai dimuat. Fitur pencarian dinonaktifkan."
          );
        }
      });
  }

  // 3. Load Data dari Server (Dipertahankan)
  function loadRecipeData() {
    const container = document.getElementById("recipe-items-container");
    container.innerHTML =
      '<div class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat bahan baku...</div>';

    // STEP 1: Fetch data ingredients (Stok & Unit Kompatibel)
    fetch(ingredientsUrl)
      .then((response) => {
        if (!response.ok) throw new Error("Failed to load ingredients data.");
        return response.json();
      })
      .then((data) => {
        cachedIngredients = data;

        // STEP 2: Fetch existing recipe data
        return fetch(
          `${LOAD_URL}?item_type=${currentItemType}&item_id=${currentItemId}`
        );
      })
      .then((response) => {
        if (!response.ok)
          throw new Error("Failed to load existing recipe data.");
        return response.json();
      })
      .then((recipeData) => {
        container.innerHTML = "";

        if (
          recipeData.success &&
          recipeData.recipe &&
          recipeData.recipe.length > 0
        ) {
          // Load existing recipe
          recipeData.recipe.forEach((item) => {
            // PENTING: Menggunakan item.unit_id yang berasal dari kolom display_unit_id di DB
            addRecipeItemRow(
              item.stock_id,
              item.unit_id, // <--- Nilai unit yang sudah diset di DB
              item.quantity_used
            );
          });
        } else {
          // Add empty row
          addRecipeItemRow();
        }
      })
      .catch((error) => {
        console.error("Error loading recipe:", error);
        container.innerHTML = `<div class="alert alert-danger">Gagal memuat data resep. ${error.message}</div>`;
      });
  }

  // Fungsi dipanggil saat modal dibuka, setelah stock dipilih, atau saat baris baru ditambah

  function updateUnitOptions(
    stockId,
    unitSelectElement,
    selectedUnitId = null
  ) {
    // Mencari data stock dari cache lokal
    const stockData = cachedIngredients.find((s) => s.id === stockId);
    unitSelectElement.innerHTML = ""; // Reset dropdown unit

    if (
      stockData &&
      stockData.available_units &&
      stockData.available_units.length > 0
    ) {
      // Menambahkan opsi unit yang kompatibel
      stockData.available_units.forEach((u) => {
        // Periksa apakah unit ini adalah unit yang tersimpan di resep (selectedUnitId)
        const selected = u.id == selectedUnitId ? "selected" : "";
        // Tandai unit default dari stock tersebut (visual)
        const label = u.id == stockData.current_unit_id ? `${u.name}` : u.name;

        unitSelectElement.innerHTML += `<option value="${u.id}" ${selected}>${label}</option>`;
      });

      // Jika tidak ada selectedUnitId (misalnya baris baru), atur ke unit default stok (current_unit_id)
      if (!selectedUnitId && stockData.current_unit_id) {
        unitSelectElement.value = stockData.current_unit_id;
      }
    } else {
      unitSelectElement.innerHTML =
        '<option value="">Tidak ada unit kompatibel</option>';
    }
  }

  // Repeater Logic
  function addRecipeItemRow(
    selectedStockId = null,
    selectedUnitId = null,
    qty = null
  ) {
    const container = document.getElementById("recipe-items-container");

    let stockOptionsHtml = '<option value="">-- Cari Bahan --</option>';
    cachedIngredients.forEach((item) => {
      const selected = item.id == selectedStockId ? "selected" : "";
      stockOptionsHtml += `<option value="${item.id}" ${selected}>${item.name}</option>`;
    });

    const template = `
      <div class="recipe-item mb-3 p-3 border rounded bg-light">
        <div class="row align-items-end">
          <div class="col-md-5">
            <label class="mb-1 fw-600 small">Bahan Baku</label>
            <select class="form-control form-control-sm recipe-stock-select select2-init" 
                    data-placeholder="Cari Bahan Baku..." 
                    required>
              ${stockOptionsHtml}
            </select>
          </div>
          <div class="col-md-3">
            <label class="mb-1 fw-600 small">Jumlah</label>
            <input type="number" class="form-control form-control-sm recipe-quantity" 
                   min="0" step="0.01" placeholder="0" value="${
                     qty || ""
                   }" required>
          </div>
          <div class="col-md-3">
            <label class="mb-1 fw-600 small">Unit</label>
            <select class="form-control form-control-sm recipe-unit-select" required>
              <option value="">-</option>
            </select>
          </div>
          <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm btn-block remove-recipe-item" title="Hapus">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    `;

    container.insertAdjacentHTML("beforeend", template);

    initializeSelect2OnNewItem();

    if (selectedStockId) {
      const lastRow = container.lastElementChild;
      const unitSelect = lastRow.querySelector(".recipe-unit-select");
      updateUnitOptions(selectedStockId, unitSelect, selectedUnitId);
    }
  }

  // 4. Save Recipe Function (Perbaikan Konflik Alert)
  function saveRecipe() {
    const recipeItems = [];
    let isValid = true;
    const saveBtn = document.getElementById("save-recipe");

    document.querySelectorAll(".recipe-item").forEach((item) => {
      const stockSelect = item.querySelector(".recipe-stock-select");
      const quantityInput = item.querySelector(".recipe-quantity");
      const unitSelect = item.querySelector(".recipe-unit-select");

      // Reset validation classes
      stockSelect.classList.remove("is-invalid");
      quantityInput.classList.remove("is-invalid");
      unitSelect.classList.remove("is-invalid");

      if (!stockSelect.value || !quantityInput.value || !unitSelect.value) {
        isValid = false;
        if (!stockSelect.value) stockSelect.classList.add("is-invalid");
        if (!quantityInput.value) quantityInput.classList.add("is-invalid");
        if (!unitSelect.value) unitSelect.classList.add("is-invalid");
        return;
      }

      recipeItems.push({
        stock_id: parseInt(stockSelect.value),
        quantity: parseFloat(quantityInput.value),
        unit_id: parseInt(unitSelect.value),
      });
    });

    if (!isValid) {
      alert("Mohon lengkapi semua field resep.");
      return;
    }

    // Disable button and show loading
    saveBtn.disabled = true;
    saveBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

    // Get CSRF token
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    // Send to server
    fetch(SAVE_URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
      },
      body: JSON.stringify({
        item_type: currentItemType,
        item_id: currentItemId,
        recipe_items: recipeItems,
      }),
    })
      .then(async (response) => {
        // Perbaikan: Jika response BUKAN 2xx (response.ok = false), kita throw error
        if (!response.ok) {
          const errorData = await response.json();
          // Throw error with message captured from server (e.g., Validation error)
          throw new Error(errorData.message || "Error saat pemrosesan server.");
        }
        return response.json();
      })
      .then((data) => {
        // Sukses (Status 200/201)
        if (data.success) {
          alert("Resep berhasil disimpan!");
          hideModal();

          // FIX: Tunda reload untuk menyelesaikan promise fetch
          setTimeout(() => {
            window.location.reload();
          }, 50);
        } else {
          // Fallback: Seharusnya tidak terjadi jika response.ok dicek di atas
          alert("Gagal menyimpan resep: " + (data.message || "Unknown error."));
        }
      })
      .catch((error) => {
        // Tampilkan pesan error yang lebih detail
        let msg = error.message;
        if (msg.includes("Validation error")) {
          msg =
            "Terjadi kesalahan validasi di server. Periksa kembali input Anda.";
        }

        console.error("Error saving recipe:", error);
        alert("Terjadi kesalahan saat menyimpan resep: " + msg);
      })
      .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Resep';
      });
  }

  // Open modal for product recipe
  const btnManageProductRecipe = document.getElementById(
    "btn-manage-product-recipe"
  );
  btnManageProductRecipe?.addEventListener("click", function (e) {
    e.preventDefault();
    currentItemType = "product";
    currentItemId = this.dataset.productId;
    document.getElementById("modal-item-name").textContent =
      this.dataset.productName;
    loadRecipeData();
    showModal();
  });

  // Open modal for option recipe
  document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-manage-recipe")) {
      e.preventDefault();
      const btn = e.target.closest(".btn-manage-recipe");
      currentItemType = "option";
      currentItemId = btn.dataset.optId;
      document.getElementById("modal-item-name").textContent =
        btn.dataset.optName;
      loadRecipeData();
      showModal();
    }
  });

  // Add recipe item
  document
    .getElementById("add-recipe-item")
    ?.addEventListener("click", function () {
      addRecipeItemRow();
    });

  // Remove recipe item
  document.addEventListener("click", function (e) {
    if (e.target.closest(".remove-recipe-item")) {
      const recipeItem = e.target.closest(".recipe-item");
      if (document.querySelectorAll(".recipe-item").length > 1) {
        recipeItem.remove();
      } else {
        alert("Minimal harus ada 1 bahan dalam resep.");
      }
    }
  });

  // Save recipe
  document
    .getElementById("save-recipe")
    ?.addEventListener("click", function () {
      saveRecipe();
    });

  // Handle Change Stock -> Update Unit Dropdown
  document.addEventListener("change", function (e) {
    if (e.target.classList.contains("recipe-stock-select")) {
      const stockId = parseInt(e.target.value);
      const row = e.target.closest(".recipe-item");
      const unitSelect = row.querySelector(".recipe-unit-select");
      updateUnitOptions(stockId, unitSelect);
    }
  });

  // Close modal button handlers
  document.querySelectorAll('[data-dismiss="modal"]').forEach((btn) => {
    btn.addEventListener("click", function () {
      hideModal();
    });
  });
})();
