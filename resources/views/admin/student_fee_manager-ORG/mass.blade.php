<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.fee_manager', ['value' => 'mass']) }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="class_id_on_create" class="eForm-label">{{ get_phrase('Class') }}</label>
            <select name="class_id" id="class_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSectionOnCreate(this.value)">
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($classes as $class)
                      <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="fpb-7">
            <label for="section_id_on_create" class="eForm-label">{{ get_phrase('Section') }}</label>
            <select name="section_id" id="section_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select section') }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="title" class="eForm-label">{{ get_phrase('Invoice title') }}</label>
            <input type="text" class="form-control eForm-control" id="title" name="title" required>
        </div>

        <div class="fpb-7">
            <label for="amount" class="eForm-label">{{ get_phrase('Total amount').'('.school_currency().')' }}</label>
            <input type="number" class="form-control eForm-control" id="amount" name="amount" required onkeyup="updateDueAmount()" onchange="updateDueAmount()">
        </div>

        <div class="fpb-7">
            <label for="paid_amount" class="eForm-label">{{ get_phrase('Paid amount').'('.school_currency().')' }}</label>
            <input type="number" class="form-control eForm-control" id="paid_amount" name="paid_amount" required onkeyup="updateDueAmount()" onchange="updateDueAmount()">
        </div>

        <div class="fpb-7">
            <label for="due_amount" class="eForm-label">{{ get_phrase('Due amount').'('.school_currency().')' }}</label>
            <input type="number" class="form-control eForm-control" id="due_amount" name="due_amount" required readonly>
        </div>

        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-control" required>
                <option value="">{{ get_phrase('Select a status') }}</option>
                <option value="paid">{{ get_phrase('Paid') }}</option>
                <option value="unpaid">{{ get_phrase('Unpaid') }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="payment_method" class="eForm-label">{{ get_phrase('Payment method') }}</label>
            <select name="payment_method" id="payment_method" class="form-select eForm-control">
                <option value="">{{ get_phrase('Select a payment method') }}</option>
                <option value="cash">{{ get_phrase('Cash') }}</option>
                <option value="paypal">{{ get_phrase('Paypal') }}</option>
                <option value="paytm">{{ get_phrase('Paytm') }}</option>
                <option value="razorpay">{{ get_phrase('Razorpay') }}</option>
                <option value="stripe">{{ get_phrase('Stripe') }}</option>
                <option value="flutterwave">{{ get_phrase('Flutterwave') }}</option>
            </select>
        </div>
    </div>
    <div class="form-group col-md-12">
        <button class="btn-form" type="submit">{{ get_phrase('Create invoice') }}</button>
    </div>
</form>

<script type="text/javascript">
    "use strict";

    // Function to fetch sections dynamically
    function classWiseSectionOnCreate(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_on_create').html(response);
            }
        });
    }

    // Function to auto-update Due Amount
    function updateDueAmount() {
        let amount = parseFloat(document.getElementById("amount").value) || 0;
        let paidAmount = parseFloat(document.getElementById("paid_amount").value) || 0;
        let dueAmount = amount - paidAmount;

        document.getElementById("due_amount").value = dueAmount >= 0 ? dueAmount : 0;
    }

    $(document).ready(function () {
        $(".eChoice-multiple-with-remove").select2();
    });
</script>
