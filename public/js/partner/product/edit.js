// ===== 1. PRODUCT STOCK ADJUSTMENT CALCULATION =====
(function () {
  const newQtyInput = document.getElementById("new_quantity");
  const currentQtyInput = document.getElementById("current_quantity");
  const adjustmentInfo = document.getElementById("adjustment_info");
  const adjustmentType = document.getElementById("adjustment_type");
  const adjustmentAmount = document.getElementById("adjustment_amount");

  function calculateAdjustment() {
    if (!newQtyInput || !currentQtyInput) return;

    // Gunakan parseFloat jika stok bisa desimal, atau parseInt jika bulat
    const newQty = parseFloat(newQtyInput.value) || 0;
    const currentQty = parseFloat(currentQtyInput.value) || 0;
    const difference = newQty - currentQty;

    // Jika input kosong, disabled, ATAU difference (selisih) adalah 0 -> Sembunyikan
    if (newQtyInput.value === "" || newQtyInput.disabled || difference === 0) {
      adjustmentInfo.style.display = "none";
      return; // Berhenti di sini, tidak lanjut ke bawah
    }

    const langIncreaseStock =
      window.outletProductLang?.type_increase || "Penambahan";
    const langDecreaseStock =
      window.outletProductLang?.type_decrease || "Pengurangan";

    // Karena kode di atas sudah me-return saat 0, maka kode di bawah hanya jalan jika ada selisih
    adjustmentInfo.style.display = "block"; // Tampilkan box

    if (difference > 0) {
      adjustmentInfo.className = "alert alert-success py-1 px-2";
      adjustmentType.textContent = langIncreaseStock;
      adjustmentAmount.textContent = "+" + difference + " pcs";
    } else {
      // Pasti difference < 0 karena 0 sudah di filter di atas
      adjustmentInfo.className = "alert alert-danger py-1 px-2";
      adjustmentType.textContent = langDecreaseStock;
      adjustmentAmount.textContent = "-" + Math.abs(difference) + " pcs";
    }
  }

  newQtyInput?.addEventListener("input", calculateAdjustment);
  // Panggil sekali saat load untuk memastikan status awal benar
  calculateAdjustment();
})();

// ===== 2. OPTION STOCK ADJUSTMENT CALCULATION =====
(function () {
  function calculateOptionAdjustment(optId) {
    const newQtyInput = document.getElementById(`opt-new-qty-${optId}`);
    const currentQtyInput = document.getElementById(`opt-current-qty-${optId}`);
    const adjustmentInfo = document.getElementById(`opt-adj-info-${optId}`);
    const adjustmentType = adjustmentInfo?.querySelector(".opt-adj-type");
    const adjustmentAmount = adjustmentInfo?.querySelector(".opt-adj-amount");

    if (!newQtyInput || !currentQtyInput || !adjustmentInfo) return;

    const newQty = parseInt(newQtyInput.value) || 0;
    const currentQty = parseInt(currentQtyInput.value) || 0;
    const difference = newQty - currentQty;

    // Jika input kosong, disabled, ATAU difference (selisih) adalah 0 -> Sembunyikan
    if (newQtyInput.value === "" || newQtyInput.disabled || difference === 0) {
      adjustmentInfo.style.display = "none";
      return; // Berhenti di sini, tidak lanjut ke bawah
    }
    // Karena kode di atas sudah me-return saat 0, maka kode di bawah hanya jalan jika ada selisih
    adjustmentInfo.style.display = "block"; // Tampilkan box

    const langIncreaseStock = window.outletProductLang?.type_increase;
    const langDecreaseStock = window.outletProductLang?.type_decrease;
    const langNoChange = window.outletProductLang?.type_no_change;

    if (difference > 0) {
      adjustmentInfo.className = "alert alert-success py-1 px-2 small";
      adjustmentType.textContent = langIncreaseStock;
      adjustmentAmount.textContent = "+" + difference + " pcs";
    } else {
      adjustmentInfo.className = "alert alert-danger py-1 px-2 small";
      adjustmentType.textContent = langDecreaseStock;
      adjustmentAmount.textContent = "-" + Math.abs(difference) + " pcs";
    }

    adjustmentInfo.style.display = "block";
    adjustmentInfo.style.margin = "0";
  }

  document.querySelectorAll(".opt-new-qty").forEach((input) => {
    const optId = input.dataset.optId;
    input.addEventListener("input", () => calculateOptionAdjustment(optId));
    calculateOptionAdjustment(optId);
  });
})();

// ===== 3. TOGGLE ALWAYS AVAILABLE =====
(function () {
  // Product
  const aaProd = document.getElementById("aa_product");
  const prodWrap = document.getElementById("product_qty_group");
  const prodQty = document.getElementById("new_quantity");

  function syncProd() {
    if (!prodWrap || !prodQty) return;

    if (aaProd?.checked) {
      if (!prodQty.dataset.prev) prodQty.dataset.prev = prodQty.value || "0";
      prodWrap.classList.add("d-none");
      prodWrap.style.display = "none";
      prodQty.disabled = true;

      const adjustmentInfo = document.getElementById("adjustment_info");
      if (adjustmentInfo) adjustmentInfo.style.display = "none";
    } else {
      prodWrap.classList.remove("d-none");
      prodWrap.style.display = "block";
      prodQty.disabled = false;
      if (prodQty.dataset.prev) prodQty.value = prodQty.dataset.prev;

      prodQty.dispatchEvent(new Event("input"));
    }
  }

  aaProd?.addEventListener("change", syncProd);
  syncProd();

  // Options
  document.querySelectorAll(".opt-aa").forEach((checkbox) => {
    const optId = checkbox.dataset.optId;
    const qtyWrapper = document.getElementById(`opt-qty-wrap-${optId}`);
    const qtyInput = document.getElementById(`opt-new-qty-${optId}`);
    const adjInfo = document.getElementById(`opt-adj-info-${optId}`);

    function syncOpt() {
      if (!qtyWrapper || !qtyInput) return;

      if (checkbox.checked) {
        qtyWrapper.classList.add("d-none");
        qtyWrapper.style.display = "none";
        qtyInput.disabled = true;
        if (adjInfo) adjInfo.style.display = "none";
      } else {
        qtyWrapper.classList.remove("d-none");
        qtyWrapper.style.display = "block";
        qtyInput.disabled = false;
        qtyInput.dispatchEvent(new Event("input"));
      }
    }

    checkbox.addEventListener("change", syncOpt);
    syncOpt();
  });
})();

// ===== 4. PRODUCT STOCK TYPE TOGGLE =====
(function () {
  const stockTypeSelect = document.getElementById("product_stock_type");
  const aaGroup = document.getElementById("product_aa_group");
  const qtyGroup = document.getElementById("product_qty_group");
  const linkedGroup = document.getElementById("product_linked_group");
  const newQtyInput = document.getElementById("new_quantity");
  const aaCheckbox = document.getElementById("aa_product");

  function handleProductStockType() {
    const stockType = stockTypeSelect?.value;

    if (stockType === "linked") {
      if (aaGroup) aaGroup.style.display = "none";
      if (qtyGroup) qtyGroup.style.display = "none";
      if (linkedGroup) linkedGroup.style.display = "block";

      if (newQtyInput) {
        newQtyInput.disabled = true;
        const currentQty = document.getElementById("current_quantity");
        if (currentQty) newQtyInput.value = currentQty.value;
      }

      if (aaCheckbox) aaCheckbox.checked = false;
    } else {
      if (aaGroup) aaGroup.style.display = "block";
      if (qtyGroup) qtyGroup.style.display = "block";
      if (linkedGroup) linkedGroup.style.display = "none";

      if (newQtyInput && aaCheckbox) {
        newQtyInput.disabled = aaCheckbox.checked;
      } else if (newQtyInput) {
        newQtyInput.disabled = false;
      }

      if (newQtyInput) newQtyInput.dispatchEvent(new Event("input"));
    }
  }

  stockTypeSelect?.addEventListener("change", handleProductStockType);
  handleProductStockType();
})();

// ===== 5. OPTION STOCK TYPE TOGGLE =====
(function () {
  function handleStockTypeChange(selectEl) {
    const optId = selectEl.dataset.optId;
    const stockType = selectEl.value;

    const aaContainer = document.querySelector(`.opt-aa-container-${optId}`);
    const qtyWrapper = document.getElementById(`opt-qty-wrap-${optId}`);
    const linkedInfo = document.getElementById(`opt-linked-info-${optId}`);
    const aaCheckbox = document.getElementById(`opt-aa-${optId}`);
    const newQtyInput = document.getElementById(`opt-new-qty-${optId}`);

    if (stockType === "linked") {
      if (aaContainer) {
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
      if (newQtyInput) newQtyInput.disabled = true;
    } else {
      if (aaContainer) {
        aaContainer.classList.remove("d-none");
        aaContainer.style.display = "inline-block";
      }
      if (linkedInfo) {
        linkedInfo.classList.add("d-none");
        linkedInfo.style.display = "none";
      }

      if (aaCheckbox && qtyWrapper && newQtyInput) {
        if (aaCheckbox.checked) {
          qtyWrapper.classList.add("d-none");
          qtyWrapper.style.display = "none";
          newQtyInput.disabled = true;
        } else {
          qtyWrapper.classList.remove("d-none");
          qtyWrapper.style.display = "block";
          newQtyInput.disabled = false;
          newQtyInput.dispatchEvent(new Event("input"));
        }
      }
    }
  }

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

  // Check if SweetAlert2 is loaded
  const hasSwal = typeof Swal !== "undefined";

  // Helper functions for alerts
  function showError(title, message) {
    if (hasSwal) {
      Swal.fire({
        icon: "error",
        title: title,
        text: message,
        confirmButtonColor: "#8c1000",
        confirmButtonText: "OK",
      });
    } else {
      alert(`${title}\n${message}`);
    }
  }

  function showSuccess(title, message, callback) {
    if (hasSwal) {
      Swal.fire({
        icon: "success",
        title: title,
        text: message,
        confirmButtonColor: "#8c1000",
        confirmButtonText: "OK",
        timer: 2000,
        timerProgressBar: true,
      }).then(() => {
        if (callback) callback();
      });
    } else {
      alert(`${title}\n${message}`);
      if (callback) callback();
    }
  }

  function showWarning(title, message) {
    if (hasSwal) {
      Swal.fire({
        icon: "warning",
        title: title,
        text: message,
        confirmButtonColor: "#8c1000",
        confirmButtonText: "OK",
      });
    } else {
      alert(`${title}\n${message}`);
    }
  }

  function showLoading(message = "Memproses...") {
    if (hasSwal) {
      Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
    }
  }

  function closeLoading() {
    if (hasSwal) {
      Swal.close();
    }
  }

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

  // 2. Select2 DIHAPUS - Tidak digunakan lagi

  // 3. Load Data dari Server
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
            addRecipeItemRow(item.stock_id, item.unit_id, item.quantity_used);
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
            <select class="form-control form-control-sm recipe-stock-select" required>
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

    if (selectedStockId) {
      const lastRow = container.lastElementChild;
      const unitSelect = lastRow.querySelector(".recipe-unit-select");
      updateUnitOptions(selectedStockId, unitSelect, selectedUnitId);
    }
  }

  // 4. Save Recipe Function
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
      showWarning("Data Tidak Lengkap", "Mohon lengkapi semua field resep.");
      return;
    }

    // Show loading
    if (hasSwal) {
      showLoading("Menyimpan resep...");
    } else {
      saveBtn.disabled = true;
      saveBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    }

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
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || "Error saat pemrosesan server.");
        }
        return response.json();
      })
      .then((data) => {
        closeLoading();

        if (data.success) {
          hideModal();
          showSuccess("Berhasil!", "Resep berhasil disimpan!", () => {
            setTimeout(() => {
              window.location.reload();
            }, 500);
          });
        } else {
          showError("Gagal", data.message || "Unknown error.");
        }
      })
      .catch((error) => {
        closeLoading();

        let msg = error.message;
        if (msg.includes("Validation error")) {
          msg =
            "Terjadi kesalahan validasi di server. Periksa kembali input Anda.";
        }

        console.error("Error saving recipe:", error);
        showError("Terjadi Kesalahan", msg);
      })
      .finally(() => {
        if (!hasSwal) {
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Resep';
        }
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
      const totalItems = document.querySelectorAll(".recipe-item").length;

      if (totalItems > 1) {
        if (hasSwal) {
          Swal.fire({
            title: "Hapus Bahan?",
            text: "Apakah Anda yakin ingin menghapus bahan ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#8c1000",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
            reverseButtons: true,
          }).then((result) => {
            if (result.isConfirmed) {
              recipeItem.remove();
            }
          });
        } else {
          if (confirm("Apakah Anda yakin ingin menghapus bahan ini?")) {
            recipeItem.remove();
          }
        }
      } else {
        showWarning(
          "Tidak Bisa Dihapus",
          "Minimal harus ada 1 bahan dalam resep."
        );
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
