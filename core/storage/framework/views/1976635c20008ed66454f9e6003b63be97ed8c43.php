<li class="menu-item menu-item-has-children static mega-dropdown d-lg-none d-block"><a href="<?php echo e($href); ?>"><?php echo e($link["text"]); ?></a>
    <ul class="mega-menu">
        <li class="mega-wrap">
                <?php if($catAvailable): ?>

                    <a href="<?php echo e($allUrl); ?>" data-tabid="all"><?php echo e(__('All')); ?></a>
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
                        <a href="<?php echo e($catUrl); ?>" data-tabid="#megaTab<?php echo e($link["type"]); ?><?php echo e($mcat->id); ?>"><?php echo e($mcat->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php endif; ?>

                <?php
                    if ($catAvailable) {
                        $colClass = 'col-lg-10';
                    } elseif (!$catAvailable) {
                        $colClass = 'col-lg-12';
                    }
                ?>
                    <?php if(!$catAvailable): ?>
                        <?php $__currentLoopData = $megaMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

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
                            <a href="<?php echo e($detailsUrl); ?>"><?php echo e(strlen($mItem->title) > 30 ? mb_substr($mItem->title,0,30,'utf-8') . '...' : $mItem->title); ?></a>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
        </li>
    </ul>
</li>
<?php /**PATH /Users/samiulalimpratik/Sites/plusagency/plusagency/core/resources/views/front/partials/mobile-mega-menu.blade.php ENDPATH**/ ?>