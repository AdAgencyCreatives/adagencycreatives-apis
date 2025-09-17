@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 1.5;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Registration Not Supported</h1>
                    <div style=" border-radius: 5px; max-width: 900px; margin: 0 auto;  line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p style="">We appreciate your interest in joining <a href="{{ $data['FRONTEND_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a>. This platform is designed to support
                            advertising art directors, designers, and copywriters in the United States.
                            Your registration is reviewed for relevant experience and your portfolio's alignment with
                            opportunities we share. Unfortunately, the following account has not been approved at this
                            time.
                        </p>

                        <h4 style="text-decoration: underline; margin-bottom: 3px;">Details:</h4>
                        <div><b>User name: </b>{{ $data['user']->username ?? '' }}</div>
                        <div><b>Email: </b>{{ $data['user']->email ?? '' }}</div>

                        <div style="margin-top: 20px;">If you have any questions about this decision,
                            reach out to <a href="mailto:info@adagencycreatives.com">info@adagencycreatives.com</a>. Our
                            team receives many inquiries and requests, so please allow a few business days for us to
                            respond.
                        </div>
                        <p>Explore more jobs and update your preferences anytime by visiting your dashboard.</p>
                        <p>Cheers,<br>
                            The Ad Agency Creatives Team.</p>
                        @include('emails.includes.jobboard_footer')