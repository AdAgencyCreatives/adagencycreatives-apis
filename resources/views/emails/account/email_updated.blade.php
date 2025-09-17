@include('emails.includes.jobboard_header')

<!-- 1 Column Text : BEGIN -->
<tr>
    <td>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="padding: 0 0 30px; font-family: sans-serif; mso-height-rule: exactly; line-height: 14px;  font-size: 14px; position: relative;"
                    class="body_text_color body_text_size">
                    <h1 class="main-heading">
                        Email Update</h1>
                    <div style=" border-radius: 5px; width: 900px; margin: 0 auto; ">
                        <span style="font-weight: normal; font-size: 14px;" class="welcome">Hi
                            {{ $userName }},</span>

                        <p style="margin-bottom: 20px;">Your {Ad Agency Creatives} account email address has been
                            successfully updated
                            to aac.
                        </p>

                        <p style="margin-bottom: 20px;">If you did not make this update, please contact us at
                            info@adagencycreatives.com.
                        </p>

                        <p style="margin-bottom: 20px;">As always, be sure to update your password often and keep your
                            information confidential.
                        </p>

                        <p>If link is not clickable, copy and paste the folowing URL into the browser.
                        </p>
                        <p style="overflow-wrap: break-word; word-wrap: break-word;"> {{ $url }}</p>

                        @include('emails.includes.jobboard_footer')