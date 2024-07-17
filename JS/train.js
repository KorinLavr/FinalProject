function validateAndSubmit() {
    const inputs = document.querySelectorAll('.form-container input');
    let isValid = true;

    inputs.forEach(input => {
        if (input.value.trim() === '') {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    // Validate date
    const trainingDateInput = document.getElementById('training-date');
    const selectedDate = new Date(trainingDateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Remove time part for today's date comparison

    if (selectedDate <= today) {
        trainingDateInput.classList.add('error');
        alert('לא ניתן לבחור תאריך אימון שהוא היום או תאריך שעבר. בחר תאריך עתידי.');
        isValid = false;
    } else {
        trainingDateInput.classList.remove('error');
    }

    // Validate training price
    const trainingPriceInput = document.getElementById('training-price');
    const trainingPrice = parseFloat(trainingPriceInput.value);

    if (trainingPrice < 50 || trainingPrice > 200) {
        trainingPriceInput.classList.add('error');
        alert('מחיר לא תקין. המחיר נע בין 50 ש"ח ל-200 ש"ח.');
        isValid = false;
    } else {
        trainingPriceInput.classList.remove('error');
    }

    // Validate training max participants
    const trainingMaxInput = document.getElementById('training-max');
    const trainingMax = parseInt(trainingMaxInput.value);

    if (trainingMax <= 0) {
        trainingMaxInput.classList.add('error');
        alert('מספר משתתפים לא תקין.');
        isValid = false;
    } else if (trainingMax > 50) {
        trainingMaxInput.classList.add('error');
        alert('מספר המשתתפים שגוי. המספר המרבי של משתתפים באימון יכול להיות 50 אנשים.');
        isValid = false;
    } else {
        trainingMaxInput.classList.remove('error');
    }

    if (!isValid) {
        alert('יש למלא את כל הפרטים לפני יצירת האימון החדש.');
    } else {
        const confirmation = confirm('האם אתה בטוח שברצונך ליצור אימון חדש?');
        if (confirmation) {
            const form = document.getElementById('trainingForm');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Expect JSON response from the server
            .then(data => {
                if (data.success) {
                    alert('האימון נוצר בהצלחה!');
                    window.location.href = 'https://iskorinla2.mtacloud.co.il/Includes/trainings.html';
                } else {
                    alert('שגיאה ביצירת האימון החדש: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('שגיאה ביצירת האימון החדש.');
            });
        }
    }
}

function getQueryParams() {
    const params = {};
    window.location.search.substring(1).split('&').forEach(function(param) {
        const [key, value] = param.split('=');
        params[key] = decodeURIComponent(value);
    });
    return params;
}

document.addEventListener("DOMContentLoaded", function() {
    initAutocomplete(); // Initialize autocomplete
    const params = getQueryParams();
    if (params.trainerID) {
        document.getElementById('trainer-ID').value = params.trainerID;
    }
});
