@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Email Updated</h1>
                    <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">

                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['recipient'] }},</span>

                        <p>Your {{ $data['APP_NAME'] }} account email address has been successfully updated to
                            {{ $data['new_email'] }}.
                        </p>

                        <p>If you did not make this update, please contact us at info@adagencycreatives.com
                        </p>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                                                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>

                        @include('emails.includes.jobboard_footer')