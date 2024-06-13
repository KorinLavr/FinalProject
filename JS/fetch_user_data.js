// Fetch user data
const urlParams = new URLSearchParams(window.location.search);
const userType = urlParams.get('userType');
const userID = urlParams.get('userID');

fetch(`fetch_user_data.php?userType=${userType}&userID=${userID}`)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('user-details').innerText = data.error;
        } else {
            displayUserData(data.data);
        }
    })
    .catch(error => {
        document.getElementById('user-details').innerText = 'An error occurred while fetching data';
    });

// Function to display user data
function displayUserData(userData) {
    const userDetailsDiv = document.getElementById('user-details');
    userDetailsDiv.innerHTML = `
        <p>First Name: ${userData.F_name || userData.First_name}</p>
        <p>Last Name: ${userData.L_name || userData.Last_name}</p>
        <p>Gender: ${userData.Sex || userData.Gender}</p>
        <p>Date of Birth: ${userData.Bdate || userData.Birth_date}</p>
        <p>Phone Number: ${userData.Phone_num}</p>
        <p>Email: ${userData.Email}</p>
        ${userData.Exp_years ? `<p>Experience: ${userData.Exp_years} years</p>` : ''}
        ${userData.Type ? `<p>Training Types: ${userData.Type}</p>` : ''}
        ${userData.Certification !== undefined ? `<p>Certification: ${userData.Certification ? 'Yes' : 'No'}</p>` : ''}
    `;
}
