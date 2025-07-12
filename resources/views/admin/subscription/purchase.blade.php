@extends('admin.navigation')

@section('content')

<div class="mainSection-title">
	<div class="row">
	  <div class="col-12">
	    <div
	      class="d-flex justify-content-between align-items-center flex-wrap gr-15"
	    >
	      <div class="d-flex flex-column">
	        <h4>{{ get_phrase('Package List') }}</h4>
	        <ul class="d-flex align-items-center eBreadcrumb-2">
	          <li><a href="#">{{ get_phrase('Home') }}</a></li>
	          <li><a href="#">{{ get_phrase('Package List') }}</a></li>
	        </ul>
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- Start Package List -->
<div class="row">
	<div class="col-12">
	  <div class="eSection-packageList">
	    <div class="row flex-wrap">
	    	@foreach($packages as $package)
		    <div class="col-lg-3 col-md-6">
		        <div class="packageBox text-center">
		          <h4 class="packageTitle">{{ $package->name }}</h4>
		          <p class="packagePrice">
		          	<span>{{ currency($package->price) }}</span>/
		          	<?php if($package['interval'] == 'Days'): ?>
	            		{{ $package['days'].' '.$package['interval'] }}
	            	<?php else: ?>
	            		{{ $package['interval'] }}
	            	<?php endif; ?>
		          </p>
		          <ul class="packageFeatures">
		            <li class="mainFeature">{{ $package->description }}</li>
		          </ul>
		          <a href="{{ route('admin.subscription.payment',['package_id'=>$package['id'] ]) }}" class="packageSubs_btn">{{ get_phrase('Subscribe') }}</a>
		        </div>
		    </div>
		    @endforeach 
	    </div>
	  </div>
	</div>
</div>
@endsection
