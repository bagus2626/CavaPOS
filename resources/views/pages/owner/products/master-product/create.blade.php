@extends('layouts.owner')

@section('title', 'Create Product')
@section('page_title', 'Create New Master Product')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('owner.user-owner.master-products.index') }}" class="btn bg-choco text-white mb-3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Products
                </a>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product Information</h3>
                        <div class="card-tools">

                        </div>
                    </div>

                    <form action="{{ route('owner.user-owner.master-products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <!-- Basic Product Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" name="product_category" id="product_category" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <div class="input-group">
                                            <input type="number" id="quantity" name="quantity" class="form-control text-center" value="0" min="0" required>
                                            <button type="button" class="btn btn-outline-secondary ml-1" onclick="decreaseQuantity()">-</button>
                                            <button type="button" class="btn btn-outline-secondary ml-1" onclick="increaseQuantity()">+</button>
                                            <button type="button" class="btn btn-outline-secondary ml-1" onclick="maxQuantity('quantity')">Max</button>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" id="price" name="price" class="form-control" placeholder="Enter product price" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Promo</label>
                                        <select class="form-control" name="promotion_id" id="promotion_id">
                                            <option value="">Select Promotion</option>
                                            @foreach($promotions as $promotion)
                                                <option value="{{ $promotion->id }}">{{ $promotion->promotion_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Product Images (Max 5)</label>
                                        <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple required>
                                        <small class="text-muted">You can upload up to 5 images.</small>
                                    </div>

                                    <!-- Preview -->
                                    <div id="image-preview" class="d-flex flex-wrap mt-2"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control summernote" rows="3" placeholder="Enter detailed product description"></textarea>
                            </div>

                            <hr>

                            <div class="row" id="menu-options-container">
                                <!-- Dynamic option forms will be added here -->
                            </div>

                            <!-- Options -->
                            <h4 class="mb-3 d-flex justify-content-between align-items-center">
                                Options
                                <button type="button" class="btn btn-sm btn-primary" onclick="addMenuOption()">
                                    + Add Menu Option
                                </button>
                            </h4>


                        </div>

                        <div class="card-footer text-right">
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function decreaseQuantity() {
        let input = document.getElementById("quantity");
        let value = parseInt(input.value) || 0;
        if (value > 0) {
            input.value = value - 1;
        }
    }

    function increaseQuantity() {
        let input = document.getElementById("quantity");
        let value = parseInt(input.value) || 0;
        input.value = value + 1;
    }

    function maxQuantity(elementId) {
        let input = document.getElementById(elementId);
        let value = parseInt(input.value) || 0;
        input.value = 999999999;
    }
</script>
<script>
    const priceInput = document.getElementById('price');

    priceInput.addEventListener('input', function (e) {
        // Hapus semua non-digit
        let value = this.value.replace(/[^,\d]/g, '');
        // Ubah ke format ribuan
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });
</script>
<script>
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        previewContainer.innerHTML = "";

        // Validasi maksimal 5 gambar
        if (this.files.length > 5) {
            alert("You can only upload up to 5 images.");
            this.value = "";
            return;
        }

        // Preview gambar
        Array.from(this.files).forEach(file => {
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("img-thumbnail", "m-1");
                    img.style.width = "120px";
                    img.style.height = "120px";
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
<script>
let menuIndex = 0;

function addMenuOption() {
    menuIndex++;
    let container = document.getElementById('menu-options-container');

    let html = `
        <div class="col-12 menu-option mb-3" data-menu-index="${menuIndex}">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="card-title">Menu Option ${menuIndex}</h5>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeMenuOption(this)">X</button>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Menu Name</label>
                                <input type="text" name="menu_options[${menuIndex}][name]" class="form-control" placeholder="Enter menu name" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Menu Description</label>
                                <input name="menu_options[${menuIndex}][description]" class="form-control" rows="2"></input>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Pilihan</label>
                                <select class="form-control" name="menu_options[${menuIndex}][provision]" id="menu_options[${menuIndex}][provision]" required>
                                    <option value="">Select Provision</option>
                                    <option value="OPTIONAL">Opsional</option>
                                    <option value="OPTIONAL MAX">Opsional, Maksimal Pilih</option>
                                    <option value="MAX">Wajib, Maksimal Pilih</option>
                                    <option value="EXACT">Wajib, Pilih</option>
                                    <option value="MIN">Wajib, Minimal Pilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 jumlah-pilihan">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <input type="number"
                                        id="menu_options[${menuIndex}][provision_value]"
                                        name="menu_options[${menuIndex}][provision_value]"
                                        class="form-control" min="0" value="0"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="options-container" id="options-container-${menuIndex}"></div>

                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addOption(${menuIndex})">
                        + Add Option
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeMenuOption(button) {
    button.closest('.menu-option').remove();
}

function addOption(menuIndex) {
    let container = document.getElementById('options-container-' + menuIndex);
    let optionIndex = container.children.length + 1;

    let html = `
        <div class="card mb-2 option-item">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Option ${optionIndex}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">Remove</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Option Name</label>
                            <input type="text" name="menu_options[${menuIndex}][options][${optionIndex}][name]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="menu_options[${menuIndex}][options][${optionIndex}][price]" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="menu_options[${menuIndex}][options][${optionIndex}][description]" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
}

function removeOption(button) {
    button.closest('.option-item').remove();
}

function previewImage(event, menuIndex, optionIndex) {
    let file = event.target.files[0];
    let preview = document.getElementById(`preview-${menuIndex}-${optionIndex}`);
    if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
}

//hide element saat optional
function provisionOption(selectEl) {
  const root = selectEl.closest('.menu-option');       // bungkus 1 card
  if (!root) return;
  const jumlahCol = root.querySelector('.jumlah-pilihan');
  if (!jumlahCol) return;

  const jumlahInput = jumlahCol.querySelector('input[name$="[provision_value]"]');

  if (selectEl.value === 'OPTIONAL') {
    // sembunyikan & disable agar tidak ikut form-validation/submit
    jumlahCol.classList.add('d-none');   // Bootstrap utility
    if (jumlahInput) {
      jumlahInput.disabled = true;
      jumlahInput.required = false;
      jumlahInput.value = 0; // opsional: set 0 saat disembunyikan
    }
  } else {
    // tampilkan & aktifkan lagi
    jumlahCol.classList.remove('d-none');
    if (jumlahInput) {
      jumlahInput.disabled = false;
      jumlahInput.required = true;
    }
  }
}

// Inisialisasi untuk elemen yang sudah dirender Blade
document.addEventListener('DOMContentLoaded', () => {
  document
    .querySelectorAll('#menu-options-container select[name^="menu_options"][name$="[provision]"]')
    .forEach(provisionOption);
});

// Event delegation agar elemen baru dari JS juga ikut
document.getElementById('menu-options-container')
  .addEventListener('change', (e) => {
    if (e.target.matches('select[name^="menu_options"][name$="[provision]"]')) {
      provisionOption(e.target);
    }
  });
</script>
@endsection
