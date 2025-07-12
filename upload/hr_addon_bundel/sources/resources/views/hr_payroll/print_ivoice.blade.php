<?php
use App\Models\User;
use App\Models\Addon\Hr_user_list;
?>

@extends($roleName)

@section('content')


 <?php foreach ($payslip as $key=>$row):

  $user = Hr_user_list::where('id',$row['user_id'])->where('school_id',$row['school_id'])->first()->toArray();

  $date = date('M,Y', $row['created_at']);

?>


<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div
        class="d-flex justify-content-between align-items-center flex-wrap gr-15"
      >
        <div class="d-flex flex-column">
          <h4> {{ get_phrase('Print payslip') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
            <li><a href="#">{{ get_phrase('Payroll') }}</a></li>
            <li><a href="#">{{ get_phrase('Print invoice') }}</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Start Admin area -->
<div class="row" id="print_area">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <!-- Invoice Head -->
      <div class="row justify-content-between flex-wrap">
        <div class="col-auto">
          <img
         src="{{ asset('assets/images/invoice_logo.png') }}"
            alt=""
            class="invoice_logo"
          />
        </div>
        <div class="col-auto">
          <img
          src="{{ asset('assets/images/invoice_logo.png') }}"
            alt=""
            class="invoice_logo"
          />
        </div>
      </div>
      <!-- Invoice Info -->
      <div
        class="row flex-wrap justify-content-md-between align-items-start"
      >
        <div class="col-4">
          <div class="invoice_details">
            <h4 class="invoice_title">{{ get_phrase('INVOICE') }}</h4>
          </div>
        </div>
        <div class="col-8">
          <div class="invoice_details text-end">
            <div class="item">
              <p class="sub-title">{{ get_phrase('Employee') }}</p>
              <div class="title"> {{ $user['name'] }}</div>
            </div>
            <div class="item">
              <p class="sub-title">{{ get_phrase('Date') }}</p>
              <div class="title">{{ $date }}</div>
            </div>
            @if($row['status'] == 0)
            <p class="item">
             <span class="eBadge ebg-soft-danger">{{ get_phrase('Unpaid') }}</span>
            </p>
        @else
        <p class="item">
         <span class="eBadge ebg-soft-success">{{ get_phrase('Paid') }}</span>
        </p>
        @endif
          </div>
        </div>
      </div>
      <!-- Invoice Summary -->
      <div class="invoice_summary d-flex flex-column">
        <div class="invoice_summary_item">
          <div class="summary_title d-flex align-items-center">
            <span class="summary_title_icon allowance_icon"></span>
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
            <span class="summary_title_icon deduction_icon"></span>
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
            <span class="summary_title_icon payslip_icon"></span>
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
                        <p>{{ $user['joining_salary'] }}</p>
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
      <a href="#" onclick="printDiv()" class="print_invoice_btn">
        <span>{{ get_phrase('Print Invoice') }}</span>
      </a>
    </div>
  </div>
</div>
<!-- End Admin area -->

<?php endforeach; ?>

 <script>
    "use strict";
        function printDiv() {

            var printContents = document.getElementById('print_area').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
 </script>

@endsection
