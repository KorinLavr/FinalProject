    document.getElementById('editDetails').addEventListener('click', function()
    {
        var fields = document.querySelectorAll('#personalDetailsForm input, #personalDetailsForm select');
        fields.forEach(function(field) {
            field.disabled = false;
        });
        document.getElementById('saveButton').style.display = 'inline-block';
    });
    
    // Show success message if present
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        $('#successMessage').text(decodeURIComponent(message));
    }
    
    function validateForm() {
    // Get the value of the birth date input
    var dobInput = document.getElementById("dob").value;
    // Calculate the age
    var dob = new Date(dobInput);
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    var monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    // Check if the age is less than 18
    if (age < 18) {
        // Show an error message
        alert("עליך להיות בגיל 18 לפחות");
        // Prevent the form from being submitted
        return false;
    }
    // If the age is 18 or above, allow the form to be submitted
    return true;
}