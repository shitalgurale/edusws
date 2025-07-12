<form method="POST" class="d-block ajaxForm" action="{{ route('hr.user_role_create_post') }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">
                {{ get_phrase('Role name') }}
            </label>
            <input type="text" class="form-control eForm-control" id="name" name="name" placeholder="Provide role name">
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">
                {{ get_phrase('Create role') }}
            </button>
        </div>
    </div>
</form>

<script>
    "use strict";
    $(".ajaxForm").validate({}); 
$(".ajaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, showAllRole);
});

</script>
