@extends('accountant.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Inventory Manager') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Inventory Manager') }}</a></li>
                        </ul>
                    </div>

                    {{-- add new inventory button --}}
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn bi bi-plus"
                            onclick="rightModal('{{ route('accountant.inventory.create.modal') }}', '{{ get_phrase('Create Inventory') }}')">{{ get_phrase('Add Inventory') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap">
                {{-- table list --}}

                <div class="d-flex justify-content-between align-items-center  position-relative">
                    <form method="post" enctype="multipart/form-data" class="d-block flex-grow-1 ajaxForm pt-3"
                        action="{{ route('accountant.inventory.filter') }}">
                        @csrf
                        <div class="row">
                            <div class="row justify-content-center">
                                <div class="col-xl-4 mb-3">
                                    <input type="text" class="form-control eForm-control" name="eDateRange"
                                        value="{{ date('m/d/Y', $date_start) . ' - ' . date('m/d/Y', $date_end) }}">
                                </div>


                                <div class="col-xl-2 mb-3">
                                    <button type="submit" class="eBtn eBtn btn-secondary form-control"
                                        name="filter">{{ get_phrase('Filter') }}</button>
                                </div>

                            </div>
                        </div>
                    </form>


                    {{-- export button --}}
                    <button class="eBtn-3 dropdown-toggle float-end" type="button" id="defaultDropdown"
                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <span class="pr-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77"
                                viewBox="0 0 10.771 12.31">
                                <path id="arrow-right-from-bracket-solid"
                                    d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                                    transform="translate(0 12.31) rotate(-90)" fill="#00a3ff"></path>
                            </svg>
                        </span>
                        {{ get_phrase('Export') }}
                    </button>

                    {{-- dropdown menu --}}
                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 position-absolute right-0">
                        <li>
                            <a class="dropdown-item" id="pdf" href="javascript:;"
                                onclick="Export()">{{ get_phrase('PDF') }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item" id="print" href="javascript:;"
                                onclick="printableDiv('inventory_export')">{{ get_phrase('Print') }}</a>
                        </li>
                    </ul>
                </div>


                @if (count($inventory) > 0)
                    <div class="card-body" id="inventory_export">
                        <div class="table-responsive tScrollFix pb-2">
                            <table class="table eTable">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Product Name') }}</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Category') }}</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Quantity') }}</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Unit Price') }}</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Total Price') }}</th>
                                        <th scope="col" class="text-center">{{ get_phrase('Date') }}</th>
                                        <th scope="col" class="text-end">{{ get_phrase('Action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($inventory as $key => $display)
                                        <tr>

                                            <th>{{ $inventory->firstItem() + $key }}</th>
                                            <td class="text-center">{{ $display['product_name'] }}</td>
                                            <td class="text-center"><b>{{ $display['category_name'] }}</b></td>
                                            <td class="text-center">{{ $display['quantity'] }}</td>
                                            <td class="text-center">{{ $display['price'] }}</td>
                                            <td class="text-center">{{ $display['total_price'] }}</td>
                                            <td class="text-center">{{ date('m-d-y', $display['date']) }}</td>


                                            {{-- action button --}}
                                            <td>
                                                <div class="adminTable-action">
                                                    <button type="button"
                                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        {{ get_phrase('Actions') }}
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                        <li>

                                                            {{-- inventory edit button --}}
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="rightModal('{{ route('accountant.inventory.edit', $display['id']) }}', 'Edit Inventory')">{{ get_phrase('Edit') }}</a>


                                                            {{-- sell inventory --}}
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="rightModal('{{ route('accountant.inventory.sell.modal', $display['id']) }}', 'Sell Inventory')">{{ get_phrase('Sell') }}</a>


                                                            {{-- inventory delete button --}}
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="confirmModal('{{ route('accountant.inventory.delete', $display['id']) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- pagination 10 --}}
                            <div
                                class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                                <p class="admin-tInfo">
                                    {{ get_phrase('Showing') . ' 1 - ' . count($inventory) . ' ' . get_phrase('from') . ' ' . $inventory->total() . ' ' . get_phrase('data') }}
                                </p>
                                <div class="admin-pagi">
                                    {!! $inventory->appends(request()->all())->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@else
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('public/assets/images/empty_box.png') }}" />
        <br>
        <span class="">{{ get_phrase('No data found') }}</span>
    </div>
    @endif


    <script type="text/javascript">
        "use strict";

        function filter_class() {
            var class_id = $('#class_id').val();
            if (class_id != "") {
                showAllExams();
            } else {
                toastr.error('{{ 'Please select a class' }}');
            }
        }

        var showAllExams = function() {
            var class_id = $('#class_id').val();
            let url = "{{ route('admin.class_wise_exam_list', ['id' => ':class_id']) }}";
            url = url.replace(":class_id", class_id);
            if (class_id != "") {
                $.ajax({
                    url: url,
                    success: function(response) {
                        $('.exam_content').html(response);
                    }
                });
            }
        }


        function downloadCSVFile(csv, filename) {
            var csv_file, download_link;

            csv_file = new Blob([csv], {
                type: "text/csv"
            });

            download_link = document.createElement("a");

            download_link.download = filename;

            download_link.href = window.URL.createObjectURL(csv_file);

            download_link.style.display = "none";

            document.body.appendChild(download_link);

            download_link.click();

        }

        document.getElementById("download-button").addEventListener("click", function() {
            var html = document.querySelector("#offline_exam_export").outerHTML;
            htmlToCSV(html, "offline_exam.csv");
        });


        function htmlToCSV(html, filename) {
            var data = [];
            var rows = document.querySelectorAll("#offline_exam_export tr");
            for (var i = 0; i < rows.length; i++) {
                var row = [],
                    cols = rows[i].querySelectorAll("td, th");


                for (var j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                    console.log(cols[j].innerText)

                }

                data.push(row.join(","));

            }
            downloadCSVFile(data.join("\n"), filename);
        }

        function Export() {
            html2canvas(document.getElementById('inventory_export'), {
                onrendered: function(canvas) {
                    var data = canvas.toDataURL();
                    var docDefinition = {
                        content: [{
                            image: data,
                            width: 500
                        }]
                    };
                    pdfMake.createPdf(docDefinition).download("inventory_export.pdf");
                }
            });
        }

        function printableDiv(printableAreaDivId) {
            var printContents = document.getElementById(printableAreaDivId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
