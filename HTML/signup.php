<?php 
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../CSS/signup.css" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Signup</title>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script>
    function checkPasswordStrength(password) {
      const numberCheckbox = document.getElementById("number-check");
      const uppercaseCheckbox = document.getElementById("uppercase-check");
      const specialCharCheckbox = document.getElementById("special-check");
      const minLengthCheckbox = document.getElementById("length-check");
      const strengthBar = document.getElementById("strength-bar");
      const strengthText = document.getElementById("strength-text");

      let hasNumber = /\d/.test(password);
      let hasUppercase = /[A-Z]/.test(password);
      let hasSpecialChar = /[^A-Za-z0-9]/.test(password);
      let hasMinLength = password.length >= 8;
      let strength = 0;

      numberCheckbox.checked = hasNumber;
      uppercaseCheckbox.checked = hasUppercase;
      specialCharCheckbox.checked = hasSpecialChar;
      minLengthCheckbox.checked = hasMinLength;

      if (hasNumber) strength++;
      if (hasUppercase) strength++;
      if (hasSpecialChar) strength++;
      if (hasMinLength) strength++;

      const strengthPercentage = (strength / 4) * 100;
      strengthBar.style.width = strengthPercentage + "%";

      if (strengthPercentage < 40) {
        strengthText.textContent = "Weak";
        strengthText.style.color = "red";
        strengthBar.style.backgroundColor = "red";
      } else if (strengthPercentage < 70) {
        strengthText.textContent = "Moderate";
        strengthText.style.color = "blue";
        strengthBar.style.backgroundColor = "blue";
      } else {
        strengthText.textContent = "Strong";
        strengthText.style.color = "green";
        strengthBar.style.backgroundColor = "green";
      }

      const submitBtn = document.getElementById("submit-btn");
      submitBtn.disabled = !(hasNumber && hasUppercase && hasSpecialChar && hasMinLength);
    }

    function validatePasswordMatch() {
      const password = document.getElementById("password").value;
      const reEnterPassword = document.getElementById("re-enter-password").value;
      const matchMessage = document.getElementById("password-match-message");

      if (password !== reEnterPassword) {
        matchMessage.textContent = "Passwords do not match!";
        matchMessage.className = "no-match";
      } else {
        matchMessage.textContent = "Passwords match.";
        matchMessage.className = "match";
      }
    }

    function sendVerificationEmail() {
      date = new Date();
      var template = {
        'name': '<?php echo htmlspecialchars($_SESSION['fname']);?>',
        'toEmail': '<?php echo htmlspecialchars($_SESSION['verify-email']);?>',
        'timestamp': date.toDateString() + date.toLocaleTimeString(),
        'verification_code': '<?php echo htmlspecialchars($_SESSION['verify-code']);?>'
      };
      emailjs.init({
        publicKey: 'ECTSeMcGKIrjecfzP',
        blockHeadless: true,
        limitRate: {
          id: 'app',
          throttle: 10000
        }
      });
      
      emailjs.send('service_7tpn0mm', 'template_divc31p', template).then(
        function(response) {
          console.log("Successfully sent email: ", response);
          window.parent.postMessage("email_sent_success", '*');
          return true;
        },

        function(error) {
          console.log("Failed to send email: ", error);
          window.parent.postMessage("email_sent_error", '*');
          return false;
        }
      );
    }

    window.onload.sendVerificationEmail();
  </script>
</head>
<body>
  <div class="wrapper">
  <?php if(isset($_SESSION['verify-email'])): ?>
    <form target='_top' method="POST">
     <div class="verification-window">
      <p>Sent verification email to $_SESSION['verify-email'].\nPlease enter verification code:\n</p>
      <?php $_SESSION['verify-code'] = sprintf("%06d", mt_rand(100000, 999999));?>
      </div>
    </form>

  <?php else : ?>
    <form action="" method="POST" onsubmit="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
      <h1>Signup</h1>

      <!-- First Name -->
      <div class="input-box">
        <input type="text" placeholder="First Name" name="first-name"
        value="<?php echo htmlspecialchars($_POST['first-name'] ?? '');?>" required>
        <i class='bx bxs-user'></i>
      </div>

      <!-- Last Name -->
      <div class="input-box">
        <input type="text" placeholder="Last Name" name="last-name"
        value="<?php echo htmlspecialchars($_POST['last-name'] ?? '');?>" required>
        <i class='bx bxs-user'></i>
      </div>

      <!-- Birth Date -->
      <div class="input-box">
        <input type="date" name="birth-date"
        value="<?php echo htmlspecialchars($_POST['birth-date'] ?? '');?>" required>
        <i class='bx bxs-calendar'></i>
      </div>

      <!-- Email -->
      <div class="input-box">
        <input type="email" name="email" placeholder="Email"
        value="<?php echo htmlspecialchars($_POST['email'] ?? '');?>" required>
        <i class='bx bxs-envelope'></i>
      </div>

      <!-- Username -->
      <div class="input-box">
        <input type="text" name="username" placeholder="Username"
        value="<?php echo htmlspecialchars($_POST['username'] ?? '');?>" required>
        <i class='bx bxs-user'></i>
      </div>

      <!-- Password -->
      <div class="input-box">
        <input type="password" id="password" name="password" placeholder="Password"
        value="<?php echo htmlspecialchars($_POST['password'] ?? '');?>" required oninput="checkPasswordStrength(this.value)">
        <i class='bx bxs-lock-alt'></i>
      </div>

      <!-- Re-enter Password -->
      <div class="input-box">
        <input type="password" id="re-enter-password" placeholder="Re-enter Password"
        value="<?php echo htmlspecialchars($_POST['re-enter-password'] ?? '');?>" required oninput="validatePasswordMatch()">
        <i class='bx bxs-lock-alt'></i>
      </div>
      <p id="password-match-message"></p>

      <!-- Password Strength Bar -->
      <div class="password-strength">
        <div class="strength-bar" id="strength-bar"></div>
        <span id="strength-text" class="strength-text">Weak</span>
      </div>

      <!-- Password Checklist -->
      <div class="password-requirements">
        <ul>
          <li><input type="checkbox" id="number-check" disabled> Numbers</li>
          <li><input type="checkbox" id="special-check" disabled> Special Characters</li>
          <li><input type="checkbox" id="uppercase-check" disabled> Uppercase</li>
          <li><input type="checkbox" id="length-check" disabled> 8 Characters</li>
        </ul>
      </div>

      <!-- Security Questions -->
      <div class="input-box">
        <select name="security-question" required>
          <option value="">Select a Security Question</option>
          <option value="pet-name">What is the name of your first pet?</option>
          <option value="mother-maiden">What is your mother's maiden name?</option>
          <option value="favorite-color">What is your favorite color?</option>
        </select>
        <i class='bx bxs-lock-alt'></i>
      </div>
      <div class="input-box">
        <input type="text" name="security-answer" placeholder="Answer to Security Question" required>
        <i class='bx bxs-lock-alt'></i>
      </div>

      <!-- CAPTCHA -->
      <div class="g-recaptcha" data-sitekey="your_site_key"></div>

      <p style="font-size: 12px; margin-top: 10px;">Activation link will be sent to your email after sign up.</p>

      <button type="submit" id="submit-btn" class="btn" disabled>Signup</button>

      <div class="register-link">
        <p>Have an account? <a href="login.html">Login Now</a></p>
      </div>

    <?php endif;?>
    <?php
    $_SESSION['fname'] = $_POST['first-name'];
    $_SESSION['verify-email'] = $_POST['email'];
    ?>
    </form>
  </div>

  <?php 
  unset($_SESSION['fname']);
  unset($_SESSION['verify-email']);
  unset($_SESSION['verify-code']);
  ?>
</body>
</html>
