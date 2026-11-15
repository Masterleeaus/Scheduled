<?php $__env->startSection('meta-keywords', "$be->home_meta_keywords"); ?>
<?php $__env->startSection('meta-description', "$be->home_meta_description"); ?>


<?php $__env->startSection('content'); ?>
  <!--   hero area start   -->
  <?php if($bs->home_version == 'static'): ?>
    <?php if ($__env->exists('front.default.partials.static')) echo $__env->make('front.default.partials.static', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php elseif($bs->home_version == 'slider'): ?>
    <?php if ($__env->exists('front.default.partials.slider')) echo $__env->make('front.default.partials.slider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php elseif($bs->home_version == 'video'): ?>
    <?php if ($__env->exists('front.default.partials.video')) echo $__env->make('front.default.partials.video', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php elseif($bs->home_version == 'particles'): ?>
    <?php if ($__env->exists('front.default.partials.particles')) echo $__env->make('front.default.partials.particles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php elseif($bs->home_version == 'water'): ?>
    <?php if ($__env->exists('front.default.partials.water')) echo $__env->make('front.default.partials.water', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php elseif($bs->home_version == 'parallax'): ?>
    <?php if ($__env->exists('front.default.partials.parallax')) echo $__env->make('front.default.partials.parallax', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>
  <!--   hero area end    -->


  <!--    introduction area start   -->
  <div class="intro-section" <?php if($bs->feature_section == 0): ?> style="margin-top: 0px;" <?php endif; ?>>
     <div class="container">
       <?php if($bs->feature_section == 1): ?>
       <div class="hero-features">
          <div class="row">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <style>
                    .sf<?php echo e($feature->id); ?>::after {
                        background-color: #<?php echo e($feature->color); ?>;
                    }
                </style>
                <div class="col-md-3 col-sm-6 single-hero-feature sf<?php echo e($feature->id); ?>" style="background-color: #<?php echo e($feature->color); ?>;">
                <div class="outer-container">
                    <div class="inner-container">
                        <div class="icon-wrapper">
                        <i class="<?php echo e($feature->icon); ?>"></i>
                        </div>
                        <h3><?php echo e(convertUtf8($feature->title)); ?></h3>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
       </div>
       <?php endif; ?>

       <?php if($bs->intro_section == 1): ?>
       <div class="row">
          <div class="col-lg-6 <?php echo e($rtl == 1 ? 'pl-lg-0' : 'pr-lg-0'); ?>">
             <div class="intro-txt">
                <span class="section-title"><?php echo e(convertUtf8($bs->intro_section_title)); ?></span>
                <h2 class="section-summary"><?php echo e(convertUtf8($bs->intro_section_text)); ?> </h2>
                <?php if(!empty($bs->intro_section_button_url) && !empty($bs->intro_section_button_text)): ?>
                <a href="<?php echo e($bs->intro_section_button_url); ?>" class="intro-btn" target="_blank"><span><?php echo e(convertUtf8($bs->intro_section_button_text)); ?></span></a>
                <?php endif; ?>
             </div>
          </div>
          <div class="col-lg-6 <?php echo e($rtl == 1 ? 'pr-lg-0' : 'pl-lg-0'); ?> px-md-3 px-0">
             <div class="intro-bg" style="background-image: url('<?php echo e(asset('assets/front/img/'.$bs->intro_bg)); ?>'); background-size: cover;">
                <?php if(!empty($bs->intro_section_video_link)): ?>
                <a id="play-video" class="video-play-button" href="<?php echo e($bs->intro_section_video_link); ?>">
                  <span></span>
                </a>
                <?php endif; ?>
             </div>
          </div>
       </div>
       <?php endif; ?>
     </div>
  </div>
  <!--    introduction area end   -->


  <?php if($bs->service_section == 1): ?>
    <?php if(!serviceCategory()): ?>
        <!--   service section start   -->
        <section class="services-area pb-130">
            <div class="container">
                <div class="row text-center">
                <div class="col-lg-6 offset-lg-3">
                    <span class="section-title"><?php echo e(convertUtf8($bs->service_section_title)); ?></span>
                    <h2 class="section-summary"><?php echo e(convertUtf8($bs->service_section_subtitle)); ?></h2>
                </div>
                </div>
            </div>
            <div class="container">
                <div class="row justify-content-center">
                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-4 col-md-6 col-sm-8">
                            <div class="services-item mt-30">
                                <div class="services-thumb">
                                    <img class="lazy" data-src="<?php echo e(asset('assets/front/img/services/' . $service->main_image)); ?>" alt="service" />
                                </div>
                                <div class="services-content">
                                    <a class="title" <?php if($service->details_page_status == 1): ?> href="<?php echo e(route('front.servicedetails', [$service->slug, $service->id])); ?>" <?php endif; ?>><h4><?php echo e($service->title); ?></h4></a>

                                    <p>
                                        <?php if(strlen($service->summary) > 120): ?>
                                           <?php echo e(mb_substr($service->summary, 0, 120, 'utf-8')); ?><span style="display: none;"><?php echo e(mb_substr($service->summary, 120, null, 'utf-8')); ?></span>
                                           <a href="#" class="see-more"><?php echo e(__('see more')); ?>...</a>
                                        <?php else: ?>
                                           <?php echo e($service->summary); ?>

                                        <?php endif; ?>
                                    </p>

                                    <?php if($service->details_page_status == 1): ?>
                                    <a href="<?php echo e(route('front.servicedetails', [$service->slug, $service->id])); ?>"><?php echo e(__('Read More')); ?> <i class="fas fa-plus"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
        <!--   service section end   -->
    <?php elseif(serviceCategory()): ?>
        <!--   service category section start   -->
        <div class="service-categories">
            <div class="container">
            <div class="row text-center">
                <div class="col-lg-6 offset-lg-3">
                    <span class="section-title"><?php echo e(convertUtf8($bs->service_section_title)); ?></span>
                    <h2 class="section-summary"><?php echo e(convertUtf8($bs->service_section_subtitle)); ?></h2>
                </div>
            </div>
            </div>
            <div class="container">
                <div class="row">
                    <?php $__currentLoopData = $scategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $scategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="single-category">
                                <?php if(!empty($scategory->image)): ?>
                                    <div class="img-wrapper">
                                        <img class="lazy" data-src="<?php echo e(asset('assets/front/img/service_category_icons/'.$scategory->image)); ?>" alt="">
                                    </div>
                                <?php endif; ?>
                                <div class="text">
                                    <h4><?php echo e(convertUtf8($scategory->name)); ?></h4>
                                    <p>
                                       <?php if(strlen($scategory->short_text) > 112): ?>
                                          <?php echo e(mb_substr($scategory->short_text, 0, 112, 'utf-8')); ?><span style="display: none;"><?php echo e(mb_substr($scategory->short_text, 112,null, 'utf-8')); ?></span>
                                          <a href="#" class="see-more"><?php echo e(__('see more')); ?>...</a>
                                       <?php else: ?>
                                          <?php echo e($scategory->short_text); ?>

                                       <?php endif; ?>
                                    </p>
                                    <a href="<?php echo e(route('front.services', ['category'=>$scategory->id])); ?>" class="readmore"><?php echo e(__('View Services')); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <!--   service category section end   -->
    <?php endif; ?>
  <?php endif; ?>


  <?php if($bs->approach_section == 1): ?>
  <!--   how we do section start   -->
  <div class="approach-section">
     <div class="container">
        <div class="row">
           <div class="col-lg-6">
              <div class="approach-summary">
                 <span class="section-title"><?php echo e(convertUtf8($bs->approach_title)); ?></span>
                 <h2 class="section-summary"><?php echo e(convertUtf8($bs->approach_subtitle)); ?></h2>
                 <?php if(!empty($bs->approach_button_url) && !empty($bs->approach_button_text)): ?>
                 <a href="<?php echo e($bs->approach_button_url); ?>" class="boxed-btn" target="_blank"><span><?php echo e(convertUtf8($bs->approach_button_text)); ?></span></a>
                 <?php endif; ?>
              </div>
           </div>
           <div class="col-lg-6">
              <ul class="approach-lists">
                 <?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <li class="single-approach">
                      <div class="approach-icon-wrapper"><i class="<?php echo e($point->icon); ?>"></i></div>
                      <div class="approach-text">
                         <h4><?php echo e(convertUtf8($point->title)); ?></h4>
                         <p>
                            <?php if(strlen($point->short_text) > 150): ?>
                                <?php echo e(mb_substr($point->short_text, 0, 150, 'utf-8')); ?><span style="display: none;"><?php echo e(mb_substr($point->short_text, 150, null, 'utf-8')); ?></span>
                                <a href="#" class="see-more"><?php echo e(__('see more')); ?>...</a>
                            <?php else: ?>
                                <?php echo e($point->short_text); ?>

                            <?php endif; ?>
                        </p>
                      </div>
                   </li>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
           </div>
        </div>
     </div>
  </div>
  <!--   how we do section end   -->
  <?php endif; ?>


  <?php if($bs->statistics_section == 1): ?>
  <!--    statistics section start    -->
  <div class="statistics-section" <?php if($bs->home_version != 'parallax'): ?> style="background-image: url('<?php echo e(asset('assets/front/img/'.$be->statistics_bg)); ?>'); background-size:cover;" <?php endif; ?> id="statisticsSection" <?php if($bs->home_version == 'parallax'): ?> data-parallax="scroll" data-speed="0.2" data-image-src="<?php echo e(asset('assets/front/img/'.$be->statistics_bg)); ?>" <?php endif; ?>>
     <div class="statistics-container">
        <div class="container">
           <div class="row no-gutters">
             <?php $__currentLoopData = $statistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $statistic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <div class="col-lg-3 col-md-6">
                  <div class="round"
                     data-value="1"
                     data-number="<?php echo e(convertUtf8($statistic->quantity)); ?>"
                     data-size="200"
                     data-thickness="6"
                     data-fill="{
                     &quot;color&quot;: &quot;#<?php echo e($bs->base_color); ?>&quot;
                     }">
                     <strong></strong>
                     <h5><i class="<?php echo e($statistic->icon); ?>"></i> <?php echo e(convertUtf8($statistic->title)); ?></h5>
                  </div>
               </div>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           </div>
        </div>
     </div>
  </div>
  <!--    statistics section end    -->
  <?php endif; ?>


  <?php if($bs->portfolio_section == 1): ?>
  <!--    case section start   -->
  <div class="case-section">
     <div class="container">
        <div class="row text-center">
           <div class="col-lg-6 offset-lg-3">
              <span class="section-title"><?php echo e(convertUtf8($bs->portfolio_section_title)); ?></span>
              <h2 class="section-summary"><?php echo e(convertUtf8($bs->portfolio_section_text)); ?></h2>
           </div>
        </div>
     </div>
     <div class="container-fluid">
        <div class="row">
           <div class="col-md-12">
              <div class="case-carousel owl-carousel owl-theme">
                 <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <div class="single-case single-case-bg-1" style="background-image: url('<?php echo e(asset('assets/front/img/portfolios/featured/'.$portfolio->featured_image)); ?>');">
                      <div class="outer-container">
                         <div class="inner-container">
                            <h4><?php echo e(strlen($portfolio->title) > 36 ? mb_substr($portfolio->title, 0, 36, 'utf-8') . '...' : $portfolio->title); ?></h4>
                            <?php if(!empty($portfolio->service)): ?>
                            <p><?php echo e($portfolio->service->title); ?></p>
                            <?php endif; ?>

                            <a href="<?php echo e(route('front.portfoliodetails', [$portfolio->slug, $portfolio->id])); ?>" class="readmore-btn"><span><?php echo e(__('Read More')); ?></span></a>

                         </div>
                      </div>
                   </div>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--    case section end   -->
  <?php endif; ?>


  <?php if($bs->testimonial_section == 1): ?>
  <!--   Testimonial section start    -->
  <div class="testimonial-section pb-115">
     <div class="container">
        <div class="row text-center">
           <div class="col-lg-6 offset-lg-3">
              <span class="section-title"><?php echo e(convertUtf8($bs->testimonial_title)); ?></span>
              <h2 class="section-summary"><?php echo e(convertUtf8($bs->testimonial_subtitle)); ?></h2>
           </div>
        </div>
        <div class="row">
           <div class="col-md-12">
              <div class="testimonial-carousel owl-carousel owl-theme">
                 <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <div class="single-testimonial">
                      <div class="img-wrapper"><img class="lazy" data-src="<?php echo e(asset('assets/front/img/testimonials/'.$testimonial->image)); ?>" alt=""></div>
                      <div class="client-desc">
                         <p class="comment"><?php echo e(convertUtf8($testimonial->comment)); ?></p>
                         <h6 class="name"><?php echo e(convertUtf8($testimonial->name)); ?></h6>
                         <p class="rank"><?php echo e(convertUtf8($testimonial->rank)); ?></p>
                      </div>
                   </div>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--   Testimonial section end    -->
  <?php endif; ?>


  <?php if($bs->team_section == 1): ?>
  <!--    team section start   -->
  <div class="team-section section-padding" <?php if($bs->home_version != 'parallax'): ?> style="background-image: url('<?php echo e(asset('assets/front/img/'.$bs->team_bg)); ?>'); background-size:cover;" <?php endif; ?> <?php if($bs->home_version == 'parallax'): ?> data-parallax="scroll" data-speed="0.2" data-image-src="<?php echo e(asset('assets/front/img/'.$bs->team_bg)); ?>" <?php endif; ?>>
     <div class="team-content">
        <div class="container">
           <div class="row text-center">
              <div class="col-lg-6 offset-lg-3">
                 <span class="section-title"><?php echo e(convertUtf8($bs->team_section_title)); ?></span>
                 <h2 class="section-summary"><?php echo e(convertUtf8($bs->team_section_subtitle)); ?></h2>
              </div>
           </div>
           <div class="row">
              <div class="team-carousel common-carousel owl-carousel owl-theme">
                <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <div class="single-team-member">
                    <div class="team-img-wrapper">
                       <img class="lazy" data-src="<?php echo e(asset('assets/front/img/members/'.$member->image)); ?>" alt="">
                       <div class="social-accounts">
                          <ul class="social-account-lists">
                             <?php if(!empty($member->facebook)): ?>
                               <li class="single-social-account"><a href="<?php echo e($member->facebook); ?>"><i class="fab fa-facebook-f"></i></a></li>
                             <?php endif; ?>
                             <?php if(!empty($member->twitter)): ?>
                               <li class="single-social-account"><a href="<?php echo e($member->twitter); ?>"><i class="fab fa-twitter"></i></a></li>
                             <?php endif; ?>
                             <?php if(!empty($member->linkedin)): ?>
                               <li class="single-social-account"><a href="<?php echo e($member->linkedin); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                             <?php endif; ?>
                             <?php if(!empty($member->instagram)): ?>
                               <li class="single-social-account"><a href="<?php echo e($member->instagram); ?>"><i class="fab fa-instagram"></i></a></li>
                             <?php endif; ?>
                          </ul>
                       </div>
                    </div>
                    <div class="member-info">
                       <h5 class="member-name"><?php echo e(convertUtf8($member->name)); ?></h5>
                       <small><?php echo e(convertUtf8($member->rank)); ?></small>
                    </div>
                 </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--    team section end   -->
  <?php endif; ?>


  <?php if($be->pricing_section == 1): ?>
  <!-- pricing begin -->
  <div class="pricing-tables">
     <div class="container">
       <div class="row text-center">
          <div class="col-lg-6 offset-lg-3">
             <span class="section-title"><?php echo e(convertUtf8($be->pricing_title)); ?></span>
             <h2 class="section-summary"><?php echo e(convertUtf8($be->pricing_subtitle)); ?></h2>
          </div>
       </div>
        <div class="pricing-carousel common-carousel owl-carousel owl-theme">
          <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="single-pricing-table">
               <span class="title"><?php echo e(convertUtf8($package->title)); ?></span>
               <div class="price">
                  <h1>
                      <?php echo e($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''); ?><?php echo e($package->price); ?><?php echo e($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''); ?>

                  </h1>
               </div>
               <div class="features">
                  <?php echo replaceBaseUrl(convertUtf8($package->description)); ?>

               </div>

               <?php if($package->order_status == 1): ?>
               <a href="<?php echo e(route('front.packageorder.index', $package->id)); ?>" class="pricing-btn"><?php echo e(__('Place Order')); ?></a>
               <?php endif; ?>

            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
     </div>
  </div>
  <!-- pricing end -->
  <?php endif; ?>



  <?php if($bs->news_section == 1): ?>
  <!--    blog section start   -->
  <div class="blog-section section-padding">
     <div class="container">
        <div class="row text-center">
           <div class="col-lg-6 offset-lg-3">
              <span class="section-title"><?php echo e(convertUtf8($bs->blog_section_title)); ?></span>
              <h2 class="section-summary"><?php echo e(convertUtf8($bs->blog_section_subtitle)); ?></h2>
           </div>
        </div>
        <div class="blog-carousel owl-carousel owl-theme common-carousel">
           <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="single-blog">
                 <div class="blog-img-wrapper">
                    <img class="lazy" data-src="<?php echo e(asset('assets/front/img/blogs/'.$blog->main_image)); ?>" alt="">
                 </div>
                 <div class="blog-txt">
                    <?php
                        $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                        $blogDate = $blogDate->translatedFormat('jS F, Y');
                    ?>

                    <p class="date"><small><?php echo e(__('By')); ?> <span class="username"><?php echo e(__('Admin')); ?></span></small> | <small><?php echo e($blogDate); ?></small> </p>

                    <h4 class="blog-title"><a href="<?php echo e(route('front.blogdetails', [$blog->slug, $blog->id])); ?>"><?php echo e(strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title); ?></a></h4>


                    <p class="blog-summary"><?php echo strlen(strip_tags($blog->content)) > 100 ? mb_substr(strip_tags($blog->content), 0, 100, 'utf-8') . '...' : strip_tags($blog->content); ?></p>


                    <a href="<?php echo e(route('front.blogdetails', [$blog->slug, $blog->id])); ?>" class="readmore-btn"><span><?php echo e(__('Read More')); ?></span></a>

                 </div>
              </div>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
     </div>
  </div>
  <!--    blog section end   -->
  <?php endif; ?>


  <?php if($bs->call_to_action_section == 1): ?>
  <!--    call to action section start    -->
  <div class="cta-section" style="background-image: url('<?php echo e(asset('assets/front/img/'.$bs->cta_bg)); ?>');background-size:cover;">
     <div class="container">
        <div class="cta-content">
           <div class="row">
              <div class="col-md-9 col-lg-7">
                 <h3><?php echo e(convertUtf8($bs->cta_section_text)); ?></h3>
              </div>
              <div class="col-md-3 col-lg-5 contact-btn-wrapper">
                 <a href="<?php echo e($bs->cta_section_button_url); ?>" class="boxed-btn contact-btn"><span><?php echo e(convertUtf8($bs->cta_section_button_text)); ?></span></a>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--    call to action section end    -->
  <?php endif; ?>


  <?php if($bs->partner_section == 1): ?>
  <!--   partner section start    -->
  <div class="partner-section">
     <div class="container <?php echo e($be->theme_version != 'dark' ? 'top-border' : ''); ?>">
        <div class="row">
           <div class="col-md-12">
              <div class="partner-carousel owl-carousel owl-theme common-carousel">
                 <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <a class="single-partner-item d-block" href="<?php echo e($partner->url); ?>" target="_blank">
                      <div class="outer-container">
                         <div class="inner-container">
                            <img class="lazy" data-src="<?php echo e(asset('assets/front/img/partners/'.$partner->image)); ?>" alt="">
                         </div>
                      </div>
                   </a>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--   partner section end    -->
  <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.default.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/default/index1.blade.php ENDPATH**/ ?>