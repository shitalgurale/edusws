<div class="eoff-form">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('accountant.create.inventory') }}">
        @csrf
        <div class="form-row">


            {{-- product name --}}
            <div class="fpb-7">
                <label for="quantity" class="eForm-label">{{ get_phrase('Product Name') }}
                    <span class="required">*</span>
                </label>

                <div>
                    <input class="form-control eForm-control" id="product_name" type="text" min="1"
                        name="product_name">
                </div>
            </div>

            {{-- inventory name --}}
            <div class="fpb-7">
                <label for="category_name" class="eForm-label">{{ get_phrase('Select an Inventory') }}</label>
                <select name="category_name" id="category_name"
                    class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select an inventory') }}</option>
                    @foreach ($inventory_category as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>


            {{-- inventory quantity --}}
            <div class="fpb-7">
                <label for="quantity" class="eForm-label">{{ get_phrase('Quantity') }}<span
                        class="required">*</span></label>
                <div>
                    <input class="form-control eForm-control" id="quantity" type="number" min="1"
                        name="quantity">
                </div>
            </div>


            {{-- inventory price --}}
            <div class="fpb-7">
                <label for="price" class="eForm-label">{{ get_phrase('Unit Price') }}<span
                        class="required">*</span></label>
                <div>
                    <input class="form-control eForm-control" id="price" type="number" min="1"
                        name="price">
                </div>
            </div>


            {{-- issue date --}}
            <div class="fpb-7">
                <label for="date" class="eForm-label">{{ get_phrase('Date') }}<span
                        class="required">*</span></label>
                <input type="text" class="form-control eForm-control inputDate" id="date" name="date"
                    value="{{ date('m/d/Y') }}" />
            </div>


            {{-- create button --}}
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Create') }}</button>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript">
    "use strict";


    function classWiseSubject(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response) {
                $('#subject_id').html(response);
            }
        });
    }

    $(function() {
        $('.inputDate').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format("YYYY"), 10),
            },
            function(start, end, label) {
                var years = moment().diff(start, "years");
            }
        );
    });

    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });
</script>
