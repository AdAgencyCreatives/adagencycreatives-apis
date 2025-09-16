@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading"> Job Invitation</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">
                            Hi
                            {{ $data['receiver_name'] }},</span>


                        <p>{{ $data['agency_name'] }} has discovered your profile on <a href="{{ $data['APP_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a> and
                            apparently liked what they saw! They are inviting you to apply for their
                            {{ $data['job_title'] }}
                            role. Nice work!
                        </p>


                        <p><a href="{{ $data['job_url'] }}" target="_blank">Click here to apply</a> or learn more about
                            the position.
                        </p>

                        @include('emails.includes.jobboard_footer')