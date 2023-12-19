@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; color: #000000; font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1
                        style="background: #fff; text-align: center; padding: 30px; border-bottom: 2px solid #000;     text-transform: uppercase;">
                        New Inquiry</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important;"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hello Member Support,</span>

                        <p>There is new inquiry message from someone.</p>

                        <div><b>Name: </b>{{ $data['name'] }}</div>
                        <div><b>Email: </b>{{ $data['email'] }}</div>
                        <div><b>Phone: </b>{{ $data['phone'] }}</div>
                        <div><b>Message: </b>{{ $data['message'] }}</div>
                        <div><b>Location: </b>{{ $data['location'] }}</div>

                        @include('emails.includes.jobboard_footer')
