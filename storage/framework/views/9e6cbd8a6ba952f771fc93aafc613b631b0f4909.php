<?php
use App\Models\Subject;
?>

<?php if(count($syllabuses) > 0): ?>
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th><?php echo e(get_phrase('Title')); ?></th>
            <th><?php echo e(get_phrase('Syllabus')); ?></th>
            <th><?php echo e(get_phrase('Subject')); ?></th>
            <th class="text-end"><?php echo e(get_phrase('Option')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($syllabuses as $syllabus):?>
            <tr>
                <td><?php echo e($syllabus['title']); ?></td>
                <td><a href="<?php echo e(asset('assets/uploads/syllabus')); ?>/<?php echo e($syllabus['file']); ?>" class="btn btn-primary btn-sm bi bi-download" download><?php echo e(get_phrase(' Download')); ?></a></td>
                <td>
                    <?php $subject= Subject::where('id' ,$syllabus['subject_id'])->first()->toArray(); ?>
                    <?php echo e($subject['name']); ?>

                </td>
                <td class="text-center">
                    <div class="adminTable-action">
                        <button
                          type="button"
                          class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                          data-bs-toggle="dropdown"
                          aria-expanded="false"
                        >
                          <?php echo e(get_phrase('Actions')); ?>

                        </button>
                        <ul
                          class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                        >
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('teacher.syllabus.delete', ['id' => $syllabus['id']])); ?>', 'undefined')"><?php echo e(get_phrase('Delete')); ?></a>
                          </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="syllabus_content">
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
        <br>
        <?php echo e(get_phrase('No data found')); ?>

    </div>
</div>
<?php endif; ?>

<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/syllabus/list.blade.php ENDPATH**/ ?>