<?php
use App\Http\Controllers\CommonController;
?>

@if(count($invoices) > 0)
<div class="table-responsive">
    <table id="student_fee" class="table eTable eTable-2">
        <thead>
            <tr>
                <th>{{ get_phrase('Invoice No') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Invoice Title') }}</th>
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Document') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('Option') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (array)(new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>{{ sprintf('%08d', $invoice['id']) }}</td>
                    <td>
                        <strong>{{ $student_details['name'] ?? 'N/A' }}</strong> <br>
                        <small><strong>{{ get_phrase('Class') }}:</strong> {{ $student_details['class_name'] ?? 'N/A' }}</small><br>
                        <small><strong>{{ get_phrase('Section') }}:</strong> {{ $student_details['section_name'] ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $invoice['title'] ?? 'N/A' }}</td>
                    <td>
                        {{ school_currency($invoice['total_amount']) }} <br>
                        <small><strong>{{ get_phrase('Created at') }}:</strong> {{ date('d-M-Y', $invoice['timestamp']) }}</small>
                    </td>
                    <td>
                        {{ school_currency($invoice['paid_amount']) }} <br>
                        <small>
                            <strong>{{ get_phrase('Payment date') }}:</strong>
                            @php
                                $updated_time = strtotime($invoice['updated_at'] ?? '');
                            @endphp
                            {{ $updated_time ? date('d-M-Y', $updated_time) : get_phrase('Not found') }}
                        </small>
                    </td>
                    <td>
                        <a href="{{ asset('assets/uploads/offline_payment/'.$invoice['document_image']) }}" download>
                            {{ $invoice['document_image'] }}
                        </a>
                    </td>
                    <td>
                        @if(strtolower($invoice['status']) == 'unpaid')
                            <span class="eBadge ebg-danger">{{ get_phrase('Never Paid') }}</span>
                        @else
                            <span class="eBadge ebg-success">{{ ucfirst($invoice['status']) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="adminTable-action">
                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown">
                                {{ get_phrase('Actions') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.studentFeeinvoice', ['id'=>$invoice['id']]) }}" target="_blank">{{ get_phrase('Print invoice') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.update_offline_payment', ['id' => $invoice['id'],'status'=>'approve']) }}', 'undefined');">{{ get_phrase('Approve') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.update_offline_payment', ['id' => $invoice['id'],'status'=>'decline']) }}', 'undefined');">{{ get_phrase('Decline') }}</a>
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
<div class="card-body permission_content">
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
                <th>{{ get_phrase('Total Amount') }}</th>
                <th>{{ get_phrase('Created at') }}</th>
                <th>{{ get_phrase('Paid Amount') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <?php $student_details = (array)(new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td>{{ sprintf('%08d', $invoice['id']) }}</td>
                    <td><strong>{{ $student_details['name'] ?? 'N/A' }}</strong></td>
                    <td>
                        <small>{{ $student_details['class_name'] ?? 'N/A' }}</small><br>
                        <small>{{ $student_details['section_name'] ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $invoice['title'] ?? 'N/A' }}</td>
                    <td>{{ school_currency($invoice['total_amount']) }}</td>
                    <td><small>{{ date('d-M-Y', $invoice['timestamp']) }}</small></td>
                    <td>{{ school_currency($invoice['paid_amount']) }}</td>
                    <td>
                        @if(strtolower($invoice['status']) == 'unpaid')
                            <span class="eBadge ebg-success">{{ ucfirst($invoice['status']) }}</span>
                        @else
                            <span class="eBadge ebg-danger">{{ ucfirst($invoice['status']) }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
