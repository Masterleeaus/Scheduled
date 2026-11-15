

<?php $__env->startSection('template_title'); ?>
    <?php echo e(trans('installer_messages.license.templateTitle')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    <i class="fa fa-key fa-fw" aria-hidden="true"></i>
    <?php echo trans('installer_messages.license.title'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('container'); ?>
    <div class="tabs tabs-full">



        <form method="post" action="<?php echo e(route('LaravelInstaller::licenseCheck')); ?>" class="tabs-wrap">
            <?php if(session()->has('license_error')): ?>
                <div class="alert alert-danger" id="error_alert">
                    <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">
                        <i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <p style="margin-bottom: 0px;"><?php echo e(session()->get('license_error')); ?></p>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning" style="background-color: #fff3cd; color: #856404;">
                <p style="margin-bottom: 0px; color: orange;">Make sure your internet connection is ON</p>
            </div>

            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">

            <div>
                <div class="form-group <?php echo e($errors->has('email') ? ' has-error ' : ''); ?>">
                    <label for="email">
                        Email Address
                    </label>
                    <input type="text" name="email" id="email" value="" placeholder="Your Mail Address" />
                    <p>This Mail Address will be used to inform you about Urgent Notices, Announcements, Offers / Sales etc...</p>
                    <?php if($errors->has('email')): ?>
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            <?php echo e($errors->first('email')); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?php echo e($errors->has('username') ? ' has-error ' : ''); ?>">
                    <label for="username">
                        Envato Username
                    </label>
                    <input type="text" name="username" id="username" value="" placeholder="Username of Your Envato Account" />
                    <?php if($errors->has('username')): ?>
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            <?php echo e($errors->first('username')); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?php echo e($errors->has('purchase_code') ? ' has-error ' : ''); ?>">
                    <label for="purchase_code">
                        Purchase Code
                    </label>
                    <input type="text" name="purchase_code" id="purchase_code" value="" placeholder="Your Item Purchase Code" />
                    <?php if($errors->has('purchase_code')): ?>
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            <?php echo e($errors->first('purchase_code')); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <div class="buttons">
                    <button class="button" type="submit" style="font-size: 14px;">
                        Verify
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('vendor.installer.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/codecanyon8/public_html/plusagency/core/resources/views/vendor/installer/license.blade.php ENDPATH**/ ?>