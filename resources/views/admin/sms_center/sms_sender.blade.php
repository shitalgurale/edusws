@extends('admin.navigation')

@section('content')
<style>
    .smsSettingslink{
        color: #0d6efd;
    }
    .smsSettingsSpacing{
        margin-top: 1rem;
        font-size: 14px;
        font-weight: 400;
    }
    .notyfyActive{
        color: red;
        font-size: 14px;
        font-weight: 400;
    }
</style>


<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="page-title">
          <i class="mdi mdi-format-list-numbered title_icon"></i> {{ get_phrase('SMS Sender') }}
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row  mt-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h4 class="header-title mb-3">{{ get_phrase('Choose sms receiver') }}</h4>
        <div class="row">
            <div class="fpb-7">     
              <select name="receiver" id="receiver" class="form-select eForm-select eChoice-multiple-with-remove" data-toggle = "select2" onchange="toggleReceiverWiseOptions(this.value)" required>
                  <option value="">{{ get_phrase('Select receiver') }}</option>
                  <option value="student">{{ get_phrase('Student') }}</</option>
                  <option value="parent">{{ get_phrase('Parent') }}</</option>
                  <option value="teacher">{{ get_phrase('Teacher') }}</</option>
              </select>
            </div>
            <div class="fpb-7">
                <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSectionOnTakingAttendance(this.value)" required>
                    <option value="">{{ get_phrase('Select a class') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
         <div class="fpb-7">
            <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase('Select section') }}</option>
            </select>
        </div>
         <div class='row py-1'>
            <div class="form-group col-md-12" id="showStudentDiv">
                <a class="btn btn-block btn-secondary" onclick="getStudentList()" >{{ get_phrase('Show receiver') }}</a>
            </div>
        </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <form action="{{route('admin.sms_center.sms_sending')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                  <div class="card-body">
                    <h4 class="header-title mb-3"><?php echo get_phrase('List of receivers'); ?></h4>
                    <div class="list_of_receivers">

                        <div class="row py-1" id = "student_content">
                        </div>

                    </div>
                  </div>
                </div>
            </div>
            
            
            
          
            
            <!--
            
              <div class="col-md-5">
                <div class="card">
                  <div class="card-body">
                       <h4 class="header-title mb-3"><?php echo get_phrase('Template'); ?></h4>
                       <h4 class="header-title mb-3"><?php echo get_phrase('message'); ?></h4>

                        <div class="row">
                            <div class="col-md-12">
                              <div class="form-group mb-3">
                                <label for="example-textarea"><?php echo get_phrase('Message to send'); ?></label>
                                <textarea class="form-control" id="message_to_send" rows="7" placeholder="<?php echo get_phrase('Write down your message within 160 characters'); ?>..." maxlength="160" required></textarea>
                                <small><?php echo get_phrase('Remaining characters is'); ?> <strong id="remaining_character">160</strong> </small>
                              </div>
                            </div>
                          </div>
                          -->
                          
                          <div class="col-md-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-3">{{ get_phrase('Template') }}</h4>

            <div class="form-group mb-3">
                <label for="templateSelect">{{ get_phrase('Choose a template') }}</label>
                <select id="templateSelect" class="form-select eForm-select eChoice-multiple-with-remove" onchange="applyTemplate()">
                    <option value="">{{ get_phrase('Select Template') }}</option>
                    <option value="attendance">{{ get_phrase('Attendance') }}</option>
                    <option value="holiday">{{ get_phrase('Holiday') }}</option>
                    <option value="other">{{ get_phrase('Other') }}</option>
                </select>
            </div>

            <h4 class="header-title mb-3">{{ get_phrase('Message') }}</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="message_to_send">{{ get_phrase('Message to send') }}</label>
                        <textarea class="form-control" id="message_to_send" rows="7" placeholder="{{ get_phrase('Write down your message within 160 characters') }}..." maxlength="160" required></textarea>
                        <small>{{ get_phrase('Remaining characters is') }} <strong id="remaining_character">160</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                          
                          
                          
                          
                          <div class="row">
                            <div class="col-md-12 text-end">
                                <br/>
                                {{--  @if ($sms_settings[0]->active_sms == 'none')
                                <h6 class="notyfyActive">Please activate a sms gateway.</h6>
                                @else  --}}
                                <button type="submit" class="btn btn-block btn-primary" onclick="sendSmsToTheReceiver()"><?php echo get_phrase('Send sms'); ?></button>
                              {{--  @endif  --}}
                            </div>
                          </div>
                        </div>
                      </div>

        <div class="row  mt-4">
            <div class="col-md-3">
                <div class="card">
                  <div class="card-body">
                    <h4 class="header-title mb-3"><?php echo get_phrase('instruction'); ?></h4>
                    <div class="row mt-2">
                      <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                        <!--  <small><?php echo get_phrase('Before sending sms to the receivers please make sure that you have set up sms settings perfectly.'); ?></small>-->

                          <h6 class="smsSettingsSpacing"><?php echo get_phrase('Set sms settings'); ?> <a class="smsSettingslink" href="{{ route('admin.sms_center.index') }}"><?php echo get_phrase('here'); ?></a>.</h6>

                          <h6  class="smsSettingsSpacing"><?php echo get_phrase('Activated'); ?> :
                            @if ($sms_settings[0]->active_sms == 'twilio')
                            <strong>Twilio</strong>.
                            @elseif ($sms_settings[0]->active_sms == 'msg91')
                            <strong>MSG91</strong>.
                            @elseif ($sms_settings[0]->active_sms == 'none')
                            <strong>None</strong>.
                           @endif  </h6>
                        </div>
                      </div>
                    </div>
                    

                  </div>
                </div>
              </div>
        </div>
    </form>
  </div>

</div>


<script type="text/javascript">

    "use strict";

      $('document').ready(function(){

          $('#class_id').change(function(){
              $('#showStudentDiv').show();
              $('#student_content').hide();
          });
          $('#section_id').change(function(){
              $('#showStudentDiv').show();

              $('#student_content').hide();
          });
      });

      function classWiseSectionOnTakingAttendance(classId) {
          let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
          url = url.replace(":classId", classId);
          $.ajax({
              url: url,
              success: function(response){
                  $('#section_id').html(response);
              }
          });
      }

      function getStudentList() {
    var receiver = $('#receiver').val();
    var class_id = $('#class_id').val();
    var section_id = $('#section_id').val();

    if(receiver != ""){
        if (receiver != "teacher") {
            if(class_id != '' && section_id != ''){
                $.ajax({
                    url : '{{ route('admin.sms_center.receivers') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {class_id : class_id, section_id : section_id, receiver : receiver},
                    beforeSend: function() {
                        $('#student_content').html('<div class="text-center">Loading...</div>').show();
                    },
                    success : function(response) {
                        if (response.trim() != '') {
                            $('#student_content').html(response).show();
                        } else {
                            $('#student_content').html('<div class="text-danger">No receivers found.</div>').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        toastr.error("{{ get_phrase('Unable to fetch receiver list.') }}");
                        $('#student_content').html('');
                    }
                });
            } else {
                toastr.error("{{ get_phrase('Please select in all fields !') }}");
            }
        } else {
            // teacher logic
            $.ajax({
                url: "{{ route('admin.sms_center.receivers') }}",
                data : {class_id : class_id, section_id : section_id, receiver : receiver},
                success: function(response){
                    $('#student_content').html(response).show();
                    copyTheMessageToForm();
                }
            });
        }
    } else {
        toastr.error("{{ get_phrase('Please select a receiver !') }}");
    }
}





function applyTemplate() {
    const template = document.getElementById('templateSelect').value;
    let message = '';

    if (template === 'attendance') {
        message = '{var1} present for the day, IN/OUT Time {var2} PM, Worked Hours {var3}';
    } else if (template === 'holiday') {
        message = 'It is announced as a holiday to the school from {var1} to {var2} due to {var3}';
    }

    $('#message_to_send').val(message).trigger('input');
}



      $(document).ready(function () {
        $(".eChoice-multiple-with-remove").select2();
      });

      var formClass;

      function sendSmsToTheReceiver() {
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var receiver = $('#receiver').val();
        if(receiver != ""){
            formClass = receiver+"AjaxForm";
            if (receiver != "teacher") {
            if (class_id != "" && section_id != "") {
                $('.'+formClass).submit();
            }else{
                toastr.error("{{ get_phrase('Please select class and section !') }}");
                return;
            }
            }else{
            $('.'+formClass).submit();
            }
        }else{
            toastr.error("{{ get_phrase('Please select a receiver !') }}");
            return;
        }
        }
        $('#message_to_send').bind('input propertychange', function() {
            var currentLength = $('#message_to_send').val().length;
            var remaining_character = 160 - currentLength;
            $('#remaining_character').text(remaining_character);
            copyTheMessageToForm();
          });

          function copyTheMessageToForm() {
            var message = $('#message_to_send').val();
            $('.messages-to-send').val(message);
          }

     
     
     
     
    function sendSmsToTheReceiver() {
    const csrfToken = '{{ csrf_token() }}';

    fetch("{{ route('sms.send') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            mobiles: '919876543210',             // Replace with actual number
            studentName: 'Rahul Kumar',         // Replace dynamically
            dateTime: '2025-07-21 14:40:00',    // Replace dynamically
            workedHours: '06:30'                // Replace dynamically
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => {
        console.error('SMS error:', err);
        alert("SMS failed to send.");
    });
}


  </script>

@endsection

