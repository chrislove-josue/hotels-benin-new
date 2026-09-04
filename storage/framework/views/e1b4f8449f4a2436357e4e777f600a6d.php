<?php
    $active = $active ?? false;
?>
<a href="<?php echo e($href); ?>"
   class="group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition <?php echo e($active ? 'bg-hb-gold text-hb-blue-deeper font-semibold' : 'text-white/80 hover:bg-white/10'); ?>"
   :class="collapsed ? 'justify-center' : 'justify-start'">
    <i class="bi <?php echo e($icon); ?> text-lg"></i>
    <span class="whitespace-nowrap" x-show="!collapsed"><?php echo e($label); ?></span>
</a>
<?php /**PATH C:\Project\hotels-benin-new\resources\views\layouts\partials\sidebar-link.blade.php ENDPATH**/ ?>