<form method="POST" class="d-block ajaxForm" id="edit_form" action="" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">
                <?php echo e(get_phrase('User name')); ?>

            </label>
            <input type="text" class="form-control eForm-control" id="name" name="name" value="<?php echo e($user['name']); ?>" required>
            <input type="hidden" class="form-control eForm-control" id="user_id_for_edit" name="user_id_for_edit" value="<?php echo e($user['id']); ?>" required>
        </div>


        <div class="fpb-7">
            <label for="emp_bioid" class="eForm-label">
                <?php echo e(get_phrase('Bio ID')); ?>

            </label>
            <input type="number" class="form-control eForm-control" id="emp_bioid" name="emp_bioid" value="<?php echo e($user['emp_bioid']); ?>" required>

        </div>


        <div class="fpb-7">
            <label for="email" class="eForm-label">
                <?php echo e(get_phrase('Email')); ?>

            </label>
            <input type="text" class="form-control eForm-control" id="email" name="email" value="<?php echo e($user['email']); ?>" required>

        </div>

        <div class="fpb-7">
            <label for="gender" class="eForm-label">
                <?php echo e(get_phrase('Gender')); ?>

            </label>
            <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">
                    <?php echo e(get_phrase('Select a gender')); ?>

                </option>
                <option value="male" <?php echo e($user['gender'] == 'male' ? 'selected' :''); ?>>
                    <?php echo e('Male'); ?>

                </option>
                <option value="female" <?php echo e($user['gender'] == 'female' ? 'selected' :''); ?>>
                    <?php echo e('Female'); ?>

                </option>
                <option value="other" <?php echo e($user['gender'] == 'other' ? 'selected' :''); ?>>
                    <?php echo e('Other'); ?>

                </option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="blood_group" class="eForm-label">
                <?php echo e(get_phrase('Blood group')); ?>

            </label>
            <select name="blood_group" id="blood_group" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">
                    <?php echo e(get_phrase('Select a blood group')); ?>

                </option>
                <option value="a+" <?php echo e($user['blood_group'] == 'a+' ? 'selected' :''); ?>>
                    <?php echo e('A+'); ?>

                </option>
                <option value="a-" <?php echo e($user['blood_group'] == 'a-' ? 'selected' :''); ?>>
                    <?php echo e('A-'); ?>

                </option>
                <option value="b+" <?php echo e($user['blood_group'] == 'b+' ? 'selected' :''); ?>>
                    <?php echo e('B+'); ?>

                </option>
                <option value="b-" <?php echo e($user['blood_group'] == 'b-' ? 'selected' :''); ?>>
                    <?php echo e('B-'); ?>

                </option>
                <option value="ab+" <?php echo e($user['blood_group'] == 'ab+' ? 'selected' :''); ?>>
                    <?php echo e('AB+'); ?>

                </option>
                <option value="ab-" <?php echo e($user['blood_group'] == 'ab-' ? 'selected' :''); ?>>
                    <?php echo e('AB-'); ?>

                </option>
                <option value="o+" <?php echo e($user['blood_group'] == 'o+' ? 'selected' :''); ?>>
                    <?php echo e('O+'); ?>

                </option>
                <option value="o-" <?php echo e($user['blood_group'] == 'o-' ? 'selected' :''); ?>>
                    <?php echo e('O-'); ?>

                </option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="phone" class="eForm-label">
                <?php echo e(get_phrase('Phone')); ?>

            </label>
            <input type="text" class="form-control eForm-control" id="phone" name="phone" value="<?php echo e($user['phone']); ?>" required>

        </div>

        <div class="fpb-7">
            <label for="address" class="eForm-label">
                <?php echo e(get_phrase('Address')); ?>

            </label>
            <input type="text" class="form-control eForm-control" id="address" name="address" value="<?php echo e($user['address']); ?>" required>

        </div>

        <div class="fpb-7">
            <label for="role" class="eForm-label">
                <?php echo e(get_phrase('Role')); ?>

            </label>
            <select name="aj_role_id" id="aj_role_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">
                    <?php echo e(get_phrase('Select a role')); ?>

                </option>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role->id); ?>" <?php echo e($role->id == $user['role_id'] ? 'selected':''); ?>>
                        <?php echo e(ucfirst($role['name'])); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </select>

        </div>

        <div class="fpb-7">
            <label for="salary" class="eForm-label">
                <?php echo e(get_phrase('Salary')); ?>

            </label>
            <input type="number" class="form-control eForm-control" id="joining_salary" name="joining_salary" value="<?php echo e($user['joining_salary']); ?>" required>

        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" id="edituser" type="button">
                <?php echo e(get_phrase('Update Data')); ?>

            </button>
        </div>
    </div>
</form>

<script>
"use strict";

$(document).ready(function () {
  $(".eChoice-multiple-with-remove").select2();
});

$("#edituser").on("click",function(e){
               e.preventDefault();
            let form=new FormData(edit_form);

            let name= $("#name").val();
            let email= $("#email").val();
            let gender= $("#gender").val();
            let bloodgroup= $("#blood_group").val();
            let phone= $("#phone").val();
            let address= $("#address").val();
            let role_id= $("#aj_role_id").val();
            let joining_salary= $("#joining_salary").val();
            let user_id_for_edit= $("#user_id_for_edit").val();
            let emp_bioid = $("#emp_bioid").val();


            form.append("name",name);
            form.append("email",email);
            form.append("gender",gender);
            form.append("bloodgroup",bloodgroup);
            form.append("phone",phone);
            form.append("address",address);
            form.append("role_id",role_id);
            form.append("joining_salary",joining_salary);
            form.append("user_id_for_edit",user_id_for_edit);
            form.append("emp_bioid", emp_bioid);
            form.append("_token","<?php echo e(csrf_token()); ?>");

            console.log(role_id);



            var url = '<?php echo e(route("hr.user_lists_user_edit_post", ":id")); ?>';
            url = url.replace(':id', user_id_for_edit );

            $.ajax({
                url:url,
                type: "POST",
                data:form,
                contentType: false,
                processData: false,
                success : function(data){
                    filter_user();
                    $("#offcanvasScrollingRightBS").removeClass("show");
                        document.querySelectorAll(".offcanvas-backdrop").forEach(el => el.remove());
                        toastr.success("updated Successfully");


                }

            });

      })






</script>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/hr_user_list/edit.blade.php ENDPATH**/ ?>