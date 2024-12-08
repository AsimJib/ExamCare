// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCM79WYeS4p0PpHkw1l3q6Kt5MY0Jdub6s",
    authDomain: "examcare-app-manager.firebaseapp.com",
    databaseURL: "https://examcare-app-manager.firebaseio.com",
    projectId: "examcare-app-manager",
    storageBucket: "examcare-app-manager.appspot.com",
    messagingSenderId: "1078639810324",
    appId: "1:1078639810324:web:b8929f9719cd39fd959401"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const database = firebase.database();

document.getElementById('sameAsPresent').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('permanentVillage').value = document.getElementById('presentVillage').value;
        document.getElementById('permanentPostOffice').value = document.getElementById('presentPostOffice').value;
        document.getElementById('permanentPoliceStation').value = document.getElementById('presentPoliceStation').value;
        document.getElementById('permanentDistrict').value = document.getElementById('presentDistrict').value;
        document.getElementById('permanentPinCode').value = document.getElementById('presentPinCode').value;
    } else {
        document.getElementById('permanentVillage').value = '';
        document.getElementById('permanentPostOffice').value = '';
        document.getElementById('permanentPoliceStation').value = '';
        document.getElementById('permanentDistrict').value = '';
        document.getElementById('permanentPinCode').value = '';
    }
});

document.getElementById('postApplyingFor').addEventListener('change', function() {
    const post = this.value;
    const subjectField = document.getElementById('subjectField');
    const qualificationsSection = document.getElementById('qualificationsSection');
    const qualification10th = document.getElementById('qualification10th');
    const qualification12th = document.getElementById('qualification12th');
    const qualificationGraduation = document.getElementById('qualificationGraduation');
    const qualificationsTitle = document.getElementById('qualificationsTitle');
    const totalPayable = document.getElementById('totalPayable');

    console.log('Selected post:', post);

    subjectField.style.display = (post === 'teacher') ? 'block' : 'none';
    qualificationsSection.style.display = (post !== '') ? 'block' : 'none';
    qualificationsTitle.style.display = (post !== '') ? 'block' : 'none';

    qualification10th.style.display = 'block';
    qualification12th.style.display = (post === 'teacher' || post === 'internal_data_analyst' || post === 'data_entry' || post === 'customer_service_executive' || post === 'video_banner_editor') ? 'block' : 'none';
    qualificationGraduation.style.display = (post === 'teacher' || post === 'internal_data_analyst') ? 'block' : 'none';

    document.querySelectorAll('.qualification input[type="text"]').forEach(input => {
        input.required = input.parentElement.parentElement.style.display !== 'none';
    });

    // Fetch and display the amount
    const chargeRef = firebase.database().ref(`Application Details/${post}/Apply Charge`);
    console.log('Fetching data from path:', `Application Details/${post}/Apply Charge`);
    chargeRef.once('value').then((snapshot) => {
        console.log('Snapshot exists:', snapshot.exists());
        console.log('Snapshot value:', snapshot.val());
        if (snapshot.exists()) {
            totalPayable.textContent = `Total Payable Amount: ₹${snapshot.val()}`;
            totalPayable.style.display = 'block'; // Display the label
        } else {
            totalPayable.textContent = 'Total Payable: N/A';
            totalPayable.style.display = 'block'; // Display the label
        }
    }).catch((error) => {
        console.error("Error fetching data: ", error);
        totalPayable.textContent = 'Total Payable: Error fetching amount';
        totalPayable.style.display = 'block'; // Display the label
    });
});

// Initial setup to hide the qualifications title
document.getElementById('qualificationsTitle').style.display = 'none';

// Initial setup to hide the total payable label
document.getElementById('totalPayable').style.display = 'none';

// Function to validate date of birth
function validateDOB() {
    const dobInput = document.getElementById('dob');
    const dob = new Date(dobInput.value);
    const minDate = new Date('1979-01-01');
    const maxDate = new Date('2004-12-31');
    const submitButton = document.querySelector('button[type="submit"]');

    if (dob < minDate || dob > maxDate) {
        alert("You're not eligible due to age limitations!");
        dobInput.value = ''; // Clear the invalid date
        submitButton.disabled = true; // Disable submit button
    } else {
        submitButton.disabled = false; // Enable submit button
    }
}

// Add event listener to validate date of birth on change
document.getElementById('dob').addEventListener('change', validateDOB);

// Function to initialize Razorpay payment
function initiatePayment(amount) {
    const post = document.getElementById('postApplyingFor').value;
    var options = {
        "key": "rzp_test_Biw7k35dT7rhHS", // Enter the Key ID generated from the Dashboard
        "amount": amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, multiplying by 100
        "currency": "INR",
        "name": "Your Company Name",
        "description": `Payment for ${post}`, // Updated description
        "image": "https://example.com/your_logo",
        "handler": handlePaymentSuccess,
        "prefill": {
            "name": document.getElementById('fullName').value,
            "email": document.getElementById('email').value,
            "contact": document.getElementById('mobileNumber').value
        },
        "theme": {
            "color": "#3399cc"
        },
        "modal": {
            "ondismiss": function () {
                console.log("Payment cancelled");
                handlePaymentFailure();
            }
        },
        "method": {
            "upi": true // Enable UPI payments
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function (response) {
        console.log(response.error);
        handlePaymentFailure(response.error);
    });
    rzp1.open();
}

// Function to handle successful payment
function handlePaymentSuccess(response) {
    if (response.razorpay_payment_id) {
        console.log('Payment successful, Payment ID:', response.razorpay_payment_id);
        storeFormData(response.razorpay_payment_id);
    } else {
        handlePaymentFailure(response);
    }
}

// Function to handle payment failure
function handlePaymentFailure(error) {
    // Redirect to the 'Payment Failed' page
    window.location.href = 'payment_failed.html';
}

// Function to store form data in Firebase
function storeFormData(paymentId) {
    const post = document.getElementById('postApplyingFor').value;
    const subject = document.getElementById('subjectField').style.display === 'block' ? document.getElementById('selectSubject').value : null;
    const totalAmount = document.getElementById('totalPayable').textContent.split('₹')[1]; // Extract amount from text
    
    const formData = {
        registrationNumber: Math.floor(100000000 + Math.random() * 900000000).toString(),
        fullName: document.getElementById('fullName').value,
        dob: document.getElementById('dob').value,
        gender: document.getElementById('gender').value,
        fathersName: document.getElementById('fathersName').value,
        mobileNumber: document.getElementById('mobileNumber').value,
        email: document.getElementById('email').value,
        caste: document.getElementById('caste').value,
        religion: document.getElementById('religion').value,
        aadhaarNumber: document.getElementById('aadhaarNumber').value,
        languages: Array.from(document.getElementById('languages').selectedOptions).map(opt => opt.value),
        postApplyingFor: post,
        subject: subject,
        presentAddress: {
            village: document.getElementById('presentVillage').value,
            postOffice: document.getElementById('presentPostOffice').value,
            policeStation: document.getElementById('presentPoliceStation').value,
            district: document.getElementById('presentDistrict').value,
            state: document.getElementById('presentState').value,
            pinCode: document.getElementById('presentPinCode').value
        },
        permanentAddress: {
            village: document.getElementById('permanentVillage').value,
            postOffice: document.getElementById('permanentPostOffice').value,
            policeStation: document.getElementById('permanentPoliceStation').value,
            district: document.getElementById('permanentDistrict').value,
            state: document.getElementById('permanentState').value,
            pinCode: document.getElementById('permanentPinCode').value
        },
        qualifications: {},
        paymentDetails: {
            paymentId: paymentId,
            paymentDate: new Date().toLocaleString(),
            amount: totalAmount // Store the total amount
        }
    };

    // Store qualification details based on the selected post
    if (post === 'teacher' || post === 'internal_data_analyst' || post === 'data_entry' || post === 'customer_service_executive' || post === 'video_banner_editor') {
        formData.qualifications.tenth = {
            board: document.getElementById('board10th').value,
            passingYear: document.getElementById('passingYear10th').value,
            fullMarks: document.getElementById('fullMarks10th').value,
            marksObtained: document.getElementById('marksObtained10th').value,
        };
    }

    if (post === 'teacher' || post === 'internal_data_analyst' || post === 'data_entry' || post === 'customer_service_executive' || post === 'video_banner_editor') {
        formData.qualifications.twelfth = {
            board: document.getElementById('board12th').value,
            passingYear: document.getElementById('passingYear12th').value,
            fullMarks: document.getElementById('fullMarks12th').value,
            marksObtained: document.getElementById('marksObtained12th').value,
        };
    }

    if (post === 'teacher' || post === 'internal_data_analyst') {
        formData.qualifications.graduation = {
            university: document.getElementById('boardGraduation').value,
            passingYear: document.getElementById('passingYearGraduation').value,
            fullMarks: document.getElementById('fullMarksGraduation').value,
            marksObtained: document.getElementById('marksObtainedGraduation').value,
        };
    }

    const saveDataWithUniqueRegistrationNumber = (registrationNumber) => {
        const ref = firebase.database().ref(`Registered Users/${registrationNumber}`);
        ref.once('value').then((snapshot) => {
            if (snapshot.exists()) {
                // If registration number already exists, generate a new one
                saveDataWithUniqueRegistrationNumber(generateRegistrationNumber());
            } else {
                // Save form data under the generated registration number
                ref.set(formData).then(() => {
                        console.log('Data saved successfully. Redirecting to registration successful page...');
                        // Redirect to the 'Registration Successful' page
                        window.location.href = `registration_successful.html?registrationNumber=${registrationNumber}`;
                    }).catch((error) => {
                        console.error('Error saving data:', error);
                    });
                }
            }).catch((error) => {
                console.error('Error checking registration number:', error);
            });
        };

    saveDataWithUniqueRegistrationNumber(formData.registrationNumber);
}

document.getElementById('jobApplicationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission action

    const post = document.getElementById('postApplyingFor').value;
    const chargeRef = firebase.database().ref(`Application Details/${post}/Apply Charge`);
    chargeRef.once('value').then((snapshot) => {
        if (snapshot.exists()) {
            const amount = snapshot.val();
            initiatePayment(amount); // Initiate Razorpay payment
        } else {
            alert('Unable to fetch application charge. Please try again later.');
        }
    }).catch((error) => {
        console.error("Error fetching data: ", error);
        alert('Unable to fetch application charge. Please try again later.');
    });
});
