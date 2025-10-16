<?php $__env->startSection('title','Departments'); ?>
<?php $__env->startSection('content'); ?>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Departments</h3>
                </div>
                <div class="col-sm-6">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department_create')): ?>
                    <a class="btn btn-primary float-right show-modal" style="float:right;"
                    href="javascript:void(0);" data-title="Add New" data-size="sm" data-action="<?php echo e(route('departments.create')); ?>">
                        Add New
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="clearfix"></div>

        <div class="card">
            <?php echo $__env->make('departments.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1509079.cloudwaysapps.com/jyujyqeajn/public_html/resources/views/departments/index.blade.php ENDPATH**/ ?>