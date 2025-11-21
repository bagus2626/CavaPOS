<!-- Create Disbursement Modal -->
<div class="modal fade" id="createDisbursementModal" role="dialog" aria-labelledby="createDisbursementModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-choco">
                <h5 class="modal-title text-white" id="createDisbursementModalLabel">
                    <i class="fas fa-money-bill-transfer mr-2"></i>
                    {{ __('messages.owner.xen_platform.payouts.single_withdrawal') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createAccountForm" method="POST"
                  action="{{ route('owner.user-owner.xen_platform.payout.create') }}">
                @csrf
                <input type="hidden" id="validate_account_number" name="validate_account_number" value="">
                <input type="hidden" id="validate_account_holder_name" name="validate_account_holder_name" value="">
                <input type="hidden" name="for_user_id" id="for_user_id" value="{{ $accounts['id'] }}"
                       data-balance="{{ $accounts['balance']}}" data-email="{{ $accounts['email'] ?? '' }}">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="channel_code" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.recipient_bank') }}</label>
                        <select class="form-control select2" name="channel_code" id="channel_code" style="width: 100%;">
                            <option value="" selected>{{ __('messages.owner.xen_platform.payouts.select_bank') }}</option>
                            @foreach($payoutChannels AS $channel)
                                <option value="{{ $channel['channel_code'] }}"
                                        data-min="{{ $channel['amount_limits']['minimum'] ?? 0 }}"
                                        data-max="{{ $channel['amount_limits']['maximum'] ?? 0 }}"
                                >{{ $channel['channel_name'] }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted" id="bank-limits"></small>
                    </div>

                    <div class="form-group">
                        <label for="account_number" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.recipient_account_number') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="527XXXXXXX"
                                   aria-label="Account Number" id="account_number" name="account_number">
                            <div class="input-group-append">
                                <button class="btn btn-warning" type="button" id="validateBtn">
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('messages.owner.xen_platform.payouts.validate') }}
                                </button>
                            </div>
                        </div>
                        <div id="validation-feedback" class="invalid-feedback" style="display: none;"></div>
                        <div id="validation-success" class="valid-feedback" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.amount_to_send') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">IDR</span>
                            </div>
                            <input class="form-control" type="number" id="amount" name="amount" placeholder="0"
                                   required>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('messages.owner.xen_platform.payouts.available_balance') }}: <span id="available-balance" class="font-weight-bold">0</span>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="reference_id" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.reference_id') }}</label>
                        <input class="form-control" type="text" id="reference_id" name="reference_id"
                               placeholder="Unique reference ID" required>
                    </div>


                    <div class="form-group">
                        <label for="description" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.description') }}</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="2" placeholder="Transaction description" required>{{ __('messages.owner.xen_platform.payouts.withdrawal') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="recipient_email" class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.recipient_email') }}</label>
                        <input class="form-control" type="text" id="recipient_email" name="recipient_email"
                               placeholder="email1@example.com, email2@example.com">
                        <small class="form-text text-muted">
                            {{ __('messages.owner.xen_platform.payouts.email_instruction') }}
                            <span id="email-validation-status" class="ml-2"></span>
                        </small>
                        <div id="email-validation-feedback" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <!-- Validation Summary -->
                    <div class="alert alert-info" id="validation-summary" style="display: none;">
                        <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i> {{ __('messages.owner.xen_platform.payouts.validation_summary') }}</h6>
                        <div id="validation-details" class="small"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> {{ __('messages.owner.xen_platform.payouts.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-eye mr-1"></i> {{ __('messages.owner.xen_platform.payouts.preview') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Disbursement Modal -->
<div class="modal fade" id="confirmDisbursementModal" role="dialog" aria-labelledby="confirmDisbursementModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-choco">
                <h5 class="modal-title text-white" id="confirmDisbursementModalLabel">
                    <i class="fas fa-shield-alt mr-2"></i>
                    {{ __('messages.owner.xen_platform.payouts.confirm_withdrawal') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirm-detail">
                    <h5 class="text-dark">{{ __('messages.owner.xen_platform.payouts.amount_to_send') }}</h5>
                    <h4 id="confirm-amount" class="font-weight-bold text-danger"></h4>
                    <small class="text-muted">{{ __('messages.owner.xen_platform.payouts.from') }} <span id="confirm-balance"></span> {{ __('messages.owner.xen_platform.payouts.available') }}</small>
                </div>
                <hr>

                <h5 class="text-dark">{{ __('messages.owner.xen_platform.payouts.send_to') }}</h5>
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
                    <strong>{{ __('messages.owner.xen_platform.payouts.reference') }}:</strong> <span id="confirm-reference"></span>
                </p>
                <p>
                    <strong>{{ __('messages.owner.xen_platform.payouts.description') }}:</strong> <span id="confirm-description"></span>
                </p>
                <p>
                    <strong>{{ __('messages.owner.xen_platform.payouts.recipient_email') }}:</strong> <span id="confirm-email"></span>
                </p>
                <!-- Warning Message -->
                <div class="alert alert-danger py-2">
                    <small>
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ __('messages.owner.xen_platform.payouts.preview_instruction') }}
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.owner.xen_platform.payouts.back') }}
                </button>
                <button type="button" class="btn btn-success" id="submitDisbursementBtn">
                    <i class="fas fa-paper-plane mr-1"></i> {{ __('messages.owner.xen_platform.payouts.confirm_and_submit') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            let isSubmitting = false;

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
            const $previewBtn = $modal.find('#previewBtn');

            const $level3Elements = $modal.find('.modal-body input, .modal-body select').filter(function () {
                const id = this.id;
                const name = this.name;
                return id !== 'channel_code' && id !== 'for_user_id' && id !== 'account_number' && id !== 'amount' && id !== 'reference_id' && id !== 'recipient_email';
            });

            const $validatedNumber = $modal.find('#validate_account_number');
            const $validatedName = $modal.find('#validate_account_holder_name');
            const $balanceSpan = $modal.find('#available-balance');

            const $confirmModal = $('#confirmDisbursementModal');
            const $submitDisbursementBtn = $confirmModal.find('#submitDisbursementBtn');
            const $form = $('#createAccountForm');

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email.trim());
            }

            function validateMultipleEmails(emailString) {
                if (!emailString.trim()) {
                    return { isValid: true, emails: [], invalidEmails: [] };
                }

                const emails = emailString.split(',').map(email => email.trim()).filter(email => email !== '');
                const invalidEmails = emails.filter(email => !isValidEmail(email));

                return {
                    isValid: invalidEmails.length === 0,
                    emails: emails,
                    invalidEmails: invalidEmails
                };
            }

            function updateEmailValidationUI() {
                const emailValue = $emailInput.val().trim();
                const validation = validateMultipleEmails(emailValue);
                const $emailStatus = $('#email-validation-status');
                const $emailFeedback = $('#email-validation-feedback');

                $emailInput.removeClass('is-invalid is-valid');
                $emailFeedback.hide();

                if (!emailValue) {
                    $emailStatus.html('');
                    return true;
                }

                if (validation.isValid) {
                    $emailInput.addClass('is-valid');
                    const emailCount = validation.emails.length;
                    $emailStatus.html(`<span class="text-success"><i class="fas fa-check-circle"></i> ${emailCount} valid email(s)</span>`);
                    return true;
                } else {
                    $emailInput.addClass('is-invalid');
                    $emailFeedback.html(`Invalid email addresses: ${validation.invalidEmails.join(', ')}`).show();
                    $emailStatus.html(`<span class="text-danger"><i class="fas fa-exclamation-circle"></i> ${validation.invalidEmails.length} invalid email(s)</span>`);
                    return false;
                }
            }

            function generateUniqueReferenceId() {
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
                const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '');
                const randomStr = Math.random().toString(36).substring(2, 8).toUpperCase();

                return `DSP-${dateStr}-${timeStr}-${randomStr}`;
            }

            function formatRupiah(number) {
                const num = parseInt(String(number).replace(/\D/g, ''), 10);
                if (isNaN(num)) return 'Rp. 0';
                return 'Rp. ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function validateAmount() {
                const amount = parseFloat($amountInput.val()) || 0;
                const availableBalance = parseFloat($accountSelect.data('balance')) || 0;
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
                    return false;
                } else {
                    $amountInput.removeClass('is-invalid');
                    $amountInput.siblings('.amount-feedback').hide();
                    return true;
                }
            }

            function controlFormState(level) {
                const bankNameSelected = !!$bankCodeSelect.val();
                const isLevel1Complete = bankNameSelected;

                const enableLevel2 = level >= 2 && isLevel1Complete;
                $accountNumberInput.prop('disabled', !enableLevel2);
                $validateBtn.prop('disabled', !enableLevel2);

                const enableLevel3 = level >= 3 && isLevel1Complete;
                $amountInput.prop('disabled', !enableLevel3);
                $level3Elements.prop('disabled', !enableLevel3);
                $referenceInput.prop('disabled', !enableLevel3);
                $descriptionInput.prop('disabled', !enableLevel3);
                $emailInput.prop('disabled', !enableLevel3);

                const balance = $accountSelect.data('balance') || 0;
                const defaultEmail = $accountSelect.data('email') || '';

                $balanceSpan.text(formatRupiah(balance));

                if (!$emailInput.val().trim() && defaultEmail) {
                    $emailInput.val(defaultEmail);
                    updateEmailValidationUI();
                }

                if (level === 0 && !$referenceInput.val()) {
                    $referenceInput.val(generateUniqueReferenceId());
                }

                updatePreviewButtonState();
            }

            function updatePreviewButtonState() {
                const isAmountValid = validateAmount();
                const isAccountValidated = $validatedName.val() && $validatedNumber.val() === $accountNumberInput.val().trim();
                const isReferenceFilled = $referenceInput.val();
                const isDescriptionFilled = $descriptionInput.val();
                const isEmailValid = updateEmailValidationUI();

                const isFormValid = isAmountValid &&
                    isAccountValidated &&
                    isReferenceFilled &&
                    isDescriptionFilled &&
                    isEmailValid;

                $previewBtn.prop('disabled', !isFormValid);
            }

            $bankCodeSelect.prop('disabled', false);
            controlFormState(0);
            $amountInput.after('<div class="invalid-feedback amount-feedback" style="display:none;"></div>');
            $referenceInput.val(generateUniqueReferenceId());
            $referenceInput.prop('readonly', true);

            function checkLevel1AndProceed() {
                const bankNameSelected = !!$bankCodeSelect.val();

                if (bankNameSelected) {
                    controlFormState(2);
                } else {
                    controlFormState(1);
                }
            }

            $bankCodeSelect.on('change', checkLevel1AndProceed);

            $referenceInput.on('input', updatePreviewButtonState);
            $descriptionInput.on('input', updatePreviewButtonState);
            $amountInput.on('input', updatePreviewButtonState);
            $emailInput.on('input', updatePreviewButtonState);

            $validateBtn.on('click', function () {
                const bankCode = $bankCodeSelect.val();
                const accountNumber = $accountNumberInput.val().trim();
                const $btn = $(this);

                if (!bankCode) {
                    console.warn("Mohon pilih Bank Penerima.");
                    showTemporaryNotification('Mohon pilih Bank Penerima.', 'Gagal', 'error');
                    controlFormState(1);
                    return;
                }
                if (!accountNumber) {
                    console.warn("Mohon masukkan Nomor Rekening.");
                    showTemporaryNotification('Mohon masukkan Nomor Rekening.', 'Gagal', 'error');
                    return;
                }

                $accountNumberInput.removeClass('is-valid is-invalid');
                $feedbackDiv.hide();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

                $.ajax({
                    url: '{{ route("owner.user-owner.xen_platform.payout.validate-bank") }}',
                    method: 'GET',
                    data: {
                        bank_code: bankCode,
                        account_number: accountNumber,
                        reference_id: 'validation-ref-' + Date.now()
                    },
                    success: function (response) {
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
                    error: function (xhr) {
                        $accountNumberInput.addClass('is-invalid');
                        $feedbackDiv.html('⚠️ Terjadi error server. Coba lagi.').removeClass('valid-feedback').addClass('invalid-feedback').show();
                        controlFormState(2);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Validate');
                        updatePreviewButtonState();
                    }
                });
            });

            $accountNumberInput.on('input', function () {
                if ($validatedNumber.val()) {
                    controlFormState(2);
                }
                updatePreviewButtonState();
            });

            $form.on('submit', function (e) {
                e.preventDefault();

                const isAmountValid = validateAmount();
                const isAccountValidated = $validatedName.val() && $validatedNumber.val() === $accountNumberInput.val().trim();
                const isReferenceFilled = $referenceInput.val();
                const isDescriptionFilled = $descriptionInput.val();
                const isEmailValid = updateEmailValidationUI();

                if (!isAccountValidated || !isAmountValid || !isReferenceFilled || !isDescriptionFilled || !isEmailValid) {
                    console.warn('Terdapat data yang belum lengkap atau belum divalidasi. Mohon periksa kembali.');
                    showTemporaryNotification('Terdapat data yang belum lengkap atau belum divalidasi. Mohon periksa kembali.', 'Gagal', 'error');
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

            $submitDisbursementBtn.off('click').on('click', function () {
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

                    success: function (response) {
                        $confirmModal.modal('hide');
                        $modal.modal('hide');

                        setTimeout(() => {
                            if (response.success) {
                                showTemporaryNotification(response.message, 'Disbursement Berhasil', 'success');
                            } else {
                                showTemporaryNotification('Error: ' + response.message, 'Terjadi Kesalahan', 'error');
                            }

                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }, 200);
                    },
                    error: function (xhr) {
                        let errorMessage = 'Gagal memproses disbursement. Silakan cek koneksi atau hubungi admin.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.success === false) {
                                errorMessage = xhr.responseJSON.message;

                                if (xhr.responseJSON.errors && xhr.responseJSON.errors.recipient_email) {
                                    errorMessage = xhr.responseJSON.errors.recipient_email[0];
                                }
                            }
                        }

                        showTemporaryNotification('Error: ' + errorMessage, 'Terjadi Kesalahan', 'error');

                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Confirm & Submit');
                        $confirmModal.modal('show');

                        isSubmitting = false;
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Confirm & Submit');
                    }
                });
            });

            $confirmModal.on('hidden.bs.modal', function () {
                if (!isSubmitting) {
                    $submitDisbursementBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Confirm & Submit');
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

            // Initialize email validation on page load
            updateEmailValidationUI();
        });
    </script>
@endpush
