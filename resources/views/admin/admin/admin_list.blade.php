@extends('admin.navigation')

@section('content')

<?php use App\Models\School; ?>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Admins') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Users') }}</a></li>
                        <li><a href="#">{{ get_phrase('Admin') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.open_modal') }}', 'Create Admin')">{{ get_phrase('Create Admin') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Admin area -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            @if(count($admins) > 0)
            <!-- Table -->
            <div class="table-responsive">
                <table class="table eTable eTable-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ get_phrase('Name') }}</th>
                            <th scope="col">{{ get_phrase('Email') }}</th>
                            <th scope="col">{{ get_phrase('User Info') }}</th>
                            <th scope="col">{{ get_phrase('Account Status') }}</th>
                            <th scope="col">{{ get_phrase('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $key => $admin)
                        <?php 
                        $info = json_decode($admin->user_information);
                        
                        // Check if the image exists, if not, use the default image
                        $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
                    ?>
                        <tr>
                            <th scope="row">
                                <p class="row-number">{{ $admins->firstItem() + $key }}</p>
                            </th>
                            <td>
                          <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="{{ asset('assets') }}/{{ $user_image }}"
                              />
                            </div>
                            <div class="dAdmin_profile_name">
                              <h4>{{ $admin->name }}</h4>
                            </div>
                          </div>
                        </td>                            <td>
                                <div class="dAdmin_info_name min-w-250px">
                                    <p>{{ $admin->email }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="dAdmin_info_name min-w-250px">
                                    <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone }}</p>
                                    <p><span>{{ get_phrase('Address') }}:</span> {{ $info->address }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="dAdmin_info_name min-w-100px">
                                    @if(!empty($admin->account_status == 'disable'))
                                    <span class="eBadge ebg-soft-danger">{{ get_phrase('Disabled') }}</span>
                                    @else
                                    <span class="eBadge ebg-soft-success">{{ get_phrase('Enable') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="adminTable-action">
                                    <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown">
                                        {{ get_phrase('Actions') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.open_edit_modal', ['id' => $admin->id]) }}', '{{ get_phrase('Edit Admin') }}')">{{ get_phrase('Edit') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.admin.delete', ['id' => $admin->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
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
            <div class="empty_box center">
                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                <br>
                <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    
    function Export() {
        const element = document.getElementById("admin_list");
        var clonedElement = element.cloneNode(true);
        $(clonedElement).css("display", "block");

        var opt = {
            margin: 1,
            filename: 'admin_list_{{ date("y-m-d") }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 }
        };

        html2pdf().set(opt).from(clonedElement).save();
        clonedElement.remove();
    }

    function printableDiv(printableAreaDivId) {
        var printContents = document.getElementById(printableAreaDivId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

@endsection
