<!-- start page title -->


@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('User Roles') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('User Roles') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('hr.user_role_create') }}', '{{ get_phrase('Create Roles') }}')"> {{ get_phrase('Create Roles') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>
<?php $index=1; ?>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">

            <!-- Table -->


            <div class="table-responsive">
               <?php if (count($roles) > 0): ?>
                    <table class="table eTable eTable-2">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ get_phrase('Role') }}</th>
                            <th scope="col">{{ get_phrase('Permanent') }}</th>
                            <th scope="col">{{ get_phrase('Options') }}</th>

                        </thead>
                        <tbody>


                                @foreach ($roles as $key => $role)


                                <tr>
                                    <th scope="row">
                                    <p class="row-number">{{ $index++ }}</p>
                                    </th>
                                    <td>
                                        <div   class="dAdmin_profile d-flex align-items-center min-w-200px">

                                            <div class="dAdmin_profile_name">
                                            <h4> {{ get_phrase(ucfirst($role['name'])) }}</h4>
                                            </div>
                                        </div>
                                        </td>
                                                <td>
                                                <div class="dAdmin_info_name min-w-200px">
                                                    <p>   {{ get_phrase(ucfirst($role['permanent'])) }} </p>
                                                </div>
                                                </td>
                                    <td>
                                 <div class="dAdmin_info_name min-w-150px">
                                    <?php if($role['permanent'] != 'yes'): ?>
                                    <div class="adminTable-action float-start">
                                        <button
                                        type="button"
                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        >
                                        {{ get_phrase('Actions') }}
                                        </button>
                                        <ul
                                        class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                                        >
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('hr.user_role_edit',['id' => $role['id']]) }}', '{{ get_phrase('Update role') }}');">{{ get_phrase('Edit') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.user_role_detele', ['id' => $role['id']]) }}', 'undefined')">{{ get_phrase('Delete') }}</a>
                                        </li>
                                        </ul>
                                    </div>
                                    <?php else: ?>


                                        <div class="dAdmin_info_name min-w-150px">
                                            <p><span class="eBadge eBadge-pill ebg-soft-dark">{{ get_phrase('Not Editable') }}</span></p>
                                        </div>


                                    <?php endif; ?>
                                </div>
                                    </td>


                                </tr>
                             @endforeach
                        </tbody>
                    </table>
           <?php else: ?>
          {{ "no data found " }}
          <?php endif; ?>
            </div>
        </div>
    </div>
</div>


@endsection
