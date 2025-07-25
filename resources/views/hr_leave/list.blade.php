<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_user_list;
use App\Models\Addon\Hr_roles;


?>

@extends($roleName)

@section('content')

<style>
    .eTable-2 > :not(caption) > * > * {
  border-bottom: 1px dashed #dedede;
  padding: 20px 8px !important;
}
</style>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Leave Lists') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('Leave List') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            @if(auth()->user()->role_id == 2)

                <a href="javascript:;" class="export_btn float-end m-1"onclick="rightModal('{{ route('hr.show_leave_request_modal_for_admin') }}','{{ get_phrase('Crete new leave') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Create New Leave') }}</a>

            @elseif(auth()->user()->role_id == 3)

                <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('{{ route('hr.show_leave_request_modal') }}','{{ get_phrase('Add Leave') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Add New Leave') }}</a>

            @elseif(auth()->user()->role_id == 4)
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('{{ route('hr.show_leave_request_modal') }}','{{ get_phrase('Add Leave') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Add New Leave') }}</a>

            @elseif(auth()->user()->role_id == 5)
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('{{ route('hr.show_leave_request_modal') }}','{{ get_phrase('Add Leave') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Add New Leave') }}</a>

            @endif


          </div>
        </div>
      </div>
    </div>
</div>



<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <form class="w-100" action="{{ route('hr.list_of_leaves',['from' => 'sdd','to'=>'sdad','type'=>'dasd']) }}" method="get">


                <div class="row justify-content-md-center">

                    <div class="col-md-2">
                    </div>
                    @if(auth()->user()->role_id == 2)
                    <div class="col-md-2">
                        <div class="form-group">


                            <select name="role_id" id="role_id" class="form-select eForm-control" aria-label="Default select example" onchange="fetch_type(this.value)">
                                <option value="">
                                    {{ get_phrase('Select a role') }}
                                </option>
                                <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                                <?php foreach ($roles as $role): ?>
                                <option value="{{ $role['id'] }}" {{ $hr_searched_role_id == $role['id'] ? 'selected':'' }}>
                                    {{ get_phrase(ucfirst($role['name'])) }}
                                </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <input type="text" id="datetimes" class="form-select eForm-control" aria-label="Default select example" name="datetimes" />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-block btn-secondary" id="submit-button" onclick="update_date_range();">
                            {{ get_phrase('Filter') }}
                        </button>
                    </div>

                </div>

                <br>



            </form>



            <ul class="nav nav-tabs eNav-Tabs-custom"id="myTab"role="tablist" >

                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="pending-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#pendingtable"
                    type="button"
                    role="tab"
                    aria-controls="pendingtable"
                    aria-selected="false"
                  >
                  {{ get_phrase('Pending ') }}<p class="badge bg-warning ">
                    {{ count($list_of_pending_leaves) }}
                </p>
                    <span></span>
                  </button>
                </li>


                <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="approve-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#approvetable"
                      type="button"
                      role="tab"
                      aria-controls="approvetable"
                      aria-selected="false"
                    >
                    {{ get_phrase('Approve ') }}<p class="badge bg-success ">
                      {{ count($list_of_approve_leaves) }}
                  </p>
                      <span></span>
                    </button>
                  </li>


                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="decline-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#declinetable"
                      type="button"
                      role="tab"
                      aria-controls="declinetable"
                      aria-selected="false"
                    >
                    {{ get_phrase('Decline ') }}<p class="badge bg-danger ">
                      {{ count($list_of_decline_leaves) }}
                  </p>
                      <span></span>
                    </button>
                  </li>


              </ul>


            <div class="tab-content pb-2" id="nav-tabContent">
                <div class="tab-pane fade show active" id="pendingtable" role="tabpanel" aria-labelledby="pending-tab">

                    <div class="eForm-layouts">
                       @if (count($list_of_pending_leaves) > 0 )
                       <?php $list_of_pending_leaves=$list_of_pending_leaves->toArray();  ?>

                      <table class="table eTable eTable-2">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            @if(auth()->user()->role_id == 2)
                            <th scope="col">{{ get_phrase('Employee') }}</th>
                            <th scope="col">{{ get_phrase('Role') }}</th>
                            @endif
                            <th scope="col">{{ get_phrase('Start date') }}</th>
                            <th scope="col">{{ get_phrase('End date') }}</th>
                            <th scope="col">{{ get_phrase('Reason') }}</th>
                            
                            <th scope="col" class="text-center">{{ get_phrase('Option') }}</th>

                        </thead>
                          <tbody>
                              <?php foreach($list_of_pending_leaves as $key => $leave): ?>
                              <tr>
                                  <td>
                                      {{ $key+1 }}
                                  </td>

                                  @if(auth()->user()->role_id == 2)

                                  <td>
                                      <?php  $name=Hr_user_list::find($leave['user_id']); ?>

                                    <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                    <div class="dAdmin_profile_name">
                                        <h4> {{ get_phrase(ucfirst($name->name??"")) }}</h4>

                                    </div>
                                  </div>


                                 </td>
                                  <td>
                                      <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                               if(!empty($r))
                                               {
                                                $r=$r->toArray();
                                               }
                                                ?>

                                            <div class="dAdmin_info_name min-w-150px">
                                                <p>{{  $r['name']??"" }}</p>

                                            </div>
                                  </td>

                                  @endif

                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p>  {{ date('d/m/Y', $leave['start_date']) }}</p>

                                    </div>


                                  </td>
                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> {{ date('d/m/Y', $leave['end_date']) }}</p>

                                    </div>

                                  </td>
                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> {{ substr($leave['reason'], 0, 50) . '...' }}</p>

                                    </div>


                                  </td>
                              

                                 

                                    <div class="dAdmin_info_name min-w-150px">

                                         <td>
                                      @if(auth()->user()->role_id==2)

                                      <div class="adminTable-action">

                                         
                                          <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                              {{ get_phrase('Actions') }}
                                          </button>

                                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                              <!-- item-->

                                              @if($leave['status']==0 || $leave['status']==2)

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve']) }}', 'undefined');"> {{ get_phrase('Approve') }}</a>
                                              </li>

                                              @endif

                                              @if($leave['status']==1 || $leave['status']==0)

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline']) }}', 'undefined');"> {{ get_phrase('Decline') }}</a>
                                              </li>


                                              @endif

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete']) }}', 'undefined');"> {{ get_phrase('Delete') }}</a>
                                              </li>


                                            </ul>
                                      </div>

                                      @else
                                      <div class="adminTable-action">
                                          <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false" @if($leave['status']==1 || $leave['status']==2) disabled @endif>
                                            {{ get_phrase('Actions') }}
                                          </button>

                                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                              <!-- item-->

                                              <li>
                                                <a class="dropdown-item" href="javascript:;"
                                                 onclick="rightModal('{{ route('hr.show_leave_update_request_modal', ['id' => $leave['id']]) }}', '{{ get_phrase('Edit Leave') }}')">
                                                  <i class="mdi mdi-cancel"></i>
                                                  {{ get_phrase('Edit') }}</a>
                                              </li>

                                              <li>
                                                <a class="dropdown-item" href="javascript:;"
                                                onclick="confirmModal('{{ route('hr.delete_leave_request', ['id'=>$leave['id']]) }}', 'undefined');">
                                                  {{ get_phrase('Delete') }}</a>
                                              </li>



                                          </div>
                                        </ul>

                                      @endif
                                      </td>
                                    </div>
                               </tr>
                              <?php endforeach; ?>
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




                <div class="tab-pane fade show " id="approvetable" role="tabpanel" aria-labelledby="approve-tab">

                    <div class="eForm-layouts">
                      @if (count($list_of_approve_leaves) > 0 )
                       <?php   $list_of_approve_leaves=$list_of_approve_leaves->toArray();?>


                      <table class="table eTable eTable-2">
                      <thead>
                        <tr>
                            <th scope="col">#</th>
                            @if(auth()->user()->role_id == 2)
                            <th scope="col">{{ get_phrase('Employee') }}</th>
                            <th scope="col">{{ get_phrase('Role') }}</th>
                            @endif
                            <th scope="col">{{ get_phrase('Start date') }}</th>
                            <th scope="col">{{ get_phrase('End date') }}</th>
                            <th scope="col">{{ get_phrase('Reason') }}</th>
                            @if(auth()->user()->role_id == 2)
                            <th scope="col" class="text-center" >{{ get_phrase('Option') }}</th>
                            @endif

                            </thead>
                            <tbody>
                                <?php foreach($list_of_approve_leaves as $key => $leave): ?>
                                <tr>
                                    <td>
                                        {{ $key+1 }}
                                    </td>

                                    @if(auth()->user()->role_id == 2)

                                    <td>
                                        <?php $name=Hr_user_list::find($leave['user_id']);?>
                                            <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                                <div class="dAdmin_profile_name">
                                                    <h4> {{ get_phrase(ucfirst($name->name??"")) }}</h4>

                                                </div>
                                            </div>
                                    </td>
                                    <td>
                                        <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                                if(!empty($r))
                                                {
                                                $r=$r->toArray();
                                                }
                                                ?>

                                            <div class="dAdmin_info_name min-w-150px">
                                                <p>{{  $r['name']??"" }}</p>

                                            </div>
                                    </td>

                                    @endif

                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p>  {{ date('d/m/Y', $leave['start_date']) }}</p>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p> {{ date('d/m/Y', $leave['end_date']) }}</p>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p> {{ substr($leave['reason'], 0, 50) . '...'  }}</p>

                                        </div>
                                    </td>

                                    
                                    <div class="dAdmin_info_name min-w-150px">
                                        <td>
                                        @if(auth()->user()->role_id==2)

                                        <div class="adminTable-action">
                                          
                                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ get_phrase('Actions') }}
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                <!-- item-->

                                                @if($leave['status']==0 || $leave['status']==2)
                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve']) }}', 'undefined');"> {{ get_phrase('Approve') }}</a>
                                                </li>
                                                @endif

                                                @if($leave['status']==1 || $leave['status']==0)

                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline']) }}', 'undefined');"> {{ get_phrase('Decline') }}</a>
                                                </li>
                                                @endif

                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete']) }}', 'undefined');"> {{ get_phrase('Delete') }}</a>
                                                </li>



                                            </ul>
                                        </div>


                                        @endif
                                        </td>
                                        </div>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        @else
                        <div class="empty_box text-center">
                            <img class="mb-3 " width="150px" src="{{ asset('assets/images/empty_box.png') }}" />

                            <br>
                            <span class="">
                               {{ get_phrase(' No data found') }}
                             </span>
                        </div>

                       @endif
                     </div>


                </div>

                <div class="tab-pane fade show " id="declinetable" role="tabpanel" aria-labelledby="decline-tab">

                    <div class="eForm-layouts">

                        @if (count($list_of_decline_leaves) > 0 )
                        <?php $list_of_decline_leaves=$list_of_decline_leaves->toArray(); ?>


                    <table class="table eTable eTable-2">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            @if(auth()->user()->role_id == 2)
                            <th scope="col">{{ get_phrase('Employee') }}</th>
                            <th scope="col">{{ get_phrase('Role') }}</th>
                            @endif
                            <th scope="col">{{ get_phrase('Start date') }}</th>
                            <th scope="col">{{ get_phrase('End date') }}</th>
                            <th scope="col">{{ get_phrase('Reason') }}</th>
                         
                            @if(auth()->user()->role_id == 2)
                            <th scope="col" class="text-center">{{ get_phrase('Option') }}</th>
                            @endif

                            </thead>
                         <tbody>
                            <?php foreach($list_of_decline_leaves as $key => $leave):?>
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>

                                @if(auth()->user()->role_id == 2)

                                <td>
                                    <?php $name=Hr_user_list::find($leave['user_id']);
                                       ?>

                                    <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                        <div class="dAdmin_profile_name">
                                            <h4> {{ get_phrase(ucfirst($name->name??"")) }}</h4>

                                        </div>
                                      </div>

                                </td>
                                <td>
                                    <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                             if(!empty($r))
                                             {
                                              $r=$r->toArray();
                                             }
                                              ?>

                                          <div class="dAdmin_info_name min-w-150px">
                                              <p>{{  $r['name']??"" }}</p>

                                          </div>
                                </td>

                                @endif

                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p>  {{ date('d/m/Y', $leave['start_date']) }}</p>

                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> {{ date('d/m/Y', $leave['end_date']) }}</p>

                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> {{ substr($leave['reason'], 0, 50) . '...' }}</p>

                                    </div>
                                </td>
                           

                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                    @if(auth()->user()->role_id==2)

                                    <div class="dropdown text-start">
                                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ get_phrase('Actions') }}
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <!-- item-->

                                            @if($leave['status']==0 || $leave['status']==2)
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve']) }}', 'undefined');"> {{ get_phrase('Approve') }}</a>
                                              </li>
                                            @endif

                                            @if($leave['status']==1 || $leave['status']==0)
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline']) }}', 'undefined');"> {{ get_phrase('Decline') }}</a>
                                              </li>
                                            @endif

                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete']) }}', 'undefined');"> {{ get_phrase('Delete') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                    @endif

                                </td>
                            </tr>
                            <?php endforeach; ?>
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
    </div>
</div>

@endsection
