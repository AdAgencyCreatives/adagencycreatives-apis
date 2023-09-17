@extends('layouts.app')

@section('title', 'Dashboard')

@section('scripts')
@vite('resources/js/app.js')
<script>
var user = @json(Auth::user());
setTimeout(() => {
    window.Echo.private('messanger.' + user.id)
        .listen('.private_msg', (e) => {
            console.log(e.data);
            displayReceivedMessage('Guido Tremblay', e.data.message)
        })
}, 200);

$('#sendMsgForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the values from the form
    var message = $('#message').val();
    var receiver = $('#receiver').val();
    var sender = user.uuid;
    var csrf = $("input[name='_token']").val();

    sendMessage('You', message);


    // Perform AJAX request
    $.ajax({
        url: '/api/v1/messages',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Authorization': "Bearer 1|NCBGp4lYiou2RD3hgfxWRImKrO6M9MFyJd0jEbdBe002974a"
        },
        data: {
            message: message,
            receiver_id: receiver,
            sender_id: sender
        },
        success: function(response) {
            // Handle the success response from the server
            console.log('Message sent successfully:', response);
            $("#message").val('');
        },
        error: function(xhr, status, error) {
            // Handle any errors that occur during the AJAX request
            console.error('Error sending message:', error);
        }
    });
});

function sendMessage(sender, message) {
    var chatMessages = document.getElementById('chatMessages');

    var messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message-right pb-4';
    messageDiv.innerHTML = `
        <div>
            <div class="text-muted small text-nowrap mt-2">${getCurrentTime()}</div>
        </div>
        <div class="flex-shrink-1 bg-light rounded py-2 px-3 me-3">
            <div class="fw-bold mb-1">${sender}</div>
            ${message}
        </div>
    `;

    chatMessages.appendChild(messageDiv);
}

function getCurrentTime() {
    // Return current time (you can replace this with your desired time format)
    var now = new Date();
    return now.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function displayReceivedMessage(sender, message) {
    var chatMessages = document.getElementById('chatMessages');

    var messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message-left pb-4';
    messageDiv.innerHTML = `
        <div>
            <div class="text-muted small text-nowrap mt-2">${getCurrentTime()}</div>
        </div>
        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ms-3">
            <div class="fw-bold mb-1">${sender}</div>
            ${message}
        </div>
    `;

    chatMessages.appendChild(messageDiv);
}
</script>
@endsection
@section('content')

<h1>User ID: {{ Auth::id() }}</h1>
<div class="card">
    <div class="row g-0">
        <div class="col-12 col-lg-5 col-xl-3 border-end list-group">

            <div class="px-4 d-none d-md-block">
                <div class="d-flex align-items-start align-items-center">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control my-3" placeholder="Search...">
                    </div>
                </div>
            </div>

            <a href="#" class="list-group-item list-group-item-action border-0 ml-3">
                <div class="badge bg-success float-end">5</div>
                <div class="d-flex align-items-start">

                    <div class="flex-grow-1 ms-3">
                        Marilie Okuneva
                        <div class="small"><span class="fas fa-circle chat-online"></span> Online</div>
                    </div>
                </div>
            </a>


            <hr class="d-block d-lg-none mt-1 mb-0">
        </div>
        <div class="col-12 col-lg-7 col-xl-9">
            <div class="py-2 px-4 border-bottom d-none d-lg-block">
                <div class="d-flex align-items-start align-items-center py-1">
                    <div class="position-relative">

                    </div>
                    <div class="flex-grow-1 ps-3">
                        <strong>Marilie Okuneva</strong>
                        <!-- <div class="text-muted small"><em>Typing...</em></div> -->
                    </div>

                </div>
            </div>

            <div class="position-relative">
                <div class="chat-messages p-4" id="chatMessages">
                </div>
            </div>

            <div class="flex-grow-0 py-3 px-4 border-top">
                <form id="sendMsgForm">
                    @csrf()
                    <div class="input-group">
                        <input type="text" class="form-control" id="message" placeholder="Type your message">
                        <input type="hidden" id="receiver" value="2a697fb5-3e83-3359-81f0-4594d284d2e6">
                        <button id="sendMsgBtn" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection