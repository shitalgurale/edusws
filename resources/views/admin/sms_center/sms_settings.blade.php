@extends('admin.navigation')

@section('content')
<style>
    .msg_center {
        margin: auto;
        width: 50%;
    }
    .vrt {
        flex-basis: inherit;
    }
    .gateway-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .gateway-options .form-check {
        display: flex;
        align-items: center;
    }
    .gateway-options .form-check-input {
        margin-right: 0.3rem;
    }
</style>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
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
                    <div class="vrt tab-content w-100" id="v-pills-tabContent">
                        <div class="tab-pane fade show active">
                            
                            <!-- Gateway Heading   -->
                           
                            <div class="btn btn-primary btn-lg" style="pointer-events: none; font-size: 20px; font-weight: 600;">
                                {{ get_phrase('Active a SMS Gateway') }}
                            </div>
                               <br/> <br/>

                            <!-- Gateway Selection Only -->
                            <div class="gateway-options">
                               <!-- <div class="form-check">
                                    <input class="form-check-input gateway-radio" type="radio" name="active_sms" value="none"
                                        {{ $sms_settings[0]->active_sms == 'none' ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ get_phrase('None') }}</label>
                                    </div>
                                -->
                                <div class="form-check">
                                    <input class="form-check-input gateway-radio" type="radio" name="active_sms" value="twilio"
                                        {{ $sms_settings[0]->active_sms == 'twilio' ? 'checked' : '' }}>
                                    <label class="form-check-label">Twilio</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input gateway-radio" type="radio" name="active_sms" value="msg91"
                                        {{ $sms_settings[0]->active_sms == 'msg91' ? 'checked' : '' }}>
                                    <label class="form-check-label">MSG91</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input gateway-radio" type="radio" name="active_sms" value="other"
                                        {{ $sms_settings[0]->active_sms == 'other' ? 'checked' : '' }}>
                                    <label class="form-check-label">Other</label>
                                </div>
                            </div>

                            <!-- Twilio Settings -->
                            <div id="twilio-settings" class="gateway-settings mt-4" style="display: none;">
                                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                    @csrf
                                    <input type="hidden" name="active_sms" value="twilio">
                                    <label class="mb-2">Twilio {{ get_phrase('SID') }}</label>
                                    <input type="text" class="form-control" name="twilio_sid" value="{{ $sms_settings[0]->twilio_sid }}">
                                    <label class="mb-2">Twilio {{ get_phrase('token') }}</label>
                                    <input type="text" class="form-control" name="twilio_token" value="{{ $sms_settings[0]->twilio_token }}" required>
                                    <label class="mb-2">Twilio {{ get_phrase('sender phone number') }}</label>
                                    <input type="text" class="form-control" name="twilio_from" value="{{ $sms_settings[0]->twilio_from }}" required>
                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>
                                </form>
                            </div>

                            <!-- MSG91 Settings -->
                            <div id="msg91-settings" class="gateway-settings mt-4" style="display: none;">
                                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                    @csrf
                                    <input type="hidden" name="active_sms" value="msg91">
                                    <label class="mb-2">MSG91 {{ get_phrase('auth key') }}</label>
                                    <input type="text" class="form-control" name="msg91_authentication_key" value="{{ $sms_settings[0]->msg91_authentication_key }}" required>
                                    <label class="mb-2">MSG91 {{ get_phrase('sender id') }}</label>
                                    <input type="text" class="form-control" name="msg91_sender_id" value="{{ $sms_settings[0]->msg91_sender_id }}" required>
                                    <label class="mb-2">MSG91 {{ get_phrase('route') }}</label>
                                    <input type="text" class="form-control" name="msg91_route" value="{{ $sms_settings[0]->msg91_route }}" required>
                                    <label class="mb-2">MSG91 {{ get_phrase('country code') }}</label>
                                    <input type="text" class="form-control" name="msg91_country_code" value="{{ $sms_settings[0]->msg91_country_code }}" required>
                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Other Settings -->
                            <div id="other-settings" class="gateway-settings mt-4" style="display: none;">
                                <form method="POST" action="{{ route('admin.sms_center.settingsUpdate') }}">
                                    @csrf
                                    <input type="hidden" name="active_sms" value="other">
                                    <label class="mb-2">{{ get_phrase('API Key') }}</label>
                                    <input type="text" class="form-control" name="other_api_key" value="{{ $sms_settings[0]->other_api_key ?? '' }}" required>
                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleGatewaySettings() {
        const selected = document.querySelector('input[name="active_sms"]:checked')?.value || 'none';

        document.getElementById('twilio-settings').style.display = 'none';
        document.getElementById('msg91-settings').style.display = 'none';
        document.getElementById('other-settings').style.display = 'none';

        if (selected === 'twilio') {
            document.getElementById('twilio-settings').style.display = 'block';
        } else if (selected === 'msg91') {
            document.getElementById('msg91-settings').style.display = 'block';
        } else if (selected === 'other') {
            document.getElementById('other-settings').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.gateway-radio').forEach(el => {
            el.addEventListener('change', toggleGatewaySettings);
        });
        toggleGatewaySettings();
    });
</script>
@endsection
