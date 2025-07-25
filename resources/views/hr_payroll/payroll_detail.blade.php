<?php
use App\Models\User;
use App\Models\Addon\Hr_user_list;

if(empty($list_of_payrolls)) { ?>

    <div class="empty_box text-center">
        <img class="mb-3 " width="150px" src="{{ asset('assets/images/empty_box.png') }}" />

        <br>
        <span class="">
            {{ get_phrase('No data found') }}
         </span>
    </div>

<?php } else { ?>

    <div class="table-responsive border-top">
        <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th scope="col">{{ get_phrase('ID') }}</th>
                <th scope="col">{{ get_phrase('USER') }}</th>
                <th scope="col" colspan="2">{{ get_phrase('SUMMARY') }}</th>
                <th scope="col">{{ get_phrase('DATE') }}</th>
                <th scope="col">{{ get_phrase('STATUS') }}</th>
                <th scope="col">{{ get_phrase('OPTIONS') }}</th>
              </tr>
                    </thead>
        <tbody>
            <?php

            foreach($list_of_payrolls as $row): ?>
            <tr>

            <th scope="row">
                <p class="row-number">   {{ $row['id'] }}</p>
            </th>

            <td>
                <?php $user =Hr_user_list::where('id', $row['user_id'])->first();  ?>
                <div class="dAdmin_info_name min-w-100px">
                    <p><span> {{ $user->name }} </span></p>
                  </div>
            </td>

            <td>
                <div class="dAdmin_info_name min-w-100px">
                  <p>{{ get_phrase('Net salary') }}:</p>
                </div>
              </td>


              <td>
                <?php
                $basic_salary = Hr_user_list::where('id', $row['user_id'])->pluck('joining_salary')->toArray();
                $net_salary = $basic_salary[0]+$row['allowances'] - $row['deducition'];

                ?>

                <div class="dAdmin_info_name">
                  <div class="dAdmin_info_name min-w-100px">
                    <p> {{ $net_salary }} </p>
                  </div>
                </div>
              </td>


            <td>
              <div class="dAdmin_info_name min-w-100px">
                <p>{{ date('M,Y', $row['created_at']) }}</p>
              </div>
            </td>
            <td >

                    @if($row['status'] == 1)
                    <span class="eBadge ebg-soft-success">{{ get_phrase('Paid') }}</span>
                    @else
                    <span class="eBadge ebg-soft-red">{{ get_phrase('Unpaid') }}</span>
                    @endif

            </td>
            <td>
                <div class="adminTable-action">
                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown"  aria-expanded="false">
                    {{ get_phrase('Action') }} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action" >

                    <li>
                    <a href="#" class="dropdown-item" onclick="largeModal('{{ route('hr.payslip',['id'=>$row['id']]) }}', '{{ get_phrase('Payslip details') }}');">
                        <i class="mdi mdi-eye"></i>
                        {{ get_phrase('view payslip details') }}



                    </a>
                    </li>
                    <li>
                    <a href="{{ route('hr.print_invoice',['id'=>$row['id']]) }}" target="_blank" class="dropdown-item">
                        <i class="mdi mdi-printer"> </i>
                        {{ get_phrase('Print invoice') }}
                    </a>
                    </li>

                    <?php if($row['status'] == 0) { ?>
                    <li>
                    <a class="dropdown-item" href="{{ route('hr.update_payroll_status',['id'=>$row['id'],'after_update_date'=>$row['created_at']]) }}">
                        <i class="mdi mdi-target"></i>
                        {{ get_phrase('Mark as paid') }}
                    </a>
                    </li>



                    <?php } ?>
                </ul>
                </div>

      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</div>


<?php } ?>

