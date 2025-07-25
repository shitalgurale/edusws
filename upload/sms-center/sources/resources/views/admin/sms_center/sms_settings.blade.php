@extends('admin.navigation')

@section('content')
<style>
    .msg_center{
        margin: auto;
        width: 50%;
    }
    #sms_tolio{
        font-size: 10px;
        margin-bottom: 15px;
    }
    .vrt{
        flex-basis: inherit;
    }
</style>


<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Sms Settings') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Sms Center') }}</a></li>
              <li><a href="#">{{ get_phrase('Sms Settings') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="col-12 pb-3">
                <div class="d-flex msg_center flex-column flex-md-row align-items-start vTabs-gap">
                    <div class="nav flex-row flex-md-column nav-pills vrt eNav-Tabs-vertical"
                        id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active mb-3" id="v-pills-vHome-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-vHome" type="button"
                            role="tab" aria-controls="v-pills-vHome"
                            aria-selected="true">{{ get_phrase('Active a sms getway') }}</button>
                        <button class="nav-link mb-3" id="v-pills-vProfile-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-vProfile" type="button" role="tab"
                            aria-controls="v-pills-vProfile"
                            aria-selected="false">{{ get_phrase('Twilio') }}</button>
                        <button class="nav-link " id="v-pills-vMessages-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-vMessages"
                            type="button" role="tab" aria-controls="v-pills-vMessages"
                            aria-selected="false">{{ get_phrase('MSG91') }}</button>



                    </div>
                    <div class="vrt tab-content " id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-vHome"
                            role="tabpanel" aria-labelledby="v-pills-vHome-tab">

                            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                @csrf
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="active_sms" id="flexRadioDefault1" value="none"
                                     @if ($sms_settings[0]->active_sms == 'none')
                                    checked
                                   @endif >

                                    <label class="form-check-label" for="flexRadioDefault1">
                                        {{ get_phrase('None') }}
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="active_sms" id="flexRadioDefault1" value="twilio"
                                     @if ($sms_settings[0]->active_sms == 'twilio')
                                     checked
                                    @endif >


                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Twilio
                                    </label>
                                </div>
                                <div class="form-check ">
                                    <input class="form-check-input" type="radio" name="active_sms" id="flexRadioDefault2" value="msg91"
                                    @if ($sms_settings[0]->active_sms == 'msg91')
                                    checked
                                   @endif >
                                    <label class="form-check-label" for="flexRadioDefault2">
                                      MSG91
                                    </label>
                                </div>
                                <div class="fpb-7 pt-2">
                                    <button class="btn-form" type="submit">{{ get_phrase('Save') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-vProfile" role="tabpanel"
                            aria-labelledby="v-pills-vProfile-tab">
                            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                @csrf

                                <div class="form-row">

                                    <label class="mb-2" for="twilio_sid">Twilio{{ get_phrase('SID') }} </label>
                                    <input type="text" class="form-control" id="twilio_sid" name="twilio_sid" value="{{ $sms_settings[0]->twilio_sid }}">

                                    <label class="mb-2" for="twilio_sid">Twilio <?php echo get_phrase('token'); ?></label>
                                    <input type="text" class="form-control" id="twilio_token" name = "twilio_token" value="{{ $sms_settings[0]->twilio_token }}" required>

                                    <label class="mb-2"  for="twilio_from">Twilio <?php echo get_phrase('sender phone number'); ?></label>
                                    <input type="text" class="form-control" id="twilio_from" name = "twilio_from" value="{{ $sms_settings[0]->twilio_from }}" required>

                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-vMessages" role="tabpanel"
                            aria-labelledby="v-pills-vMessages-tab">
                            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                @csrf

                                <div class="form-row">

                                    <label class="mb-2" for="msg91_authentication_key">MSG91 <?php echo get_phrase('auth key'); ?></label>
                                    <input type="text" class="form-control" id="msg91_authentication_key" name = "msg91_authentication_key"value="{{ $sms_settings[0]->msg91_authentication_key }}" required>

                                    <label class="mb-2" for="twilio_sid">MSG91 <?php echo get_phrase('sender id'); ?></label>
                                    <input type="text" class="form-control" id="twilio_token" name = "msg91_sender_id" value="{{ $sms_settings[0]->msg91_sender_id }}" required>

                                    <label class="mb-2"  for="twilio_from">MSG91 <?php echo get_phrase('route'); ?></label>
                                    <input type="text" class="form-control" id="twilio_from" name = "msg91_route"value="{{ $sms_settings[0]->msg91_route }}" required>

                                    <label class="mb-2"  for="twilio_from">MSG91 <?php echo get_phrase('country code'); ?></label>
                                    <input type="text" class="form-control" id="twilio_from" name = "msg91_country_code"value="{{ $sms_settings[0]->msg91_country_code }}" required>

                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
