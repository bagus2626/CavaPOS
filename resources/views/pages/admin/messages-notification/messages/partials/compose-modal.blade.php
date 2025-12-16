<div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-0 border-0">

            <div class="compose-new-mail-sidebar w-100">
                <div class="card shadow-none quill-wrapper p-0">

                    {{-- Header --}}
                    <div class="card-header">
                        <h3 class="card-title" id="emailCompose">New Message</h3>
                    </div>

                    {{-- Form start --}}
                    <form action="{{ route('admin.message-notification.messages.store') }}" method="POST"
                        id="compose-form" enctype="multipart/form-data">
                        @csrf

                        <div class="card-content">
                            <div class="card-body pt-0">

                                {{-- From (readonly) --}}
                                <div class="form-group row">
                                    <label for="emailfrom" class="col-sm-2 col-form-label text-right">From</label>
                                    <input type="text" id="emailfrom" class="form-control col-sm-10"
                                        value="{{ auth()->user()->name ?? 'admin@example.com' }}" disabled>
                                </div>
                                {{-- message type --}}
                                <div class="form-group row">
                                    <label for="messageType" class="col-sm-2 col-form-label text-right">Message
                                        Type</label>
                                    <div class="col-sm-10">
                                        <select name="message_type" id="messageType" class="form-control"
                                            onchange="handleOptions()" required>
                                            <option value="">--Select Message Type--</option>
                                            <option value="message">Message</option>
                                            <option value="popup">Popup Notification</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="target" style="display: none;">
                                    <label for="targetType" class="col-sm-2 col-form-label text-right">Target</label>
                                    <div class="col-sm-10">
                                        <select name="target" id="targetType" class="form-control"
                                            onchange="handleOptions()">
                                            <option value="">--Select Recipient--</option>
                                            <option value="single">To a Single/Multiple Person</option>
                                            <option value="broadcast">Broadcast</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="target-group" style="display: none;">
                                    <label for="targetGroup" class="col-sm-2 col-form-label text-right">Target
                                        Group</label>
                                    <div class="col-sm-10">
                                        <select name="target_group" id="targetGroup" class="form-control">
                                            <option value="">--Select Target--</option>
                                            <option value="all">All</option>
                                            <option value="business-partner">Business Partner (owner, outlet, employees)
                                            </option>
                                            <option value="owner">Owner</option>
                                            <option value="outlet">Outlet</option>
                                            <option value="employee">Employee</option>
                                            <option value="end-customer">End Customer</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="recipient-email" style="display: none;">
                                    <label for="recipientData" class="col-sm-2 col-form-label text-right">Recipient
                                        Account</label>
                                    <div class="col-sm-10">
                                        <select name="recipients_data[]" id="recipientData" class="form-control"
                                            multiple="multiple"></select>
                                    </div>
                                </div>
                                <input type="hidden" name="recipients_meta" id="recipientsMeta">

                                {{-- Schedule range (optional) --}}
                                <div class="form-group row" id="schedule-range-wrapper" style="display:none;">
                                    <label class="col-sm-2 col-form-label text-right">Schedule</label>
                                    <div class="col-sm-10">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-1">
                                                <input type="datetime-local" name="schedule_start" id="scheduleStart"
                                                    class="form-control" placeholder="Start datetime">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <input type="datetime-local" name="schedule_end" id="scheduleEnd"
                                                    class="form-control" placeholder="End datetime">
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-0"
                                            id="clearSchedule">
                                            Hapus jadwal
                                        </button>
                                    </div>
                                </div>

                                {{-- (Opsional) Subject --}}
                                <div class="form-label-group">
                                    <label for="emailSubject">Subject</label>
                                    <input type="text" id="emailSubject" name="subject" class="form-control"
                                        placeholder="Subject">
                                </div>

                                <div class="snow-container border rounded p-50">
                                    {{-- Toolbar di atas, full width dengan fitur lengkap --}}
                                    <div class="compose-quill-toolbar pb-0 mb-25" id="compose-toolbar">
                                        {{-- Style & Font --}}
                                        <span class="ql-formats mr-0">
                                            <select class="ql-header">
                                                <option value="1">Heading 1</option>
                                                <option value="2">Heading 2</option>
                                                <option value="3">Heading 3</option>
                                                <option selected>Normal</option>
                                            </select>
                                        </span>

                                        {{-- Text Formatting --}}
                                        <span class="ql-formats">
                                            <button type="button" class="ql-bold"></button>
                                            <button type="button" class="ql-italic"></button>
                                            <button type="button" class="ql-underline"></button>
                                            <button type="button" class="ql-strike"></button>
                                        </span>

                                        {{-- Colors --}}
                                        <span class="ql-formats">
                                            <select class="ql-color"></select>
                                            <select class="ql-background"></select>
                                        </span>

                                        {{-- Lists & Alignment --}}
                                        <span class="ql-formats">
                                            <button type="button" class="ql-list" value="ordered"></button>
                                            <button type="button" class="ql-list" value="bullet"></button>
                                            <button type="button" class="ql-indent" value="-1"></button>
                                            <button type="button" class="ql-indent" value="+1"></button>
                                        </span>

                                        <span class="ql-formats">
                                            <select class="ql-align"></select>
                                        </span>

                                        {{-- Insert Media --}}
                                        <span class="ql-formats">
                                            <button type="button" class="ql-link"></button>
                                            <button type="button" class="ql-image"></button>
                                            <button type="button" class="ql-video"></button>
                                        </span>

                                        {{-- Clean & Code View --}}
                                        <span class="ql-formats">
                                            <button type="button" class="ql-clean"></button>
                                        </span>
                                    </div>

                                    {{-- Editor area --}}
                                    <div class="compose-editor"></div>

                                    <input type="hidden" name="body" id="quillBody">
                                </div>

                                {{-- Attachment --}}
                                <div class="form-group mt-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="emailAttach"
                                            name="attachments[]" multiple>
                                        <label class="custom-file-label" for="emailAttach">Attach file(s)</label>
                                    </div>

                                    {{-- Preview attachment --}}
                                    <div id="attachment-preview" class="mt-1"></div>
                                </div>


                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="card-footer d-flex justify-content-between align-items-center pt-0">
                            <div class="d-flex align-items-center">
                                <button type="button" id="toggleSchedule"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                    <i class='bx bx-time-five mr-25'></i>
                                    Scheduled
                                </button>
                                <span id="scheduleSummary" class="text-muted small ml-2 d-none"></span>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="button"
                                    class="btn btn-light-secondary cancel-btn mr-1 d-flex align-items-center"
                                    data-dismiss="modal">
                                    <i class='bx bx-x mr-25'></i>
                                    <span class="d-sm-inline d-none">Cancel</span>
                                </button>
                                <button type="submit" class="btn-send btn btn-primary d-flex align-items-center">
                                    <i class='bx bx-send mr-25'></i>
                                    <span class="d-sm-inline d-none">Send</span>
                                </button>
                            </div>
                        </div>
                    </form>
                    {{-- Form end --}}

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>



<script src="{{ asset('script/admin/messages/messages.js') }}"></script>

{{-- preview attachments --}}

<style>
    /* Fix overflow untuk modal dan card */
    .snow-container {
        position: relative;
        overflow: visible !important;
    }

    .compose-quill-toolbar {
        border: none !important;
        border-bottom: 1px solid #ddd !important;
        padding: 8px 5px !important;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        background: #fafafa;
        overflow: visible !important;
    }

    .compose-quill-toolbar .ql-formats {
        margin-right: 8px !important;
        display: inline-flex;
        align-items: center;
        flex-wrap: nowrap;
    }

    .compose-quill-toolbar button,
    .compose-quill-toolbar select {
        margin: 0 2px !important;
    }

    /* Fix untuk dropdown dan tooltip yang terpotong */
    .compose-quill-toolbar .ql-picker {
        position: relative;
    }

    .compose-quill-toolbar .ql-picker-options {
        position: absolute;
        z-index: 1050 !important;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        max-height: 200px;
        overflow-y: auto;
    }

    .compose-quill-toolbar .ql-tooltip {
        position: absolute !important;
        z-index: 1051 !important;
        left: 0 !important;
        top: 100% !important;
        margin-top: 5px !important;
    }

    /* Styling untuk editor area */
    .compose-editor {
        min-height: 200px;
        max-height: 400px;
        overflow-y: auto;
        background: white;
    }

    .compose-editor .ql-editor {
        padding: 12px 15px;
        min-height: 200px;
    }

    .compose-editor.ql-blank::before {
        color: #aaa;
        font-style: italic;
    }

    /* Fix z-index untuk modal agar dropdown tidak tertutup */
    .modal-content {
        overflow: visible !important;
    }

    .card-content {
        overflow: visible !important;
    }

    /* Responsiveness untuk toolbar */
    @media (max-width: 768px) {
        .compose-quill-toolbar {
            padding: 5px 3px !important;
            gap: 3px;
        }

        .compose-quill-toolbar .ql-formats {
            margin-right: 5px !important;
        }

        .compose-quill-toolbar button,
        .compose-quill-toolbar select {
            margin: 0 1px !important;
        }
    }

    /* Tambahan untuk memastikan color picker tidak terpotong */
    .ql-color-picker .ql-picker-options,
    .ql-background .ql-picker-options {
        width: 152px !important;
    }

    /* Fix untuk link/video tooltip input - Posisi dan styling yang lebih baik */
    .ql-snow .ql-tooltip {
        position: absolute !important;
        transform: translateX(-50%) !important;
        left: 50% !important;
        top: 45px !important;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 8px 12px;
        border-radius: 6px;
        z-index: 1061 !important;
        white-space: nowrap;
    }

    .ql-snow .ql-tooltip::before {
        content: "Visit URL:";
        line-height: 26px;
        margin-right: 8px;
        color: #444;
        font-size: 13px;
    }

    .ql-snow .ql-tooltip[data-mode=link]::before {
        content: "Enter link:";
    }

    .ql-snow .ql-tooltip[data-mode=video]::before {
        content: "Enter video:";
    }

    .ql-snow .ql-tooltip input[type=text] {
        width: 200px;
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
        outline: none;
    }

    .ql-snow .ql-tooltip input[type=text]:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
    }

    .ql-snow .ql-tooltip a.ql-action,
    .ql-snow .ql-tooltip a.ql-remove {
        margin-left: 4px;
        padding: 0;
        text-decoration: none;
        font-size: 13px;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: #333 !important;
        cursor: pointer;
    }

    .ql-snow .ql-tooltip a.ql-action:hover {
        text-decoration: underline;
    }

    .ql-snow .ql-tooltip a.ql-action::after {
        content: 'Save';
        border: none;
    }

    .ql-snow .ql-tooltip a.ql-remove::before {
        content: 'Remove';
        color: #dc3545;
    }

    .ql-snow .ql-tooltip a.ql-remove:hover {
        text-decoration: underline;
    }

    /* Hide default content */
    .ql-snow .ql-tooltip a.ql-action::before,
    {
    display: none;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Quill
        var quill = new Quill('.compose-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: '#compose-toolbar',
                    handlers: {
                        // Custom handler untuk image (opsional - untuk kontrol lebih)
                        image: function() {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.click();

                            input.onchange = () => {
                                const file = input.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        const range = quill.getSelection();
                                        quill.insertEmbed(range.index, 'image', e.target
                                            .result);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            };
                        }
                    }
                }
            },
            placeholder: 'Write your message here...'
        });

        // Inisialisasi Select2 dengan AJAX (mirip Gmail)
        $('#recipientData').select2({
            placeholder: 'Pilih atau ketik account',
            multiple: true,
            tags: true,
            tokenSeparators: [',', ' '],
            width: '100%',
            ajax: {
                url: '/admin/message-notification/get-recipients',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        target_group: $('#targetGroup').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            const label = item.name ?
                                item.name + ' - ' + item.email + (item.role ? ' (' + item
                                    .role + ')' : '') :
                                item.email;

                            return {
                                id: item.id,
                                text: label,
                                role: item.role,
                                email: item.email
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Saat target group berubah, kosongkan pilihan recipient
        $('#targetGroup').on('change', function() {
            $('#recipientData').val(null).trigger('change');
        });

        // Array untuk menyimpan file yang dipilih
        let selectedFiles = [];
        const blockedExtensions = ['html', 'htm', 'js', 'mjs', 'vbs', 'exe', 'bat', 'cmd', 'sh', 'php',
            'phtml'
        ];

        // Fungsi untuk mendapatkan ekstensi file
        function getFileExtension(filename) {
            return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase();
        }

        // Fungsi untuk validasi file
        function isFileAllowed(file) {
            const messageType = $('#messageType').val();

            // Validasi khusus untuk popup - hanya terima image
            if (messageType === 'popup') {
                if (!file.type.startsWith('image/')) {
                    return {
                        allowed: false,
                        message: `Popup notification hanya menerima file gambar. File "${file.name}" bukan gambar.`
                    };
                }
            }

            // Validasi blocked extensions
            const ext = getFileExtension(file.name);
            if (blockedExtensions.includes(ext)) {
                return {
                    allowed: false,
                    message: `File dengan ekstensi .${ext} tidak diizinkan untuk keamanan.`
                };
            }

            if (file.type && (file.type === 'text/html' || file.type === 'application/x-httpd-php')) {
                return {
                    allowed: false,
                    message: 'File HTML atau script tidak diizinkan untuk keamanan.'
                };
            }

            return {
                allowed: true
            };
        }

        // Fungsi untuk menampilkan peringatan inline
        function showFileWarning(blockedFiles) {
            const preview = $('#attachment-preview');

            // Hapus peringatan lama jika ada
            $('#file-warning-box').remove();

            if (blockedFiles.length === 0) return;

            const warningBox = $(`
            <div id="file-warning-box" class="alert alert-danger mb-2" role="alert">
                ${blockedFiles.map(name => `
                    <div class="d-flex align-items-center mb-1">
                        <i class='bx bx-error-circle mr-1'></i>
                        ${typeof name === 'string' && name.includes('memerlukan') ? name : `File&nbsp;<strong>${name}</strong>&nbsp;tidak dapat diupload`}
                    </div>
                `).join('')}
            </div>
        `);

            // Tambahkan di awal preview area
            preview.prepend(warningBox);

            // Auto hide setelah 5 detik
            setTimeout(function() {
                warningBox.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Fungsi untuk render preview attachments
        function renderAttachmentPreviews() {
            const preview = $('#attachment-preview');

            // Hapus semua preview kecuali warning box
            preview.children().not('#file-warning-box').remove();

            if (selectedFiles.length === 0) {
                $('.custom-file-label').text('Attach file(s)');
                return;
            }

            // Update label
            const labelText = selectedFiles.map(f => f.name).join(', ');
            $('.custom-file-label').text(labelText);

            selectedFiles.forEach((file, index) => {
                const row = $('<div class="d-flex align-items-center mb-1"></div>');

                // Kolom ikon / thumbnail
                const iconWrapper = $('<div class="mr-1"></div>');

                if (file.type && file.type.startsWith('image/')) {
                    const url = URL.createObjectURL(file);
                    const img = $('<img>')
                        .attr('src', url)
                        .css({
                            width: '40px',
                            height: '40px',
                            'object-fit': 'cover',
                            'border-radius': '4px'
                        });

                    img.on('load', function() {
                        URL.revokeObjectURL(url);
                    });

                    iconWrapper.append(img);
                } else {
                    iconWrapper.append(
                        "<i class='bx bxs-file mr-25' style='font-size: 24px;'></i>"
                    );
                }

                // Kolom teks (nama + ukuran)
                const sizeKB = Math.round(file.size / 1024);
                const text = $(`
                <div class="small flex-grow-1">
                    <div>${file.name}</div>
                    <div class="text-muted">${file.type || 'Unknown type'} • ${sizeKB} KB</div>
                </div>
            `);

                // Kolom tombol hapus
                const deleteBtn = $(`
                <button type="button" class="btn btn-sm btn-link text-danger p-0 ml-2" title="Hapus file">
                    <i class='bx bx-x' style='font-size: 20px;'></i>
                </button>
            `);

                deleteBtn.on('click', function() {
                    removeFile(index);
                });

                row.append(iconWrapper).append(text).append(deleteBtn);
                preview.append(row);
            });
        }

        // Fungsi untuk menghapus file dari array
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            renderAttachmentPreviews();
        }

        // Fungsi untuk update file input dengan DataTransfer
        function updateFileInput() {
            const input = document.getElementById('emailAttach');
            const dt = new DataTransfer();

            selectedFiles.forEach(file => {
                dt.items.add(file);
            });

            input.files = dt.files;
        }

        // Event handler untuk file input (DENGAN VALIDASI & INLINE WARNING)
        $('#emailAttach').on('change', function(e) {
            const newFiles = Array.from(e.target.files || []);
            let blockedFileNames = [];

            // Validasi setiap file baru
            newFiles.forEach(file => {
                const validation = isFileAllowed(file);

                if (!validation.allowed) {
                    blockedFileNames.push(file.name);
                } else {
                    // Cek duplikat berdasarkan nama dan ukuran
                    const isDuplicate = selectedFiles.some(f =>
                        f.name === file.name && f.size === file.size
                    );

                    if (!isDuplicate) {
                        selectedFiles.push(file);
                    }
                }
            });

            updateFileInput();
            renderAttachmentPreviews();

            // Tampilkan peringatan inline jika ada file yang diblokir
            if (blockedFileNames.length > 0) {
                showFileWarning(blockedFileNames);
            }
        });

        // Function untuk toggle fields berdasarkan message type
        function toggleFieldsBasedOnMessageType() {
            const messageType = $('#messageType').val();
            const $subjectWrapper = $('#emailSubject').closest('.form-label-group');
            const $bodyWrapper = $('.snow-container');
            const $attachmentWrapper = $('#emailAttach').closest('.form-group');
            const $attachmentLabel = $('#emailAttach').next('label');

            // Popup Link Field (buat baru jika belum ada)
            let $popupLinkWrapper = $('#popup-link-wrapper');
            if (!$popupLinkWrapper.length) {
                $popupLinkWrapper = $(`
                <div class="form-group" id="popup-link-wrapper" style="display: none;">
                    <label for="popupLink">Link URL</label>
                    <input type="text" id="popupLink" name="popup_link" class="form-control" placeholder="cafe.vastech.co.id">
                </div>
            `);
                $attachmentWrapper.before($popupLinkWrapper);
            }

            if (messageType === 'popup') {
                // Hide subject dan body untuk popup
                $subjectWrapper.hide();
                $bodyWrapper.hide();

                // Remove required dari subject untuk popup
                $('#emailSubject').removeAttr('required');

                // Show popup link field
                $popupLinkWrapper.show();

                // Update label attachment untuk image only
                $attachmentLabel.text('Attach image file (Required)');

                // Update accept attribute untuk image only
                $('#emailAttach').attr('accept', 'image/*');
                $('#emailAttach').attr('required', true);

                // Clear subject dan body value
                $('#emailSubject').val('');
                quill.setContents([]);

                // Clear existing files yang bukan gambar
                selectedFiles = selectedFiles.filter(file => file.type.startsWith('image/'));
                updateFileInput();
                renderAttachmentPreviews();

            } else {
                // Show subject dan body untuk message
                $subjectWrapper.show();
                $bodyWrapper.show();

                // Add required ke subject untuk message
                $('#emailSubject').attr('required', true);

                // Hide popup link field
                $popupLinkWrapper.hide();
                $('#popupLink').val('');

                // Reset label attachment
                $attachmentLabel.text('Attach file(s)');

                // Reset accept attribute untuk all files
                $('#emailAttach').removeAttr('accept');
                $('#emailAttach').removeAttr('required');
            }
        }

        // Event handler messageType change
        $('#messageType').on('change', function() {
            toggleFieldsBasedOnMessageType();
            handleOptions();
        });

        // Schedule toggle & range dengan validasi waktu
        var $scheduleWrapper = $('#schedule-range-wrapper');
        var $scheduleStart = $('#scheduleStart');
        var $scheduleEnd = $('#scheduleEnd');
        var $scheduleSummary = $('#scheduleSummary');

        function clearSchedule() {
            $scheduleStart.val('');
            $scheduleEnd.val('');
            $scheduleSummary.addClass('d-none').text('');
        }

        function validateScheduleDates() {
            var startVal = $scheduleStart.val();
            var endVal = $scheduleEnd.val();

            if (startVal && endVal) {
                var startDate = new Date(startVal);
                var endDate = new Date(endVal);

                if (endDate <= startDate) {
                    $scheduleEnd.val('');
                    alert('Waktu selesai harus lebih besar dari waktu mulai');
                    return false;
                }
            }

            // Validasi waktu mulai tidak boleh di masa lampau
            if (startVal) {
                var startDate = new Date(startVal);
                var now = new Date();

                if (startDate < now) {
                    $scheduleStart.val('');
                    alert('Waktu mulai tidak boleh di masa lampau');
                    return false;
                }
            }

            return true;
        }

        function updateScheduleSummary() {
            var startVal = $scheduleStart.val();
            var endVal = $scheduleEnd.val();

            if (startVal || endVal) {
                var text = 'Scheduled: ';
                if (startVal) {
                    var startDate = new Date(startVal);
                    text += startDate.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                if (endVal) {
                    var endDate = new Date(endVal);
                    text += ' - ' + endDate.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }

                $scheduleSummary.removeClass('d-none').text(text);
            } else {
                $scheduleSummary.addClass('d-none').text('');
            }
        }

        $('#toggleSchedule').on('click', function() {
            if ($scheduleWrapper.is(':visible')) {
                clearSchedule();
                $scheduleWrapper.hide();
            } else {
                $scheduleWrapper.show();
            }
        });

        $('#clearSchedule').on('click', function() {
            clearSchedule();
            $scheduleWrapper.hide();
        });

        $scheduleStart.on('change', function() {
            if (validateScheduleDates()) {
                updateScheduleSummary();
            }
        });

        $scheduleEnd.on('change', function() {
            if (validateScheduleDates()) {
                updateScheduleSummary();
            }
        });

        // Event handler submit form
        var form = document.getElementById('compose-form');
        form.addEventListener('submit', function(e) {
            const messageType = $('#messageType').val();

            // Validasi khusus untuk popup
            if (messageType === 'popup') {
                // Validasi: popup harus ada attachment gambar
                if (selectedFiles.length === 0) {
                    e.preventDefault();
                    showFileWarning(['Popup notification memerlukan minimal 1 file gambar']);
                    return false;
                }

                // Validasi: semua file harus gambar
                const hasNonImage = selectedFiles.some(file => !file.type.startsWith('image/'));
                if (hasNonImage) {
                    e.preventDefault();
                    showFileWarning(['Popup notification hanya menerima file gambar']);
                    return false;
                }

                // Set subject dengan placeholder jika popup
                if (!$('#emailSubject').val()) {
                    $('#emailSubject').val('Popup Notification - ' + new Date().toISOString());
                }

                // Set body dengan popup link jika ada
                const popupLink = $('#popupLink').val();
                if (popupLink) {
                    document.getElementById('quillBody').value =
                        `<a href="${popupLink}" target="_blank">${popupLink}</a>`;
                } else {
                    document.getElementById('quillBody').value = '<p>Popup Notification</p>';
                }
            } else {
                // Untuk message biasa, validasi hanya subject
                const subjectValue = $('#emailSubject').val().trim();
                
                if (!subjectValue) {
                    e.preventDefault();
                    alert('Subject harus diisi');
                    $('#emailSubject').focus();
                    return false;
                }

                // Set body dari quill (boleh kosong)
                document.getElementById('quillBody').value = quill.root.innerHTML;
            }

            const selected = $('#recipientData').select2('data').map(function(item) {
                return {
                    id: item.id,
                    role: item.role || null,
                    email: item.email || null
                };
            });

            document.getElementById('recipientsMeta').value = JSON.stringify(selected);

            // Validasi schedule sebelum submit
            if ($scheduleStart.val() || $scheduleEnd.val()) {
                if (!validateScheduleDates()) {
                    e.preventDefault();
                    return false;
                }
            }

            // Validasi attachment
            if (selectedFiles.length > 0) {
                let hasInvalidFile = false;
                selectedFiles.forEach(file => {
                    const validation = isFileAllowed(file);
                    if (!validation.allowed) {
                        hasInvalidFile = true;
                    }
                });

                if (hasInvalidFile) {
                    e.preventDefault();
                    showFileWarning(['Terdapat file yang tidak diizinkan']);
                    return false;
                }
            }
        });

        // Call toggle pada load untuk set initial state
        toggleFieldsBasedOnMessageType();
        handleOptions();
    });
</script>
