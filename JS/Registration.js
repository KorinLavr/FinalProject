document.addEventListener('DOMContentLoaded', function() {
    // Get userType from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const userType = urlParams.get('userType');

    // Show relevant fields and title based on userType
    const trainerFields = document.getElementById('trainerFields');
    const typeUserRegister = document.getElementById('type_user_register');
    const userTypeInput = document.getElementById('userType');
    
    userTypeInput.value = userType;

    if (userType === 'trainer') {
        typeUserRegister.textContent = "יצירת פרופיל מאמן";
        trainerFields.style.display = 'block';
    } else if (userType === 'trainee') {
        typeUserRegister.textContent = "יצירת פרופיל מתאמן";
        trainerFields.style.display = 'none';
        const trainerIdField = document.getElementById('trainer_id');
        if (trainerIdField) {
            trainerIdField.style.display = 'none';
        }
    }
});

document.getElementById("Registration_Form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission behavior
    
    if (validateForm()) {
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        
        xhr.open("POST", "Registration.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            alert(response.error);
                        } else {
                            alert(response.message);
                            document.getElementById("Registration_Form").reset(); // Clear the form fields on success
                            window.location.href = 'index.html'; // Redirect to home page
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        alert("An error occurred. Please try again.");
                    }
                } else {
                    alert("An error occurred. Please try again.");
                }
            }
        };
        
        xhr.send(formData);
    }
});

function calculateAge(dob) {
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDifference = today.getMonth() - birthDate.getMonth();
    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

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
            { id: "training-type", errorMessage: "יש למלא סוגי אימונים" },
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
        const value = input ? input.value.trim() : '';

        if (!input || !value || (field.customValidation && !field.customValidation(value))) {
            if (errorSpan) {
                errorSpan.textContent = field.errorMessage;
            }
            isValid = false;
        } else {
            if (errorSpan) {
                errorSpan.textContent = "";
            }
        }
    });

    // Age validation for trainers and trainees
    const dob = document.getElementById("dob").value;
    const age = calculateAge(dob);
    const dobError = document.getElementById("dob-error");

    if (userType === 'trainer' && age < 18) {
        if (dobError) {
            dobError.textContent = "לא ניתן להירשם כמאמן במערכת. מאמן יכול להיות רק בגיל 18 ומעלה.";
        }
        isValid = false;
    } else if (userType === 'trainee' && age < 18) {
        if (dobError) {
            dobError.textContent = "לא ניתן להירשם כמתאמן במערכת. מתאמן יכול להיות רק בגיל 18 ומעלה.";
        }
        isValid = false;
    } else {
        if (dobError) {
            dobError.textContent = "";
        }
    }

    // Password confirmation validation
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const confirmPasswordError = document.getElementById("confirm-password-error");

    if (password !== confirmPassword) {
        if (confirmPasswordError) {
            confirmPasswordError.textContent = "אימות סיסמה נכשל. הסיסמאות אינן תואמות.";
        }
        isValid = false;
    } else {
        if (confirmPasswordError) {
            confirmPasswordError.textContent = "";
        }
    }

    return isValid;
}
