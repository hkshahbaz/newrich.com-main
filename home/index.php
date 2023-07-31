<?php

define('TITLE', "Home");

include '../form/FormGenerator.php';
include '../assets/layouts/header.php';
check_verified();

$json_file_path = '../form/form.json';
$formData = new FormGenerator($json_file_path, 'admin');
$form = $formData->generateForm();
?>


<main role="main" class="container">

    <div class="row">
        <div class="col-sm-3">

            <?php include('../assets/layouts/profile-card.php'); ?>

        </div>
        <div class="col-sm-9">

            <div class="d-flex align-items-center p-3 mt-5 mb-3 text-white-50 bg-purple rounded box-shadow">
                <img class="mr-3" src="../assets/images/logonotextwhite.png" alt="" width="48" height="48">
                <div class="lh-100">
                    <h6 class="mb-0 text-white lh-100">Hey there, <?php echo $_SESSION['username']; ?></h6>
                    <small>Last logged in at <?php echo date("m-d-Y", strtotime($_SESSION['last_login_at'])); ?></small>
                </div>
            </div>

            <div class="my-3 p-3 bg-white rounded box-shadow">
                <h6 class="mb-0">Form Generator</h6>
                <sub class="text-muted border-bottom border-gray pb-2 mb-0">[Following form is generated dynamically!]</sub> 
            </div>


        <div class="row">

        <div class="col-sm-12 px-5">

            <div class="text-center mb-3">
                    <div style="display:none;"  class="alert alert-success text-success font-weight-bold" id="responseMessageSuccess">
                        
                    </div>
                    <div style="display:none;" class="alert alert-danger text-danger font-weight-bold " id="responseMessageFail">
                        
                    </div>
            </div>

            <?= $form; ?>


        </div>
    </div>


        </div>
    </div>
</main>


<script>
  document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    document.getElementById( 'responseMessageSuccess' ).style.display = 'none';
    document.getElementById( 'responseMessageFail' ).style.display = 'none';


    // Perform client-side validations
    if (!validateForm()) {
      return;
    }
    
    // Collect form data
    var formData = new FormData(this);

    // Append submit input value to the FormData object
    var submitInput = document.getElementById('submit');
    formData.append(submitInput.name, submitInput.value);


    // Send form data via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../contact/includes/contact.inc.php', true);
    xhr.onload = function() {
        let response = JSON.parse(xhr.response);
        
        if (response.status === 'success') {
            document.getElementById( 'responseMessageSuccess' ).style.display = 'block';
            document.getElementById('responseMessageSuccess').innerHTML = response.message;
        } else {
            document.getElementById( 'responseMessageFail' ).style.display = 'block';
            document.getElementById('responseMessageFail').innerHTML = response.message;
        }
    };
    xhr.send(formData);
  });

  function validateForm() {
  var nameInput = document.getElementById('name');
  var emailInput = document.getElementById('email');
  var subjectInput = document.getElementById('subject');
  var messageInput = document.getElementById('message');

  // Validate name field
  if (nameInput.value.trim() === '') {
    alert('Please enter your name');
    nameInput.focus();
    return false;
  }
  // Validate name field
  if (subjectInput.value.trim() === '') {
    alert('Please enter subject');
    subjectInput.focus();
    return false;
  }

  // Validate name field
  if (messageInput.value.trim() === '') {
    alert('Please enter message');
    messageInput.focus();
    return false;
  }


  // Validate email field
  var email = emailInput.value.trim();
  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (email === '') {
    alert('Please enter your email');
    emailInput.focus();
    return false;
  } else if (!emailRegex.test(email)) {
    alert('Please enter a valid email address');
    emailInput.focus();
    return false;
  }

  // All validations passed, the form is valid
  return true;
}

</script>
