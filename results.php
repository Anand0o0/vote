<?php include 'header.php'; ?>
<?php
session_start();
require_once 'Database.php';
require_once 'Candidate.php';
require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/Exception.php';
require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$winners = Candidate::fetchWinners($db);

if (!empty($winners)) {
    echo "<h1>Results</h1>";
    echo "<table border='1'>
            <tr>
                <th>Post</th>
                <th>Candidate Name</th>
                <th>Votes</th>
            </tr>";
    $resultsText = "Winners:\n\n";
    foreach ($winners as $winner) {
        echo "<tr>
                <td>{$winner->candidate_post}</td>
                <td>{$winner->candidate_name}</td>
                <td>{$winner->voteCount}</td>
              </tr>";
        $resultsText .= "Post: {$winner->candidate_post}\nCandidate: {$winner->candidate_name}\nVotes: {$winner->voteCount}\n\n";
    }
    echo "</table>";
} else {
    echo "No results found.";
}

$userEmails = Candidate::fetchVoterEmails($db);

foreach ($userEmails as $email) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vijethayc@gmail.com';
        $mail->Password = 'wckobphfuzilxdns'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('vijethayc@gmail.com', 'Online Voting System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'CR & ACR Election Results';
        $mail->Body = nl2br($resultsText);
        $mail->AltBody = $resultsText;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<?php include 'footer.php'; ?>