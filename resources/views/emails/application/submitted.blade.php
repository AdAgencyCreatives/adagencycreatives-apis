@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Application Submitted</h1>
                    <div style=" border-radius: 5px; max-width: 450px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['recipient'] }},</span>

                        <p>Your application for <a href="{{ $data['job_url'] }}"
                                target="_blank">{{ $data['job_title'] }}</a> has been submitted.
                        </p>

                        <p>We know the job search can be an uncertain journey and we’re rooting for you all the way.
                        </p>

                        @include('emails.includes.jobboard_footer')