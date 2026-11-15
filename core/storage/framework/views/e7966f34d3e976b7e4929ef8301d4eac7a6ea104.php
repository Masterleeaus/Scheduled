<div class="hero-area lazy" id="hero-home-5" data-bg="<?php echo e(asset('assets/front/img/'.$bs->hero_bg)); ?>" style="background-size: cover;">
  <div id="bgndVideo" data-property="{videoURL:'<?php echo e($bs->hero_section_video_link); ?>',containment:'#hero-home-5', quality:'large', autoPlay:true, loop:true, mute:true, opacity:1}"></div>
   <div class="container">
      <div class="hero-txt">
         <div class="row">
           <div class="col-12">
              <span><?php echo e(convertUtf8($bs->hero_section_title)); ?></span>
              <h1><?php echo e(convertUtf8($bs->hero_section_text)); ?></h1>
              <?php if(!empty($bs->hero_section_button_url) && !empty($bs->hero_section_button_text)): ?>
              <a href="<?php echo e($bs->hero_section_button_url); ?>" class="hero-boxed-btn" target="_blank"><?php echo e(convertUtf8($bs->hero_section_button_text)); ?></a>
              <?php endif; ?>
           </div>
         </div>
      </div>
   </div>
   <div class="hero-area-overlay" style="background-color: #<?php echo e($be->hero_overlay_color); ?>;opacity: <?php echo e($be->hero_overlay_opacity); ?>;"></div>
</div>
<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/default/partials/video.blade.php ENDPATH**/ ?>