<?php
use App\Models\User;
use App\Models\Addon\Hr_user_list;


?>

@foreach ($payslip as $key=>$row)

   <?php $user = Hr_user_list::where('id',$row['user_id'])->where('school_id',$row['school_id'])->first()->toArray();

      $date = date('M,Y', $row['created_at']);

    ?>

    <div class="modal-body payslip_modal_body" id="payroll_print">
        <div class="payslip_user_summary text-center">
          <h4 class="item">
            {{ get_phrase('Employee') }} :
            <span class="inner-item">  {{ $user['name']??"" }}</span>
          </h4>
          <p class="item">
            {{ get_phrase('Date') }} : <span class="inner-item">  {{ $date }}</span>
          </p>

          @if($row['status'] == 0)
          <p class="item">
            {{ get_phrase('Status') }} : <span class="eBadge ebg-soft-danger">{{ get_phrase('Unpaid') }}</span>
          </p>
      @else
      <p class="item">
        {{ get_phrase('Status') }} : <span class="eBadge ebg-soft-success">{{ get_phrase('Paid') }}</span>
      </p>
      @endif

        </div>
        <!-- Invoice Summary -->
        <div
          class="invoice_summary d-flex flex-column payslip_modal_body_content"
        >
          <div class="invoice_summary_item">
            <div class="summary_title d-flex align-items-center">
              <h4>{{ get_phrase('Allowance Summary') }}</h4>
            </div>
            <div class="summary_table">
              <div
                class="summary_thead d-flex justify-content-between align-items-center"
              >
                <h4>{{ get_phrase('TYPE') }}</h4>
                <h4>{{ get_phrase('AMOUNT') }}</h4>
              </div>
              <div
                class="summary_tbody d-flex justify-content-between align-items-center"
              >

                        @if($row['status'] == 0)
                        <p class="item">
                           <span class="eBadge ebg-soft-danger">{{ get_phrase('Unpaid') }}</span>
                        </p>
                    @else
                    <p class="item">
                       <span class="eBadge ebg-soft-success">{{ get_phrase('Paid') }}</span>
                    </p>
                    @endif

                <p class="amount">{{ $row['allowances'] }}</p>
              </div>
            </div>
          </div>
          <div class="invoice_summary_item">
            <div class="summary_title d-flex align-items-center">
                <h4>{{ get_phrase('Deduction Summary') }}</h4>
            </div>
            <div class="summary_table">
              <div
                class="summary_thead d-flex justify-content-between align-items-center"
              >
              <h4>{{ get_phrase('TYPE') }}</h4>
              <h4>{{ get_phrase('AMOUNT') }}</h4>
              </div>
              <div
                class="summary_tbody d-flex justify-content-between align-items-center"
              >
              @if($row['status'] == 0)
              <p class="item">
                 <span class="eBadge ebg-soft-danger">{{ get_phrase('Unpaid') }}</span>
              </p>
          @else
          <p class="item">
             <span class="eBadge ebg-soft-success">{{ get_phrase('Paid') }}</span>
          </p>
          @endif
          <p class="amount">{{ $row['deducition'] }}</p>
              </div>
            </div>
          </div>
          <div class="invoice_summary_item">
            <div class="summary_title d-flex align-items-center">
              <h4>{{ get_phrase('Payslip Summary') }}</h4>
            </div>
            <div class="salary_table">
              <div class="table-responsive">
                <table class="table eTable eTable-2">
                  <tbody>
                    <tr>
                      <td>
                        <div class="dAdmin_info_name">
                          <p>{{ get_phrase('Basic Salary') }}</p>
                        </div>
                      </td>
                      <td>
                        <div class="dAdmin_info_name">
                          <p>{{ $user['joining_salary']??"" }}</p>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="dAdmin_info_name">
                          <p>{{ get_phrase('Total Allowance') }}</p>
                        </div>
                      </td>
                      <td>
                        <div class="dAdmin_info_name">
                          <p>{{ $row['allowances'] }}</p>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="dAdmin_info_name">
                          <p>{{ get_phrase('Total Dedication') }}</p>
                        </div>
                      </td>
                      <td>
                        <div class="dAdmin_info_name">
                            <p>{{ $row['deducition'] }}</p>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="dAdmin_info_name">
                          <p><span>{{ get_phrase('Net Salary') }}</span></p>
                        </div>
                      </td>
                      <td>
                        <div class="dAdmin_info_name">
                          <p><span>{{ $user['joining_salary']+$row['allowances']-$row['deducition'] }}</span></p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


@endforeach
