<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            padding-top: 60px;
        }

        .container {
            margin-top: 50px;
        }

        h1 {
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            color: #343a40;
        }

        .accordion-button {
            background-color: #343a40;
            color: #ffffff;
            font-weight: bold;
        }

        .accordion-button:not(.collapsed) {
            background-color: #ff6b6b;
            color: #ffffff;
        }

        .accordion-item {
            border: none;
            margin-bottom: 10px;
        }

        .accordion-body {
            background-color: #fff;
            border-radius: 0 0 5px 5px;
            padding: 20px;
        }

        .accordion-header {
            border-radius: 5px;
        }

        .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
<div class="back-button">
    <a href="voter_dashboard.php" class="btn btn-secondary">← Back</a>
</div>
    <div class="container">
        <h1>Frequently Asked Questions (FAQs)</h1>
        <div class="accordion" id="faqAccordion">
            <!-- Registration and Login -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How do I register to vote?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To register, contact the  registrar office , you will be added in the database if eligible ,manage your profile afterwards .
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        What should I do if I forget my password?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Click on the "Forgot Password" link on the login page. You’ll receive a reset link in your registered email to create a new password.
                    </div>
                </div>
            </div>

            <!-- Voting Process -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        How do I cast my vote in the e-voting system?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Log in to your account, select the ongoing election, review the candidates, and click on the "Vote" button next to your preferred candidate. Confirm your vote when prompted.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Can I vote for more than one candidate in an election?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No, you can only vote for one candidate per election. Make sure to review your choice before submitting.
                    </div>
                </div>
            </div>

            <!-- Voting Eligibility -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Who is eligible to vote in the elections?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        All registered users who have verified their identities are eligible to vote in the elections.
                    </div>
                </div>
            </div>

            <!-- Technical Issues -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSix">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        What should I do if the website is not loading?
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Check your internet connection and try refreshing the page. If the issue persists, try accessing the site using a different browser or contact support.
                    </div>
                </div>
            </div>

            <!-- Security and Privacy -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSeven">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        Is my vote confidential?
                    </button>
                </h2>
                <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, the E-voting system is designed to ensure the confidentiality and anonymity of your vote.
                    </div>
                </div>
            </div>

            <!-- Results and After Voting -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingEight">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                        When will the election results be announced?
                    </button>
                </h2>
                <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Election results will be announced instantly after the voting period ends. You can view the results on the dashboard.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
