<?php

$success = isset($_GET['success']) && $_GET['success'] == '1';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>JOB FAIR 2026</title>

    <link rel="stylesheet" href="css/style.css">

</head>


<body class="public-page">

    <header class="institution-header">

        <div class="brand-lockup">
            <img
                class="cpsu-mark"
                src="img/Central_Philippines_State_University_Official_Logo.png"
                alt="Central Philippines State University logo"
            >
            <div>
                <p class="brand-kicker">Central Philippines State University</p>
                <h1 class="brand-name">JOB FAIR 2026</h1>
            </div>
        </div>

        <div class="partner-ribbon" aria-label="Partner organizations">
            <span class="partner-mark"><img src="img/safe_center.png" alt="SAFE Center of CPSU logo"></span>
            <span class="partner-mark"><img src="img/peso_ph.png" alt="Public Employment Service Office logo"></span>
            <span class="partner-mark"><img src="img/kabankalan.png" alt="Kabankalan City logo"></span>
            <span class="partner-mark"><img src="img/city_mall.png" alt="CityMall logo"></span>
        </div>

        <button
            type="button"
            class="fullscreen-button"
            id="fullscreenButton"
            hidden
            aria-label="Enter fullscreen"
            title="Enter fullscreen"
        >
            Fullscreen
        </button>

    </header>

    <div class="container">

        <div class="form-box">

            <div class="form-heading">
                <h2 class="eyebrow">Registration portal</h2>
            </div>


            <?php if ($success): ?>

                <div class="success-message">
                    Applicant successfully registered!
                </div>

            <?php endif; ?>


            <form
                id="applicantForm"
                action="save_applicant.php"
                method="POST"
                autocomplete="off"
            >



                <div class="row">

                    <div class="form-group">

                        <label for="last_name">
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            autocomplete="off"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="first_name">
                            First Name
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            autocomplete="off"
                            required
                        >

                    </div>

                </div>




                <div class="row">

                    <div class="form-group">

                        <label for="middle_name">
                            Middle Name
                            <span>(If applicable)</span>
                        </label>

                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            placeholder="N/A"
                            autocomplete="off"
                        >

                    </div>


                    <div class="form-group">

                        <label for="extension">
                            Name Extension
                            <span>(If applicable)</span>
                        </label>

                        <input
                            type="text"
                            id="extension"
                            name="extension"
                            placeholder="Jr., Sr., III,N/A"
                            autocomplete="off"
                        >

                    </div>

                </div>




                <div class="row">

                    <div class="form-group">

                        <label for="sex">
                            Sex
                        </label>

                        <select
                            id="sex"
                            name="sex"
                            autocomplete="sex"
                            required
                        >

                            <option value="" selected disabled>
                                Select Sex
                            </option>

                            <option value="Male">
                                Male
                            </option>

                            <option value="Female">
                                Female
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="age">
                            Age
                        </label>

                        <input
                            type="number"
                            id="age"
                            name="age"
                            min="1"
                            max="120"
                            required
                        >

                    </div>

                </div>




                <div class="row">

                    <div class="form-group">

                        <label for="barangay">
                            Barangay
                        </label>

                        <input
                            type="text"
                            id="barangay"
                            name="barangay"
                            autocomplete="off"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="city_municipality">
                            City/Municipality
                        </label>

                        <input
                            type="text"
                            id="city_municipality"
                            name="city_municipality"
                            autocomplete="off"
                            required
                        >

                    </div>

                </div>




                <div class="row">

                    <div class="form-group">

                        <label for="phone_number">
                            Mobile Number
                        </label>

                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            autocomplete="off"
                            value="09"
                            minlength="11"
                            maxlength="11"
                            pattern="09[0-9]{9}"
                            inputmode="numeric"
                            onfocus="placeMobileCursor(this)"
                            oninput="enforceMobilePrefix(this)"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="example@email.com"
                            autocomplete="off"
                            oninput="completeGmailAddress(this)"
                            required
                        >

                    </div>

                </div>




                <button
                    type="button"
                    class="confirm-button"
                    onclick="showConfirmation()"
                >
                    Confirm
                </button>


            </form>

        </div>

    </div>




    <div
        id="confirmationModal"
        class="modal"
    >

        <div class="modal-content">

            <h2>
                Confirm Information
            </h2>


            <p>
                Please make sure that all the information
                you entered is correct before submitting.
            </p>


            <div class="modal-buttons">



                <button
                    type="button"
                    class="cancel-button"
                    onclick="closeConfirmation()"
                >
                    Cancel
                </button>



                <button
                    type="button"
                    class="final-confirm-button"
                    onclick="submitForm()"
                >
                    Confirm
                </button>


            </div>

        </div>

    </div>



    <script src="js/script.js"></script>

</body>

</html>
