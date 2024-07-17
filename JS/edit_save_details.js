// edit_save_details.js

document.getElementById('edit-button').addEventListener('click', function() {
    // Enable editing fields
    document.getElementById('first-name').setAttribute('contenteditable', true);
    document.getElementById('last-name').setAttribute('contenteditable', true);
    document.getElementById('gender').setAttribute('contenteditable', true);
    document.getElementById('birth-date').setAttribute('contenteditable', true);
    document.getElementById('phone-number').setAttribute('contenteditable', true);
    document.getElementById('email').setAttribute('contenteditable', true);
    document.getElementById('experience').setAttribute('contenteditable', true);
    document.getElementById('training-types').setAttribute('contenteditable', true);
    document.getElementById('certification').setAttribute('contenteditable', true);

    // Show save button and hide edit button
    document.getElementById('edit-button').style.display = 'none';
    document.getElementById('save-button').style.display = 'inline-block';
});


    // Prepare data for saving
    const updatedData = {
        F_name: document.getElementById('first-name').innerText.trim(),
        L_name: document.getElementById('last-name').innerText.trim(),
        Sex: document.getElementById('gender').innerText.trim(),
        Bdate: document.getElementById('birth-date').innerText.trim(),
        Phone_num: document.getElementById('phone-number').innerText.trim(),
        Email: document.getElementById('email').innerText.trim(),
        Exp_years: document.getElementById('experience').innerText.trim(),
        Type: document.getElementById('training-types').innerText.trim(),
        Certification: document.getElementById('certification').innerText.trim() === 'כן' ? true : false // Assuming the certification is represented as text 'כן' or 'לא'
    };

    // Send data to server for saving
    fetch('save_user_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Handle success or display error message
        console.log('Form data saved:', data);
        // Optionally redirect or display a success message
    })
    .catch(error => {
        console.error('Error saving form data:', error);
        // Handle error case, display an error message to the user
    });
});
