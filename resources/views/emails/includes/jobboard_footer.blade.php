<p style=""></p>
<p style="margin-bottom: 0px;">Thanks,</p>
<p style="margin: 0;">Member Support</p>
<img width="150" src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/ad-agency-creatives-logo-footer.png"
    alt="" />
<p style="text-align: center; margin-bottom: 0;">Gather. Inspire. Do Cool $#*t!</p>
<img class="footer-logo" style="width: 100px; position: absolute; right: 15px; bottom: 15px;"
    src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/cropped-Ad-Agency-Creatives-Logo.png">
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
                <div style="display:inline-block; margin-top:5px;" id="footer">
                    <a style="color: #ffffff; margin-right: 15px;"
                        href="https://adagencycreatives.com/privacy-policy/">Privacy Policy</a>
                    <a style="color: #ffffff; margin-right: 15px;"
                        href="https://adagencycreatives.com/terms-and-conditions/">User
                        Agreement</a>
                    <a style="color: #ffffff;" href="https://adagencycreatives.com/contact">Contact Us</a>
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
@isset($data)
    @isset($data['APP_URL'])
        @if ($data['APP_URL'] != 'https://api.adagencycreatives.com')
            <div style="display: none">
                <pre>
                    @php
                        print_r($data);
                    @endphp
                </pre>
            </div>
        @endif
    @endisset
@endisset
</body>

</html>
