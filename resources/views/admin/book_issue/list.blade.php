<?php 

use App\Http\Controllers\CommonController;
use App\Models\Book;

?>


@if(count($book_issues) > 0)
<div class="table-responsive">
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ get_phrase('Book name') }}</th>
                <th>{{ get_phrase('Issue date') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th class="text-center">{{ get_phrase('Option') }}</th>
            </tr>
        </thead>
        <tbody>
@foreach ($book_issues as $book_issue)

    <?php 
        $book_details = \App\Models\Book::find($book_issue->book_id ?? null);
        $student_details = (new \App\Http\Controllers\CommonController)->get_student_details_by_id($book_issue->student_id ?? null);
    ?>

    @if ($book_details && $student_details)
        <tr>
            <td>{{ $loop->index + 1 }}</td>

            <td><strong>{{ $book_details->name ?? 'N/A' }}</strong></td>

            <td>{{ date('D, d/M/Y', $book_issue->issue_date ?? time()) }}</td>

            <td>
                <strong>{{ $student_details->name ?? 'N/A' }}</strong><br>
                <strong>{{ get_phrase('Id') }}: </strong>
                <small>{{ $student_details->code ?? 'N/A' }}</small>
            </td>

            <td>{{ $student_details->class_name ?? 'N/A' }}</td>

            <td>
                @if ($book_issue->status)
                    <span class="eBadge ebg-success">{{ get_phrase('Returned') }}</span>
                @else
                    <span class="eBadge ebg-danger">{{ get_phrase('Pending') }}</span>
                @endif
            </td>

            <td class="text-start">
                <div class="adminTable-action">
                    <button
                      type="button"
                      class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                      data-bs-toggle="dropdown"
                      aria-expanded="false"
                    >
                      {{ get_phrase('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                        @if (!$book_issue->status)
                            <li>
                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.book_issue', ['id' => $book_issue->id]) }}', '{{ get_phrase('Update issued book') }}')">
                                    {{ get_phrase('Edit') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.book_issue.return', ['id' => $book_issue->id]) }}', 'undefined');">
                                    {{ get_phrase('Return this book') }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.book_issue.delete', ['id' => $book_issue->id]) }}', 'undefined');">
                                {{ get_phrase('Delete') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endif

@endforeach

        </tbody>
    </table>
</div>
@else
<div class="empty_box center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    <span class="">{{ get_phrase('No data found') }}</span>
</div>
@endif

@if(count($book_issues) > 0)
<div class="table-responsive display-none-view" id="book_issue_report">
    <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase('Book issue list') }}</h4>
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ get_phrase('Book name') }}</th>
                <th>{{ get_phrase('Issue date') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($book_issues as $book_issue)
                <?php 
                $book_details = Book::find($book_issue['book_id']);
                $student_details = (new CommonController)->get_student_details_by_id($book_issue['student_id']);
                ?>
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><strong>{{ $book_details['name'] }}</strong></td>
                    <td>
                        {{ date('D, d/M/Y', $book_issue['issue_date']) }}
                    </td>
                    <td>
                        <strong>{{ $student_details->name ?? 'N/A' }}</strong>
                        <br> 
                        <strong>{{ get_phrase('Id') }}: </strong>
                        <small>{{ $student_details->code ?? 'N/A' }}</small>
                    </td>
                    <td>
                        {{ $student_details->class_name ?? 'N/A' }}
                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-success">{{ get_phrase('Returned') }}</span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger">{{ get_phrase('Pending') }}</span>
                        <?php endif; ?>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif