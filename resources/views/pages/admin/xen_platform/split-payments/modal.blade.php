<div class="modal fade text-left" id="createAccountModal" role="dialog" aria-labelledby="myModalLabel140"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title white" id="myModalLabel140">Create Split Rules</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="createAccountForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Split Rule Name : </label>
                        <input class="form-control" type="text" id="split_rule_name" name="split_rule_name"
                               placeholder="Enter split rule name" required>
                    </div>

                    <div class="form-group">
                        <label>Source Account</label>
                        <select class="form-control select2" name="partner_account_id" id="partner_account_id" required>
                            <option value="" selected>Select Partner Account</option>
                            @foreach($accounts AS $account)
                                <option value="{{ $account['xendit_user_id'] }}">{{ $account['business_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="master_account_form">
                        <label>Destination Account : </label>
                        <input class="form-control" type="text" value="PT VASTU CIPTA PERSADA" readonly required>
                    </div>

                    <div class="form-group">
                        <label>Currency : </label>
                        <input class="form-control" type="text" value="IDR" readonly required>
                    </div>

                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="form-group mb-1">
                                <label>Split Type</label>
                                <div class="d-flex justify-content-start">
                                    <div class="radio">
                                        <input type="radio" name="split_type_option" value="FLAT" id="flat_amount_radio"
                                               checked onchange="chooseSplit()">
                                        <label for="flat_amount_radio">Flat</label>
                                    </div>
                                    <div class="radio ml-2">
                                        <input type="radio" name="split_type_option" value="PERCENT"
                                               id="percent_amount_radio" onchange="chooseSplit()">
                                        <label for="percent_amount_radio">Percent</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="flat_amount_form">
                        <label>Amount : </label>
                        <input class="form-control" type="number" id="flat_amount" name="flat_amount"
                               placeholder="Enter amount">
                    </div>

                    <div class="form-group" id="percent_amount_form" style="display: none">
                        <label>Percent : </label>
                        <input class="form-control" type="text" id="percent_amount" name="percent_amount"
                               placeholder="Enter percent (0 - 100)">
                        <small id="percent_error" class="text-danger d-none">Enter numbers 0-100, maximum 2
                            decimals</small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="description" cols="30" rows="3" class="form-control"
                                  placeholder="Optional description"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="submit" id="btn-submit-split-rule" class="btn btn-secondary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            chooseSplit();
            setupEventListeners();
        });

        function setupEventListeners() {
            $('input[name="split_type_option"]').on('change', chooseSplit);

            $(document).on('input', '#percent_amount', function() {
                validatePercentAmount.call(this);
                removeErrorHighlight($(this));
            });

            $(document).on('input', '#flat_amount', function() {
                validateFlatAmount();
                removeErrorHighlight($(this));
            });

            $(document).on('input', '#split_rule_name, #partner_account_id, #description', function() {
                removeErrorHighlight($(this));
            });

            $('#createAccountForm').on('submit', handleFormSubmit);
        }

        const chooseSplit = () => {
            let selected = $("input[name='split_type_option']:checked").val();
            const submitButton = $('#btn-submit-split-rule');

            $('#percent_error').addClass('d-none');
            $('#flat_error').addClass('d-none');
            submitButton.attr('disabled', false);

            if (selected === "FLAT") {
                $("#flat_amount").prop("required", true);
                $("#percent_amount").prop("required", false);
                $("#flat_amount_form").show();
                $("#percent_amount_form").hide();

                validateFlatAmount();
            } else if (selected === "PERCENT") {
                $("#flat_amount").prop("required", false);
                $("#percent_amount").prop("required", true);
                $("#flat_amount_form").hide();
                $("#percent_amount_form").show();

                validatePercentAmount();
            }
        };

        function validatePercentAmount() {
            let val = $(this).val();
            const submitButton = $('#btn-submit-split-rule');

            val = val.replace(',', '.');
            val = val.replace(/[^0-9.]/g, '');

            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts.slice(1).join('');
            }

            if (parts[1] && parts[1].length > 2) {
                parts[1] = parts[1].substring(0, 2);
                val = parts[0] + '.' + parts[1];
            }

            const numericVal = parseFloat(val);

            if (val === '' || isNaN(numericVal)) {
                $('#percent_error').text('Please enter a valid percentage').removeClass('d-none');
                submitButton.attr('disabled', true);
            } else if (numericVal < 0 || numericVal > 100) {
                $('#percent_error').text('Percentage must be between 0 and 100').removeClass('d-none');
                submitButton.attr('disabled', true);
            } else {
                $('#percent_error').addClass('d-none');
                submitButton.attr('disabled', false);
            }

            $(this).val(val);
        }

        function validateFlatAmount() {
            const flatAmount = $('#flat_amount').val();
            const submitButton = $('#btn-submit-split-rule');

            if ($('#flat_error').length === 0) {
                $('#flat_amount').after('<small id="flat_error" class="text-danger d-none">Enter a valid amount (minimum 1, whole number only)</small>');
            }

            const numericVal = parseFloat(flatAmount);

            if (flatAmount === '' || isNaN(numericVal)) {
                $('#flat_error').text('Please enter a valid amount').removeClass('d-none');
                submitButton.attr('disabled', true);
            } else if (numericVal < 1) {
                $('#flat_error').text('Amount must be at least 1').removeClass('d-none');
                submitButton.attr('disabled', true);
            } else if (!Number.isInteger(numericVal)) {
                $('#flat_error').text('Amount must be a whole number (no decimals)').removeClass('d-none');
                submitButton.attr('disabled', true);
            } else {
                $('#flat_error').addClass('d-none');
                submitButton.attr('disabled', false);
            }
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            showPageLoader("Creating Split Rule...");

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.xen_platform.split-payments.split-rules.create') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hidePageLoader();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Split rule created successfully!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#createAccountModal').modal('hide');
                                $('#createAccountForm')[0].reset();
                                chooseSplit();

                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to create split rule.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    hidePageLoader();

                    let errorMessage = 'An error occurred while creating split rule.';
                    let errorDetails = '';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = 'Validation Error';
                        errorDetails = '';

                        Object.keys(errors).forEach(field => {
                            errors[field].forEach(msg => {
                                errorDetails += `• ${msg}<br>`;
                            });
                        });

                        highlightErrorFields(errors);
                    }

                    else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    else if (xhr.status >= 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    }

                    if (errorDetails) {
                        Swal.fire({
                            icon: 'error',
                            title: errorMessage,
                            html: errorDetails,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }

        function validateForm() {
            const splitRuleName = $('#split_rule_name').val().trim();
            const partnerAccountId = $('#partner_account_id').val();
            const splitType = $('input[name="split_type_option"]:checked').val();

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let isValid = true;

            if (!splitRuleName) {
                showFieldError('#split_rule_name', 'Split rule name is required.');
                isValid = false;
            }

            if (!partnerAccountId) {
                showFieldError('#partner_account_id', 'Please select a partner account.');
                isValid = false;
            }

            if (splitType === 'FLAT') {
                const flatAmount = $('#flat_amount').val();
                const numericVal = parseFloat(flatAmount);

                if (!flatAmount || isNaN(numericVal)) {
                    showFieldError('#flat_amount', 'Please enter a valid amount.');
                    isValid = false;
                } else if (numericVal < 1) {
                    showFieldError('#flat_amount', 'Amount must be at least 1.');
                    isValid = false;
                } else if (!Number.isInteger(numericVal)) {
                    showFieldError('#flat_amount', 'Amount must be a whole number (no decimals).');
                    isValid = false;
                }
            } else if (splitType === 'PERCENT') {
                const percentAmount = $('#percent_amount').val();
                const numericVal = parseFloat(percentAmount);

                if (!percentAmount || isNaN(numericVal)) {
                    showFieldError('#percent_amount', 'Please enter a valid percentage.');
                    isValid = false;
                } else if (numericVal < 0 || numericVal > 100) {
                    showFieldError('#percent_amount', 'Percentage must be between 0 and 100.');
                    isValid = false;
                }
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fix the errors in the form before submitting.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }

            return isValid;
        }

        function highlightErrorFields(errors) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            Object.keys(errors).forEach(field => {
                const inputField = $(`[name="${field}"]`);
                const formGroup = inputField.closest('.form-group');

                if (inputField.length) {
                    inputField.addClass('is-invalid');

                    const errorMessages = errors[field].join(', ');
                    formGroup.append(`<div class="invalid-feedback">${errorMessages}</div>`);
                }
            });

            const firstErrorField = Object.keys(errors)[0];
            if (firstErrorField) {
                $(`[name="${firstErrorField}"]`).focus();
            }
        }

        function removeErrorHighlight(field) {
            field.removeClass('is-invalid');
            field.siblings('.invalid-feedback').remove();
        }

        function showFieldError(selector, message) {
            const field = $(selector);
            const formGroup = field.closest('.form-group');

            field.addClass('is-invalid');
            formGroup.append(`<div class="invalid-feedback">${message}</div>`);
        }


        $('#createAccountModal').on('hidden.bs.modal', function () {
            $('#createAccountForm')[0].reset();
            chooseSplit();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });
    </script>
@endpush