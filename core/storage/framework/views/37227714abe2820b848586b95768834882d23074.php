
<?php
    $catAvailable = true;
    if ($link["type"] == 'services-megamenu' && serviceCategory()) {
        $data = $currentLang->megamenus()->where('type', 'services')->where('category', 1);
        $cats = $currentLang->scategories()->where('status', 1)->orderBy('serial_number', 'ASC')->get();
        $catModel = '\App\Scategory';
        $itemModel = '\App\Service';
        $allUrl = route("front.services");
    } elseif ($link["type"] == 'products-megamenu') {
        $data = $currentLang->megamenus()->where('type', 'products')->where('category', 1);
        $cats = $currentLang->pcategories()->where('status', 1)->get();
        $catModel = '\App\Pcategory';
        $itemModel = '\App\Product';
        $allUrl = route("front.product");
    } elseif ($link["type"] == 'portfolios-megamenu' && serviceCategory()) {
        $data = $currentLang->megamenus()->where('type', 'portfolios')->where('category', 1);
        $cats = $currentLang->scategories()->where('status', 1)->get();
        $catModel = '\App\Scategory';
        $itemModel = '\App\Portfolio';
        $allUrl = route('front.portfolios');
    } elseif ($link["type"] == 'services-megamenu' && !serviceCategory()) {
        $data = $currentLang->megamenus()->where('type', 'services')->where('category', 0);
        $itemModel = '\App\Service';
        $catAvailable = false;
    } elseif ($link["type"] == 'portfolios-megamenu' && !serviceCategory()) {
        $data = $currentLang->megamenus()->where('type', 'portfolios')->where('category', 0);
        $itemModel = '\App\Portfolio';
        $catAvailable = false;
    } elseif ($link["type"] == 'courses-megamenu') {
        $data = $currentLang->megamenus()->where('type', 'courses')->where('category', 1);
        $cats = $currentLang->course_categories()->where('status', 1)->get();
        $catModel = '\App\CourseCategory';
        $itemModel = '\App\Course';
        $allUrl = route("courses");
    } elseif ($link["type"] == 'causes-megamenu') {
        $data = $currentLang->megamenus()->where('type', 'causes')->where('category', 0);
        $itemModel = '\App\Donation';
        $catAvailable = false;
    } elseif ($link["type"] == 'events-megamenu') {
        $data = $currentLang->megamenus()->where('type', 'events')->where('category', 1);
        $cats = $currentLang->event_categories()->where('status', 1)->get();
        $catModel = '\App\EventCategory';
        $itemModel = '\App\Event';
        $allUrl = route("front.events");
    } elseif ($link["type"] == 'blogs-megamenu') {
        $data = $currentLang->megamenus()->where('type', 'blogs')->where('category', 1);
        $cats = $currentLang->bcategories()->where('status', 1)->get();
        $catModel = '\App\Bcategory';
        $itemModel = '\App\Blog';
        $allUrl = route("front.blogs");
    }

    if ($data->count() > 0) {
        $megaMenus = $data->first()->menus;
        $megaMenus = json_decode($megaMenus, true);
    } else {
        $megaMenus = [];
    }
    // dd($megaMenus);
?>

<?php if ($__env->exists('front.partials.mobile-mega-menu')) echo $__env->make('front.partials.mobile-mega-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<li class="mega-dropdown d-none d-lg-inline-block">
    <a class="dropbtn" href="<?php echo e($href); ?>"><?php echo e($link["text"]); ?> <i class="fas fa-angle-down"></i></a>
    <div class="mega-dropdown-content">
        <div class="row">
            <?php if($catAvailable): ?>
                <div class="col-lg-2">
                    <div class="megamenu-cats">
                        <ul>
                            <li class="active"><a href="<?php echo e($allUrl); ?>" data-tabid="all"><?php echo e(__('All')); ?></a></li>
                            <?php $__currentLoopData = $megaMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mCatId => $mItemIds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $mcat = $catModel::where('id', $mCatId);
                                    if ($mcat->count() == 0) {
                                        continue;
                                    } else {
                                        $mcat = $mcat->first();
                                    }

                                    if ($link["type"] == 'services-megamenu') {
                                        $catUrl = route('front.services', ['category' => $mcat->id, 'term'=>request()->input('term')]);
                                    } elseif ($link["type"] == 'products-megamenu') {
                                        $catUrl = route('front.product', ['category_id' => $mcat->id]);
                                    } elseif ($link["type"] == 'portfolios-megamenu') {
                                        $catUrl = route('front.portfolios', ['category' => $mcat->id]);
                                    } elseif ($link["type"] == 'courses-megamenu') {
                                        $catUrl = route('courses', ['category_id' => $mcat->id]);
                                    } elseif ($link["type"] == 'events-megamenu') {
                                        $catUrl = route('front.events', ['category' => $mcat->id]);
                                    } elseif ($link["type"] == 'blogs-megamenu') {
                                        $catUrl = route('front.blogs', ['category' => $mcat->slug]);
                                    }
                                ?>
                                <li><a href="<?php echo e($catUrl); ?>" data-tabid="#megaTab<?php echo e($link["type"]); ?><?php echo e($mcat->id); ?>"><?php echo e($mcat->name); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php
                if ($catAvailable) {
                    $colClass = 'col-lg-10';
                } elseif (!$catAvailable) {
                    $colClass = 'col-lg-12';
                }
            ?>
            <div class="<?php echo e($colClass); ?>">

                <?php if($catAvailable): ?>
                    <?php $__currentLoopData = $megaMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mCatId => $mItemIds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $mcat = $catModel::where('id', $mCatId);
                            if ($mcat->count() == 0) {
                                continue;
                            } else {
                                $mcat = $mcat->first();
                            }

                            if ($link["type"] == 'services-megamenu') {
                                $catUrl = route('front.services', ['category' => $mcat->id, 'term'=>request()->input('term')]);
                            } elseif ($link["type"] == 'products-megamenu') {
                                $catUrl = route('front.product', ['category_id' => $mcat->id]);
                            } elseif ($link["type"] == 'portfolios-megamenu') {
                                $catUrl = route('front.portfolios', ['category' => $mcat->id]);
                            } elseif ($link["type"] == 'courses-megamenu') {
                                $catUrl = route('courses', ['category_id' => $mcat->id]);
                            } elseif ($link["type"] == 'events-megamenu') {
                                $catUrl = route('front.events', ['category' => $mcat->id]);
                            } elseif ($link["type"] == 'blogs-megamenu') {
                                $catUrl = route('front.blogs', ['category' => $mcat->slug]);
                            }
                        ?>
                        <div class="mega-tab" id="megaTab<?php echo e($link["type"]); ?><?php echo e($mCatId); ?>">
                            <h3 class="category">
                                <a href="<?php echo e($catUrl); ?>"><?php echo e($mcat->name); ?></a>
                            </h3>
                            <div class="row">
                                <?php $__currentLoopData = $mItemIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $mItem = $itemModel::where('id', $mItemId);
                                        if ($mItem->count() == 0) {
                                            continue;
                                        } else {
                                            $mItem = $mItem->first();
                                        }
                                        if ($link['type'] == 'services-megamenu') {
                                            $detailsUrl = route('front.servicedetails', [$mItem->slug]);
                                            $imgSrc = asset('assets/front/img/services/' . $mItem->main_image);
                                        } elseif ($link["type"] == 'products-megamenu') {
                                            $detailsUrl = route('front.product.details',$mItem->slug);
                                            $imgSrc = asset('assets/front/img/product/featured/' . $mItem->feature_image);
                                        } elseif ($link["type"] == 'portfolios-megamenu') {
                                            $detailsUrl = route('front.portfoliodetails',[$mItem->slug]);
                                            $imgSrc = asset('assets/front/img/portfolios/featured/' . $mItem->featured_image);
                                        } elseif ($link["type"] == 'courses-megamenu') {
                                            $detailsUrl = route('course_details',[$mItem->slug]);
                                            $imgSrc = asset('assets/front/img/courses/' . $mItem->course_image);
                                        } elseif ($link["type"] == 'events-megamenu') {
                                            $eventImg = json_decode($mItem->image, true);
                                            $detailsUrl = route('front.event_details',[$mItem->slug]);
                                            $imgSrc = !empty($eventImg) ? asset('assets/front/img/events/sliders/' . $eventImg[0]) : '';
                                        } elseif ($link["type"] == 'blogs-megamenu') {
                                            $detailsUrl = route('front.blogdetails',[$mItem->slug]);
                                            $imgSrc = asset('assets/front/img/blogs/' . $mItem->main_image);
                                        }
                                    ?>
                                    <div class="col-lg-3">
                                        <div class="single-item">
                                            <div class="thumb">
                                                <a href="<?php echo e($detailsUrl); ?>" class="d-block">
                                                    <img class="lazy" data-src="<?php echo e($imgSrc); ?>" alt="Megamenu Image" style="width: 100%;">
                                                </a>
                                            </div>
                                            <div class="title">
                                                <a href="<?php echo e($detailsUrl); ?>"><?php echo e(strlen($mItem->title) > 30 ? mb_substr($mItem->title,0,30,'utf-8') . '...' : $mItem->title); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php elseif(!$catAvailable): ?>
                    <?php $__currentLoopData = $megaMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($loop->iteration % 5 == 1): ?>
                        <div class="row">
                        <?php endif; ?>
                            <?php
                                $mItem = $itemModel::where('id', $mItemId);
                                if ($mItem->count() == 0) {
                                    continue;
                                } else {
                                    $mItem = $mItem->first();
                                }
                                if ($link['type'] == 'services-megamenu') {
                                    $detailsUrl = route('front.servicedetails', [$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/services/' . $mItem->main_image);
                                } elseif ($link['type'] == 'portfolios-megamenu') {
                                    $detailsUrl = route('front.portfoliodetails',[$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/portfolios/featured/' . $mItem->featured_image);
                                } elseif ($link["type"] == 'causes-megamenu') {
                                    $detailsUrl = route('front.cause_details',[$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/donations/' . $mItem->image);
                                }
                            ?>
                            <div class="col">
                                <div class="single-item">
                                    <div class="thumb">
                                        <a href="<?php echo e($detailsUrl); ?>" class="d-block">
                                            <img class="lazy" data-src="<?php echo e($imgSrc); ?>" alt="Megamenu Image" style="width: 100%;">
                                        </a>
                                    </div>
                                    <div class="title">
                                        <a href="<?php echo e($detailsUrl); ?>"><?php echo e(strlen($mItem->title) > 30 ? mb_substr($mItem->title,0,30,'utf-8') . '...' : $mItem->title); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php if($loop->last): ?>
                                <?php
                                    $left = 5 - (count($megaMenus) % 5);
                                ?>
                                <?php if($left < 5): ?>
                                    <?php for($i=0; $i < $left; $i++): ?>
                                        <div class="col"></div>
                                    <?php endfor; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php if($loop->iteration % 5 == 0): ?>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</li>

<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/default/partials/mega-menu.blade.php ENDPATH**/ ?>