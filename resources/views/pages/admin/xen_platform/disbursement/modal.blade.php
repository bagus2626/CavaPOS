<div class="modal fade text-left" id="createDisbursementModal" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title white" id="myModalLabel140">Single Disbursement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="createAccountForm" method="POST" action="{{ route('admin.xen_platform.disbursement.create') }}">
                @csrf
                <input type="hidden" id="validate_account_number" name="validate_account_number" value="">
                <input type="hidden" id="validate_account_holder_name" name="validate_account_holder_name" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Partner Account</label>
                        <select class="form-control select2" name="for_user_id" id="for_user_id">
                            <option value="" selected>Select...</option>
                            @foreach($accounts AS $account)
                                <option value="{{ $account['id'] }}"
                                        data-balance="{{ $account['balance']}}"
                                        data-email="{{ $account['email'] ?? '' }}"
                                >{{ $account['public_profile']['business_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Recipient bank name</label>
                        <select class="form-control select2" name="channel_code" id="channel_code">
                            <option value="" selected>Select...</option>
                            @foreach($payoutChannels AS $channel)
                                <option value="{{ $channel['channel_code'] }}"
                                        data-min="{{ $channel['amount_limits']['minimum'] ?? 0 }}"
                                        data-max="{{ $channel['amount_limits']['maximum'] ?? 0 }}"
                                >{{ $channel['channel_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Recipient account number</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="527XXXXXXX"
                                   aria-label="Amount" id="account_number" name="account_number">
                            <div class="input-group-append">
                                <button class="btn btn-warning" type="button" id="validateBtn">Validate</button>
                            </div>
                            <div id="validation-feedback" class="invalid-feedback" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Enter amount to send</label>
                        <input class="form-control" type="number" id="amount" name="amount" placeholder="0" required>
                        <p style="display: none;">
                            <small class="text-muted">
                                <span id="available-balance">0</span> available
                            </small>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Currency</label>
                        <input class="form-control" type="text" id="currency" name="currency" value="IDR" readonly>
                    </div>

                    <div class="form-group">
                        <label>Reference</label>
                        <input class="form-control" type="text" id="reference_id" name="reference_id" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input class="form-control" type="text" id="description" name="description" placeholder="Transaction dscription" required>
                    </div>

                    <div class="form-group">
                        <label>Recipient email</label>
                        <input class="form-control" type="email" id="recipient_email" name="recipient_email" placeholder="Receipt will be sent to the email address" readonly>
                        <p><small class="text-muted">Use comma to add another email</small></p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>

                    <button type="submit" class="btn btn-secondary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Create</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="confirmDisbursementModal" role="dialog" aria-labelledby="myModalLabelConfirm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title white" id="myModalLabelConfirm">Confirm Disbursement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirm-detail">
                    <h5 class="text-dark">Amount to send</h5>
                    <h4 id="confirm-amount" class="font-weight-bold text-danger"></h4>
                    <small class="text-muted">From <span id="confirm-balance"></span> available</small>
                </div>
                <hr>

                <h5 class="text-dark">Send to</h5>
                <p class="mb-1">
                    <span id="confirm-bank-name" class="font-weight-bold"></span>
                </p>
                <p class="mb-1">
                    <span id="confirm-account-number" class="text-dark"></span>
                </p>
                <p class="mb-1 text-uppercase">
                    <span id="confirm-account-holder" class="font-weight-bold"></span>
                </p>

                <hr>

                <p>
                    <strong>Reference:</strong> <span id="confirm-reference"></span>
                </p>
                <p>
                    <strong>Description:</strong> <span id="confirm-description"></span>
                </p>
                <p>
                    <strong>Recipient email:</strong> <span id="confirm-email"></span>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <span class="d-none d-sm-block">Back</span>
                </button>

                <button type="button" class="btn btn-secondary ml-1" id="submitDisbursementBtn">
                    <span class="d-none d-sm-block">Submit</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            // $("#channel_code").select2({ dropdownParent: $('#createDisbursementModal') });
            // $("#for_user_id").select2({ dropdownParent: $('#createDisbursementModal') });

            const $modal = $('#createDisbursementModal');

            const $bankCodeSelect = $modal.find('#channel_code');
            const $accountSelect = $modal.find('#for_user_id');
            const $accountNumberInput = $modal.find('#account_number');
            const $validateBtn = $modal.find('#validateBtn');
            const $feedbackDiv = $modal.find('#validation-feedback');
            const $amountInput = $modal.find('#amount');
            const $emailInput = $modal.find('#recipient_email');
            const $referenceInput = $modal.find('#reference_id');
            const $descriptionInput = $modal.find('#description');

            const $level3Elements = $modal.find('.modal-body input, .modal-body select').filter(function() {
                const id = this.id;
                const name = this.name;
                return id !== 'channel_code' && id !== 'for_user_id' && id !== 'account_number' && id !== 'amount' && id !== 'reference_id' && id !== 'recipient_email';
            });

            const $validatedNumber = $modal.find('#validate_account_number');
            const $validatedName = $modal.find('#validate_account_holder_name');
            const $createBtn = $modal.find('button[type="submit"]');
            const $balanceTextContainer = $modal.find('.form-group p').first();
            const $balanceSpan = $modal.find('#available-balance');

            const $confirmModal = $('#confirmDisbursementModal');
            const $submitDisbursementBtn = $confirmModal.find('#submitDisbursementBtn');
            const $form = $('#createAccountForm');

            function generateUniqueReferenceId() {
                const timestamp = new Date().getTime();
                const randomPart = Math.random().toString(36).substring(2, 6).toUpperCase();
                return `REF-${timestamp}-${randomPart}`;
            }

            function formatRupiah(number) {
                const num = parseInt(String(number).replace(/\D/g, ''), 10);
                if (isNaN(num)) return 'Rp. 0';
                return 'Rp. ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function validateAmount() {
                const amount = parseFloat($amountInput.val()) || 0;
                const selectedAccountOption = $accountSelect.find('option:selected');
                const availableBalance = parseFloat(selectedAccountOption.data('balance')) || 0;
                const selectedBankOption = $bankCodeSelect.find('option:selected');
                const minLimit = parseFloat(selectedBankOption.data('min')) || 0;
                const maxLimit = parseFloat(selectedBankOption.data('max')) || 0;

                let message = '';

                if (amount === 0) {
                    message = '';
                } else if (amount > availableBalance) {
                    message = `Jumlah melewati batas saldo. Saldo tersedia: ${formatRupiah(availableBalance)}.`;
                } else if (amount < minLimit) {
                    message = `Jumlah minimum untuk bank ini adalah ${formatRupiah(minLimit)}.`;
                } else if (amount > maxLimit) {
                    message = `Jumlah maksimum untuk bank ini adalah ${formatRupiah(maxLimit)}.`;
                }

                if (message) {
                    $amountInput.addClass('is-invalid').removeClass('is-valid');
                    let $amountFeedback = $amountInput.siblings('.amount-feedback');
                    if ($amountFeedback.length === 0) {
                        $amountFeedback = $('<div class="invalid-feedback amount-feedback"></div>').insertAfter($amountInput);
                    }
                    $amountFeedback.html(`❌ ${message}`).show();
                    $createBtn.prop('disabled', true);
                    return false;
                } else {
                    $amountInput.removeClass('is-invalid');
                    $amountInput.siblings('.amount-feedback').hide();

                    const isLevel3Valid = $level3Elements.filter('[required]').get().every(el => $(el).val()) && $referenceInput.val() && $descriptionInput.val();
                    if (isLevel3Valid) {
                        $createBtn.prop('disabled', false);
                    }
                    return true;
                }
            }

            function controlFormState(level) {
                const partnerAccountSelected = !!$accountSelect.val();
                const bankNameSelected = !!$bankCodeSelect.val();
                const isLevel1Complete = partnerAccountSelected && bankNameSelected;

                const enableLevel2 = level >= 2 && isLevel1Complete;
                $accountNumberInput.prop('disabled', !enableLevel2);
                $validateBtn.prop('disabled', !enableLevel2);

                const enableLevel3 = level >= 3 && isLevel1Complete;
                $amountInput.prop('disabled', !enableLevel3);
                $level3Elements.prop('disabled', !enableLevel3);
                $referenceInput.prop('disabled', !enableLevel3);
                $descriptionInput.prop('disabled', !enableLevel3);

                $emailInput.prop('readonly', true);

                if (partnerAccountSelected) {
                    const $selectedOption = $accountSelect.find('option:selected');
                    const balance = $selectedOption.data('balance') || 0;
                    const email = $selectedOption.data('email') || '';

                    $balanceSpan.text(formatRupiah(balance));
                    $balanceTextContainer.show();
                    $emailInput.val(email);
                } else {
                    $balanceTextContainer.hide();
                    $balanceSpan.text('0');
                    $emailInput.val('');
                }

                if (level === 0 && !$referenceInput.val()) {
                    $referenceInput.val(generateUniqueReferenceId());
                }

                $createBtn.prop('disabled', level < 3 || !validateAmount());

                if (level < 3) {
                    $validatedNumber.val('');
                    $validatedName.val('');
                    $accountNumberInput.removeClass('is-valid is-invalid');
                    $feedbackDiv.hide().removeClass('valid-feedback invalid-feedback');
                    $amountInput.removeClass('is-invalid is-valid');
                    $amountInput.siblings('.amount-feedback').hide();
                }
                if (level < 2) {
                    $bankCodeSelect.prop('disabled', !partnerAccountSelected);
                }
            }

            $bankCodeSelect.prop('disabled', true);
            $accountSelect.prop('disabled', false);
            controlFormState(0);
            $amountInput.after('<div class="invalid-feedback amount-feedback" style="display:none;"></div>');
            $referenceInput.val(generateUniqueReferenceId());

            function checkLevel1AndProceed() {
                const partnerAccountSelected = !!$accountSelect.val();
                const bankNameSelected = !!$bankCodeSelect.val();

                $bankCodeSelect.prop('disabled', !partnerAccountSelected);

                if (partnerAccountSelected && bankNameSelected) {
                    controlFormState(2);
                } else {
                    controlFormState(1);
                }
            }
            $accountSelect.on('change', checkLevel1AndProceed);
            $bankCodeSelect.on('change', checkLevel1AndProceed);

            $referenceInput.on('input', function() {
                if ($validatedName.val() && $validatedNumber.val()) {
                    validateAmount();
                }
            });
            $descriptionInput.on('input', function() {
                if ($validatedName.val() && $validatedNumber.val()) {
                    validateAmount();
                }
            });
            $amountInput.on('input', function() {
                if ($validatedName.val() && $validatedNumber.val()) {
                    validateAmount();
                }
            });

            $validateBtn.on('click', function() {
                const bankCode = $bankCodeSelect.val();
                const accountNumber = $accountNumberInput.val().trim();
                const $btn = $(this);

                if (!bankCode || !$accountSelect.val()) {
                    console.warn("Mohon pilih Partner Account dan Bank Penerima.");
                    controlFormState(1);
                    return;
                }
                if (!accountNumber) {
                    console.warn("Mohon masukkan Nomor Rekening.");
                    return;
                }

                $accountNumberInput.removeClass('is-valid is-invalid');
                $feedbackDiv.hide();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

                $.ajax({
                    url: '{{ route("admin.xen_platform.disbursement.validate-bank") }}',
                    method: 'GET',
                    data: {
                        bank_code: bankCode,
                        account_number: accountNumber,
                        reference_id: 'validation-ref-' + Date.now()
                    },
                    success: function(response) {
                        const xenditStatus = response.status;
                        const isFound = response.result && response.result.is_found;
                        const accountHolderName = response.result.account_holder_name ?? "Nama Tidak Ditemukan";
                        const bankName = $bankCodeSelect.find('option:selected').text();

                        if (xenditStatus === 'COMPLETED' && isFound) {
                            $accountNumberInput.addClass('is-valid');
                            $feedbackDiv.html(`Ditemukan: ${accountHolderName} (${bankName})`).removeClass('invalid-feedback').addClass('valid-feedback').show();
                            $validatedNumber.val(accountNumber);
                            $validatedName.val(accountHolderName);

                            controlFormState(3);
                        } else {
                            $accountNumberInput.addClass('is-invalid');
                            $feedbackDiv.html('❌ Rekening tidak valid atau tidak ditemukan.').removeClass('valid-feedback').addClass('invalid-feedback').show();
                            controlFormState(2);
                        }
                    },
                    error: function(xhr) {
                        $accountNumberInput.addClass('is-invalid');
                        $feedbackDiv.html('⚠️ Terjadi error server. Coba lagi.').removeClass('valid-feedback').addClass('invalid-feedback').show();
                        controlFormState(2);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Validate');
                    }
                });
            });

            $accountNumberInput.on('input', function() {
                if ($validatedNumber.val()) {
                    controlFormState(2);
                }
            });


            $form.on('submit', function(e) {
                e.preventDefault();

                const isAmountValid = validateAmount();
                const isAccountValidated = $validatedName.val() && $validatedNumber.val() === $accountNumberInput.val().trim();
                const isReferenceFilled = $referenceInput.val();

                if (!isAccountValidated || !isAmountValid || !isReferenceFilled || !$descriptionInput.val()) {
                    console.warn('Terdapat data yang belum lengkap atau belum divalidasi. Mohon periksa kembali.');
                    controlFormState(3);
                    return;
                }

                const amount = $amountInput.val();
                const availableBalanceText = $balanceSpan.text();
                const bankName = $bankCodeSelect.find('option:selected').text();
                const accountNumber = $accountNumberInput.val();
                const accountHolder = $validatedName.val();
                const reference = $referenceInput.val();
                const description = $descriptionInput.val();
                const recipientEmail = $emailInput.val();

                $confirmModal.find('#confirm-amount').text(formatRupiah(amount));
                $confirmModal.find('#confirm-balance').text(availableBalanceText.replace('Rp. ', ''));

                $confirmModal.find('#confirm-bank-name').text(bankName);
                $confirmModal.find('#confirm-account-number').text(accountNumber);
                $confirmModal.find('#confirm-account-holder').text(accountHolder);

                $confirmModal.find('#confirm-reference').text(reference);
                $confirmModal.find('#confirm-description').text(description);
                $confirmModal.find('#confirm-email').text(recipientEmail);

                $modal.modal('hide');
                $confirmModal.modal('show');
            });


            $submitDisbursementBtn.off('click').on('click', function() {
                const $btn = $(this);

                isSubmitting = true;

                const payload = {
                    _token: $form.find('input[name="_token"]').val(),
                    reference_id: $referenceInput.val(),
                    channel_code: $bankCodeSelect.val(),
                    channel_properties: {
                        account_number: $validatedNumber.val(),
                        account_holder_name: $validatedName.val()
                    },
                    amount: parseFloat($amountInput.val()),
                    description: $descriptionInput.val(),
                    currency: 'IDR',
                    for_user_id: $accountSelect.val(),
                    recipient_email: $emailInput.val()
                };

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: payload,
                    dataType: 'json',

                    success: function(response) {
                        $confirmModal.modal('hide');
                        $modal.modal('hide');

                        setTimeout(() => {
                            showTemporaryNotification('Disbursement berhasil dibuat! Reference: ' + response.reference_id, 'Disbursement Berhasil', 'success');

                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }, 200);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal memproses disbursement. Silakan cek koneksi atau hubungi admin.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }

                        showTemporaryNotification('Error: ' + errorMessage, 'Terjadi Kesalahan','error');

                        $btn.prop('disabled', false).text('Submit');
                        $confirmModal.modal('show');

                        isSubmitting = false;
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Submit');
                    }
                });
            });

            $confirmModal.on('hidden.bs.modal', function () {
                if(!isSubmitting) {
                    $submitDisbursementBtn.prop('disabled', false).text('Submit');
                    $modal.modal('show');
                }
            });

            function showTemporaryNotification(message, titleMessage, type) {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "showDuration": 500,
                    "hideDuration": 3000,
                    "timeOut": 2000,
                };

                if (type === 'success') {
                    toastr.success(message, titleMessage, type);
                } else {
                    toastr.error(message, titleMessage, type);
                }
            }
        });
    </script>
@endpush


