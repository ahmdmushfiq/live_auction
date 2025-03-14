@extends('layouts.master')
@section('title') @endsection
@section('css')
<link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
    type="text/css" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.css">
<link href="
        https://cdn.jsdelivr.net/npm/filepond@4.30.6/dist/filepond.min.css
        " rel="stylesheet">
@endsection
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="p-3">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @elseif (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                @elseif (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
                @endif
            </div>
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    Products
                </h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addProductModal">Add Product</button>
            </div>
            <div class="card-body">
                <table id="productTable" class="table table-bordered dt-responsive nowrap align-middle"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Starting Price</th>
                            <th>Auction End Time</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@include('products.create-product-modal')
@include('products.edit-product-modal')

@endsection
@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js" type="text/javascript"></script>

<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/filepond@4.30.6/dist/filepond.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-filepond@1.0.0/filepond.jquery.min.js"></script>
<script
    src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.js">
</script>
<script
    src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-exif-orientation@1.0.11/dist/filepond-plugin-image-exif-orientation.min.js">
</script>
<script src="
        https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2.2.8/dist/filepond-plugin-file-validate-size.min.js
        "></script>
<script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-encode@2.1.14/dist/filepond-plugin-file-encode.min.js">
</script>
<script>
    $(document).ready(function () {
        loadData();
        FilePond.registerPlugin(
                // encodes the file as base64 data
                FilePondPluginFileEncode,
                // validates the size of the file
                FilePondPluginFileValidateSize,
                // corrects mobile image orientation
                FilePondPluginImageExifOrientation,
                // previews dropped images
                FilePondPluginImagePreview
            );
            $('.image').filepond({
                credits: false,
                allowImagePreview: true, // Enable image preview
                allowFileTypeValidation: true, // Validate file types if needed
                allowFileSizeValidation: true, // Validate file size if needed
                acceptedFileTypes: ['image/*'], // Restrict to images
                maxFileSize: '3MB' // Example: limit file size to 5MB
            });
    });

     function loadData() {
            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                stateSave: true,
                pageLength: 50,
                "scrollX": true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                header: {
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                ajax: {
                    url: "{{ route('products.index') }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'starting_price',
                        name: 'starting_price',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        }

    $(document).on('click', '.edit-btn', function (e) {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('products.edit', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function (response) {
                if(response.status == 'success') {
                    $('#edit_product_id').val(response.data.id);
                    $('#edit_name').val(response.data.name);
                    $('#edit_description').val(response.data.description);
                    $('#edit_price').val(response.data.starting_price);
                    $('#edit_end_time').val(response.data.end_time);                
                    $('.edit_image').filepond({
                        credits: false,
                        allowImagePreview: true,
                        allowFileTypeValidation: true,
                        allowFileSizeValidation: true,
                        acceptedFileTypes: ['image/*'],
                        maxFileSize: '3MB',
                        files: response.product_image ? [{ source: response.product_image }] : []
                    });
                    $('#editProductModal').modal('show');
                }
            }
        });
    })

    $(document).on('click', '.delete-btn', function (e) {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('products.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    data: {
                    _token: "{{ csrf_token() }}"  // Add the CSRF token here
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            Swal.fire(
                                'Deleted!',
                                'Product has been deleted.',
                                'success'
                            )
                            $('#productTable').DataTable().ajax.reload();
                        }
                    }
                });
            }
        })
    })

    $('.close-modal').on('click', function () {
        // location.reload();
        alert('test');
    });
</script>
@endsection