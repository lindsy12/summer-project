<?php
// Include the database configuration
require_once('../authentication/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Dermatologists - Skincare Experts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-purple: #6a11cb;
            --secondary-orange: #ff7e5f;
            --light-purple: #f0e6ff;
            --dark-purple: #4d0ca2;
            --light-orange: #ffefe6;
            --text-dark: #333;
            --text-light: #666;
            --white: #ffffff;
        }
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        body {
            background-color: #f8f9fa;
            color: var(--text-dark);
            line-height: 1.6;
        }
       
       
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            margin-top: 6rem;
        }
       
        .page-header h1 {
            color: var(--primary-purple);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
       
        .page-header p {
            color: var(--text-light);
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }
       
        .dermatologists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
       
        .dermatologist-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
       
        .dermatologist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
       
        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, var(--primary-purple), var(--dark-purple));
            display: flex;
            align-items: center;
            justify-content: center;
        }
       
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
       
        .card-image .avatar {
            font-size: 5rem;
            color: white;
        }
       
        .dermatologist-card:hover .card-image img {
            transform: scale(1.05);
        }
       
        .card-content {
            padding: 1.5rem;
        }
       
        .card-content h2 {
            color: var(--primary-purple);
            margin-bottom: 0.5rem;
            font-size: 1.4rem;
        }
       
        .specialty {
            color: var(--secondary-orange);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
       
        .experience {
            color: var(--text-light);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
       
        .experience i {
            margin-right: 0.5rem;
            color: var(--primary-purple);
        }
       
        .expertise {
            margin-bottom: 1rem;
        }
       
        .expertise p {
            margin-bottom: 0.3rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
        }
       
        .expertise i {
            margin-right: 0.5rem;
            color: var(--primary-purple);
            font-size: 0.9rem;
        }
       
        .availability {
            margin-bottom: 1rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
        }
       
        .availability i {
            margin-right: 0.5rem;
            color: var(--primary-purple);
        }
       
        .quote {
            font-style: italic;
            color: var(--dark-purple);
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            background-color: var(--light-purple);
            border-left: 3px solid var(--primary-purple);
            border-radius: 0 5px 5px 0;
        }
       
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(to right, var(--primary-purple), var(--secondary-orange));
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
        }
       
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
        }
       
.booking-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}


.modal-content {
    background-color: var(--white);
    border-radius: 10px;
    padding: 20px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
    position: relative;
    max-height: 80vh; /* Sets the maximum height to 80% of the viewport height */
    overflow-y: auto; /* Adds a scrollbar if content exceeds the max-height */
    box-sizing: border-box; /* Ensures padding is included in the total height */
}


.close-modal {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    color: var(--text-light);
    cursor: pointer;
    transition: color 0.3s ease;
}


.close-modal:hover {
    color: var(--primary-purple);
}


.modal-header {
    margin-bottom: 1.5rem;
    text-align: center;
}


.modal-header h2 {
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}        .form-group {
            margin-bottom: 1.5rem;
        }
       
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
       
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
       
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
       
        .no-doctors, .error-message {
            text-align: center;
            grid-column: 1 / -1;
            padding: 2rem;
            color: var(--text-light);
            font-size: 1.2rem;
        }
       
        .loading {
            text-align: center;
            grid-column: 1 / -1;
            padding: 2rem;
            color: var(--text-light);
        }
       
        /* Responsive Styles */
        @media (max-width: 992px) {
            .container {
                padding: 1.5rem;
            }
           
            .dermatologists-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1.5rem;
            }
        }
       
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
           
            .page-header p {
                font-size: 1rem;
            }
           
            .dermatologists-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.2rem;
            }
           
            .card-content {
                padding: 1.2rem;
            }
        }
       
        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }
           
            .page-header {
                margin-bottom: 2rem;
            }
           
            .page-header h1 {
                font-size: 1.8rem;
            }
           
            .dermatologists-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
           
            .modal-content {
                padding: 1.5rem;
                width: 95%;
            }
        }
    </style>
</head>
<body>
                <?php include '../includes/header.php'; ?>


    <div class="container">
        <div class="page-header">
            <h1>Our Expert Dermatologists</h1>
            <p>Meet our team of skilled dermatologists dedicated to providing the best skincare treatments and advice. Each specialist brings unique expertise and experience to help you achieve healthy, glowing skin.</p>
        </div>
       
        <div class="dermatologists-grid" id="dermatologists-container">
            <div class="loading">Loading dermatologists...</div>
        </div>
    </div>
   
    <!-- Booking Modal -->
    <div class="booking-modal" id="bookingModal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            <div class="modal-header">
                <h2>Book an Appointment</h2>
                <p>Schedule a consultation with <span id="dermatologistName"></span></p>
            </div>
            <form id="bookingForm">
                <input type="hidden" id="dermatologistId">
               
                <div class="form-group">
                    <label for="patientName">Your Name</label>
                    <input type="text" id="patientName" class="form-control" required>
                </div>
               
                <div class="form-group">
                    <label for="patientEmail">Email Address</label>
                    <input type="email" id="patientEmail" class="form-control" required>
                </div>
               
                <div class="form-group">
                    <label for="patientPhone">Phone Number</label>
                    <input type="tel" id="patientPhone" class="form-control" required>
                </div>
               
                <div class="form-group">
                    <label for="appointmentDate">Preferred Date</label>
                    <input type="date" id="appointmentDate" class="form-control" required>
                </div>
               
                <div class="form-group">
                    <label for="appointmentTime">Preferred Time</label>
                    <select id="appointmentTime" class="form-control" required>
                        <option value="">Select a time</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="14:00">02:00 PM</option>
                        <option value="15:00">03:00 PM</option>
                        <option value="16:00">04:00 PM</option>
                        <option value="17:00">05:00 PM</option>
                    </select>
                </div>
               
                <div class="form-group">
                    <label for="concern">Skin Concern</label>
                    <textarea id="concern" class="form-control" placeholder="Please describe your skin concern or reason for consultation..." required></textarea>
                </div>
               
                <button type="submit" class="btn">Book Appointment</button>
            </form>
        </div>
    </div>


            <?php include '../includes/footer.php'; ?>




    <script>
        // Function to fetch dermatologists from the server
        async function fetchDermatologists() {
            try {
                const response = await fetch('../authentication/dashboards/get_dermatologists.php');
               
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
               
                const dermatologists = await response.json();
               
                const container = document.getElementById('dermatologists-container');
                container.innerHTML = '';
               
                if (dermatologists.length === 0) {
                    container.innerHTML = '<p class="no-doctors">No dermatologists found.</p>';
                    return;
                }
               
                dermatologists.forEach(derm => {
                    const card = document.createElement('div');
                    card.className = 'dermatologist-card';
                   
                    // Handle missing profile data gracefully
                    const profileImage = derm.profile_image
                        ? '../authentication/' + derm.profile_image
                        : '';
                   
                    const yearsExperience = derm.years_experience || 'Not specified';
                    const experience1 = derm.experience1 || 'Dermatology specialist';
                    const experience2 = derm.experience2 || 'Skin care expert';
                    const availability = derm.availability || 'Schedule varies';
                    const quote = derm.quote || 'Committed to providing the best skin care solutions.';
                   
                    card.innerHTML = `
                        <div class="card-image">
                            ${profileImage ?
                                `<img src="${profileImage}" alt="${derm.name}">` :
                                `<i class="fas fa-user-md avatar"></i>`
                            }
                        </div>
                        <div class="card-content">
                            <h2>${derm.name}</h2>
                            <span class="specialty">Dermatology Specialist</span>
                            <span class="experience"><i class="fas fa-briefcase"></i> ${yearsExperience} years of experience</span>
                           
                            <div class="expertise">
                                <p><i class="fas fa-star"></i> ${experience1}</p>
                                <p><i class="fas fa-star"></i> ${experience2}</p>
                            </div>
                           
                            <div class="availability"><i class="fas fa-calendar-alt"></i> ${availability}</div>
                           
                            <div class="quote">"${quote}"</div>
                           
                            <button class="btn book-btn" data-id="${derm.dermatologist_id || derm.id}" data-name="${derm.name}">Book Appointment</button>
                        </div>
                    `;
                   
                    container.appendChild(card);
                });
               
                // Add event listeners to booking buttons
                document.querySelectorAll('.book-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        openBookingModal(this.getAttribute('data-id'), this.getAttribute('data-name'));
                    });
                });
               
            } catch (error) {
                console.error('Error fetching dermatologists:', error);
                document.getElementById('dermatologists-container').innerHTML =
                    '<p class="error-message">Unable to load dermatologists. Please try again later.</p>';
            }
        }
       
        // Function to open booking modal
        function openBookingModal(id, name) {
            document.getElementById('dermatologistId').value = id;
            document.getElementById('dermatologistName').textContent = name;
            document.getElementById('bookingModal').style.display = 'flex';
           
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointmentDate').min = today;
        }
       
        // Function to close booking modal
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
       
        // Event listeners
        document.getElementById('closeModal').addEventListener('click', closeBookingModal);
       
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
           
            const formData = {
                dermatologist_id: document.getElementById('dermatologistId').value,
                patient_name: document.getElementById('patientName').value,
                patient_email: document.getElementById('patientEmail').value,
                patient_phone: document.getElementById('patientPhone').value,
                appointment_date: document.getElementById('appointmentDate').value,
                appointment_time: document.getElementById('appointmentTime').value,
                concern: document.getElementById('concern').value
            };
           
            try {
                const response = await fetch('../authentication/dashboards/book_appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
               
                const result = await response.json();
               
                if (result.success) {
                    alert('Appointment booked successfully!');
                    closeBookingModal();
                    document.getElementById('bookingForm').reset();
                } else {
                    alert('Error booking appointment: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error booking appointment. Please try again.');
            }
        });
       
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('bookingModal')) {
                closeBookingModal();
            }
        });
       
        // Fetch dermatologists when page loads
        document.addEventListener('DOMContentLoaded', fetchDermatologists);
    </script>
</body>
</html>

