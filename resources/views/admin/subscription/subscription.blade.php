<?php use App\Models\Package; ?>

@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Subscription') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Back Office') }}</a></li>
                        <li><a href="#">{{ get_phrase('Subscription') }}</a></li>
                    </ul>
                </div>
                @if(get_settings('frontend_view') == '1')
	            <div class="export-btn-area">
	            	<a href="{{ route('landingPage') }}" target="_blank" class="export_btn"><?php echo get_phrase('Visit Website'); ?></a>
	            </div>
	            @endif
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
	<div class="col-12">
		<div class="eSection-wrap">
			<div class="title">
                <h3>{{ get_phrase('Current Plan') }}</h3>
            </div>
			<div class="mainSection-title">
				@if($subscription_details->get()->count() > 0)
					<?php $subscription_details = $subscription_details->first(); ?>
					<div>
						<p>
		                	<span class="float-left">
		                		<h4>{{ $package_details['name'] }}</h4>
		                	</span>
						</p>
						<span class="interval">
		                	<?php if($package_details['interval'] == 'Days'): ?>
		                		{{ $package_details['days'].' '.$package_details['interval'] }}
		                	<?php else: ?>
		                		{{ $package_details['interval'] }}
		                	<?php endif; ?>
		                </span>


						<hr>

						<div class="body row">
							@if($subscription_details->status == 1)
								<span>
									{{ get_phrase('Subscription Renew Date') }}
								</span>
								<?php $renew_date = $subscription_details->expire_date + 24*60*60; ?>
								<span>
									{{ date('d M, Y', $renew_date) }}
								</span>
								<br>
								<br>
								<span>
									{{ get_phrase('Amount To Be Charged') }}
								</span>
								<span>
									{{ currency($package_details['price']) }}
								</span>
								<br>
								<br>
								@if($if_pending_payment>0)
									<div>
										<span class="float-left">
					                		<h4>{{ get_phrase('Pending Payment') }}</h4>
					                	</span>

										<hr>

										<div class="body row">
											<span>
												{{ get_phrase('You payment request has been sent to Superadmin ') }}
											</span>

										</div>
									</div>
								@else
									<?php 
									$today = date("Y-m-d");
									$today_time = strtotime($today);
									$expiry_status = (int)$subscription_details->expire_date < $today_time;
									?>
									@if($expiry_status)
									<div class="col-md-3">
										<a href="{{ route('admin.subscription.purchase') }}" class="btn btn-outline-primary float-left" data-bs-toggle="tooltip">{{ get_phrase('Subscribe') }}</a>
									</div>
									@endif
								@endif
							@else
								<span>
									{{ get_phrase('Your subscription has been expired. Renew Subscription.') }}
								</span>
								<br>
								<br>
								<div class="col-md-3">
									<a href="{{ route('admin.subscription.purchase') }}" class="btn btn-outline-primary float-left" data-bs-toggle="tooltip">{{ get_phrase('Subscribe') }}</a>
								</div>
							@endif
						</div>
					</div>
				@else

                    @if($if_pending_payment>0)
                    <div>
						<span class="float-left">
	                		<h4>{{ get_phrase('Pending Payment') }}</h4>
	                	</span>

						<hr>

						<div class="body row">
							<span>
								{{ get_phrase('You payment request has been sent to Superadmin ') }}
							</span>

						</div>
					</div>


                    @else

                    <div>
						<span class="float-left">
	                		<h4>{{ get_phrase('Not Subscribed') }}</h4>
	                	</span>

						<hr>

						<div class="body row">
							<span>
								{{ get_phrase('You are not subscribed to any plan. Subscribe now.') }}
							</span>
							<br>
							<br>
							<div class="col-md-3">
								<a href="{{ route('admin.subscription.purchase') }}" class="btn btn-outline-primary float-left" data-bs-toggle="tooltip">{{ get_phrase('Subscribe') }}</a>
							</div>
						</div>
					</div>


                    @endif

				@endif
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
        	<div class="title">
                <h3>{{ get_phrase('Subscription Report') }}</h3>
            </div>
            <div class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
              <form method="GET" action="{{ route('admin.subscription', ['date_form' => $date_from, 'date_to' => $date_to]) }}">
                <div
                  class="row"
                >
                    <div class="col-xl-8">
                        <input type="text" class="form-control eForm-control" name="eDateRange"
                            value="{{ date('m/d/Y', $date_from).' - '.date('m/d/Y', $date_to) }}" />
                    </div>

                    <div class="col-xl-2">
                        <button type="submit" class="eBtn eBtn btn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
              </form>
              <!-- Export Button -->
              @if(count($subscriptions) > 0)
              <div class="position-relative">
                <button
                  class="eBtn-3 dropdown-toggle"
                  type="button"
                  id="defaultDropdown"
                  data-bs-toggle="dropdown"
                  data-bs-auto-close="true"
                  aria-expanded="false"
                >
                  <span class="pr-10">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="12.31"
                      height="10.77"
                      viewBox="0 0 10.771 12.31"
                    >
                      <path
                        id="arrow-right-from-bracket-solid"
                        d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                        transform="translate(0 12.31) rotate(-90)"
                        fill="#00a3ff"
                      />
                    </svg>
                  </span>
                  {{ get_phrase('Export') }}
                </button>
                <ul
                  class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                >
                    <li>
                        <a class="dropdown-item" id="pdf" href="javascript:;" onclick="generatePDF()">{{ get_phrase('PDF') }}</a>
                    </li>
                    <li>
                        <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('subscription_report')">{{ get_phrase('Print') }}</a>
                    </li>
                </ul>
              </div>
              @endif
            </div>
        	@if(count($subscriptions) > 0)
        	<div class="table-responsive subscription_report" id="subscription_report">
				<table class="table eTable">
					<thead>
	                    <th>#</th>
	                    <th>{{ get_phrase('Package') }}</th>
	                    <th>{{ get_phrase('Price') }}</th>
	                    <th>{{ get_phrase('Paid By') }}</th>
	                    <th>{{ get_phrase('Purchase Date') }}</th>
	                    <th>{{ get_phrase('Expire Date') }}</th>
	                </thead>
	                <tbody>
	                	@foreach($subscriptions as $subscription)
	                		<?php
	                		$package = Package::find($subscription->package_id);
	                		?>
	                		<tr>
	                			<td>{{ $loop->index + 1 }}</td>
	                			<td><strong>{{ $package->name }}</strong></td>
	                			<td>{{ currency($subscription->paid_amount) }}</td>
	                			<td>{{ $subscription->payment_method }}</td>
	                			<td>{{ date('d-M-Y', $subscription->date_added) }}</td>
	                			<td>{{ date('d-M-Y', $subscription->expire_date) }}</td>
	                		</tr>
	                	@endforeach
	                </tbody>
				</table>
			</div>
			@else
			<div class="empty_box center">
                <img class="mb-3" width="150px" src="{{ asset('public/assets/images/empty_box.png') }}" />
                <br>
                {{ get_phrase('No data found') }}
            </div>
			@endif
		</div>
	</div>
</div>

<script type="text/javascript">

  "use strict";

	function generatePDF() {

    	// Choose the element that our invoice is rendered in.
	    const element = document.getElementById("subscription_report");

	    // clone the element
	    var clonedElement = element.cloneNode(true);

	    // change display of cloned element
	    $(clonedElement).css("display", "block");

	    // Choose the clonedElement and save the PDF for our user.

		var opt = {
		  margin:       1,
		  filename:     'subscription_report.pdf',
		  image:        { type: 'jpeg', quality: 0.98 },
		  html2canvas:  { scale: 2 }
		};

		// New Promise-based usage:
		html2pdf().set(opt).from(clonedElement).save();

	    // remove cloned element
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
