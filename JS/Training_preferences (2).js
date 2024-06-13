document.addEventListener("DOMContentLoaded", function () {
    var clientNum = document.querySelector("input[name='client_num']").value;
    var formFields = document.querySelectorAll("#preferencesForm input, #preferencesForm button, #preferencesForm select, #preferencesForm textarea");

 // Fetch training preferences using fetch API
    fetch("Training_preferences.php", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ client_num: clientNum })
    })
    .then(response => response.json())
    .then(response => {
        if (response.success && response.data) {
            var data = response.data;

            document.querySelector(`input[name='training_type'][value='${data.Training_type}']`).checked = true;
            document.querySelector("input[name='start_hour']").value = data.Start_hour;
            document.querySelector("input[name='end_hour']").value = data.End_hour;
            document.querySelector("input[name='max_price']").value = data.Max_price;

            // Update the dropdown button text with the selected training type
            dropdownButton.innerText = data.Training_type;

            formFields.forEach(field => {
                field.disabled = true;
            });

            // Disable the slider
        //    $("#slider-range").slider("value", data.Max_price);
            $("#slider-range").slider("disable");
        }
    })
    .catch(error => console.error('Error fetching training preferences:', error));
});

document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let items = document.querySelectorAll('.dropdown-menu li label');
    
    items.forEach(item => {
        let text = item.textContent.toLowerCase();
        if (text.indexOf(filter) === -1) {
            item.parentElement.style.display = 'none';
        } else {
            item.parentElement.style.display = '';
        }
    });
});

const dropdownButton = document.getElementById('SelectDropdown');
const dropdownMenu = document.querySelector('.dropdown-menu');
//const preferencesForm = document.getElementById('preferencesForm');
//const formFields = preferencesForm.querySelectorAll('input, button, select, textarea');

function handleRadioSelection(event) {
    const radio = event.target;
    if (radio.checked) {
        // Update dropdown button text with the selected value
        dropdownButton.innerText = radio.value;
    }
}

// Attach event listener to each radio button
const radioButtons = dropdownMenu.querySelectorAll('input[type="radio"]');
radioButtons.forEach((radio) => {
    radio.addEventListener('change', handleRadioSelection);
});

 

$(function() {
    $("#slider-range").slider({
        range: "min",
        value: 200,
        min: 50,
        max: 200,
        slide: function(event, ui) {
            $("#max_price").val(ui.value);
            // Display message when the slider is clicked
            $("#warningMessage").html("המחיר המינימלי לאימון הוא ₪50");
        },
        stop: function(event, ui) {
            // Clear the message when the slider is released
            $("#warningMessage").html("");
        }
    });

    $( "#max_price" ).val($( "#slider-range" ).slider( "value" ) );
    
});


// Function to disable form fields
//function disableFormFields() {
//    formFields.forEach(field => {
//        field.disabled = true;
 //   });
//}


document.getElementById("editPreferencesButton").addEventListener("click", function (e) {
        e.preventDefault();
        var formFields = document.querySelectorAll("#preferencesForm input, #preferencesForm button, #preferencesForm select, #preferencesForm textarea");
        formFields.forEach(field => {
            field.disabled = false;
        });
        $("#slider-range").slider("enable");
        
        warningMessage.innerHTML = " "
    });
    

function validateForm() {
    var trainingTypeSelected = document.querySelector("input[name='training_type']:checked");
    if (!trainingTypeSelected) {
        alert("יש לבחור סוג אימון מהרשימה");
        return false;
    }
    
    var startTime = document.querySelector("input[name='start_hour']").value;
    var endTime = document.querySelector("input[name='end_hour']").value;
    var maxPrice = document.querySelector("input[name='max_price']").value;

    // Default values
    var defaultStartTime = "06:00";
    var defaultEndTime = "00:00";
    var defaultMaxPrice =  "₪200";

    var warningMessage = document.getElementById('warningMessage');
    if (startTime === defaultStartTime && endTime === defaultEndTime && 
         maxPrice == defaultMaxPrice) {
        warningMessage.innerHTML = "לא שינית את טווח שעות האימון ומחיר מקסימלי לאימון ולכן המערכת תחפש אימונים בהתאם לערכי ברירת המחדל הרשומים";
    } else {
        warningMessage.innerHTML = "";
    }

    return true;
}


function handleSubmit() {
    
    if (!validateForm()) {
        return false; // Prevent form submission if validation fails
    }
    
    var formData = new FormData(document.getElementById('preferencesForm'));
 
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'Training_preferences.php', true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            // Check if the registration was successful
            if (response.success) {
                alert("הנתונים נשמרו בהצלחה");
                // Disable form fields
                var formFields = document.querySelectorAll("#preferencesForm input, #preferencesForm button, #preferencesForm select, #preferencesForm textarea");
                formFields.forEach(field => {
                    field.disabled = true;
                });
                
                // Disable the slider
                $("#slider-range").slider("disable");
                
            } else {
                // Display error message
                    alert('השמירה נכשלה. נסה/י פעם נוספת ' + response.message);
            }
        }
    }
  //  disableFormFields();
    
    xhr.send(formData);
    return false; // Prevent form from submitting the default way
}





//אם יהיה זמן להוסיף ימי אימון ומדריך מועדף


