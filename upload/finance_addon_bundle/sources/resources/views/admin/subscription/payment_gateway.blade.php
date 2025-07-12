<?php
use App\Models\GlobalSettings;

$active_payment_methods=GlobalSettings::where('key','paypal')
                ->orWhere('key','stripe')
                ->orWhere('key','razorpay')
                ->orWhere('key','paytm')
                ->orWhere('key','flutterwave')
                ->orWhere('key','offline')
                ->get();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ get_phrase('Payment | Ekator 8') }}</title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- all the css files -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/logo.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}">

    <!--Custom css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/swiper-bundle.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/custom.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/style.css') }}" />
    <!-- Datepicker css -->

    <link rel="stylesheet" href="{{ asset('public/assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/select2.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}">

<body>
    <?php  $off="";?>
    <div class="main_content paymentContent">
        <div class="paymentHeader d-flex justify-content-between align-items-center">
            <h5 class="title">
                {{ get_phrase('Make Payment') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.href='{{ redirect()->back()->getTargetUrl() }}'"></button>
        </div>

        <div class="paymentWrap d-flex align-items-start flex-wrap">
            <div class="paymentLeft">

                <p class="payment_tab_title pb-30">
                    {{ get_phrase('Payment Gateway') }}
                </p>
                <!-- Tab -->
                <div class="nav flex-md-column flex-row nav-pills payment_modalTab" id="v-pills-tab" role="tablist" aria-orientation="vertical">




                    @foreach($active_payment_methods as $key => $value)
                    <?php $method=json_decode($value['value'],true);?>
                    @if($method['status']==1 && addon_status('payment_gateways')==1)

                    <div class="tabItem " id="v-pills-{{ $value['key'] }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $value['key'] }}" role="tab" aria-controls="v-pills-{{ $value['key'] }}" aria-selected="true">
                        <div class="payment_gateway_option d-flex align-items-center">
                            <div class="logo">
                                @php
                                $image_logo="public/assets/images/".$value['key'].".png";
                                @endphp
                                <img src="{{ asset($image_logo) }}" alt="" />
                            </div>
                            <div class="info">
                                <p class="card_no">
                                    {{ $value['key'] }}
                                    @if($value['key'] != 'offline')
                                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                                    @endif
                                </p>

                            </div>
                        </div>
                    </div>

                    @endif

                    @endforeach

                    @if(addon_status('payment_gateways')!=1)
                    @php $off=' show active'; @endphp

                    <div
                    class="tabItem "
                    id="v-pills-offline-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#v-pills-offline"
                    role="tab"
                    aria-controls="v-pills-offline"
                    aria-selected="true"
                >
                    <div class="payment_gateway_option d-flex align-items-center">
                    <div class="logo">
                        @php
                        $image_logo="public/assets/images/offline.png";
                        @endphp
                        <img src="{{ asset($image_logo) }}"  alt="" />
                    </div>
                    <div class="info">
                        <p class="card_no">{{ get_phrase('Offline') }}<span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span></p>

                    </div>
                    </div>
                </div>



                    @endif
                    

                </div>
            </div>



            <div class="paymentRight">
                <p class="payment_tab_title pb-30">
                    {{ get_phrase('Invoice Summary') }}
                </p>
                <div class="payment_table">
                    <div class="table-responsive">
                        <table class="table eTable eTable-2">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="dAdmin_info_name">
                                            <p><span>{{ '01' }}</span></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-100px">
                                            <p>
                                                {{ $selected_package['name'] }}
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px text-end">
                                            <p>
                                                {{ $selected_package['price']." ".get_active_currency() }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px text-end">
                                            <p><span>
                                                    {{ get_phrase('Grand Total') }} :
                                                    {{ $selected_package['price']." ".get_active_currency() }}
                                                </span></p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Content -->
                <div class="tab-content payment_modalTab_content" id="v-pills-tabContent">


                    @if(addon_status('payment_gateways')==1)
                        @include('admin.subscription.paypal')
                        @include('admin.subscription.stripe')
                        @include('admin.subscription.razorpay')
                        @include('admin.subscription.paytm')
                        @include('admin.subscription.flutterwave')
                    @endif












                    <div class="tab-pane fade <?= $off; ?>" id="v-pills-offline" role="tabpanel" aria-labelledby="v-pills-offline-tab" tabindex="0">
                        <div class="off_payment_form">


                            <form action="{{ route('admin.admin_subscription_offline_payment', ['id' => $selected_package['id']]) }}" class="offline-form form" method="post" enctype="multipart/form-data">
                                @csrf
                                
                                <hr class="border mb-4">

                                <input type="hidden" id="amount" class="form-control eForm-control" name="amount" value="{{ $selected_package['price'] }}" readonly>


                                <div class="payable_document">
                                    <label for="payableDocuemnt" class="eForm-label">
                                        {{ get_phrase('Document of your payment') }} (jpg, pdf, txt, png, docx)
                                    </label>
                                    <input type="file" class="form-control eForm-control-file" id="document_image" name="document_image" required>
                                </div>

                        </div>



                        <button type="submit" class="off_payment_btn">
                            {{ get_phrase('Submit payment document') }}
                        </button>


                        <div class="offline_payment_instruction">
                            <p>
                                {{ get_phrase('Instruction') }}: {{ get_phrase('Admin will review your payment document and then approve the Payment.') }}
                            </p>
                        </div>
                        </form>





                    </div>
                </div>
            </div>
        </div>


        <script src="{{ asset('public/assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('public/assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('public/assets/js/swiper-bundle.min.js') }}"></script>

        <script type="text/javascript"></script>
            </body>

</html>
