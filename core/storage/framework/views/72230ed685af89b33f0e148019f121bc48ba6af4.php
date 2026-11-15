<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
  	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <title><?php echo e($bs->website_title); ?></title>
  	<link rel="icon" href="<?php echo e(asset('assets/front/img/'.$bs->favicon)); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/login.css')); ?>">
  </head>
  <body>
    <div class="login-page">
      <div class="text-center mb-4">
        <img class="login-logo" src="<?php echo e(asset('assets/front/img/'.$bs->logo)); ?>" alt="">
      </div>
      <div class="form">
        <?php if(session()->has('alert')): ?>
          <div class="alert alert-danger fade show" role="alert" style="font-size: 14px;">
            <strong>Oops!</strong> <?php echo e(session('alert')); ?>

          </div>
        <?php endif; ?>
        <form class="login-form" action="<?php echo e(route('admin.auth')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <input type="text" name="username" placeholder="username"/>
          <?php if($errors->has('username')): ?>
            <p class="text-danger text-left"><?php echo e($errors->first('username')); ?></p>
          <?php endif; ?>
          <input type="password" name="password" placeholder="password"/>
          <?php if($errors->has('password')): ?>
            <p class="text-danger text-left"><?php echo e($errors->first('password')); ?></p>
          <?php endif; ?>
          <button type="submit">login</button>
        </form>
        <a class="forget-link" href="<?php echo e(route('admin.forget.form')); ?>">Forgot Password / Username ?</a>
      </div>
    </div>


    <!-- jquery js -->
    <script src="<?php echo e(asset('assets/front/js/jquery-3.3.1.min.js')); ?>"></script>
    <!-- popper js -->
    <script src="<?php echo e(asset('assets/front/js/popper.min.js')); ?>"></script>
    <!-- bootstrap js -->
    <script src="<?php echo e(asset('assets/front/js/bootstrap.min.js')); ?>"></script>
    <!-- Bootstrap Notify -->
    <script src="<?php echo e(asset('assets/admin/js/plugin/bootstrap-notify/bootstrap-notify.min.js')); ?>"></script>

    <?php if(session()->has('warning')): ?>
    <script>
      var content = {};

      content.message = '<?php echo e(session('warning')); ?>';
      content.title = 'Sorry!';
      content.icon = 'fa fa-bell';

      $.notify(content,{
        type: 'warning',
        placement: {
          from: 'top',
          align: 'right'
        },
        showProgressbar: true,
        time: 1000,
        delay: 4000,
      });
    </script>
    <?php endif; ?>
  </body>
</html>
<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/admin/login.blade.php ENDPATH**/ ?>