                                                    
                                                  
                                                    </td>
                                                    </tr>
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
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                        <tr>
                                                            <td style="padding: 10px 70px 30px 20px; border-radius: 0 0 5px 5px;">
                                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                    <tr>
                                                                        <td align="right" valign="middle" style="width: 20%;">
                                                                            <img style="display: block; width: 50px; height: 50px; background: #ffffff; border-radius: 50%;"
                                                                                src="https://ad-agency-creatives.s3.amazonaws.com/agency_logo/aac-logo-round-transparent-bold.png"
                                                                                alt="AAC Logo">
                                                                        </td>
                                                                        <td align="center" valign="middle" style="font-family: sans-serif; padding-left:30px; font-size: 12px; color: #ffffff; text-align: center; width: 60%;">
                                                                            <p>
                                                                                &copy; {{ date('Y') }} Ad Agency Creatives. All Rights Reserved.
                                                                            </p>
                                                                        </td>

                                                                        <td align="right" valign="middle" style="width: 50%;">
                                                                            <a href="https://www.instagram.com/adagencycreatives" target="_blank" style="text-decoration: none; display: inline-block; margin: 0 4px;">
                                                                                <img src="https://img.icons8.com/ios-filled/50/ffffff/instagram-new.png" width="24" height="24" alt="Instagram">
                                                                            </a>
                                                                            <a href="https://www.linkedin.com/company/ad-agency-creatives" target="_blank" style="text-decoration: none; display: inline-block; margin: 0 4px;">
                                                                                <img src="https://img.icons8.com/ios-filled/50/ffffff/linkedin.png" width="24" height="24" alt="LinkedIn">
                                                                            </a>
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
                                                    </div>
                                                    </body>

                                                    </html>