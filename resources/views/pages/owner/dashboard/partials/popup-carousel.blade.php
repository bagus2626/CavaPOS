@if (isset($popups) && $popups->count())
    <div class="modal fade popup-modal" id="popupCarouselModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content popup-modal-content border-0">

                <div class="popup-inner p-0">
                    <div id="popupCarousel" class="carousel slide" data-ride="carousel">

                        {{-- Dots indicator kalau lebih dari 1 --}}
                        @if ($popups->count() > 1)
                            <ol class="carousel-indicators popup-indicators">
                                @foreach ($popups as $idx => $popup)
                                    <li data-target="#popupCarousel" data-slide-to="{{ $idx }}"
                                        class="{{ $idx === 0 ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        @endif

                        <div class="carousel-inner">
                            @foreach ($popups as $idx => $popup)
                                @php
                                    $firstImage = null;
                                    $imageLink = null;

                                    // Cek apakah ada gambar di attachments
                                    if ($popup->attachments && $popup->attachments->count()) {
                                        foreach ($popup->attachments as $att) {
                                            if (Str::startsWith($att->mime_type, 'image/')) {
                                                $firstImage = Storage::disk('public')->url($att->file_path);
                                                break;
                                            }
                                        }
                                    }

                                    // Jika tidak ada di attachments, cek di body
                                    if (!$firstImage && !empty($popup->body)) {
                                        preg_match('/<img[^>]+src="([^">]+)"/', $popup->body, $matches);
                                        if (isset($matches[1])) {
                                            $firstImage = $matches[1];
                                        }
                                    }

                                    // Ekstrak link dari body jika ada (cari tag <a> yang membungkus <img> atau link pertama di body)
                                    if (!empty($popup->body)) {
                                        // Cari link yang membungkus image
                                        preg_match(
                                            '/<a[^>]+href="([^">]+)"[^>]*>[\s\S]*?<img/',
                                            $popup->body,
                                            $linkMatches,
                                        );
                                        if (isset($linkMatches[1])) {
                                            $imageLink = $linkMatches[1];
                                        } else {
                                            // Jika tidak ada link di image, cari link pertama di body
                                            preg_match('/<a[^>]+href="([^">]+)"/', $popup->body, $linkMatches);
                                            if (isset($linkMatches[1])) {
                                                $imageLink = $linkMatches[1];
                                            }
                                        }

                                        // Pastikan link memiliki protokol
                                        if ($imageLink && !preg_match('/^https?:\/\//i', $imageLink)) {
                                            $imageLink = 'https://' . $imageLink;
                                        }
                                    }

                                @endphp

                                <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                    <div class="popup-full-image-container">
                                        @if ($firstImage)
                                            @if ($imageLink)
                                                <a href="{{ $imageLink }}" target="_blank"
                                                    class="popup-full-image-link">
                                                    <img src="{{ $firstImage }}" alt="Popup Image"
                                                        class="popup-full-screen-image">
                                                    <div class="popup-link-badge">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </div>
                                                </a>
                                            @else
                                                <img src="{{ $firstImage }}" alt="Popup Image"
                                                    class="popup-full-screen-image">
                                            @endif
                                        @else
                                            <div class="popup-no-image">
                                                <i class="fas fa-image"></i>
                                                <p>No Image Available</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        :root {
            --choco: #8c1000;
            --soft-choco: #c12814;
        }

        /* ===== Modal Container - TRANSPARENT ===== */
        .popup-modal .modal-dialog {
            max-width: 1000px;
        }

        .popup-modal-content {
            border-radius: 0;
            overflow: visible;
            background: transparent !important;
            border: none;
            box-shadow: none;
            position: relative;
            padding: 0;
        }

        .popup-inner {
            border-radius: 0;
            background: transparent !important;
            padding: 0;
        }

        /* ===== Backdrop untuk modal ===== */
        .popup-modal.modal {
            background: transparent !important;
        }

        /* ===== Full Image Container ===== */
        .popup-full-image-container {
            position: relative;
            width: 100%;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            overflow: visible;
        }

        /* ===== Image Styles ===== */
        .popup-full-screen-image {
            max-width: 100%;
            max-height: 100%;   
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease, filter 0.3s ease;
            box-shadow: none;
            border-radius: 8px;
        }

        /* ===== Link Wrapper ===== */
        .popup-full-image-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            max-width: 100%;
            max-height: 100%;
        }

        .popup-full-image-link:hover .popup-full-screen-image {
            transform: scale(1.02);
            filter: brightness(1.1);
        }

        .popup-full-image-link:hover .popup-link-badge {
            opacity: 1;
            transform: scale(1);
        }

        /* ===== Link Badge - Fixed to Image Corner ===== */
        .popup-link-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            transform: scale(0.9);
            background: rgba(140, 16, 0, 0.95);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
            box-shadow: 0 8px 24px rgba(140, 16, 0, 0.6);
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.3);
            z-index: 10;
        }

        .popup-link-badge i {
            font-size: 1.3rem;
        }

        /* ===== No Image Placeholder ===== */
        .popup-no-image {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
        }

        .popup-no-image i {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .popup-no-image p {
            font-size: 1.2rem;
            font-weight: 500;
            margin: 0;
            opacity: 0.7;
        }

        /* ===== Carousel Indicators ===== */
        .popup-indicators {
            bottom: -50px;
            z-index: 10;
            margin: 0;
        }

        .popup-indicators li {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.6);
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .popup-indicators .active {
            background: linear-gradient(135deg, var(--choco), var(--soft-choco));
            border-color: var(--choco);
            box-shadow: 0 0 15px rgba(140, 16, 0, 0.8);
            transform: scale(1.2);
        }

        .popup-indicators li:hover {
            background-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.1);
        }

        /* ===== Responsive ===== */
        @media (max-width: 991.98px) {
            .popup-modal .modal-dialog {
                max-width: 95%;
            }

            .popup-full-image-container {
                height: 500px;
            }

            .popup-link-badge {
                width: 50px;
                height: 50px;
            }

            .popup-link-badge i {
                font-size: 1.3rem;
            }

            .popup-indicators li {
                width: 10px;
                height: 10px;
            }

            .popup-indicators {
                bottom: -40px;
            }
        }

        @media (max-width: 767.98px) {
            .popup-modal .modal-dialog {
                max-width: 95%;
                margin: 0.5rem auto;
            }

            .popup-full-image-container {
                height: 400px;
            }

            .popup-link-badge {
                width: 45px;
                height: 45px;
            }

            .popup-link-badge i {
                font-size: 1.2rem;
            }

            .popup-no-image i {
                font-size: 3rem;
            }

            .popup-no-image p {
                font-size: 1rem;
            }

            .popup-indicators {
                bottom: -35px;
            }

            .popup-indicators li {
                width: 8px;
                height: 8px;
                margin: 0 3px;
            }
        }

        @media (max-width: 575.98px) {
            .popup-full-image-container {
                height: 350px;
            }

            .popup-link-badge {
                width: 40px;
                height: 40px;
            }

            .popup-link-badge i {
                font-size: 1rem;
            }
        }

        /* ===== Animation untuk modal ===== */
        .popup-modal.fade .modal-dialog {
            transform: scale(0.9);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .popup-modal.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }
    </style>

    @push('scripts')
        <script>
            $(function() {
                // Inisialisasi carousel dengan auto-slide
                $('#popupCarousel').carousel({
                    interval: 4000,
                    ride: 'carousel',
                    wrap: true,
                    pause: false
                });

                let isManuallyPaused = false;

                // Ketika user klik di area modal (kecuali gambar dengan link), pause carousel
                $('#popupCarouselModal').on('click', function(e) {
                    // Jangan pause jika yang diklik adalah link gambar
                    if ($(e.target).closest('.popup-full-image-link').length > 0) {
                        return;
                    }

                    if (!isManuallyPaused) {
                        $('#popupCarousel').carousel('pause');
                        isManuallyPaused = true;

                        // Resume otomatis setelah 7 detik
                        setTimeout(function() {
                            isManuallyPaused = false;
                            $('#popupCarousel').carousel('cycle');
                        }, 7000);
                    }
                });

                // Tampilkan modal
                $('#popupCarouselModal').modal('show');
            });
        </script>
    @endpush
@endif
