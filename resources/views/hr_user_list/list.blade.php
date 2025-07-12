@if (isset($role_id))
<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <!-- Table -->
      <div class="table-responsive">
        @if (count($users) > 0)
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">{{ get_phrase('Name') }}</th>
                <th scope="col">{{ get_phrase('Email') }}</th>
                <th scope="col">{{ get_phrase('Address') }}</th>
                <th scope="col">{{ get_phrase('Options') }}</th>


            </thead>
            <tbody>
                <?php $index=1; ?>
                  @foreach($users as $key => $user)
                  <tr>
                    <th scope="row">
                      <p class="row-number">{{ $index++ }}</p>
                    </th>
                    <td>
                      <div
                        class="dAdmin_profile d-flex align-items-center min-w-200px"
                      >

                        <div class="dAdmin_profile_name">
                          <h4> {{ get_phrase(ucfirst($user['name'])) }}</h4>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="dAdmin_info_name min-w-250px">
                        <p>   {{ get_phrase(ucfirst($user['email'])) }} </p>
                      </div>
                    </td>

                     <td>
                      <div class="dAdmin_info_name min-w-250px">
                        <p>   {{ get_phrase(ucfirst($user['address'])) }} </p>
                      </div>
                    </td>


                    <td>

                      <div class="adminTable-action">
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
                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('hr.user_lists_user_edit', ['id' => $user['id']]) }}', '{{ get_phrase('Update user') }}')">{{ get_phrase('Edit') }}</a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('delete_user({{ $user['id'] }})', 'ajax_delete' )">{{ get_phrase('Delete') }}</a>
                          </li>
                        </ul>
                      </div>
                    </td>


                  </tr>
            @endforeach
            </tbody>
          </table>
        @else
          <div class="empty_box text-center">
            <img class="mb-3 " width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
            <br>
            <span class="">
              {{ get_phrase('No data found') }}
            </span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@else
  {{ get_phrase("Role id not set") }}
@endif

<script>
  "use strict";

  function delete_user(id) {
    let delete_user_id= id;


    var url = '{{ route("hr.user_lists_user_delete", ":id") }}';
    url = url.replace(':id', delete_user_id );

    $.ajax({
        url:url,
        type: "GET",
        contentType: false,
        processData: false,
        success : function(data){
            filter_user();



           $("#confirmSweetAlerts").modal('hide');
                toastr.success("Deleted Successfully");


        }

    });


  }

</script>
