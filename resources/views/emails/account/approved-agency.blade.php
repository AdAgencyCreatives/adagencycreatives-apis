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
                        Account Activated</h1>
                    <div style="background:#fff; border-radius: 5px; max-width: 450px; margin: 0 auto; color:#000000; line-height:1.5 !important"
                        class="content">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $data['user']->first_name ?? '' }},</span>

                        <p>Welcome to the <a href="{{ $data['FRONTEND_URL'] }}"
                                target="_blank">{{ $data['APP_NAME'] }}</a> community.
                        </p>

                        <div style="">To help you get the most out of your account, you’ll want to
                            first complete <a href="{{ $data['FRONTEND_URL'] }}/profile" target="_blank">your Agency's
                                Profile.</a>
                            You can start by telling everyone a bit about your agency and speciality.</div>
                        <div style="margin-top: 20px;">Once you’ve got your profile looking good, take a moment to
                            adjust
                            your preferences. After that,
                            you’re ready to post jobs and find amazing talent for your team.</div>
                        <div style="margin-top: 20px;">If you forget your password, no problem. You can <a
                                href="{{ $data['FRONTEND_URL'] }}/reset-password" target="_blank">reset it
                                here.</a>

                        </div>

                        <p>
                            Thanks,<br />
                            Member Support<br />
                            <img width="350"
                                src="https://ad-agency-creatives.s3.us-east-1.amazonaws.com/agency_logo/ad-agency-creatives-bottom-logo-bold.png"
                                alt="" />
                        </p>
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width: 20%;">&nbsp;</td>
                                <td style="width: 60%;">
                                    <p style="text-align: center; margin-bottom: 0;">Gather. Inspire. Do Cool $#*t!</p>
                                </td>
                                <td style="width: 20%;">
                                    <img class="footer-logo"
                                        style="display: block; width: 120px; position: absolute; right: 15px; bottom: 15px;"
                                        src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/aac-logo-round-transparent-bold.png">
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- 1 Column Text : BEGIN -->

</table>
<!-- Email Body : END -->

<!-- Email Footer : BEGIN -->
<br>
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="left" width="100%"
    style="max-width: 600px; border-radius: 5px;" bgcolor="#000000" class="footer_bg">
    <tr>
        <td style="padding: 15px; width: 100%; font-size: 14px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px; text-align: left; color: #ffffff; word-break: break-all;"
            class="footer_text_color footer_text_size">
            <div style="text-align:center;">
                <div>© {{ date('Y') }} Ad Agency Creatives. All Rights Reserved.</div>
                <div style="display:flex; gap:15px; justify-content:center; margin-top:5px;" id="footer">
                    <a style="color: #ffffff;" href="https://adagencycreatives.com/privacy-policy/">Privacy Policy</a>
                    <a style="color: #ffffff;" href="https://adagencycreatives.com/terms-and-conditions/">User
                        Agreement</a>
                    <a style="color: #ffffff;" href="https://adagencycreatives.com/contact-us/">Contact Us</a>
                </div>
            </div>
        </td>
    </tr>
</table>
</div>
</center>
</td>
</tr>
</table>
</body>

</html>
