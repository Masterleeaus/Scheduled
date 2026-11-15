<?php
    $links = json_decode($menus, true);
    //  dd($links);
?>
<div class="header-navbar">
    <div class="row">
       <div class="col-lg-2 col-6">
          <div class="logo-wrapper">
             <a href="<?php echo e(route('front.index')); ?>"><img class="lazy" data-src="<?php echo e(asset('assets/front/img/'.$bs->logo)); ?>" alt=""></a>
          </div>
       </div>
       <div class="col-lg-10 col-6 <?php echo e($rtl == 1 ? 'text-left' : 'text-right'); ?> position-static">
          <ul class="main-menu" id="mainMenu">
             <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <?php
                     $href = getHref($link);
                 ?>


                 <?php if(strpos($link["type"], '-megamenu') !==  false): ?>
                    <?php if ($__env->exists('front.default.partials.mega-menu')) echo $__env->make('front.default.partials.mega-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                 
                 <?php else: ?>
                     <?php if(!array_key_exists("children",$link)): ?>

                         
                         <li><a href="<?php echo e($href); ?>" target="<?php echo e($link["target"]); ?>"><?php echo e($link["text"]); ?></a></li>

                     <?php else: ?>
                         <li class="dropdown">
                             
                             <a class="dropdown-btn" href="<?php echo e($href); ?>" target="<?php echo e($link["target"]); ?>"><?php echo e($link["text"]); ?></a>

                             <ul class="dropdown-lists">
                                 
                                 <?php $__currentLoopData = $link["children"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <?php
                                         $l2Href = getHref($level2);
                                     ?>

                                     <li <?php if(array_key_exists("children", $level2)): ?> class="submenus" <?php endif; ?>>
                                         <a  href="<?php echo e($l2Href); ?>" target="<?php echo e($level2["target"]); ?>"><?php echo e($level2["text"]); ?></a>

                                         
                                         <?php
                                             if (array_key_exists("children", $level2)) {
                                                 create_menu($level2);
                                             }
                                         ?>
                                         

                                     </li>
                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 
                             </ul>

                         </li>
                     <?php endif; ?>
                 <?php endif; ?>

             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

             <?php if($bs->is_quote == 1): ?>
             <li><a href="<?php echo e(route('front.quote')); ?>" class="boxed-btn"><?php echo e(__('Request A Quote')); ?></a></li>
             <?php endif; ?>
          </ul>
          <div id="mobileMenu"></div>
       </div>
    </div>
 </div>

<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/default/partials/navbar.blade.php ENDPATH**/ ?>