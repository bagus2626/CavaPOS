<div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-0 border-0">

            <div class="compose-new-mail-sidebar w-100">
                <div class="card shadow-none quill-wrapper p-0">

                    {{-- Header --}}
                    <div class="card-header">
                        <h3 class="card-title" id="emailCompose">New Message</h3>
                        <button type="button" class="close close-icon" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>

                    {{-- Form start --}}
                    <form
                        action="{{ route('admin.message-notification.messages.store') }}"
                        method="POST"
                        id="compose-form"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="card-content">
                            <div class="card-body pt-0">

                                {{-- From (readonly) --}}
                                <div class="form-group row">
                                    <label for="emailfrom" class="col-sm-2 col-form-label text-right">From</label>
                                    <input type="text"
                                           id="emailfrom"
                                           class="form-control col-sm-10"
                                           value="{{ auth()->user()->name ?? 'admin@example.com' }}"
                                           disabled>
                                </div>
                                {{-- message type --}}
                                <div class="form-group row">
                                    <label for="messageType" class="col-sm-2 col-form-label text-right">Message Type</label>
                                    <div class="col-sm-10">
                                        <select name="message_type" id="messageType" class="form-control" onchange="handleOptions()" required>
                                            <option value="">--Select Message Type--</option>
                                            <option value="message">Message</option>
                                            <option value="popup">Popup Notification</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="target" style="display: none;">
                                    <label for="targetType" class="col-sm-2 col-form-label text-right">Target</label>
                                    <div class="col-sm-10">
                                        <select name="target" id="targetType" class="form-control" onchange="handleOptions()">
                                            <option value="">--Select Recipient--</option>
                                            <option value="single">To a Single/Multiple Person</option>
                                            <option value="broadcast">Broadcast</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="target-group" style="display: none;">
                                    <label for="targetGroup" class="col-sm-2 col-form-label text-right">Target Group</label>
                                    <div class="col-sm-10">
                                        <select name="target_group" id="targetGroup" class="form-control">
                                            <option value="">--Select Target--</option>
                                            <option value="all">All</option>
                                            <option value="business-partner">Business Partner (owner, outlet, employees)</option>
                                            <option value="owner">Owner</option>
                                            <option value="outlet">Outlet</option>
                                            <option value="employee">Employee</option>
                                            <option value="end-customer">End Customer</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="recipient-email" style="display: none;">
                                    <label for="recipientData" class="col-sm-2 col-form-label text-right">Recipient Account</label>
                                    <div class="col-sm-10">
                                        <select name="recipients_data[]" id="recipientData" class="form-control" multiple="multiple"></select>
                                    </div>
                                </div>
                                <input type="hidden" name="recipients_meta" id="recipientsMeta">

                                {{-- Schedule range (optional) --}}
                                <div class="form-group row" id="schedule-range-wrapper" style="display:none;">
                                    <label class="col-sm-2 col-form-label text-right">Schedule</label>
                                    <div class="col-sm-10">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-1">
                                                <input type="datetime-local"
                                                    name="schedule_start"
                                                    id="scheduleStart"
                                                    class="form-control"
                                                    placeholder="Start datetime">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <input type="datetime-local"
                                                    name="schedule_end"
                                                    id="scheduleEnd"
                                                    class="form-control"
                                                    placeholder="End datetime">
                                            </div>
                                        </div>

                                        <button type="button"
                                                class="btn btn-sm btn-link text-danger p-0 mt-1"
                                                id="clearSchedule">
                                            Hapus jadwal
                                        </button>
                                    </div>
                                </div>

                                {{-- (Opsional) Subject --}}
                                <div class="form-label-group">
                                    <label for="emailSubject">Subject</label>
                                    <input type="text" id="emailSubject" name="subject" class="form-control" placeholder="Subject">
                                </div>
                                
                                <div class="snow-container border rounded p-50">
                                    {{-- Toolbar di atas, full width --}}
                                    <div class="compose-quill-toolbar pb-0 mb-25" id="compose-toolbar">
                                        <span class="ql-formats mr-0">
                                            <button type="button" class="ql-bold"></button>
                                            <button type="button" class="ql-italic"></button>
                                            <button type="button" class="ql-underline"></button>
                                            <button type="button" class="ql-link"></button>
                                            <button type="button" class="ql-image"></button>
                                        </span>
                                    </div>

                                    {{-- Editor area --}}
                                    <div class="compose-editor"></div>

                                    <input type="hidden" name="body" id="quillBody">
                                </div>
                                
                                {{-- Attachment --}}
                                <div class="form-group mt-2">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input"
                                            id="emailAttach"
                                            name="attachments[]"
                                            multiple>
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
                                <button type="button"
                                        id="toggleSchedule"
                                        class="btn btn-sm btn-outline-secondary">
                                    <i class='bx bx-time-five mr-25'></i>
                                    Scheduled
                                </button>
                                <span id="scheduleSummary" class="text-muted small ml-2 d-none"></span>
                            </div>

                            <div>
                                <button type="button"
                                        class="btn btn-light-secondary cancel-btn mr-1"
                                        data-dismiss="modal">
                                    <i class='bx bx-x mr-25'></i>
                                    <span class="d-sm-inline d-none">Cancel</span>
                                </button>
                                <button type="submit" class="btn-send btn btn-primary">
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

<script>
    document.addEventListener("DOMContentLoaded", function () {

    // Inisialisasi Quill
    var quill = new Quill('.compose-editor', {
        theme: 'snow',
        modules: {
            toolbar: '#compose-toolbar'
        }
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
            data: function (params) {
                return {
                    q: params.term || '',
                    target_group: $('#targetGroup').val()
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        const label = item.name
                            ? item.name + ' - ' + item.email + (item.role ? ' (' + item.role + ')' : '')
                            : item.email;

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
    $('#targetGroup').on('change', function () {
        $('#recipientData').val(null).trigger('change');
    });

    // 🔹 Preview attachment kecil-kecil
    $('#emailAttach').on('change', function () {
        const files   = Array.from(this.files || []);
        const preview = $('#attachment-preview');
        preview.empty();

        // Update label custom-file
        const labelText = files.length
            ? files.map(f => f.name).join(', ')
            : 'Attach file(s)';
        $(this).next('.custom-file-label').text(labelText);

        if (!files.length) return;

        files.forEach(file => {
            const row = $('<div class="d-flex align-items-center mb-1"></div>');

            // Kolom ikon / thumbnail
            const iconWrapper = $('<div class="mr-1"></div>');

            if (file.type && file.type.startsWith('image/')) {
                const url = URL.createObjectURL(file);
                const img = $('<img>')
                    .attr('src', url)
                    .css({
                        width:  '40px',
                        height: '40px',
                        'object-fit': 'cover',
                        'border-radius': '4px'
                    });

                // optional, supaya tidak leak memory
                img.on('load', function () {
                    URL.revokeObjectURL(url);
                });

                iconWrapper.append(img);
            } else {
                // generic icon untuk semua tipe non-image
                iconWrapper.append(
                    "<i class='bx bxs-file mr-25' style='font-size: 24px;'></i>"
                );
            }

            // Kolom teks (nama + ukuran)
            const sizeKB = Math.round(file.size / 1024);
            const text = $(`
                <div class="small">
                    <div>${file.name}</div>
                    <div class="text-muted">${file.type || 'Unknown type'} • ${sizeKB} KB</div>
                </div>
            `);

            row.append(iconWrapper).append(text);
            preview.append(row);
        });
    });

    // Sebelum submit, copy konten Quill ke input hidden
    var form = document.getElementById('compose-form');
    form.addEventListener('submit', function () {
        document.getElementById('quillBody').value = quill.root.innerHTML;

        const selected = $('#recipientData').select2('data').map(function (item) {
            return {
                id: item.id,
                role: item.role || null,
                email: item.email || null
            };
        });

        document.getElementById('recipientsMeta').value = JSON.stringify(selected);
    });

    handleOptions();


    // === Scheduled toggle & range ===
    var $scheduleWrapper = $('#schedule-range-wrapper');
    var $scheduleStart   = $('#scheduleStart');
    var $scheduleEnd     = $('#scheduleEnd');
    var $scheduleSummary = $('#scheduleSummary');

    function clearSchedule() {
        $scheduleStart.val('');
        $scheduleEnd.val('');
        $scheduleSummary.addClass('d-none').text('');
    }

    // Klik tombol "Scheduled" untuk show/hide range
    $('#toggleSchedule').on('click', function () {
        if ($scheduleWrapper.is(':visible')) {
            // Kalau sedang tampil, hide + clear (hapus jadwal)
            clearSchedule();
            $scheduleWrapper.hide();
        } else {
            // Tampilkan input range tanggal
            $scheduleWrapper.show();
        }
    });

    // Tombol "Hapus jadwal" di dalam form-group
    $('#clearSchedule').on('click', function () {
        clearSchedule();
        $scheduleWrapper.hide();
    });

    // Update summary kecil di footer saat tanggal berubah
    $scheduleStart.add($scheduleEnd).on('change', function () {
        var startVal = $scheduleStart.val();
        var endVal   = $scheduleEnd.val();

        if (startVal || endVal) {
            var text = 'Scheduled: ';
            if (startVal) text += startVal;
            if (endVal)   text += ' - ' + endVal;

            $scheduleSummary.removeClass('d-none').text(text);
        } else {
            $scheduleSummary.addClass('d-none').text('');
        }
    });

});



</script>