        new Card({
            form: '#payment_form',
            container: '.card-wrapper',
            formSelectors: {
                numberInput: 'input#credit_card_number',
                expiryInput: 'input#expiration_month, input#expiration_year',
                cvcInput: 'input#cvv',
                nameInput: 'input#full_name'
            },
            width: 150, 
            formatting: true, 
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'שם מלא',
                expiry: '••/••',
                cvc: '•••'
            },
            messages: {
                validDate: 'valid\ndate', 
                monthYear: 'MM/YY' 
            },
            masks: {
                cardNumber: '•' 
            },
            debug: false
        });

        // Custom validation for expiration date
        const monthInput = document.getElementById('expiration_month');
        const yearInput = document.getElementById('expiration_year');
        
        monthInput.addEventListener('input', () => {
            if (monthInput.value.length === 2) {
                yearInput.focus();
            }
        });

        yearInput.addEventListener('input', () => {
            if (yearInput.value.length === 2) {
                document.getElementById('cvv').focus();
            }
        });

        // Limit CVV to 3 digits
        document.getElementById('cvv').addEventListener('input', (e) => {
            e.target.value = e.target.value.slice(0, 3);
        });

        // Format credit card number and recognize card type
        document.getElementById('credit_card_number').addEventListener('input', (e) => {
            const value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';

            for (let i = 0; i < value.length; i++) {
                if (i !== 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }

            e.target.value = formattedValue;
        });
        
        
        // Function to validate the form
        function validateForm() {
            const fullNameInput = document.getElementById('full_name');
            // Validate full name
            const fullName = fullNameInput.value.trim();
            if (fullName.split(' ').length < 2) {
                document.getElementById('name_error').innerHTML = 'יש להזין שם פרטי ושם משפחה';
                fullNameInput.focus();
                return false; // Prevent form submission
            } else {
                  document.getElementById('name_error').innerHTML = '';
            }
            
            const idInput = document.getElementById('ID');
            const idError = document.getElementById('id_error');
            const idValue = idInput.value.trim();
            if (!/^\d{9}$/.test(idValue)) {
                idError.innerHTML = 'יש להזין מספר תעודת זהות תקין בן 9 ספרות';
                idInput.focus();
                return false; // Prevent form submission
            } else {
                idError.innerHTML = '';
            }
            
            const cardInput = document.getElementById('credit_card_number');
            const cardError = document.getElementById('card_error');
            const cardValue = cardInput.value.replace(/\D/g, '');
            if (cardValue.length !== 16) {
                cardError.innerHTML = 'יש להזין מספר כרטיס תקין בן 16 ספרות';
                cardInput.focus();
                return false; // Prevent form submission
            } else {
                cardError.innerHTML = '';
            }
            
            const monthInput = document.getElementById('expiration_month');
            const yearInput = document.getElementById('expiration_year');
            const expiryError = document.getElementById('expiry_error');
            const currentYear = new Date().getFullYear() % 100; // Get last two digits of current year
            const currentMonth = new Date().getMonth() + 1; // Get current month (0-based index)

            const expiryMonth = parseInt(monthInput.value, 10);
            const expiryYear = parseInt(yearInput.value, 10);

            if (expiryMonth < 1 || expiryMonth > 12) {
                expiryError.innerHTML = 'יש להזין חודש תקין בין 01 ל-12';
                monthInput.focus();
                return false; 
            } else if (expiryYear < currentYear || (expiryYear === currentYear && expiryMonth < currentMonth)) {
                expiryError.innerHTML = 'יש להזין שנה תקינה בעתיד';
                yearInput.focus();
                return false; 
            } else {
                expiryError.innerHTML = '';
            }
            
            
            // Validate CVV
            const cvvInput = document.getElementById('cvv');
            const cvvError = document.getElementById('cvv_error');
            const cvvValue = cvvInput.value.trim();
            if (cvvValue.length < 3) {
                cvvError.innerHTML = 'יש להזין CVV של 3 ספרות';
                cvvInput.focus();
                return false; // Prevent form submission
            } else {
                cvvError.innerHTML = '';
            }
            
            return true; // Allow form submission
        }
        
        // Submit the form using AJAX
        document.getElementById('payment_form').addEventListener('submit', function (event) {
            event.preventDefault();
            
            if (!validateForm()) {
                // If validation fails, do not proceed with form submission
                return;
            }
            
            document.getElementById('payment_failure_message').innerText = '';
            document.getElementById('registration_failure_message').innerText = '';
            document.querySelector('.loading').style.display = 'inline-block'; // Show loading indicator
            
            fetch('Process_payment.php', {
                method: 'POST',
                body: new FormData(this),
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('.loading').style.display = 'none'; // Hide loading indicator
                if (data.success) {
                    // Payment successful, update content accordingly
                    document.getElementById('payment_form').style.display = 'none';
                    document.getElementById('training_details').style.display = 'none';
                    document.getElementById('payment_title').style.display = 'none';
                    document.getElementById('payment_success_title').style.display = 'block';
                    document.getElementById('payment_success_message').innerText = 'התשלום בוצע בהצלחה! תודה על הרשמתך לאימון.';
                    document.getElementById('trainee_profile_link').style.display = 'block';
                     // Apply the success class to the main container
                    document.querySelector('main').classList.add('main-success');
                } else {
                    if (data.message === 'User already registered for this training') {
                        document.getElementById('registration_failure_message').innerText = 'הנך רשום/ה כבר לאימון זה';
                    }else{
                    // Payment failed, show error message
                    document.getElementById('payment_failure_message').innerText = 'התשלום נכשל. נסה/י שוב.';
                    }
                }
            })
            .catch(error => {
                 // Hide the loading spinner
                document.querySelector('.loading').style.display = 'none';
                console.error('Error:', error);
            });
        });
        