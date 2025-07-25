<style>
    .profile_img {
        display: flex;
        justify-content: center;
    }

    .student_simg {
        display: flex;
        justify-content: center;
    }

    .name_title h4 {
        font-size: 14px;
        font-weight: 500;
    }

    .text {
        border-top: 1px solid #817e7e21;
    }

    .text h4 {
        border-bottom: 1px solid #817e7e21;
        padding-bottom: 7px;
        padding-top: 5px;
        font-size: 14px;
        font-weight: 400;
    }

    .text h4:last-child {
        border-bottom: none;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="school_name">
            <h2 class="text-center"><?php echo e(DB::table('schools')->where('id', auth()->user()->school_id)->value('title')); ?></h2>
        </div>
    </div>
</div>

<section class="profile">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile_img">
                    <div class="test_div">
                        <div class="student_simg">
                            <?php if(!empty($student_details->photo)): ?>
                                <img src="<?php echo e($student_details->photo); ?>" class="rounded-circle div-sc-five">
                            <?php else: ?>
                                <img src="<?php echo e(asset('assets/uploads/default.png')); ?>" class="rounded-circle div-sc-five">
                            <?php endif; ?>
                        </div>
                        <div class="name_title mt-3 text-center">
                            <h4><?php echo e(get_phrase('Name')); ?> : <?php echo e($student_details->name ?? '-'); ?></h4>
                            <h4><?php echo e(get_phrase('Email')); ?> : <?php echo e(null_checker($student_details->email ?? '')); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <ul class="nav nav-pills eNav-Tabs-justify" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-jHome-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-jHome" type="button" role="tab" aria-controls="pills-jHome"
                            aria-selected="true">
                            <?php echo e(get_phrase('Student Info')); ?>

                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-jProfile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-jProfile" type="button" role="tab"
                            aria-controls="pills-jProfile" aria-selected="false">
                            <?php echo e(get_phrase('Change Password')); ?>

                        </button>
                    </li>
                    
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-admission-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-admission" type="button" role="tab"
                            aria-controls="pills-admission" aria-selected="false">
                            <?php echo e(get_phrase('Admission Details')); ?>

                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-additional-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-additional" type="button" role="tab"
                            aria-controls="pills-additional" aria-selected="false">
                            <?php echo e(get_phrase('More Additional Information')); ?>

                        </button>
                    </li>
                </ul>

                <div class="tab-content eNav-Tabs-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="pills-jHome" role="tabpanel"
                        aria-labelledby="pills-jHome-tab">
                        <div class="text name_title">
                            <h4><?php echo e(get_phrase('Name')); ?> : <?php echo e($student_details->name ?? '-'); ?></h4>
                            <h4><?php echo e(get_phrase('Class')); ?> : <?php echo e(null_checker($student_details->class_name ?? '')); ?></h4>
                            <h4><?php echo e(get_phrase('Section')); ?> : <?php echo e(null_checker($student_details->section_name ?? '')); ?></h4>
                            <h4><?php echo e(get_phrase('Session')); ?> : <?php echo e(null_checker($student_details->session_title ?? 'N/A')); ?></h4>
                            <h4><?php echo e(get_phrase('Parent')); ?> : <?php echo e(null_checker($student_details->parent_name ?? '')); ?></h4>
                            <h4><?php echo e(get_phrase('Blood')); ?> : <?php echo e(null_checker(strtoupper($student_details->blood_group ?? ''))); ?></h4>
                            <h4><?php echo e(get_phrase('Contact')); ?> : <?php echo e(null_checker($student_details->phone ?? '')); ?></h4>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="pills-jProfile" role="tabpanel" aria-labelledby="pills-jProfile-tab">
                        <form action="<?php echo e(route('admin.user_password')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="fpb-7">
                                <input type="text" class="form-control eForm-control" name="password"
                                    id="password<?php echo e($student_details->user_id ?? '0'); ?>">
                                <input type="hidden" name="user_id" value="<?php echo e($student_details->user_id ?? '0'); ?>">
                            </div>

                            <div class="generatePass d-flex">
                                <div class="pt-2">
                                    <button type="button" class="btn-form" style="width: 127px;" aria-expanded="false"
                                        onclick="generatePassword('<?php echo e($student_details->user_id ?? '0'); ?>')">Generate
                                        Password</button>
                                </div>
                                <div class="ms-3 pt-2">
                                    <button type="submit" class="btn-form float-end"><?php echo e(get_phrase('Submit')); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    

<div class="tab-pane fade" id="pills-admission" role="tabpanel" aria-labelledby="pills-admission-tab">
    <div class="text name_title">
        <?php if($admission_details): ?>

            <?php
                $user_info = json_decode($admission_details->user_information, true);
                $birthday_unix = $user_info['birthday'] ?? null;
                $dob_formatted = $birthday_unix ? \Carbon\Carbon::createFromTimestamp($birthday_unix)->format('d-m-Y') : '-';
            ?>

            <h4><?php echo e(get_phrase('Admission Date')); ?> : 
                <?php echo e(\Carbon\Carbon::parse($admission_details->admission_date)->format('d-m-Y')); ?></h4>

            <h4><?php echo e(get_phrase('Date of Birth')); ?> : <?php echo e($dob_formatted); ?></h4>
            <h4><?php echo e(get_phrase('Blood')); ?> : <?php echo e(null_checker(strtoupper($student_details->blood_group ?? ''))); ?></h4>
            <h4><?php echo e(get_phrase('Contact')); ?> : <?php echo e(null_checker($student_details->phone ?? '')); ?></h4>

            <h4><?php echo e(get_phrase('Class')); ?> : 
                <?php echo e(DB::table('classes')->where('id', $admission_details->class_id)->value('name') ?? 'N/A'); ?></h4>

            <h4><?php echo e(get_phrase('Section')); ?> : 
                <?php echo e(DB::table('sections')->where('id', $admission_details->section_id)->value('name') ?? 'N/A'); ?></h4>

            <h4><?php echo e(get_phrase('Session')); ?> : 
                <?php echo e(DB::table('sessions')->where('id', $admission_details->session_id)->value('session_title') ?? 'N/A'); ?></h4>
            <h4><?php echo e(get_phrase('Religion')); ?> : <?php echo e($admission_details->religion ?? '-'); ?></h4>
            <h4><?php echo e(get_phrase('Caste')); ?> : <?php echo e($admission_details->caste ?? '-'); ?></h4>
            <h4><?php echo e(get_phrase('Nationality')); ?> : <?php echo e($admission_details->nationality ?? '-'); ?></h4>
            <h4><?php echo e(get_phrase('Father Name')); ?> : <?php echo e($admission_details->father_name ?? '-'); ?></h4>
            <h4><?php echo e(get_phrase('Mother Name')); ?> : <?php echo e($admission_details->mother_name ?? '-'); ?></h4>

        <?php else: ?>
            <p>No admission details found.</p>
        <?php endif; ?>
    </div>
</div>



                    
                    <div class="tab-pane fade" id="pills-additional" role="tabpanel" aria-labelledby="pills-additional-tab">
                        <div class="text">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php
                                        $extra_info = json_decode($student_details->student_info ?? '');
                                    ?>
                                    <ul>
                                        <?php if(!empty($extra_info) && is_array($extra_info)): ?>
                                            <?php $__currentLoopData = $extra_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <h4><?php echo e($key + 1); ?>. <?php echo e($info); ?></h4>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function generatePassword(id) {
        var length = 12;
        var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        var password = "";
        for (var i = 0; i < length; ++i) {
            var randomNumber = Math.floor(Math.random() * charset.length);
            password += charset[randomNumber];
        }
        document.getElementById("password" + id).value = password;
    }
</script>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/student/student_profile.blade.php ENDPATH**/ ?>