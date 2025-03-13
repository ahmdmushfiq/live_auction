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
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    Bidders
                </h4>
            </div>
            <div class="card-body">
                <table id="bidderTable" class="table table-bordered dt-responsive nowrap align-middle"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Top Amount Bidded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bidders as $bidder)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $bidder->name }}</td>
                            <td>{{ $bidder->top_bid }}</td>
                            <td>
                                <button type="button" class="btn btn-primary view-bid"
                                    data-id="{{ $bidder->user_id }}">View Bids</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="bidModal" tabindex="-1" aria-labelledby="bidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bidModalLabel">Bids</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bidTable">
                    <table class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="bidTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script>
    $('.view-bid').on('click', function() {
        var bidderId = $(this).data('id');
        var productId = '{{ $product->id }}';
        
        $.ajax({
            url: "{{ route('bidder-bids') }}",
            data: {
                bidderId: bidderId,
                productId: productId
            },
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    $('#bidTable tbody').empty();
                    $.each(response.data, function (key, value) {
                        var row = '<tr>' +
                            '<td>' + (key + 1) + '</td>' +
                            '<td>' + value.amount + '</td>' +
                            '</tr>';
                        $('#bidTable tbody').append(row);
                    });
                }
                $('#bidModal').modal('show');
            }
        });
    });
            
</script>
@endsection