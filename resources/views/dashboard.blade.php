@extends('layouts.master')
@section('title') @endsection
@section('css')
<link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<div class="container">
    <h2>Live Auctions</h2>

    @if($products->isEmpty())
    <p>No active auctions available at the moment.</p>
    @else
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4">
            <div class="card">
                @if ($product->hasMedia('product_image'))
                <img src="{{ $product->getFirstMediaUrl('product_image') }}" class="card-img-top" alt="Product Image"
                    style="height: 150px; object-fit: contain;">
                @else
                <img src="{{ URL::asset('assets/images/default.png') }}" class="card-img-top" alt="Default Image"
                    style="height: 150px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p>Starting Price: ${{ $product->starting_price }}</p>
                    <p>Current Price: <span id="current-price-{{ $product->id }}">{{ $product->current_price }}</span>
                    </p>
                    <p>Ends In: <span id="timer-{{ $product->id }}"></span></p>
                    @if(Auth::user()->isBidder())
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-primary bid-button" data-id="{{ $product->id }}">
                            Place Bid
                        </button>
                        <a href="{{ route('bids-show', $product->id) }}" class="btn btn-primary">Show Bidders</a>
                    </div>
                    @else
                    <a href="{{ route('bids-show', $product->id) }}" class="btn btn-primary">Show Bidders</a>
                    @endif
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                startCountdown({{ $product->id }}, "{{ $product->end_time }}");
            });
        </script>

        @endforeach
    </div>
    @endif
</div>

@include('products.bid-modal')


@endsection
@section('script')
<!-- apexcharts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js')}}"></script>
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
    $('.bid-button').click(function() {
        let productId = $(this).data('id');
        $('#product_id').val(productId);
        $('#bidModal').modal('show');
    });

    $('#bid-form').submit(function(e) {
        e.preventDefault();
        let productId = $('#product_id').val();
        let bidAmount = $('#bid-amount').val();
        $.ajax({
            url: "{{ route('place-bid') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                amount: bidAmount
            },
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    })
                   
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: xhr.responseJSON.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
        $('#bidModal').modal('hide');
        $('#bid-amount').val('');
        });



        // Function to start and update the countdown for each product
        function startCountdown(productId, endTime) {
            let auctionEndTime = new Date(endTime.replace(" ", "T")).getTime();

            function updateCountdown() {
                let now = new Date().getTime();
                let remaining = Math.max(0, auctionEndTime - now);

                let minutes = Math.floor(remaining / (1000 * 60));
                let seconds = Math.floor((remaining % (1000 * 60)) / 1000);

                let timerElement = document.getElementById("timer-" + productId);

                if (remaining <= 0) {
                    timerElement.innerText = "Auction Ended";
                    clearInterval(countdownIntervals[productId]); // Stop the countdown
                } else {
                    timerElement.innerText = minutes + "m " + seconds + "s";
                }
            }

            countdownIntervals[productId] = setInterval(updateCountdown, 1000);
            updateCountdown(); // Run immediately
        }

        // Store countdown intervals for each product
        var countdownIntervals = {};

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('58cf7a0b5055c60af6b9', {
        cluster: 'ap2'
        });

        var channel = pusher.subscribe('bids');
        channel.bind('new-bid', function(data) {
            var bidAmount = data.bid.amount;
            var productId = data.bid.product_id;
            var bidderId = data.bid.user_id;
            var productName = data.product_name;
            var authUserId = {{ Auth::id() }};
            var newEndTime = data.new_end_time;
            var previousHighestBidder = data.previous_highest_bidder; 
            $('#current-price-' + productId).text('$' + bidAmount);
            if (previousHighestBidder && previousHighestBidder !== bidderId && previousHighestBidder === authUserId) {
                alert("You have been outbid on product: " + productName);
            }
            if (newEndTime) {
                let updatedTime = new Date(newEndTime.replace(" ", "T")).getTime();
                clearInterval(countdownIntervals[productId]); // Stop the previous countdown
                startCountdown(productId, newEndTime); // Restart with the new end time
            }
        });

        
</script>
@endsection