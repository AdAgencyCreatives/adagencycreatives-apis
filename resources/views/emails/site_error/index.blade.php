@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Site Error Notification</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello Admin,</span>

                        <p>There is an error occurred on the site.</p>

                        <div><b>URL: </b>{{ $data['url'] }}</div>
                        <div><b>Error Message: </b>{{ $data['error_message'] }}</div>
                        <div><b>Date/Time: </b>{{ $data['date_time'] }}</div>
                        <div><b>IP Address: </b>{{ $data['ip_address'] }}</div>
                        <div><b>User Agent: </b>{{ $data['user_agent'] }}</div>

                        <p style="">&nbsp;</p>
                        @include('emails.includes.jobboard_footer')