<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/scheduleTimeRangeControl.css">
    <?php // TODO: only used on the schedule page so we should insert this line with php ideally ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/roundSlider/1.6.1/roundslider.css" integrity="sha512-XO53CaiPx+m4HUiZ02P4OEGLyyT46mJQzWhwqYsdqRR7IOjPuujK0UPAK9ckSfcJE4ED7dT9pF9r78yXoOKeYw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>  
      

</head>
<body>
<style>
  body,  html {
    height:100vh;
  }

  .customh5{
    margin-left:150px;
}
.custominputs {
height:30px;
}

#sidenav, .navlink {
  background-color: #468F8B;
  color: black;
  font-weight: bold;
}
#logo {
    font-weight: bold;
    text-align: center;
    align-items: center;
    color: black;
    font-size: larger;
}
.card{
  border:solid 2px black;
  margin: 5px;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  border-radius: 5px;
}
.custombtn {
  margin-bottom: 1rem !important;
  color:black !important;
  font-weight:bolder;
  font-size:13px;
}

.table-wrapper {
    width: 90%; /* Adjust this value to control the width of the table */
    margin: auto; /* Centers the table wrapper within its parent */
    overflow-x: auto; /* Adds a scrollbar if the table overflows */
}

.custom-table {
    border: 2px solid black;
    width: 100%; /* Makes the table take up the full width of its wrapper */
}

.custom-table th, .custom-table td {
    border-right: 1px solid black;
    border-collapse: collapse;
}

.custom-table th:last-child, .custom-table td:last-child {
    border-right: none;
}

#addTaskButton {
    /* Button styles */
    padding-right: 20px;

}

@media screen and (max-width: 640px) {
    .custom-table th, .custom-table td {
        border-right: none;
    }
}




  </style>
  <?php include 'adminsidebar.php'; ?>


  <div id="main-content" class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-4">
    <?php 
        if(isset($page))
        {
            if($page !== false)
                include($page);
        }
        else
        {
    ?>
        <div class="cell">
          <div class="card" >
            <div class="card-section">
              <h4>Dashboard Card.</h4>
              <p>Responsive card width n height</p>
            </div>
          </div>
        </div>
      <div class="cell">
        <div class="card" >
          <div class="card-section">
            <h4>Dashboard Card.</h4>
            <p>Responsive card width n height</p>
          </div>
        </div>
      </div>
      <div class="cell">
        <div class="card" >
          <div class="card-section">
            <h4>Dashboard Card.</h4>
            <p>Responsive card width n height</p>
          </div>
        </div>
      </div>
      <div class="cell">
        <div class="card" >
          <div class="card-section">
            <h4>Dashboard Card.</h4>
            <p>Responsive card width n height</p>
          </div>
        </div>
    </div>
        <?php
        }   
        ?>
    </div>

<div class="reveal" id="inviteModal" data-reveal>
    <div class="grid-x grid-padding-x align-center">
        <div class="cell small-12 medium-12 large-12">
            <h2>Invite Household Members</h2>
            <p>Household Universal Link to allow members to join.</p>
            <form>
                <div class="grid-x grid-padding-x align-middle">
                    <div class="cell auto urlstyle">
                        <!-- -->
                        <p><a id="invitationUrl"><?=$link?></a></p>
                    </div>
                    <div class="cell shrink">
                        <button id="copyInviteButton" type="button" class="button primary">Copy</button>
                    </div>
                </div>
            </form>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>

    
    <?php 
        if(isset($dynamicScripts))
        {
            foreach($dynamicScripts as $path)
            {
                echo("<script>\n");
                include($path);
                echo("</script>\n");
            }
        }

        if(isset($staticScripts))
        {
            foreach($staticScripts as $path)
            {
                echo("<script src=\"" . $path . "\"></script>\n");
            }
        }
    ?>
    <script>
    $(document).foundation();

    $('#inviteButton').on('click', function(e) {
        e.preventDefault(); // Prevent the default behavior of the link
        $('#inviteModal').foundation('open');
    });

    $('#copyInviteButton').on('click', function(e) {
        // Select the text inside the <a> tag
        var textToCopy = $('#invitationUrl').text();
        // Create a temporary input element
        var tempInput = $("<input>");
        // Append it to the body
        $("body").append(tempInput);
        // Set the value of the input to the text we want to copy
        tempInput.val(textToCopy).select();
        // Copy the selected text to the clipboard
        document.execCommand("copy");
        // Remove the temporary input element
        tempInput.remove();
        // Change button text to "Copied"
        $(this).text("Copied");
    });

    </script>
</body>
</html> 