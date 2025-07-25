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
            <h4>{{ get_phrase('User Lists') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('User List') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('{{ route('hr.userlist_import') }}', 'Import Users')"><i class="bi bi-plus"></i> {{ get_phrase('Import Users') }}</a>
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('{{ route('hr.create_user') }}', 'Create user')"><i class="bi bi-plus"></i> {{ get_phrase('Create New User') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>



<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <div class="expense_body">
                <div class="row mt-3">
                    <div class="col-md-3"></div>
                    <div class="col-md-4">
                        <select name="role_id" id="role_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                            <option value="">
                                {{ get_phrase('Select a role') }}
                            </option>


                            <?php foreach ($roles as $role): ?>
                            <option value="{{ $role['id'] }}">
                                {{ get_phrase(ucfirst($role['name'])) }}
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="filter_user()" class="btn btn-block btn-secondary">
                            {{ get_phrase('Filter') }}
                        </button>

                    </div>
                </div>
                <div class="card-body user_content">
                    <div class="empty_box text-center">
                        <img class="mb-3 " width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    "use strict";
    function filter_user(){
        var role_id = $('#role_id').val();
        if(role_id != ""){
            showAllUsers();
        }else{
            toastr.error('{{ get_phrase('Please select a role') }}');
        }
    }

var showAllUsers = function () {
    var role_id = $('#role_id').val();
    var url="{{ route('hr.userlist_show') }}";
    if(role_id != ""){
        $.ajax({
            url: url,
            data:{role_id:role_id},
            success: function(response){
               - $('.user_content').html(response);
            }
        });
    }
}





</script>
@if(isset($data['current_role_id']))

<script>
    filter_user()
</script>

@endif
@endsection
