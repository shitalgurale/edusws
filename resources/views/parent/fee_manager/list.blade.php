<?php
use App\Http\Controllers\CommonController;
?>

<div>
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>{{ get_phrase('Invoice No') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Invoice Title') }}</th>
                <th>{{ get_phrase('Amount') }}</th>
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Due Amount') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('Option') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>{{ sprintf('%08d', $invoice['id']) }}</td>
                    <td>
                        {{ $student_details->name }} <br>
                        <small><strong>{{ get_phrase('Class') }} :</strong> {{ $student_details->class_name }}</small> <br>
                        <small><strong>{{ get_phrase('Section') }} :</strong> {{ $student_details->section_name }}</small>
                    </td>
                    <td>{{ $invoice['title'] }}</td>
                    <td>
                        {{ school_currency($invoice['amount']) }}
                        @if(!empty($invoice['discounted_price']))<br>
                        <small><strong>{{ get_phrase('Discount:') }}</strong> {{ school_currency($invoice['discounted_price']) }}</small>
                        @endif
                    </td>
                    <td>
                        {{ school_currency($invoice['total_amount']) }} <br>
                        <small><strong>{{ get_phrase('Created at') }}:</strong> {{ date('d-M-Y', $invoice['timestamp']) }}</small>
                    </td>
                    <td>
                        {{ school_currency($invoice['paid_amount']) }} <br>
                        <small>
                            <strong>{{ get_phrase('Payment date') }}:</strong>
                            <?php $updated_time = strtotime($invoice['updated_at']); ?>
                            @if ($updated_time != '')
                                {{ date('d-M-Y', $updated_time) }}
                            @else
                                {{ get_phrase('Not found') }}
                            @endif
                        </small>
                    </td>
                    <td>{{ school_currency($invoice['due_amount']) }}</td>
                    <td>
                        @php $status = strtolower($invoice['status']); @endphp
                        @if ($status == 'unpaid')
                            <span class="eBadge ebg-soft-danger">{{ ucfirst($status) }}</span>
                        @elseif ($status == 'pending')
                            <span class="eBadge ebg-soft-warning">{{ ucfirst($status) }}</span>
                        @else
                            <span class="eBadge ebg-soft-success">{{ ucfirst($status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="adminTable-action">
                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ get_phrase('Actions') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                <li>
                                    <a class="dropdown-item" href="{{ route('parent.studentFeeinvoice', ['id' => $invoice['id']]) }}" target="_blank">
                                        {{ get_phrase('Print invoice') }}
                                    </a>
                                </li>
                                @php
                                    $actual_total = !empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0
                                        ? $invoice['amount'] - $invoice['discounted_price']
                                        : $invoice['amount'];
                                @endphp
                                @if ($status == 'unpaid' || $invoice['paid_amount'] < $actual_total)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('parent.FeePayment', ['id' => $invoice['id']]) }}">
                                            {{ get_phrase('Pay') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="display-none-view" id="student_fee_report">
    <table id="student_fee_report" class="table eTable">
        <thead>
            <tr>
                <th>{{ get_phrase('Invoice No') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class & Section') }}</th>
                <th>{{ get_phrase('Invoice Title') }}</th>
                <th>{{ get_phrase('Amount') }}</th>
                <th>{{ get_phrase('Discount Amount') }}</th>
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Created at') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Due Amount') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>{{ sprintf('%08d', $invoice['id']) }}</td>
                    <td>{{ $student_details->name }}</td>
                    <td>
                        <small>{{ $student_details->class_name }}</small><br>
                        <small>{{ $student_details->section_name }}</small>
                    </td>
                    <td>{{ $invoice['title'] }}</td>
                    <td>
                        {{ school_currency($invoice['amount']) }}
                        @if(!empty($invoice['discounted_price']))<br>
                        <small><strong>{{ get_phrase('Discount:') }}</strong> {{ school_currency($invoice['discounted_price']) }}</small>
                        @endif
                    </td>
                    <td>{{ school_currency($invoice['discounted_price'] ?? 0) }}</td>
                    <td>{{ school_currency($invoice['total_amount']) }}</td>
                    <td><small>{{ date('d-M-Y', $invoice['timestamp']) }}</small></td>
                    <td>{{ school_currency($invoice['paid_amount']) }}</td>
                    <td>{{ school_currency($invoice['due_amount']) }}</td>
                    <td>
                        @php $status = strtolower($invoice['status']); @endphp
                        @if ($status == 'unpaid')
                            <span class="bg bg-danger">{{ ucfirst($status) }}</span>
                        @else
                            <span class="bg bg-success">{{ ucfirst($status) }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
