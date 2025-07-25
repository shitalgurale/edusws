<?php
use App\Http\Controllers\CommonController;
use App\Models\FeeInstallment;


if ($selected_class == "") {
    $sel_class = 'all-class';
} else {
    $sel_class = $selected_class;
}

if ($date_from == "") {
    $date_from = date('d-M-Y', strtotime(' -30 day'));
} else {
    $date_from = date('d-M-Y', $date_from);
}

if ($date_to == "") {
    $date_to = date('d-M-Y');
} else {
    $date_to = date('d-M-Y', $date_to);
}

if ($selected_status == "") {
    $sel_status = 'paid-and-unpaid';
} else {
    $sel_status = $selected_status;
}
?>

@if(count($invoices) > 0)
<div class="table-responsive">
    <table id="student_fee" class="table eTable eTable-2">
        <thead>
            <tr>
                <th>{{ get_phrase('Invoice No') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Invoice Title') }}</th>
                <th>{{ get_phrase('Amount') }}</th>
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Installments') }}</th>
                <th>{{ get_phrase('Due Amount') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('Option') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>
                        {{ sprintf('%08d', $invoice['id']) }}
                    </td>
                    <td>
                        <strong>{{ $student_details->name }}</strong> <br>
                        <small> <strong>{{ get_phrase('Class') }} :</strong> {{ $student_details->class_name }}</small> <br>
                        <small><strong>{{ get_phrase('Section') }} :</strong> {{ $student_details->section_name }}</small>
                    </td>
                    <td>
                        {{ $invoice['title'] }}
                    </td>
                    <td>
                        {{ school_currency($invoice['amount']) }}
                        @if(!empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0)
                        <br>
                        <small> <strong> {{ get_phrase('Discount:') }}</strong> {{ school_currency($invoice['discounted_price']) }}</small>
                        @endif
                    </td>
                    <td>
                        @if(!empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0)
                            {{ school_currency($invoice['amount'] - $invoice['discounted_price']) }}
                        @else
                            {{ school_currency($invoice['amount']) }}
                        @endif
                    </td>
                    <td>
                        {{ school_currency($invoice['paid_amount']) }} <br>
                        <small>
                            <strong> {{ get_phrase('Paid date') }} : </strong>
                            <?php $updated_time = strtotime($invoice['updated_at']); ?>
                            @if ($updated_time != "")
                                {{ date('d-M-Y', $updated_time) }}
                            @else
                                {{ get_phrase('Not found') }}
                            @endif
                        </small>
                    </td>
                    <td>
                        @php
                            $installments = \App\Models\FeeInstallment::where('invoice_id', $invoice->id ?? $invoice_details['id'])->orderBy('paid_at')->get();
                        @endphp
                        @if($installments->count() > 0)
                            @foreach($installments as $index => $inst)
                                <strong>{{ ordinal($index + 1) }} Installment:</strong> {{ school_currency($inst->amount_paid) }}<br>
                                <small><strong>Paid Date:</strong> {{ date('d-M-Y', strtotime($inst->paid_at)) }}</small><br>
                            @endforeach
                        @else
                            <span class="text-muted">{{ get_phrase('No installments') }}</span>
                        @endif
                    </td>
                    <td>
                        {{ school_currency($invoice['due_amount']) }}
                    </td>
                    <td>
                        @php
                            $actual_total = !empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0 ? $invoice['amount'] - $invoice['discounted_price'] : $invoice['amount'];
                        @endphp
                        @if($invoice['paid_amount'] == 0 || is_null($invoice['paid_amount']))
                            <span class="eBadge ebg-danger">{{ get_phrase('Never Paid') }}</span>
                        @elseif($invoice['paid_amount'] < $actual_total)
                            <span class="eBadge ebg-warning text-dark">{{ get_phrase('Partial Paid') }}</span>
                        @else
                            <span class="eBadge ebg-success">{{ get_phrase('Paid') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="adminTable-action">
                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ get_phrase('Actions') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.studentFeeinvoice', ['id'=>$invoice['id']]) }}" target="_blank">{{ get_phrase('Print invoice') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.fee_manager', ['id' => $invoice->id]) }}', '{{ get_phrase('Edit Invoice') }}')">{{ get_phrase('Edit') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.fee_manager.delete', ['id' => $invoice->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card-body fee_content">
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        {{ get_phrase('No data found') }}
    </div>
</div>
@endif

<div class="table-responsive display-none-view" id="student_fee_report">
    <table id="student_fee_report" class="table eTable eTable-2">
        <thead>
            <tr>
                <th>{{ get_phrase('Invoice No') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class & Section') }}</th>
                <th>{{ get_phrase('Invoice Title') }}</th>
                <th>{{ get_phrase('Amount') }}</th>
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Created at') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Installments') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>
                        {{ sprintf('%08d', $invoice['id']) }}
                    </td>
                    <td>
                        {{ $student_details->name }}
                    </td>
                    <td>
                        <small>{{ $student_details->class_name }}</small> <br>
                        <small>{{ $student_details->section_name }}</small>
                    </td>
                    <td>
                        {{ $invoice['title'] }}
                    </td>
                    <td>
                        {{ school_currency($invoice['amount']) }}
                    </td>
                    <td>
                        @if(!empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0)
                            {{ school_currency($invoice['amount'] - $invoice['discounted_price']) }}
                        @else
                            {{ school_currency($invoice['amount']) }}
                        @endif
                    </td>
                    <td>
                        <small>{{ date('d-M-Y', $invoice['timestamp']) }} </small>
                    </td>
                    <td>
                        {{ school_currency($invoice['paid_amount']) }}
                    </td>
                    <td>
                        <span class="eBadge ebg-{{ strtolower($invoice['status']) == 'unpaid' ? 'danger' : 'success' }}">{{ ucfirst($invoice['status']) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
