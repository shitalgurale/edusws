<style type="text/css">
    .cursor-pointer{
        cursor: pointer;
    }
</style>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row" id = "parent-div" data-plugin="dragula" data-containers='["section-list"]'>
                        <div class="col-md-12">
                            <div class="bg-dragula p-2 p-lg-4">
                                <h5 class="mt-0 d-flex justify-content-between align-items-center"><?php echo get_phrase('List of Sections'); ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-rounded alignToTitle" id = "section-sort-btn" onclick="sort('{{ $course_id }}')" name="button"><?php echo get_phrase('Update Sorting'); ?></button>
                                </h5>
                                <div id="section-list" class="py-2 draggable-items">
                                    <?php foreach ($course_sections as $section): ?>
                                        <!-- Item -->
                                        <div class="card mb-0 mt-2 draggable-item" id ="<?php echo $section['id']; ?>">
                                            <div class="card-body cursor-pointer">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h5 class="mb-1 mt-0 bg-white"><?php echo $section['title']; ?></h5>
                                                    </div> <!-- end media-body -->
                                                </div> <!-- end media -->
                                            </div> <!-- end card-body -->
                                        </div> <!-- end col -->
                                    <?php endforeach; ?>
                                </div> <!-- end company-list-1-->
                            </div> <!-- end div.bg-light-->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>


 <!-- Sortable Js Dragula js -->

<script>
   
   $('.draggable-items').sortable();
   
</script>

<script>
    function sort(id) {
        var containerArray = ['section-list'];
        var itemArray = [];
        var itemJSON;
        for(var i = 0; i < containerArray.length; i++) {
            $('#'+containerArray[i]).each(function () {
                $(this).find('.draggable-item').each(function() {
                    itemArray.push(this.id);
                });
            });
        }

        itemJSON = JSON.stringify(itemArray);
        var url = "{{ route('teacher.addons.section_sort_update', ['id' => ":id"]) }}";
        url = url.replace(":id", id);
        console.log(itemJSON);
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            data : {
                itemJSON : itemJSON
            },
            success: function(response)
            {
                toastr.success('<?php  echo get_phrase('Sections have been Sorted'); ?>');
                setTimeout(function(){
                    location.reload();
                }, 1000);
            },
            error: function (xhr) {
                var err = JSON.parse(xhr.responseText);
                alert(err.message);
            }
        });
    }
    onDomChange(function(){
        $('#section-sort-btn').show();
    });
</script>



