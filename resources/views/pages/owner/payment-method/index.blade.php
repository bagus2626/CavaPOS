@extends('layouts.owner')

@section('title', __('messages.owner.payment_methods.payment_method_list'))
@section('page_title', __('messages.owner.payment_methods.payment_methods'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.payment_methods.payment_methods') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.payment_methods.subtitle') }}</p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
          <div class="alert-content">
            {{ session('success') }}
          </div>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            {{ session('error') }}
          </div>
        </div>
      @endif

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="table-controls">
            <form method="GET"
                action="{{ route('owner.user-owner.payment-methods.index') }}"
                class="payment-search-form">
              <div class="input-wrapper search-wrapper" style="position: relative;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>

                <input type="text"
                  id="searchInput"
                  name="search"
                  class="form-control-modern with-icon"
                  value="{{ request('search') }}"
                  placeholder="{{ __('messages.owner.payment_methods.search_placeholder') }}">

                <button type="button"
                  id="clearSearchBtn"
                  class="search-clear-btn"
                  style="display:none;">
                  <span class="material-symbols-outlined">close</span>
                </button>

                <noscript>
                  <button type="submit" class="btn-modern btn-secondary-modern">
                    {{ __('messages.owner.payment_methods.search') ?? 'Search' }}
                  </button>
                </noscript>
              </div>
            </form>


            <div style="display: flex; gap: var(--spacing-sm);">
              
              <a href="{{ route('owner.user-owner.payment-methods.create') }}" class="btn-modern btn-primary-modern">
                <span class="material-symbols-outlined">add</span>
                {{ __('messages.owner.payment_methods.add_payment_method') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      @include('pages.owner.payment-method.display')

    </div>
  </div>

@endsection

<style>
.qris-modal {
  position: fixed;
  inset: 0;
  display: none;
  z-index: 2000; /* pastikan di atas navbar */
}

.qris-modal.is-open {
  display: block;
}

.qris-modal__backdrop {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.55);
}

.qris-modal__dialog {
  position: relative;
  width: min(720px, calc(100% - 24px));
  max-height: calc(100vh - 120px);
  margin: 90px auto 30px; /* ganti sesuai navbar */
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  z-index: 1; /* di atas backdrop */
}

@media (max-width: 576px) {
  .qris-modal__dialog {
    margin: 70px auto 20px;
    max-height: calc(100vh - 90px);
  }
}

.qris-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-bottom: 1px solid rgba(0,0,0,.08);
}

.qris-modal__title { margin: 0; font-size: 16px; }

.qris-modal__close {
  border: none;
  background: transparent;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
}

.qris-modal__body {
  padding: 12px;
  text-align: center;
  overflow: auto;
}

.qris-modal__img{
  max-height: 80vh;
  width: auto;
  max-width: 100%;
  object-fit: contain;
}

.payment-search-form {
  flex: 1;
  max-width: 520px; /* desktop lebih lebar */
  width: 100%;
}

.input-wrapper {
  position: relative;
}

/* ========= SEARCH INPUT LAYOUT (FINAL) ========= */
.input-wrapper {
  position: relative;
  width: 100%;
}

/* input utama */
.input-wrapper input.form-control-modern{
  width: 100%;
  padding-left: 44px;   /* ruang icon search kiri */
  padding-right: 44px;  /* ruang tombol X kanan */
}

/* icon search kiri */
.input-wrapper .input-icon{
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #999;
}

/* tombol clear (X) */
.search-clear-btn{
  position: absolute;
  right: 50px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  color: #999;
  cursor: pointer;
  padding: 0;
  display: none;
  align-items: center;
  z-index: 2;
}

.search-clear-btn:hover{
  color: #ae1504;
}


/* ===== FORCE FULL WIDTH SEARCH ON MOBILE ===== */
@media (max-width: 768px) {
  .table-controls{
    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    gap: var(--spacing-md);
    width: 100%;
  }

  .payment-search-form{
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
  }

  .payment-search-form .input-wrapper,
  .payment-search-form .search-wrapper,
  .payment-search-form input{
    width: 100% !important;
    max-width: 100% !important;
  }

  /* button add juga full width biar rapi */
  .table-controls > div:last-child{
    width: 100% !important;
  }

  .table-controls > div:last-child .btn-modern{
    width: 100% !important;
    justify-content: center;
  }
}


</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    if (!searchInput || !clearBtn) return;

    let timer;

    function toggleClearBtn(){
      clearBtn.style.display = (searchInput.value || '').trim() ? 'flex' : 'none';
    }

    function applySearch(keyword){
      const url = new URL(window.location.href);

      if (keyword) url.searchParams.set('search', keyword);
      else url.searchParams.delete('search');

      url.searchParams.delete('page'); // reset page saat keyword berubah
      window.location.href = url.toString();
    }

    toggleClearBtn();

    searchInput.addEventListener('input', function(){
      toggleClearBtn();
      clearTimeout(timer);
      timer = setTimeout(() => {
        applySearch(searchInput.value.trim());
      }, 350);
    });

    clearBtn.addEventListener('click', function(){
      searchInput.value = '';
      toggleClearBtn();
      applySearch('');
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.js-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const name = form.dataset.name || 'data ini';

            Swal.fire({
                title: '{{ __('messages.owner.payment_methods.delete_confirmation_title') ?? 'Hapus data?' }}',
                text: `{{ __('messages.owner.payment_methods.delete_confirmation_text') ?? 'Metode pembayaran' }} "${name}" {{ __('messages.owner.payment_methods.delete_confirmation_suffix') ?? 'akan dihapus.' }}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('messages.owner.payment_methods.confirm_delete') ?? 'Ya, hapus' }}',
                cancelButtonText: '{{ __('messages.owner.payment_methods.cancel') ?? 'Batal' }}',
                reverseButtons: true,
                confirmButtonColor: '#ae1504',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  let activeModal = null;

  function openModal(modal) {
    if (!modal) return;
    activeModal = modal;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden'; // lock scroll
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = ''; // restore scroll
    if (activeModal === modal) activeModal = null;
  }

  // open from trigger
  document.querySelectorAll('.js-qris-open').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const selector = btn.getAttribute('data-modal');
      openModal(document.querySelector(selector));
    });
  });

  // close when click backdrop or close button
  document.addEventListener('click', (e) => {
    const closeEl = e.target.closest('[data-close]');
    if (!closeEl) return;

    const modal = e.target.closest('.qris-modal');
    closeModal(modal);
  });

  // close on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && activeModal) closeModal(activeModal);
  });
});
</script>

@endpush