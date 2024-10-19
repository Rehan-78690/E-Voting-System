<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Vote</title>
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

        .step-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-right: 10px;
        }

        .step-content {
            font-size: 1.1rem;
            color: #333;
        }

        .btn-primary {
            background-color: #ff6b6b;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #ff4747;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>How to Vote in the E-Voting System</h1>
        <div class="accordion" id="voteAccordion">
            <!-- Step 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep1" aria-expanded="true" aria-controls="collapseStep1">
                        Step 1: Log in to Your Account
                    </button>
                </h2>
                <div id="collapseStep1" class="accordion-collapse collapse show" aria-labelledby="headingStep1" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">1.</span>
                        <span class="step-content">Enter your email and password on the login page to access your voter dashboard. Ensure that you use the correct credentials associated with your voter registration.</span>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep2" aria-expanded="false" aria-controls="collapseStep2">
                        Step 2: Select an Ongoing Election
                    </button>
                </h2>
                <div id="collapseStep2" class="accordion-collapse collapse" aria-labelledby="headingStep2" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">2.</span>
                        <span class="step-content">Once logged in, you will see a list of ongoing elections. Click on the election you want to participate in. You can review the election details such as the date, time, and the candidates participating.If no elections is currently ongoing then check notifications to see when will the  elections start. </span>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep3" aria-expanded="false" aria-controls="collapseStep3">
                        Step 3: Review Candidate Profiles
                    </button>
                </h2>
                <div id="collapseStep3" class="accordion-collapse collapse" aria-labelledby="headingStep3" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">3.</span>
                        <span class="step-content">Click on a candidate’s name to view their profile, manifesto, and other information. Take your time to understand each candidate's vision and proposed plans before making your choice.</span>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep4" aria-expanded="false" aria-controls="collapseStep4">
                        Step 4: Cast Your Vote
                    </button>
                </h2>
                <div id="collapseStep4" class="accordion-collapse collapse" aria-labelledby="headingStep4" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">4.</span>
                        <span class="step-content">To cast your vote, click on the "Vote" button next to your preferred candidate. You will be prompted to confirm your choice before the vote is recorded. Make sure to review your selection carefully.</span>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep5">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep5" aria-expanded="false" aria-controls="collapseStep5">
                        Step 5: Confirm Your Vote
                    </button>
                </h2>
                <div id="collapseStep5" class="accordion-collapse collapse" aria-labelledby="headingStep5" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">5.</span>
                        <span class="step-content">After selecting your preferred candidate, a confirmation dialog will appear. Review your choice and click on "Confirm" to submit your vote. Once confirmed, the vote cannot be changed.</span>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep6">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep6" aria-expanded="false" aria-controls="collapseStep6">
                        Step 6: Check Voting Confirmation
                    </button>
                </h2>
                <div id="collapseStep6" class="accordion-collapse collapse" aria-labelledby="headingStep6" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">6.</span>
                        <span class="step-content">After confirming your vote, you will see a success message indicating that your vote has been recorded. You can also view your voting history to verify your participation in the election.</span>
                    </div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingStep7">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep7" aria-expanded="false" aria-controls="collapseStep7">
                        Step 7: Log Out or see Voting history
                    </button>
                </h2>
                <div id="collapseStep7" class="accordion-collapse collapse" aria-labelledby="headingStep7" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        <span class="step-number">7.</span>
                        <span class="step-content">Once you have successfully voted, log out of your account for security purposes. To log out, click on the "Sign Out" option from the dashboard menu.Moreover you can see history of your previous participation.</span>
                    </div>
                </div>
            </div>

            <!-- Final Note -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingNote">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNote" aria-expanded="false" aria-controls="collapseNote">
                        Final Note: Need Help?
                    </button>
                </h2>
                <div id="collapseNote" class="accordion-collapse collapse" aria-labelledby="headingNote" data-bs-parent="#voteAccordion">
                    <div class="accordion-body">
                        If you encounter any issues during the voting process, feel free to reach out to our support team. We are here to ensure that your voting experience is seamless and secure.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
