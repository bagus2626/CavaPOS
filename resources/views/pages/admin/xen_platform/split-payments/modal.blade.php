<div class="modal fade text-left" id="createAccountModal" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel140">Create Split Rules</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="createAccountForm" method="POST" action="{{ route('admin.xen_platform.split-payments.split-rules.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Split Rule Name : </label>
                        <input class="form-control" type="text" id="split_rule_name" name="split_rule_name" required>
                    </div>
{{--                    <div class="row mt-1">--}}
{{--                        <div class="col-12">--}}
{{--                            <div class="form-group mb-1">--}}
{{--                                <label>Source Account</label>--}}
{{--                                <div class="d-flex justify-content-start">--}}
{{--                                    <div class="radio">--}}
{{--                                        <input type="radio" name="destination_account_option" value="MASTER"--}}
{{--                                               id="master_account_radio" checked onchange="chooseAccountType()">--}}
{{--                                        <label for="master_account_radio">Master Account</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="radio ml-2">--}}
{{--                                        <input type="radio" name="destination_account_option" value="SUB"--}}
{{--                                               id="sub_account_radio" onchange="chooseAccountType()">--}}
{{--                                        <label for="sub_account_radio">Sub Account</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="form-group">
                        <label>Source Account</label>
                        <select class="form-control" name="partner_account_id" id="partner_account_id">
                            <option value="" selected>Select Partner Account</option>
                            @foreach($accounts AS $account)
                                <option value="{{ $account['xendit_user_id'] }}">{{ $account['business_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

{{--                    <div class="row mt-1">--}}
{{--                        <div class="col-12">--}}
{{--                            <div class="form-group mb-1">--}}
{{--                                <label>Destination Account</label>--}}
{{--                                <div class="d-flex justify-content-start">--}}
{{--                                    <div class="radio">--}}
{{--                                        <input type="radio" name="destination_account_option" value="MASTER" id="master_account_radio" checked onchange="chooseAccountType()">--}}
{{--                                        <label for="master_account_radio">Master Account</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="radio ml-2">--}}
{{--                                        <input type="radio" name="destination_account_option" value="SUB" id="sub_account_radio" onchange="chooseAccountType()">--}}
{{--                                        <label for="sub_account_radio">Sub Account</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="form-group" id="master_account_form">
                        <label>Destination Account : </label>
                        <input class="form-control" type="text" value="PT VASTU CIPTA PERSADA" readonly required>
                    </div>

{{--                    <div class="form-group" id="sub_account_form" style="display: none">--}}
{{--                        <select class="form-control" name="partner_account_id" id="partner_account_id">--}}
{{--                            <option value="" selected>Select Partner Account</option>--}}
{{--                            @foreach($accounts AS $account)--}}
{{--                                <option value="{{ $account['xendit_user_id'] }}">{{ $account['business_name'] }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}

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
                                        <input type="radio" name="split_type_option" value="FLAT" id="flat_amount_radio" checked onchange="chooseSplit()">
                                        <label for="flat_amount_radio">Flat</label>
                                    </div>
                                    <div class="radio ml-2">
                                        <input type="radio" name="split_type_option" value="PERCENT" id="percent_amount_radio" onchange="chooseSplit()">
                                        <label for="percent_amount_radio">Percent</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="flat_amount_form">
                        <label>Amount : </label>
                        <input class="form-control" type="number" id="flat_amount" name="flat_amount">
                    </div>

                    <div class="form-group" id="percent_amount_form" style="display: none">
                        <label>Percent : </label>
                        <input class="form-control" type="text" id="percent_amount" name="percent_amount" placeholder="Masukkan persen (0 - 100)">
                        <small id="percent_error" class="text-danger d-none">Masukkan angka 0–100, maksimal 2 desimal</small>

                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="description" cols="30" rows="3" class="form-control" placeholder="Keterangan optional"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="submit" id="btn-submit-split-rule" class="btn btn-warning ml-1">
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
        $(document).ready(function() {
            $("#partner_account_id").select2();
        });

        const chooseSplit = () => {
            let selected = $("input[name='split_type_option']:checked").val();

            if (selected === "FLAT") {
                $("#flat_amount").addClass("required");
                $("#percent_amount").removeClass("required");
                $("#flat_amount_form").show();
                $("#percent_amount_form").hide();
            } else if (selected === "PERCENT") {
                $("#flat_amount").removeClass("required");
                $("#percent_amount").addClass("required");
                $("#flat_amount_form").hide();
                $("#percent_amount_form").show();
            }
        };

        // const chooseAccountType = () => {
        //     let selected = $("input[name='destination_account_option']:checked").val();
        //
        //     if (selected === "MASTER") {
        //         $("#partner_account_id").removeClass("required");
        //         $("#partner_account_id").val('');
        //         $("#master_account_form").show();
        //         $("#sub_account_form").hide();
        //     } else if (selected === "SUB") {
        //         $("#partner_account_id").addClass("required");
        //         $("#master_account_form").hide();
        //         $("#sub_account_form").show();
        //     }
        // };

        $(document).on('input', '#percent_amount', function () {
            let val = $(this).val();
            val = val.replace(',', '.');
            val = val.replace(/[^0-9.]/g, '');

            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts[1];
            }

            if (parts[1] && parts[1].length > 2) {
                parts[1] = parts[1].substring(0, 2);
                val = parts.join('.');
            }

            const numericVal = parseFloat(val);

            if (isNaN(numericVal) || numericVal < 0 || numericVal > 100) {
                $('#percent_error').removeClass('d-none');
                $('#btn-submit-split-rule').attr('disabled', true);
            } else {
                $('#percent_error').addClass('d-none');
                $('#btn-submit-split-rule').attr('disabled', false);
            }

            $(this).val(val);
        })    </script>
@endpush