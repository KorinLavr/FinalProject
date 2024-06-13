document.addEventListener('DOMContentLoaded', function() {
    // Get userType from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const userType = urlParams.get('userType');
  
    // Show relevant fields and title based on userType
    var trainerFields = document.getElementById('trainerFields');
  //  var traineeFields = document.getElementById('traineeFields');
    var typeUserRegister = document.getElementById('type_user_register');
    var userTypeInput = document.getElementById('userType');
    
    userTypeInput.value = userType;

    if (userType === 'trainer') {
        typeUserRegister.innerHTML = "יצירת פרופיל מאמן";
        trainerFields.style.display = 'block';
      //  traineeFields.style.display = 'none';
      //  document.getElementById('trainer_id').style.display = 'block';
    } else if (userType === 'trainee') {
        typeUserRegister.innerHTML = "יצירת פרופיל מתאמן";
        trainerFields.style.display = 'none';
        document.getElementById('trainer_id').style.display = 'none';
     //   traineeFields.style.display = 'block';
    }
});


document.getElementById("Registration_Form").addEventListener("submit", function(event) {
    
    event.preventDefault(); // Prevent the default form submission behavior

    if (validateForm()) {
        const formData = new FormData(document.getElementById("Registration_Form"));
        const xhr = new XMLHttpRequest();
    
        xhr.open("POST", "Registration.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.error) {
                    alert(response.error);
                } else {
                    alert(response.message);
                     document.getElementById("Registration_Form").reset(); // Clear the form fields on success
                     window.location.href = 'index.html'; // Redirect to home page
                }
            }
        };
    
        xhr.send(new URLSearchParams(formData).toString());
    }
});

function validateForm() {
    
    let isValid = true;
    const userType = document.getElementById('userType').value;
    let fields;

    if (userType === 'trainer') {
        fields = [
            { id: "trainer-ID", errorMessage: "יש למלא תז מאמן", customValidation: value => value.length === 9 },
            { id: "first-name", errorMessage: "יש למלא שם פרטי" },
            { id: "last-name", errorMessage: "יש למלא שם משפחה" },
            { id: "gender", errorMessage: "יש לבחור מגדר" },
            { id: "dob", errorMessage: "יש למלא תאריך לידה" },
            { id: "phone", errorMessage: "יש למלא מספר טלפון" },
            { id: "email", errorMessage: "יש למלא כתובת דוא\"ל" },
            { id: "experience", errorMessage: "יש למלא שנות ניסיון" },
            { id: "training-types", errorMessage: "יש למלא סוגי אימונים" },
            { id: "password", errorMessage: "סיסמה לא חוקית. הסיסמה חייבת להכיל לפחות 8 תווים, כולל אותיות ומספרים.", customValidation: value => /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(value) },
            { id: "confirm-password", errorMessage: "יש לאמת סיסמה" }
        ];
    } else if (userType === 'trainee') {
        fields = [
            { id: "first-name", errorMessage: "יש למלא שם פרטי" },
            { id: "last-name", errorMessage: "יש למלא שם משפחה" },
            { id: "gender", errorMessage: "יש לבחור מגדר" },
            { id: "dob", errorMessage: "יש למלא תאריך לידה" },
            { id: "phone", errorMessage: "יש למלא מספר טלפון" },
            { id: "email", errorMessage: "יש למלא כתובת דוא\"ל" },
            { id: "password", errorMessage: "סיסמה לא חוקית. הסיסמה חייבת להכיל לפחות 8 תווים, כולל אותיות ומספרים.", customValidation: value => /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(value) },
            { id: "confirm-password", errorMessage: "יש לאמת סיסמה" }
        ];
    }
    
    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const errorSpan = document.getElementById(`${field.id}-error`);
        const value = input.value.trim();
        console.log(`${field.id}: ${value}`); 

        if (!value || (field.customValidation && !field.customValidation(value))) {
            input.classList.add("error");
            errorSpan.textContent = field.customValidation && !field.customValidation(value) ? field.errorMessage : field.errorMessage;
            isValid = false;
        } else {
            input.classList.remove("error");
            errorSpan.textContent = "";
        }
    });

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    if (password !== confirmPassword) {
        document.getElementById("confirm-password-error").textContent = "אימות סיסמה נכשל. הסיסמאות אינן תואמות.";
        document.getElementById("confirm-password").classList.add("error");
        isValid = false;
    }

    return isValid;
}

