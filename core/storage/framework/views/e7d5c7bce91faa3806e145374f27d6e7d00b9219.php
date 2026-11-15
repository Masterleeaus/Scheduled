<?php
    $version = $be->theme_version;

    if ($version == 'dark') {
        $version = 'default';
    }
?>



<?php $__env->startSection('breadcrumb-title', $bs->error_title); ?>
<?php $__env->startSection('breadcrumb-subtitle', $bs->error_subtitle); ?>
<?php $__env->startSection('breadcrumb-link', __('404')); ?>

<?php $__env->startSection('content'); ?>


  <!--    Error section start   -->
  <div class="error-section">
     <div class="container">
        <div class="row">
           <div class="col-lg-6">
              <div class="not-found">
                 <img src="<?php echo e(asset('assets/front/img/404.png')); ?>" alt="">
              </div>
           </div>
           <div class="col-lg-6">
              <div class="error-txt">
                 <div class="oops">
                    <img src="<?php echo e(asset('assets/front/img/oops.png')); ?>" alt="">
                 </div>
                 <h2>You're lost...</h2>
                 <p>The page you are looking for might have been moved, renamed, or might never existed.</p>
                 <a href="<?php echo e(route('front.index')); ?>" class="go-home-btn">Back Home</a>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--    Error section end   -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make("front.$version.layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/errors/404.blade.php ENDPATH**/ ?>