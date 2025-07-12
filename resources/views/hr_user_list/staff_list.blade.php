@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('All Staff') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
                        <li><a href="#">{{ get_phrase('All Staff') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="{{ route('hr.create_user') }}" class="export_btn">
                        <i class="bi bi-plus"></i> {{ get_phrase('Add Staff') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if (count($users) > 0)
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="table-responsive">
                <table class="table eTable eTable-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ get_phrase('Name') }}</th>
                            <th scope="col">{{ get_phrase('Email') }}</th>
                            <th scope="col">{{ get_phrase('Address') }}</th>
                            <th scope="col">{{ get_phrase('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; @endphp
                        @foreach($users as $user)
                            @php $info = json_decode($user->user_information); @endphp
                            <tr>
                                <th scope="row">
                                    <p class="row-number">{{ $index++ }}</p>
                                </th>
                                <td>
                                    <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                        <div class="dAdmin_profile_name">
                                            <h4>{{ ucfirst($user->name) }}</h4>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-250px">
                                        <p>{{ $user->email }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-250px">
                                    <p>   {{ get_phrase(ucfirst($user['address'])) }} </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="adminTable-action">
                                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ get_phrase('Actions') }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('hr.user_lists_user_edit', ['id' => $user->id]) }}', '{{ get_phrase('Update user') }}')">{{ get_phrase('Edit') }}</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('delete_user({{ $user->id }})', 'ajax_delete')">{{ get_phrase('Delete') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="empty_box text-center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    <span class="">{{ get_phrase('No data found') }}</span>
</div>
@endif

<script>
"use strict";
function delete_user(id) {
    let delete_user_id = id;
    var url = '{{ route("hr.user_lists_user_delete", ":id") }}';
    url = url.replace(':id', delete_user_id);

    $.ajax({
        url: url,
        type: "GET",
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload();
            $("#confirmSweetAlerts").modal('hide');
            toastr.success("Deleted Successfully");
        }
    });
}
</script>
@endsection
