<form method="POST" class="d-block ajaxForm" action="{{ route('hr.userlist_import_post') }}">
    @csrf
    <div class="form-row">

        <div class="fpb-7">
            <div class="mt-3">
                <?php

                foreach($roles as $key => $role){ ?>
                <?php if($role['name'] != 'superadmin' && $role['name'] != 'student' && $role['name'] != 'parent'): ?>


                <div class="form-check">
                    <input name="role_name[]" type="checkbox" class="form-check-input" id="customCheck1{{ $key }}" value="{{ $role['name'] }}">
                    <label class="form-check-label" for="customCheck1{{ $key }}">
                        {{ get_phrase(ucfirst($role['name'])) }}
                    </label>
                </div>



                <?php endif; ?>
                <?php } ?>
            </div>
        </div>
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">
                {{ get_phrase('Import user') }}
            </button>
        </div>
    </div>
</form>

<script>
    "use strict";
    $(document).ready(function () {

});
$(".ajaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, showAllUsers);
});
</script>
