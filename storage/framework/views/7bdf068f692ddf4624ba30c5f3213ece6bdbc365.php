<?php $__env->startSection('content'); ?>

<style type="text/css">

.login-image{
    width: inherit; 
    height: 100%; 
    position: fixed; 
    background-image: url('public/assets/images/login.png'); 
    background-size: cover; 
    background-position: center;
}

</style>

<div class="row h-100">
    <div class="col-lg-6 d-none d-lg-block p-0 h-100">
        <div class="bg-image login-image">
        </div>
    </div>
    <div class="col-lg-6 p-0 h-100 position-relative">
        <div class="parent-elem">
            <div class="middle-elem">
                <div class="primary-form">
                    <div class="form-logo mb-5">
                        <img height="60px" src="<?php echo e(asset('assets/uploads/logo/'.get_settings('dark_logo'))); ?>">
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="login-form">
                                <form method="post" action="<?php echo e(route('login')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email" class="form-label"><?php echo e(get_phrase('Email')); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email" class="form-control" id="email"
                                                  placeholder="Your email address">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="form-label"><?php echo e(get_phrase('Password')); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                <input type="password" name="password" class="form-control border-end" id="password"
                                                  placeholder="Input your password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="w-100 d-flex justify-content-end">
                                                <a href="<?php echo e(route('password.request')); ?>" class="float-end"><?php echo e(get_phrase('Forgot password')); ?></a>
                                                <i class="bi bi-record-fill text-5px mx-1 px-1"></i>
                                                <a href="<?php echo e(get_settings('help_link')); ?>" target="_blank" class="float-end me-1"><?php echo e(get_phrase('Help')); ?></a>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100"><?php echo e(get_phrase('Login')); ?></button>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.signin_page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/auth/login.blade.php ENDPATH**/ ?>