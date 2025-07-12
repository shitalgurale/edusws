<?php
use App\Models\Addon\Hr_user_list;
?>


@extends($roleName)

@section('content')






<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Payslip Details') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('Payroll') }}</a></li>
            </ul>
          </div>

        </div>
      </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="eSection-wrap human_resource_content">
            
            @if(!empty($payroll))

            <table class="table eTable eTable-2">
                <thead>
                <tr>
                    <th scope="col">#</th>

                    <th scope="col">{{ get_phrase('User') }}</th>
                    <th scope="col">{{ get_phrase('Summary') }}</th>

                    <th scope="col">{{ get_phrase('') }}</th>
                    <th scope="col">{{ get_phrase('Date') }}</th>


                    <th scope="col">{{ get_phrase('Status') }}</th>
                    <th scope="col" class="text-center">{{ get_phrase('Option') }}</th>


                </thead>
                <tbody>
                    <?php
                    $count = 1;

                    foreach($payroll as $row): ?>
                    <tr>
                        <td>
                            {{ $count++ }}
                        </td>
                        <td>
                            <?php
                                $user =Hr_user_list::where( array('id' =>  $row['user_id'],'school_id' => $row['school_id']))->first();?>

                            <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                <div class="dAdmin_profile_name">
                                    <h4> {{ get_phrase(ucfirst($user->name)) }}</h4>

                                </div>
                            </div>
                        </td>
                        <td>
                            <?php

                                $net_salary = $user->joining_salary+$row['allowances'] - $row['deducition'];
                                ?>

                            <div class="dAdmin_info_name min-w-150px">

                                <p><span>{{ get_phrase('Net Salary') }}: </span> </p>


                            </div>

                        </td>
                        <td>

                            <div class="dAdmin_info_name min-w-150px">

                                <p> {{ $net_salary }}</p>


                            </div>

                        </td>
                        <td>
                            <?php
                                $date = date('M-Y', (int)$row['created_at']);


                                ?>

                            <div class="dAdmin_info_name min-w-150px">
                                <p>  {{ $date }}</p>

                            </div>
                        </td>

                        <td >
                            <div class="dAdmin_info_name min-w-150px">
                                @if($row['status'] == 1)
                                    <span class="eBadge eBadge-pill ebg-soft-success ">{{ get_phrase('Paid') }}</span>
                                @else
                                    <span class="eBadge eBadge-pill ebg-soft-danger ">{{ get_phrase('Unpaid') }}</span>
                                @endif
                            </div>
                        </td>

                        
                        <div class="dAdmin_info_name min-w-150px">
                            <td>
                                 <div class="adminTable-action">
                                 <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ get_phrase('Actions') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">

                                    <a href="#" class="dropdown-item" onclick="largeModal('{{ route('hr.payslip',['id'=>$row['id']]) }}', '{{ get_phrase('payslip details') }}');">
                                        <i class="mdi mdi-eye"></i>
                                        {{ get_phrase('View payslip details') }}



                                    </a>
                                        <li>
                                            <a href="{{ route('hr.print_invoice',['id'=>$row['id']]) }}" target="_blank" class="dropdown-item">
                                                <i class="mdi mdi-printer"> </i>
                                                {{ get_phrase('Print invoice') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
</div>





@endsection
