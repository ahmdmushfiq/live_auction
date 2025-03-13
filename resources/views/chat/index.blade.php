@extends('layouts.master')

@section('title') Chat @endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .message {
        padding: 7px;
        border-radius: 5px;
        margin-bottom: 7px;
    }

    .text-left {
        text-align: left;
        background-color: #f1f1f1;
    }

    .text-right {
        text-align: right;
        background-color: #dbdbdb;
        color: rgb(8, 8, 8);
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    @if(auth()->user()->isAdmin())
    <h2>Chat with Bidder: {{ $bidder->name }}</h2>
    <div id="chat-box" style="max-height: 400px; overflow-y: scroll;">
        <div id="messages">
            @foreach ($messages as $message)
            <div class="">
                @if ($message->sender_id == Auth::user()->id)
                <div class="message text-right">
                    <strong>You:</strong> {{ $message->message }}
                    <br>
                    <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                </div>
                @else
                <div class="message text-left">
                    <strong>{{ $message->sender->name }}:</strong> {{ $message->message }}
                    <br>
                    <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <h2>Chat with Admin</h2>
    <div id="chat-box" style="max-height: 400px; overflow-y: scroll;">
        <div id="messages">
            @foreach ($messages as $message)
            <div class="">
                @if ($message->sender_id == Auth::user()->id)
                <div class="message text-right">
                    <strong>You:</strong> {{ $message->message }}
                    <br>
                        <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                </div>
                @else
                <div class="message text-left">
                    <strong>{{ $message->sender->name }}:</strong> {{ $message->message }}
                    <br>
                        <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="mb-3">
        <form action="{{ route('send-message') }}" method="POST" id="sendMessage">
            @csrf
            <textarea id="message-input" class="form-control" placeholder="Type your message..." rows="3"></textarea>
            <div class="d-flex justify-content-end mt-2 me-2">
                <button type="submit" class="btn btn-rounded btn-success d-flex align-items-center">
                    <i class="ri-send-plane-fill me-2"></i> Send
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js')}}"></script>
<script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;
    var pusher = new Pusher('58cf7a0b5055c60af6b9', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('chat-channel-{{ auth()->id() }}');

    channel.bind('new-message', function(data) {
        let timestamp = new Date(data.chat.created_at);
        let formattedTime = timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
        var messageHtml;
        if (data.chat.sender_id == {{ auth()->id() }}) {
            messageHtml = '<div><div class="message text-right"><strong>You:</strong> ' + data.chat.message + '<br><small class="text-muted">' + formattedTime + '</small></div></div>';
        } else {
            messageHtml = '<div><div class="message text-left"><strong>' + data.sender_name + ':</strong> ' + data.chat.message + '<br><small class="text-muted">' + formattedTime + '</small></div></div>';
        }
        $('#messages').append(messageHtml);
        
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    });

    $('#sendMessage').submit(function(e) {
        e.preventDefault();
        var message = $('#message-input').val();
        if(message == '') {
            return;
        }
        $.ajax({
            url: "{{ route('send-message') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: message,
                receiver_id: {{ $adminId ?? $bidderId }},
            },
            success: function(response) {
                if (response.status == 'success') {
                    $('#message-input').val('');
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });
</script>
@endsection