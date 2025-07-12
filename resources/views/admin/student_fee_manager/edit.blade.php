<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.fee_manager.update', ['id' => $invoice_details->id]) }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="class_id_on_create" class="eForm-label">{{ get_phrase('Class') }}</label>
            <select name="class_id" id="class_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseStudentOnCreate(this.value)">
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class['id'] }}" {{ $class['id'] == $invoice_details['class_id'] ? 'selected' : '' }}>{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="fpb-7">
            <label for="student_id_on_create" class="eForm-label">{{ get_phrase('Select student') }}</label>
            <div id="student_content">
                <select name="student_id" id="student_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select a student') }}</option>
                    @foreach ($enrollments as $enrollment)
                    <?php $student = \App\Models\User::find($enrollment->user_id); ?>
                        <option value="{{ $enrollment['user_id'] }}" {{ $enrollment['user_id'] == $invoice_details['student_id'] ? 'selected' : '' }}>{{ $student['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="fpb-7">
            <label for="title" class="eForm-label">{{ get_phrase('Invoice title') }}</label>
            <input type="text" class="form-control eForm-control" id="title" name="title" value="{{ $invoice_details['title'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="amount" class="eForm-label">{{ get_phrase('Total amount') }} ({{ school_currency() }})</label>
            <input type="number" class="form-control eForm-control" id="amount" name="amount" value="{{ $invoice_details['amount'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="discounted_price" class="eForm-label">{{ get_phrase('Discount amount') }} ({{ school_currency() }})</label>
            <input type="number" class="form-control eForm-control" id="discounted_price" name="discounted_price" value="{{ $invoice_details['discounted_price'] ?? 0 }}" min="0" onkeyup="updateDueAmount()">
        </div>

        <div class="fpb-7">
            <label for="paid_amount" class="eForm-label">{{ get_phrase('Previously Paid') }} ({{ school_currency() }})</label>
            <input type="text" readonly class="form-control eForm-control" id="paid_amount" name="paid_amount" value="{{ $invoice_details['paid_amount'] }}">
        </div>

        <div class="fpb-7">
            <label for="new_installment_amount" class="eForm-label">{{ get_phrase('Add Installment Amount') }} ({{ school_currency() }})</label>
            <input type="number" class="form-control eForm-control" id="new_installment_amount" name="new_installment_amount" min="0" placeholder="e.g. 5000" oninput="updateDueAmount()">
            <small class="text-muted">This will be added to previously paid amount</small>
        </div>

        <div class="fpb-7">
            <label for="calculated_due" class="eForm-label">{{ get_phrase('Updated Due Amount') }} ({{ school_currency() }})</label>
            <input type="text" readonly class="form-control eForm-control" id="calculated_due" value="">
        </div>

            @php
                $paid_amount = $invoice_details['paid_amount'];
                $due_amount = $invoice_details['due_amount'];
                $status = 'unpaid';

                if ($due_amount == 0) {
                    $status = 'paid';
                } elseif ($paid_amount > 0 && $due_amount > 0) {
                    $status = 'partially_paid';
                }
            @endphp

        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select a status') }}</option>
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>{{ get_phrase('Paid') }}</option>
                <option value="partially_paid" {{ $status == 'partially_paid' ? 'selected' : '' }}>{{ get_phrase('Partial Paid') }}</option>
                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>{{ get_phrase('Never paid') }}</option>
            </select>
        </div>


        <div class="fpb-7">
            <label for="payment_method" class="eForm-label">{{ get_phrase('Payment method') }}</label>
            <select name="payment_method" id="payment_method" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">{{ get_phrase('Select a payment method') }}</option>
                <option value="cash" {{ $invoice_details['payment_method'] == 'cash' ? 'selected' : '' }}>{{ get_phrase('Cash') }}</option>
                <option value="paypal" {{ $invoice_details['payment_method'] == 'paypal' ? 'selected' : '' }}>{{ get_phrase('Paypal') }}</option>
                <option value="paytm" {{ $invoice_details['payment_method'] == 'paytm' ? 'selected' : '' }}>{{ get_phrase('Paytm') }}</option>
                <option value="razorpay" {{ $invoice_details['payment_method'] == 'razorpay' ? 'selected' : '' }}>{{ get_phrase('Razorpay') }}</option>
                <option value="stripe" {{ $invoice_details['payment_method'] == 'stripe' ? 'selected' : '' }}>{{ get_phrase('Stripe') }}</option>
                <option value="flutterwave" {{ $invoice_details['payment_method'] == 'flutterwave' ? 'selected' : '' }}>{{ get_phrase('Flutterwave') }}</option>
            </select>
        </div>
    </div>

    <div class="form-group col-md-12">
        <button class="btn-form" type="submit">{{ get_phrase('Update invoice') }}</button>
    </div>
</form>

<script type="text/javascript">
"use strict";

function updateDueAmount() {
    let totalAmount = parseFloat(document.getElementById('amount').value) || 0;
    let discount = parseFloat(document.getElementById('discounted_price').value) || 0;
    let paid = parseFloat(document.getElementById('paid_amount').value) || 0;
    let installment = parseFloat(document.getElementById('new_installment_amount').value) || 0;

    let netFee = totalAmount - discount;
    let updatedPaid = paid + installment;
    let due = netFee - updatedPaid;

    document.getElementById('calculated_due').value = due > 0 ? due.toFixed(2) : "0.00";

    if (updatedPaid > netFee) {
        document.getElementById('calculated_due').value = "Exceeds total!";
    }
}

function classWiseStudentOnCreate(classId) {
    let url = "{{ route('admin.class_wise_student', ['id' => ':classId']) }}".replace(":classId", classId);
    $.ajax({
        url: url,
        success: function(response){
            $('#student_id_on_create').html(response);
        }
    });
}

$(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
    updateDueAmount(); // Initialize on load
});
</script>
