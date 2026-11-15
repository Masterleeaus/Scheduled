<!DOCTYPE html>
<html lang="en">
   <head>
      <!--Start of Google Analytics script-->
      <?php if($bs->is_analytics == 1): ?>
      <?php echo $bs->google_analytics_script; ?>

      <?php endif; ?>
      <!--End of Google Analytics script-->

      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <meta name="description" content="<?php echo $__env->yieldContent('meta-description'); ?>">
      <meta name="keywords" content="<?php echo $__env->yieldContent('meta-keywords'); ?>">

      <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
      <title><?php echo e($bs->website_title); ?> <?php echo $__env->yieldContent('pagename'); ?></title>
      <!-- favicon -->
      <link rel="shortcut icon" href="<?php echo e(asset('assets/front/img/'.$bs->favicon)); ?>" type="image/x-icon">
      <!-- bootstrap css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/bootstrap.min.css')); ?>">
      <!-- plugin css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/plugin.min.css')); ?>">

      <!-- main css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/style.css')); ?>">

      <!-- common css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/common-style.css')); ?>">
      <?php echo $__env->yieldContent('styles'); ?>

      <?php if($bs->is_tawkto == 1 || $bex->is_whatsapp == 1): ?>
      <style>
        .back-to-top.show {
            right: auto;
            left: 20px;
        }
      </style>
      <?php endif; ?>
      <?php if(count($langs) == 0): ?>
      <style media="screen">
      .support-bar-area ul.social-links li:last-child {
          margin-right: 0px;
      }
      .support-bar-area ul.social-links::after {
          display: none;
      }
      </style>
      <?php endif; ?>

      <!-- responsive css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/responsive.css')); ?>">
      <!-- common base color change -->
      <link href="<?php echo e(url('/')); ?>/assets/front/css/common-base-color.php?color=<?php echo e($bs->base_color); ?>" rel="stylesheet">
      <!-- base color change -->
      <link href="<?php echo e(url('/')); ?>/assets/front/css/base-color.php?color=<?php echo e($bs->base_color); ?><?php echo e($be->theme_version != 'dark' ? "&color1=" . $bs->secondary_base_color : ""); ?>" rel="stylesheet">

      <?php if($be->theme_version == 'dark'): ?>
        <!-- dark version css -->
        <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/dark.css')); ?>">
        <!-- dark version base color change -->
        <link href="<?php echo e(url('/')); ?>/assets/front/css/dark-base-color.php?color=<?php echo e($bs->base_color); ?>" rel="stylesheet">
      <?php endif; ?>

      <?php if($rtl == 1): ?>
      <!-- RTL css -->
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/rtl.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('assets/front/css/pb-rtl.css')); ?>">
      <?php endif; ?>
      <!-- jquery js -->
      <script src="<?php echo e(asset('assets/front/js/jquery-3.3.1.min.js')); ?>"></script>

      <?php if($bs->is_appzi == 1): ?>
      <!-- Start of Appzi Feedback Script -->
      <script async src="https://app.appzi.io/bootstrap/bundle.js?token=<?php echo e($bs->appzi_token); ?>"></script>
      <!-- End of Appzi Feedback Script -->
      <?php endif; ?>

      <!-- Start of Facebook Pixel Code -->
      <?php if($be->is_facebook_pexel == 1): ?>
        <?php echo $be->facebook_pexel_script; ?>

      <?php endif; ?>
      <!-- End of Facebook Pixel Code -->

      <!--Start of Appzi script-->
      <?php if($bs->is_appzi == 1): ?>
      <?php echo $bs->appzi_script; ?>

      <?php endif; ?>
      <!--End of Appzi script-->
   </head>



   <body <?php if($rtl == 1): ?> dir="rtl" <?php endif; ?>>

      <!--   header area start   -->
      <div class="header-area header-absolute <?php echo $__env->yieldContent('no-breadcrumb'); ?>">
         <div class="container">
            <div class="support-bar-area">
               <div class="row">
                  <div class="col-lg-6 support-contact-info">
                     <span class="address"><i class="far fa-envelope"></i> <?php echo e($bs->support_email); ?></span>
                     <span class="phone"><i class="flaticon-chat"></i> <?php echo e($bs->support_phone); ?></span>
                  </div>
                  <div class="col-lg-6 <?php echo e($rtl == 1 ? 'text-left' : 'text-right'); ?>">
                     <ul class="social-links">
                       <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <li><a target="_blank" href="<?php echo e($social->url); ?>"><i class="<?php echo e($social->icon); ?>"></i></a></li>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </ul>

                     <?php if(!empty($currentLang) && count($langs) > 1): ?>
                       <div class="language">
                          <a class="language-btn" href="#"><i class="flaticon-worldwide"></i> <?php echo e(convertUtf8($currentLang->name)); ?></a>
                          <ul class="language-dropdown">
                            <?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href='<?php echo e(route('changeLanguage', $lang->code)); ?>'><?php echo e(convertUtf8($lang->name)); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </ul>
                       </div>
                     <?php endif; ?>

                     <?php if(auth()->guard()->guest()): ?>
                        <?php if($bex->is_user_panel == 1): ?>
                            <ul class="login">
                                <li><a href="<?php echo e(route('user.login')); ?>"><?php echo e(__('Login')); ?></a></li>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                     <?php if(auth()->guard()->check()): ?>
                     <div class="language dashboard">
                         <a class="language-btn" href="#">
                             <i class="far fa-user"></i> <?php echo e(Auth::user()->username); ?>

                         </a>
                         <ul class="language-dropdown">
                             <li>
                                 <a href="<?php echo e(route('user-dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                             </li>

                             <?php if($bex->recurring_billing == 1): ?>
                                 <li><a href="<?php echo e(route('user-packages')); ?>"><?php echo e(__('Packages')); ?></a></li>
                             <?php endif; ?>

                             <?php if($bex->is_shop == 1 && $bex->catalog_mode == 0): ?>
                                 <li><a href="<?php echo e(route('user-orders')); ?>"><?php echo e(__('Product Orders')); ?> </a></li>
                             <?php endif; ?>

                             <?php if($bex->recurring_billing == 0): ?>
                                 <li><a href="<?php echo e(route('user-package-orders')); ?>"><?php echo e(__('Package Orders')); ?> </a></li>
                             <?php endif; ?>


                             <?php if($bex->is_course == 1): ?>
                             <li>
                                 <a href="<?php echo e(route('user.course_orders')); ?>" ><?php echo e(__('Courses')); ?></a>
                             </li>
                             <?php endif; ?>


                             <?php if($bex->is_event == 1): ?>
                             <li>
                                 <a href="<?php echo e(route('user-events')); ?>" ><?php echo e(__('Event Bookings')); ?></a>
                             </li>
                             <?php endif; ?>


                             <?php if($bex->is_donation == 1): ?>
                             <li>
                                 <a href="<?php echo e(route('user-donations')); ?>" ><?php echo e(__('Donations')); ?></a>
                             </li>
                             <?php endif; ?>

                             <?php if($bex->is_ticket == 1): ?>
                             <li>
                                 <a href="<?php echo e(route('user-tickets')); ?>"><?php echo e(__('Support Tickets')); ?></a>
                             </li>
                             <?php endif; ?>

                             <li>
                                 <a href="<?php echo e(route('user-profile')); ?>"><?php echo e(__('Edit Profile')); ?></a>
                             </li>

                             <?php if($bex->is_shop == 1 && $bex->catalog_mode == 0): ?>
                                 <li>
                                     <a href="<?php echo e(route('shpping-details')); ?>"><?php echo e(__('Shipping Details')); ?></a>
                                 </li>
                                 <li>
                                     <a href="<?php echo e(route('billing-details')); ?>"><?php echo e(__('Billing Details')); ?></a>
                                 </li>
                                 <li>
                                     <a href="<?php echo e(route('user-reset')); ?>"><?php echo e(__('Change Password')); ?></a>
                                 </li>
                             <?php endif; ?>
                             <li>
                                 <a href="<?php echo e(route('user-logout')); ?>" target="_self"><?php echo e(__('Logout')); ?></a>
                             </li>
                         </ul>
                     </div>
                     <?php endif; ?>


                  </div>
               </div>
            </div>


            <?php if ($__env->exists('front.default.partials.navbar')) echo $__env->make('front.default.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

         </div>
      </div>
      <!--   header area end   -->


      <?php if(!request()->routeIs('front.index') && !request()->routeIs('front.packageorder.confirmation')): ?>
        <!--   breadcrumb area start   -->
        <div class="breadcrumb-area cases lazy" data-bg="<?php echo e(asset('assets/front/img/' . $bs->breadcrumb)); ?>" style="background-size:cover;">
            <div class="container">
            <div class="breadcrumb-txt">
                <div class="row">
                    <div class="col-xl-7 col-lg-8 col-sm-10">
                        <span><?php echo $__env->yieldContent('breadcrumb-title'); ?></span>
                        <h1><?php echo $__env->yieldContent('breadcrumb-subtitle'); ?></h1>
                        <ul class="breadcumb">
                        <li><a href="<?php echo e(route('front.index')); ?>"><?php echo e(__('Home')); ?></a></li>
                        <li><?php echo $__env->yieldContent('breadcrumb-link'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
            <div class="breadcrumb-area-overlay" style="background-color: #<?php echo e($be->breadcrumb_overlay_color); ?>;opacity: <?php echo e($be->breadcrumb_overlay_opacity); ?>;"></div>
        </div>
        <!--   breadcrumb area end    -->
      <?php endif; ?>


      <?php echo $__env->yieldContent('content'); ?>


      <!--    footer section start   -->
      <footer class="footer-section">
         <div class="container">
            <?php if(!($bex->home_page_pagebuilder == 0 && $bs->top_footer_section == 0)): ?>
            <div class="top-footer-section">
               <div class="row">
                  <div class="col-lg-4 col-md-12">
                     <div class="footer-logo-wrapper">
                        <a href="<?php echo e(route('front.index')); ?>">
                        <img class="lazy" data-src="<?php echo e(asset('assets/front/img/'.$bs->footer_logo)); ?>" alt="">
                        </a>
                     </div>
                     <p class="footer-txt">
                        <?php if(strlen($bs->footer_text) > 194): ?>
                           <?php echo e(mb_substr($bs->footer_text, 0, 194, 'UTF-8')); ?><span style="display: none;"><?php echo e(mb_substr($bs->footer_text, 194, null, 'UTF-8')); ?></span>
                           <a href="#" class="see-more"><?php echo e(__('see more')); ?>...</a>
                        <?php else: ?>
                           <?php echo e($bs->footer_text); ?>

                        <?php endif; ?>
                    </p>
                  </div>
                  <div class="col-lg-2 col-md-3">
                     <h4><?php echo e(__('Useful Links')); ?></h4>
                     <ul class="footer-links">
                        <?php $__currentLoopData = $ulinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ulink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <li><a href="<?php echo e($ulink->url); ?>"><?php echo e(convertUtf8($ulink->name)); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </ul>
                  </div>
                  <div class="col-lg-3 col-md-4">
                     <h4><?php echo e(__('Newsletter')); ?></h4>
                     <form class="footer-newsletter" id="footerSubscribeForm" action="<?php echo e(route('front.subscribe')); ?>" method="post">
                       <?php echo csrf_field(); ?>
                       <p><?php echo e(convertUtf8($bs->newsletter_text)); ?></p>
                       <input type="email" name="email" value="" placeholder="<?php echo e(__('Enter Email Address')); ?>" />
                       <p id="erremail" class="text-danger mb-0 err-email"></p>
                       <button type="submit"><?php echo e(__('Subscribe')); ?></button>
                     </form>
                  </div>
                  <div class="col-lg-3 col-md-5">
                     <h4><?php echo e(__('Contact Us')); ?></h4 >
                     <div class="footer-contact-info">
                        <ul>
                           <li><i class="fa fa-home"></i>
                            <?php
                            $addresses = explode(PHP_EOL, $bex->contact_addresses);
                            ?>
                            <span>
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($address); ?>

                                <?php if(!$loop->last): ?>
                                    |
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </span>
                           </li>

                           <li><i class="fa fa-phone"></i>
                            <?php
                             $phones = explode(',', $bex->contact_numbers);
                            ?>
                            <span>
                                <?php $__currentLoopData = $phones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($phone); ?>

                                <?php if(!$loop->last): ?>
                                    ,
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </span>
                          </li>
                           <li><i class="far fa-envelope"></i>
                            <?php
                             $mails = explode(',', $bex->contact_mails);
                            ?>
                            <span>
                                <?php $__currentLoopData = $mails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($mail); ?>

                                <?php if(!$loop->last): ?>
                                    ,
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </span>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
            <?php endif; ?>

            <?php if(!($bex->home_page_pagebuilder == 0 && $bs->copyright_section == 0)): ?>
            <div class="copyright-section">
               <div class="row">
                  <div class="col-sm-12 text-center">
                     <?php echo replaceBaseUrl(convertUtf8($bs->copyright_text)); ?>

                  </div>
               </div>
            </div>
            <?php endif; ?>
         </div>
      </footer>
      <!--    footer section end   -->


      
      <div id="WAButton"></div>

        <!--====== PRELOADER PART START ======-->
        <?php if($bex->preloader_status == 1): ?>
        <div id="preloader">
            <div class="loader revolve">
                <img src="<?php echo e(asset('assets/front/img/' . $bex->preloader)); ?>" alt="">
            </div>
        </div>
        <?php endif; ?>
        <!--====== PRELOADER PART ENDS ======-->

        <?php if($bex->is_shop == 1 && $bex->catalog_mode == 0): ?>
            <div id="cartIconWrapper">
                <a class="d-block" id="cartIcon" href="<?php echo e(route('front.cart')); ?>">
                    <div class="cart-length">
                        <i class="fas fa-cart-plus"></i>
                        <span class="length"><?php echo e(cartLength()); ?> <?php echo e(__('ITEMS')); ?></span>
                    </div>
                    <div class="cart-total">
                        <?php echo e($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''); ?>

                        <?php echo e(cartTotal()); ?>

                        <?php echo e($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''); ?>

                    </div>
                </a>
            </div>
        <?php endif; ?>

      <!-- back to top area start -->
      <div class="back-to-top">
         <i class="fas fa-chevron-up"></i>
      </div>
      <!-- back to top area end -->


      
      <?php if($be->cookie_alert_status == 1): ?>
      <?php echo $__env->make('cookie-consent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <?php endif; ?>
      

      
      <?php if ($__env->exists('front.partials.popups')) echo $__env->make('front.partials.popups', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      

      <?php
        $mainbs = [];
        $mainbs = json_encode($mainbs);
      ?>
      <script>
        var mainbs = <?php echo $mainbs; ?>;
        var mainurl = "<?php echo e(url('/')); ?>";
        var vap_pub_key = "<?php echo e(env('VAPID_PUBLIC_KEY')); ?>";
        var rtl = <?php echo e($rtl); ?>;
      </script>
      <!-- popper js -->
      <script src="<?php echo e(asset('assets/front/js/popper.min.js')); ?>"></script>
      <!-- bootstrap js -->
      <script src="<?php echo e(asset('assets/front/js/bootstrap.min.js')); ?>"></script>
      <!-- Plugin js -->
      <script src="<?php echo e(asset('assets/front/js/plugin.min.js')); ?>"></script>
      <!-- main js -->
      <script src="<?php echo e(asset('assets/front/js/main.js')); ?>"></script>
      <!-- pagebuilder custom js -->
      <script src="<?php echo e(asset('assets/front/js/common-main.js')); ?>" defer></script>

      
      <?php if($bex->is_whatsapp == 1): ?>
        <script type="text/javascript">
            var whatsapp_popup = <?php echo e($bex->whatsapp_popup); ?>;
            var whatsappImg = "<?php echo e(asset('assets/front/img/whatsapp.svg')); ?>";
            $(function () {
                $('#WAButton').floatingWhatsApp({
                    phone: "<?php echo e($bex->whatsapp_number); ?>", //WhatsApp Business phone number
                    headerTitle: "<?php echo e($bex->whatsapp_header_title); ?>", //Popup Title
                    popupMessage: `<?php echo nl2br($bex->whatsapp_popup_message); ?>`, //Popup Message
                    showPopup: whatsapp_popup == 1 ? true : false, //Enables popup display
                    buttonImage: '<img src="' + whatsappImg + '" />', //Button Image
                    position: "right" //Position: left | right

                });
            });
        </script>
      <?php endif; ?>
      <?php echo $__env->yieldContent('scripts'); ?>
      <?php echo $__env->yieldPushContent('event-js'); ?>

        <?php if(session()->has('success')): ?>
        <script>
            toastr["success"]("<?php echo e(__(session('success'))); ?>");
        </script>
        <?php endif; ?>

        <?php if(session()->has('error')): ?>
        <script>
            toastr["error"]("<?php echo e(__(session('error'))); ?>");
        </script>
        <?php endif; ?>

      <!--Start of subscribe functionality-->
      <script>
        $(document).ready(function() {
          $("#subscribeForm, #footerSubscribeForm").on('submit', function(e) {
            // console.log($(this).attr('id'));

            e.preventDefault();

            let formId = $(this).attr('id');
            let fd = new FormData(document.getElementById(formId));
            let $this = $(this);

            $.ajax({
              url: $(this).attr('action'),
              type: $(this).attr('method'),
              data: fd,
              contentType: false,
              processData: false,
              success: function(data) {
                // console.log(data);
                if ((data.errors)) {
                  $this.find(".err-email").html(data.errors.email[0]);
                } else {
                  toastr["success"]("You are subscribed successfully!");
                  $this.trigger('reset');
                  $this.find(".err-email").html('');
                }
              }
            });
          });


        });
      </script>
      <!--End of subscribe functionality-->

      <!--Start of Tawk.to script-->
      <?php if($bs->is_tawkto == 1): ?>
      <?php echo $bs->tawk_to_script; ?>

      <?php endif; ?>
      <!--End of Tawk.to script-->

      <!--Start of AddThis script-->
      <?php if($bs->is_addthis == 1): ?>
      <?php echo $bs->addthis_script; ?>

      <?php endif; ?>
      <!--End of AddThis script-->
   </body>
</html>
<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/default/layout.blade.php ENDPATH**/ ?>