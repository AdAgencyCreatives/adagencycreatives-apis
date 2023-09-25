<script>
    function fetchIndustriesForCreative() {

        $.ajax({
            url: '/api/v1/get_industry-experiences',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateFilter(response.data, '#industry');
                var industry_experience = "{{ $user->creative?->industry_experience }}";
                var industryArray = industry_experience.split(',');
                industryArray.forEach(function(uuid) {
                    $('#industry option[value="' + uuid + '"]').prop('selected', true);
                });
                $('#industry').trigger('change');

            },
            error: function() {
                alert('Failed to fetch industries from the API.');
            }
        });
    }

    function fetchMediasForCreative() {
        $.ajax({
            url: '/api/v1/get_media-experiences',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateFilter(response.data, '#media');
                var media_experience = "{{ $user->creative?->media_experience }}";
                var mediaArray = media_experience.split(',');
                mediaArray.forEach(function(uuid) {
                    $('#media option[value="' + uuid + '"]').prop('selected', true);
                });
                $('#media').trigger('change');

            },
            error: function() {
                alert('Failed to fetch medias from the API.');
            }
        });
    }

    function fetchStrengthsForCreative() {
        $.ajax({
            url: '/api/v1/get_strengths',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateFilter(response.data, '#strengths');
                var strengths = "{{ $user->creative?->strengths }}";
                var strengthArray = strengths.split(',');
                strengthArray.forEach(function(uuid) {
                    $('#strengths option[value="' + uuid + '"]').prop('selected', true);
                });
                $('#strengths').trigger('change');

            },
            error: function() {
                alert('Failed to fetch strength from the API.');
            }
        });
    }

    function fetchIndustriesForAgency() {
        $.ajax({
            url: '/api/v1/get_industry-experiences',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateFilter(response.data, '#industry');
                var industry_experience = "{{ $user->agency?->industry_experience }}";
                var industryArray = industry_experience.split(',');
                industryArray.forEach(function(uuid) {
                    $('#industry option[value="' + uuid + '"]').prop('selected', true);
                });
                $('#industry').trigger('change');

            },
            error: function() {
                alert('Failed to fetch industries from the API.');
            }
        });
    }

    function fetchMediasForAgency() {
        $.ajax({
            url: '/api/v1/get_media-experiences',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateFilter(response.data, '#media');
                var industry_experience = "{{ $user->agency?->media_experience }}";
                var industryArray = industry_experience.split(',');
                industryArray.forEach(function(uuid) {
                    $('#media option[value="' + uuid + '"]').prop('selected', true);
                });
                $('#media').trigger('change');

            },
            error: function() {
                alert('Failed to fetch medias from the API.');
            }
        });
    }



    $(document).ready(function() {
        var user = @json($user);

        var address = {!! json_encode($user->addresses->first()) !!};
        if (user.role === 'agency' || user.role === 'advisor') {
            fetchIndustriesForAgency();
            fetchMediasForAgency();
        } else if (user.role === 'creative') {
            var creative_years_of_experience = "{{ $user->creative?->years_of_experience }}";
            fetchYearsOfExperienceWithSelectedValue(creative_years_of_experience);
            fetchIndustriesForCreative();
            fetchMediasForCreative();
            fetchStrengthsForCreative();
        }

        if (!address) {
            fetchStates()
        } else {
            var user_state = address.state.uuid;
            fetchStates(user_state);
        }


        $('#state').on('change', function() {
            var selectedStateId = $(this).val();

            if (!address) {
                getCitiesByState(selectedStateId);
            } else {
                var city_id = address.city.uuid;
                getCitiesByState(selectedStateId, city_id);
            }

        });

        $("#profile-form").on("submit", function(event) {
            event.preventDefault();

            var $errorContainer = $('#error-messages');
            $errorContainer.hide();

            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            if (password !== confirm_password) {
                var $errorList = $errorContainer.find('ul');
                $errorList.empty();
                $errorList.append('<li> Passwords do not match </li>');
                $errorContainer.show();
                return;
            }

            var formData = {
                first_name: $("#first_name").val(),
                last_name: $("#last_name").val(),
                email: $("#email").val(),
                username: $("#username").val(),
                status: $("#status").val(),
                role: $("#role").val(),
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: "/api/v1/users/" + "{{ $user->uuid }}",
                type: "PATCH",
                data: JSON.stringify(formData),

                contentType: "application/json",
                success: function(response) {
                    // Handle success response
                    console.log("API call success:", response);
                    Swal.fire({
                        title: 'Success',
                        text: "User updated successfully",
                        icon: 'success'
                    });

                },
                error: function(error) {
                    if (error.status === 422) {
                        var errorMessages = error.responseJSON.errors;
                        var $errorContainer = $('#error-messages');
                        var $errorList = $errorContainer.find('ul');

                        $errorList.empty();

                        $.each(errorMessages, function(field, errors) {
                            $.each(errors, function(index, error) {
                                $errorList.append('<li>' + error + '</li>');
                            });
                        });

                        $errorContainer.show();
                    } else {
                        console.error("API call error:", error.responseText);
                    }

                }
            });
        });

        $("#agency-form").on("submit", function(event) {
            event.preventDefault();

            var formData = {
                name: $("input[name='name']").val(),
                size: $("input[name='size']").val(),
                type_of_work: $("select[name='type_of_work']").val(),
                industry_experience: $("select[name='industry_experience']").val(), // array
                about: $("textarea[name='about']").val(),
                _token: "{{ csrf_token() }}"
            };

            console.log(formData);
            return;
            $.ajax({
                url: "/api/v1/agencies/" + "{{ $user->agency?->uuid }}",
                type: "PATCH",
                data: JSON.stringify(formData),

                contentType: "application/json",
                success: function(response) {
                    console.log("API call success:", response);
                    Swal.fire({
                        title: 'Success',
                        text: "Agency info updated successfully",
                        icon: 'success'
                    });

                },
                error: function(error) {
                    if (error.status === 422) {
                        var errorMessages = error.responseJSON.errors;
                        var $errorContainer = $('#error-messages');
                        var $errorList = $errorContainer.find('ul');

                        $errorList.empty();

                        $.each(errorMessages, function(field, errors) {
                            $.each(errors, function(index, error) {
                                $errorList.append('<li>' + error + '</li>');
                            });
                        });

                        $errorContainer.show();
                    } else {
                        console.error("API call error:", error.responseText);
                    }

                }
            });
        });

        $("#creative-form").on("submit", function(event) {
            event.preventDefault();

            var formData = {
                years_of_experience: $("input[name='years_of_experience']").val(),
                type_of_work: $("select[name='type_of_work']").val(),
                _token: "{{ csrf_token() }}"
            };
            console.log(formData);
            $.ajax({
                url: "/api/v1/creatives/" + "{{ $user->creative?->uuid }}",
                type: "PATCH",
                data: JSON.stringify(formData),

                contentType: "application/json",
                success: function(response) {
                    console.log("API call success:", response);
                    Swal.fire({
                        title: 'Success',
                        text: "Agency info updated successfully",
                        icon: 'success'
                    });

                },
                error: function(error) {
                    if (error.status === 422) {
                        var errorMessages = error.responseJSON.errors;
                        var $errorContainer = $('#error-messages');
                        var $errorList = $errorContainer.find('ul');

                        $errorList.empty();

                        $.each(errorMessages, function(field, errors) {
                            $.each(errors, function(index, error) {
                                $errorList.append('<li>' + error + '</li>');
                            });
                        });

                        $errorContainer.show();
                    } else {
                        console.error("API call error:", error.responseText);
                    }

                }
            });
        });

        $("#password-form").on("submit", function(event) {
            event.preventDefault();

            var $errorContainer = $('#error-messages');
            $errorContainer.hide();

            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            if (password !== confirm_password) {
                var $errorList = $errorContainer.find('ul');
                $errorList.empty();
                $errorList.append('<li> Passwords do not match </li>');
                $errorContainer.show();
                return;
            }

            var formData = {
                password: $("#password").val(),
                user_id: "{{ $user->id }}",
                _token: "{{ csrf_token() }}"
            };


            $.ajax({
                url: "{{ route('user.password.update') }}",
                type: "PUT",
                data: JSON.stringify(formData),

                contentType: "application/json",
                success: function(response) {
                    console.log("API call success:", response);
                    Swal.fire({
                        title: 'Success',
                        text: "Password updated successfully",
                        icon: 'success'
                    });

                },
                error: function(error) {
                    if (error.status === 422) {
                        var errorMessages = error.responseJSON.errors;
                        var $errorContainer = $('#error-messages');
                        var $errorList = $errorContainer.find('ul');

                        $errorList.empty();

                        $.each(errorMessages, function(field, errors) {
                            $.each(errors, function(index, error) {
                                $errorList.append('<li>' + error + '</li>');
                            });
                        });

                        $errorContainer.show();
                    } else {
                        console.error("API call error:", error.responseText);
                    }

                }
            });
        });

        // disable submit button if user is advisor
        const userRole = '{{ auth()->user()->role }}';
        if (userRole === 'advisor') {
            $('button[type="submit"]').prop('disabled', true);
        }




    });
</script>
