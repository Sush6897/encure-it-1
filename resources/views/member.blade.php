<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

<h1>Member </h1>
 <div class="container">
  
  <ul id="memberlist">
    <?php
    // recursive function to display like tree structure member
    function tree($members, $parentid=NULL){
        echo "<ul>";
        foreach($members as $m){
            if ($m['parent_id'] == $parentid) {
                echo '<li data-memberid="' . $m['id'] . '" data-parentid="' . $m['parent_id'] . '">' . $m['name'];
                tree($members, $m['id']);
                echo '</li>';
            }

        }
        echo "</ul>";
   }

   tree($members);
    ?>
  </ul>
  <button class="btn btn-danger" id="addmember" data-bs-toggle="modal" data-bs-target="#addMemberModal">Add Member</button>
    <!-- define a modal  -->
  <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">Add Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addmemberform" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="parentdropdown" class="form-label">Parent:</label>
                        <select class="form-select" id="parentdropdown" name="parent" >
                            <!-- Your options here -->
                        </select>
                        
                    </div>
                    <div class="mb-3">
                        <label for="membername" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="membername" name="name" required pattern="[A-Za-z]{3,50}"  title="Name must be between 3 and 50 characters" >
                        <div class="invalid-feedback">
                            Name must be between 3 and 50 characters.

                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="save">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Fetch parents and update dropdown
    $.ajax({
        url: '{{url("get_parent")}}',
        type: 'GET',
        success: function (data) {
            $('#parentdropdown').html(data.options);
        }
    });

    // Click on add member button resets input fields
    $('#addmember').on('click', function () {
        $('#parentdropdown').val('');
        $('#membername').val('');
    });

    // Save button click event
    $('#save').on('click', function (event) {
        var form = $('#addmemberform')[0];
        $('#membername').val($('#membername').val().trim());

        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            $(form).addClass('was-validated');
        } else {
            var formData = $('#addmemberform').serialize();
            $.ajax({
                url: '{{url("submit-form")}}',
                type: 'POST',
                data: { formData: formData },
                success: function (response) {
                    var newMember = response;
                    
                    if (newMember.members.parent_id === 0) {
                        // If parent_id is null, create a new <ul> and append the <li>
                        var newUl = $('<ul>');
                        newUl.append('<li data-memberid="' + newMember.members.id + '">' + newMember.members.name + '</li>');
                        $('#memberlist').append(newUl);
                    } else {
                        console.log(newMember);
                        // If parent_id is not null, find the existing <ul> and append the <li>
                        var parentLi = $('li[data-memberid="' + newMember.members.parent_id + '"]');
                        if (parentLi.length > 0) {
                            var parentUl = parentLi.children('ul');
                            if (parentUl.length === 0) {
                                // If the parent <ul> doesn't exist, create it
                                parentUl = $('<ul>');
                                parentLi.append(parentUl);
                            }
                            parentUl.append('<li data-memberid="' + newMember.members.id + '">' + newMember.members.name + '</li>');
                        } else {
                            // If the parent <li> doesn't exist, create a new <ul> and <li>
                            var newUl = $('<ul>');
                            newUl.append('<li data-memberid="' + newMember.members.id + '">' + newMember.members.name + '</li>');
                            $('#memberlist').append(newUl);
                        }
                    }

                    // Append the new parent to the dropdown
                    $('#parentdropdown').append($('<option>', {
                        value: newMember.members.id,
                        text: newMember.members.name
                    }));

                    $('#addMemberModal').modal('hide');
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
    });

    // Clear validation classes when the modal is hidden
    $('#addMemberModal').on('hidden.bs.modal', function () {
        $('#addmemberform').removeClass('was-validated');
    });
});

</script>
</body>
</html>