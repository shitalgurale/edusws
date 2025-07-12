<?php $__env->startSection('content'); ?>
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
          <i class="mdi mdi-format-list-numbered title_icon"></i> <?php echo e(get_phrase('SMS Sender')); ?>

        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row  mt-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h4 class="header-title mb-3"><?php echo e(get_phrase('Choose sms receiver')); ?></h4>
        <div class="row">
            <div class="fpb-7">     
              <select name="receiver" id="receiver" class="form-select eForm-select eChoice-multiple-with-remove" data-toggle = "select2" onchange="toggleReceiverWiseOptions(this.value)" required>
                  <option value=""><?php echo e(get_phrase('Select receiver')); ?></option>
                  <option value="student"><?php echo e(get_phrase('Student')); ?></</option>
                  <option value="parent"><?php echo e(get_phrase('Parent')); ?></</option>
                  <option value="teacher"><?php echo e(get_phrase('Teacher')); ?></</option>
              </select>
            </div>
            <div class="fpb-7">
                <label for="class_id" class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSectionOnTakingAttendance(this.value)" required>
                    <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
         <div class="fpb-7">
            <label for="section_id" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value=""><?php echo e(get_phrase('Select section')); ?></option>
            </select>
        </div>
         <div class='row py-1'>
            <div class="form-group col-md-12" id="showStudentDiv">
                <a class="btn btn-block btn-secondary" onclick="getStudentList()" ><?php echo e(get_phrase('Show receiver')); ?></a>
            </div>
        </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <form action="<?php echo e(route('admin.sms_center.sms_sending')); ?>" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
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
              <div class="col-md-5">
                <div class="card">
                  <div class="card-body">
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
                          <div class="row">
                            <div class="col-md-12">
                                
                                <button type="submit" class="btn btn-block btn-primary" onclick="sendSmsToTheReceiver()"><?php echo get_phrase('Send sms'); ?></button>
                              
                            </div>
                          </div>
                        </div>
                      </div>


                <div class="card">
                  <div class="card-body">
                    <h4 class="header-title mb-3"><?php echo get_phrase('instruction'); ?></h4>
                    <div class="row mt-2">
                      <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                          <small><?php echo get_phrase('Before sending sms to the receivers please make sure that you have set up sms settings perfectly.'); ?></small>

                          <h6 class="smsSettingsSpacing"><?php echo get_phrase('You can set sms settings'); ?> <a class="smsSettingslink" href="<?php echo e(route('admin.sms_center.index')); ?>"><?php echo get_phrase('here'); ?></a>.</h6>

                          <h6  class="smsSettingsSpacing"><?php echo get_phrase('Currently activated'); ?> :
                            <?php if($sms_settings[0]->active_sms == 'twilio'): ?>
                            <strong>Twilio</strong>.
                            <?php elseif($sms_settings[0]->active_sms == 'msg91'): ?>
                            <strong>MSG91</strong>.
                            <?php elseif($sms_settings[0]->active_sms == 'none'): ?>
                            <strong>None</strong>.
                           <?php endif; ?>  </h6>
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
          let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
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
                        //type : 'POST',
                        url : '<?php echo e(route('admin.sms_center.receivers')); ?>',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {class_id : class_id, section_id : section_id, receiver : receiver},
                        success : function(response) {
                            $('#student_content').show();
                            $('#student_content').html(response);

                        }
                    });
                }else{
                    toastr.error("<?php echo e(get_phrase('Please select in all fields !')); ?>");
                }
            }else{
                $.ajax({
                  url: "<?php echo e(route('admin.sms_center.receivers')); ?>",
                  data : {class_id : class_id, section_id : section_id, receiver : receiver},
                  success: function(response){
                    $('#student_content').html(response);
                    copyTheMessageToForm();
                  }
                });
              }

        }else{

            toastr.error("<?php echo e(get_phrase('Please select a receiver !')); ?>");
          }
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
                toastr.error("<?php echo e(get_phrase('Please select class and section !')); ?>");
                return;
            }
            }else{
            $('.'+formClass).submit();
            }
        }else{
            toastr.error("<?php echo e(get_phrase('Please select a receiver !')); ?>");
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

      function toggleReceiverWiseOptions(receiver) {
        if (receiver != "") {
          if (receiver === "teacher") {
            $('#class_id').prop('disabled', true);
            $('#section_id').prop('disabled', true);
          }else{
            $('#class_id').prop('disabled', false);
            $('#section_id').prop('disabled', false);
          }
        }else{
          toastr.error('<?php echo get_phrase('Receiver can not be empty'); ?>');
        }
    }


  </script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/sms_center/sms_sender.blade.php ENDPATH**/ ?>