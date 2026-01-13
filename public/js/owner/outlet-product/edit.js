// ===== 1. ACTIVE/HOT PRODUCT SWITCHES =====
(function () {
  const sw = document.getElementById("is_active_switch");
  const hid = document.getElementById("is_active");
  const lab = document.getElementById("is_active_label");
  function sync() {
    hid.value = sw.checked ? 1 : 0;
    if (lab) lab.textContent = sw.checked ? "Active" : "Inactive";
  }
  sw?.addEventListener("change", sync);
})();

(function () {
  const sw = document.getElementById("is_hot_product_switch");
  const hid = document.getElementById("is_hot_product");
  const lab = document.getElementById("is_hot_product_label");
  function sync() {
    hid.value = sw.checked ? 1 : 0;
    if (lab) lab.textContent = sw.checked ? "Active" : "Inactive";
  }
  sw?.addEventListener("change", sync);
})();

// ===== 2. PRODUCT STOCK ADJUSTMENT CALCULATION =====
(function () {
  const newQtyInput = document.getElementById("new_quantity");
  const currentQtyInput = document.getElementById("current_quantity");
  const adjustmentInfo = document.getElementById("adjustment_info");
  const adjustmentType = document.getElementById("adjustment_type");
  const adjustmentAmount = document.getElementById("adjustment_amount");

  function calculateAdjustment() {
    if (!newQtyInput || !currentQtyInput) return;

    const newQty = parseInt(newQtyInput.value) || 0;
    const currentQty = parseInt(currentQtyInput.value) || 0;
    const difference = newQty - currentQty;

    if (newQtyInput.value === "" || newQtyInput.disabled || difference === 0) {
      adjustmentInfo.style.display = "none";
      return;
    }

    adjustmentInfo.style.display = "block";

    const langIncreaseStock = window.outletProductLang?.type_increase;
    const langDecreaseStock = window.outletProductLang?.type_decrease;

    if (difference > 0) {
      adjustmentInfo.className = "alert alert-success py-1 px-2";
      adjustmentType.textContent = langIncreaseStock;
      adjustmentAmount.textContent = "+" + difference + " pcs";
    } else {
      adjustmentInfo.className = "alert alert-danger py-1 px-2";
      adjustmentType.textContent = langDecreaseStock;
      adjustmentAmount.textContent = "-" + Math.abs(difference) + " pcs";
    }

    adjustmentInfo.style.display = "block";
  }

  newQtyInput?.addEventListener("input", calculateAdjustment);
  calculateAdjustment(); // Initial calculation
})();

// ===== 3. OPTION STOCK ADJUSTMENT CALCULATION =====
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

  // Attach event listeners to all option new quantity inputs
  document.querySelectorAll(".opt-new-qty").forEach((input) => {
    const optId = input.dataset.optId;
    input.addEventListener("input", () => calculateOptionAdjustment(optId));
    calculateOptionAdjustment(optId); // Initial calculation
  });
})();

// ===== 4. TOGGLE ALWAYS AVAILABLE =====
(function () {
  // Product Always Available
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

  // Options Always Available
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
    syncOpt(); // Initial sync
  });
})();

// ===== 5. PRODUCT STOCK TYPE TOGGLE =====
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
  handleProductStockType(); // Initial state
})();

// ===== 6. OPTION STOCK TYPE TOGGLE =====
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
      // Direct
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
  let currentPartnerId = null;

  const SAVE_URL = "/owner/user-owner/outlet-products/recipe/save";
  const LOAD_URL = "/owner/user-owner/outlet-products/recipe/load";
  const ingredientsUrl = "/owner/user-owner/outlet-products/recipe/ingredients";
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

  // FUNGSI MODAL MURNI BS4 / JQUERY
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

  // Load Data dari Server
  function loadRecipeData() {
    const container = document.getElementById("recipe-items-container");
    container.innerHTML =
      '<div class="text-center py-3"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat bahan baku...</div>';

    // Validasi: Pastikan currentPartnerId ada
    if (!currentPartnerId) {
      container.innerHTML = `<div class="alert alert-danger">Error: Partner ID tidak ditemukan. Pastikan data produk sudah dimuat dengan benar.</div>`;
      console.error(
        "currentPartnerId tidak ditemukan. Periksa atribut data-partner-id pada button."
      );
      return;
    }

    // STEP 1: Fetch data ingredients dengan filter partner_id
    fetch(`${ingredientsUrl}?partner_id=${currentPartnerId}`)
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
    const stockData = cachedIngredients.find((s) => s.id === stockId);
    unitSelectElement.innerHTML = "";

    if (
      stockData &&
      stockData.available_units &&
      stockData.available_units.length > 0
    ) {
      stockData.available_units.forEach((u) => {
        const selected = u.id == selectedUnitId ? "selected" : "";
        const label = u.id == stockData.current_unit_id ? `${u.name}` : u.name;

        unitSelectElement.innerHTML += `<option value="${u.id}" ${selected}>${label}</option>`;
      });

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
    <div class="recipe-item" style="margin-bottom: var(--spacing-md); padding: var(--spacing-md); background: var(--card-bg); border: 2px solid var(--border-color); border-radius: var(--radius-sm); transition: all var(--transition-base); position: relative;">
      <button type="button" class="btn-remove remove-recipe-item" style="position: absolute; top: var(--spacing-sm); right: var(--spacing-sm);">
        <span class="material-symbols-outlined">close</span>
      </button>
      
      <div class="row align-items-end">
        <div class="col-md-5">
          <div class="form-group-modern">
            <label class="form-label-modern">
              Bahan Baku
            </label>
            <div class="select-wrapper">
              <select class="form-control-modern recipe-stock-select" required>
                ${stockOptionsHtml}
              </select>
              <span class="material-symbols-outlined select-arrow">expand_more</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group-modern">
            <label class="form-label-modern">
              Jumlah
            </label>
            <input type="number" class="form-control-modern recipe-quantity" 
                   min="0" step="0.01" placeholder="0" value="${
                     qty || ""
                   }" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group-modern">
            <label class="form-label-modern">
              Unit
            </label>
            <div class="select-wrapper">
              <select class="form-control-modern recipe-unit-select" required>
                <option value="">-</option>
              </select>
              <span class="material-symbols-outlined select-arrow">expand_more</span>
            </div>
          </div>
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
        '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
    }

    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

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
          saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Simpan Resep';
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
    currentPartnerId = this.dataset.partnerId;
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
      currentPartnerId = btn.dataset.partnerId;
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
