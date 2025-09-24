@extends('layouts.owner')

@section('title', 'Update Master Product')
@section('page_title', 'Update Master Product')

@section('content')
<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Periksa kembali input kamu:</strong>
                <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <a href="{{ route('owner.user-owner.master-products.index') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left mr-2"></i>Back to Master Products
        </a>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Master Product</h3>
            </div>

            <form action="{{ route('owner.user-owner.master-products.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Basic Product Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $data->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="product_category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if($data->category_id == $category->id) selected @endif>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity & Price -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="input-group">
                                    <input type="number" id="quantity" name="quantity" class="form-control text-center"
                                        value="{{ $data->quantity }}" min="0" required>
                                    <button type="button" class="btn btn-outline-secondary ml-1" onclick="decreaseQuantity()">-</button>
                                    <button type="button" class="btn btn-outline-secondary ml-1" onclick="increaseQuantity()">+</button>
                                    <button type="button" class="btn btn-outline-secondary ml-1" onclick="maxQuantity('quantity')">Max</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" id="price" name="price" class="form-control"
                                        value="{{ number_format($data->price,0,',','.') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="mb-1" for="promotion_id">Promotion</label>
                                <select id="promotion_id" name="promotion_id" class="form-control">
                                {{-- kosong = tanpa promo --}}
                                @php
                                    $selectedPromoId = old('promotion_id', $data->promo_id);
                                @endphp
                                <option value="">— No Promotion —</option>
                                @foreach($promotions as $promo)
                                    <option value="{{ $promo->id }}" {{ (string)$selectedPromoId === (string)$promo->id ? 'selected' : '' }}>
                                    {{ $promo->promotion_name }}
                                    (
                                    @if($promo->promotion_type === 'percentage')
                                        {{ number_format($promo->promotion_value, 0, ',', '.') }}% Off
                                    @else
                                        Rp.
                                        @if(fmod($promo->promotion_value, 1) == 0)
                                        {{ number_format($promo->promotion_value, 0, ',', '.') }} Off
                                        @else
                                        {{ number_format($promo->promotion_value, 2, ',', '.') }} Off
                                        @endif
                                    @endif
                                    )
                                    </option>
                                @endforeach
                                </select>
                                @error('promotion_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Existing Images -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Existing Images</label>
                            <div class="d-flex flex-wrap" id="existing-images">
                                @foreach($data->pictures as $pic)
                                    <div class="position-relative m-1">
                                        <img src="{{ asset($pic['path']) }}" style="width:120px;height:120px;" class="img-thumbnail">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                            onclick="removeExistingImage(this, '{{ $pic['filename'] }}')">X</button>
                                        <input type="hidden" name="existing_images[]" value="{{ $pic['filename'] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Upload New Images -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Add New Images (Max 5)</label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                            <small class="text-muted">You can upload up to 5 images.</small>
                            <div id="image-preview" class="d-flex flex-wrap mt-2"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control summernote" rows="3">{{ $data->description }}</textarea>
                    </div>

                    <hr>



                    <div class="row" id="menu-options-container">
                        @foreach($data->parent_options as $pIndex => $parent)
                            <div class="col-12 menu-option mb-3" data-menu-index="{{ $pIndex+1 }}">
                                <input type="hidden" name="menu_options[{{ $pIndex+1 }}][parent_id]" value="{{ $parent->id }}">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="card-title">Menu Option {{ $pIndex+1 }}</h5>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeMenuOption(this)">X</button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Menu Name</label>
                                                    <input type="text" name="menu_options[{{ $pIndex+1 }}][name]"
                                                        value="{{ $parent->name }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Menu Description</label>
                                                    <input type="text" name="menu_options[{{ $pIndex+1 }}][description]"
                                                        value="{{ $parent->description }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pilihan</label>
                                                    <select
                                                    class="form-control provision-select"
                                                    data-index="{{ $pIndex+1 }}"
                                                    name="menu_options[{{ $pIndex+1 }}][provision]"
                                                    id="menu_options[{{ $pIndex+1 }}][provision]"
                                                    required
                                                    >
                                                        <option value="">Select Provision</option>
                                                        <option value="OPTIONAL" {{ $parent->provision === 'OPTIONAL' ? 'selected' : '' }}>Opsional</option>
                                                        <option value="OPTIONAL MAX" {{ $parent->provision === 'OPTIONAL MAX' ? 'selected' : '' }}>Opsional, Maksimal Pilih</option>
                                                        <option value="MAX" {{ $parent->provision === 'MAX' ? 'selected' : '' }}>Wajib, Maksimal Pilih</option>
                                                        <option value="EXACT" {{ $parent->provision === 'EXACT' ? 'selected' : '' }}>Wajib, Pilih</option>
                                                        <option value="MIN" {{ $parent->provision === 'MIN' ? 'selected' : '' }}>Wajib, Minimal Pilih</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2" id="jumlah-options-{{ $pIndex+1 }}">
                                                <div class="form-group">
                                                    <label>Jumlah</label>
                                                    <div class="input-group">
                                                    <input type="number"
                                                        id="menu_options[{{ $pIndex+1 }}][provision_value]"
                                                        name="menu_options[{{ $pIndex+1 }}][provision_value]"
                                                        class="form-control" min="0" value="{{ $parent->provision_value }}"
                                                        required>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="options-container" id="options-container-{{ $pIndex+1 }}">
                                            @foreach($parent->options as $oIndex => $option)
                                                <div class="card mb-2 option-item">
                                                    <input type="hidden" name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][option_id]" value="{{ $option->id }}">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <h6>Option {{ $oIndex+1 }}</h6>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">Remove</button>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Option Name</label>
                                                                    <input type="text" name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][name]"
                                                                        value="{{ $option->name }}" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Quantity</label>
                                                                    <div class="input-group">
                                                                        <input type="number" name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][quantity]"
                                                                            value="{{ $option->quantity }}" class="form-control" min="0" required>
                                                                        <button type="button" class="btn btn-outline-secondary ml-1" onclick="maxQuantity('')">Max</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                {{-- <div class="form-group">
                                                                    <label>Price</label>
                                                                    <input type="number" name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][price]"
                                                                        value="{{ $option->price }}" class="form-control" min="0" required>
                                                                </div> --}}
                                                                <div class="form-group">
                                                                    <label>Price</label>
                                                                    <input type="text" class="form-control currency-display"
                                                                            value="{{ number_format((float)$option->price, 0, ',', '.') }}">
                                                                    <input type="hidden" class="currency-value"
                                                                            name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][price]"
                                                                            value="{{ (float)$option->price }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Description</label>
                                                                    <textarea name="menu_options[{{ $pIndex+1 }}][options][{{ $oIndex+1 }}][description]" class="form-control" rows="2">{{ $option->description }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="button" class="btn btn-sm btn-success mt-2" onclick="addOption({{ $pIndex+1 }})">+ Add Option</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Existing Menu Options -->
                    <h4 class="mb-3 d-flex justify-content-between align-items-center">
                        Options
                        <button type="button" class="btn btn-sm btn-primary" onclick="addMenuOption()">+ Add Menu Option</button>
                    </h4>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">Update Product</button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
{{-- @include('pages.owner.master-products.create') --}}
<script>
function removeExistingImage(button, filename) {
    // Hapus elemen dari DOM
    button.closest('div.position-relative').remove();

    // Bisa juga menambahkan array input untuk dikirim ke backend agar dihapus dari storage/database
}
</script>

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
// let menuIndex = 0;
let menuIndex = {{ isset($data) && isset($data->parent_options) ? $data->parent_options->count() : 0 }};

function addMenuOption() {
    menuIndex++;
    let container = document.getElementById('menu-options-container');

    let html = `
        <div class="col-12 menu-option mb-3" data-menu-index="${menuIndex}">
            <input type="hidden" name="menu_options[${menuIndex}][parent_id]" value=null>
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
                                <select
                                    class="form-control provision-select"
                                    data-index="${menuIndex}"
                                    name="menu_options[${menuIndex}][provision]"
                                    id="menu_options_${menuIndex}_provision"  <!-- hindari id dengan [] -->
                                    required
                                    >
                                    <option value="">Select Provision</option>
                                    <option value="OPTIONAL">Opsional</option>
                                    <option value="OPTIONAL MAX">Opsional, Maksimal Pilih</option>
                                    <option value="MAX">Wajib, Maksimal Pilih</option>
                                    <option value="EXACT">Wajib, Pilih</option>
                                    <option value="MIN">Wajib, Minimal Pilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" id="jumlah-options-${menuIndex}">
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
    let optionIndex = container.querySelectorAll('.option-item').length + 1;

    let html = `
        <div class="card mb-2 option-item">
            <input type="hidden" name="menu_options[${menuIndex}][options][${optionIndex}][option_id]" value=null>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Option ${optionIndex}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">Remove</button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Option Name</label>
                            <input type="text" name="menu_options[${menuIndex}][options][${optionIndex}][name]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Quantity</label>
                            <div class="input-group">
                                <input type="number"
                                    id="menu_options[${menuIndex}][options][${optionIndex}][quantity]"
                                    name="menu_options[${menuIndex}][options][${optionIndex}][quantity]"
                                    class="form-control" min="0" value="0"
                                    required>
                                <button type="button"
                                    class="btn btn-outline-secondary ml-1"
                                    onclick="maxQuantity('menu_options[${menuIndex}][options][${optionIndex}][quantity]')">
                                    Max
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Price</label>
                            <input type="text" class="form-control currency-display" value="">
                            <input type="hidden"
                                    name="menu_options[${menuIndex}][options][${optionIndex}][price]"
                                    class="currency-value" value="0">
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
</script>
<script>
function toggleJumlahByIndex(idx, provisionVal) {
  const box = document.getElementById(`jumlah-options-${idx}`);
  if (!box) return;
  const input = box.querySelector('input[name$="[provision_value]"]');
  const hide = (provisionVal === 'OPTIONAL');

  if (hide) {
    box.classList.add('d-none');       // sembunyikan
    if (input) { input.disabled = true; input.required = false; }
  } else {
    box.classList.remove('d-none');    // tampilkan
    if (input) { input.disabled = false; input.required = true; }
  }
}

// init untuk yang dirender Blade
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.provision-select').forEach(sel => {
    toggleJumlahByIndex(sel.dataset.index, sel.value || '');
  });
});

// update saat select berubah
document.addEventListener('change', function (e) {
  if (!e.target.matches('.provision-select')) return;
  toggleJumlahByIndex(e.target.dataset.index, e.target.value || '');
});
</script>

<script>
// --- helper ---
function unformatRupiah(str) {
  // ambil hanya digit
  const digits = String(str || '').replace(/\D+/g, '');
  return digits ? String(parseInt(digits, 10)) : '';
}
function formatRupiahFromDigits(digits) {
  if (!digits) return '';
  const n = parseInt(digits, 10);
  return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(n);
}
function findHidden(el) {
  // hidden di sibling berikutnya atau dalam form-group yang sama
  const next = el.nextElementSibling;
  if (next && next.classList && next.classList.contains('currency-value')) return next;
  return el.closest('.form-group')?.querySelector('.currency-value') || null;
}
function syncDisplayAndHidden(displayEl) {
  const hidden = findHidden(displayEl);
  const raw = unformatRupiah(displayEl.value);
  if (hidden) hidden.value = raw || '0';
  displayEl.value = formatRupiahFromDigits(raw);
}

// --- init untuk semua currency-display yang sudah ada di DOM ---
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.currency-display').forEach((el) => {
    // jika ada hidden, ambil nilai hidden sebagai sumber kebenaran
    const hidden = findHidden(el);
    const digits = hidden ? unformatRupiah(hidden.value) : unformatRupiah(el.value);
    if (hidden) hidden.value = digits || '0';
    el.value = formatRupiahFromDigits(digits);
  });
});

// --- event delegation: tangkap input apapun dengan class .currency-display ---
document.addEventListener('input', (e) => {
  if (!e.target.classList?.contains('currency-display')) return;
  // simpan posisi caret kasar (opsional: sederhana, caret pindah ke akhir saat format)
  syncDisplayAndHidden(e.target);
});

// --- sinkron terakhir saat submit form (jaga-jaga) ---
document.addEventListener('submit', (e) => {
  const form = e.target.closest('form');
  if (!form) return;
  form.querySelectorAll('.currency-display').forEach(syncDisplayAndHidden);
});
</script>


@endsection
