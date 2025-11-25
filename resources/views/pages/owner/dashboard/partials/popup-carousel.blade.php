@if(isset($popups) && $popups->count())
<div class="modal fade popup-modal" id="popupCarouselModal" tabindex="-1" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content popup-modal-content border-0">

            {{-- Tombol close melayang --}}
            <button type="button" class="popup-close-btn" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="popup-inner p-0">
                <div id="popupCarousel" class="carousel slide" data-ride="carousel">

                    {{-- Dots indicator kalau lebih dari 1 --}}
                    @if($popups->count() > 1)
                        <ol class="carousel-indicators popup-indicators">
                            @foreach($popups as $idx => $popup)
                                <li data-target="#popupCarousel"
                                    data-slide-to="{{ $idx }}"
                                    class="{{ $idx === 0 ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                    @endif

                    <div class="carousel-inner">
                        @foreach($popups as $idx => $popup)
                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                <div class="popup-slide-card">

                                    {{-- Title + waktu kecil di atas --}}
                                    <div class="popup-header d-flex justify-content-between align-items-start">
                                        <div>
                                            @if(!empty($popup->title))
                                                <h5 class="popup-title mb-1">
                                                    {{ $popup->title }}
                                                </h5>
                                            @endif
                                        </div>

                                        @if($popups->count() > 1)
                                            <span class="popup-counter badge badge-light">
                                                {{ $idx + 1 }} / {{ $popups->count() }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Konten utama --}}
                                    <div class="popup-body">
                                        <div class="popup-body-content">
                                            {!! $popup->body !!}
                                        </div>

                                        {{-- Attachments kalau ada --}}
                                        @if($popup->attachments && $popup->attachments->count())
                                            <div class="popup-attachments mt-2 pt-2">
                                                <div class="d-flex flex-wrap">
                                                    @foreach($popup->attachments as $att)
                                                        @php
                                                            $url = Storage::disk('public')->url($att->file_path);
                                                            $isImage = Str::startsWith($att->mime_type, 'image/');
                                                        @endphp

                                                        @if($isImage)
                                                            <a href="{{ $url }}" target="_blank" class="mr-2 mb-2">
                                                                <img src="{{ $url }}"
                                                                     alt="{{ $att->file_name }}"
                                                                     class="popup-attachment-image">
                                                            </a>
                                                        @else
                                                            <a href="{{ $url }}" target="_blank" class="popup-attachment-chip">
                                                                <i class="fas fa-paperclip mr-1"></i>
                                                                {{ $att->file_name }}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Controls kiri/kanan kalau > 1 --}}
                    @if($popups->count() > 1)
                        <a class="carousel-control-prev popup-control-prev" href="#popupCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next popup-control-next" href="#popupCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    :root {
        /* fallback kalau belum didefinisikan */
        --choco: #8c1000;
        --soft-choco: #c12814;
    }

    /* ===== Shell modal ===== */
    .popup-modal .modal-dialog {
        max-width: 720px;
    }

    .popup-modal-content {
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;                    /* putih polos, tanpa gradasi */
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.2);
        position: relative;
        padding: 0;
    }

    .popup-inner {
        border-radius: 14px;
        background: #f9fafb;
        padding: .75rem;
    }

    /* Strip accent merah di atas kartu */
    .popup-slide-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--choco), var(--soft-choco));
        border-radius: 16px 16px 0 0;
    }

    /* Close button melayang */
    .popup-close-btn {
        position: absolute;
        top: 10px;
        right: 12px;
        border: none;
        background: rgba(140, 16, 0, 0.06);
        border-radius: 999px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        line-height: 1;
        padding: 0;
        cursor: pointer;
        transition: all .15s ease-in-out;
        z-index: 5;
        color: #4b5563;
    }

    .popup-close-btn span {
        margin-top: -2px;
    }

    .popup-close-btn:hover {
        background: rgba(140, 16, 0, 0.14);
        color: var(--choco);
    }

    /* ===== Carousel & slide card ===== */
    .popup-slide-card {
        position: relative;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        padding: 0.85rem 1rem 0.9rem;
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .popup-header {
        padding-top: .35rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid #eef1f4;
        margin-bottom: 0.45rem;
    }

    .popup-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
    }

    .popup-meta {
        font-size: 0.78rem;
        color: #6b7280;
    }

    .popup-counter {
        border-radius: 999px;
        font-size: 0.75rem;
        padding: 0.15rem 0.6rem;
        background: rgba(140, 16, 0, 0.06);
        color: var(--choco);
    }

    .popup-body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding-right: 0.25rem;
    }

    .popup-body-content {
        font-size: 0.92rem;
        color: #374151;
    }

    /* Gambar dari Quill di dalam body popup */
    #popupCarouselModal .popup-body-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0.45rem auto;
        border-radius: 10px;
    }

    @media (min-width: 768px) {
        #popupCarouselModal .popup-body-content img {
            max-height: 380px;
            object-fit: contain;
        }
    }

    /* Support alignment bawaan Quill */
    #popupCarouselModal .popup-body-content .ql-align-center img {
        margin-left: auto;
        margin-right: auto;
    }

    #popupCarouselModal .popup-body-content .ql-align-right img {
        margin-left: auto;
        margin-right: 0;
    }

    /* ===== Attachments ===== */
    .popup-attachments {
        border-top: 1px dashed #e5e7eb;
        margin-top: 0.5rem;
        padding-top: 0.4rem;
    }

    .popup-attachment-image {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .popup-attachment-image:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .popup-attachment-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        margin: 3px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #111827;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }

    .popup-attachment-chip:hover {
        background: rgba(140, 16, 0, 0.06);
        border-color: var(--choco);
        text-decoration: none;
    }

    .popup-attachment-chip i {
        color: var(--choco);
    }

    /* ===== Indicators & controls ===== */
    .popup-indicators {
        bottom: -4px;
    }

    .popup-indicators li {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background-color: #e5e7eb;
    }

    .popup-indicators .active {
        background: linear-gradient(135deg, var(--choco), var(--soft-choco));
    }

    .popup-control-prev,
    .popup-control-next {
        width: 8%;
    }

    .popup-control-prev .carousel-control-prev-icon,
    .popup-control-next .carousel-control-next-icon {
        filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.4));
    }

    /* ===== Responsive tweak ===== */
    @media (max-width: 575.98px) {
        .popup-inner {
            padding: .5rem;
        }

        .popup-slide-card {
            padding: 0.7rem 0.75rem 0.75rem;
        }

        .popup-title {
            font-size: 0.95rem;
        }
    }
</style>


@push('scripts')
<script>
    $(function () {
        // Inisialisasi carousel: auto-slide tiap 3 detik, looping
        $('#popupCarousel').carousel({
            interval: 3000,
            ride: 'carousel',
            wrap: true,
            pause: false
        });

        // Flag untuk memastikan kita hanya stop sekali
        let popupCarouselStopped = false;

        // Kalau user klik DI DALAM modal, hentikan auto-slide
        $('#popupCarouselModal').on('click', function () {
            if (!popupCarouselStopped) {
                $('#popupCarousel').carousel('pause');
                popupCarouselStopped = true;
            }
        });

        // Tampilkan modal otomatis ketika halaman selesai load
        $('#popupCarouselModal').modal('show');
    });
</script>
@endpush
@endif
