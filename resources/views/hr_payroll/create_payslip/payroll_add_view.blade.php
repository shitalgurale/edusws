<?php

?>
<style>
    .red_tag{
        color:rgb(255, 0, 0);
    }
</style>
<!-- Start Content-->
<div class="container-fluid">
    <form method="post" action="{{ route('hr.insert_payslip_to_db') }}">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" id="input_counts">
                        <h4>
                            {{ get_phrase('Allowances') }}
                        </h4>

                        <hr>
                        <div id="allowance">
                            <div class="row mt-2">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control eForm-control" name="allowance_type[]" id="allowance_type_1" placeholder="{{ get_phrase('Type') }} " />
                                </div>

                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="number" class="form-control eForm-control" name="allowance_amount[]" placeholder="{{ get_phrase('Amount') }}" id="allowance_amount_1" />
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <button type="button" class="btn btn-outline-success btn-sm" onClick="add_allowance()">
                                        +
                                    </button>
                                </div>
                            </div>
                            <span class="red_tag" id="alert_text"></span>
                        </div>


                        <div id="allowance_input">
                            <div class="row mt-2">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control eForm-control" name="allowance_type[]" placeholder="{{ get_phrase('Type') }}" id="allowance_type" readonly />
                                </div>

                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="number" class="form-control eForm-control" name="allowance_amount[]" placeholder="{{ get_phrase('Amount') }}" readonly id="allowance_amount" />
                                </div>

                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <button type="button" class="btn btn-outline-danger" id="allowance_amount_delete" onclick="deleteAllowanceParentElement(this)">
                                        -
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h4>
                            {{ get_phrase('Deductions') }}

                        </h4>
                        <hr>

                        <div id="deduction">
                            <div class="row mt-2">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control eForm-control" name="deduction_type[]" placeholder="{{ get_phrase('Type') }}" id="deduction_type_1" />
                                </div>

                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="number" class="form-control eForm-control" name="deduction_amount[]" placeholder="{{ get_phrase('Amount') }}" id="deduction_amount_1" />
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <button type="button" class="btn btn-outline-success btn-sm" onClick="add_deduction()">
                                        +
                                    </button>
                                </div>
                            </div>
                            <span class="red_tag" id="alert_text_deduction"></span>
                        </div>

                        <div id="deduction_input">
                            <div class="row mt-2">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control eForm-control" name="deduction_type[]" placeholder="{{ get_phrase('Type') }}" id="deduction_type" readonly />
                                </div>

                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <input type="number" class="form-control eForm-control" name="deduction_amount[]" placeholder="{{ get_phrase('Amount') }}" id="deduction_amount" readonly />
                                </div>

                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <button type="button" class="btn btn-outline-danger" id="deduction_amount_delete" onclick="deleteDeductionParentElement(this)">
                                        -
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 col-lg-2"></div>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">


                        @csrf
                        <h3 class="header-title mb-3">
                            {{ get_phrase('Summary') }}
                        </h3>
                        <div class="form-group">
                            <div class="col-12">

                                <label for="field-1" class="control-label">
                                    {{ get_phrase('Basic') }}
                                </label>
                                <input type="text" class="form-control eForm-control" name="basic" id="basic" value="{{ $joining_salary }}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label class="control-label">
                                    {{ get_phrase('Total allowance') }}
                                </label>
                                <input type="text" class="form-control eForm-control" value="0" name="total_allowance" id="total_allowance" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label class="control-label">
                                    {{ get_phrase('Total deduction') }}
                                </label>
                                <input type="text" class="form-control eForm-control" value="0" name="total_deduction" id="total_deduction" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="field-1" class="control-label">
                                    {{ get_phrase('Net salary') }}
                                </label>
                                <input type="text" class="form-control eForm-control" name="net_salary" id="net_salary" value="{{ $joining_salary }}" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="field-2">
                                    {{ get_phrase('Status') }}
                                </label>
                                <select name="status" class="form-select eForm-control selectboxit">
                                    <option value="1">
                                        {{ get_phrase('Paid') }}
                                    </option>
                                    <option value="0">
                                        {{ get_phrase('Unpaid') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group pt-2">
                            <div class="col-12">
                                <button type="submit" class="btn-form float-right">
                                    {{ get_phrase('Create payslip') }}
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{ $user_id }}" />
                        <input type="hidden" name="month" value="{{ $month }}" />
                        <input type="hidden" name="year" value="{{ $year }}" />
    </form>
</div>
</div>
</div>
</div>

</div>
<script src="{{ asset('assets/custom/js/191jquery.min.js') }}"></script>
<script src="{{ asset('assets/custom/js/331jquery.min.js') }}"></script>


<script type="text/javascript">

"use strict";

    var allowance_count     = 1;
    var deduction_count     = 1;
    var total_allowance     = 0;
    var total_deduction     = 0;
    var deleted_allowances  = [];
    var deleted_deductions  = [];
    var i;
    var net_salary;
    var current_delete_amout;
    var total_allowance_1;
    var net_salary_1;
    var total_deduction_1;

    $(document).ready(function () {
        $('#allowance').show();


        if ($.isFunction($.fn.selectBoxIt))
        {
            $("select.selectboxit").each(function (i, el)
            {
                var $this = $(el),
                        opts = {
                            showFirstOption: attrDefault($this, 'first-option', true),
                            'native': attrDefault($this, 'native', false),
                            defaultText: attrDefault($this, 'text', ''),
                        };

                $this.addClass('visible');
                $this.selectBoxIt(opts);
            });
        }

    });




    function get_users(role_id)
    {
        if(role_id != '')
        {
        var role_id = $('#role_id').val();
        var url="{{ route('hr.get_user_by_role') }}";
        $.ajax({
            url: url,
            data:{role_id:role_id},
            success : function(response)
            {
              jQuery('#user_holder').html(response);
            }
          });
        }
        else
        {
            jQuery('#user_holder').html('<option value="">{{ get_phrase("select_a_role_first") }}</option>');
        }

    }

    $('#allowance_input').hide();
    $('#allowance').hide()
    $('#deduction').hide()





    // CREATING BLANK ALLOWANCE INPUT
    var blank_allowance = '';
    var count_number_of_added_blank_allowance=0;
    $(document).ready(function () {
        blank_allowance = $('#allowance_input').html();
    });

    function add_allowance()
    {

          var  First_input_type=$('#allowance_type_1').val();
          var  First_input_amount=$('#allowance_amount_1').val();


        if(First_input_type!="" && First_input_amount!="" && First_input_amount>0)
        {
            calculate_total_allowance()
            allowance_count++;
            $("#alert_text").text("");
            $('#first_minus').hide();
            count_number_of_added_blank_allowance++;
            $("#allowance").append(blank_allowance);
            $('#allowance_type').attr('id', 'allowance_type_' + allowance_count);
            $('#allowance_amount').attr('id', 'allowance_amount_' + allowance_count);
            $('#allowance_amount_delete').attr('id', 'allowance_amount_delete_' + allowance_count);
            $('#allowance_amount_delete_' + allowance_count).attr('onclick', 'deleteAllowanceParentElement(this, ' + allowance_count + ')');
            $('#allowance_type_' + allowance_count).val(First_input_type);
            $('#allowance_amount_' + allowance_count).val(First_input_amount);
            document.getElementById("allowance_type_1").value = "";
            document.getElementById("allowance_amount_1").value = "";
        }
        else
        {
            $("#alert_text").text("please fill up the inputs with correct value");
        }

    }

    // REMOVING ALLOWANCE INPUT
    function deleteAllowanceParentElement(n, allowance_count) {
        count_number_of_added_blank_allowance--;
        current_delete_amout =  parseInt($('#allowance_amount_' + allowance_count).val());
        total_allowance_1= parseInt($('#total_allowance').val());
        total_allowance_1=parseInt(total_allowance_1)-parseInt(current_delete_amout);
        net_salary_1= parseInt($('#net_salary').val());
        if( current_delete_amout!=0)
        {
            net_salary_1=parseInt(net_salary_1)-parseInt(current_delete_amout);
        }


        $('#net_salary').val(net_salary_1);
        $('#total_allowance').val(total_allowance_1);

        if(count_number_of_added_blank_allowance == 0)
        {
            $('#first_minus').show();
        count_number_of_added_blank_allowance=0;

        }

        n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
        deleted_allowances.push(allowance_count);

    }

    function calculate_total_allowance()
    {

        var amount;
        for(i = 1; i <= allowance_count; i++) {
            if(jQuery.inArray(i, deleted_allowances) == -1)
            {

                amount = $('#allowance_amount_' + i).val();

                    amount = parseInt(amount);
                    total_allowance = amount + total_allowance;
                    $('#total_allowance').val(total_allowance);



            }

        }
        net_salary = parseInt($('#basic').val()) + parseInt($('#total_allowance').val()) - parseInt($('#total_deduction').val());
        $('#net_salary').val(net_salary);
        total_allowance = 0;
    }

    $('#deduction_input').hide();



    // CREATING BLANK DEDUCTION INPUT
    var blank_deduction = '';
    var count_number_of_added_blank_deduction=0;
    $(document).ready(function () {
        blank_deduction = $('#deduction_input').html();
        $('#deduction').show();
    });

    function add_deduction()
    {


        var First_input_type_deduction=$('#deduction_type_1').val();
        var First_input_amount_deduction=$('#deduction_amount_1').val();



        if(First_input_type_deduction!="" && First_input_amount_deduction!="" && First_input_amount_deduction>0)
        {
            calculate_total_deduction()
            deduction_count++;
            $("#alert_text_deduction").text("");
            $('#first_minus_deduction').hide();
            count_number_of_added_blank_deduction++;
            $("#deduction").append(blank_deduction);
            $('#deduction_type').attr('id', 'deduction_type_' + deduction_count);
            $('#deduction_amount').attr('id', 'deduction_amount_' + deduction_count);
            $('#deduction_amount_delete').attr('id', 'deduction_amount_delete_' + deduction_count);
            $('#deduction_amount_delete_' + deduction_count).attr('onclick', 'deleteDeductionParentElement(this, ' + deduction_count + ')');
                //my code
            $('#deduction_type_'+ deduction_count).val(First_input_type_deduction);
            $('#deduction_amount_'+ deduction_count).val(First_input_amount_deduction);
            document.getElementById("deduction_type_1").value = "";
            document.getElementById("deduction_amount_1").value = "";
        }
        else
        {
            $("#alert_text_deduction").text("please fill up the fields with correct input");
        }
    }

    // REMOVING DEDUCTION INPUT
    function deleteDeductionParentElement(n, deduction_count) {

        count_number_of_added_blank_deduction--;
        current_delete_amout =  parseInt($('#deduction_amount_' + deduction_count).val());
        total_deduction_1= parseInt($('#total_deduction').val());
        total_deduction_1=parseInt(total_deduction_1)-parseInt(current_delete_amout);
        net_salary_1= parseInt($('#net_salary').val());
        if( current_delete_amout!=0)
        {
            net_salary_1=parseInt(net_salary_1)+parseInt(current_delete_amout);
        }


        $('#net_salary').val(net_salary_1);
        $('#total_deduction').val(total_deduction_1);

        if(count_number_of_added_blank_deduction == 0)
        {
            $('#first_minus_deduction').show();
        count_number_of_added_blank_deduction=0;

        }



        n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
        deleted_deductions.push(deduction_count);
    }

    function calculate_total_deduction()
    {
        var amount;
        for(i = 1; i <= deduction_count; i++) {
            if(jQuery.inArray(i, deleted_deductions) == -1)
            {
                amount = $('#deduction_amount_' + i).val();

                    amount = parseInt(amount);
                    total_deduction = amount + total_deduction;
                    $('#total_deduction').val(total_deduction);


            }
        }
        net_salary = parseInt($('#basic').val()) + parseInt($('#total_allowance').val()) - parseInt($('#total_deduction').val());
        $('#net_salary').val(net_salary);
        total_deduction = 0;
    }

</script>
