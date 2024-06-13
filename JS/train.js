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

    const trainingDateInput = document.getElementById('training-date');
    const trainingTimeInput = document.getElementById('training-time');
    const selectedDate = new Date(trainingDateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Remove time part for today's date comparison

    // Check if the selected date is in the past
    if (selectedDate < today) {
        trainingDateInput.classList.add('error');
        alert('לא ניתן לבחור תאריך אימון שכבר עבר. בחר תאריך עתידי או את התאריך של היום.');
        isValid = false;
    } else {
        trainingDateInput.classList.remove('error');
    }

    // Check if the selected time on today's date is in the past
    if (selectedDate.getTime() === today.getTime()) {
        const selectedTime = trainingTimeInput.value;
        const currentTime = new Date().toTimeString().split(' ')[0];

        if (selectedTime < currentTime) {
            trainingTimeInput.classList.add('error');
            alert('לא ניתן לבחור שעת אימון שכבר עברה. בחר שעה עתידית.');
            isValid = false;
        } else {
            trainingTimeInput.classList.remove('error');
        }
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
    const params = getQueryParams();
    if (params.trainerID) {
        document.getElementById('trainer
